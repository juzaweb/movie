<?php

namespace Juzaweb\Movie\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Juzaweb\Backend\Models\Post;
use Symfony\Component\Console\Input\InputOption;

class GenerateDemoVideoCommand extends Command
{
    protected $name = 'movie:generate-demo-video';

    public function handle()
    {
        $limit = $this->option('limit');

        $movies = Post::with(
            [
                'resources' => function ($q) {
                    $q->where('type', 'servers');
                    $q->orWhere('type', 'files');
                }
            ]
        )
            ->whereType('movies')
            ->where(
                function (Builder $q) {
                    $q->whereDoesntHave(
                        'resources',
                        function ($q2) {
                            $q2->where('type', 'servers');
                        }
                    );
                    $q->orWhereDoesntHave(
                        'resources',
                        function ($q2) {
                            $q2->where('type', 'files');
                        }
                    );
                }
            )
            ->orderBy('id', 'DESC')
        ->limit($limit)
        ->get();

        foreach ($movies as $movie) {
            $this->info("Generate movie {$movie->title}");

            DB::beginTransaction();
            try {
                if ($movie->getMeta('tv_series')) {
                    $this->updateForTvSeries($movie);
                } else {
                    $this->updateForMovies($movie);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
    }

    private function updateForMovies(Post $movie)
    {
        if (!$server = $movie->resources->where('type', 'servers')->first()) {
            $server = $movie->resources()->create(
                [
                    'name' => 'S1',
                    'type' => 'servers'
                ]
            );
        }

        $files = $movie->resources->where('type', 'files')
            ->map(
                function ($item) {
                    return $item->getMeta('url');
                }
            )
            ->toArray();

        $videos = $this->getVideos();

        foreach ($videos as $video) {
            if (in_array($video['url'], $files)) {
                continue;
            }

            $file = $movie->resources()->create(
                [
                    'name' => $video['name'],
                    'type' => 'files',
                    'parent_id' => $server->id
                ]
            );

            $file->setMeta('url', $video['url']);

            $file->setMeta('source', $video['source']);

            $this->info("-- Add file {$video['url']}");
        }
    }

    private function updateForTvSeries(Post $movie)
    {
        if (!$server = $movie->resources->where('type', 'servers')->first()) {
            $server = $movie->resources()->create(
                [
                    'name' => 'S1',
                    'type' => 'servers'
                ]
            );
        }

        $files = $movie->resources->where('type', 'files')
            ->map(
                function ($item) {
                    return $item->getMeta('url');
                }
            )
            ->toArray();

        $displayOrder = $movie->resources->where('type', 'files')->max('display_order') + 1;
        $videos = $this->getVideos();

        foreach ($videos as $video) {
            if (in_array($video['url'], $files)) {
                continue;
            }

            $file = $movie->resources()->create(
                [
                    'name' => $displayOrder,
                    'type' => 'files',
                    'display_order' => $displayOrder,
                    'parent_id' => $server->id
                ]
            );

            $file->setMeta('url', $video['url']);

            $file->setMeta('source', $video['source']);

            $displayOrder++;

            $this->info("-- Add file {$video['url']}");
        }
    }

    private function getVideos(): array
    {
        return [
            [
                'name' => 'mp4',
                'source' => 'mp4',
                'url' => 'https://cdn.juzaweb.com/storage/demo/demo.mp4',
            ],
            [
                'name' => 'hls',
                'source' => 'm3u8',
                'url' => 'https://cdn.juzaweb.com/storage/demo/demo.m3u8',
            ],
            [
                'name' => 'youtube',
                'source' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=kErrg42WLcg'
            ]
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['limit', null, InputOption::VALUE_OPTIONAL, 'The limit posts generate.', 20],
        ];
    }
}
