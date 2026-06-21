<?php

require __DIR__ . '/vendor/autoload.php';

// Check the Application configure method
$ref = new ReflectionMethod(Illuminate\Foundation\Application::class, 'configure');
echo 'Application::configure in: ' . $ref->getFileName() . PHP_EOL;

// Check the Application constructor for env loading
$refConst = new ReflectionMethod(Illuminate\Foundation\Application::class, '__construct');
echo 'Application::__construct in: ' . $refConst->getFileName() . PHP_EOL;

// Read relevant parts
$file = file($ref->getFileName());
for ($i = $ref->getStartLine() - 1; $i < min($ref->getEndLine(), count($file)); $i++) {
    echo $i+1 . ': ' . $file[$i];
}
