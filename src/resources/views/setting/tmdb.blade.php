<form method="post" action="{{ route('admin.setting.save') }}" class="form-ajax">
    <input type="hidden" name="form" value="mymo">

    <div class="row">
        <div class="col-md-8">
            <h5>Imdb</h5>

            @component('cms::components.form_security', [
                'name' => 'tmdb_api_key',
                'value' => get_config('tmdb_api_key'),
                'label' => trans('mymo::app.tmdb_api_key'),
            ])
            @endcomponent

            <h5>Report</h5>
            {{ Field::select(
                trans('Allow to report movie'),
                 'mymo_movie_report',
                 [
                     'value' => get_config('mymo_movie_report', 1),
                     'options' => [
                         '0' => trans('cms::app.disabled'),
                         '1' => trans('cms::app.enabled'),
                     ]
                 ]
            ) }}

            <h5>Player</h5>

            @component('cms::components.form_input', [
                'name' => 'player_watermark',
                'value' => get_config('player_watermark'),
                'label' => trans('mymo::app.player_watermark'),
            ])
            @endcomponent

            @component('cms::components.form_image', [
                'name' => 'player_watermark_logo',
                'value' => get_config('player_watermark_logo'),
                'label' => trans('mymo::app.player_watermark_logo'),
            ])
            @endcomponent
        </div>

        <div class="col-md-4">
            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> {{ trans('cms::app.save') }}</button>
        </div>
    </div>

</form>
