@extends('juzaweb::layouts.backend')

@section('content')
    @component('juzaweb::components.form_resource', [
        'model' => $model
    ])

        <div class="row">

            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-form-label" for="name">@lang('mymo::app.name')</label>

                    <input type="text" name="name" class="form-control" id="name" autocomplete="off" required value="{{ $model->name }}">
                </div>

                <div class="form-group">
                    <label class="col-form-label" for="order">@lang('mymo::app.order')</label>

                    <input type="text" name="order" class="form-control" id="order" autocomplete="off" value="{{ $model->order ?: 1 }}" required>
                </div>

                <div class="form-group">
                    <label class="col-form-label" for="baseStatus">@lang('mymo::app.status')</label>
                    <select name="status" id="baseStatus" class="form-control">
                        <option value="1" @if($model->status == 1) selected @endif>@lang('mymo::app.enabled')</option>
                        <option value="0" @if($model->status == 0 && !is_null($model->status)) selected @endif>@lang('mymo::app.disabled')</option>
                    </select>
                </div>
            </div>
        </div>

        <input type="hidden" name="movie_id" value="{{ $movie_id }}">

    @endcomponent
@endsection
