<?php
/**
 * JUZAWEB CMS - The Best CMS for Laravel Project
 *
 * @package    juzaweb/juzacms
 * @author     The Anh Dang <dangtheanh16@gmail.com>
 * @link       https://juzaweb.com/cms
 * @license    MIT
 */

namespace Juzaweb\Movie\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Juzaweb\AdsManager\Models\VideoAds;
use Juzaweb\Backend\Http\Resources\PostResourceCollection;
use Juzaweb\Backend\Http\Resources\ResourceResource;
use Juzaweb\Backend\Http\Resources\TaxonomyResource;
use Juzaweb\Backend\Models\Post;
use Juzaweb\Backend\Models\Resource;
use Juzaweb\Backend\Models\Taxonomy;
use Juzaweb\Backend\Repositories\PostRepository;
use Juzaweb\Backend\Repositories\ResourceRepository;
use Juzaweb\CMS\Facades\Plugin;
use Juzaweb\CMS\Http\Controllers\Controller;
use Juzaweb\CMS\Traits\ResponseMessage;
use Juzaweb\Movie\Helpers\VideoFile;
use Juzaweb\Movie\Http\Requests\ReportRequest;
use TwigBridge\Facade\Twig;

class AjaxController extends Controller
{
    use ResponseMessage;

    public function __construct(
        protected ResourceRepository $resourceRepository,
        protected PostRepository $postRepository
    ) {
    }

    public function getFilterForm(Request $request): string
    {
        $genres = Taxonomy::where(
            'taxonomy',
            '=',
            'genres'
        )
            ->get();
        $countries = Taxonomy::where('taxonomy', '=', 'countries')
            ->get();
        $years = Taxonomy::where('taxonomy', '=', 'years')
            ->get();

        return Twig::render(
            'theme::components.filter_form',
            [
                'genres' => TaxonomyResource::collection($genres)->toArray($request),
                'countries' => TaxonomyResource::collection($countries)->toArray($request),
                'years' => TaxonomyResource::collection($years)->toArray($request),
            ]
        );
    }

    public function getMoviesByGenre(Request $request): string
    {
        $genre = $request->get('cat_id');
        $showpost = $request->get('showpost', 12);

        if ($showpost > 20) {
            $showpost = 12;
        }

        $query = Post::selectFrontendBuilder();
        $query->whereTaxonomy($genre);
        $query->limit($showpost);

        $posts = PostResourceCollection::make($query->get())
            ->toArray($request);

        return Twig::render(
            'theme::components.movies_by_genre',
            [
                'items' => $posts
            ]
        );
    }

    public function getPopularMovies(Request $request): JsonResponse
    {
        $type = $request->get('type');
        $items = $this->getPopular($type);

        foreach ($items as $item) {
            $item->url = $item->getLink();
            $item->thumbnail = $item->getThumbnail();
            $item->views = $item->views .' '. trans('cms::app.views');
            $item->origin_title = (string) $item->getMeta('origin_title');
            $item->year = $item->getMeta('year');
        }

        return response()->json(
            [
                'items' => $items
            ]
        );
    }

    public function getPlayer(Request $request): JsonResponse
    {
        $slug = $request->post('slug');
        $vid = $request->post('vid');

        $movie = Post::createFrontendBuilder()
            ->where('slug', '=', $slug)
            ->firstOrFail();
        $tracks = [];

        if (get_config('only_member_view') == 1) {
            if (!Auth::check()) {
                $file = new \stdClass();
                $file->source = 'embed';
                $files[] = (object) ['file' => route('watch.no-view')];

                return response()->json(
                    [
                        'data' => [
                            'status' => true,
                            'sources' => Twig::render(
                                'theme::components.player_script',
                                [
                                    'movie' => $movie,
                                    'file' => $file,
                                    'files' => $files,
                                    'tracks' => $tracks,
                                ]
                            ),
                        ]
                    ]
                );
            }
        }

        $video = Resource::find($vid);

        if ($video) {
            $files = (new VideoFile())->getFiles($video);
            $ads = false;

            if (Plugin::isEnabled('juzaweb/ads-manager')) {
                $ads = VideoAds::where(['position' => 'movie'])->get()
                    ->unique('offset')
                    ->mapWithKeys(
                        function ($item, $index) {
                            return [
                                "ad{$index}" => [
                                    'offset' => $item->offset,
                                    'skipoffset' => $item->options['skipoffset'] ?? 5,
                                    'tag' => route('ajax', ['video-ads']) ."/?position=movie&id={$item->id}",
                                ]
                            ];
                        }
                    )
                ->toArray();
            }

            $tracks = Resource::where('type', '=', 'subtitles')
                ->where('parent_id', '=', $video->id)
                ->wherePublish()
                ->get()
                ->map(
                    function ($item) {
                        return [
                            'kind' => 'captions',
                            'file' => upload_url($item->getMeta('url')),
                            'label' => $item->name,
                        ];
                    }
                )
                ->toArray();
            $video = (new ResourceResource($video))->toArray($request);

            return response()->json(
                [
                    'data' => [
                        'status' => true,
                        'sources' => Twig::render(
                            'theme::components.player_script',
                            compact(
                                'video',
                                'files',
                                'ads',
                                'movie',
                                'tracks'
                            )
                        ),
                    ]
                ]
            );
        }

        return response()->json(
            [
                'data' => [
                    'status' => false,
                ]
            ]
        );
    }

    public function download(Request $request): RedirectResponse
    {
        $link = $request->input('link');
        $download = Resource::find($link);
        if (empty($download) || $download->status != 'publish') {
            abort(404);
        }

        if (!$url = $download->getMeta('url')) {
            abort(404);
        }

        return redirect()->to($url);
    }

    public function report(ReportRequest $request): JsonResponse
    {
        $post = $this->postRepository->find($request->input('post_id'));

        $data = $request->only(['description', 'type', 'post_id']);
        $data['type'] = 'movie-reports';
        $data['name'] = 'Report movie '. $post->title;

        $this->resourceRepository->create($data);

        return $this->success('Send Report success');
    }

    protected function getPopular($type): Collection
    {
        $query = Post::selectFrontendBuilder();
        $query->where(['type' => 'movies']);

        if ($type == 'day' || $type == 'month') {
            $date = match ($type) {
                'month' => date('Y-m'),
                default => date('Y-m-d'),
            };

            $query->whereHas(
                'postViews',
                function (Builder $q) use ($date) {
                    $q->where('day', 'like', $date . '%');
                    $q->orderBy('views', 'desc');
                }
            );
        }

        if ($type == 'week') {
            $day = date('w');
            $week_start = date('Y-m-d', strtotime('-'. $day .' days'));
            $week_end = date('Y-m-d', strtotime('+'. (6-$day) .' days'));

            $query->whereHas(
                'postViews',
                function (Builder $q) use ($week_start, $week_end) {
                    $q->where('day', '>=', $week_start);
                    $q->where('day', '<=', $week_end);
                    $q->orderBy('views', 'desc');
                }
            );
        }

        $query->orderBy('views', 'DESC');

        $query->limit(10);

        return $query->get();
    }
}
