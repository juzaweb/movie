<?php

namespace Juzaweb\Movie\Http\Controllers\Backend;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\Movie\Helpers\TmdbImport;

class TmdbController extends BackendController
{
    public function addMovie(Request $request): JsonResponse|RedirectResponse
    {
        $this->validate(
            $request,
            [
                'tmdb' => 'required',
                'type' => 'required|in:1,2',
            ]
        );

        DB::beginTransaction();
        try {
            $model = TmdbImport::make()->import(
                $request->input('tmdb'),
                $request->input('type')
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }

        return $this->success(
            [
                'message' => trans('Import success.'),
                'redirect' => route(
                    'admin.posts.edit',
                    [
                        'movies',
                        $model->id
                    ]
                ),
            ]
        );
    }
}
