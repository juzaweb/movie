@extends('juzaweb::layouts.backend')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="btn-group float-right">
                <a href="{{ route('admin.sliders.create') }}" class="btn btn-success"><i class="fa fa-plus-circle"></i> @lang('juzaweb::app.add_new')</a>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <form method="post" class="form-inline">
                @csrf
                <select name="bulk_actions" class="form-control w-60 mb-2 mr-1">
                    <option value="">@lang('juzaweb::app.bulk_actions')</option>
                    <option value="delete">@lang('juzaweb::app.delete')</option>
                </select>

                <button type="submit" class="btn btn-primary mb-2" id="apply-action">@lang('juzaweb::app.apply')</button>
            </form>
        </div>

        <div class="col-md-8">
            <form method="get" class="form-inline" id="form-search">

                <div class="form-group mb-2 mr-1">
                    <label for="inputName" class="sr-only">@lang('juzaweb::app.search')</label>
                    <input name="search" type="text" id="inputName" class="form-control" placeholder="@lang('juzaweb::app.search')" autocomplete="off">
                </div>


                <button type="submit" class="btn btn-primary mb-2"><i class="fa fa-search"></i> @lang('juzaweb::app.search')</button>
            </form>
        </div>

    </div>

    <div class="table-responsive mb-5">
        <table class="table mymo-table">
            <thead>
                <tr>
                    <th data-width="3%" data-field="state" data-checkbox="true"></th>
                    <th data-field="name" data-formatter="name_formatter">@lang('juzaweb::app.name')</th>
                    <th data-width="15%" data-field="created_at">@lang('juzaweb::app.created_at')</th>
                </tr>
            </thead>
        </table>
    </div>

    <script type="text/javascript">
        function thumbnail_formatter(value, row, index) {
            return '<img src="'+ row.thumb_url +'" class="w-100">';
        }

        function name_formatter(value, row, index) {
            return '<a href="'+ row.edit_url +'">'+ value +'</a>';
        }

        function status_formatter(value, row, index) {
            if (value == 1) {
                return '<span class="text-success">@lang('juzaweb::app.enabled')</span>';
            }
            return '<span class="text-danger">@lang('juzaweb::app.disabled')</span>';
        }

        var table = new JuzawebTable({
            url: '{{ route('admin.sliders.get-data') }}',
            action_url: '{{ route('admin.sliders.bulk-actions') }}',
        });
    </script>
@endsection