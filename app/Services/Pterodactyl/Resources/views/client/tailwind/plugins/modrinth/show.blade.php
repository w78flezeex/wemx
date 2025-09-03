@extends(Theme::path('orders.master'))
@section('title', $resource['title']. ' | ' . $order->name)

@section('content')
    <div class="container mx-auto">
        <div
            class="relative overflow-x-auto overflow-y-auto shadow-md sm:rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-400"
            style="height: 80vh;">
            <div class="p-6">
                <div class="flex flex-col md:flex-row space-y-6 md:space-y-0 md:space-x-6">
                    {{-- Icon and Title --}}
                    <div class="flex-shrink-0 text-center md:text-left">
                        <img src="{{ $resource['icon_url'] }}" alt="{{ $resource['title'] }} Icon"
                             class="w-32 h-32 rounded-lg shadow-md mx-auto md:mx-0">
                    </div>

                    <div class="flex-1">
                        {{-- Plugin Title and Description --}}
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $resource['title'] }}</h2>
                        <p class="mt-2 text-base text-gray-600 dark:text-gray-300">{{ $resource['description'] }}</p>

                        {{-- Links and Actions --}}
{{--                        <div class="mt-4">--}}
{{--                            <div class="flex flex-wrap gap-2">--}}
{{--                                @if($resource['source_url'])--}}
{{--                                    <a href="{{ $resource['source_url'] }}" target="_blank"--}}
{{--                                       class="inline-flex items-center justify-center bg-primary-600 hover:bg-primary-700 text-white font-medium py-1.5 px-3 rounded-md shadow focus:outline-none focus:ring-2 focus:ring-primary-500">--}}
{{--                                        Source Code--}}
{{--                                    </a>--}}
{{--                                @endif--}}
{{--                                @if($resource['issues_url'])--}}
{{--                                    <a href="{{ $resource['issues_url'] }}" target="_blank"--}}
{{--                                       class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-medium py-1.5 px-3 rounded-md shadow focus:outline-none focus:ring-2 focus:ring-red-500">--}}
{{--                                        Report Issues--}}
{{--                                    </a>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                </div>

                {{-- Tabs for Description and Versions --}}
                <div class="mt-8">
                    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab"
                            data-tabs-toggle="#default-tab-content" role="tablist">
                            <li class="me-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="description-tab"
                                        data-tabs-target="#profile" type="button" role="tab" aria-controls="profile"
                                        aria-selected="false">Description
                                </button>
                            </li>
                            @if(!empty($resource['gallery']))
                                <li class="me-2" role = "presentation">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="gallery-tab"
                                            data-tabs-target="#gallery" type="button" role="tab" aria-controls="gallery"
                                            aria-selected="false">Gallery
                                    </button>
                                </li>
                            @endif
                            <li class="me-2" role="presentation">
                                <button
                                    class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                    id="versions-tab" data-tabs-target="#dashboard" type="button" role="tab"
                                    aria-controls="dashboard" aria-selected="false">Versions
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div id="default-tab-content">
                        <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="profile" role="tabpanel"
                             aria-labelledby="description-tab">
                            <div
                                class="text-base text-gray-700 dark:text-gray-300 prose dark:prose-invert w-full max-w-3xl mx-auto">
                                {!! Str::markdown($resource['body']) !!}
                            </div>
                        </div>
                        @if(!empty($resource['gallery']))
                            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="gallery" role="tabpanel"
                                 aria-labelledby="gallery-tab">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                    @foreach($resource['gallery'] as $image)
                                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                                            <img src="{{ $image['raw_url'] ?? $image['url'] }}" class="w-full h-48 object-cover rounded-lg" alt="img">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="dashboard" role="tabpanel"
                             aria-labelledby="versions-tab">
                            @if(!empty($resource['versions']))
                                <div class="mt-4">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Version
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Game Versions
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Loaders
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Date
                                                </th>
                                                <th scope="col" class="relative px-6 py-3"><span
                                                        class="sr-only">Install</span></th>
                                            </tr>
                                            </thead>
                                            <tbody
                                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($resource['versions'] as $version)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                        {{ $version['version_number'] }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                                        {{ implode(', ', $version['game_versions']) }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                                        {{ implode(', ', $version['loaders']) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                        {{ \Carbon\Carbon::parse($version['date_published'])->format('F j, Y') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <a href="{{ route('pterodactyl.plugins.modrinth.install', ['order' => $order->id, 'version_id' => $version['id'], 'project_id' => $resource['id']]) }}"
                                                           class="text-green-600 hover:text-green-900">Install</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>


                {{-- Added padding to the bottom --}}
                <div class="pb-6"></div>
            </div>
        </div>
    </div>
@endsection
