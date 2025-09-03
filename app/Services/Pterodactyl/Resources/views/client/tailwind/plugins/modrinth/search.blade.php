<div class="flex justify-center mb-6">
    <form action="{{ route('pterodactyl.plugins.modrinth', ['order' => $order->id]) }}" method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.search') }}"
               class="px-3 py-1 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="submit"
                class="px-3 py-1 bg-blue-500 text-white rounded-md text-sm hover:bg-blue-600 dark:bg-blue-700 dark:hover:bg-blue-600">
            {{ __('admin.search') }}
        </button>
    </form>
</div>
