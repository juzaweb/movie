<?php

namespace Juzaweb\Movie\Helpers;

use Juzaweb\Models\Taxonomy;
use Juzaweb\Movie\Models\Movie\Movie;
use Illuminate\Support\Str;
use Juzaweb\Support\FileManager;

class ImportMovie
{
    public $data;
    public $errors = [];
    
    public function __construct(array $data)
    {
        $fillData = [
            'name',
            'other_name',
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
        
        $model = Movie::create(array_merge($this->data, [
            'thumbnail' => FileManager::addFile($this->data['thumbnail']),
            'poster' => FileManager::addFile($this->data['poster']),
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

        return $model;
    }

    public function validate()
    {
        if (empty($this->data['name'])) {
            $this->errors[] = 'Name is required.';
        }
    
        if (empty($this->data['description'])) {
            $this->errors[] = 'Description is required.';
        }
    
        if (empty($this->data['thumbnail'])) {
            $this->errors[] = 'Thumbnail is required.';
        }
        
        if (empty($this->data['genres'])) {
            $this->errors[] = 'Genres is required.';
        }
        
        if ($this->data['tv_series'] === null) {
            $this->errors[] = 'TV Series is required.';
        }
        
        if (Movie::where('other_name', '=', $this->data['other_name'])
            ->where('year', '=', $this->data['year'])
            ->whereNotNull('other_name')
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
        $slug = Str::slug($name);

        $model = Taxonomy::firstOrCreate([
            'taxonomy' => $type,
            'slug' => $slug
        ], [
            'name' => $name
        ]);

        return $model->id;
    }
}
