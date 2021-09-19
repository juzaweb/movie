@extends('juzaweb::layouts.backend')

@section('content')

    @component('juzaweb::components.form_resource', [
        'method' => $model->id ? 'put' : 'post',
        'action' =>  $model->id ?
            route('admin.movies.update', [$model->id]) :
            route('admin.movies.store')
    ])
        @if($model->id)
            <div class="btn-group mr-5">
                <a href="{{ route('admin.movies.servers', ['movies', $model->id]) }}" class="btn btn-success"><i class="fa fa-upload"></i> @lang('mymo::app.upload_videos')</a>
            </div>
        @endif

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

            @include('juzaweb::components.form_ckeditor', [
                'label' => trans('mymo::app.description'),
                'name' => 'description',
                'value' => $model->description
            ])

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

            <div class="form-group">
                <label class="col-form-label" for="trailer_link">@lang('mymo::app.trailer')</label>
                <input type="text" name="trailer_link" class="form-control" id="trailer_link" value="{{ $model->trailer_link }}" autocomplete="off">
            </div>

            {{--qualities--}}

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
                'value' => $model->getThumbnail()
            ])

            @include('juzaweb::components.form_image', [
                'label' => trans('mymo::app.poster'),
                'name' => 'poster',
                'value' => $model->getPoster()
            ])

            @do_action('post_type.movies.form.rigth', $model)
        </div>
    </div>
    @endcomponent

@endsection
