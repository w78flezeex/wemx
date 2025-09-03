@extends(AdminTheme::wrapper(), ['title' => __('admin.settings'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('css_libraries')
<link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.css')) }}" />
<link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/select2/dist/css/select2.min.css')) }}">

@endsection

@section('js_libraries')
<script src="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.js')) }}"></script>
<script src="{{ asset(AdminTheme::assets('modules/select2/dist/js/select2.full.min.js')) }}"></script>
@endsection

@section('container')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form action="{{ route('admin.settings.store') }}" method="POST">
            <div class="card-header">
              <h4>Discord Donnect Settings</h4>
            </div>
            <div class="card-body">
                @csrf
              <div class="row">
                <div class="form-group col-12">
                  <div class="control-label">
                      Force users to connect their Discord account
                  </div>
                  <label class="custom-switch mt-2" onclick="location.href = ' /admin/settings/store?discordconnect:force_connect_all={{ settings('discordconnect:force_connect_all') ? 0 : 1 }} ';">
                      <input type="checkbox" name="discordconnect:force_connect_all" value="1" class="custom-switch-input" @if(settings('discordconnect:force_connect_all', 0)) checked="" @endif />
                      <span class="custom-switch-indicator"></span>
                      <span class="custom-switch-description">
                        Force users to connect their Discord account
                      </span>
                  </label>
                </div>

                <div class="form-group col-12">
                  <div class="control-label">
                      Force users to connect their Discord before placing an order
                  </div>
                  <label class="custom-switch mt-2" onclick="location.href = ' /admin/settings/store?discordconnect:force_connect_order={{ settings('discordconnect:force_connect_order') ? 0 : 1 }} ';">
                      <input type="checkbox" name="discordconnect:force_connect_order" value="1" class="custom-switch-input" @if(settings('discordconnect:force_connect_order', 0)) checked="" @endif />
                      <span class="custom-switch-indicator"></span>
                      <span class="custom-switch-description">
                        Force users to connect their Discord before placing an order
                      </span>
                  </label>
                </div>
              
                <div class="form-group col-12">
                    <label>Discord Server ID</label>
                    <input type="number" name="discord-connect::discord_server" value="@settings('discord-connect::discord_server', '')" class="form-control">
                    <small class="text-sm mt-2">The ID of the Discord server</small>
                </div>
                <div class="form-group col-12">
                    <label>Discord Bot Token</label>
                    <input type="text" name="encyroted::discord-connect::bot_token" value="" class="form-control">
                    <small class="text-sm mt-2">
                        For security reasons, the bot token is not shown after saving. If you need to change the token, please enter the new token and save the settings.
                    </small>
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <button type="submit" class="btn btn-primary">{!! __('admin.submit') !!}</button>
            </div>
          </div>
        </form>
    </div>
</div>
<style>
    span.select2.select2-container.select2-container--default {
        width: 100% !important;
    }
</style>
@endsection