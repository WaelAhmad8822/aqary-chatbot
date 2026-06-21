<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "getenv APP_KEY: " . (getenv('APP_KEY') ?: 'NULL') . "\n";
echo "getenv DB_CONNECTION: " . (getenv('DB_CONNECTION') ?: 'NULL') . "\n";
echo "getenv OPENROUTER_API_KEY: " . (getenv('OPENROUTER_API_KEY') ? 'SET' : 'NULL') . "\n";

echo "\$_SERVER keys: ";
foreach (array_keys($_SERVER) as $k) {
    if (str_contains($k, 'APP') || str_contains($k, 'DB_') || str_contains($k, 'OPEN')) {
        echo "$k=" . ($_SERVER[$k] ?: 'NULL') . " ";
    }
}
echo "\n";

echo "FILE: " . __FILE__ . "\n";
echo "DIR: " . __DIR__ . "\n";
