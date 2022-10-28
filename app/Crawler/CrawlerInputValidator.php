<?php

namespace App\Crawler;

class CrawlerInputValidator
{
    public function __invoke(mixed $url): bool
    {
        assert(is_string($url));
        $parts = parse_url($url);
        return is_array($parts);
    }
}
