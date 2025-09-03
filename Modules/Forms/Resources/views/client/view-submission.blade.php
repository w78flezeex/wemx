@extends(Theme::wrapper())
@section('title', $submission->form->title)

{{-- Keywords for search engines --}}
@section('keywords', 'WemX Dashboard, WemX Panel')

@section('header')
    <link rel="stylesheet" href="{{ Theme::get('Default')->assets }}assets/css/typography.min.css">
@endsection

@section('container')
<header class="mb-4 lg:mb-6 not-format">
   <h1 class="mb-4 text-2xl font-extrabold leading-tight text-gray-900 lg:mb-6 lg:text-4xl dark:text-white">{{ $submission->form->title }}</h1>
</header>

<div class="flex flex-wrap mt-6">
    <div class="w-3/4 md:w-3/4 pr-4 pl-4 sm:w-full pr-4 pl-4 mb-8">

        @if(!$submission->form->can_view_submission AND auth()->user()->isAdmin())
        <div class="flex items-center p-4 mb-4 text-sm text-gray-800 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-300" role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div>
              This submission is not visible to the user.
            </div>
        </div>
        @endif

        @if(!$submission->paid AND $submission->form->isPaid())
        <div class="flex items-center p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300" role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div>
                <p class="font-semibold">This submission is not paid yet.</p>
                <p class="text-sm">Please pay the required amount to proceed.</p>
            </div>
        </div>

        <div class="space-y-4 mb-4 rounded-lg border border-gray-100 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800">
            <div class="space-y-2">
                <dl class="flex items-center justify-between gap-4">
                    <dt class="text-base font-normal text-gray-500 dark:text-gray-400">{{ __('client.price') }}</dt>
                    <dd class="text-base font-medium text-gray-900 dark:text-white">{{ price($submission->form->price) }}</dd>
                </dl>
            </div>
            <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">

            <form action="{{ route('forms.submissions.pay', $submission->token) }}" class="mx-auto" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="gateway" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('client.gateway') }}</label>
                    <select id="gateway" name="gateway" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        @foreach(App\Models\Gateways\Gateway::get() as $gateway)
                            @if(!in_array($gateway->id, $submission->form->allowed_gateways ?? []))
                                @continue
                            @endif
                            <option value="{{ $gateway->id }}">{{ $gateway->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2 mb-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                        {{ __('client.pay') }}
                    </button>
                </div>
            </form>
  
        </div>
        @endif

        <div class="p-6 mb-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <dl class="text-gray-900 divide-y divide-gray-200 dark:text-white dark:divide-gray-700">
                @foreach($submission->data as $key => $value)
                <div class="flex flex-col pb-3 mb-3">
                    <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">{{ $key }}</dt>
                    <dd class="text-lg font-semibold">{{ $value }}</dd>
                </div>
                @endforeach
            </dl>
        </div>

        <ol class="relative border-s border-gray-200 dark:border-gray-700">
        @foreach($submission->messages()->oldest()->get() as $message)
        <li class="mb-10 ms-6">
            <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                @if($message->user)
                <img class="rounded-full shadow-lg" src="{{ $message->user->avatar() }}" alt="{{ $message->user->username }}"/>
                @else
                <div class="relative inline-flex border border-gray-500 items-center justify-center mt-0.5 w-6 h-6 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                    <span class="font-medium text-gray-600 dark:text-gray-300">{{ substr($submission->email(), 0, 2) }}</span>
                </div>
                @endif
            </span>
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600">
                <div class="items-center justify-between mb-3 sm:flex">
                    <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">{{ $message->created_at->diffForHumans() }}</time>
                    @if($message->user)
                    <div class="text-sm font-normal text-gray-800 lex dark:text-gray-300 capitalize">
                        <strong>{{ $message->user->username }}</strong> 
                        @if($message->user->is_admin()) 
                        <span class="bg-emerald-100 text-emerald-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-emerald-900 dark:text-emerald-300">Support Team</span>
                        @endif
                    </div>
                    @else
                    <div class="text-sm font-normal text-gray-800 lex dark:text-gray-300 capitalize">
                        <strong>{{ $message->guest_email }}</strong> 
                    </div>
                    @endif
                </div>
                <div class="">
                    <div class="format min-w-fit format-sm sm:format-base text-sm text-gray-700 dark:text-gray-300 lg:format-sm format-blue dark:format-invert">
                        {{ $message->message }}
                    </div>
                </div>
            </div>
        </li>
        @endforeach
        </ol>

        @if($submission->form->can_respond)
        <form id="comment-form" action="{{ route('forms.view-submission.post-message', $submission->token) }}" method="POST">
            @csrf
            <div class="sm:col-span-2 mb-6">
                <label for="message" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your message</label>
                <textarea required="" name="message" id="message" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write your thoughts here..."></textarea>                
            </div>
            <div class="text-right mb-4">
                <button type="submit" id="post_comment" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2 me-2 mb-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">Comment</button>
            </div>
        </form>
        @endif

    </div>
    <div class="w-1/3 md:w-1/4 pr-4 pl-4 sm:w-full pr-4 pl-4">

        <dl class="max-w-md text-gray-900 divide-y divide-gray-200 dark:text-white dark:divide-gray-700 mb-4">
            <div class="flex flex-col pb-3">
                <dt class="mb-1 text-gray-500 md:text-md dark:text-gray-400 mb-2">Members</dt>
                <dd class="text-lg font-semibold flex gap-1">
                    @if($submission->user)
                        <img class="w-10 h-10 border-2 border-white rounded-full dark:border-gray-800" src="{{ $submission->user->avatar() }}" alt="">
                    @else
                        <div class="relative inline-flex border border-gray-500 items-center justify-center mt-0.5 w-9 h-9 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                            <span class="font-medium text-gray-600 dark:text-gray-300">{{ substr($submission->email(), 0, 2) }}</span>
                        </div>
                    @endif
                </dd>
            </div>
            <div class="flex flex-col py-3">
                <dt class="mb-1 text-gray-500 md:text-md dark:text-gray-400">Status</dt>
                <dd class="text-lg font-semibold">
                    @if($submission->status == 'open')
                        <span class="bg-green-100 text-green-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">{{ $submission->status }}</span>
                    @elseif($submission->status == 'closed')
                        <span class="bg-red-100 text-red-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">{{ $submission->status }}</span>
                    @elseif($submission->status == 'awaiting_payment')
                        <span class="bg-red-100 text-red-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Awaiting Payment</span>
                    @else
                        <span class="bg-blue-100 text-blue-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $submission->status }}</span>
                    @endif
                </dd>
            </div>
            <div class="flex flex-col pt-3">
                <dt class="mb-1 text-gray-500 md:text-md dark:text-gray-400">Created</dt>
                <dd class="text-lg font-semibold">{{ $submission->created_at->format(settings('date_format', 'd M Y')) }}</dd>
            </div>
        </dl>

        <hr class="h-px my-4 bg-gray-200 border-0 dark:bg-gray-700">

        <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Actions</h2>
        <ul class="max-w-md space-y-2 text-gray-500 list-inside dark:text-gray-400">
            <li class="flex items-center mb-2 hover:underline hover:cursor-pointer" data-modal-target="static-modal" data-modal-toggle="static-modal">
                <span class="text-gray-500 dark:text-gray-400 flex-shrink-0 mr-1">
                    <i class='bx bxs-comment-edit'></i>
                </span>
                Update Status
           </li>
           @auth
            @if(auth()->user()->isAdmin())
            @if($submission->user)
            <li class="flex items-center mb-2">
                <a href="{{ route('users.edit', $submission->user->id) }}" target="_blank" class="hover:underline hover:cursor-pointer">
                    <span class="text-gray-500 dark:text-gray-400 flex-shrink-0">
                        <i class='bx bxs-user' ></i>
                    </span>
                    View Profile
                </a>
            </li>
            @endif
            <li class="flex items-center mb-2" onclick="deleteSubmission()">
                <a class="hover:underline hover:cursor-pointer">
                    <span class="text-gray-500 dark:text-gray-400 flex-shrink-0">
                        <i class='bx bxs-trash' ></i>
                    </span>
                    Delete Submission
                </a>
            </li>
            @endif
            @endauth
        </ul>

    </div>
</div>

<!-- Main modal -->
<div id="static-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Update Status
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="static-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form action="{{ route('forms.submissions.update', $submission->token) }}" method="POST">
                @csrf
            <div class="p-4 md:p-5 space-y-4">

            <div class="mx-auto">
                <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                <select id="status" name="status" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="open">Open</option>
                <option value="closed">Closed</option>
                <option value="completed">Completed</option>
                </select>
            </div>

            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email User (Optional)</label>
                <textarea id="email" name="email" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write your thoughts here..."></textarea>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Notify the user about the status update. Leave empty if you do not wish to email</p>

            </div>
  
            </div>
            <!-- Modal footer -->
            <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update</button>
                <button data-modal-hide="static-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Dismiss</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
    function deleteSubmission() {
        if(confirm('Are you sure you want to delete this submission?')) {
            window.location.href = "{{ route('forms.submissions.delete', $submission->token) }}";
        }
    }
</script>

@endsection