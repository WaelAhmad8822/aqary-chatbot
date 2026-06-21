<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$db = include __DIR__ . '/config/database.php';
echo "DB default: " . ($db['default'] ?? 'not set') . "\n";
echo "DB sqlite DB name: " . json_encode($db['connections']['sqlite']['database'] ?? 'not set') . "\n";

$services = include __DIR__ . '/config/services.php';
echo "Services: " . json_encode($services) . "\n";

$appCfg = include __DIR__ . '/config/app.php';
echo "App key: " . ($appCfg['key'] ?? 'not set') . "\n";
echo "App env: " . ($appCfg['env'] ?? 'not set') . "\n";
