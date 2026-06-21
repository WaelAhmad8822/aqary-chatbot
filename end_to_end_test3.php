<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$sessionId = 'dbg-1111-1111-4111-8111-111111111111';

$request = Illuminate\Http\Request::create(
    '/api/chat', 'POST',
    ['session_id' => $sessionId, 'message' => 'I want an apartment in New Cairo for up to 3 million']
);

$response = $kernel->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Headers: " . json_encode($response->headers->all()) . "\n";
$content = $response->getContent();
echo "Body length: " . strlen($content) . "\n";
echo "Body:\n" . $content . "\n";

$data = json_decode($content, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSON ERROR: " . json_last_error_msg() . "\n";
    exit;
}

echo "\n--- Parsed ---\n";
echo "Reply: {$data['reply']}\n";
echo "Intent: {$data['intent']}\n";
echo "Fallback: " . ($data['fallback'] ? 'YES' : 'no') . "\n";

$loc = $data['resolution']['outcomes']['location'] ?? null;
$pt = $data['resolution']['outcomes']['propertyType'] ?? null;
echo "Location: " . ($loc['status'] ?? 'N/A') . " -> " . ($loc['canonical_name'] ?? '-') . "\n";
echo "PropertyType: " . ($pt['status'] ?? 'N/A') . " -> " . ($pt['canonical_name'] ?? '-') . "\n";

echo "\nFull resolution:\n" . json_encode($data['resolution'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
