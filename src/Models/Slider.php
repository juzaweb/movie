<?php

namespace Juzaweb\Movie\Models;

use Illuminate\Database\Eloquent\Model;
use Juzaweb\Traits\ResourceModel;

/**
 * Juzaweb\Movie\Models\Slider
 *
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Slider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Slider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Slider query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Slider whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Slider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Slider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Slider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Slider whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Slider extends Model
{
    use ResourceModel;

    protected $fieldName = 'name';
    protected $table = 'sliders';
    protected $fillable = [
        'name',
        'content'
    ];

    protected $casts = [
        'content' => 'array'
    ];
}
