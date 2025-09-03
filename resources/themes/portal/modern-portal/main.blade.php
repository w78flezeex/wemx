<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@settings('seo::title', 'WemX')</title>
    <link rel="icon" href="@settings('favicon', 'https://imgur.com/oJDxg2r.png')">

    {{-- meta tags --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Meta Description Tag: Affects click-through rates from search results -->
    <meta name="description" content="{{ $meta_description ?? settings('seo::description', 'Manage your orders with an easy-to-use Dashboard') }}">
    <meta name="theme-color" content="@settings('seo::color', '#4f46e5')">
    <meta name="keywords" content="{{ $meta_keywords ?? settings('seo::keywords', '')}}">

    <!-- Meta Robots Tag: Controls search engine crawling and indexing -->
    <meta name="robots" content="@settings('seo::robots', 'index, follow')">

    <!-- Open Graph Tags: Enhances visibility and engagement on social media platforms -->
    <meta property="og:title" content="@settings('seo::title', 'WemX')">
    <meta property="og:description" content="{{ $meta_description ?? settings('seo::description', 'Manage your orders with an easy-to-use Dashboard') }}">
    <meta property="og:image" content="@settings('seo::image', '/static/wemx.png')">

    <link rel="stylesheet" href="{{ Theme::get('Default')->assets }}assets/css/custom.css">
    <link rel="stylesheet" href="{{ Theme::get('Default')->assets }}assets/css/badge.css">

    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    @if (settings('portal::snow', false))
        <script src="{{ Theme::get('Default')->assets }}assets/js/snow.js"></script>
    @endif

    <script src="https://cdn.tailwindcss.com/"></script>

    @include(Portal::path('layouts.tailwind'))
</head>
<body>
@include(Portal::path('layouts.header'))
@if (settings('portal::snow', false))
    <div class="snow-container"></div>
@endif
<section id="home" class="bg-[url('@settings('portal::header_background')')]">
    <div class="grid py-8 px-4 mx-auto max-w-screen-xl lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
        <div class="place-self-center mr-auto lg:col-span-7">


            @if(settings('announcements::dashboard_notice') == "info")
                <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                    <span class="font-medium">@settings('announcements::dashboard_notice', 'SET TEXT ON ADMIN DASHBOARD')</span>
                </div>
            @elseif(settings('announcements::dashboard_notice') == "danger")
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <span class="font-medium">@settings('announcements::dashboard_notice', 'SET TEXT ON ADMIN DASHBOARD')</span>
                </div>
            @elseif(settings('announcements::dashboard_notice') == "success")
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    <span class="font-medium">@settings('announcements::dashboard_notice', 'SET TEXT ON ADMIN DASHBOARD')</span>
                </div>
            @elseif(settings('announcements::dashboard_notice') == "warning")
                <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300" role="alert">
                    <span class="font-medium">@settings('announcements::dashboard_notice', 'SET TEXT ON ADMIN DASHBOARD')</span>
                </div>
            @endif



            @if (settings('portal::announcement', false))
                @if(settings('portal::announcement-type') == "info")
                    <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                        <span class="font-medium">@settings('portal::announcement-text', 'SET TEXT ON ADMIN DASHBOARD')</span>
                    </div>
                @elseif(settings('portal::announcement-type') == "danger")
                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                        <span class="font-medium">@settings('portal::announcement-text', 'SET TEXT ON ADMIN DASHBOARD')</span>
                    </div>
                @elseif(settings('portal::announcement-type') == "success")
                    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                        <span class="font-medium">@settings('portal::announcement-text', 'SET TEXT ON ADMIN DASHBOARD')</span>
                    </div>
                @elseif(settings('portal::announcement-type') == "warning")
                    <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300" role="alert">
                        <span class="font-medium">@settings('portal::announcement-text', 'SET TEXT ON ADMIN DASHBOARD')</span>
                    </div>
                @endif
            @endif

            <h1 class="mb-4 max-w-2xl text-4xl font-extrabold leading-none md:text-5xl xl:text-6xl text-[@settings('portal::title_color', '#ffffff')]">
                @settings('portal::title', 'Minecraft Server Hosting')
            </h1>
            <p class="mb-6 max-w-2xl font-light lg:mb-8 md:text-lg lg:text-xl text-[@settings('portal::description_color', '#ffffff')]">
                @settings('portal::description', 'Start your Minecraft server today for as low as $1/GB and be equiped for all situations with our high performance gear keeping your servers running 24/7')
            </p>
            <a href="#pricing" class="inline-flex justify-center items-center py-3 px-5 mr-3 text-base font-medium text-center rounded-lg bg-[@settings('portal::button_color', '#9f21c2')] hover:bg-[@settings('portal::button_hover_color', '#9f21c2')] focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900 text-[@settings('portal::button_text_color', '#ffffff')]">
                @settings('portal::button',  __('client.explore_plans') )
                <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </a>
            <a href="@settings('socials::discord')" class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-gray-900 rounded-lg border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:text-white dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                {!! __('auth.join_discord') !!}
            </a>
        </div>
        <div class="hidden lg:mt-0 lg:col-span-5 lg:flex">
            <img src="@settings('portal::default::header_image', 'https://www.freepnglogos.com/uploads/minecraft-png/download-minecraft-characters-png-png-image-pngimg-29.png')" alt="{!! __('mockup') !!}">
        </div>
    </div>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="@settings('portal::color', '#0099ff')" fill-opacity="1" d="M0,288L48,272C96,256,192,224,288,197.3C384,171,480,149,576,165.3C672,181,768,235,864,250.7C960,267,1056,245,1152,250.7C1248,256,1344,288,1392,304L1440,320L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>
</section>

<section id="features" class="bg-[@settings('portal::color', '#0099ff')]">
    <div class="py-8 px-4 mx-auto max-w-screen-xl text-center sm:py-16 lg:px-6">
        <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-[@settings('portal::features_title_color', '#ffffff')]">{!! __('client.our_game_anel') !!}</h2>
        <p class="sm:text-xl text-[@settings('portal::features_subtitle_color', '#cccccc')]">{!! __('client.our_game_anel_desc', ['app' => settings('app_name', 'WemX')]) !!}</p>
        <div class="mt-8 lg:mt-12 space-y-8 md:grid md:grid-cols-2 lg:grid-cols-3 md:gap-12 md:space-y-0">
            <div>
                <span class="bx-lg mx-auto mb-4 text-[@settings('portal::features_icon_color', '#3457D5')]">
                    <i class="bx bxs-file"></i>
                </span>
                <h3 class="mb-2 text-xl font-bold text-[@settings('features_titles_color', '#ffffff')]">{!! __('client.file_manager') !!}</h3>
                <p class="text-[@settings('portal::features_subtitle_color', '#cccccc')]">
                    {!! __('client.file_manager_desc') !!}
                </p>
            </div>

            <div>
                <span class="bx-lg mx-auto mb-4 text-[@settings('portal::features_icon_color', '#3457D5')]">
                    <i class="bx bxs-plug"></i>
                </span>
                <h3 class="mb-2 text-xl font-bold text-[@settings('features_titles_color', '#ffffff')]">{!! __('client.plugins_manager') !!}</h3>
                <p class="text-[@settings('portal::features_subtitle_color', '#cccccc')]">
                    {!! __('client.plugins_manager_desc') !!}
                </p>
            </div>

            <div>
                <span class="bx-lg mx-auto mb-4 text-[@settings('portal::features_icon_color', '#3457D5')]">
                    <i class="bx bx-revision"></i>
                </span>
                <h3 class="mb-2 text-xl font-bold text-[@settings('features_titles_color', '#ffffff')]">{!! __('client.backup_manager') !!}</h3>
                <p class="text-[@settings('portal::features_subtitle_color', '#cccccc')]">
                    {!! __('client.backup_manager_desc') !!}
                </p>
            </div>

            <div>
                <span class="bx-lg mx-auto mb-4 text-[@settings('portal::features_icon_color', '#3457D5')]">
                    <i class="bx bxs-user"></i>
                </span>
                <h3 class="mb-2 text-xl font-bold text-[@settings('features_titles_color', '#ffffff')]">{!! __('client.user_manager') !!}</h3>
                <p class="text-[@settings('portal::features_subtitle_color', '#cccccc')]">
                    {!! __('client.user_manager_desc') !!}
                </p>
            </div>

            <div>
                <span class="bx-lg mx-auto mb-4 text-[@settings('portal::features_icon_color', '#3457D5')]">
                    <i class="bx bxs-time-five"></i>
                </span>
                <h3 class="mb-2 text-xl font-bold text-[@settings('features_titles_color', '#ffffff')]">{!! __('client.schedule_manager') !!}</h3>
                <p class="text-[@settings('portal::features_subtitle_color', '#cccccc')]">
                    {!! __('client.schedule_manager_desc') !!}
                </p>
            </div>

            <div>
                <span class="bx-lg mx-auto mb-4 text-[@settings('portal::features_icon_color', '#3457D5')]">
                    <i class="bx bxs-data"></i>
                </span>
                <h3 class="mb-2 text-xl font-bold text-[@settings('features_titles_color', '#ffffff')]">{!! __('client.database_manager') !!}</h3>
                <p class="text-[@settings('portal::features_subtitle_color', '#cccccc')]">
                    {!! __('client.database_manager_desc') !!}
                </p>
            </div>
        </div>
    </div>
</section>

<section class="bg-[url('@settings('portal::pricing_background')')]" id="pricing">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="@settings('portal::color', '#0099ff')" fill-opacity="1" d="M0,128L48,149.3C96,171,192,213,288,208C384,203,480,149,576,112C672,75,768,53,864,85.3C960,117,1056,203,1152,240C1248,277,1344,267,1392,261.3L1440,256L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path></svg>
    <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
        @if($selected_category !== NULL)
            <div class="mx-auto max-w-screen-md text-center mb-8 lg:mb-12">
                <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">{!! __('client.viewing_plans_for') !!} {{ $selected_category->name }}</h2>
                <p class="mb-5 font-light text-gray-500 sm:text-xl dark:text-gray-400">{{ $selected_category->description }}</p>
            </div>
            @if ($selected_category->packages->isEmpty())
                <div class="mx-auto max-w-screen-md text-center content-center mb-8 lg:mb-12">
                    <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-white">{{ settings('portal::empity_package', 'Coming soon') }}</h2>
                </div>
            @endif
            <div class="space-y-8 lg:grid lg:grid-cols-3 sm:gap-6 xl:gap-10 lg:space-y-0">
                @foreach ($selected_category->packages as $package)

                    @if($package->status == 'unlisted' OR $package->status == 'inactive' OR $package->status == 'restricted')
                        @if($package->status == 'restricted')
                            @if(Auth::guest() OR !Auth::user()->is_admin())
                                @continue
                            @endif
                        @else
                            @continue
                        @endif
                    @endif

                        @if(settings('portal::pricing_mode', false))
                            <div class="card-container flex flex-col p-6 mx-auto max-w-lg text-center text-gray-900 bg-white rounded-lg border border-gray-100 shadow dark:border-gray-600 xl:p-8 dark:bg-gray-800 dark:text-white">
                                @if(settings('portal::show_badge-off', false))
                                    @if(!empty($package->prices->first()->data['portal']))
                                        <div class="pt-ribbon-wrapper">
                                            <div class="pt-ribbon">{{ $package->prices->first()->data['portal'] }}</div>
                                        </div>
                                    @endif
                                @endif
                                <h3 class="mb-4 text-2xl font-semibold">{{ $package->name }}</h3>
                                @if(settings('portal::show_badge_stock', false))
                                    <p class="font-light sm:text-lg text-gray-400">Stock:

                                        @if($package->global_quantity == -1)
                                            <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">In stock</span>
                                        @elseif($package->global_quantity == 0)
                                            <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Stock out</span>
                                        @elseif($package->global_quantity <= @settings('portal::min-stock','5'))
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Low stock</span>
                                        @else
                                            <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">In stock</span>
                                        @endif

                                    </p>
                                @endif
                                <p class="font-light text-gray-500 sm:text-lg dark:text-gray-400">
                                    {!! __('client.price_block_desc', [
                                        'period' => mb_strtolower($package->prices->first()->period()),
                                        'total_price' => $package->prices->first()->totalPrice(),
                                        'renewal_price' => $package->prices->first()->renewal_price,
                                        'per_period' => mb_strtolower($package->prices->first()->periodToHuman()),
                                        'symbol' => currency('symbol')
                                     ]) !!}
                                </p>

                                    @if(settings('portal::current_position') == "left")
                                        <div class="flex justify-center items-baseline my-8">
                                            <span class="mr-2 text-5xl font-extrabold">{{ currency('symbol') }}{{ $package->prices->first()->renewal_price }}</span>
                                            <span class="text-gray-500 dark:text-gray-400">/{{ $package->prices->first()->periodToHuman() }}</span>
                                        </div>
                                    @elseif(settings('portal::current_position') == "right")
                                        <div class="flex justify-center items-baseline my-8">
                                            <span class="mr-2 text-5xl font-extrabold">{{ $package->prices->first()->renewal_price }}{{ currency('symbol') }}</span>
                                            <span class="text-gray-500 dark:text-gray-400">/{{ $package->prices->first()->periodToHuman() }}</span>
                                        </div>
                                    @endif
                                <!-- List -->
                                <ul role="list" class="mb-8 space-y-4 text-left">
                                    <img class="w-full h-auto" src="{{ asset('storage/products/' . $package->icon) }}"
                                         alt="icon"/>
                                    <div class="grid grid-cols-1 gap-4">
                                        @foreach($package->features()->orderBy('order', 'desc')->get() as $feature)
                                            <li class="flex items-center space-x-3">
                                                <!-- Icon -->
                                                <span class="text-{{$feature->color}}-500 dark:text-{{$feature->color}}-500 bx-sm">
                                            {!! $feature->icon !!}
                                        </span>
                                                <span>{{ $feature->description }}</span>
                                            </li>
                                        @endforeach
                                    </div>
                                </ul>
                                <a href="{{ route('store.package', ['package' => $package->id]) }}" class="text-[@settings('portal::button_text_color', '#ffffff')] bg-[@settings('portal::button_color', '#9f21c2')] hover:bg-[@settings('portal::button_hover_color', '#9f21c2')] focus:ring-4 focus:ring-primary-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">{!! __('client.get_started') !!}</a>
                            </div>
                        @else
                            <div class="card-container flex flex-col max-w-sm p-6 text-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                                <div class="grid py-8 px-4 mx-auto max-w-screen-xl lg:gap-8 xl:gap-0 lg:grid-cols-12">
                                    @if(settings('portal::show_badge-off', false))
                                        @if(!empty($package->prices->first()->data['portal']))
                                            <div class="pt-ribbon-wrapper">
                                                <div class="pt-ribbon">{{ $package->prices->first()->data['portal'] }}</div>
                                            </div>
                                        @endif
                                    @endif
                                    <div class="place-self-center mr-auto lg:col-span-8">
                                        <h3 class="mb-4 text-2xl font-semibold">{{ $package->name }}</h3>
                                        @if(settings('portal::show_badge_stock', false))
                                            <p class="font-light sm:text-lg text-gray-400">Stock:

                                                @if($package->global_quantity == -1)
                                                    <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">In stock</span>
                                                @elseif($package->global_quantity == 0)
                                                    <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Stock out</span>
                                                @elseif($package->global_quantity <= @settings('portal::min-stock','5'))
                                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Low stock</span>
                                                @else
                                                    <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">In stock</span>
                                                @endif

                                            </p>
                                        @endif

                                        @if(settings('portal::current_position') == "left")
                                            <div class="flex justify-center items-baseline my-8">
                                                <span class="mr-2 text-5xl font-extrabold">{{ currency('symbol') }}{{ $package->prices->first()->renewal_price }}</span>
                                                <span class="text-gray-500 dark:text-gray-400">/{{ $package->prices->first()->periodToHuman() }}</span>
                                            </div>
                                        @elseif(settings('portal::current_position') == "right")
                                            <div class="flex justify-center items-baseline my-8">
                                                <span class="mr-2 text-5xl font-extrabold">{{ $package->prices->first()->renewal_price }}{{ currency('symbol') }}</span>
                                                <span class="text-gray-500 dark:text-gray-400">/{{ $package->prices->first()->periodToHuman() }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="hidden lg:mt-4 lg:col-span-4 lg:flex">
                                        <img class="w-32 h-24" src="{{ asset('storage/products/' . $package->icon) }}" alt="icon"/>
                                    </div>
                                </div>

                                <ul role="list" class="mb-8 space-y-4 text-left">
                                    <div class="grid grid-cols-1 gap-4">
                                        @foreach($package->features()->orderBy('order', 'desc')->get() as $feature)
                                            <li class="flex items-center space-x-3">
                                                <!-- Icon -->
                                                <span class="text-{{$feature->color}}-500 dark:text-{{$feature->color}}-500 bx-sm">
                                        {!! $feature->icon !!}
                                    </span>
                                                <span>{{ $feature->description }}</span>
                                            </li>
                                        @endforeach
                                    </div>
                                </ul>
                                <a href="{{ route('store.package', ['package' => $package->id]) }}" class="text-[@settings('portal::button_text_color', '#ffffff')] bg-[@settings('portal::button_color', '#9f21c2')] hover:bg-[@settings('portal::button_hover_color', '#9f21c2')] focus:ring-4 focus:ring-primary-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">{!! __('client.get_started') !!}</a>
                            </div>
                        @endif
                @endforeach
            </div>
    </div>
    @else
        <div class="mx-auto max-w-screen-md text-center mb-8 lg:mb-12">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">
                {!! __('client.no_plans_display') !!}</h2>
            <p class="mb-5 font-light text-gray-500 sm:text-xl dark:text-gray-400">
                {!! __('client.no_plans_display_desc') !!}</p>
        </div>
        @endif
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="@settings('portal::color', '#0099ff')" fill-opacity="1" d="M0,128L48,122.7C96,117,192,107,288,106.7C384,107,480,117,576,138.7C672,160,768,192,864,197.3C960,203,1056,181,1152,181.3C1248,181,1344,203,1392,213.3L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>
</section>

<section>
    <aside aria-label="Related articles" class="py-8 lg:py-15 bg-[@settings('portal::color', '#0099ff')]">
        <div class="px-4 mx-auto max-w-screen-xl">
            <h2 class="mb-8 text-2xl font-bold text-gray-900 dark:text-white">{!! __('client.other_categories') !!}</h2>
            <div class="grid gap-12 sm:grid-cols-2 lg:grid-cols-3">

                @foreach ($categories->all() as $category)
               @if(in_array($category->status, ['unlisted', 'inactive', 'restricted']))
                     @if($category->status == 'restricted')
                         @if(Auth::guest() OR !Auth::user()->is_admin())
                             @continue
                         @endif
                     @else
                         @continue
                     @endif
                 @endif
                    <article class="max-w-xs">
                        <a href="{{ url()->current() }}?category={{ $category->link }}#pricing">
                            <img src="{{ $category->icon() }}" class="mb-5 rounded-lg" alt="Image 1" style="height: 200px;">
                        </a>
                        <h2 class="mb-2 text-xl font-bold leading-tight text-gray-900 dark:text-white">
                            <a href="#">{{ $category->name }}</a>
                        </h2>
                        <p class="mb-4 font-light text-gray-500 dark:text-gray-400">{{ $category->description }}</p>
                        <a href="{{ url()->current() }}?category={{ $category->link }}#pricing"
                           class="py-2.5 px-5 mr-2 mb-2 min-w-full text-center text-sm font-medium bg-[@settings('portal::button_color', '#9f21c2')] text-[@settings('portal::button_text_color', '#ffffff')] focus:outline-none rounded-lg hover:bg-[@settings('portal::button_hover_color', '#9f21c2')] hover:text-white focus:z-10 focus:ring-4 focus:ring-gray-200">@if(request()->input('category', (isset($selected_category->link)) ? $selected_category->link : '') == $category->link)
                                {!! __('client.selected') !!}
                            @else
                                {!! __('client.pricing') !!}
                            @endif</a>
                    </article>
                @endforeach

            </div>
        </div>
    </aside>
</section>

<section class="bg-[url('@settings('portal::user_register_background')')]">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="@settings('portal::color', '#0099ff')" fill-opacity="1" d="M0,128L48,122.7C96,117,192,107,288,106.7C384,107,480,117,576,138.7C672,160,768,192,864,197.3C960,203,1056,181,1152,181.3C1248,181,1344,203,1392,213.3L1440,224L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path></svg>
    <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
        <div class="mx-auto max-w-screen-sm text-center">
            <h2 class="mb-4 text-4xl font-extrabold leading-tight text-gray-900 dark:text-white">{!! __('client.get_started_now') !!}</h2>
            <p class="mb-6 font-light text-gray-500 dark:text-gray-400 md:text-lg">
                {!! __('client.register_your_account_no-time') !!}
            </p>
            <a href="{{ route('register') }}" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                {!! __('client.create_my_account') !!}
            </a>
        </div>
    </div>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#1F2937" fill-opacity="1" d="M0,256L80,245.3C160,235,320,213,480,208C640,203,800,213,960,218.7C1120,224,1280,224,1360,224L1440,224L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path></svg>
</section>

@include(Theme::path('layouts.cookie'))

<footer class="p-4 bg-gray-50 sm:p-6 dark:bg-gray-800">
    <div class="mx-auto max-w-screen-xl">
        <div class="md:flex md:justify-between">
            <div class="mb-6 md:mb-0">
                <a href="/" class="flex items-center">
                    <img src="@settings('logo', 'https://imgur.com/oJDxg2r.png')" class="mr-3 h-8"
                         alt="@settings('app_name', 'WemX') Logo"/>
                    <span
                        class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">@settings('app_name', 'WemX')</span>
                </a>
            </div>
            <div class="grid grid-cols-2 gap-8 sm:gap-6 sm:grid-cols-3">
                <div>
                    <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">{!! __('client.resources') !!}</h2>
                    <ul class="text-gray-600 dark:text-gray-400">
                        @foreach(Page::getActive() as $page)
                            @if(in_array('footer_resources', $page->placement))
                                <li class="mb-4">
                                    <a href="{{ route('page', $page->path) }}" @if($page->new_tab) target="_blank"
                                       @endif class="hover:underline">{{ $page->name }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">{!! __('client.help_center') !!}</h2>
                    <ul class="text-gray-600 dark:text-gray-400">
                        @foreach(Page::getActive() as $page)
                            @if(in_array('footer_help_center', $page->placement))
                                <li class="mb-4">
                                    <a href="{{ route('page', $page->path) }}" @if($page->new_tab) target="_blank"
                                       @endif class="hover:underline">{{ $page->name }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">{!! __('client.legal') !!}</h2>
                    <ul class="text-gray-600 dark:text-gray-400">
                        @foreach(Page::getActive() as $page)
                            @if(in_array('footer_legal', $page->placement))
                                <li class="mb-4">
                                    <a href="{{ route('page', $page->path) }}" @if($page->new_tab) target="_blank"
                                       @endif class="hover:underline">{{ $page->name }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <hr class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8"/>
        <div class="flex mt-4 space-x-6 sm:justify-center sm:mt-0">
            @if(settings('socials::discord'))
                <a href="@settings('socials::discord')" target="_blank"
                   class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                    <i class='bx bxl-discord-alt' style="font-size: 1.25rem"></i>
                </a>
            @endif
            @if(settings('socials::twitter'))
                <a href="@settings('socials::twitter')" target="_blank"
                   class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                    </svg>
                </a>
            @endif
            @if(settings('socials::github'))
                <a href="@settings('socials::github')" target="_blank"
                   class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/>
                    </svg>
                </a>
            @endif
        </div>
    </div>
</footer>
@if (settings('portal::snow', false))
    </div>
</div>
@endif
</body>
</html>
