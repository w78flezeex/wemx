@extends(AdminTheme::wrapper(), ['title' => __('admin.payments'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('css_libraries')
    <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/select2/dist/css/select2.min.css')) }}">

@endsection

@section('js_libraries')
    <script src="{{ asset(AdminTheme::assets('modules/select2/dist/js/select2.full.min.js')) }}"></script>
@endsection

@section('container')
    <section class="section">
        <div class="section-body">
            <div class="col-12 mb-4">
                <div class="card mb-0">
                    <div class="card-body">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link @if($status == 'paid') active @endif"
                                   href="{{ route('payments.index', ['status' => 'paid']) }}">{!! __('admin.paid') !!}
                                    <span
                                        class="badge @if($status == 'paid') badge-white @else badge-primary @endif">{{ Payment::whereNot('type', 'subscription')->whereStatus('paid')->count() }}</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if($status == 'unpaid') active @endif"
                                   href="{{ route('payments.index', ['status' => 'unpaid']) }}">{!! __('admin.unpaid') !!}
                                    <span
                                        class="badge @if($status == 'unpaid') badge-white @else badge-primary @endif">{{ Payment::whereNot('type', 'subscription')->whereStatus('unpaid')->count() }}</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if($status == 'refunded') active @endif"
                                   href="{{ route('payments.index', ['status' => 'refunded']) }}">{!! __('admin.refunded') !!}
                                    <span
                                        class="badge @if($status == 'refunded') badge-white @else badge-primary @endif">{{ Payment::whereNot('type', 'subscription')->whereStatus('refunded')->count() }}</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-body p-0 mt-4">
                        <div class="mb-3 ml-4" style="display:flex; justify-content: space-between">
                            <div>
                                <button class="btn btn-icon icon-left btn-primary" data-toggle="modal" data-target="#PaymentFilterModal">
                                    <i class="fas fa-filter"></i> {!! __('admin.filter', ['default' => 'Filter']) !!}
                                </button>
                                <div class="dropdown d-inline">
                                    <button class="btn btn-primary btn-icon icon-left dropdown-toggle" type="button" id="sortUsersDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      <i class="fas fa-sort-alpha-up"></i> {!! __('admin.sort_by', ['default' => 'Sort By']) !!}
                                    </button>
                                    <div class="dropdown-menu" x-placement="bottom-start">
                                      <a class="dropdown-item" href="{{ route('payments.index', ['status' => $status, 'sort' => 'latest']) }}">{{ __('admin.latest') }}</a>
                                      <a class="dropdown-item" href="{{ route('payments.index', ['status' => $status, 'sort' => 'oldest']) }}">{{ __('admin.oldest') }}</a>
                                      <a class="dropdown-item" href="{{ route('payments.index', ['status' => $status, 'sort' => 'random']) }}">{{ __('admin.random') }}</a>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <a href="{{ route('payments.create') }}" class="btn btn-icon icon-left btn-primary mr-4"><i
                                    class="fas fa-solid fa-plus"></i> {!! __('admin.create') !!}
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-md">
                                <tbody>
                                <tr>
                                    <th>{!! __('admin.id') !!}</th>
                                    <th>{!! __('admin.user') !!}</th>
                                    <th>{!! __('admin.description') !!}</th>
                                    <th>{!! __('admin.amount') !!}</th>
                                    <th>{!! __('admin.type') !!}</th>
                                    <th>{!! __('admin.status') !!}</th>
                                    <th>{!! __('admin.create_at') !!}</th>
                                    <th class="text-right">{!! __('admin.actions') !!}</th>
                                </tr>

                                @foreach($payments as $payment)
                                    <tr>
                                        <td>{{ Str::substr($payment->id, 0, 8) }}</td>
                                        <td>
                                            <a href="{{ route('users.edit', ['user' => $payment->user->id]) }}"
                                               style="display: flex; color: #6c757d">
                                                <img alt="image" src="{{ $payment->user->avatar() }}"
                                                     class="rounded-circle mr-1 mt-1" width="32px" height="32px"
                                                     data-toggle="tooltip" title=""
                                                     data-original-title="{{ $payment->user->first_name }} {{ $payment->user->last_name }}">
                                                <div class="flex">
                                                    {{ $payment->user->username }} <br>
                                                    <small>{{ $payment->user->email }}</small>
                                                </div>
                                            </a>
                                        </td>

                                        <td>{{ $payment->description }}</td>

                                        <td>{{ price($payment->amount) }}</td>

                                        <td>{{ $payment->type }}</td>

                                        <td>
                                            <div class="@if($payment->status == 'paid') badge badge-success
                                            @elseif($payment->status == 'unpaid') badge badge-danger @endif">
                                                {!! __('admin.' . $payment->status) !!}
                                            </div>
                                        </td>

                                        <td>{{ $payment->created_at->translatedFormat(settings('date_format', 'd M Y')) }}</td>

                                        <td class="text-right">
                                            <button type="button" onclick="deletePayment('{{ $payment->id }}')" class="btn btn-danger"><i class="fas fa-solid fa-trash"></i></button>
                                            <a href="{{ route('payments.edit', ['payment' => $payment->id]) }}"
                                               class="btn btn-primary">{!! __('admin.manage') !!}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        {{ $payments->links(AdminTheme::pagination()) }}
                    </div>
                </div>
            </div>
        </div>
</section>

<div class="modal fade" tabindex="-1" role="dialog" id="PaymentFilterModal" aria-hidden="true" style="display: none;">
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <form action="">
    <div class="modal-header">
        <h5 class="modal-title">{!! __('admin.filter', ['default' => 'Filter']) !!}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('admin.close') }}">
        <span aria-hidden="true">×</span>
        </button>
    </div>
    <div class="modal-body">
        @csrf
        <div id="filters-container">
            @if(isset(request()->filter))
                @foreach(request()->filter as $key => $filter)
                <div class="row filter" id="filter">
                <div class="form-group col-4">
                    <label>{{ __('admin.key') }}</label>
                    <select class="form-control select2 select2-hidden-accessible" required="" name="filter[{{$key}}][key]" tabindex="-1" aria-hidden="true">
                    @foreach(Payment::$filters as $userFilter)
                        <option value="{{ $userFilter }}" @if($filter['key'] == $userFilter) selected @endif>{{ $userFilter }}</option>
                    @endforeach
                    </select>
                </div>
                <div class="form-group col-4">
                    <label>{{ __('admin.operator') }}</label>
                    <select class="form-control select2 select2-hidden-accessible" required="" name="filter[{{$key}}][operator]" tabindex="-1" aria-hidden="true">
                        <option value="=" @if($filter['operator'] == '=') selected @endif>Equals</option>
                        <option value="!=" @if($filter['operator'] == '!=') selected @endif>Does not Equal</option>
                        <option value="LIKE" @if($filter['operator'] == 'LIKE') selected @endif>Contains</option>
                        <option value="NOT LIKE" @if($filter['operator'] == 'NOT LIKE') selected @endif>Does not contain</option>
                        <option value=">" @if($filter['operator'] == '>') selected @endif>Greater Than</option>
                        <option value="<" @if($filter['operator'] == '<') selected @endif>Less Than</option>
                    </select>
                </div>
                <div class="form-group col-4">
                    <label>{{ __('admin.value') }}</label>
                    <input type="text" placeholder="Value" required="" value="{{ $filter['value'] }}" name="filter[{{$key}}][value]" class="form-control">
                </div>
                </div>
                @endforeach
            @else
            <div class="row filter" id="filter">
                <div class="form-group col-4">
                <label>{{ __('admin.key') }}</label>
                <select class="form-control select2 select2-hidden-accessible" required="" name="filter[0][key]" tabindex="-1" aria-hidden="true">
                    @foreach(Payment::$filters as $filter)
                    <option value="{{ $filter }}">{{ $filter }}</option>
                    @endforeach
                </select>
                </div>
                <div class="form-group col-4">
                <label>{{ __('admin.operator') }}</label>
                <select class="form-control select2 select2-hidden-accessible" required="" name="filter[0][operator]" tabindex="-1" aria-hidden="true">
                    <option value="=">{{ __('admin.equals') }}</option>
                    <option value="!=">{{ __('admin.not_equals') }}</option>
                    <option value="LIKE">{{ __('admin.contains') }}</option>
                    <option value="NOT LIKE">{{ __('admin.not_contains') }}</option>
                    <option value=">">{{ __('admin.greater_than') }}</option>
                    <option value="<">{{ __('admin.less_than') }}</option>
                </select>
                </div>
                <div class="form-group col-4">
                <label>{{ __('admin.value') }}</label>
                <input type="text" placeholder="Value" required="" name="filter[0][value]" class="form-control">
                </div>
            </div>
            @endif
        </div>
        <button type="button" id="remove-filter" class="btn btn-danger">{{ __('admin.remove_filter') }}</button>
        <button type="button" id="add-filter" class="btn btn-primary">{{ __('admin.add_filter') }}</button>

    </div>
    <div class="modal-footer bg-whitesmoke br">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{!! __('admin.close') !!}</button>
        <button type="submit" class="btn btn-primary">{!! __('admin.filter') !!}</button>
    </div>
    </form>
    </div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function deletePayment(payment) {
        if (window.confirm('Are you sure you want to delete this payment?')) {
            window.location.href = "/admin/payments/" + payment +"/delete";
        } else {
            event.preventDefault();
        }
    }
</script>

<script>
    $(document).ready(function() {
        $('#add-filter').click(function() {
            // Clone the first filter div
            var newFilter = $('.filter:first').clone();
            
            // Find the highest existing index to ensure uniqueness
            var highestIndex = -1;
            $('[name^="filter["]').each(function() {
                var name = $(this).attr('name');
                var result = name.match(/\[(\d+)\]/);
                if (result && parseInt(result[1]) > highestIndex) {
                    highestIndex = parseInt(result[1]);
                }
            });
            var newIndex = highestIndex + 1;
            
            // Update the 'name' attributes with the new index
            newFilter.find('[name]').each(function() {
                var name = $(this).attr('name').replace(/\[\d+\]/, '[' + newIndex + ']');
                $(this).attr('name', name);
            });
            
            // Append the new filter div to the container
            $('#filters-container').append(newFilter);
        });
    
        $('#remove-filter').click(function() {
            // Only remove the filter if there is more than one
            if ($('.filter').length > 1) {
                $('.filter:last').remove();
            }
        });
    });
</script>
@endsection
