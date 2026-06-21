<?php

require __DIR__ . '/vendor/autoload.php';

try {
    $app = require __DIR__ . '/bootstrap/app.php';
    echo "App booted OK\n";
} catch (Throwable $e) {
    echo "BOOT ERROR: " . get_class($e) . ": " . $e->getMessage() . "\n";
    exit(1);
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
echo "Kernel created\n";

$request = Illuminate\Http\Request::create(
    '/api/chat', 'POST',
    ['session_id' => 'test-qck-1111-4111-8111-111111111111', 'message' => 'I want an apartment in New Cairo for up to 3 million']
);

echo "Request URI: " . $request->getUri() . "\n";
echo "Request Method: " . $request->getMethod() . "\n";

try {
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    $content = $response->getContent();
    echo "Status: $status\n";
    echo "Content:\n" . substr($content, 0, 2000) . "\n";
} catch (Throwable $e) {
    echo "Exception: " . get_class($e) . ": " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
