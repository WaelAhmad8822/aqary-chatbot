<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

function callChat($kernel, $sessionId, $message, $label) {
    echo "=== $label ===\n";
    echo "> $message\n";

    $server = [
        'HTTP_HOST' => 'localhost',
        'SERVER_NAME' => 'localhost',
        'SERVER_PORT' => '80',
        'REMOTE_ADDR' => '127.0.0.1',
        'REQUEST_URI' => '/api/chat',
        'REQUEST_METHOD' => 'POST',
    ];
    $request = Illuminate\Http\Request::create(
        '/api/chat', 'POST',
        ['session_id' => $sessionId, 'message' => $message],
        server: $server
    );

    $response = $kernel->handle($request);
    $data = json_decode($response->getContent(), true);

    if ($data === null || ($response->getStatusCode() !== 200)) {
        echo "ERROR: Status={$response->getStatusCode()}\n";
        echo substr($response->getContent(), 0, 200) . "\n\n";
        return [$kernel, null];
    }

    echo "Bot: {$data['reply']}\n";
    echo "Intent: {$data['intent']}" . ($data['fallback'] ? ' (FALLBACK)' : '') . "\n";

    $loc = $data['resolution']['outcomes']['location'] ?? null;
    $pt = $data['resolution']['outcomes']['propertyType'] ?? null;
    if ($loc) echo "Location: {$loc['status']} -> {$loc['canonical_name']}\n";
    if ($pt) echo "PropertyType: {$pt['status']} -> {$pt['canonical_name']}\n";
    echo "Awaiting: " . json_encode($data['awaiting_slots'] ?? []) . "\n";

    if (($data['search']['status'] ?? null) === 'results') {
        echo "SEARCH RESULTS: " . count($data['properties'] ?? []) . " found\n";
        foreach ($data['properties'] as $i => $p) {
            echo "  " . ($i+1) . ". {$p['title']} - EGP {$p['price']} - {$p['location_name']}\n";
        }
    } else {
        echo "Search: {$data['search']['status']}\n";
    }
    echo "\n";
    return [$kernel, $data];
}

$sid = 'search-flow-' . uniqid();

list($kernel, $d1) = callChat($kernel, $sid,
    'I want an apartment in New Cairo for up to 3 million', 'Turn 1');

// Try simple "yes" or "no" for optional prefs
list($kernel, $d2) = callChat($kernel, $sid,
    'no', 'Turn 2: Decline optional');

// If still waiting, provide simple answers
if ($d2 !== null && in_array('optional_preferences', $d2['awaiting_slots'] ?? [])) {
    list($kernel, $d3) = callChat($kernel, $sid,
        'area 150, 3 bedrooms, 2 bathrooms', 'Turn 3: Simple optional');
}

// Try a fresh session with everything in one message
echo "--- Fresh session: everything at once ---\n";
$sid2 = 'all-in-one-' . uniqid();
list($kernel, $d4) = callChat($kernel, $sid2,
    'I want a 3 bedroom apartment in New Cairo for 2.5 million with parking and security', 'All-in-one');

echo "=== SUMMARY ===\n";
echo "English parsing: " . ($d1['fallback'] ? 'FAIL' : 'OK') . "\n";
echo "Arabic parsing: " . (($d1['resolution']['outcomes']['location']['canonical_name'] ?? '') === 'New Cairo' ? 'OK' : 'CHECK') . "\n";
echo "Alias (Tagamoa): " . (($d4['resolution']['outcomes']['location']['canonical_name'] ?? '') === 'New Cairo' ? 'OK' : 'CHECK') . "\n";
echo "Search execution: " . (($d4['search']['status'] ?? 'not_ready') === 'results' ? 'OK' : 'not triggered') . "\n";

echo "END\n";
