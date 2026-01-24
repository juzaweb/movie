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

                @if($model->exists)
                    <a href="{{ admin_url('tv-series/' . $model->id . '/servers') }}" class="btn btn-info">
                        <i class="fas fa-file-video"></i> {{ __('movie::translation.manage_files') }}
                    </a>
                @endif
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-9">
                <x-card title="{{ __('movie::translation.information') }}">
                    {{ Field::text(__('movie::translation.name'), 'name', ['value' => $model->name, 'required' => true]) }}

                    {{ Field::text(__('movie::translation.origin_name'), 'origin_name', ['value' => $model->origin_name]) }}

                    {{ Field::slug(__('movie::translation.slug'), 'slug', ['value' => $model->slug]) }}

                    {{ Field::editor(__('movie::translation.content'), 'content', ['value' => $model->content, 'rows' => 5]) }}

                    <div class="row">
                        <div class="col-md-6">
                            {{ Field::number(__('movie::translation.current_episode'), 'current_episode', ['value' => $model->current_episode]) }}
                        </div>
                        <div class="col-md-6">
                            {{ Field::number(__('movie::translation.max_episode'), 'max_episode', ['value' => $model->max_episode]) }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            {{ Field::number(__('movie::translation.rating'), 'rating', ['value' => $model->rating, 'step' => '0.1', 'min' => '0', 'max' => '10']) }}
                        </div>
                        <div class="col-md-6">
                            {{ Field::number(__('movie::translation.year'), 'year', ['value' => $model->year]) }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            {{ Field::date(__('movie::translation.release_date'), 'release', ['value' => $model->release?->format('Y-m-d')]) }}
                        </div>
                        <div class="col-md-6">
                            {{ Field::number(__('movie::translation.runtime'), 'runtime', ['value' => $model->runtime, 'placeholder' => 'e.g., 120']) }}
                        </div>
                    </div>

                    {{ Field::text(__('movie::translation.video_quality'), 'video_quality', ['value' => $model->video_quality, 'placeholder' => 'e.g., HD, 4K']) }}

                    {{ Field::text(__('movie::translation.trailer_link'), 'trailer_link', ['value' => $model->trailer_link]) }}
                </x-card>
            </div>

            <div class="col-md-3">
                <x-language-card :label="$model" :locale="$locale"/>

                <x-card>
                    {!! Field::select($model, 'status', ['label' => __('movie::translation.status'), 'value' => $model->status?->value])
                            ->dropDownList(\Juzaweb\Modules\Core\Enums\PostStatus::all()) !!}
                </x-card>

                <x-card title="{{ __('movie::translation.genres') }}">
                    <div class="scrollable-list">
                        @component('core::components.categories-checkbox', [
                            'categories' => $genres,
                            'selectedCategories' => $model->genres->pluck('id')->toArray(),
                            'showQuickAdd' => true,
                            'storeUrl' => admin_url('movie-genres'),
                            'locale' => $locale,
                            'name' => 'genres[]',
                            'level' => 0,
                            'hasParent' => false,
                        ])
                        @endcomponent
                    </div>
                </x-card>

                <x-card title="{{ __('movie::translation.countries') }}">
                    <div class="scrollable-list">
                        @component('core::components.categories-checkbox', [
                            'categories' => $countries,
                            'selectedCategories' => $model->countries->pluck('id')->toArray(),
                            'showQuickAdd' => true,
                            'storeUrl' => admin_url('movie-countries'),
                            'locale' => $locale,
                            'name' => 'countries[]',
                            'level' => 0,
                            'hasParent' => false,
                        ])
                        @endcomponent
                    </div>
                </x-card>

                <x-card title="{{ __('movie::translation.relationships') }}">
                    {{ Field::tags(__('movie::translation.actors'), 'actors[]', ['value' => $model->actors->pluck('id')->toArray()])
                       ->loadDataModel(\Juzaweb\Modules\Movie\Models\Actor::class)
                       ->dropDownList($model->actors->pluck('name', 'id')->toArray()) }}

                    {{ Field::tags(__('movie::translation.directors'), 'directors[]', ['value' => $model->directors->pluck('id')->toArray()])
                       ->loadDataModel(\Juzaweb\Modules\Movie\Models\Director::class)
                       ->dropDownList($model->directors->pluck('name', 'id')->toArray()) }}

                    {{ Field::tags(__('movie::translation.writers'), 'writers[]', ['value' => $model->writers->pluck('id')->toArray()])
                       ->loadDataModel(\Juzaweb\Modules\Movie\Models\Writer::class)
                       ->dropDownList($model->writers->pluck('name', 'id')->toArray()) }}
                </x-card>

                <x-card title="{{ __('movie::translation.images') }}">
                    {{ Field::image(__('movie::translation.thumbnail'), 'thumbnail', ['value' => $model->thumbnail]) }}

                    {{ Field::image(__('movie::translation.poster'), 'poster', ['value' => $model->poster]) }}
                </x-card>
            </div>
        </div>
    </form>

@endsection

@section('scripts')

@endsection
