<?php

namespace Juzaweb\Modules\Movie\Http\Controllers;

use Juzaweb\Modules\Core\Contracts\Setting;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\Movie\Http\Requests\SettingRequest;

class SettingController extends AdminController
{
    public function index()
    {
        Breadcrumb::add(__('movie::translation.movie_settings'));

        return view('movie::setting.index');
    }

    public function update(SettingRequest $request)
    {
        app(Setting::class)->sets($request->safe()->all());

        return $this->success(__('movie::translation.setting_updated_successfully'));
    }
}
