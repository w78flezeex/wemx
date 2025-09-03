<div id="plugin-list" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    @foreach($resources as $plugin)
        <div class="plugin-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 flex flex-col justify-between">
            <div>
                <div class="flex justify-center mb-4">
                    <img
                        src="{{ empty($plugin['icon_url']) ? 'https://avatars.githubusercontent.com/u/67560307?s=48&v=4' : $plugin['icon_url'] }}"
                        class="w-24 h-24 object-contain">
                </div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $plugin['title'] }}</h4>
                <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                    {{ Str::limit($plugin['description'], 50) }}
                </p>
            </div>
            <div class="flex justify-between mt-auto">
                <a href="{{ 'https://modrinth.com/plugin/' . $plugin['slug'] }}"
                   class="px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600" target="_blank">
                    <i class="bx bxs-blanket"></i>
                </a>
                <a href="{{ route('pterodactyl.plugins.modrinth.show', ['order' => $order->id, 'project_id' => $plugin['project_id']]) }}"
                   class="px-3 py-1 bg-green-500 text-white rounded text-sm hover:bg-green-600">{!! __('admin.view') !!}</a>
            </div>
        </div>
    @endforeach
</div>
