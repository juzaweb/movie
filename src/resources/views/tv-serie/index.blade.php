@extends('core::layouts.admin')

@section('content')
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ $createUrl }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('movie::translation.create_tv_serie') }}
            </a>

            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#tmdbImportModal">
                <i class="fas fa-download"></i> {{ __('movie::translation.import_from_tmdb') }}
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <x-card title="{{ __('movie::translation.tv_series') }}">
                {{ $dataTable->table() }}
            </x-card>
        </div>
    </div>
@endsection

@section('scripts')
    {{ $dataTable->scripts(null, ['nonce' => csp_script_nonce()]) }}

    <!-- TMDB Import Modal -->
    <div class="modal fade" id="tmdbImportModal" tabindex="-1" role="dialog" aria-labelledby="tmdbImportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tmdbImportModalLabel">{{ __('movie::translation.import_from_tmdb') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="import-message" class="mb-3 box-hidden"></div>

                    <div class="form-group">
                        <label for="tmdb_id_input">{{ __('movie::translation.tmdb_id') }}</label>
                        <input type="text" class="form-control" id="tmdb_id_input"
                            placeholder="{{ __('movie::translation.enter_tmdb_id') }}">
                        <small class="form-text text-muted">
                            {{ __('movie::translation.tmdb_api_key_description') }}
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ __('movie::translation.back') }}</button>
                    <button type="button" class="btn btn-primary" id="importFromTmdbBtn">
                        <i class="fas fa-download"></i> {{ __('movie::translation.import') }}
                    </button>
                </div>
            </div>
        </div>

        <script type="text/javascript" nonce="{{ csp_script_nonce() }}">
            $(function() {
                $('#importFromTmdbBtn').on('click', function() {
                    const tmdbId = $('#tmdb_id_input').val();
                    const btn = $(this);

                    if (!tmdbId) {
                        show_message('{{ __('movie::translation.enter_tmdb_id') }}', 'error');
                        return;
                    }

                    btn.prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin"></i> {{ __('movie::translation.importing') }}');

                    $.ajax({
                        url: '{{ route('admin.tv-series.import-from-tmdb') }}',
                        method: 'POST',
                        data: {
                            tmdb_id: tmdbId,
                        },
                        success: function(response) {
                            if (response.success === true) {
                                $('#tmdbImportModal').modal('hide');
                                show_message(response, false, $('#import-message'));

                                // Redirect to the edit page
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                }
                            } else {
                                show_message(response);
                            }
                        },
                        error: function(xhr) {
                            show_message(xhr.responseJSON, false, $('#import-message'));
                        },
                        complete: function() {
                            btn.prop('disabled', false).html(
                                '<i class="fas fa-download"></i> {{ __('movie::translation.import') }}'
                                );
                        }
                    });
                });
            });
        </script>
    @endsection
