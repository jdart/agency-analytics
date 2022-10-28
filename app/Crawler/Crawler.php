<?php

namespace App\Crawler;

use App\Models\CrawlerImage;
use App\Models\CrawlerLink;
use App\Models\CrawlerUrl;
use Illuminate\Support\Collection;

class Crawler
{
    public function __construct(
        protected CrawlerUrlRepository $crawlerUrlRepository
    ) {
    }

    protected function getDomainFromUrl(string $url, ?string $default = null): ?string
    {
        $urlParts = parse_url($url);
        return $urlParts['host'] ?? $default;
    }

    protected function normalizeUrl(string $baseDomain, string $url): string
    {
        $domain = $this->getDomainFromUrl($url, null);
        if (is_string($domain)) {
            $result = $url;
        } else {
            $result = 'https://'.$baseDomain.$url;
        }
        $hash_pos = strpos($result, '#');
        if (is_int($hash_pos)) {
            $result = substr($result, 0, $hash_pos);
        }
        return rtrim($result, '/');
    }

    protected function urlHasSameDomain(string $domain, string $href)
    {
        $hrefDomain = $this->getDomainFromUrl($href, $domain);
        return strtolower($hrefDomain) === strtolower($domain);
    }

    protected function download(CrawlerUrl $crawlerUrl): void
    {
        $fp = fopen(config('app.tmp_file'), 'w+');
        $ch = curl_init($crawlerUrl->url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }

    public function __invoke(CrawlerUrl $crawlerUrl): bool
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $timeBefore = microtime(true);
        try {
            $this->download($crawlerUrl);
            $timeAfter = microtime(true);
            $dom->loadHTMLFile(config('app.tmp_file'));
        } catch (\Throwable $e) {
            return false;
        }
        $crawlerUrl->pageload_seconds = $timeAfter - $timeBefore;
        $domain = $this->getDomainFromUrl($crawlerUrl->url);
        $title = $dom->getElementsByTagName('title')[0];

        if ($title) {
            $crawlerUrl->title = $title->textContent;
        }
        $this->links($dom, $domain, $crawlerUrl);
        $this->images($dom, $domain, $crawlerUrl);
        $this->text($dom, $crawlerUrl);
        $crawlerUrl->save();
        return true;
    }

    protected function links(\DOMDocument $dom, string $domain, CrawlerUrl $crawlerUrl): void
    {
        $links = $dom->getElementsByTagName('a');
        $newLinks = [];
        foreach ($links as $link) {
            $linkHref = $link->getAttribute('href');
            if (!is_string($linkHref)) {
                continue;
            }
            $linkHref = $this->normalizeUrl($domain, $linkHref);
            $internal = $this->urlHasSameDomain($domain, $linkHref);
            $newLinks[$linkHref] = new CrawlerLink(['internal' => $internal, 'url' => $linkHref]);
            if ($internal) {
                $this->crawlerUrlRepository->add($linkHref);
            }
        }
        $crawlerUrl->links()->saveMany(array_values($newLinks));
    }

    protected function images(\DOMDocument $dom, string $domain, CrawlerUrl $crawlerUrl): void
    {
        $images = $dom->getElementsByTagName('img');
        $newImages = [];
        foreach ($images as $img) {
            $imgSrc = $img->getAttribute('src');
            if (!is_string($imgSrc)) {
                continue;
            }
            $imgSrc = $this->normalizeUrl($domain, $imgSrc);
            $newImages[$imgSrc] = new CrawlerImage(['url' => $imgSrc]);
        }
        $crawlerUrl->images()->saveMany(array_values($newImages));
    }

    protected function text(\DOMDocument $dom, CrawlerUrl $crawlerUrl): void
    {
        $body = $dom->getElementsByTagName('body')[0];
        $words = collect([]);
        $this->collectText($body, $words);
        $crawlerUrl->words = $words->count();
    }

    protected function collectText(\DOMNode $node, Collection $result): void
    {
        if ($node->childNodes->length === 0) {
            $textContent = $node->textContent ?? '';
            foreach (explode(" ", $textContent) as $text) {
                $text = trim($text);
                if (strlen($text) > 0) {
                    $result->add($text);
                }
            }
        }

        /** @var \DOMNode $childNode */
        foreach ($node->childNodes as $childNode) {
            if (in_array($childNode->nodeName, ['script', 'style'])) {
                continue;
            }
            $this->collectText($childNode, $result);
        }
    }
}
