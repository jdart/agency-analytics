<?php

namespace App\Crawler;

use App\Jobs\ProcessCrawlerUrl;

class CrawlerStart
{
    public function __construct(
        protected CrawlerUrlRepository $crawlerUrlRepository
    ) {
    }

    public function __invoke(string $url): void
    {
        $url = rtrim($url, '/');
        $crawlerUrl = $this->crawlerUrlRepository->add($url);
        ProcessCrawlerUrl::dispatch($crawlerUrl);
    }
}
