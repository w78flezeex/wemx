<?php

namespace App\Http\Controllers\Admin;

use App\Facades\AdminTheme as Theme;
use App\Http\Controllers\Controller;
use App\Models\ErrorLog;
use App\Models\Order;
use App\Models\Package;
use App\Models\User;
use App\Rules\ValidDomain;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\OrderPriceModifier;

class OrdersController extends Controller
{
    public function index($status)
    {
        $orders = Order::query();

        if (request()->get('sort') == 'random') {
            $orders->inRandomOrder();
        }

        if (request()->get('sort') == 'latest' or request()->get('sort') == null) {
            $orders->latest();
        }

        if (request()->get('sort') == 'oldest') {
            $orders->oldest();
        }

        if (isset(request()->filter)) {
            foreach (request()->filter as $filter) {
                if (in_array($filter['operator'], ['LIKE', 'NOT LIKE'])) {
                    $filter['value'] = '%' . $filter['value'] . '%';
                }

                $orders->where($filter['key'], $filter['operator'], $filter['value']);
            }
        }

        $orders = $orders->where('status', $status)->paginate(request()->get('per_page', 20));

        return Theme::view('orders.index', compact('orders', 'status'));
    }

    public function create()
    {
        if (request()->has('package') and request()->input('package') != 0) {
            $package = Package::query()->findOrFail(request()->input('package'));

            return Theme::view('orders.create', compact('package'));
        }

        return Theme::view('orders.create');
    }

    /**
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $user = User::query()->findOrFail($request->input('user_id'));
        $package = Package::query()->findOrFail($request->input('package_id'));
        $price = $package->prices()->findOrFail($request->input('price'));

        $rules = $package->service()->getCheckoutRules($package);
        $validated = $request->validate(array_merge([
            'domain' => ['nullable', new ValidDomain],
            'status' => 'required',
            'due_date' => 'required|date',
            'last_renewed_at' => 'required|date',
            'create_instance' => 'boolean',
            'notify_user' => 'boolean',
        ],
            $rules
        ));

        $order = Order::query()->create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'price' => $price,
            'name' => $package->name,
            'service' => $package->service,
            'status' => $request->input('status'),
            'domain' => $request->input('domain'),
            'due_date' => $request->input('due_date'),
            'last_renewed_at' => $request->input('last_renewed_at'),
            'options' => $request->except(['_token', 'user_id', 'package_id', 'price', 'domain', 'status', 'due_date', 'last_renewed_at', 'create_instance', 'notify_user']),
        ]);

        // attempt to create a instance of order service
        if ($request->input('create_instance', false)) {
            $order->service()->create();
        }

        if ($request->input('notify_user', false)) {
            $this->emailOrderReady($user, $order);
        }

        return redirect()->route('orders.edit', $order->id)->with('success',
            trans('responses.order_create_success', ['default' => 'Order :name was created successfully', 'name' => $order->name])
        );
    }

    private function emailOrderReady(User $user, Order $order): void
    {
        app()->setLocale($user->language);
        $user->email([
            'subject' => __('admin.order_create_email_subject'),
            'content' => emailMessage('order_created', $user->language) .
                __('admin.order_create_email_content', [
                    'due_date' => $order->due_date->translatedFormat('d M Y'),
                    'order_id' => $order->id,
                    'order_name' => $order->name,
                ]),
            'button' => [
                'name' => __('admin.email_manage_button'),
                'url' => route('dashboard'),
            ],
        ]);
    }

    public function edit(Order $order)
    {
        $casts = [
            'data' => json_encode($order->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'options' => json_encode($order->options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ];

        $order_errors = ErrorLog::query()->where('order_id', $order->id)->where('severity', '!=', 'RESOLVED')->get();

        return Theme::view('orders.edit.edit-order', compact('order', 'casts', 'order_errors'));
    }

    public function update(Order $order)
    {
        $validated = request()->validate([
            'name' => 'required',
            'user_id' => 'required|numeric',
            'external_id' => 'sometimes|required',
            'last_renewed_at' => 'required|date',
            'cancelled_at' => 'sometimes|required|date',
            'notes' => 'max:1000',
            'package_id' => 'required|numeric',
            'data' => 'sometimes|required|json',
            'options' => 'sometimes|required|json',
        ]);

        $order->name = $validated['name'];
        $order->user_id = $validated['user_id'];
        $order->external_id = $validated['external_id'] ?? null;
        $order->last_renewed_at = $validated['last_renewed_at'];
        $order->domain = request()->input('domain', $order->domain);
        $order->cancelled_at = request()->input('cancelled_at', $order->cancelled_at);
        $order->notes = request()->input('notes', $order->notes);
        $order->package_id = request()->input('package_id', $order->package_id);
        $order->data = json_decode(request()->input('data', $order->data));
        $order->options = json_decode(request()->input('options', $order->options));
        $order->save();

        return redirect()->back()->with('success',
            trans('responses.order_update_success', ['default' => 'Order :name was update successfully', 'name' => $order->name])
        );
    }

    public function editPrice(Order $order)
    {
        $order_errors = ErrorLog::query()->where('order_id', $order->id)->where('severity', '!=', 'RESOLVED')->get();

        return Theme::view('orders.edit.edit-price', compact('order', 'order_errors'));
    }

    public function editService(Order $order)
    {
        $order_errors = ErrorLog::query()->where('order_id', $order->id)->where('severity', '!=', 'RESOLVED')->get();

        return Theme::view('orders.edit.edit-service', compact('order', 'order_errors'));
    }

    public function updatePrice(Order $order, Request $request)
    {
        $validated = $request->validate([
            'price' => 'required|array',
            'price.type' => 'required|in:recurring,one_time',
            'price.period' => 'required|numeric|min:1',
            'price.price' => 'required|numeric|min:0',
            'price.renewal_price' => 'required|numeric|min:0',
            'price.setup_fee' => 'required|numeric|min:0',
            'price.cancellation_fee' => 'required|numeric|min:0',
            'price.upgrade_fee' => 'required|numeric|min:0',
        ]);

        $price = array_merge($order->price, $request->get('price'));
        $order->price = $price;
        $order->save();

        return redirect()->back()->with('success', 'Order price has been updated successfully.');
    }

    public function createPriceModifier(Order $order, Request $request)
    {
        $validatedData = $request->validate([
            'description' => 'required',
            'base_price' => 'nullable|numeric',
            'monthly_price' => 'required|numeric',
            'cancellation_fee' => 'required|numeric',
            'upgrade_fee' => 'required|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $order->priceModifiers()->create([
            'description' => $validatedData['description'],
            'type' => 'manual_modifier',
            'base_price' => $validatedData['base_price'],
            'daily_price' => $validatedData['monthly_price'] / 30,
            'cancellation_fee' => $validatedData['cancellation_fee'],
            'upgrade_fee' => $validatedData['upgrade_fee'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
        ]);

        return redirect()->back()->with('success', 'Price modifier has been created successfully.');
    }

    public function updatePriceModifier(Order $order, $modifier_id, Request $request)
    {
        $modifier = OrderPriceModifier::withoutActiveModifierScope()->findOrFail($modifier_id);

        $validatedData = $request->validate([
            'description' => 'required',
            'base_price' => 'nullable|numeric',
            'monthly_price' => 'required|numeric',
            'cancellation_fee' => 'required|numeric',
            'upgrade_fee' => 'required|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $modifier->update([
            'description' => $validatedData['description'],
            'base_price' => $validatedData['base_price'],
            'daily_price' => $validatedData['monthly_price'] / 30,
            'cancellation_fee' => $validatedData['cancellation_fee'],
            'upgrade_fee' => $validatedData['upgrade_fee'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'is_active' => $validatedData['is_active'] ?? false,
        ]);

        return redirect()->back()->with('success', 'Price modifier has been updated successfully.');
    }

    public function extend(Order $order)
    {
        request()->validate([
            'new_due_date' => 'required|date',
        ]);

        $new_due_date = Carbon::parse(request()->input('new_due_date'));
        $old_due_date = $order->due_date;

        // check whether the new due date is a future date.
        // if so, then extend the due date +1 day
        if ($order->due_date->isBefore($new_due_date)) {
            $days = $order->due_date->diffInDays(request()->input('new_due_date'));
            $order->extend($days + 1);
        }
        // if due date is current due date or is older, set it
        // as the due date without performing any other actions
        elseif ($order->due_date->isAfter($new_due_date)) {
            $order->due_date = $new_due_date;
            $order->save();
        }

        if (request()->input('email') !== null) {
            app()->setLocale($order->user->language);
            $order->user->email([
                'subject' => __('admin.order_extended_email_subject'),
                'content' => request()->input('email') .
                    __('admin.extension_order_email_content', [
                        'order_id' => $order->id,
                        'new_due_date' => $new_due_date->translatedFormat('d M Y'),
                        'old_due_date' => $old_due_date->translatedFormat('d M Y'),
                        'order_name' => $order->name,
                    ]),
            ]);
        }

        return redirect()->back()->with('success',
            trans('responses.order_update_due_date_success', ['default' => 'Order :name due date has been updated successfully.', 'name' => $order->name])
        );
    }

    public function cancel(Order $order)
    {
        request()->validate([
            'cancelled_at' => 'required',
            'cancel_reason' => 'max:255',
        ]);

        if ($order->status == 'cancelled') {
            return redirect()->back()->with('error',
                trans('responses.service_error_cancelled', ['default' => 'Service :name was already cancelled.', 'name' => $order->name])
            );
        }

        $order->cancel(request()->input('cancelled_at'), request()->input('cancel_reason'));

        return redirect()->back()->with('success',
            trans('responses.service_success_cancelled', ['default' => 'Service :name was cancelled.', 'name' => $order->name])
        );
    }

    public function action(Order $order, $action)
    {
        if ($action == 'suspend') {
            $order->suspend();
        }

        if ($action == 'unsuspend') {
            $order->unsuspend();
        }

        if ($action == 'terminate') {
            $order->terminate();
        }

        if ($action == 'force_terminate') {
            $order->forceTerminate();
        }

        if ($action == 'force_suspend') {
            $order->forceSuspend();
        }

        return redirect()->back()->with('success',
            trans('responses.service_action_completed', ['default' => 'Service action :action has been completed', 'action' => $action])
        );
    }

    /**
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function tryAgain(Order $order)
    {
        $order->service()->create();

        ErrorLog::query()->where('order_id', $order->id)->where('severity', '!=', 'RESOLVED')->update(['severity' => 'RESOLVED']);

        return redirect()->back()->with('success',
            trans('responses.service_attempted_try_create', ['default' => 'Attempted to create service instance again'])
        );
    }

    public function destroy(Order $order)
    {
        if ($order->status !== 'terminated') {
            return redirect()->route('orders.index', 'terminated')->with('error', 'The order must first be terminated in order to delete it.');
        }

        $order->delete();

        return redirect()->route('orders.index', 'terminated')->with('success', 'The order has been deleted.');
    }

    public function getPricesForPackage(Package $package)
    {
        return $package->prices;
    }
}
