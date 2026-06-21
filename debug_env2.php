<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

// The kernel must be created for full boot
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "env('APP_KEY'): " . (env('APP_KEY') ?: 'NULL') . "\n";
echo "env('DB_CONNECTION'): " . (env('DB_CONNECTION') ?: 'NULL') . "\n";
echo "env('OPENROUTER_API_KEY'): " . (env('OPENROUTER_API_KEY') ? 'SET' : 'NULL') . "\n";
echo "env('APP_ENV'): " . (env('APP_ENV') ?: 'NULL') . "\n";
