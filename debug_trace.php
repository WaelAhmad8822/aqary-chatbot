<?php

require __DIR__ . '/vendor/autoload.php';

try {
    $app = require __DIR__ . '/bootstrap/app.php';
} catch (Throwable $e) {
    die("App boot failed: " . $e->getMessage());
}

// Register tracing
$app->afterResolving(function ($object, $app) {
    if ($object instanceof Illuminate\Foundation\Http\Kernel) {
        echo "Kernel resolved\n";
    }
});

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create(
    '/api/chat', 'POST',
    ['session_id' => 'trace-test-1111-4111-8111-111111111111', 'message' => 'I want an apartment in New Cairo for up to 3 million']
);

$request->setUserResolver(function () {
    return null;
});

echo "Handling request...\n";

try {
    $response = $kernel->handle($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() === 302) {
        echo "Redirect to: " . $response->headers->get('Location') . "\n";
        // Check content type
        echo "Content-Type: " . $response->headers->get('Content-Type') . "\n";
    }
    $content = $response->getContent();
    echo "Body length: " . strlen($content) . "\n";
} catch (Throwable $e) {
    echo "Exception: " . get_class($e) . ": " . $e->getMessage() . "\n";
}
