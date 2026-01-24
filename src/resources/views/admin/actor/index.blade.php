@extends('core::layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <a href="{{ admin_url('movie-actors/create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('movie::translation.create_new_actor') }}
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-card title="{{ __('movie::translation.movie_actors') }}">
                {{ $dataTable->table() }}
            </x-card>
        </div>
    </div>
@endsection

@section('scripts')
    {{ $dataTable->scripts(null, ['nonce' => csp_script_nonce()]) }}
@endsection
