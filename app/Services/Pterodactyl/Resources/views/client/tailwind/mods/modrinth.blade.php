@extends(Theme::path('orders.master'))
@section('title', 'Plugins | ' . $order->name)

@if(settings('encrypted::pterodactyl::api_admin_key', false))
    @section('content')
        <div class="container mx-auto px-4">
            @include(Theme::serviceView('pterodactyl', 'mods.modrinth.search'))
            @include(Theme::serviceView('pterodactyl', 'mods.modrinth.categories'))
            @include(Theme::serviceView('pterodactyl', 'mods.modrinth.mods'))
            @include(Theme::serviceView('pterodactyl', 'plugins.pagination'), ['pagination' => $pagination])
        </div>
    @endsection
@endif
