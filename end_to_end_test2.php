<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

function makeRequest($kernel, $sessionId, $message, $label) {
    echo "=== $label ===\n";
    echo "Message: $message\n\n";

    $request = Illuminate\Http\Request::create(
        '/api/chat', 'POST',
        ['session_id' => $sessionId, 'message' => $message]
    );

    $start = microtime(true);
    $response = $kernel->handle($request);
    $elapsed = round((microtime(true) - $start) * 1000);

    $data = json_decode($response->getContent(), true);
    echo "Latency: {$elapsed}ms\n";
    echo "Reply: {$data['reply']}\n";
    echo "Intent: {$data['intent']}\n";
    echo "Fallback: " . ($data['fallback'] ? 'YES' : 'no') . "\n";

    $loc = $data['resolution']['outcomes']['location'] ?? null;
    $pt = $data['resolution']['outcomes']['propertyType'] ?? null;
    echo "Location: " . ($loc['status'] ?? 'N/A') . " -> " . ($loc['canonical_name'] ?? '-') . "\n";
    echo "PropertyType: " . ($pt['status'] ?? 'N/A') . " -> " . ($pt['canonical_name'] ?? '-') . "\n";

    if (($data['search']['status'] ?? null) === 'results') {
        echo "Search: " . count($data['properties'] ?? []) . " results\n";
        foreach (array_slice($data['properties'] ?? [], 0, 3) as $p) {
            echo "  - {$p['title']} (EGP {$p['price']})\n";
        }
    } else {
        echo "Search Status: {$data['search']['status']}\n";
    }
    echo "Awaiting slots: " . json_encode($data['awaiting_slots'] ?? []) . "\n";
    echo "\n";
    return [$kernel, $data];
}

// Test 1: English - complete scenario
echo "=== SCENARIO: English Full Flow ===\n\n";
$session1 = 'e2e-1111-1111-4111-8111-111111111111';

list($kernel, $d1) = makeRequest($kernel, $session1,
    'I want an apartment in New Cairo for up to 3 million', 'Turn 1: English query');

list($kernel, $d2) = makeRequest($kernel, $session1,
    'yes I want area 150, 3 bedrooms, 2 bathrooms, and security', 'Turn 2: Optional preferences');

if (($d2['search']['status'] ?? null) === 'results') {
    list($kernel, $d3) = makeRequest($kernel, $session1,
        'show me photos of the first property', 'Turn 3: Request photos');
}

echo "\n=== SCENARIO: Arabic Query ===\n\n";
$session2 = 'e2e-2222-2222-4111-8111-222222222222';
list($kernel, $d4) = makeRequest($kernel, $session2,
    'عايز شقة في التجمع الخامس بحد أقصى 3 مليون', 'Turn 1: Arabic');

echo "\n=== SCENARIO: Arabizi/Mixed ===\n\n";
$session3 = 'e2e-3333-3333-4111-8111-333333333333';
list($kernel, $d5) = makeRequest($kernel, $session3,
    'I want a flat in Tagamoa for 3 million with parking', 'Turn 1: Arabizi/mixed');

echo "\nEND OF TEST\n";
