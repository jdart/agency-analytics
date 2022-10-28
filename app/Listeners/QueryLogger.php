<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;

class QueryLogger
{
    public function handle(QueryExecuted $query): void
    {
        /** @var object|string $raw */
        $raw = $query->sql;
        if (is_object($raw) && method_exists($raw, '__toString')) {
            $raw = $raw->__toString();
        }
        if (!is_string($raw)) {
            Log::debug('SQL: Unable to log query');

            return;
        }
        $sql = preg_replace("/\s\s+/", ' ', $raw);
        if (strlen($sql) > 2000) {
            $sql = substr($sql, 0, 2000).'...';
        }
        $bindings = $query->bindings;
        if (count($query->bindings) > 50) {
            $bindings = array_splice($bindings, 0, 50);
        }

        Log::debug('SQL: '.$sql, $bindings);
    }
}
