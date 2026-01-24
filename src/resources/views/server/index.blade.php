@extends('core::layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <a href="{{ $createUrl }}" class="btn btn-primary mb-3">
                <i class="fas fa-plus"></i> {{ __('movie::translation.create_server') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <x-card title="{{ __('movie::translation.servers') }}">
                {{ $dataTable->table() }}
            </x-card>
        </div>
    </div>
@endsection

@section('scripts')
    {{ $dataTable->scripts(null, ['nonce' => csp_script_nonce()]) }}
@endsection
