@extends(AdminTheme::wrapper(), ['title' => __('admin.groups', ['default' => 'Groups']), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{!! __('admin.create_group') !!}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('groups.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="name">{!! __('admin.name') !!}</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">{!! __('admin.create') !!}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
