<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create(
    '/api/chat', 'POST',
    ['session_id' => 'debug-redir-1111-4111-8111-111111111115', 'message' => 'test']
);

echo "URI: " . $request->getUri() . "\n";
echo "Method: " . $request->getMethod() . "\n";

try {
    $response = $kernel->handle($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content:\n" . $response->getContent() . "\n";
} catch (\Throwable $e) {
    echo "ERROR: " . get_class($e) . ": " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
