<ul>
    <li>Pages Crawled: {{ $urls->count() }}</li>
    <li>Unique images: {{ $images }}</li>
    <li>Unique internal: {{ $internalLinks }}</li>
    <li>Unique external: {{ $externalLinks }}</li>
    <li>Average pageload: {{ $averagePageload }}</li>
    <li>Average word count: {{ $averageWords }}</li>
    <li>Average title length: {{ $averageTitleLength }}</li>
</ul>
<h2>Pages crawled</h2>
<ul>
@foreach ($urls as $url)
    <li>{{ $url->url }}</li>
@endforeach
</ul>
