<?php

require __DIR__ . '/vendor/autoload.php';

// Check APP_KEY from env
echo "APP_KEY from env: " . (getenv('APP_KEY') ?: 'NOT SET') . "\n";

// Direct from .env file
$envContent = file_get_contents(__DIR__ . '/.env');
preg_match('/APP_KEY=(.+)/', $envContent, $m);
echo "APP_KEY from file: " . ($m[1] ?? 'NOT FOUND') . "\n";

// Boot app same way as successful test
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create request exactly like the working test
$sessionId = 'comp-' . uniqid();
$request = Illuminate\Http\Request::create(
    '/api/chat', 'POST',
    ['session_id' => $sessionId, 'message' => 'I want an apartment in New Cairo for up to 3 million']
);

echo "Request class: " . get_class($request) . "\n";
echo "Request URI: " . $request->getUri() . "\n";
echo "Request path: " . $request->getPathInfo() . "\n";

// Check if there's a middleware stack issue
$response = $kernel->handle($request);
echo "Response status: " . $response->getStatusCode() . "\n";

$content = $response->getContent();

// Check if the response is a Laravel error page
if (strpos($content, 'Redirecting to') !== false) {
    echo "-> This is a redirect page\n";
}

// Try to see what route matched
$route = $request->route();
if ($route) {
    echo "Route matched: " . $route->uri() . "\n";
    echo "Route name: " . $route->getName() . "\n";
    echo "Route action: " . json_encode($route->getAction()) . "\n";
} else {
    echo "No route matched!\n";
}
