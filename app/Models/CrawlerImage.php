<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $url_id
 * @property string $url
*/
class CrawlerImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'url'
    ];
}
