<?php

require __DIR__ . '/vendor/autoload.php';

$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/api/chat';
$_SERVER['HTTP_HOST'] = 'localhost';
$_POST = [
    'session_id' => 'capture-test-' . uniqid(),
    'message' => 'I want an apartment in New Cairo for up to 3 million',
];

try {
    $app = require __DIR__ . '/bootstrap/app.php';
} catch (Throwable $e) {
    die("Boot: " . $e->getMessage() . "\n");
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
$content = $response->getContent();
if ($response->getStatusCode() === 200) {
    $data = json_decode($content, true);
    if ($data) {
        echo "Reply: {$data['reply']}\n";
        echo "Intent: {$data['intent']}\n";
        $loc = $data['resolution']['outcomes']['location'] ?? null;
        echo "Location: " . ($loc['status'] ?? 'N/A') . " -> " . ($loc['canonical_name'] ?? '-') . "\n";
    }
} else {
    echo "Non-200: " . substr($content, 0, 300) . "\n";
}
