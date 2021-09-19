@extends('juzaweb::layouts.backend')

@section('content')
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="float-right">
                <div class="btn-group">
                    <a href="javascript:void(0)" class="btn btn-success" data-toggle="modal" data-target="#tmdb-modal"><i class="fa fa-plus"></i> @lang('mymo::app.add_from_tmdb')</a>
                </div>

                <div class="btn-group">
                    <a href="{{ route('admin.tv-series.create') }}" class="btn btn-success"><i class="fa fa-plus-circle"></i> @lang('mymo::app.add_new')</a>
                </div>
            </div>
        </div>
    </div>

    {{ $dataTable->render() }}

    @include('mymo::movies.form_tmdb')
@endsection
