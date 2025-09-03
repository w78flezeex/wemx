@extends(AdminTheme::wrapper(), ['title' => __('Forms'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('css_libraries')
    <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.css')) }}"/>
    <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/select2/dist/css/select2.min.css')) }}">
@endsection

@section('js_libraries')
    <script src="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.js')) }}"></script>
    <script src="{{ asset(AdminTheme::assets('modules/select2/dist/js/select2.full.min.js')) }}"></script>
@endsection

@section('container')
    <section class="section">
        <div class="section-body">
            <div class="col-12" id="alert{{$form->id}}" style="display: none;">
                <div class="alert alert-info alert-dismissible show fade">
                    <div class="alert-body">
                      <button class="close" onclick="hideInfoAlert('alert{{$form->id}}')" data-dismiss="alert">
                        <span class="text-white">×</span>
                      </button>
                      <strong>Tip: </strong> To add your form to the navigation, create a new <a target="_blank" href="{{ route('pages.create') }}">page</a> and make it redirect to your form and set the desired placement
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Forms</h4>
                        <div class="card-header-action">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createFieldModal"><i class="fas fa-solid fa-plus"></i>
                                New Field
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($form->fields->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-md">
                                <tbody>
                                    <tr>
                                        <th class="text-center">{!! __('Label') !!}</th>
                                        <th class="text-center">{!! __('Description') !!}</th>
                                        <th class="text-center">{!! __('Type') !!}</th>
                                        <th class="text-center">{!! __('Rules') !!}</th>
                                        <th class="text-center">{!! __('Actions') !!}</th>
                                    </tr>

                                    @foreach ($form->fields()->orderBy('order', 'desc')->get() as $field)
                                        <tr>
                                            <td class="text-center">{{ $field->label }}</td>
                                            <td class="text-center">{{ $field->description }}</td>
                                            <td class="text-center">{{ $field->type }}</td>
                                            <td class="text-center">{{ $field->rules }}</td>

                                            <td class="text-center">
                                                <a href="{{ route('admin.forms.fields.up', $field->id) }}" class="btn btn-primary"><i class="fas fa-solid fa-caret-up"></i></a>
                                                <a href="{{ route('admin.forms.fields.down', $field->id) }}" class="btn btn-primary"><i class="fas fa-solid fa-caret-down"></i></a>
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editFieldModal{{$field->id}}">
                                                    Edit
                                                </button>
                                                <a href="{{ route('admin.forms.fields.destroy', $field->id) }}" class="btn btn-danger"><i class="fas fa-trash-alt"></i></a>

                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                        @else 
                            @include(AdminTheme::path('empty-state'), ['title' => 'No Fields', 'description' => 'This form has no fields. Please add some fields to this form to continue.'])
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Edit Form') }}</h4>

                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.forms.update', $form->id) }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name" onchange="generateSlug()"
                                    value="{{ $form->name }}" required>
                                <small class="form-text text-muted">Name of the form (Not displayed to users)</small>
                            </div>

                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" name="title" id="title"
                                    value="{{ $form->title }}" required>
                                <small class="form-text text-muted">Title of the form (Displayed to users)</small>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="summernote form-control" name="description" id="description"style="display: none;">{!! $form->description !!}</textarea>
                                <small class="form-text text-muted">
                                    Description of the form (Displayed to users)
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="path">Slug</label>
                                <div class="input-group-prepend">
                                    <div>
                                        <div class="input-group-text">
                                            {{ url('/') }}/forms/
                                        </div>
                                    </div>
                                    <input type="text" name="slug" id="slug" placeholder="example" class="form-control" value="{{ $form->slug }}" required="" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="notification_email">Notification Email (Optional)</label>
                                <input type="email" class="form-control" name="notification_email" id="notification_email"
                                    value="{{ $form->notification_email }}">
                                <small class="form-text text-muted">The email where a notification is sent to once this form has been submitted</small>
                            </div>

                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="max_submissions">Maximum Submissions (Optional)</label>
                                    <input type="number" class="form-control" name="max_submissions" id="max_submissions"
                                        value="{{ $form->max_submissions }}">
                                    <small class="form-text text-muted">Maximum amount of times this form can be submitted before its closed. (Leave empty to not set a limit)</small>
                                </div>
    
                                <div class="form-group col-6">
                                    <label for="max_submissions_per_user">Maximum Submissions per user (Optional)</label>
                                    <input type="number" class="form-control" name="max_submissions_per_user" id="max_submissions_per_user"
                                        value="{{ $form->max_submissions_per_user }}">
                                    <small class="form-text text-muted">Maximum amount of times this form can be submitted by a single user. (If you enable guests, the form can be submitted multiple times by guests!)</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="required_packages">Required Package (Optional)</label>
                                <div class="input-group mb-2">
                                    <select name="required_packages[]" id="required_packages"
                                        class="form-control select2 select2-hidden-accessible" multiple="" tabindex="-1"
                                        aria-hidden="true">
                                        @foreach (Package::get() as $package)
                                            <option value="{{ $package->id }}" @if(in_array($package->id, $form->required_packages ?? [])) selected @endif>{{ $package->name }}</option>
                                        @endforeach

                                    </select>
                                    <small class="form-text text-muted">Does the user require certain packages in order to view this form</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="control-label">Make form paid</div>
                                <label class="custom-switch mt-2">
                                    <input type="checkbox" id="is_paid" onchange="formPaidUpdated(this)" class="custom-switch-input" value="1" @if($form->isPaid()) checked @endif/>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">
                                        If enabled, the user will have to pay before they can submit the form
                                    </span>
                                </label>
                            </div>

                            <div id="price_field" @if(!$form->isPaid()) style="display: none;" @endif>
                                <div class="form-group">
                                    <label for="price">Price</label>
                                    <input type="number" step="0.01" min="0" value="{{ $form->price ?? 0 }}" class="form-control" name="price" id="price"
                                        value="{{ old('price') }}">
                                    <small class="form-text text-muted">
                                        The price the user has to pay in order to submit the form
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="allowed_gateways">Allowed Gateways (Optional)</label>
                                    <div class="input-group mb-2">
                                        <select class="form-control select2 select2-hidden-accessible"
                                                name="allowed_gateways[]" tabindex="-1" aria-hidden="true" multiple>
                                            @foreach(App\Models\Gateways\Gateway::get() as $gateway)
                                                @if(!$gateway->status OR $gateway->type != 'once')
                                                    @continue
                                                @endif
                                                <option value="{{ $gateway->id }}" @if(in_array($gateway->id, $form->allowed_gateways ?? [])) selected @endif>{{ $gateway->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Select the gateways allowed to pay with</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4 col-12">
                                    <div class="control-label">Can guest view</div>
                                    <label class="custom-switch mt-2">
                                        <input type="checkbox" name="guest" class="custom-switch-input" @if($form->guest) checked="" @endif value="1" />
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">
                                            Can guests view this form?
                                        </span>
                                    </label>
                                </div>

                                <div class="form-group col-md-4 col-12">
                                    <div class="control-label">Can view own submission</div>
                                    <label class="custom-switch mt-2">
                                        <input type="checkbox" name="can_view_submission" onchange="canViewUpdated()" @if($form->can_view_submission) checked="" @endif class="custom-switch-input" value="1" />
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">
                                            Can users view their own submission?
                                        </span>
                                    </label>
                                </div>

                                <div class="form-group col-md-4 col-12" id="can_respond_field" @if(!$form->can_view_submission) style="display: none;" @endif>
                                    <div class="control-label">Can users respond</div>
                                    <label class="custom-switch mt-2">
                                        <input type="checkbox" name="can_respond" class="custom-switch-input" @if($form->can_respond) checked="" @endif value="1" />
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">
                                            Can users respond to to their submission?
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="control-label">Is Acitve?</div>
                                <label class="custom-switch mt-2">
                                    <input type="checkbox" id="active" name="active" class="custom-switch-input" value="1" @if($form->active) checked @endif/>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">
                                        Is this from active, toggle to deactivate the form
                                    </span>
                                </label>
                            </div>

                            <div class="col-md-12">
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- create field modal --}}
    <div class="modal fade" id="createFieldModal" tabindex="-1" role="dialog" aria-labelledby="createFieldModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createFieldModalLabel">Create Field</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('admin.forms.fields.store', $form->id) }}" method="POST">
                    <div class="modal-body">
                        @csrf

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="label">Label</label>
                                <input type="text" class="form-control" name="label" id="label" placeholder="Label" onchange="generateFieldId(this.value)" required>
                                <small class="form-text text-muted">Label of the field i.e "Email"</small>
                            </div>

                            <div class="form-group col-6">
                                <label for="label">Identifier</label>
                                <input type="text" class="form-control" name="name" id="field_name0" placeholder="Identifier" required>
                                <small class="form-text text-muted">Identifier of field (Leave as default if unsure)</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description (optional)</label>
                            <input type="text" class="form-control" name="description" id="description" placeholder="Description">
                            <small class="form-text text-muted">Short description of what the input field is for</small>
                        </div>

                        <div class="form-group">
                            <label for="type">Field Type</label>
                            <div class="input-group mb-2">
                                <select class="form-control select2 select2-hidden-accessible" onchange="fieldTypeUpdated(this.value)" name="type" tabindex="-1" aria-hidden="true" required>
                                    <option value="text">Text</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="select">Select</option>
                                    <option value="radio">Radio</option>
                                    <option value="email">Email</option>
                                    <option value="number">Number</option>
                                    <option value="date">Date</option>
                                    <option value="url">Url</option>
                                    <option value="password">Password</option>
                                </select>
                                <small class="form-text text-muted">Select field type</small>
                            </div>
                        </div>

                        <div id="options_div0" style="display: none">
                            <hr>
                            <div class="form-group">
                                <label for="options">Options</label>
                                <div id="more_options0">
                                    <input type="text" class="form-control mt-2" name="options[]" id="options0" placeholder="Option">
                                </div>
                                <div class="d-flex">
                                    <small class="form-text text-success mr-2" onclick="addFieldOption()" style="cursor: pointer;">Add option</small>
                                    <small class="form-text text-danger" onclick="removeFieldOption()" style="cursor: pointer;">Remove option</small>
                                </div>
                            </div>
                            <hr>
                        </div>

                        <div class="form-group" id="placeholderdiv0">
                            <label for="placeholder">Placeholder</label>
                            <input type="text" class="form-control" name="placeholder" id="placeholder" placeholder="Placeholder">
                            <small class="form-text text-muted">The placeholder text for this field</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="default_value">Default Value (optional)</label>
                            <input type="text" class="form-control" name="default_value" id="default_value" placeholder="Default Value">
                            <small class="form-text text-muted">Default Value of this field</small>
                        </div>

                        <div class="form-group">
                            <label for="rules">Validation Rules</label>
                            <input type="text" class="form-control" name="rules" id="rules0" placeholder="rules">
                            <small class="form-text text-muted">Validation rules help you make sure that the correct data is entered. For example, to make this field required use "required" </small>
                            <div>
                                <button type="button" onclick="appendRule('required')" class="btn btn-sm btn-primary">required</button>
                                <button type="button" onclick="appendRule('numeric')" class="btn btn-sm btn-primary">numeric</button>
                                <button type="button" onclick="appendRule('email')" class="btn btn-sm btn-primary">email</button>
                                <button type="button" onclick="appendRule('active_url')" class="btn btn-sm btn-primary">active url</button>
                                <button type="button" onclick="appendRule('url')" class="btn btn-sm btn-primary">url</button>
                                <button type="button" onclick="appendRule('date')" class="btn btn-sm btn-primary">date</button>
                                <button type="button" onclick="appendRule('min:3')" class="btn btn-sm btn-primary">min chars</button>
                                <button type="button" onclick="appendRule('max:255')" class="btn btn-sm btn-primary">max chars</button>
                                <button type="button" onclick="appendRule('in:audi,bmw,mercedes')" class="btn btn-sm btn-primary">in list</button>
                                <button type="button" onclick="appendRule('starts_with:test')" class="btn btn-sm btn-primary">stars with</button>
                                <button type="button" onclick="appendRule('ends_with:test')" class="btn btn-sm btn-primary">ends with</button>
                                <button type="button" onclick="appendRule('date')" class="btn btn-sm btn-primary">date</button>
                            </div>
                            <small class="form-text text-muted">View all <a href="https://laravel.com/docs/11.x/validation#available-validation-rules" target="_blank">validation rules</a> </small>

                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @foreach($form->fields()->orderBy('order', 'desc')->get() as $field)
    {{-- edit field modal --}}
    <div class="modal fade" id="editFieldModal{{ $field->id }}" tabindex="-1" role="dialog" aria-labelledby="editFieldModal{{ $field->id }}Label">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFieldModal{{ $field->id }}Label">{{ $field->label }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('admin.forms.fields.update', $field->id) }}" method="POST">
                    <div class="modal-body">
                        @csrf

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="label">Label</label>
                                <input type="text" class="form-control" name="label" value="{{ $field->label }}" id="label" placeholder="Label" onchange="generateFieldId(this.value, '{{ $field->id }}')" required>
                                <small class="form-text text-muted">Label of the field i.e "Email"</small>
                            </div>

                            <div class="form-group col-6">
                                <label for="label">Identifier</label>
                                <input type="text" class="form-control" name="name" value="{{ $field->name }}" id="field_name{{ $field->id }}" placeholder="Identifier" required>
                                <small class="form-text text-muted">Identifier of field (Leave as default if unsure)</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description (optional)</label>
                            <input type="text" class="form-control" name="description" value="{{ $field->description }}" id="description" placeholder="Description">
                            <small class="form-text text-muted">Short description of what the input field is for</small>
                        </div>

                        <div class="form-group">
                            <label for="type">Field Type</label>
                            <div class="input-group mb-2">
                                <select class="form-control select2 select2-hidden-accessible" onchange="fieldTypeUpdated(this.value, '{{$field->id}}')" name="type" tabindex="-1" aria-hidden="true" required>
                                    <option value="text" @if($field->type == 'text') selected @endif>Text</option>
                                    <option value="textarea" @if($field->type == 'textarea') selected @endif>Textarea</option>
                                    <option value="select" @if($field->type == 'select') selected @endif>Select</option>
                                    <option value="radio" @if($field->type == 'radio') selected @endif>Radio</option>
                                    <option value="email" @if($field->type == 'email') selected @endif>Email</option>
                                    <option value="number" @if($field->type == 'number') selected @endif>Number</option>
                                    <option value="date" @if($field->type == 'date') selected @endif>Date</option>
                                    <option value="url" @if($field->type == 'url') selected @endif>Url</option>
                                    <option value="password" @if($field->type == 'password') selected @endif>Password</option>
                                </select>
                                <small class="form-text text-muted">Select field type</small>
                            </div>
                        </div>

                        <div id="options_div{{$field->id}}" @if(!in_array($field->type, ['select', 'radio'])) style="display: none" @endif>
                            <hr>
                            <div class="form-group">
                                <label for="options">Options</label>
                                <div id="more_options{{$field->id}}">
                                    @foreach($field->options ?? [] as $option)
                                        <input type="text" class="form-control mt-2" name="options[]" id="options{{$field->id}}" value="{{ $option }}" placeholder="Option">
                                    @endforeach
                                </div>
                                <div class="d-flex">
                                    <small class="form-text text-success mr-2" onclick="addFieldOption('{{$field->id}}')" style="cursor: pointer;">Add option</small>
                                    <small class="form-text text-danger" onclick="removeFieldOption('{{$field->id}}')" style="cursor: pointer;">Remove option</small>
                                </div>
                            </div>
                            <hr>
                        </div>

                        <div class="form-group" id="placeholderdiv{{$field->id}}" @if(in_array($field->type, ['select', 'radio'])) style="display: none" @endif>
                            <label for="placeholder">Placeholder</label>
                            <input type="text" class="form-control" name="placeholder" value="{{$field->placeholder}}" id="placeholder" placeholder="Placeholder">
                            <small class="form-text text-muted">The placeholder text for this field</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="default_value">Default Value (optional)</label>
                            <input type="text" class="form-control" name="default_value" value="{{$field->default_value}}" id="default_value" placeholder="Default Value">
                            <small class="form-text text-muted">Default Value of this field</small>
                        </div>

                        <div class="form-group">
                            <label for="rules">Validation Rules</label>
                            <input type="text" class="form-control" value="{{ $field->rules }}" name="rules" id="rules{{ $field->id }}" placeholder="rules">
                            <small class="form-text text-muted">Validation rules help you make sure that the correct data is entered. For example, to make this field required use "required" </small>
                            <div>
                                <button type="button" onclick="appendRule('required', '{{$field->id}}')" class="btn btn-sm btn-primary">required</button>
                                <button type="button" onclick="appendRule('numeric', '{{$field->id}}')" class="btn btn-sm btn-primary">numeric</button>
                                <button type="button" onclick="appendRule('email', '{{$field->id}}')" class="btn btn-sm btn-primary">email</button>
                                <button type="button" onclick="appendRule('active_url', '{{$field->id}}')" class="btn btn-sm btn-primary">active url</button>
                                <button type="button" onclick="appendRule('url', '{{$field->id}}')" class="btn btn-sm btn-primary">url</button>
                                <button type="button" onclick="appendRule('date', '{{$field->id}}')" class="btn btn-sm btn-primary">date</button>
                                <button type="button" onclick="appendRule('min:3', '{{$field->id}}')" class="btn btn-sm btn-primary">min chars</button>
                                <button type="button" onclick="appendRule('max:255', '{{$field->id}}')" class="btn btn-sm btn-primary">max chars</button>
                                <button type="button" onclick="appendRule('in:audi,bmw,mercedes', '{{$field->id}}')" class="btn btn-sm btn-primary">in list</button>
                                <button type="button" onclick="appendRule('starts_with:test', '{{$field->id}}')" class="btn btn-sm btn-primary">stars with</button>
                                <button type="button" onclick="appendRule('ends_with:test', '{{$field->id}}')" class="btn btn-sm btn-primary">ends with</button>
                                <button type="button" onclick="appendRule('date', '{{$field->id}}')" class="btn btn-sm btn-primary">date</button>
                            </div>
                            <small class="form-text text-muted">View all <a href="https://laravel.com/docs/11.x/validation#available-validation-rules" target="_blank">validation rules</a> </small>

                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <script>
        showInfoAlert();
        function showInfoAlert()
        {
            // check if local storage has the alert
            var alert = localStorage.getItem('alert{{$form->id}}');
            if (alert == null) {
                document.getElementById('alert{{$form->id}}').style.display = '';
            }
        }

        function hideInfoAlert(id) {
            document.getElementById(id).style.display = 'none';
            localStorage.setItem(id, 'true');
        }

        function canViewUpdated() {
            var isChecked = document.getElementsByName('can_view_submission')[0].checked;
            if (isChecked) {
                document.getElementById('can_respond_field').style.display = '';
            } else {
                document.getElementById('can_respond_field').style.display = 'none';
            }
        }

        function formPaidUpdated(element) {
            var isChecked = element.checked;
            if (isChecked) {
                document.getElementById('price_field').style.display = '';
            } else {
                document.getElementById('price_field').style.display = 'none';
                document.getElementById('price').value = 0;
            }
        }

        function generateFieldId(value, id = 0) {
            var name = document.getElementById('field_name' + id);
            name.value = value
                        .toLowerCase() // convert to lowercase
                        .trim() // remove leading and trailing whitespace
                        .replace(/[^\w\s-]/g, '') // remove non-word characters
                        .replace(/[\s_-]+/g, '_') // replace spaces, underscores, and hyphens with a single hyphen
                        .replace(/^-+|-+$/g, ''); // remove leading and trailing hyphens
        }
        
        function fieldTypeUpdated(value, id = 0)
        {
            if(value == 'select' || value == 'radio') {
                // set display to ''
                document.getElementById('options_div' + id).style.display = '';
                // hide placeholder
                document.getElementById('placeholderdiv' + id).style.display = 'none';
            } else {
                // set display to none
                document.getElementById('options_div' + id).style.display = 'none';
                // show placeholder
                document.getElementById('placeholderdiv' + id).style.display = '';
            }
        }

        function appendRule(rule, id = 0)
        {
            var rules = document.getElementById('rules' + id);

            // check if rule already exists
            if (rules.value.includes(rule)) {
                return;
            }

            if (rules.value.length > 0) {
                // make sure string does not end with | 
                if (rules.value.endsWith('|')) {
                    rules.value += rule;
                } else {
                    rules.value += '|' + rule;
                }
            } else {
                rules.value = rule;
            }

        }

        function addFieldOption(id = 0)
        {
            // duplicate the options field
            var options = document.getElementById('options' + id);
            var options_div = document.getElementById('more_options' + id);

            var new_options = options.cloneNode(true);
            new_options.value = '';
            options_div.appendChild(new_options);
        }

        function removeFieldOption(id = 0)
        {
            // remove the last options field
            var options_div = document.getElementById('more_options' + id);

            if (options_div.children.length == 1) {
                return;
            }

            options_div.removeChild(options_div.lastChild);

        }

        function generateSlug() {
            var slug = document.getElementById('slug');
            var name = document.getElementById('name').value;
            
            // set title to name
            document.getElementById('title').value = name;

            slug.value = name
                        .toLowerCase() // convert to lowercase
                        .trim() // remove leading and trailing whitespace
                        .replace(/[^\w\s-]/g, '') // remove non-word characters
                        .replace(/[\s_-]+/g, '-') // replace spaces, underscores, and hyphens with a single hyphen
                        .replace(/^-+|-+$/g, ''); // remove leading and trailing hyphens
        }
    </script>
@endsection