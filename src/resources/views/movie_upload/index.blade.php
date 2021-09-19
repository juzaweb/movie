@extends('juzaweb::layouts.backend')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="float-right">
                <div class="btn-group">
                    <button type="button" class="btn btn-success status-button" data-status="1"><i class="fa fa-check-circle"></i> @lang('mymo::app.enabled')</button>
                    <button type="button" class="btn btn-warning status-button" data-status="0"><i class="fa fa-times-circle"></i> @lang('mymo::app.disabled')</button>
                </div>

                <div class="btn-group">
                    <a href="{{ route('admin.movies.servers.upload.create', [$page_type, $server->id]) }}" class="btn btn-success add-new-video"><i class="fa fa-plus-circle"></i> @lang('mymo::app.add_video')</a>
                    <button type="button" class="btn btn-danger" id="delete-item"><i class="fa fa-trash"></i> @lang('mymo::app.delete')</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <form method="get" class="form-inline" id="form-search">

                <div class="form-group mb-2 mr-1">
                    <label for="search" class="sr-only">@lang('mymo::app.search')</label>
                    <input name="search" type="text" id="search" class="form-control" placeholder="@lang('mymo::app.search')" autocomplete="off">
                </div>

                <div class="form-group mb-2 mr-1">
                    <label for="status" class="sr-only">@lang('mymo::app.status')</label>
                    <select name="status" id="status" class="form-control select2-default" data-placeholder="--- @lang('mymo::app.status') ---">
                        <option value="1">@lang('mymo::app.enabled')</option>
                        <option value="0">@lang('mymo::app.disabled')</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mb-2"><i class="fa fa-search"></i> @lang('mymo::app.search')</button>
            </form>
        </div>
    </div>

    <div class="table-responsive mb-5">
        <table class="table mymo-table">
            <thead>
            <tr>
                <th data-width="3%" data-field="state" data-checkbox="true"></th>
                <th data-field="label" data-width="15%" data-formatter="label_formatter">@lang('mymo::app.label')</th>
                <th data-field="url">@lang('mymo::app.url')</th>
                <th data-field="source" data-width="10%">@lang('mymo::app.source')</th>
                <th data-field="order" data-width="10%" data-align="center">@lang('mymo::app.order')</th>
                <th data-width="15%" data-field="status" data-align="center" data-formatter="status_formatter">@lang('mymo::app.status')</th>
                <th data-width="15%" data-field="action" data-align="center" data-formatter="action_formatter">@lang('mymo::app.action')</th>
            </tr>
            </thead>
        </table>
    </div>

    <script type="text/javascript">
        function label_formatter(value, row, index) {
            return '<a href="'+ row.edit_url +'">'+ value +'</a>';
        }

        function status_formatter(value, row, index) {
            if (value == 1) {
                return '<span class="text-success">'+ mymo.lang.enabled +'</span>';
            }
            return '<span class="text-danger">'+ mymo.lang.disabled +'</span>';
        }

        function action_formatter(value, row, index) {
            let str = '';
            str += '<a href="'+ row.subtitle_url +'" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> '+ mymo.lang.add_subtitle +'</a>';
            return str;
        }

        var table = new JuzawebTable({
            url: '{{ route('admin.movies.servers.upload.getdata', [$page_type, $server->id]) }}',
            remove_url: '{{ route('admin.movies.servers.upload.remove', [$page_type, $server->id]) }}',
        });
    </script>
@endsection