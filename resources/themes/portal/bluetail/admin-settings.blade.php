<form action="{{ route('admin.settings.store') }}" method="POST">
    @csrf

    <div class="row">
        <div class="form-group col-6">
            <label for="language">{!! __('client.portal_redirect') !!}</label>
            <select class="form-control select2 select2-hidden-accessible" name="portal::is_redirect" tabindex="-1" aria-hidden="true">
                <option value="0" @if(settings('portal::is_redirect', false)) selected @endif>{{ __('client.false') }}</option>
                <option value="1" @if(settings('portal::is_redirect', false)) selected @endif>{{ __('client.true') }}</option>
            </select>
        </div>

        <div class="form-group col-6">
            <label>Title</label>
            <input type="text" name="portal::bluetail::title" value="@settings('portal::bluetail::title', 'Open-Source Web Template for SaaS, Startup, Apps, and More')" class="form-control">
        </div>

        <div class="form-group col-6">
            <label>Description</label>
            <input type="text" name="portal::bluetail::title" value="@settings('portal::bluetail::description', 'Multidisciplinary Web Template Built with Your Favourite Technology - HTML Bootstrap, Tailwind and React NextJS.')" class="form-control">
        </div>

        <div class="form-group col-6">
            <label>{!! __('client.portal_redirect_url') !!}</label>
            <input type="text" name="portal::redirect_url" value="@settings('portal::redirect_url', '/dashboard')" class="form-control">
        </div>

        <div class="form-group col-6">
            <label>Header Image</label>
            <input type="text" name="portal::bluetail::header_image" value="@settings('portal::bluetail::header_image', 'https://dev2.wemx.net/assets/themes/bluetail/images/hero/hero-image.jpg')" class="form-control">
        </div>

        <div class="form-group col-6">
            <label for="category">{!! __('client.default_category') !!}</label>
            <select class="form-control select2 select2-hidden-accessible" name="portal::default_category" tabindex="-1" aria-hidden="true">
                @foreach(Categories::get() as $category)
                    <option value="{{ $category->link }}" @if(settings('portal::default_category') == $category->link) selected @endif>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>


        <button type="submit" class="btn btn-primary">{!! __('client.submit') !!}</button>
</form>
