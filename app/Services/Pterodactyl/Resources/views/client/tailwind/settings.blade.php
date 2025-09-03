@extends(Theme::path('orders.master'))
@section('title', 'Settings | ' . $order->name)

@if(settings('encrypted::pterodactyl::api_admin_key', false))
    @php
        $permissions = collect($order->package->data('permissions', []));
        $inputClass = 'mt-1 block w-full h-8 py-1 px-2 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white';
        $selectClass = 'mt-1 block w-full h-8 py-1 px-2 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white';
        $btnClass = 'mt-3 px-3 py-1 bg-indigo-600 text-white text-xs rounded shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500';
        $launch_url = 'sftp://'.$user['username'].'.'.$server['identifier'].'@'.$server['sftp_details']['ip'].':'.$server['sftp_details']['port'];
    @endphp

    @section('content')
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded shadow overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <!-- Startup Command Card -->
                    <div class="mb-4 shadow-sm p-4 rounded bg-gray-50 dark:bg-gray-900">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ __('client.startup_command') }}</h3>
                        <textarea id="startup"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white mt-2"
                                  rows="4"
                                  disabled>{{ $data['meta']['startup_command'] }}</textarea>
                    </div>

                    <!-- Reinstall Button -->
                    <div class="mb-4 shadow-sm p-4 rounded bg-gray-50 dark:bg-gray-900">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ __('client.reinstall') }}</h3>
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">{{ __('client.reinstall_desc') }}</p>
                        <form method="GET"
                              action="{{ route('pterodactyl.settings.reinstall', ['order' => $order->id, 'server' => $server['identifier']]) }}">
                            @csrf
                            <button type="submit" onclick="return confirm('{{ __('client.reinstall_confirm') }}')"
                                    class="{{ $btnClass }} bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-400">
                                {{ __('client.reinstall') }}
                            </button>
                        </form>
                    </div>

                    <!-- SFTP Details -->
                    <div class="mb-4 shadow-sm p-4 rounded bg-gray-50 dark:bg-gray-900">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ __('client.sftp_details') }}</h3>
                        <div class="mt-3 grid grid-cols-1 gap-y-4 sm:grid-cols-2">
                            <div>
                                <label for="sftp-username"
                                       class="block text-xs font-medium text-gray-700 dark:text-gray-400">{{ __('auth.username') }}</label>
                                <input type="text" id="sftp-username" class="{{ $inputClass }}"
                                       value="{{ $user['username'].'.'.$server['identifier'] }}" disabled>
                            </div>
                            <div>
                                <label for="sftp-address"
                                       class="block text-xs font-medium text-gray-700 dark:text-gray-400">{{ __('auth.address') }}</label>
                                <input type="text" id="sftp-address" class="{{ $inputClass }}"
                                       value="{{ $server['sftp_details']['ip'].':'.$server['sftp_details']['port'] }}"
                                       disabled>
                            </div>
                        </div>
                        <div class="mt-4 flex space-x-2">
                            <a href="{{ $launch_url }}" target="_blank"
                               class="{{ $btnClass }} bg-primary-600 hover:bg-primary-700">
                                {{ __('client.launch_sftp') }}
                            </a>
                            <button type="button" data-drawer-target="drawer-change-password"
                                    data-drawer-show="drawer-change-password" data-drawer-placement="right"
                                    aria-controls="drawer-change-password"
                                    class="{{ $btnClass }} bg-yellow-500 hover:bg-yellow-600">
                                {{ __('client.change_password') }}
                            </button>
                        </div>
                    </div>

                    <!-- Server Name and Docker Image Update -->
                    <div class="mb-4 shadow-sm p-4 rounded bg-gray-50 dark:bg-gray-900">
                        @foreach (enabledModules() as $module)
                            @includeIf(Theme::moduleView($module->getLowerName(), 'pterodactyl.settings_block'))
                        @endforeach

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">

                            <form method="POST" class="shadow-sm p-2 bg-white dark:bg-gray-800 rounded"
                                  action="{{ route('pterodactyl.settings.rename', ['order' => $order->id, 'server' => $server['identifier']]) }}">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ __('client.name') }}</h3>
                                @csrf
                                <input type="text" id="server-name" name="name" class="{{ $inputClass }} mt-2"
                                       value="{{ $server['name'] }}">
                                <button type="submit"
                                        class="{{ $btnClass }} bg-primary-600 hover:bg-primary-700 mt-2 w-full">
                                    {{ __('client.rename') }}
                                </button>
                            </form>


                            <form method="POST" class="shadow-sm p-2 bg-white dark:bg-gray-800 rounded"
                                  action="{{ route('pterodactyl.settings.docker_image', ['order' => $order->id, 'server' => $server['identifier']]) }}">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ __('client.docker_image') }}</h3>
                                @csrf
                                <select id="docker-image" name="docker_image" class="{{ $selectClass }} mt-2">
                                    @foreach($data['meta']['docker_images'] as $key => $image)
                                        <option value="{{ $image }}"
                                                @if($image == $server['docker_image']) selected @endif>{{ $key }}
                                            ({{ $image }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                        class="{{ $btnClass }} bg-primary-600 hover:bg-primary-700 mt-2 w-full">
                                    {{ __('admin.update') }}
                                </button>
                            </form>

                            <!-- Server Variables Update -->
                            @if($permissions->get('pterodactyl.variables', 0) == 1)
                                @foreach($data['data'] as $variable)
                                    <form method="POST"
                                          action="{{ route('pterodactyl.settings.update_variable', ['order' => $order->id, 'server' => $server['identifier']]) }}"
                                          class="shadow-sm p-2 bg-white dark:bg-gray-800 rounded">
                                        @csrf
                                        @php($rules = ptero()::determineType(explode('|', $variable['attributes']['rules'])))
                                        <label for="{{ $variable['attributes']['env_variable'] }}"
                                               class="block text-xs font-medium text-gray-700 dark:text-gray-400">{{ $variable['attributes']['name'] }}</label>
                                        <input type="hidden" name="var_name"
                                               value="{{ $variable['attributes']['env_variable'] }}">
                                        @if($rules['type'] == 'bool')
                                            <select name="var_value" id="{{ $variable['attributes']['env_variable'] }}"
                                                    class="{{ $selectClass }}" {{ $variable['attributes']['is_editable'] ? '' : 'disabled' }}>
                                                <option
                                                    value="1" {{ $variable['attributes']['server_value'] == '1' ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                <option
                                                    value="0" {{ $variable['attributes']['server_value'] == '0' ? 'selected' : '' }}>{{ __('No') }}</option>
                                            </select>
                                        @elseif($rules['type'] == 'number')
                                            <input type="number" name="var_value"
                                                   id="{{ $variable['attributes']['env_variable'] }}"
                                                   min="{{ $rules['min'] ?? '' }}" max="{{ $rules['max'] ?? '' }}"
                                                   class="{{ $inputClass }}"
                                                   value="{{ $variable['attributes']['server_value'] }}" {{ $variable['attributes']['is_editable'] ? '' : 'disabled' }}>
                                        @elseif($rules['type'] == 'select')
                                            <select name="var_value" id="{{ $variable['attributes']['env_variable'] }}"
                                                    class="{{ $selectClass }}" {{ $variable['attributes']['is_editable'] ? '' : 'disabled' }}>
                                                @foreach($rules['options'] as $key => $value)
                                                    <option
                                                        value="{{ $key }}" {{ $key == $variable['attributes']['server_value'] ? 'selected' : '' }}>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="text" name="var_value"
                                                   id="{{ $variable['attributes']['env_variable'] }}"
                                                   class="{{ $inputClass }}"
                                                   value="{{ $variable['attributes']['server_value'] }}" {{ $variable['attributes']['is_editable'] ? '' : 'disabled' }}>
                                        @endif
                                        @if($variable['attributes']['is_editable'])
                                            <button type="submit"
                                                    class="{{ $btnClass }} bg-primary-600 hover:bg-primary-700 mt-2 w-full">
                                                {{ __('admin.update') }}
                                            </button>
                                        @else
                                            <span
                                                class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('responses.no_permission') }}</span>
                                        @endif
                                    </form>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Change Password Drawer -->
        <div id="drawer-change-password"
             class="fixed top-0 right-0 z-40 h-screen p-4 overflow-y-auto transition-transform translate-x-full bg-white w-72 dark:bg-gray-800"
             tabindex="-1" aria-labelledby="drawer-change-password-label">
            <h5 id="drawer-change-password-label" class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                {{ __('client.change_password') }}
            </h5>
            <button type="button" data-drawer-hide="drawer-change-password" aria-controls="drawer-change-password"
                    class="absolute top-3 right-3 text-gray-400 bg-transparent hover:bg-gray-200 rounded-full w-7 h-7 dark:hover:bg-gray-600">
                <span class="sr-only">{{ __('client.close_menu') }}</span>
            </button>
            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">{{ __('client.change_service_password', ['service' => $order->package->service]) }}</p>
            <form
                action="{{ route('pterodactyl.settings.change_password', ['order' => $order->id, 'server' => $server['identifier']]) }}"
                method="POST" class="mt-5">
                @csrf
                <div class="mb-4">
                    <label for="password"
                           class="block text-xs font-medium text-gray-900 dark:text-white">{{ __('auth.new_password') }}</label>
                    <input type="password" name="password" id="password" class="{{ $inputClass }}"
                           placeholder="{{ __('auth.new_password') }}" required>
                </div>
                <div class="mb-4">
                    <label for="password_confirmation"
                           class="block text-xs font-medium text-gray-900 dark:text-white">{{ __('auth.confirm_new_password') }}</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="{{ $inputClass }}" placeholder="{{ __('auth.confirm_new_password') }}" required>
                </div>
                <button type="submit" class="{{ $btnClass }} bg-green-500 hover:bg-green-600 w-full">
                    {{ __('client.change_password') }}
                </button>
            </form>
        </div>
    @endsection
@endif
