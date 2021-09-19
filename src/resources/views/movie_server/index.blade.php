@extends('juzaweb::layouts.backend')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="btn-group float-right">
                <a href="{{ $linkCreate }}" class="btn btn-success add-new-server"><i class="fa fa-plus-circle"></i> @lang('mymo::app.add_new')</a>
            </div>
        </div>
    </div>

    {{ $dataTable->render() }}

    <script type="text/javascript">
        function status_formatter(value, row, index) {
            if (value == 1) {
                return '<span class="text-success">@lang('mymo::app.enabled')</span>';
            }
            return '<span class="text-danger">@lang('mymo::app.disabled')</span>';
        }

        function options_formatter(value, row, index) {
            let result = '<a href="'+ row.upload_url +'" class="btn btn-success btn-sm"><i class="fa fa-upload"></i> @lang('mymo::app.upload_videos')</a>';
            return result;
        }

    </script>
@endsection