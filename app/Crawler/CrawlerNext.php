<?php

namespace App\Crawler;

use App\Jobs\ProcessCrawlerUrl;

class CrawlerNext
{
    public function __construct(
        protected CrawlerUrlRepository $crawlerUrlRepository
    ) {
    }

    public function __invoke()
    {
        $maxToCrawl = config('app.max_crawled_urls', 1);
        $crawledCount = $this->crawlerUrlRepository->crawledCount();
        $crawlerUrl = $this->crawlerUrlRepository->getNext();
        if ($crawlerUrl && $crawledCount < $maxToCrawl) {
            ProcessCrawlerUrl::dispatch($crawlerUrl);
        }
    }
}
