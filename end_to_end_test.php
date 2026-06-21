<?php

/**
 * End-to-end test: sends real messages through the full chatbot pipeline
 * including the OpenRouter LLM call for NLU extraction.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::create('/api/chat', 'POST', [
        'session_id' => 'aaaaaaaa-1111-4111-8111-111111111111',
        'message' => 'I want an apartment in New Cairo for up to 3 million',
    ])
);

$data1 = json_decode($response->getContent(), true);
echo "=== TURN 1: Initial Request ===\n";
echo "Reply: " . ($data1['reply'] ?? 'N/A') . "\n";
echo "Intent: " . ($data1['intent'] ?? 'N/A') . "\n";
echo "Search Status: " . ($data1['search']['status'] ?? 'N/A') . "\n";
echo "Resolution: " . json_encode($data1['resolution'] ?? null, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
echo "Slots: " . json_encode($data1['slot_collection']['required_slots'] ?? null, JSON_PRETTY_PRINT) . "\n";
echo "Awaiting: " . json_encode($data1['awaiting_slots'] ?? []) . "\n\n";

// Check if resolution happened correctly
$locationOutcome = $data1['resolution']['outcomes']['location'] ?? null;
echo "Location Resolution: " . ($locationOutcome['status'] ?? 'none') . " -> " . ($locationOutcome['canonical_name'] ?? 'N/A') . "\n";
$typeOutcome = $data1['resolution']['outcomes']['propertyType'] ?? null;
echo "PropertyType Resolution: " . ($typeOutcome['status'] ?? 'none') . " -> " . ($typeOutcome['canonical_name'] ?? 'N/A') . "\n";

echo "\n===\n";

// If search was ready and executed
if (($data1['search']['status'] ?? null) === 'results') {
    echo "\n=== SEARCH RESULTS ===\n";
    echo "Found " . ($data1['search']['result_count'] ?? 0) . " listings\n";
    foreach ($data1['properties'] ?? [] as $p) {
        echo "  - {$p['title']} (EGP {$p['price']}, {$p['location_name']})\n";
    }
} elseif (isset($data1['awaiting_slots']) && in_array('resolution_clarification', $data1['awaiting_slots'] ?? [])) {
    echo "Clarification needed.\n";
}

echo "\n========================\n\n";

// TURN 2: Test Arabic/Arabizi
$_server = $request->server->all();
$request2 = Illuminate\Http\Request::create(
    '/api/chat', 'POST',
    [
        'session_id' => 'aaaaaaaa-1111-4111-8111-111111111111',
        'message' => 'I want to see photos of the first property',
    ],
    server: $_server
);

$response2 = $kernel->handle($request2);
$data2 = json_decode($response2->getContent(), true);
echo "=== TURN 2: Request Photos ===\n";
echo "Reply: " . ($data2['reply'] ?? 'N/A') . "\n";
echo "Intent: " . ($data2['intent'] ?? 'N/A') . "\n";

echo "\n========================\n\n";

// TURN 3: Arabic query
$request3 = Illuminate\Http\Request::create(
    '/api/chat', 'POST',
    [
        'session_id' => 'bbbbbbbb-2222-4111-8111-222222222222',
        'message' => 'عايز شقة في التجمع الخامس بحد أقصى 3 مليون',
    ],
    server: $_server
);

$response3 = $kernel->handle($request3);
$data3 = json_decode($response3->getContent(), true);
echo "=== TURN 3: Arabic Query ===\n";
echo "Reply: " . ($data3['reply'] ?? 'N/A') . "\n";
echo "Intent: " . ($data3['intent'] ?? 'N/A') . "\n";
echo "Slots: " . json_encode($data3['slot_collection']['required_slots'] ?? null, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
$locOutcome = $data3['resolution']['outcomes']['location'] ?? null;
echo "Resolution Location: " . ($locOutcome['status'] ?? 'none') . " -> " . ($locOutcome['canonical_name'] ?? 'N/A') . "\n";
$ptOutcome = $data3['resolution']['outcomes']['propertyType'] ?? null;
echo "Resolution PropertyType: " . ($ptOutcome['status'] ?? 'none') . " -> " . ($ptOutcome['canonical_name'] ?? 'N/A') . "\n";

if (($data3['search']['status'] ?? null) === 'results') {
    echo "\nSearch Results:\n";
    foreach ($data3['properties'] ?? [] as $p) {
        echo "  - {$p['title']} (EGP {$p['price']})\n";
    }
}

echo "\n========================\n\n";

// TURN 4: Test ambiguous location
$request4 = Illuminate\Http\Request::create(
    '/api/chat', 'POST',
    [
        'session_id' => 'cccccccc-3333-4111-8111-333333333333',
        'message' => 'I want a villa in Zayed for 5 million',
    ],
    server: $_server
);

$response4 = $kernel->handle($request4);
$data4 = json_decode($response4->getContent(), true);
echo "=== TURN 4: Ambiguous Location? ===\n";
echo "Reply: {$data4['reply']}\n";
echo "Location Resolution: " . json_encode($data4['resolution']['outcomes']['location'] ?? null, JSON_UNESCAPED_UNICODE) . "\n";

if (($data4['search']['status'] ?? null) === 'results') {
    echo "\nSearch Results:\n";
    foreach ($data4['properties'] ?? [] as $p) {
        echo "  - {$p['title']} (EGP {$p['price']})\n";
    }
}

echo "\n========================\n\n";
echo "E2E TEST COMPLETE\n";

$kernel->terminate($request, $response);
