@extends(AdminTheme::wrapper(), ['title' => 'Package Events', 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('css_libraries')
    <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.css')) }}" />
    <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/select2/dist/css/select2.min.css')) }}">
@endsection

@section('js_libraries')
    <script src="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.js')) }}"></script>
    <script src="{{ asset(AdminTheme::assets('modules/select2/dist/js/select2.full.min.js')) }}"></script>
@endsection

@section('container')
    <section class="section">
        <div class="section-body">
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Package Events</h4>
                        <div class="card-header-action">
                            <button class="btn btn-icon icon-left btn-primary" data-toggle="modal" data-target="#createEventModal">
                                <i class="fas fa-pencil-alt"></i>
                                New Event
                            </button>
                        </div>

                    </div>


                    <div class="card-body p-0">
                        @if($events->isEmpty())
                            @include(AdminTheme::path('empty-state'), ['title' => 'No events found', 'description' => 'Create a new event by clicking the button above.'])
                        @else 
                        <div class="table-responsive">
                            <table class="table table-striped table-md">
                                <tbody>
                                    <tr>
                                        <th class="text-center">{!! __('Name') !!}</th>
                                        <th class="text-center">{!! __('Package') !!}</th>
                                        <th class="text-center">{!! __('Event') !!}</th>
                                        <th class="text-center">{!! __('Action') !!}</th>
                                        <th class="text-center">{!! __('Roles') !!}</th>
                                        <th class="text-center">{!! __('Actions') !!}</th>
                                    </tr>

                                    @foreach ($events as $event)
                                        <tr>
                                            <td class="text-center">{{ $event->name }}</td>
                                            <td class="text-center">
                                                @if($event->all_packages)
                                                    All Packages
                                                @else
                                                @foreach ($event->packages ?? [] as $package)
                                                    <a href="{{ route('packages.edit', $package) }}">{{ $package }}</a>
                                                @endforeach
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $event->event }}</td>

                                            <td class="text-center">
                                                {{ $event->action }}
                                            </td>

                                            <td class="text-center">
                                                @foreach($roles as $role)
                                                    @if(in_array($role['id'], $event->roles))
                                                        {{ '@' . $role['name'] }}
                                                    @endif
                                                @endforeach
                                            </td>

                                            <td class="text-center">
                                                <a href="{{ route('admin.discord-connect.packages.destroy', $event->id) }}"
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
                        {{ $events->links(AdminTheme::pagination()) }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- create evemt modal --}}
    <div class="modal fade" id="createEventModal" tabindex="-1" role="dialog" aria-labelledby="createEventModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createEventModalLabel">Create Event</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ route('admin.discord-connect.packages.create') }}" method="POST">
                    <div class="modal-body">
                        @csrf

                        <div class="row">
                            <div class="form-group col-12">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="Name" placeholder="Name" required>
                                <small class="form-text text-muted">Name of the Event i.e "User Roles"</small>
                            </div>

                            <div class="form-group col-12">
                                <div class="control-label">
                                    Enable for All Packages
                                </div>
                                <label class="custom-switch mt-2">
                                    <input type="checkbox" name="all_packages" onclick="allPackagesUpdated(this, 0)" value="1" class="custom-switch-input" />
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">Enable Event for all packages?</span>
                                </label>
                            </div>

                            <div class="form-group col-12" id="packagesdiv0">
                                <label for="packages[]">Package</label>
                                <div class="input-group mb-2">
                                    <select class="form-control select2 select2-hidden-accessible" multiple name="packages[]" id="packages0" tabindex="-1" aria-hidden="true">
                                        @foreach(Package::latest()->get() as $package)
                                            <option value="{{ $package->id }}">{{ $package->name }} ({{ $package->service }})</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Select the package this event belongs to
                                    </small>
                                </div>
                            </div>

                            <div class="form-group col-12">
                                <label for="event">Event</label>
                                <div class="input-group mb-2">
                                    <select class="form-control select2 select2-hidden-accessible" name="event" tabindex="-1" aria-hidden="true" required>
                                        <option value="order_created">Order Created</option>
                                        <option value="order_renewed">Order Renewed</option>
                                        <option value="order_upgraded">Order Upgraded</option>
                                        <option value="order_suspended">Order Suspended</option>
                                        <option value="order_unsuspended">Order Unsuspended</option>
                                        <option value="order_terminated">Order Terminated</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        Select the event to be performed
                                    </small>
                                </div>
                            </div>

                            <div class="form-group col-12">
                                <label for="action">Action</label>
                                <div class="input-group mb-2">
                                    <select class="form-control select2 select2-hidden-accessible" name="action" tabindex="-1" aria-hidden="true" required>
                                        <option value="give">Give Role</option>
                                        <option value="remove">Remove Role</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        Select the action to be performed
                                    </small>
                                </div>
                            </div>

                            <div class="form-group col-12">
                                <label for="roles[]">Roles</label>
                                <div class="input-group mb-2">
                                    <select class="form-control select2 select2-hidden-accessible" multiple name="roles[]" tabindex="-1" aria-hidden="true" required>
                                        @foreach($roles->all() as $role)
                                            <option value="{{ $role['id'] }}">{{ $role['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Select the roles to be given or removed
                                    </small>
                                </div>
                            </div>
                        </div> 

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function allPackagesUpdated(element, id) {
            packagesDiv = document.getElementById('packagesdiv' + id);
            if (element.checked) {
                // display none 
                packagesDiv.style.display = 'none';
            } else {
                packagesDiv.style.display = '';
            }
        }
    </script>
@endsection
