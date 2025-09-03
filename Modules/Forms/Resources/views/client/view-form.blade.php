@extends(Theme::wrapper(), ['title' => $form->title, 'keywords' => 'WemX Dashboard, WemX Panel'])
@section('title', $form->title)
@section('keywords', 'WemX Dashboard, WemX Panel')

@section('header')
    <link rel="stylesheet" href="{{ Theme::get('Default')->assets }}assets/css/typography.min.css">
    @turnstileScripts()
@endsection

@section('container')
<section>
    <div class="py-8 lg:py-16 px-4 mx-auto max-w-screen-md">
        <article class="format format-sm sm:format-base text-center lg:format-lg format-blue dark:format-invert mx-auto w-full max-w-2xl">
            <header class="not-format mb-4 lg:mb-6">
                <h1 class="mb-4 text-3xl text-center font-extrabold leading-tight text-gray-900 dark:text-white lg:mb-6 lg:text-4xl">
                    {{ $form->title }}
                </h1>
            </header>
            {!! $form->description !!}
        </article>

        <form action="{{ route('forms.submit', $form->slug) }}" method="POST">
            @csrf
            <div class="mb-6">
                @foreach($form->fields as $field)
                        <div class="mb-4">
                            <label for="{{ $field->name }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $field->label }}</label>
                            @if(in_array($field->type, ['text', 'email', 'number', 'date', 'password', 'url']))
                                <input type="{{ $field->type }}" name="{{ $field->name }}" @if($field->default_value) value="{{ $field->default_value }}" @endif id="{{ $field->name }}" @if(Str::contains($field->rules, 'required')) required="" @endif @if(Str::contains($field->label, 'mail') AND auth()->guest()) onchange="updateGuestMail(this.value)" @endif aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="{{ $field->placeholder ?? $field->label }}">
                            @elseif($field->type == 'textarea')
                                <textarea id="{{ $field->name }}" name="{{ $field->name }}" rows="4" @if(Str::contains($field->rules, 'required')) required="" @endif aria-describedby="helper-text-explanation" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg shadow-sm border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="{{ $field->placeholder ?? $field->label }}"></textarea>
                            @elseif($field->type == 'select')
                                <select id="{{ $field->name }}" name="{{ $field->name }}" @if(Str::contains($field->rules, 'required')) required="" @endif class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    @foreach($field->options as $option)
                                        <option value="{{ $option }}" @if($field->default_value == $option) selected @endif>{{ $option }}</option>
                                    @endforeach
                                </select>
                            @elseif($field->type == 'radio')
                                @foreach($field->options as $option)
                                <div class="flex items-center ps-4 border border-gray-200 rounded dark:border-gray-700 mb-3">
                                    <input id="{{ $field->name }}" @if($field->default_value == $option) checked="" @endif @if(Str::contains($field->rules, 'required')) required="" @endif type="radio" value="{{ $option }}" name="{{ $field->name }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="{{ $field->name }}" class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $option }}</label>
                                </div>
                                @endforeach
                            @endif
                            @if($field->description)
                                <p id="helper-text-explanation" class="mt-2 text-sm text-gray-500 dark:text-gray-400">{!! $field->description !!}</p>
                            @endif
                        </div>
                @endforeach

                @if (Settings::getJson('encrypted::captcha::cloudflare', 'page_login', false))
                <div class="mb-4">
                    <x-turnstile />
                </div>
                @endif

                @guest
                <div id="alert-border-5" class="flex items-center p-3 border-t-4 mb-4 rounded-lg border-gray-300 bg-gray-50 dark:bg-gray-800 dark:border-gray-600" role="alert">
                    <div class="ms-3 text-sm font-medium text-gray-800 dark:text-gray-300">
                        You are currently logged in as guest, please provide your email address to receive a copy of your submission.
                        <div class="relative mb-3 mt-2">
                          <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 16">
                                <path d="m10.036 8.278 9.258-7.79A1.979 1.979 0 0 0 18 0H2A1.987 1.987 0 0 0 .641.541l9.395 7.737Z"/>
                                <path d="M11.241 9.817c-.36.275-.801.425-1.255.427-.428 0-.845-.138-1.187-.395L0 2.6V14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2.5l-8.759 7.317Z"/>
                            </svg>
                          </div>
                          <input type="email" name="guest_email" required="" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="name@example.com">
                        </div>
                    </div>
                </div>
                @endguest
                
                @if($form->isPaid())
                <div class="space-y-4 rounded-lg border border-gray-100 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800">
                    <div class="space-y-2">
                        <dl class="flex items-center justify-between gap-4">
                            <dt class="text-base font-normal text-gray-500 dark:text-gray-400">{{ __('client.price') }}</dt>
                            <dd class="text-base font-medium text-gray-900 dark:text-white">{{ price($form->price) }}</dd>
                        </dl>
                    </div>
                
                    <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                        <dt class="text-base font-bold text-gray-900 dark:text-white">{{ __('client.total') }}</dt>
                        <dd class="text-base font-bold text-gray-900 dark:text-white">{{ price($form->price) }}</dd>
                    </dl>
                </div>
                @endif
                
                <div class="text-right">
                    <button type="submit" class="py-3 mt-4 px-5 text-sm font-medium text-center text-white rounded-lg bg-primary-700 sm:w-fit hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Submit</button>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    function updateGuestMail(email) {
        document.querySelector('input[name="guest_email"]').value = email;
    }
</script>
    
@endsection