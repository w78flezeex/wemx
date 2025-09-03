@extends(Theme::path('orders.master'))
@section('title', 'Plugins | ' . $order->name)

@if(settings('encrypted::pterodactyl::api_admin_key', false))
    @section('content')
        <div class="flex flex-wrap gap-4 mb-6 justify-center">
            <a href="{{ route('pterodactyl.plugins.spigot', ['order' => $order->id]) }}"
               class="px-4 py-2 bg-gray-200 text-gray-800 dark:bg-gray-800 dark:text-white rounded-md hover:bg-gray-300 dark:hover:bg-gray-700">@lang('Spigot')</a>
            <a href="{{ route('pterodactyl.plugins.modrinth', ['order' => $order->id]) }}"
               class="px-4 py-2 bg-gray-200 text-gray-800 dark:bg-gray-800 dark:text-white rounded-md hover:bg-gray-300 dark:hover:bg-gray-700">@lang('Modrinth')</a>
        </div>



        <div class="container mx-auto">
            <div class="relative overflow-x-auto overflow-y-auto shadow-md sm:rounded-lg" style="height: 80vh;">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">
                            {!! __('client.name') !!}
                        </th>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">
                            {!! __('client.size') !!}
                        </th>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 text-end whitespace-nowrap">
                            {!! __('client.actions') !!}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-600">
                    @foreach($installed as $plugin)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-2 dark:text-gray-300 whitespace-nowrap">{{ $plugin['name'] }}</td>
                            <td class="px-4 py-2 dark:text-gray-300 whitespace-nowrap">{{ $plugin['size'] }}</td>
                            <td class="px-2 py-1">
                                <div class="flex justify-end items-center space-x-2">
                                    {{-- Toggle Plugin (Enable/Disable) --}}
                                    <form
                                        action="{{ route('pterodactyl.plugins.toggle', ['order' => $order->id, 'server' => $server['identifier']]) }}"
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="name" value="{{ $plugin['name'] }}">
                                        @if(str_contains($plugin['name'], '.disabled' ))
                                            @php($toggleClass = 'bg-green-500 hover:bg-green-700')
                                        @else
                                            @php($toggleClass = 'bg-yellow-500 hover:bg-yellow-700')
                                        @endif

                                        <button type="submit"
                                                class="{{ $toggleClass }} text-white font-bold py-0.5 px-2 text-sm rounded">
                                            <i class="bx bx-power-off"></i>
                                        </button>
                                    </form>

                                    {{-- Delete Plugin --}}
                                    <form
                                        action="{{ route('pterodactyl.plugins.delete', ['order' => $order->id, 'server' => $server['identifier']]) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="name" value="{{ $plugin['name'] }}">
                                        <button type="submit"
                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-0.5 px-2 text-sm rounded"
                                                onclick="return confirm('Are you sure?')">
                                            <i class="bx bxs-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>


                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endsection
@endif
