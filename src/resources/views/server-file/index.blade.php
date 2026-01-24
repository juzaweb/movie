@extends('core::layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-3">
            @can('Server Files.create')
                <x-card title="{{ __('movie::translation.quick_create_server_file') }}">
                    <form action="" class="form-ajax" method="post" data-success="quickCreateSuccess"
                          id="quick-create-form">
                        @csrf

                        {{ Field::text(__('movie::translation.name'), 'name', ['required' => true, 'placeholder' => __('movie::translation.enter_file_name')]) }}

                        {{
                            Field::select(__('movie::translation.source'), 'source', ['required' => true])
                                ->dropDownList(\Juzaweb\Modules\Core\Enums\VideoSource::all())
                        }}

                        {{ Field::text(__('movie::translation.video'), 'path', ['required' => true, 'placeholder' => __('movie::translation.enter_video_path_or_url')]) }}

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i> {{ __('movie::translation.create') }}
                            </button>
                        </div>
                    </form>
                </x-card>
            @endcan
        </div>

        <div class="col-md-9">
            <x-card title="{{ __('movie::translation.server_files') }}">
                {{ $dataTable->table() }}
            </x-card>
        </div>
    </div>
@endsection

@section('scripts')
    {{ $dataTable->scripts(null, ['nonce' => csp_script_nonce()]) }}

    <script type="text/javascript" nonce="{{ csp_script_nonce() }}">
        function quickCreateSuccess(response) {
            $('#quick-create-form')[0].reset();
            $('#jw-datatable').DataTable().ajax.reload();
        }
    </script>
@endsection
