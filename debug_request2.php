<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test without api prefix
$request = Illuminate\Http\Request::create(
    '/chat', 'POST',
    ['session_id' => 'debug2-1111-4111-8111-111111111115', 'message' => 'test']
);

echo "URI: " . $request->getUri() . "\n";

try {
    $response = $kernel->handle($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content:\n" . $response->getContent() . "\n";
} catch (\Throwable $e) {
    echo "ERROR: " . get_class($e) . ": " . $e->getMessage() . "\n";
}

echo "\n---\n";

// Now test a fresh app instance with /api/chat
$app2 = require __DIR__ . '/bootstrap/app.php';
$kernel2 = $app2->make(Illuminate\Contracts\Http\Kernel::class);

$request2 = Illuminate\Http\Request::create(
    '/api/chat', 'POST',
    ['session_id' => 'debug3-1111-4111-8111-111111111115', 'message' => 'test']
);

echo "URI2: " . $request2->getUri() . "\n";

try {
    $response2 = $kernel2->handle($request2);
    echo "Status2: " . $response2->getStatusCode() . "\n";
    echo "Content2:\n" . $response2->getContent() . "\n";
} catch (\Throwable $e) {
    echo "ERROR2: " . get_class($e) . ": " . $e->getMessage() . "\n";
}
