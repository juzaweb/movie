@extends('core::layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-3">
            @can('movie-countries.create')
                <x-card title="{{ __('movie::translation.add_country') }}">
                    <form action="" method="post" class="form-ajax" data-success="quickCreateSuccess" id="quick-create-form">
                        {{ Field::text(__('movie::translation.name'), 'name', ['required' => true]) }}

                        {{ Field::text(__('movie::translation.slug'), 'slug', ['required' => true]) }}

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i> {{ __('movie::translation.add_country') }}
                            </button>
                        </div>
                    </form>
                </x-card>
            @endcan
        </div>

        <div class="col-md-9">
            <x-card title="{{ __('movie::translation.movie_countries') }}">
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

        $(function () {
            $('input[name="name"]').on('blur', function() {
                var name = $(this).val();
                var slug = generate_slug(name);
                $('input[name="slug"]').val(slug);
            });
        });
    </script>
@endsection
