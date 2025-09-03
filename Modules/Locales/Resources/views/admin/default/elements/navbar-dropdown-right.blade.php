@php
    $manager = new \Modules\Locales\Models\Manager();
@endphp
<li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown"
        class="nav-link notification-toggle nav-link-lg d-flex align-items-center">
        <img alt="{{$manager->getCountryCode(app()->getLocale())}}"
             class="mr-2"
             src="/assets/core/img/flags/{{ strtolower($manager->getCountryCode(app()->getLocale())) }}.svg"
             style="width: 18px; border-radius: 2px;">
        {{ $manager->getCountryName(app()->getLocale()) }}
    </a>
    <div class="dropdown-menu dropdown-list dropdown-menu-right">
        <div class="dropdown-header">{{ __('locales::general.language') }}
        </div>
        <div class="dropdown-list-content dropdown-list-icons">
            @foreach ($manager->getInstalled() as $key => $lang)
                <a href="{{ route('toggle.lang', ['lang' => $key]) }}" class="dropdown-item dropdown-item-unread">
                    <img alt="{{$manager->getCountryCode($key)}}" class="mr-2"
                         src="/assets/core/img/flags/{{ strtolower($manager->getCountryCode($key)) }}.svg"
                         style="width: 18px; border-radius: 2px;">
                    {{ $manager->getCountryName($key) }}
                </a>
            @endforeach
        </div>
    </div>
</li>
