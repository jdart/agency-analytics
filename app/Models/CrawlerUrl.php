<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $url
 * @property ?string $title
 * @property ?int $pageload_seconds
 * @property int $words
 */
class CrawlerUrl extends Model
{
    use HasFactory;

    protected $fillable = ['url'];

    public function scopeVisited($query, $visited)
    {
        if ($visited) {
            return $query->where('pageload_seconds', '!=', null);
        } else {
            return $query->where('pageload_seconds', '=', null);
        }
    }

    public function images()
    {
        return $this->hasMany(CrawlerImage::class, 'url_id');
    }

    public function links()
    {
        return $this->hasMany(CrawlerLink::class, 'url_id');
    }
}
