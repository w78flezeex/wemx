@extends(AdminTheme::wrapper(), ['title' => 'DiscordWebhooks', 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.settings.store') }}" method="POST">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Discord Webhooks</h4>
                        <div>
                            <a href="{{ route('admin.discord_webhooks.embed') }}" class="btn btn-primary mr-1">Embeds</a>
                            <a href="{{ route('admin.discord_webhooks.enable_all') }}" class="btn btn-success mr-1">Enable All</a>
                            <a href="{{ route('admin.discord_webhooks.disable_all') }}" class="btn btn-warning mr-1">Disable All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @csrf
                        <div class="form-group">
                            <label for="url">Webhook URL</label>
                            <input id="url" type="url" name="discordwebhook:webhook" value="@settings('discordwebhook:webhook')" required class="form-control">
                        </div>

                        @php
                            $sections = [
                                'Orders' => 'order',
                                'Payments' => 'payment',
                                'Users' => 'user',
//                                'External Accounts' => 'external',
                                'Oauth' => 'oauth',
                                'Punishment' => 'punishment',
                                'Modules and Services' => 'module_service',
                                'Other' => 'error'
                            ];
                        @endphp

                        @foreach($sections as $section => $filter)
                            <div class="border-top my-2"></div>
                            <div class="col-12 mb-2 text-center text-primary">
                                <h5>{{ $section }}</h5>

                            </div>
                            <div class="border-top my-2"></div>
                            <div class="row">
                                @foreach(discordWebhook()::getBuyFilter($filter) as $name => $e)
                                    @empty($e['description'])
                                        @continue
                                    @endempty
                                    <div class="form-group col-4">
                                        <div class="control-label">
                                            {{ str_replace('_', ' ', ucfirst($name)) }}
                                        </div>
                                        <label class="custom-switch mt-2"
                                               onclick="location.href = '@if(settings('discordwebhook:' . $name, false)) /admin/settings/store?discordwebhook:{{ $name }}=0 @else /admin/settings/store?discordwebhook:{{ $name }}=1 @endif';">
                                            <input type="checkbox" name="discordwebhook:{{ $name }}" value="1"
                                                   class="custom-switch-input"
                                                   @if(settings('discordwebhook:' . $name, false)) checked @endif>
                                            <span class="custom-switch-indicator"></span>
                                            <span class="custom-switch-description">{{ $e['description'] }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">{!! __('admin.submit') !!}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
