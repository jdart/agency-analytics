<?php

namespace App\Crawler;

use App\Models\CrawlerUrl;
use Illuminate\Database\Eloquent\Collection;

class CrawlerUrlRepository
{
    public function add(string $url): ?CrawlerUrl
    {
        $crawlerUrl = CrawlerUrl::query()->where('url', $url)->first();
        if (!$crawlerUrl) {
            $crawlerUrl = CrawlerUrl::query()->create([
                'url' => $url,
            ]);
        }
        return $crawlerUrl;
    }

    public function crawledCount(): int
    {
        return CrawlerUrl::query()->visited(true)->count();
    }

    public function getNext(): ?CrawlerUrl
    {
        return CrawlerUrl::query()->visited(false)
            ->orderBy('id', 'asc')
            ->first();
    }

    public function all(): Collection
    {
        return CrawlerUrl::query()->get();
    }

    public function visited(): Collection
    {
        return CrawlerUrl::query()->visited(true)->get();
    }

    public function uniqueInternalLinks(Collection $visited)
    {
    }
}
