<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $url_id
 * @property string $url
 * @property bool $internal
 */
class CrawlerLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'internal',
        'url',
    ];

    public function scopeInternal($query, $value)
    {
        return $query->where('internal', $value);
    }
}
