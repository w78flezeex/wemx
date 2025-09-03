@extends(AdminTheme::wrapper(), ['title' => 'DiscordWebhooks', 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Discord Embeds</h4>
                </div>
                <div class="card-body">
                    <table class="table table-responsive table-hover">
                        <thead>
                        <tr>
                            <th class="w-100">Embed</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($events as $name => $e)
                            @empty($e['description'])
                                @continue
                            @endempty
                            <tr>
                                <td>{{ str_replace('_', ' ', ucfirst($name)) }}</td>
                                <td class="text-right">
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#{{ $name }}">
                                        Edit
                                    </button>

                                </td>
                            </tr>

                            <div class="modal fade" id="{{ $name }}" tabindex="-1" role="dialog"
                                 aria-labelledby="{{ $name }}_label" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"
                                                id="{{ $name }}_label">{{ str_replace('_', ' ', ucfirst($name)) }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="post" id="form_{{ $name }}">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="embedColor" class="form-label">Color</label>
                                                    <input type="color" class="form-control form-control-color"
                                                           id="embedColor"
                                                           name="{{ class_basename($e['event']) }}[color]"
                                                           value="{{ $embedSettings[class_basename($e['event'])]['color'] ?? '#'.$e['listener']::$color }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="embedTitle" class="form-label">Title</label>
                                                    <input type="text" class="form-control" id="embedTitle"
                                                           name="{{ class_basename($e['event']) }}[title]"
                                                           value="{{ $embedSettings[class_basename($e['event'])]['title'] ?? $e['listener']::$title }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="embedDescription" class="form-label">Description</label>
                                                    <textarea class="form-control" id="embedDescription"
                                                              name="{{ class_basename($e['event']) }}[description]">{{ $embedSettings[class_basename($e['event'])]['description'] ?? $e['listener']::$description }}
                                                    </textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="embedWebhook" class="form-label">Webhook URL (Optional)</label>
                                                    <input type="text" class="form-control" id="embedWebhook"
                                                           name="{{ class_basename($e['event']) }}[webhook]"
                                                           value="{{ $embedSettings[class_basename($e['event'])]['webhook'] ?? ''}}">
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                                            </button>
                                            <button type="button"
                                                    onclick="return document.getElementById('form_{{ $name }}').submit()"
                                                    class="btn btn-primary">Save changes
                                            </button>
                                        </div>
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
@endsection
