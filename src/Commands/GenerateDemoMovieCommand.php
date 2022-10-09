<?php

namespace Juzaweb\Movie\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Juzaweb\Backend\Models\PostMeta;
use Juzaweb\Movie\Helpers\TmdbApi;
use Juzaweb\Movie\Helpers\TmdbImport;

class GenerateDemoMovieCommand extends Command
{
    protected $signature = 'movie:generate-demo-movie';

    public function handle(): int
    {
        $api = new TmdbApi();
        $api->setAPIKey(get_config('tmdb_api_key'));

        $movies = $api->getPopularMovies();
        foreach ($movies as $movie) {
            $id = Arr::get($movie, 'id');

            if (PostMeta::where('meta_key', '=', 'tmdb_id')->where('meta_value', '=', $id)->exists()) {
                continue;
            }

            DB::beginTransaction();
            try {
                $model = TmdbImport::make()->import(
                    $id,
                    1
                );

                $this->info("Import success {$model->title}.");

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error($e->getMessage());
            }

            sleep(1);
        }

        $tvshows = $api->getPopularTVShows();
        foreach ($tvshows as $movie) {
            $id = Arr::get($movie, 'id');

            if (PostMeta::where('meta_key', '=', 'tmdb_id')->where('meta_value', '=', $id)->exists()) {
                continue;
            }

            DB::beginTransaction();
            try {
                $model = TmdbImport::make()->import(
                    Arr::get($movie, 'id'),
                    2
                );

                $this->info("Import success {$model->title}.");

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error($e->getMessage());
            }

            sleep(1);
        }

        return self::SUCCESS;
    }
}
