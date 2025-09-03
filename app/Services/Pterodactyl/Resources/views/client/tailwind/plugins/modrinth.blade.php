@extends(Theme::path('orders.master'))
@section('title', 'Plugins | ' . $order->name)

@if(settings('encrypted::pterodactyl::api_admin_key', false))
    @section('content')
        <div class="container mx-auto px-4">
            @include(Theme::serviceView('pterodactyl', 'plugins.modrinth.search'))
            @include(Theme::serviceView('pterodactyl', 'plugins.modrinth.categories'))
            @include(Theme::serviceView('pterodactyl', 'plugins.modrinth.plugins'))
            @include(Theme::serviceView('pterodactyl', 'plugins.pagination'), ['pagination' => $pagination])
        </div>
    @endsection
@endif
