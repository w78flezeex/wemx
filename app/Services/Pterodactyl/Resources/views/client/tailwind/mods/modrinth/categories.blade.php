<div class="flex flex-wrap gap-4 mb-6 justify-center">
    @foreach($categories as $category)
        <div class="relative group">
            <a href="{{ route('pterodactyl.mods.modrinth', ['id' => $category, 'order' => $order->id]) }}"
               class="px-4 py-2 bg-gray-200 text-gray-800 dark:bg-gray-800 dark:text-white rounded-md hover:bg-gray-300 dark:hover:bg-gray-700">
                {{ ucfirst($category) }}
            </a>
        </div>
    @endforeach
</div>
