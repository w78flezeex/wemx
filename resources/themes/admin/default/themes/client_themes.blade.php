@extends(AdminTheme::wrapper(), ['title' =>  __('admin.client_themes'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{!! __('admin.client_themes') !!}</h4>
                        <div class="card-header-action">
                            <form>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="{!! __('admin.search') !!}"/>
                                    <div class="input-group-btn">
                                        <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body ">
                        <div class="table-responsive">
                            <table class="table table-striped" id="sortable-table">
                                <thead>
                                <tr>
                                    <th class="text-center">
                                        <i class="fas fa-th"></i>
                                    </th>
                                    <th>{!! __('admin.theme') !!}</th>
                                    <th>{!! __('admin.authors', ['default' => 'Author(s)']) !!}</th>
                                    <th>{!! __('admin.version') !!}</th>
                                    <th>{!! __('admin.status') !!}</th>
                                    <th class="text-right">{!! __('admin.actions') !!}</th>
                                </tr>
                                </thead>
                                <tbody class="ui-sortable">
                                @foreach(Theme::list() as $theme)
                                    <tr class="" style="">
                                        <td>
                                            <div class="sort-handler ui-sortable-handle">
                                                <i class="fas fa-th"></i>
                                            </div>
                                        </td>
                                        <td>{{ $theme->name }}</td>
                                        <td>
                                            {{ $theme->author }}
                                        </td>
                                        <td>v{{ $theme->version }}</td>


                                        <td>
                                            <div
                                                class="badge @if($theme->name == Theme::active()->name) badge-success
                                                @else  badge-danger  @endif">@if($theme->name == Theme::active()->name)
                                                    {!! __('admin.active') !!}
                                                @else
                                                    {!! __('admin.inactive') !!}
                                                @endif</div>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('admin.theme.files', ['folder' => 'client/'. $theme->folder]) }}"
                                               class="btn btn-primary">{!! __('admin.files') !!}</a>
                                            <a href="{{ route('admin.theme.activate', ['theme' => $theme->name]) }}"
                                               class="btn @if($theme->name == Theme::active()->name) btn-primary disabled @else  btn-primary  @endif">{!! __('admin.activate') !!}</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @includeIf(AdminTheme::path('marketplace.resources-card'))
    </section>
@endsection
