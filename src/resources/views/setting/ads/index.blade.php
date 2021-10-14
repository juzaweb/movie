@extends('juzaweb::layouts.backend')

@section('content')

    <div class="row mb-3">
        <div class="col-md-12">
            <form method="get" class="form-inline" id="form-search">
                <div class="form-group mb-2 mr-1">
                    <label for="search" class="sr-only">@lang('juzaweb::app.search')</label>
                    <input name="search" type="text" id="search" class="form-control" placeholder="{{ trans('juzaweb::app.search') }}" autocomplete="off">
                </div>

                <button type="submit" class="btn btn-primary mb-2"><i class="fa fa-search"></i> @lang('juzaweb::app.search')</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive mb-5">
                <table class="table juzaweb-table">
                    <thead>
                        <tr>
                            <th  data-field="state" data-width="3%" data-checkbox="true"></th>
                            <th data-field="key" data-width="10%">@lang('juzaweb::app.code')</th>
                            <th data-field="name" data-formatter="name_formatter">@lang('juzaweb::app.name')</th>
                            <th data-field="status" data-width="15%" data-align="center" data-formatter="status_formatter">@lang('juzaweb::app.status')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script type="text/javascript">

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
            url: '{{ route('admin.setting.ads.get-data') }}',
        });
    </script>
@endsection