<div id="plugin-list" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    @foreach($plugins as $plugin)
        <div class="plugin-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 flex flex-col justify-between">
            <div>
                <div class="flex justify-center mb-4">
                    <img src="{{ $plugin['icon']['data'] ? 'data:image/png;base64,' . $plugin['icon']['data'] : 'https://static.spigotmc.org/img/spigot.png' }}" class="w-24 h-24 object-contain">
                </div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $plugin['name'] }}</h4>
                <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                    {{ Str::limit($plugin['tag'], 50) }}
                </p>
            </div>
            <div class="flex justify-between mt-auto">
                <a href="{{ 'https://www.spigotmc.org/resources/' . $plugin['id'] }}"
                   class="px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600" target="_blank">
                    {!! __('client.details') !!}
                </a>
                <a href="{{ route('pterodactyl.plugins.spigot.install', ['order' => $order->id, 'resource' => $plugin['id']]) }}" class="px-3 py-1 bg-green-500 text-white rounded text-sm hover:bg-green-600">{!! __('admin.install') !!}</a>
            </div>
        </div>
    @endforeach
</div>
