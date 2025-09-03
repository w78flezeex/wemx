@props(['pagination'])

<div class="mt-6 flex justify-center">
    @if ($pagination['total_pages'] > 1)
        <nav>
            <ul class="pagination flex">
                {{-- Кнопка "Попередня сторінка" --}}
                @if ($pagination['current_page'] > 1)
                    <li class="page-item">
                        <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] - 1]) }}"
                           class="page-link px-3 py-1 bg-gray-200 text-gray-800 dark:bg-gray-800 dark:text-white rounded-md hover:bg-gray-300 dark:hover:bg-gray-700">
                            &laquo; {!! __('pagination.previous') !!}
                        </a>
                    </li>
                @endif

                {{-- Нумерація сторінок --}}
                @php
                    $startPage = max(1, $pagination['current_page'] - 5);
                    $endPage = min($pagination['total_pages'], $pagination['current_page'] + 4);
                    if ($pagination['current_page'] <= 5) {
                        $endPage = min(10, $pagination['total_pages']);
                    }
                    if ($pagination['current_page'] >= $pagination['total_pages'] - 4) {
                        $startPage = max($pagination['total_pages'] - 9, 1);
                    }
                @endphp

                @for ($i = $startPage; $i <= $endPage; $i++)
                    <li class="page-item {{ $pagination['current_page'] == $i ? 'bg-blue-500 text-white' : '' }}">
                        <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                           class="page-link px-3 py-1 {{ $pagination['current_page'] == $i ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800 dark:bg-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-700' }} rounded-md">
                            {{ $i }}
                        </a>
                    </li>
                @endfor

                {{-- Кнопка "Наступна сторінка" --}}
                @if ($pagination['current_page'] < $pagination['total_pages'])
                    <li class="page-item">
                        <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) }}"
                           class="page-link px-3 py-1 bg-gray-200 text-gray-800 dark:bg-gray-800 dark:text-white rounded-md hover:bg-gray-300 dark:hover:bg-gray-700">
                            {!! __('pagination.next') !!} &raquo;
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    @endif
</div>
