<div class="flex flex-wrap gap-4 mb-6 justify-center">
    @foreach($categories as $category)
        <div class="relative group">
            <a href="{{ route('pterodactyl.plugins.spigot', ['id' => $category['id'], 'order' => $order->id]) }}"
               class="px-4 py-2 bg-gray-200 text-gray-800 dark:bg-gray-800 dark:text-white rounded-md hover:bg-gray-300 dark:hover:bg-gray-700">
                {{ $category['name'] }}
            </a>
            @if(isset($category['children']) && !empty($category['children']))
                <div
                    class="absolute left-0 hidden mt-2 space-y-2 group-hover:block bg-white dark:bg-gray-800 shadow-lg rounded-md z-10">
                    @foreach($category['children'] as $childCategory)
                        <a href="{{ route('pterodactyl.plugins.spigot', ['id' => $childCategory['id'], 'order' => $order->id]) }}"
                           class="block px-4 py-2 text-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-700">
                            {{ $childCategory['name'] }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

    @endforeach
</div>
