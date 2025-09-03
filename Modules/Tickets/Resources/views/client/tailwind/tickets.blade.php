@extends(Theme::wrapper())
@section('title', 'Tickets')

{{-- Keywords for search engines --}}
@section('keywords', 'WemX Dashboard, WemX Panel')

@section('container')
@includeIf(Theme::moduleView('tickets', 'widgets.dashboard-widget'))
@endsection