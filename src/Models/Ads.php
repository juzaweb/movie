<?php

namespace Juzaweb\Movie\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Juzaweb\Movie\Models\Ads
 *
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string|null $body
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Ads newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Ads newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Ads query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Ads whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Ads whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Ads whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Ads whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Ads whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Ads whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Juzaweb\Movie\Models\Ads whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Ads extends Model
{
    protected $table = 'ads';
    protected $primaryKey = 'id';
    protected $fillable = [
        'body',
        'status'
    ];
}
