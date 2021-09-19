@extends('juzaweb::layouts.backend')

@section('content')
    @if($model->id)
        <div class="btn-group mr-5">
            <a href="{{ route('admin.movies.servers', ['tv-series', $model->id]) }}" class="btn btn-success"><i class="fa fa-upload"></i> @lang('mymo::app.upload_videos')</a>
        </div>
    @endif

    @component('juzaweb::components.form_resource', [
        'method' => $model->id ? 'put' : 'post',
        'action' =>  $model->id ?
            route('admin.movies.update', [$model->id]) :
            route('admin.movies.store')
    ])

    <div class="row">
        <div class="col-md-8">

            <div class="form-group">
                <label class="col-form-label" for="name">@lang('mymo::app.name')</label>

                <input type="text" name="name" class="form-control" id="name" value="{{ $model->name }}" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label class="col-form-label" for="other_name">@lang('mymo::app.other_name')</label>

                <input type="text" name="other_name" class="form-control" id="other_name" value="{{ $model->other_name }}" autocomplete="off">
            </div>

            <div class="form-group">
                <label class="col-form-label" for="baseDescription">@lang('mymo::app.description')</label>
                <textarea class="form-control" name="description" id="baseDescription" rows="6">{{ $model->description }}</textarea>
            </div>

            <div class="form-group">
                <label class="col-form-label" for="rating">@lang('mymo::app.rating')</label>

                <input type="text" name="rating" class="form-control" id="rating" value="{{ $model->rating }}" autocomplete="off">
            </div>

            <div class="form-group">
                <label class="col-form-label" for="release">@lang('mymo::app.release')</label>

                <input type="text" name="release" class="form-control datepicker" id="release" value="{{ $model->release }}" autocomplete="off">
            </div>

            <div class="form-group">
                <label class="col-form-label" for="runtime">@lang('mymo::app.runtime')</label>
                <input type="text" name="runtime" class="form-control" id="runtime" value="{{ $model->runtime }}" autocomplete="off">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-form-label" for="current_episode">@lang('mymo::app.current_episode')</label>
                        <input type="text" name="current_episode" class="form-control" id="current_episode" value="{{ $model->current_episode }}" autocomplete="off">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-form-label" for="max_episode">@lang('mymo::app.max_episode')</label>
                        <input type="text" name="max_episode" class="form-control" id="max_episode" value="{{ $model->max_episode }}" autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-form-label" for="trailer_link">@lang('mymo::app.trailer')</label>
                <input type="text" name="trailer_link" class="form-control" id="trailer_link" value="{{ $model->trailer_link }}" autocomplete="off">
            </div>

            {{--qualities--}}

            <input type="hidden" name="tv_series" value="1">

            @do_action('post_type.movies.form.left')
        </div>

        <div class="col-md-4">
            @component('juzaweb::components.form_select', [
                    'label' => trans('juzaweb::app.status'),
                    'name' => 'status',
                    'value' => $model->status,
                    'options' => [
                        'publish' => trans('juzaweb::app.publish'),
                        'private' => trans('juzaweb::app.private'),
                        'draft' => trans('juzaweb::app.draft'),
                    ],
                ])
            @endcomponent

            @include('juzaweb::components.form_image', [
                'label' => trans('mymo::app.thumbnail'),
                'name' => 'thumbnail',
                'value' => $model->thumbnail
            ])

            @include('juzaweb::components.form_image', [
                'label' => trans('mymo::app.poster'),
                'name' => 'poster',
                'value' => $model->poster
            ])

            @do_action('post_type.movies.form.rigth', $model)
        </div>
    </div>

    @endcomponent
@endsection
