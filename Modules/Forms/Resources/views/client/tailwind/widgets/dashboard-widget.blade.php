@php 
    use Modules\Forms\Entities\Submission;

    // get all submissions for the current user and that have can_view_submission set to true on the form relationship
    $submissions = Submission::where('user_id', auth()->id())->whereHas('form', function($query) {
        $query->where('can_view_submission', true);
    })->orderByDesc('updated_at');
@endphp

<section>
    <div>
        <!-- Start coding here -->
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden mb-4">
            <div class="flex flex-col items-center justify-between space-y-3 border-b p-4 dark:border-gray-700 md:flex-row md:space-x-4 md:space-y-0">
                <div class="flex w-full items-center space-x-3">
                    <h5 class="font-semibold dark:text-white">Your submissions</h5>
                    <div class="font-medium text-gray-400">
                        {{ $submissions->count() }} results
                    </div>
                </div>
                <div class="flex w-full flex-row items-center justify-end space-x-3">

                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">ID</th>
                            <th scope="col" class="px-4 py-3">Ticket</th>
                            <th scope="col" class="px-4 py-3">Price</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3">Last Updated</th>
                            <th scope="col" class="px-4 py-3">Created at</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions->paginate(10) as $submission)

                        <tr class="border-b dark:border-gray-700">
                            <th scope="px-4 py-3" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"><a class="hover:text-primary-400 dark:hover:text-primary-400 font-bold" href="{{ route('forms.view-submission', $submission->token) }}">#{{ $submission->id }}</a></th>
                            <th scope="px-4 py-3" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"><a class="hover:text-primary-400 dark:hover:text-primary-400 font-bold" href="{{ route('forms.view-submission', $submission->token) }}">{{ $submission->form->name }}</a></th>
                            <td class="px-4 py-3">@if($submission->form->isPaid()) {{ price($submission->form->price) }} @else Free @endif</td>
                            <td class="px-4 py-3">
                                @if($submission->status == 'open')
                                <span class="bg-green-100 text-green-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">{{ $submission->status }}</span>
                                @elseif($submission->status == 'closed')
                                    <span class="bg-red-100 text-red-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">{{ $submission->status }}</span>
                                @elseif($submission->status == 'awaiting_payment')
                                    <span class="bg-red-100 text-red-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Awaiting Payment</span>
                                @else
                                    <span class="bg-blue-100 text-blue-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $submission->status }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $submission->updated_at->diffForHumans() ?? 'Never' }}</td>
                            <td class="px-4 py-3">{{ $submission->created_at->diffForHumans() ?? 'Never' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-2 mb-6 flex items-center justify-end">
                {{ $submissions->paginate(10)->links(Theme::pagination()) }}
            </div>
        </div>
        @if($submissions->count() == 0)
            <div class="mb-4">
                @include(Theme::path('empty-state'), ['title' => 'No submissions found', 'description' => 'You haven\'t created any form submissions yet'])
            </div>
        @endif
    </div>
</section>