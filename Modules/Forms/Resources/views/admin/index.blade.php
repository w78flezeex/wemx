@extends(AdminTheme::wrapper(), ['title' => 'Forms', 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <section class="section">
        <div class="section-body">
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Forms</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.forms.create') }}" class="btn btn-icon icon-left btn-primary">
                                <i class="fas fa-pencil-alt"></i>
                                New Form
                            </a>
                        </div>

                    </div>


                    <div class="card-body p-0">
                        @if($forms->isEmpty())
                            @include(AdminTheme::path('empty-state'), ['title' => 'No forms found', 'description' => 'Create a new form by clicking the button above.'])
                        @else 
                        <div class="table-responsive">
                            <table class="table table-striped table-md">
                                <tbody>
                                    <tr>
                                        <th class="text-center">{!! __('Name') !!}</th>
                                        <th class="text-center">{!! __('URL') !!}</th>
                                        <th class="text-center">{!! __('Notification Email') !!}</th>
                                        <th class="text-center">{!! __('Price') !!}</th>
                                        <th class="text-center">{!! __('Allow Guest') !!}</th>
                                        <th class="text-center">{!! __('Can view own submission') !!}</th>
                                        <th class="text-center">{!! __('Actions') !!}</th>
                                    </tr>

                                    @foreach ($forms as $form)
                                        <tr>
                                            <td class="text-center">{{ $form->name }}</td>
                                            <td class="text-center"><a target="_blank" href="{{$form->url()}}">{{ $form->url() }}</a></td>
                                            <td class="text-center">{{ $form->notification_email }}</td>
                                            <td class="text-center">{{ price($form->price) }}</td>

                                            <td class="text-center">
                                                @if ($form->guest)
                                                    <span class="badge badge-success">Yes</span>
                                                @else
                                                    <span class="badge badge-danger">No</span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                @if ($form->can_view_submission)
                                                    <span class="badge badge-success">Yes</span>
                                                @else
                                                    <span class="badge badge-danger">No</span>
                                                @endif
                                            </td>


                                            <td class="text-center">
                                                <a target="_blank" href="{{ route('admin.forms.submissions.index', ['form_id' => $form->id]) }}"
                                                    class="btn btn-success mr-2" title="{!! __('External') !!}">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                                <a href="{{ route('admin.forms.edit', $form->id) }}"
                                                    class="btn btn-primary mr-2" title="{!! __('Edit') !!}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a onclick="return confirm('Deleting this form will also delete all submissions linked to the form! Consider deactivating the form instead.')" href="{{ route('admin.forms.destroy', $form->id) }}"
                                                    class="btn btn-danger mr-2" title="{!! __('Delete') !!}">
                                                    <i class="fas fa-trash-alt"></i>
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
                        {{ $forms->links(AdminTheme::pagination()) }}
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