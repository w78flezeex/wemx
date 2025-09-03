<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CouponsController extends Controller
{
    /**
     * Define relationships that are allowed to be loaded.
     *
     * @var array
     */
    protected $relations = [];

    /**
     * Define allowed filters.
     *
     * @var array
     */
    protected $filters = [
        'id',
        'code',
        'discount_type',
        'discount_amount',
        'currency',
        'allowed_uses',
        'applicable_products',
        'notes',
        'expires_at',
        'created_at',
    ];

    /**
     * Allowed sort columns and their directions.
     *
     * @var array
     */
    protected $sorts = [
        'id',
        'code',
        'discount_type',
        'discount_amount',
        'currency',
        'allowed_uses',
        'expires_at',
        'created_at',
    ];

    /**
     * Sorting operators.
     *
     * @var array
     */
    protected $sort_operators = [
        'asc',
        'desc',
        'random',
    ];

    /**
     * Get all coupons.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function coupons(Request $request)
    {
        $coupons = Coupon::query();

        try {
            // add relations
            $this->applyRelations($coupons);

            // apply filters
            $this->applyFilters($coupons);

            // apply sorting
            $this->applySorts($coupons);

            // apply date filters
            $this->applyDateFilters($coupons);

        } catch (\Exception $error) {
            return response()->json(['success' => false, 'errors' => [$error->getMessage()]], 422);
        }

        return response()->json(array_merge(
            ['status' => true],
            $coupons->paginate($request->get('paginate', 15))->toArray()
        ), 200);
    }

    /**
     * Apply relationships to the query.
     *
     * @param  array  $sorts
     *
     * @throws InvalidArgumentException
     */
    protected function applyRelations(Builder $query): void
    {
        $includes = request()->get('include', '');
        if (!$includes) {
            return;
        }

        // turn includes into an array & remove any spaces
        $includes = explode(',', $includes);
        $includes = array_map('trim', $includes);

        foreach ($includes as $relation) {
            if (!in_array($relation, $this->relations)) {
                throw new InvalidArgumentException("Relation $relation is not allowed");
            }

            $query->with($relation);
        }
    }

    /**
     * Apply filters to the query.
     *
     * @param  array  $filters
     * @return void
     *
     * @throws InvalidArgumentException
     */
    protected function applyFilters(Builder $query)
    {
        $filters = request()->get('filter', []);

        // check if value is an array
        if (!is_array($filters)) {
            throw new InvalidArgumentException('Filter must be in format ?filter[key]=value');
        }

        // check if filters are empty
        if (empty($filters)) {
            return;
        }

        foreach ($filters as $key => $value) {
            if (!in_array($key, $this->filters)) {
                if (!Str::contains($key, '->')) {
                    throw new InvalidArgumentException("Filter $key is not allowed");
                }
            }

            $query->where($key, $value);
        }
    }

    /**
     * Apply sorting to the query.
     *
     * @param  array  $sorts
     *
     * @throws InvalidArgumentException
     */
    protected function applySorts(Builder $query): void
    {
        $sorts = request()->get('sort', []);

        // check if value is an array
        if (!is_array($sorts)) {
            throw new InvalidArgumentException('Sorting filters must be of format ?sort[key]=value');
        }

        // check if sorts are empty
        if (empty($sorts)) {
            return;
        }

        foreach ($sorts as $key => $operator) {
            if (!in_array($key, $this->sorts)) {
                throw new InvalidArgumentException("Sorting by $key is not allowed");
            }

            if ($operator === 'random') {
                $query->inRandomOrder();
            } else {
                $query->orderBy($key, $operator);
            }
        }
    }

    /**
     * Apply date filters to the query.
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    protected function applyDateFilters(Builder $query)
    {
        $dateFilter = request()->get('date');

        if (!$dateFilter) {
            return;
        }

        switch ($dateFilter) {
            case 'today':
                $query->whereDate('created_at', '=', Carbon::today()->toDateString());
                break;
            case 'yesterday':
                $query->whereDate('created_at', '=', Carbon::yesterday()->toDateString());
                break;
            case '3days':
                $query->whereDate('created_at', '>=', Carbon::now()->subDays(3)->toDateString());
                break;
            case '7days':
                $query->whereDate('created_at', '>=', Carbon::now()->subDays(7)->toDateString());
                break;
            case '14days':
                $query->whereDate('created_at', '>=', Carbon::now()->subDays(14)->toDateString());
                break;
            case '30days':
                $query->whereDate('created_at', '>=', Carbon::now()->subDays(30)->toDateString());
                break;
            case '90days':
                $query->whereDate('created_at', '>=', Carbon::now()->subDays(90)->toDateString());
                break;
            default:
                $dates = explode(',', $dateFilter);
                if (count($dates) === 2) {
                    try {
                        $startDate = Carbon::parse($dates[0]);
                        $endDate = Carbon::parse($dates[1]);

                        // Ensure that the start date is not after the end date
                        if ($startDate->gt($endDate)) {
                            throw new InvalidArgumentException('Start date must be before end date.');
                        }

                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    } catch (\Exception $e) {
                        throw new InvalidArgumentException("Invalid date format. Dates should be in 'YYYY-MM-DD' format.");
                    }
                } else {
                    throw new InvalidArgumentException("Custom date range must be in format 'YYYY-MM-DD,YYYY-MM-DD'");
                }
                break;
        }
    }
}
