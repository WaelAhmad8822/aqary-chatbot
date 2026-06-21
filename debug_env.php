<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

echo "env('APP_KEY'): " . (env('APP_KEY') ?: 'NULL') . "\n";
echo "env('APP_ENV'): " . (env('APP_ENV') ?: 'NULL') . "\n";
echo "env('DB_CONNECTION'): " . (env('DB_CONNECTION') ?: 'NULL') . "\n";
echo "env('OPENROUTER_API_KEY'): " . (env('OPENROUTER_API_KEY') ? 'SET' : 'NULL') . "\n";

echo "\nconfig('app.key'): " . (config('app.key') ?: 'NULL') . "\n";
echo "config('app.env'): " . (config('app.env') ?: 'NULL') . "\n";
echo "config('app.debug'): " . (config('app.debug') ? 'true' : 'false') . "\n";

echo "\nAPP_KEY from getenv: " . (getenv('APP_KEY') ?: 'NULL') . "\n";
