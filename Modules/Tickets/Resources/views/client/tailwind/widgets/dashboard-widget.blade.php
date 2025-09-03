@php
$tickets = \Modules\Tickets\Entities\Ticket::where('user_id', auth()->user()->id);
@endphp

<section>
    <div>
        <!-- Start coding here -->
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden mb-4">
            <div class="flex flex-col items-center justify-between space-y-3 border-b p-4 dark:border-gray-700 md:flex-row md:space-x-4 md:space-y-0">
                <div class="flex w-full items-center space-x-3">
                    <h5 class="font-semibold dark:text-white">Ваши тикеты</h5>
                    <div class="font-medium text-gray-400">
                        {{ $tickets->count() }} результатов
                    </div>
                </div>
                <div class="flex w-full flex-row items-center justify-end space-x-3">
                    <a href="{{ route('tickets.create') }}" class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                        <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                        </svg>
                        Новый тикет
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">ID</th>
                            <th scope="col" class="px-4 py-3">Тема</th>
                            <th scope="col" class="px-4 py-3">Отдел</th>
                            <th scope="col" class="px-4 py-3">Заказ</th>
                            <th scope="col" class="px-4 py-3">Участники</th>
                            <th scope="col" class="px-4 py-3">Статус</th>
                            <th scope="col" class="px-4 py-3">Последнее обновление</th>
                            <th scope="col" class="px-4 py-3">Создано</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets->paginate(10) as $ticket)
                        <tr class="border-b dark:border-gray-700">
                            <th scope="px-4 py-3" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"><a class="hover:text-primary-400 dark:hover:text-primary-400 font-bold" href="{{ route('tickets.view', $ticket->id) }}">#{{ $ticket->id }}</a></th>
                            <th scope="px-4 py-3" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"><a class="hover:text-primary-400 dark:hover:text-primary-400 font-bold" href="{{ route('tickets.view', $ticket->id) }}">{{ $ticket->subject }}</a></th>
                            <td class="px-4 py-3">{{ $ticket->department->name ?? 'нет' }}</td>
                            <td class="px-4 py-3"><a @isset($ticket->order->id) href="{{ route('service', ['order' => $ticket->order->id, 'page' => 'manage']) }}" @else href="#" @endif class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">{{ $ticket->order->package->name ?? 'нет' }}</a></td>
                            <td class="px-4 py-3">
                                <div class="flex -space-x-4 rtl:space-x-reverse">
                                    @if($ticket->user->avatar)
                                    <img class="w-10 h-10 border-2 border-white rounded-full dark:border-gray-800" src="{{ $ticket->user->avatar() }}" alt="">
                                    @else
                                    <div class="relative inline-flex border border-gray-500 items-center justify-center mt-0.5 w-9 h-9 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                        <span class="font-medium text-gray-600 dark:text-gray-300">{{ substr($ticket->user->first_name, 0, 1) . substr($ticket->user->last_name, 0, 1) }}</span>
                                    </div>
                                    @endif
                                    @foreach($ticket->members()->get() as $member)
                                    @if($member->user->avatar ?? false)
                                    <img class="w-10 h-10 @if($loop->last) z-10 @endif  border-2 border-white rounded-full dark:border-gray-800" src="{{ $member->user->avatar() }}" alt="">
                                    @else
                                    <div class="relative inline-flex border border-gray-500 items-center justify-center mt-0.5 w-9 h-9 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                        <span class="font-medium text-gray-600 dark:text-gray-300">{{ substr($member->user->first_name, 0, 1) . substr($member->user->last_name, 0, 1) }}</span>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($ticket->is_open)
                                <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Открыта</span>
                                @else
                                <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">Закрыта</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $ticket->updated_at->diffForHumans() ?? 'Никогда' }}</td>
                            <td class="px-4 py-3">{{ $ticket->created_at->diffForHumans() ?? 'Никогда' }}</td>
                        </tr>
                        @endforeach
                        @foreach(\Modules\Tickets\Entities\TicketMember::where('user_id', auth()->user()->id)->get() as $ticket)
                        @php
                        $ticket = $ticket->ticket;
                        @endphp
                        <tr class="border-b dark:border-gray-700">
                            <th scope="px-4 py-3" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"><a class="hover:text-primary-400 dark:hover:text-primary-400 font-bold" href="{{ route('tickets.view', $ticket->id) }}">#{{ $ticket->id }}</a></th>
                            <th scope="px-4 py-3" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"><a class="hover:text-primary-400 dark:hover:text-primary-400 font-bold" href="{{ route('tickets.view', $ticket->id) }}">{{ $ticket->subject }}</a></th>
                            <td class="px-4 py-3">{{ $ticket->department->name ?? 'нет' }}</td>
                            <td class="px-4 py-3"><a @isset($ticket->order->id) href="{{ route('service', ['order' => $ticket->order->id, 'page' => 'manage']) }}" @else href="#" @endif class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">{{ $ticket->order->package->name ?? 'нет' }}</a></td>
                            <td class="px-4 py-3">
                                <div class="flex -space-x-4 rtl:space-x-reverse">
                                    @if($ticket->user->avatar)
                                    <img class="w-10 h-10 border-2 border-white rounded-full dark:border-gray-800" src="{{ $ticket->user->avatar() }}" alt="">
                                    @else
                                    <div class="relative inline-flex border border-gray-500 items-center justify-center mt-0.5 w-9 h-9 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                        <span class="font-medium text-gray-600 dark:text-gray-300">{{ substr($ticket->user->first_name, 0, 1) . substr($ticket->user->last_name, 0, 1) }}</span>
                                    </div>
                                    @endif
                                    @foreach($ticket->members()->get() as $member)
                                    @if($member->user->avatar ?? false)
                                    <img class="w-10 h-10 @if($loop->last) z-10 @endif  border-2 border-white rounded-full dark:border-gray-800" src="{{ $member->user->avatar() }}" alt="">
                                    @else
                                    <div class="relative inline-flex border border-gray-500 items-center justify-center mt-0.5 w-9 h-9 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                        <span class="font-medium text-gray-600 dark:text-gray-300">{{ substr($member->user->first_name, 0, 1) . substr($member->user->last_name, 0, 1) }}</span>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($ticket->is_open)
                                <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Открыта</span>
                                @else
                                <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">Закрыта</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $ticket->updated_at->diffForHumans() ?? 'Никогда' }}</td>
                            <td class="px-4 py-3">{{ $ticket->created_at->diffForHumans() ?? 'Никогда' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-2 mb-6 flex items-center justify-end">
                {{ $tickets->paginate(10)->links(Theme::pagination()) }}
            </div>
        </div>
        @if($tickets->count() == 0)
        <div class="mb-4">
            @include(Theme::path('empty-state'), ['title' => 'Тикетов не найдено', 'description' => 'Вы еще не создали ни одного тикета'])
        </div>
        @endif
    </div>
</section>
