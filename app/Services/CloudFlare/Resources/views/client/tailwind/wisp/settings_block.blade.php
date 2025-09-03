@php
    $inputClass = 'mt-1 block w-full h-8 py-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white';
    $selectClass = 'mt-1 block w-full h-8 py-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white';
    $btnClass = 'mt-3 inline-flex justify-center py-1 px-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white';
@endphp
@if(cf()::where('package_id', $order->package->id)->exists())
    @php($subdomain = cf()::getOrderSubdomain($order->id, 'wisp'))

    <div class="p-6 text-center bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700 mt-5">
        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{{ __('client.domain') }} ({{ $subdomain->domain_data['subdomain'] ?? '' }}.{{ $subdomain->domain_data['domain'] ?? '' }})</h3>
        <form method="POST" action="{{ route('cf.pterodactyl.save.domain', 'wisp') }}" class="flex items-end space-x-2">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            <input type="hidden" name="ip" value="{{ $order->data['primary_ip'] }}">
            <input type="hidden" name="port" value="{{ $order->data['primary_port'] }}">
            <input name="subdomain" id="subdomain" class="flex-1 {{ $inputClass }}" placeholder="Subdomain"
                   value="{{ $subdomain->domain_data['subdomain'] ?? '' }}">
            <select id="domain" name="domain" class="flex-1 {{ $selectClass }}">
                @foreach(cf()::getDomainsByPackage($order->package->id) as $id => $domain)
                    <option value="{{ $id }}::{{ $domain }}" @if($id == ($subdomain->domain_data['id'] ?? '')) selected @endif>
                        {{ $domain }}
                    </option>
                @endforeach
            </select>
            <button type="submit"
                    class="{{ $btnClass }} bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                {!! __('admin.update') !!}
            </button>
        </form>
    </div>
@endif
