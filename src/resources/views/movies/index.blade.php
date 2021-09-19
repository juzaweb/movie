@extends('juzaweb::layouts.backend')

@section('content')

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="float-right">
                <div class="btn-group">
                    <a href="javascript:void(0)" class="btn btn-success" data-toggle="modal" data-target="#tmdb-modal"><i class="fa fa-plus"></i> @lang('mymo::app.add_from_tmdb')</a>
                </div>

                <div class="btn-group">
                    <a href="{{ route('admin.movies.create') }}" class="btn btn-success"><i class="fa fa-plus-circle"></i> @lang('mymo::app.add_new')</a>
                </div>
            </div>
        </div>
    </div>

    {{ $dataTable->render() }}

    <script type="text/javascript">
        function thumbnail_formatter(value, row, index) {
            return '<img src="'+ row.thumb_url +'" class="w-100">';
        }

        function name_formatter(value, row, index) {
            return '<a href="'+ row.edit_url +'">'+ value +'</a>';
        }

        function status_formatter(value, row, index) {
            if (row.status == 'publish') {
                return '<span class="text-success">'+ mymo.lang.publish +'</span>';
            }

            return '<span class="text-danger">'+ mymo.lang[row.status] +'</span>';
        }

        function options_formatter(value, row, index) {
            let result = '<div class="dropdown d-inline-block mb-2 mr-2">\n' +
                '          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">\n' +
                '            Options' +
                '          </button>\n' +
                '          <div class="dropdown-menu" role="menu" style="">\n' +
                '            <a href="'+ row.upload_url +'" class="dropdown-item">Upload videos</a>\n' +
                '            <a href="'+ row.download_url +'" class="dropdown-item">Download videos</a>\n' +
                '            <a href="'+ row.preview_url +'" target="_blank" class="dropdown-item">Preview</a>\n' +
                '          </div>\n' +
                '        </div>';
            return result;
        }
    </script>

{{--@include('mymo::movies.form_tmdb')--}}

@endsection
