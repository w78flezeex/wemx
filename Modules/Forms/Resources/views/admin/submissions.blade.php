@extends(AdminTheme::wrapper(), ['title' => 'Forms', 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <section class="section">
        <div class="section-body">
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Submissions</h4>
                    </div>

                    <div class="card-body p-0">
                        @if($submissions->isEmpty())
                            @include(AdminTheme::path('empty-state'), ['title' => 'No submissions found', 'description' => 'Could not locate any submissions.'])
                        @else 
                        <div class="table-responsive">
                            <table class="table table-striped table-md">
                                <tbody>
                                    <tr>
                                        <th class="text-center">{!! __('Form') !!}</th>
                                        <th class="text-center">{!! __('User') !!}</th>
                                        <th class="text-center">{!! __('Status') !!}</th>
                                        <th class="text-center">{!! __('Submitted at') !!}</th>
                                        <th class="text-center">{!! __('Actions') !!}</th>
                                    </tr>

                                    @foreach ($submissions as $submission)
                                        <tr>
                                            <td class="text-center">{{ $submission->form->title }}</td>
                                            <td class="text-center">
                                                @if($submission->user)
                                                    <a href="{{ route('users.edit', $submission->user->id) }}">{{ $submission->user->username }}</a>
                                                @elseif($submission->guest_email)
                                                    {{ $submission->guest_email }}
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                @if($submission->status == 'open')
                                                    <span class="badge badge-success">Open</span>
                                                @elseif($submission->status == 'closed')
                                                    <span class="badge badge-danger">Closed</span>
                                                @elseif($submission->status == 'awaiting_payment')
                                                    <span class="badge badge-danger">Awaiting Payment</span>
                                                @else
                                                    <span class="badge badge-info">{{ $submission->status }}</span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                {{ $submission->created_at->diffForHumans() }} 
                                            </td>

                                            <td class="text-center">
                                                <a href="{{ route('forms.view-submission', $submission->token) }}" target="_blank"
                                                    class="btn btn-primary mr-2" title="{!! __('Edit') !!}">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    <div class="card-footer text-right">
                        {{ $submissions->links(AdminTheme::pagination()) }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css_libraries')
    <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.css')) }}" />
    <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/select2/dist/css/select2.min.css')) }}">
@endsection

@section('js_libraries')
    <script src="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.js')) }}"></script>
    <script src="{{ asset(AdminTheme::assets('modules/select2/dist/js/select2.full.min.js')) }}"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
@endsection