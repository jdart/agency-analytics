<?php

namespace App\Crawler;

use App\Models\CrawlerImage;
use App\Models\CrawlerLink;
use Illuminate\Database\Eloquent\Collection;

class CrawlerStatsRepository
{
    public function uniqueImageCount(Collection $visited): int
    {
        return CrawlerImage::query()
            ->select('url')
            ->whereIn('url_id', $visited->pluck('id'))
            ->distinct()
            ->get()
            ->count();
    }

    public function uniqueInternalLinks(Collection $visited): int
    {
        return CrawlerLink::query()
            ->select('url')
            ->internal(true)
            ->whereIn('url_id', $visited->pluck('id'))
            ->distinct()
            ->get()
            ->count();
    }

    public function uniqueExternalLinks(Collection $visited): int
    {
        return CrawlerLink::query()
            ->select('url')
            ->internal(false)
            ->whereIn('url_id', $visited->pluck('id'))
            ->distinct()
            ->get()
            ->count();
    }

    public function averagePageload(Collection $visited): float
    {
        return $visited->avg('pageload_seconds');
    }

    public function averageWords(Collection $visited): int
    {
        return $visited->avg('words');
    }

    public function averageTitleLength(Collection $visited): int
    {
        return $visited->avg(fn ($url) => strlen($url->title));
    }
}
