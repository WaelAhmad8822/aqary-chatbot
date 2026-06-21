<?php

// Manually set env variables before boot
putenv('APP_KEY=' . (getenv('APP_KEY') ?: ''));
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=' . __DIR__ . '/database/database.sqlite');
putenv('OPENROUTER_API_KEY=' . (getenv('OPENROUTER_API_KEY') ?: ''));
putenv('APP_ENV=local');
putenv('OPENROUTER_MODEL=openai/gpt-oss-20b:free');

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create(
    '/api/chat', 'POST',
    ['session_id' => 'manual-env-' . uniqid(), 'message' => 'I want an apartment in New Cairo for up to 3 million with parking']
);

$response = $kernel->handle($request);
echo 'Status: ' . $response->getStatusCode() . PHP_EOL;

if ($response->getStatusCode() === 200) {
    $data = json_decode($response->getContent(), true);
    echo 'Reply: ' . ($data['reply'] ?? 'N/A') . PHP_EOL;
    echo 'Intent: ' . ($data['intent'] ?? 'N/A') . PHP_EOL;
    echo 'Fallback: ' . ($data['fallback'] ? 'YES' : 'no') . PHP_EOL;
    $loc = $data['resolution']['outcomes']['location'] ?? null;
    echo 'Location: ' . ($loc['status'] ?? 'N/A') . ' -> ' . ($loc['canonical_name'] ?? '-') . PHP_EOL;
    $pt = $data['resolution']['outcomes']['propertyType'] ?? null;
    echo 'PropertyType: ' . ($pt['status'] ?? 'N/A') . ' -> ' . ($pt['canonical_name'] ?? '-') . PHP_EOL;
    if (($data['search']['status'] ?? null) === 'results') {
        echo 'Search: ' . count($data['properties'] ?? []) . ' results' . PHP_EOL;
        foreach ($data['properties'] as $p) {
            echo '  - ' . $p['title'] . ' (EGP ' . $p['price'] . ')' . PHP_EOL;
        }
    } else {
        echo 'Search Status: ' . ($data['search']['status'] ?? 'N/A') . PHP_EOL;
    }
} else {
    echo 'Content: ' . substr($response->getContent(), 0, 500) . PHP_EOL;
}
