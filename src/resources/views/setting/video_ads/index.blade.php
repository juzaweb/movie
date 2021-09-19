@extends('juzaweb::layouts.backend')

@section('title', trans('juzaweb::app.video_ads'))

@section('content')

{{ Breadcrumbs::render('manager', [
        'name' => trans('juzaweb::app.video_ads'),
        'url' => route('admin.setting.video_ads')
    ]) }}

<div class="mymo__utils__content">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-0 card-title font-weight-bold">@lang('juzaweb::app.video_ads')</h5>
                </div>

                <div class="col-md-6">
                    <div class="float-right">
                        <div class="btn-group">

                        </div>

                        <div class="btn-group">
                            <a href="{{ route('admin.setting.video_ads.create') }}" class="btn btn-success"><i class="fa fa-plus-circle"></i> @lang('juzaweb::app.add_new')</a>
                            <button type="button" class="btn btn-danger" id="delete-item"><i class="fa fa-trash"></i> @lang('juzaweb::app.delete')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-12">
                    <form method="get" class="form-inline" id="form-search">

                        <div class="form-group mb-2 mr-1">
                            <label for="inputName" class="sr-only">@lang('juzaweb::app.search')</label>
                            <input name="search" type="text" id="inputName" class="form-control" placeholder="@lang('juzaweb::app.search')" autocomplete="off">
                        </div>

                        <div class="form-group mb-2 mr-1">
                            <label for="inputStatus" class="sr-only">@lang('juzaweb::app.status')</label>
                            <select name="status" id="inputStatus" class="form-control">
                                <option value="1">@lang('juzaweb::app.enabled')</option>
                                <option value="0">@lang('juzaweb::app.disabled')</option>
                            </select>
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
                            <th data-width="15%" data-field="created">@lang('juzaweb::app.created_at')</th>
                            <th data-width="15%" data-field="status" data-align="center" data-formatter="status_formatter">@lang('juzaweb::app.status')</th>
                        </tr>
                    </thead>
                </table>
            </div>
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
            url: '{{ route('admin.setting.video_ads.getdata') }}',
            remove_url: '{{ route('admin.setting.video_ads.remove') }}',
        });
    </script>
@endsection