@if(cf()::where('package_id', $order->package->id)->exists())
    @php
        $subdomain = cf()::getOrderSubdomain($order->id);
        $allocation = collect($server['relationships']['allocations']['data'])
            ->first(fn($allocation) => $allocation['attributes']['is_default'] == true);
    @endphp
    <div class="shadow-lg p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            {{ __('client.domain') }}
            <span class="block text-sm text-gray-500 dark:text-gray-400">
                {{ $subdomain->domain_data['subdomain'] ?? '' }}.{{ $subdomain->domain_data['domain'] ?? '' }}
            </span>
        </h3>
        <form method="POST" action="{{ route('cf.pterodactyl.save.domain') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            <input type="hidden" name="ip" value="{{ $allocation['attributes']['ip'] ?? '' }}">
            <input type="hidden" name="port" value="{{ $allocation['attributes']['port'] ?? '' }}">

            <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:space-x-4">
                <!-- Subdomain Input -->
                <div class="flex-1">
                    <input
                        type="text"
                        name="subdomain"
                        id="subdomain"
                        class="{{ $inputClass }}"
                        placeholder="{{ __('client.subdomain_placeholder') }}"
                        value="{{ $subdomain->domain_data['subdomain'] ?? '' }}">
                </div>

                <!-- Domain Select -->
                <div class="flex-1">
                    <select
                        id="domain"
                        name="domain"
                        class="{{ $inputClass }}">
                        @foreach(cf()::getDomainsByPackage($order->package->id) as $id => $domain)
                            <option value="{{ $id }}::{{ $domain }}"
                                    @if($id == ($subdomain->domain_data['id'] ?? '')) selected @endif>
                                {{ $domain }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit"
                    class="{{ $btnClass }} bg-primary-600 hover:bg-primary-700 mt-2 w-full">
                {{ __('admin.update') }}
            </button>
        </form>
    </div>
@endif
