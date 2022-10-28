<?php

namespace App\Jobs;

use App\Crawler\Crawler;
use App\Crawler\CrawlerNext;
use App\Models\CrawlerUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCrawlerUrl implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private CrawlerUrl $crawlerUrl;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CrawlerUrl $crawlerUrl)
    {
        $this->crawlerUrl = $crawlerUrl;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Crawler $crawler, CrawlerNext $crawlerNext)
    {
        if (!$crawler($this->crawlerUrl)) {
            $this->fail();
        }
        sleep(1);
        $crawlerNext();
    }
}
