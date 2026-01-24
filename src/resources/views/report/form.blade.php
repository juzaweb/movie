@extends('core::layouts.admin')

@section('content')
    <form action="{{ $action }}" class="form-ajax" method="post">
        @if($model->exists)
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-md-12">
                <a href="{{ $backUrl }}" class="btn btn-warning">
                    <i class="fas fa-arrow-left"></i> {{ __('movie::translation.back') }}
                </a>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-9">
                <x-card title="{{ __('movie::translation.information') }}">
                    {{ Field::text(trans('movie::translation.report_type'), 'report_type_name', ['value' => $model->reportType->name ?? '', 'disabled' => true]) }}

					{{ Field::text(trans('movie::translation.reported_item'), 'reportable_name', ['value' => $model->reportable->title ?? $model->reportable->name ?? '', 'disabled' => true]) }}

					{{ Field::textarea(trans('movie::translation.description'), 'description', ['value' => $model->description, 'disabled' => true]) }}

					<div class="form-group">
                        <label class="col-form-label">{{ trans('movie::translation.meta') }}</label>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ trans('movie::translation.key') }}</th>
                                    <th>{{ trans('movie::translation.value') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($model->meta ?? [] as $key => $value)
                                    <tr>
                                        <td>{{ $key }}</td>
                                        <td>{{ is_string($value) ? $value : json_encode($value) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

					{{ Field::select(trans('movie::translation.status'), 'status', [
                        'value' => $model->status->value ?? $model->status,
                        'options' => \Juzaweb\Modules\Movie\Enums\ReportStatus::all(),
                    ]) }}
                </x-card>
            </div>

            <div class="col-md-3">

            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script type="text/javascript" nonce="{{ csp_script_nonce() }}">
        $(function () {
            //
        });
    </script>
@endsection
