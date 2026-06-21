<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    $users = \App\Models\User::count();
    echo "Users count: $users\n";
} catch (Throwable $e) {
    echo "DB Error: " . get_class($e) . ": " . $e->getMessage() . "\n";
}

echo "APP_KEY loaded: " . (config('app.key') ? 'YES (' . config('app.key') . ')' : 'NO') . "\n";
echo "APP_ENV: " . (config('app.env') ?: 'not set') . "\n";
echo "DB default: " . (config('database.default') ?: 'not set') . "\n";

echo "\nTesting request...\n";
$request = Illuminate\Http\Request::create(
    '/api/chat', 'POST',
    ['session_id' => 'dbg-' . uniqid(), 'message' => 'test']
);

try {
    $response = $kernel->handle($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Body: " . substr($response->getContent(), 0, 300) . "\n";
} catch (Throwable $e) {
    echo "Error: " . get_class($e) . ": " . $e->getMessage() . "\n";
}
