<?php

namespace App\Http\Controllers;

use App\Crawler\CrawlerInputValidator;
use App\Crawler\CrawlerReset;
use App\Crawler\CrawlerStart;
use App\Crawler\CrawlerStatsRepository;
use App\Crawler\CrawlerUrlRepository;
use App\Jobs\ProcessCrawlerUrl;
use App\Models\CrawlerUrl;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\View\View;

class Controller extends BaseController
{
    public function index(): View
    {
        return view('welcome');
    }

    public function start(Request $request, CrawlerInputValidator $inputValidator, CrawlerReset $crawlerReset, CrawlerStart $crawlerStart): JsonResponse
    {
        $url = $request->json('url');

        if ($inputValidator($url)) {
            $crawlerReset();
            $crawlerStart($url);

            return new JsonResponse([
                'success' => true,
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'error' => 'Invalid URL'
            ]);
        }
    }

    public function status(CrawlerUrlRepository $crawlerUrlRepository, CrawlerStatsRepository $crawlerStatsRepository): View
    {
        $visited = $crawlerUrlRepository->visited();
        if ($visited->isEmpty()) {
            return view('status', [
                'urls' => $visited,
                'images' => 0,
                'internalLinks' => 0,
                'externalLinks' => 0,
                'averagePageload' => 0,
                'averageWords' => 0,
                'averageTitleLength' => 0,
            ]);
        }
        return view('status', [
            'urls' => $visited,
            'images' => $crawlerStatsRepository->uniqueImageCount($visited),
            'internalLinks' => $crawlerStatsRepository->uniqueInternalLinks($visited),
            'externalLinks' => $crawlerStatsRepository->uniqueExternalLinks($visited),
            'averagePageload' => $crawlerStatsRepository->averagePageload($visited),
            'averageWords' => $crawlerStatsRepository->averageWords($visited),
            'averageTitleLength' => $crawlerStatsRepository->averageTitleLength($visited),
        ]);
    }
}
