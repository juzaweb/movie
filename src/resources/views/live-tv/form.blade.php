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
            <div class="col-md-9">
                <x-card title="{{ __('movie::translation.information') }}">
                    {{ Field::text(__('movie::translation.name'), 'name', ['value' => $model->name, 'required' => true]) }}

                    {{ Field::text(__('movie::translation.slug'), 'slug', ['value' => $model->slug]) }}

                    {{ Field::textarea(__('movie::translation.content'), 'content', ['value' => $model->content, 'rows' => 5]) }}

                    {{ Field::text(__('movie::translation.streaming_url'), 'streaming_url', ['value' => $model->streaming_url, 'required' => true]) }}
                </x-card>
            </div>

            <div class="col-md-3">
                <x-language-card :label="$model" :locale="$locale" />

                <x-card title="{{ __('movie::translation.thumbnail') }}">
                    {{ Field::image(__('movie::translation.thumbnail'), 'thumbnail', ['value' => $model->getThumbnailUrl()]) }}
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
