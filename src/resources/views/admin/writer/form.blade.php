@extends('core::layouts.admin')

@section('content')
    <form action="{{ $action }}" class="form-ajax" method="post">
        @if($model->exists)
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-md-12">
                <a href="{{ admin_url('movie-writers') }}" class="btn btn-warning">
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
                        <h3 class="card-title">{{ __('movie::translation.writer') }}</h3>
                    </div>
                    <div class="card-body">
                        {!! Field::text($model, "name", ['label' => __('movie::translation.name'), 'value' => $model->name]) !!}

                        {!! Field::textarea($model, "bio", ['label' => __('movie::translation.bio'), 'value' => $model->bio]) !!}

                        {!! Field::image($model, "thumbnail", ['label' => __('movie::translation.thumbnail'), 'value' => $model->thumbnail]) !!}
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <x-language-card :label="$model" :locale="$locale" />
            </div>
        </div>
    </form>
@endsection
