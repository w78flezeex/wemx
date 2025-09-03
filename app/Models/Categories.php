<?php

namespace App\Models;

use App\Models\OrderScope\OrderByScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Categories
 *
 * @property int $id
 * @property string $name
 * @property string $link
 * @property string $icon
 * @property int $order
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Categories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Categories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Categories onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Categories query()
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Categories withoutTrashed()
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Package> $packages
 * @property-read int|null $packages_count
 *
 * @mixin \Eloquent
 */
class Categories extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected static function booted(): void
    {
        static::addGlobalScope(new OrderByScope('order'));
    }

    public function icon()
    {
        return asset('storage/products/' . $this->icon);
    }

    public function packages()
    {
        return $this->hasMany(Package::class, 'category_id', 'id');
    }

    public function changeOrder($direction = 'up')
    {
        $allItems = self::orderBy('order', 'asc')->get();
        foreach ($allItems as $index => $item) {
            $item->order = $index + 1;
            $item->save();
        }
        $currentOrder = $this->order;
        if ($direction == 'up' && $currentOrder > 1) {
            $prevItem = self::where('order', '<', $currentOrder)->orderBy('order', 'desc')->first();
            if ($prevItem) {
                $this->order = $prevItem->order;
                $prevItem->order = $currentOrder;
                $prevItem->save();
            }
        } elseif ($direction == 'down') {
            $nextItem = self::where('order', '>', $currentOrder)->orderBy('order', 'asc')->first();
            if ($nextItem) {
                $this->order = $nextItem->order;
                $nextItem->order = $currentOrder;
                $nextItem->save();
            }
        }

        return $this->save();
    }
}
