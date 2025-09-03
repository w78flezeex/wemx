@extends(Theme::path('orders.master'))
@section('title', __('client.console') . ' | ' . $order->name)

@if(settings('encrypted::pterodactyl::api_admin_key', false))
    @section('content')
        <div class="container mx-auto">
            <div class="relative shadow-md" style="min-height: 80vh;">
                @includeIf(Theme::serviceView('pterodactyl', 'stats_bar'))

                <!-- Console Output Section -->
                <div class="dark:bg-gray-800 rounded p-2">
                    <div
                        class="overflow-y-auto h-96 p-3 text-xs bg-gray-50 border text-gray-900 dark:text-white dark:bg-gray-700 dark:border-gray-600 font-semibold"
                        id="console-output">
                        <!-- Console output will be here -->
                    </div>
                    <div class="mt-2 relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <i class='bx bxs-chevrons-right text-gray-900 dark:text-white'></i>
                        </div>
                        <label>
                            <input type="text" id="commandInput"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                   placeholder="{!! __('client.type_command') !!}">
                        </label>
                    </div>
                </div>

                <!-- Favorite Commands Section -->
                <div class="dark:bg-gray-800 rounded p-2 mt-4">
                    <div class="flex flex-wrap gap-2 justify-center" id="favorite-commands">
                        <!-- Favorite commands will be dynamically added here -->
                    </div>
                </div>

                <!-- Command Buttons Section -->
                <div class="dark:bg-gray-800 rounded p-2 mt-4">
                    <div class="flex flex-wrap justify-center gap-4">
                        <button type="button"
                                class="w-1/2 inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-3 py-1 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none"
                                id="open-modal-button">
                            {{ __('client.command') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Hidden Inputs with Data -->
            <input type="hidden" id="totalMemory" value="{{ $server['limits']['memory'] }}">
            <input type="hidden" id="totalDisk" value="{{ $server['limits']['disk'] }}">
            <input type="hidden" id="totalCPU" value="{{ $server['limits']['cpu'] }}">
            <input type="hidden" id="orderId" value="{{ $order->id }}">
            <input type="hidden" id="socketUrl"
                   value="{{ route('pterodactyl.console.socket', ['order' => $order->id]) }}">

            <!-- Hidden Elements with Translations -->
            <div class="hidden">
                <div id="translate-running">{{ Str::upper(__('client.running')) }}</div>
                <div id="translate-starting">{{ Str::upper(__('client.starting')) }}</div>
                <div id="translate-stopping">{{ Str::upper(__('client.stopping')) }}</div>
                <div id="translate-offline">{{ Str::upper(__('client.offline')) }}</div>
                <div id="translate-installing">{{ Str::upper(__('client.installing')) }}</div>
                <div id="translate-suspended">{{ Str::upper(__('admin.suspended')) }}</div>
                <div id="translate-updating">{{ Str::upper(__('client.updating')) }}</div>
            </div>
        </div>

        <!-- Command Modal -->
        <div id="command-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
            <!-- Modal Overlay -->
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <!-- Modal Content -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all max-w-3xl w-full">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <!-- Tabs -->
                    <div class="flex space-x-4">
                        <button id="history-tab"
                                class="bg-gray-200 dark:bg-gray-700 dark:text-white px-4 py-2 rounded-t-lg focus:outline-none">
                            {{ __('admin.history') }}
                        </button>
                        <button id="recommended-tab"
                                class="bg-gray-100 dark:bg-gray-600 dark:text-white px-4 py-2 rounded-t-lg focus:outline-none">
                            {{ __('client.command') }}
                        </button>
                    </div>
                    <!-- Close Button -->
                    <button id="close-modal"
                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <!-- Close Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <!-- Modal Body with Tabs -->
                <div class="p-4 max-h-96 overflow-y-auto">
                    <!-- History Content -->
                    <div id="history-content">
                        <div class="flex flex-wrap gap-2" id="history-list">
                            <!-- Command history will be dynamically added here -->
                        </div>
                    </div>
                    <!-- Recommended Content -->
                    <div id="recommended-content" class="hidden">
                        <div class="flex flex-wrap gap-2" id="recommended-list">
                            <!-- Recommended commands will be dynamically added here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="{{ Module::asset('pterodactyl:js/ansi_up.js') }}"></script>
        <script src="{{ Module::asset('pterodactyl:js/console.js?v=1.1.0') }}"></script>

    @endsection
@endif
