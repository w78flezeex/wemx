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
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    @include(Portal::path('layouts.tailwind'))
</head>
<body>
@include(Portal::path('layouts.header'))


<section id="home" class="bg-black relative overflow-hidden">
    <!-- Twinkling stars animation -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none z-0">
        <div class="absolute top-0 left-0 w-full h-full animate-twinkle">
            <!-- Generate a large number of stars -->
            @for ($i = 0; $i < 300; $i++)
                <div class="w-1 h-1 bg-white rounded-full animate-star" style="top: {{ rand(0, 100) }}%; left: {{ rand(0, 100) }}%; animation-delay: {{ rand(0, 60) }}s;"></div>
            @endfor
        </div>
    </div>

    <div class="grid py-8 px-4 mx-auto max-w-screen-xl lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
        <div class="place-self-center mr-auto lg:col-span-7 futuristic-text relative z-10">
            <h1 class="mb-4 max-w-2xl text-4xl font-extrabold leading-none md:text-5xl xl:text-6xl text-white futuristic-heading animate__animated animate__fadeInDown">
                @settings('portal::title', '🌌 Server Hosting')
            </h1>
            <p class="mb-6 max-w-2xl font-light text-gray-500 lg:mb-8 md:text-lg lg:text-xl text-white futuristic-text animate__animated animate__fadeInLeft">
                @settings('portal::description', 'Embark on an interstellar journey with Shadow Hosting! Launch your servers into the cosmos today for as low as $1/GB. Our cutting-edge technology ensures your online ventures run seamlessly 24/7 across the universe.')
            </p>
            <div class="flex items-center space-x-4 mb-8">
                <a href="#pricing"
                   class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-white rounded-lg bg-gradient-to-r from-primary-700 to-primary-900 hover:bg-gradient-to-r from-primary-800 to-primary-1000 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900 futuristic-button animate__animated animate__fadeInUp floating-button">
                    @settings('portal::button',  __('🚀 Explore Galactic Plans') )
                    <svg class="ml-2 -mr-1 w-5 h-5 rocket" fill="currentColor" viewBox="0 0 20 20"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                              clip-rule="evenodd"></path>
                    </svg>
                </a>
                <a href="@settings('socials::discord')"
                   class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-gray-900 rounded-lg border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:text-white dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-800 futuristic-button animate__animated animate__fadeInUp floating-button">
                    {!! __('🌠 Join the Intergalactic Discord') !!}
                </a>
            </div>
        </div>
        <div id="rocket" class="place-self-center lg:col-span-5 lg:flex futuristic-image animate__animated animate__fadeInRight z-10 relative">
            <span class="text-white text-6xl rocket">🚀</span>
        </div>
    </div>

    <style>
        /* Define animation for twinkling stars */
        @keyframes twinkle {
            0% {
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }

        /* Apply animation to twinkling stars */
        .animate-twinkle div {
            position: absolute;
            animation: twinkle 3s infinite;
        }

        /* Define animation for rocket movement */
        @keyframes moveRocket {
            0% {
                transform: translate(-50%, -50%);
            }
            100% {
                transform: translate(-50%, -50%) translateX(calc(-50% + var(--translate-x))) translateY(calc(-50% + var(--translate-y)));
            }
        }
    </style>

    <script>
        // Function to generate random number between min and max
        function random(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }

        // Get the rocket element
        const rocket = document.getElementById('rocket');

        // Function to animate the rocket
        function animateRocket() {
            // Generate random coordinates
            const x = random(0, 100);
            const y = random(0, 100);

            // Set the animation duration based on distance
            const distance = Math.sqrt(Math.pow(x - 50, 2) + Math.pow(y - 50, 2));
            const duration = Math.max(distance * 50, 3000); // Minimum duration 3s

            // Apply animation properties
            rocket.style.setProperty('--translate-x', `${x}%`);
            rocket.style.setProperty('--translate-y', `${y}%`);
            rocket.style.animationDuration = `${duration}ms`;
        }

        // Initial animation
        animateRocket();

        // Function to restart animation
        function restartAnimation() {
            animateRocket();
            setTimeout(restartAnimation, random(5000, 10000)); // Random interval between 5s and 10s
        }

        // Start animation
        restartAnimation();
    </script>
</section>





<style>
    @keyframes floatAnimation {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-8px);
        }
    }

    @keyframes pulseAnimation {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    .floating-icon {
        animation: floatAnimation 3s infinite ease-in-out;
    }

    .pulsating-title {
        animation: pulseAnimation 2s infinite alternate ease-in-out;
    }
    
    @keyframes twinkle {
        0% {
            opacity: 0;
        }
        50% {
            opacity: 1;
        }
        100% {
            opacity: 0;
        }
    }

    .animate-twinkle div {
        position: absolute;
        animation: twinkle 3s infinite;
    }
</style>

<section class="bg-black dark:bg-black relative overflow-hidden">
    <!-- Twinkling stars animation -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none z-0">
        <div class="absolute top-0 left-0 w-full h-full animate-twinkle">
            <!-- Generate a large number of stars -->
            @for ($i = 0; $i < 300; $i++)
                <div class="w-1 h-1 bg-white rounded-full animate-star" style="top: {{ rand(0, 100) }}%; left: {{ rand(0, 100) }}%; animation-delay: {{ rand(0, 60) }}s;"></div>
            @endfor
        </div>
    </div>

    <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 rotating-grid">
        <h2 class="mb-8 text-4xl font-extrabold tracking-tight leading-tight text-center text-gray-900 dark:text-white md:text-5xl animate-bounce-in-space">{!! __('client.host_favorite_games') !!}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-8 text-gray-500 sm:gap-12 dark:text-gray-400">
            <a href="#" class="flex flex-col items-center justify-center p-6 bg-black dark:bg-black rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300 shadow-lg animate-bounce-in-space">
                <img src="https://licenses.wemx.net/img/services/rust.png" class="h-16 w-16 mb-4 floating-image"/>
                <h2 class="text-xl font-extrabold tracking-tight leading-tight text-center text-gray-900 dark:text-white">{!! __('client.rust') !!}</h2>
            </a>

            <a href="#" class="flex flex-col items-center justify-center p-6 bg-black dark:bg-black rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300 shadow-lg animate-bounce-in-space">
                <img src="https://licenses.wemx.net/img/services/minecraft.png" class="h-16 w-16 mb-4 floating-image"/>
                <h2 class="text-xl font-extrabold tracking-tight leading-tight text-center text-gray-900 dark:text-white">{!! __('client.minecraft') !!}</h2>
            </a>

            <a href="#" class="flex flex-col items-center justify-center p-6 bg-black dark:bg-black rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300 shadow-lg animate-bounce-in-space">
                <img src="https://licenses.wemx.net/img/services/ark.png" class="h-16 w-16 mb-4 floating-image"/>
                <h2 class="text-xl font-extrabold tracking-tight leading-tight text-center text-gray-900 dark:text-white">{!! __('client.ack') !!}</h2>
            </a>
        </div>
    </div>

    <style>
        /* Define animation for bouncing in space */
        @keyframes bounce-in-space {
            0% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
            100% {
                transform: translateY(0);
            }
        }

        /* Define animation for twinkling stars */
        @keyframes twinkle {
            0% {
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }

        /* Apply animation to twinkling stars */
        .animate-twinkle div {
            position: absolute;
            animation: twinkle 3s infinite;
        }
    </style>
</section>






<section id="features" class="bg-black relative overflow-hidden">
    <!-- Twinkling stars animation -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none z-0">
        <div class="absolute top-0 left-0 w-full h-full animate-twinkle">
            <!-- Generate a large number of stars -->
            @for ($i = 0; $i < 300; $i++)
                <div class="w-1 h-1 bg-white rounded-full animate-star" style="top: {{ rand(0, 100) }}%; left: {{ rand(0, 100) }}%; animation-delay: {{ rand(0, 60) }}s;"></div>
            @endfor
        </div>
    </div>

    <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6 z-10 relative">
        <div class="mb-8 max-w-screen-md lg:mb-16">
            <h2 class="mb-4 text-4xl font-extrabold text-gray-900 dark:text-white">{!! __('client.our_game_anel') !!}</h2>
            <p class="text-gray-500 sm:text-xl dark:text-gray-400">
                {!! __('client.our_game_anel_desc', ['app' => settings('app_name', 'WemX')]) !!}
            </p>
        </div>
        <div class="space-y-8 md:grid md:grid-cols-2 lg:grid-cols-3 md:gap-12 md:space-y-0">
            <div>
                <div
                    class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-primary-100 lg:h-12 lg:w-12 dark:bg-primary-900 floating-icon">
                    <div class="text-primary-600 dark:text-primary-300 text-2xl">
                        <i class="bx bxs-file"></i>
                    </div>
                </div>
                <h3 class="mb-2 text-xl font-bold dark:text-white">{!! __('client.file_manager') !!}</h3>
                <p class="text-gray-500 dark:text-gray-400">
                    {!! __('client.file_manager_desc') !!}
                </p>
            </div>

            <div>
                <div
                    class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-primary-100 lg:h-12 lg:w-12 dark:bg-primary-900 floating-icon">
                    <div class="text-primary-600 dark:text-primary-300 text-2xl">
                        <i class="bx bxs-plug"></i>
                    </div>
                </div>
                <h3 class="mb-2 text-xl font-bold dark:text-white">{!! __('client.plugins_manager') !!}</h3>
                <p class="text-gray-500 dark:text-gray-400">
                    {!! __('client.plugins_manager_desc') !!}
                </p>
            </div>

            <div>
                <div
                    class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-primary-100 lg:h-12 lg:w-12 dark:bg-primary-900 floating-icon">
                    <div class="text-primary-600 dark:text-primary-300 text-2xl">
                        <i class="bx bx-revision"></i>
                    </div>
                </div>
                <h3 class="mb-2 text-xl font-bold dark:text-white">{!! __('client.backup_manager') !!}</h3>
                <p class="text-gray-500 dark:text-gray-400">
                    {!! __('client.backup_manager_desc') !!}
                </p>
            </div>

            <div>
                <div
                    class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-primary-100 lg:h-12 lg:w-12 dark:bg-primary-900 floating-icon">
                    <div class="text-primary-600 dark:text-primary-300 text-2xl">
                        <i class="bx bxs-user"></i>
                    </div>
                </div>
                <h3 class="mb-2 text-xl font-bold dark:text-white">{!! __('client.user_manager') !!}</h3>
                <p class="text-gray-500 dark:text-gray-400">
                    {!! __('client.user_manager_desc') !!}
                </p>
            </div>

            <div>
                <div
                    class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-primary-100 lg:h-12 lg:w-12 dark:bg-primary-900 floating-icon">
                    <div class="text-primary-600 dark:text-primary-300 text-2xl">
                        <i class="bx bxs-time-five"></i>
                    </div>
                </div>
                <h3 class="mb-2 text-xl font-bold dark:text-white">{!! __('client.schedule_manager') !!}</h3>
                <p class="text-gray-500 dark:text-gray-400">
                    {!! __('client.schedule_manager_desc') !!}
                </p>
            </div>

            <div>
                <div
                    class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-primary-100 lg:h-12 lg:w-12 dark:bg-primary-900 floating-icon">
                    <div class="text-primary-600 dark:text-primary-300 text-2xl">
                        <i class="bx bxs-data"></i>
                    </div>
                </div>
                <h3 class="mb-2 text-xl font-bold dark:text-white">{!! __('client.database_manager') !!}</h3>
                <p class="text-gray-500 dark:text-gray-400">
                    {!! __('client.database_manager_desc') !!}
                </p>
            </div>
        </div>
    </div>

    <style>
        /* Define animation for floating icons */
        @keyframes floatAnimation {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        /* Apply animation to floating icons */
        .floating-icon {
            animation: floatAnimation 3s infinite ease-in-out;
        }
    </style>
</section>


<style>
    @keyframes twinkle {
        0% {
            opacity: 0;
        }
        50% {
            opacity: 1;
        }
        100% {
            opacity: 0;
        }
    }

    .animate-twinkle div {
        position: absolute;
        animation: twinkle 3s infinite;
    }
</style>

<section id="pricing" class="bg-black relative overflow-hidden">
    <!-- Twinkling stars animation -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none z-0">
        <div class="absolute top-0 left-0 w-full h-full animate-twinkle">
            <!-- Generate a large number of stars -->
            @for ($i = 0; $i < 300; $i++)
                <div class="w-1 h-1 bg-white rounded-full animate-star" style="top: {{ rand(0, 100) }}%; left: {{ rand(0, 100) }}%; animation-delay: {{ rand(0, 60) }}s;"></div>
            @endfor
        </div>
    </div>

    <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6 z-10 relative">
        @if($selected_category !== NULL)
            <div class="mx-auto max-w-screen-md text-center mb-8 lg:mb-12">
                <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">{!! __('client.viewing_plans_for') !!} {{ $selected_category->name }}</h2>
                <p class="mb-5 font-light text-gray-500 sm:text-xl dark:text-gray-400">{{ $selected_category->description }}</p>
            </div>
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
                    <div
                        class="flex flex-col p-6 mx-auto max-w-lg text-center text-gray-900 bg-black rounded-lg border border-black shadow dark:border-black xl:p-8 dark:bg-black dark:text-white">
                        <h3 class="mb-4 text-2xl font-semibold">{{ $package->name }}</h3>
                        <p class="font-light text-gray-500 sm:text-lg dark:text-gray-400">
                            {!! __('client.price_block_desc', [
                                'period' => mb_strtolower($package->prices->first()->period()),
                                'total_price' => $package->prices->first()->totalPrice(),
                                'renewal_price' => $package->prices->first()->renewal_price,
                                'per_period' => mb_strtolower($package->prices->first()->periodToHuman()),
                                'symbol' => currency('symbol')
                             ]) !!}
                        </p>
                        <div class="flex justify-center items-baseline my-8">
                                <span
                                    class="mr-2 text-5xl font-extrabold">{{ currency('symbol') }}{{ $package->prices->first()->renewal_price }}</span>
                            <span
                                class="text-gray-500 dark:text-gray-400">/{{ $package->prices->first()->periodToHuman() }}</span>
                        </div>
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
                        <a href="{{ route('store.package', ['package' => $package->id]) }}"
                           class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:text-white  dark:focus:ring-primary-900">
                            {!! __('client.get_started') !!}</a>
                    </div>
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
</section>

<style>
    @keyframes twinkle {
        0% {
            opacity: 0;
        }
        50% {
            opacity: 1;
        }
        100% {
            opacity: 0;
        }
    }

    .animate-twinkle div {
        position: absolute;
        animation: twinkle 3s infinite;
    }

    /* Define animation for stars */
    @keyframes floatAnimation {
        0%, 100% {
            transform: translateY(0);
            opacity: 0;
        }
        50% {
            transform: translateY(-8px);
            opacity: 1;
        }
    }

    /* Apply animation to stars */
    .animate-star {
        animation: floatAnimation 3s infinite ease-in-out;
    }
</style>




<section aria-label="Related articles" class="py-8 lg:py-15 bg-black dark:bg-black relative overflow-hidden">
    <!-- Twinkling stars animation -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none z-0">
        <div class="absolute top-0 left-0 w-full h-full animate-twinkle">
            <!-- Generate a large number of stars -->
            @for ($i = 0; $i < 300; $i++)
                <div class="w-1 h-1 bg-white rounded-full animate-star" style="top: {{ rand(0, 100) }}%; left: {{ rand(0, 100) }}%; animation-delay: {{ rand(0, 60) }}s;"></div>
            @endfor
        </div>
    </div>

    <div class="px-4 mx-auto max-w-screen-xl z-10 relative">
        <h2 class="mb-8 text-2xl font-bold text-gray-900 dark:text-white">{!! __('client.other_categories') !!}</h2>
        <div class="grid gap-12 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Category articles -->
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
                <article class="max-w-xs hover:shadow-lg transition-shadow duration-300">
                    <div class="image-container">
                        <a href="{{ url()->current() }}?category={{ $category->link }}#pricing">
                            <img src="{{ $category->icon() }}" class="mb-5 rounded-lg" alt="{{ $category->name }}" style="height: 200px;">
                        </a>
                    </div>
                    <h2 class="mb-2 text-xl font-bold leading-tight text-gray-900 dark:text-white">
                        <a href="#">{{ $category->name }}</a>
                    </h2>
                    <p class="mb-4 font-light text-gray-500 dark:text-gray-400">{{ $category->description }}</p>
                    <a href="{{ url()->current() }}?category={{ $category->link }}#pricing"
                        class="py-2.5 px-5 mb-2 min-w-full text-center text-sm font-medium text-white focus:outline-none bg-gradient-to-r from-primary-700 to-primary-900 rounded-lg hover:from-primary-800 hover:to-primary-1000 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                        {!! __('client.pricing') !!}
                    </a>
                </article>
            @endforeach
        </div>
    </div>

    <style>
        /* Define animation for twinkling stars */
        @keyframes twinkle {
            0% {
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }

        /* Apply animation to twinkling stars */
        .animate-twinkle div {
            position: absolute;
            animation: twinkle 3s infinite;
        }
    </style>
</section>

<section class="bg-black dark:bg-black relative overflow-hidden">
    <!-- Twinkling stars animation -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none z-0">
        <div class="absolute top-0 left-0 w-full h-full animate-twinkle">
            <!-- Generate a large number of stars -->
            @for ($i = 0; $i < 300; $i++)
                <div class="w-1 h-1 bg-white rounded-full animate-star" style="top: {{ rand(0, 100) }}%; left: {{ rand(0, 100) }}%; animation-delay: {{ rand(0, 60) }}s;"></div>
            @endfor
        </div>
    </div>

    <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6 z-10 relative">
        <div class="mx-auto max-w-screen-sm text-center">
            <h2 class="mb-4 text-4xl font-extrabold leading-tight text-gray-900 dark:text-white">{!! __('client.get_started_now') !!}</h2>
            <p class="mb-6 font-light text-gray-500 dark:text-gray-400 md:text-lg">
                {!! __('client.register_your_account_no-time') !!}
            </p>
            <a href="{{ route('register') }}"
               class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                {!! __('client.create_my_account') !!}
            </a>
        </div>
    </div>

    <style>
        /* Define animation for twinkling stars */
        @keyframes twinkle {
            0% {
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }

        /* Apply animation to twinkling stars */
        .animate-twinkle div {
            position: absolute;
            animation: twinkle 3s infinite;
        }
    </style>
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
                        <path fill-rule="evenodd"
                              d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
                              clip-rule="evenodd"/>
                    </svg>
                </a>
            @endif
        </div>
    </div>
</footer>
</body>
</html>
