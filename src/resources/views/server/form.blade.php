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

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('movie::translation.save') }}
                </button>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <x-card title="{{ __('movie::translation.information') }}">
                    {{ Field::text(__('movie::translation.name'), 'name', ['value' => $model->name, 'required' => true]) }}

                    <div class="row">
                        <div class="col-md-6">
                            {{ Field::number(__('movie::translation.display_order'), 'display_order', ['value' => $model->display_order ?? 1, 'min' => '0']) }}
                        </div>
                        <div class="col-md-6">
                            {{ Field::checkbox(__('movie::translation.active'), 'active', ['value' => $model->active ?? true]) }}
                        </div>
                    </div>
                </x-card>
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
