@extends(AdminTheme::wrapper(), ['title' => 'RequireOauth'])

@section('container')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>RequireOauth</h4>
                    <div class="card-header-action">
                        <a href="{{ route('admin.oauth', 'RequireOauth') }}" class="btn btn-primary">Settings</a>
                    </div>
                </div>


                <form action="{{ route('requireoauth.store') }}" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="row d-flex justify-content-between">
                            @foreach($drivers as $driver)
                                <div class="form-group col text-center">
                                    <div class="control-label">Require {{ ucfirst($driver) }}</div>
                                    <label class="custom-switch mt-2">
                                        <input type="checkbox" name="oauth::{{ $driver }}" value="1"
                                               class="custom-switch-input"
                                               @if(Settings::getJson('oauth::'.$driver, 'require', false)) checked @endif>
                                        <span class="custom-switch-indicator"></span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <button class="btn btn-primary" type="submit">{{ __('admin.save_changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
