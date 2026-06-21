<?php

require __DIR__ . '/vendor/autoload.php';

$ref = new ReflectionMethod(Illuminate\Foundation\Configuration\ApplicationBuilder::class, 'create');
echo 'File: ' . $ref->getFileName() . PHP_EOL;
$file = file($ref->getFileName());
for ($i = $ref->getStartLine() - 1; $i < min($ref->getEndLine(), count($file)); $i++) {
    echo ($i+1) . ': ' . $file[$i];
}

echo PHP_EOL . '---' . PHP_EOL;

// Check the Configuration\ApplicationBuilder constructor
$refConst = new ReflectionMethod(Illuminate\Foundation\Configuration\ApplicationBuilder::class, '__construct');
echo 'Constructor: ' . $refConst->getFileName() . PHP_EOL;
$file = file($refConst->getFileName());
for ($i = $refConst->getStartLine() - 1; $i < min($refConst->getEndLine(), count($file)); $i++) {
    echo ($i+1) . ': ' . $file[$i];
}
