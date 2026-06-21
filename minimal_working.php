<?php

// Exact copy of the create logic from end_to_end_final.php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create(
    '/api/chat', 'POST',
    ['session_id' => 'minimal-test-' . uniqid(), 'message' => 'I want an apartment in New Cairo for up to 3 million']
);

$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";

if ($response->getStatusCode() === 200) {
    $data = json_decode($response->getContent(), true);
    echo "OK: " . ($data['reply'] ?? 'no reply') . "\n";
} else {
    echo "FAIL: " . substr($response->getContent(), 0, 300) . "\n";
}
