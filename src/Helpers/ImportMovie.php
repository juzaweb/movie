<?php

namespace Juzaweb\Movie\Helpers;

use Juzaweb\Models\Taxonomy;
use Juzaweb\Movie\Models\Movie\Movie;
use Illuminate\Support\Str;
use Juzaweb\Support\FileManager;
use Illuminate\Support\Facades\DB;

class ImportMovie
{
    public $data;
    public $errors = [];
    
    public function __construct(array $data)
    {
        $fillData = [
            'title',
            'origin_title',
            'description',
            'content',
            'type_id',
            'poster',
            'rating',
            'release',
            'runtime',
            'video_quality',
            'trailer_link',
            'current_episode',
            'max_episode',
            'year',
            'thumbnail',
            'poster',
            'tv_series',
        ];
        
        $arrayData = [
            'genres',
            'countries',
            'actors',
            'writers',
            'directors',
            'tags'
        ];
        
        foreach ($fillData as $item) {
            if (!isset($data[$item])) {
                $data[$item] = null;
            }
            else {
                $data[$item] = trim($data[$item]);
            }
        }
    
        foreach ($arrayData as $item) {
            if (!isset($data[$item])) {
                $data[$item] = [];
            }
        }
        
        $this->data = $data;
    }
    
    /**
     * Save import movie.
     *
     * @return Movie|false
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        DB::beginTransaction();

        try {
            $model = Movie::create(array_merge($this->data, [
                'thumbnail' => FileManager::addFile($this->data['thumbnail'])->path,
                'poster' => FileManager::addFile($this->data['poster'])->path,
                'tv_series' => $this->data['tv_series'] ? 1 : 0,
                'video_quality' => $this->data['video_quality'] ?? 'HD',
                'year' => explode('-', $this->data['release'] ?? '')[0],
                'status' => 'publish',
            ]));

            $model->syncTaxonomies([
                'genres' => $this->getTaxonomyIds($this->data['genres'], 'genres'),
                'countries' => $this->getTaxonomyIds($this->data['countries'], 'countries'),
                'actors' => $this->getTaxonomyIds($this->data['actors'], 'actors'),
                'writers' => $this->getTaxonomyIds($this->data['writers'], 'writers'),
                'directors' => $this->getTaxonomyIds($this->data['directors'], 'directors'),
                'tags' => $this->getTaxonomyIds($this->data['tags'], 'tags'),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }


        return $model;
    }

    public function validate()
    {
        if (empty($this->data['title'])) {
            $this->errors[] = 'Title is required.';
        }
    
        if (empty($this->data['content'])) {
            $this->errors[] = 'Content is required.';
        }
    
        if (empty($this->data['thumbnail'])) {
            $this->errors[] = 'Thumbnail is required.';
        }
        
        if (empty($this->data['genres'])) {
            $this->errors[] = 'Genres is required.';
        }
        
        if (is_null($this->data['tv_series'])) {
            $this->errors[] = 'TV Series is required.';
        }

        if (Movie::where('origin_title', '=', $this->data['origin_title'])
            ->where('year', '=', $this->data['year'])
            ->whereNotNull('origin_title')
            ->whereNotNull('year')
            ->exists()) {
            $this->errors[] = 'Movie is exists.';
        }
        
        if (count($this->errors) > 0) {
            return false;
        }
        
        return true;
    }
    
    protected function getTaxonomyIds($genres, $type)
    {
        if (is_string($genres)) {
            return $genres;
        }
        
        $result = [];
        foreach ($genres as $genre) {
            if ($genre['name']) {
                $result[] = $this->addOrGetTaxonomy($genre['name'], $type);
            }
        }

        return $result;
    }

    protected function addOrGetTaxonomy($name, $type)
    {
        $name = trim($name);

        $model = Taxonomy::firstOrCreate([
            'taxonomy' => $type,
            'name' => $name,
            'post_type' => 'movies'
        ]);

        return $model->id;
    }
}
