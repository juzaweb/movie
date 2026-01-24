<?php

namespace Juzaweb\Modules\Movie\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\Movie\Http\DataTables\CountriesDataTable;
use Juzaweb\Modules\Movie\Http\Requests\CountryRequest;
use Juzaweb\Modules\Movie\Models\Country;

class CountryController extends AdminController
{
    public function index(CountriesDataTable $dataTable)
    {
        Breadcrumb::add(__('movie::translation.movie_countries'));

        return $dataTable->render('movie::admin.country.index');
    }

    public function create()
    {
        Breadcrumb::add(__('movie::translation.movie_countries'), action([self::class, 'index']));

        Breadcrumb::add(__('movie::translation.create_new_country'));

        $locale = $this->getFormLanguage();

        return view(
            'movie::admin.country.form',
            [
                'model' => new Country(),
                'action' => action([self::class, 'store']),
                'locale' => $locale,
            ]
        );
    }

    public function edit(string $id)
    {
        $locale = $this->getFormLanguage();
        $model = Country::withTranslation($locale)->findOrFail($id);
        $model->setDefaultLocale($locale);

        Breadcrumb::add(__('movie::translation.movie_countries'), action([self::class, 'index']));

        Breadcrumb::add(__('movie::translation.edit_country_name', ['name' => $model->name]));

        return view(
            'movie::admin.country.form',
            [
                'model' => $model,
                'action' => action([self::class, 'update'], [$id]),
                'locale' => $locale,
            ]
        );
    }

    public function store(CountryRequest $request)
    {
        $locale = $this->getFormLanguage();
        $data = $request->validated();

        $country = DB::transaction(function () use ($data, $locale) {
            $model = new Country();
            $model->setDefaultLocale($locale);
            $model->fill($data);
            $model->save();
            return $model;
        });

        return $this->success(
            [
                'message' => __('movie::translation.country_created_successfully'),
                // 'redirect' => action([self::class, 'index']),
                'data' => [
                    'id' => $country->id,
                    'name' => $country->name,
                ],
            ]
        );
    }

    public function update(CountryRequest $request, string $id)
    {
        $locale = $this->getFormLanguage();
        $data = $request->validated();
        $country = Country::findOrFail($id);
        $country->setDefaultLocale($locale);

        DB::transaction(fn() => $country->update($data));

        return $this->success(
            [
                'message' => __('movie::translation.country_updated_successfully'),
                'redirect' => action([self::class, 'index']),
            ]
        );
    }

    public function bulk(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if ($action == 'delete') {
            Country::whereIn('id', $ids)
                ->get()
                ->each
                ->delete();
        }

        return $this->success(
            [
                'message' => __('movie::translation.country_updated_successfully'),
            ]
        );
    }
}
