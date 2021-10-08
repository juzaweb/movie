<?php
/**
 * JUZAWEB CMS - The Best CMS for Laravel Project
 *
 * @package    juzaweb/laravel-cms
 * @author     The Anh Dang <dangtheanh16@gmail.com>
 * @link       https://juzaweb.com/cms
 * @license    MIT
 */

namespace Juzaweb\Movie\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Juzaweb\Http\Controllers\Controller;
use Juzaweb\Movie\Models\DownloadLink;
use Juzaweb\Movie\Models\Movie\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Juzaweb\Movie\Models\Movie\MovieViews;
use Juzaweb\Movie\Models\Video\VideoAds;
use Juzaweb\Models\Taxonomy;
use Juzaweb\Movie\Models\Movie\MovieRating;
use Juzaweb\Movie\Models\Video\VideoFile;

class AjaxController extends Controller
{
    public function getFilterForm()
    {
        $genres = Taxonomy::where('taxonomy', '=', 'genres')
            ->get(['id', 'name']);
        $countries = Taxonomy::where('taxonomy', '=', 'countries')
            ->get(['id', 'name']);
        $years = Taxonomy::where('taxonomy', '=', 'years')
            ->get(['id', 'name']);

        return view('theme::components.filter_form', [
            'genres' => $genres,
            'countries' => $countries,
            'years' => $years,
        ]);
    }

    public function getMoviesByGenre()
    {
        $genre = request()->get('cat_id');
        $showpost = request()->get('showpost', 12);

        $query = Movie::select([
            'id',
            'title',
            'origin_title',
            'description',
            'thumbnail',
            'slug',
            'views',
            'video_quality',
            'year',
            'tv_series',
            'current_episode',
            'max_episode',
        ]);

        $query->wherePublish();
        $query->whereTaxonomy($genre);
        $query->limit($showpost);

        return view('data.movies_by_genre', [
            'items' => $query->get()
        ]);
    }

    public function getPopularMovies()
    {
        $type = request()->get('type');
        $items = $this->getPopular($type);

        foreach ($items as $item) {
            $item->url = $item->getLink();
            $item->thumbnail = $item->getThumbnail();
            $item->views = $item->views .' '. trans('juzaweb::app.views');
            if (empty($item->origin_title)) {
                $item->origin_title = '';
            }
        }

        return response()->json([
            'items' => $items
        ]);
    }

    public function getPlayer()
    {
        $slug = request()->post('slug');
        $vid = request()->post('vid');

        $movie = Movie::createFrontendBuilder()
            ->where('slug', '=', $slug)
            ->firstOrFail();

        if (get_config('only_member_view') == 1) {
            if (!Auth::check()) {
                $file = new VideoFile();
                $file->source = 'embed';
                $files[] = (object) ['file' => route('watch.no-view')];

                return response()->json([
                    'data' => [
                        'status' => true,
                        'sources' => view('theme::components.player_script', [
                            'movie' => $movie,
                            'file' => $file,
                            'files' => $files,
                        ])->render(),
                    ]
                ]);
            }
        }

        $file = VideoFile::find($vid);

        if ($file) {
            $files = $file->getFiles();

            $ads_exists = VideoAds::where('status', 1)->exists();
            return response()->json([
                'data' => [
                    'status' => true,
                    'sources' => view('theme::components.player_script', [
                        'movie' => $movie,
                        'file' => $file,
                        'files' => $files,
                        'ads_exists' => $ads_exists,
                    ])->render(),
                ]
            ]);
        }

        return response()->json([
            'data' => [
                'status' => false,
            ]
        ]);
    }

    public function setMovieView()
    {
        $slug = request()->post('slug');
        $movie = Movie::createFrontendBuilder()
            ->where('slug', '=', $slug)
            ->firstOrFail(['id', 'views']);

        $views = $movie->views;
        $viewed = Cookie::get('viewed');

        if ($viewed) {
            $viewed = json_decode($viewed, true);

            if (in_array($movie->id, $viewed)) {
                return response()->json([
                    'view' => $views,
                ]);
            }
        }

        if (empty($viewed)) {
            $viewed = [];
        }

        $views = $movie->views + 1;
        $this->setView($movie->id);

        $viewed[] = $movie->id;
        Cookie::queue('viewed', json_encode($viewed), 1440);

        Movie::where('id', '=', $movie->id)
            ->update([
                'views' => $views
            ]);

        return response()->json([
            'view' => $views,
        ]);
    }

    public function download(Request $request)
    {
        $link = $request->input('link');
        $download = DownloadLink::find($link);
        if (empty($download) || $download->status != 1) {
            return abort(404);
        }

        return redirect()->to($download->url);
    }

    public function setRating()
    {
        $movie = request()->post('movie');
        $movie = Movie::createFrontendBuilder()
            ->where('id', '=', $movie)
            ->firstOrFail(['id']);

        $start = request()->post('value');
        if (empty($start)) {
            return response()->json([
                'status' => 'error',
            ]);
        }

        $client_ip = get_client_ip();

        $model = MovieRating::firstOrNew([
            'movie_id' => $movie->id,
            'client_ip' => $client_ip,
        ]);

        $model->movie_id = $movie->id;
        $model->client_ip = $client_ip;
        $model->start = $start;
        $model->save();

        return $movie->getStarRating();
    }

    public function ads() {
        $video_ads = VideoAds::where('status', '=', 1)
            ->inRandomOrder()
            ->first();

        if (empty($video_ads)) {
            $factory = new \Sokil\Vast\Factory();
            $document = $factory->create('2.0');
            $document->toDomDocument();
            return $document;
        }

        return $this->getAds($video_ads);
    }

    protected function getAds(VideoAds $video_ads) {
        $factory = new \Sokil\Vast\Factory();
        $document = $factory->create('2.0');

        $ad1 = $document
            ->createInLineAdSection()
            ->setId('ad1')
            ->setAdSystem($video_ads->name)
            ->setAdTitle($video_ads->title)
            ->addImpression('http://ad.server.com/impression', 'imp1');

        $linearCreative = $ad1
            ->createLinearCreative()
            ->setDuration(1)
            ->setId('013d876d-14fc-49a2-aefd-744fce68365b')
            ->setAdId('pre')
            ->setVideoClicksClickThrough('http://entertainmentserver.com/landing')
            ->addVideoClicksClickTracking('http://ad.server.com/videoclicks/clicktracking')
            ->addVideoClicksCustomClick('http://ad.server.com/videoclicks/customclick')
            ->addTrackingEvent('start', 'http://ad.server.com/trackingevent/start')
            ->addTrackingEvent('pause', 'http://ad.server.com/trackingevent/stop');

        $linearCreative
            ->createClosedCaptionFile()
            ->setLanguage('en-US')
            ->setType('text/srt')
            ->setUrl('http://server.com/cc.srt');

        $linearCreative
            ->createMediaFile()
            ->setProgressiveDelivery()
            ->setType('video/mp4')
            ->setHeight(100)
            ->setWidth(100)
            ->setBitrate(2500)
            ->setUrl(upload_url($video_ads->getVideoUrl()));

        $document->toDomDocument();
        return $document;
    }

    protected function getPopular($type)
    {
        $query = Movie::select([
            'id',
            'title',
            'origin_title',
            'description',
            'thumbnail',
            'slug',
            'views',
            'year',
        ])
            ->wherePublish();

        if ($type == 'day' || $type == 'month') {
            switch ($type) {
                case 'day': $date = date('Y-m-d');break;
                case 'month': $date = date('Y-m');break;
                default: $date = date('Y-m-d');break;
            }

            $query->whereIn('id', function ($builder) use ($date) {
                $builder->select(['movie_id'])
                    ->from('movie_views')
                    ->where('day', 'like', $date . '%')
                    ->orderBy('views', 'desc');
            });
        }

        if ($type == 'week') {
            $day = date('w');
            $week_start = date('Y-m-d', strtotime('-'. $day .' days'));
            $week_end = date('Y-m-d', strtotime('+'. (6-$day) .' days'));

            $query->whereIn('id', function ($builder) use ($week_start, $week_end) {
                $builder->select(['movie_id'])
                    ->from('movie_views')
                    ->where('day', '>=', $week_start)
                    ->where('day', '<=', $week_end)
                    ->orderBy('views', 'desc');
            });
        }

        $query->orderBy('views', 'DESC');

        $query->limit(10);
        return $query->get();
    }

    protected function setView($movie_id)
    {
        $model = MovieViews::firstOrNew([
            'movie_id' => $movie_id,
            'day' => date('Y-m-d'),
        ]);

        $model->movie_id = $movie_id;
        $model->views = empty($model->views) ? 1 : $model->views + 1;
        $model->day = date('Y-m-d');
        return $model->save();
    }
}
