@extends('juzaweb::layouts.backend')

@section('content')
    <form method="post" action="{{ route('admin.movies.servers.save', [$page_type, $movie->id]) }}" class="form-ajax">

        <div class="row">
            <div class="col-md-12">
                <div class="btn-group float-right">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> @lang('mymo::app.save')</button>
                </div>
            </div>
        </div>

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

        <input type="hidden" name="id" value="{{ $model->id }}">
    </form>
@endsection
