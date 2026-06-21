<?php

putenv('APP_KEY=' . (getenv('APP_KEY') ?: ''));
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=' . __DIR__ . '/database/database.sqlite');
putenv('OPENROUTER_API_KEY=' . (getenv('OPENROUTER_API_KEY') ?: ''));
putenv('APP_ENV=local');

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

// Check what middleware groups are registered
echo "Checking middleware...\n";

// Get the router
$router = $app->make(Illuminate\Contracts\Routing\Registrar::class);

// Get all routes
$routes = $router->getRoutes();
echo "Routes count: " . count($routes) . "\n";
foreach ($routes as $route) {
    $methods = implode(',', $route->methods());
    echo "  $methods {$route->uri()}\n";
    echo "  Middleware: " . json_encode($route->gatherMiddleware()) . "\n";
    echo "  Action: " . json_encode($route->getAction()) . "\n";
}

echo "\nTrying direct controller instantiation...\n";
try {
    $controller = $app->make(App\Http\Controllers\ChatController::class);
    echo "Controller created OK\n";
    
    $request = Illuminate\Http\Request::create(
        '/api/chat', 'POST',
        ['session_id' => 'direct-' . uniqid(), 'message' => 'test']
    );
    
    $response = $controller->chat($request);
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response: " . substr($response->getContent(), 0, 300) . "\n";
} catch (Throwable $e) {
    echo "Error: " . get_class($e) . ": " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
