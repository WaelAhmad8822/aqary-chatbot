<?php

require __DIR__ . '/vendor/autoload.php';

$ref = new ReflectionMethod(Illuminate\Foundation\Application::class, '__construct');
$file = file($ref->getFileName());
for ($i = $ref->getStartLine() - 1; $i < min($ref->getEndLine(), 150, count($file)); $i++) {
    echo ($i+1) . ': ' . $file[$i];
}

echo PHP_EOL . '--- Looking for "env" or "dotenv" references ---' . PHP_EOL;

$allFile = file($ref->getFileName());
foreach ($allFile as $i => $line) {
    if (stripos($line, 'env') !== false || stripos($line, 'dotenv') !== false || stripos($line, '.env') !== false) {
        echo ($i+1) . ': ' . $line;
    }
}
