<?php

echo "Before anything:\n";
echo "  getenv APP_KEY: " . (getenv('APP_KEY') ?: 'NULL') . "\n";
echo "  getenv DB_CONNECTION: " . (getenv('DB_CONNECTION') ?: 'NULL') . "\n";

// Simulate what artisan does
putenv('APP_KEY=' . (getenv('APP_KEY') ?: ''));
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=' . __DIR__ . '/database/database.sqlite');
putenv('OPENROUTER_API_KEY=' . (getenv('OPENROUTER_API_KEY') ?: ''));

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Don't call config() - just use the env values directly
echo "Before handle:\n";
echo "  env APP_KEY: " . (env('APP_KEY') ?: 'NULL') . "\n";
echo "  env DB_CONNECTION: " . (env('DB_CONNECTION') ?: 'NULL') . "\n";

$request = Illuminate\Http\Request::create(
    '/api/chat', 'POST',
    ['session_id' => 'simple-' . uniqid(), 'message' => 'I want an apartment in New Cairo for up to 3 million']
);

$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";

if ($response->getStatusCode() === 200) {
    $data = json_decode($response->getContent(), true);
    echo "OK: " . ($data['reply'] ?? '?') . "\n";
    echo "Intent: " . ($data['intent'] ?? '?') . "\n";
} else {
    echo "Body: " . substr($response->getContent(), 0, 200) . "\n";
}
