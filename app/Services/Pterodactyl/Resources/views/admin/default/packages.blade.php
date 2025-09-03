@extends(AdminTheme::wrapper(), ['title' => __('admin.packages'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {!! __('admin.packages') !!}
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{!! __('admin.id') !!}</th>
                                <th>{!! __('admin.name') !!}</th>
                                <th class="text-right">{!! __('admin.actions') !!}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($packages as $package)
                                <tr>
                                    <td>{{ $package->id }}</td>
                                    <td>{{ $package->name }}</td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                                data-target="#packageModal{{ $package->id }}">
                                            {!! __('admin.manage') !!}
                                        </button>
                                    </td>
                                </tr>

                                @php
                                    $existingCommands = json_decode($package->settings('commands', '')) ?? [];
                                @endphp

                                <!-- Modal window for managing recommended commands -->
                                <div class="modal fade" id="packageModal{{ $package->id }}" tabindex="-1"
                                     role="dialog" aria-labelledby="packageModalLabel{{ $package->id }}"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('pterodactyl.commands.store', $package->id) }}"
                                                  method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="packageModalLabel{{ $package->id }}">
                                                        {!! __('admin.manage') !!}
                                                        : {{ $package->name }}
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="{{ __('admin.close') }}">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="commands{{ $package->id }}">
                                                            Recommends Commands
                                                        </label>
                                                        <textarea name="commands" id="commands{{ $package->id }}"
                                                                  class="form-control"
                                                                  style="height: 50vh !important;"
                                                                  rows="10">{{ old('commands', implode("\n", $existingCommands ?? [])) }}</textarea>
                                                        <small class="form-text text-muted">
                                                            Every command should be on a new line.
                                                        </small>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">{!! __('admin.close') !!}
                                                    </button>
                                                    <button type="submit"
                                                            class="btn btn-primary">{!! __('admin.save_changes') !!}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
