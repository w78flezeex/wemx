<form action="{{ route('admin.settings.store') }}" method="POST">
    @csrf

    <h2 class="font-extrabold">Setting</h2>
    <div class="row">

        <div class="form-group col-6">
            <label for="language">{!! __('client.portal_redirect') !!}</label>
            <select class="form-control select2 select2-hidden-accessible" name="portal::is_redirect" tabindex="-1" aria-hidden="true">
                <option value="0" @if(settings('portal::is_redirect', false)) selected @endif>{{ __('client.false') }}</option>
                <option value="1" @if(settings('portal::is_redirect', false)) selected @endif>{{ __('client.true') }}</option>
            </select>
        </div>

        <div class="form-group col-6">
            <label>{!! __('client.portal_redirect_url') !!}</label>
            <input type="text" name="portal::redirect_url" value="@settings('portal::redirect_url', '/dashboard')" class="form-control">
        </div>

        <div class="form-group col-6">
            <label for="category">{!! __('client.default_category') !!}</label>
            <select class="form-control select2 select2-hidden-accessible" name="portal::default_category" tabindex="-1" aria-hidden="true">
                @foreach(Categories::get() as $category)
                    <option value="{{ $category->link }}" @if(settings('portal::default_category') == $category->link) selected @endif>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-6">
            <label>Portal color</label>
            <input type="color" name="portal::color" value="@settings('portal::color', '#0099ff')" class="form-control">
        </div>

        <div class="form-group col-6">
            <label>Minimum stock</label>
            <input type="number" name="portal::min-stock" value="5"  class="form-control">
        </div>
    </div>

    <h2 class="font-extrabold">Events and Announcement</h2>
    <div class="row">
        <div class="form-group col-6">
            <label for="language">Snowing</label>
            <select class="form-control select2 select2-hidden-accessible" name="portal::snow" tabindex="-1" aria-hidden="true">
                <option value="0" @if(settings('portal::snow', false)) selected @endif>False</option>
                <option value="1" @if(settings('portal::snow', false)) selected @endif>True</option>
            </select>
        </div>
        <div class="form-group col-6">
            <label for="language">Announcement visibled?</label>
            <select class="form-control select2 select2-hidden-accessible" name="portal::announcement" tabindex="-1" aria-hidden="true">
                <option value="0" @if(settings('portal::announcement', false)) selected @endif>False</option>
                <option value="1" @if(settings('portal::announcement', false)) selected @endif>True</option>
            </select>
        </div>
        <div class="form-group col-6">
            <label for="language">Announcement type</label>
            <select class="form-control select2 select2-hidden-accessible" name="portal::announcement-type" tabindex="-1" aria-hidden="true">
                <option value="info" @if(settings('portal::announcement-type') == "info") selected @endif>Info</option>
                <option value="danger" @if(settings('portal::announcement-type') == "danger") selected @endif>Danger</option>
                <option value="success" @if(settings('portal::announcement-type') == "success") selected @endif>Success</option>
                <option value="warning" @if(settings('portal::announcement-type') == "warning") selected @endif>Warning</option>
            </select>
        </div>
        <div class="form-group col-6">
            <label>Announcement text</label>
            <input type="text" name="portal::announcement-text" value="@settings('portal::announcement-text', 'Change me')"  class="form-control">
        </div>
    </div>

    <h2 class="font-extrabold">Button Setting</h2>
    <div class="row">
        <div class="form-group col-6">
            <label>Button color</label>
            <input type="color" name="portal::button_color" value="@settings('portal::button_color', '#9f21c2')" class="form-control">
        </div>

        <div class="form-group col-6">
            <label>Button description color</label>
            <input type="color" name="portal::button_text_color" value="@settings('portal::button_text_color', '#ffffff')" class="form-control">
        </div>

        <div class="form-group col-6">
            <label>Button hover color</label>
            <input type="color" name="portal::button_hover_color" value="@settings('portal::button_hover_color', '#9f21c2')" class="form-control">
        </div>

    </div>

    <h2 class="font-extrabold">Header setting</h2>
    <div class="row">

        <div class="form-group col-6">
            <label>Header background</label>
            <input type="text" name="portal::header_background" value="{{ settings('portal::header_background', Theme::get('Default')->assets . '/assets/img/bg.svg') }}" class="form-control">
        </div>

        <div class="form-group col-6">
            <label>Header image</label>
            <input type="text" name="portal::default::header_image" value="@settings('portal::default::header_image', 'https://www.freepnglogos.com/uploads/minecraft-png/download-minecraft-characters-png-png-image-pngimg-29.png')" class="form-control">
        </div>

        <div class="form-group col-6">
            <label>Header title</label>
            <input type="text" name="portal::title" value="@settings('portal::title', 'Minecraft Server Hosting')" class="form-control">
        </div>

        <div class="form-group col-6">
            <label>Header title color</label>
            <input type="color" name="portal::title_color" value="@settings('portal::title_color', '#ffffff')" class="form-control">
        </div>

        <div class="form-group col-6">
            <label>Header description</label>
            <input type="text" name="portal::description" value="@settings('portal::description', 'Start your Minecraft server today for as low as $1/GB and be equiped for all situations with our high performance gear keeping your servers running 24/7')" class="form-control">
        </div>

        <div class="form-group col-6">
            <label>Header description color</label>
            <input type="color" name="portal::description_color" value="@settings('portal::description_color', '#ffffff')" class="form-control">
        </div>

        <div class="form-group col-6">
            <label>Button description</label>
            <input type="text" name="" value="@settings('portal::button',  __('client.explore_plans'))" class="form-control" disabled>
        </div>

    </div>

    <h2 class="font-extrabold">Resource</h2>
    <div class="row">
        <div class="form-group col-6">
            <label>Pricing background</label>
            <input type="text" name="portal::pricing_background" value="@settings('portal::pricing_background')" class="form-control">
        </div>

        <div class="form-group col-6">
            <label>Pricing background</label>
            <input type="text" name="portal::user_register_background" value="@settings('portal::user_register_background')" class="form-control">
        </div>
    </div>
    

    <h2>Pricing setting</h2>
    <div class="row">
        <div class="form-group col-6">
            <label for="language">Show customised badgets?</label>
            <select class="form-control select2 select2-hidden-accessible" name="portal::show_badge-off" tabindex="-1" aria-hidden="true">
                <option value="0" @if(settings('portal::show_badge-off', false)) selected @endif>False</option>
                <option value="1" @if(settings('portal::show_badge-off', false)) selected @endif>True</option>
            </select>
        </div>

        <div class="form-group col-6">
            <label for="language">Show stock badgets?</label>
            <select class="form-control select2 select2-hidden-accessible" name="portal::show_badge_stock" tabindex="-1" aria-hidden="true">
                <option value="0" @if(settings('portal::show_badge_stock', false)) selected @endif>False</option>
                <option value="1" @if(settings('portal::show_badge_stock', false)) selected @endif>True</option>
            </select>
        </div>

        <div class="form-group col-6">
            <label for="language">What mode do you want to show?</label>
            <select class="form-control select2 select2-hidden-accessible" name="portal::pricing_mode" tabindex="-1" aria-hidden="true">
                <option value="0" @if(settings('portal::pricing_mode', false)) selected @endif>Compact</option>
                <option value="1" @if(settings('portal::pricing_mode', false)) selected @endif>Not compact</option>
            </select>
        </div>

        <div class="form-group col-6">
            <label for="language">Position of the currency symbol</label>
            <select class="form-control select2 select2-hidden-accessible" name="portal::current_position" tabindex="-1" aria-hidden="true">
                <option value="left" @if(settings('portal::current_position') == "left") selected @endif>Left</option>
                <option value="right" @if(settings('portal::current_position') == "right") selected @endif>Right</option>
            </select>
        </div>

        <div class="form-group col-6">
            <label>If the package is empty, what message is shown?</label>
            <input type="text" name="portal::empity_package" value="@settings('portal::empity_package', 'Coming soon')" class="form-control">
        </div>
    </div>

    <button type="submit" class="btn btn-primary">{!! __('client.submit') !!}</button>
</form>
