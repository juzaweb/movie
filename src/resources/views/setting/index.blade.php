@extends('core::layouts.admin')

@section('content')
    <form action="{{ route('admin.movie-settings.update') }}" class="form-ajax" method="post">
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('movie::translation.save') }}
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <x-card title="{{ __('movie::translation.tmdb_settings') }}">
                    {{ Field::security(__('movie::translation.tmdb_api_key'), 'tmdb_api_key', [
                        'placeholder' => __('movie::translation.enter_your_tmdb_api_key'),
                        'value' => setting('tmdb_api_key'),
                    ]) }}

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        {{ __('movie::translation.tmdb_api_key_description') }}
                        <a rel="noopener noreferrer" href="https://www.themoviedb.org/settings/api" target="_blank">
                            {{ __('movie::translation.get_api_key') }}
                        </a>
                    </div>
                </x-card>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('movie::translation.save') }}
                </button>
            </div>
        </div>
        <br />
    </form>
@endsection
