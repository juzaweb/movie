@extends('core::layouts.admin')

@section('content')
    <form action="{{ $action }}" class="form-ajax" method="post">
        @if($model->exists)
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-md-12">
                <a href="{{ admin_url('movie-years') }}" class="btn btn-warning">
                    <i class="fas fa-arrow-left"></i> {{ __('movie::translation.back') }}
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('movie::translation.save') }}
                </button>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('movie::translation.year') }}</h3>
                    </div>
                    <div class="card-body">
                        {!! Field::text($model, "name", ['label' => __('movie::translation.name'), 'value' => $model->name]) !!}
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
