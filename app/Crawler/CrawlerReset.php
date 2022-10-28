<?php

namespace App\Crawler;

use App\Models\CrawlerImage;
use App\Models\CrawlerLink;
use App\Models\CrawlerUrl;
use Illuminate\Support\Facades\DB;

class CrawlerReset
{
    public function __invoke()
    {
        DB::statement('truncate jobs');
        CrawlerLink::query()->truncate();
        CrawlerImage::query()->truncate();
        CrawlerUrl::query()->truncate();
    }
}
