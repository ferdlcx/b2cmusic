<?php

$directories = [
    __DIR__ . '/resources/views',
    __DIR__ . '/app',
    __DIR__ . '/config',
    __DIR__ . '/database/seeders'
];

function replaceInDir($dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    $count = 0;
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php'])) {
            $content = file_get_contents($file->getPathname());
            
            // Order matters: replace longer string first
            $newContent = str_replace(['MusicStore Luxe', 'MusicStore'], ['DjudasMS', 'DjudasMS'], $content);
            
            if ($newContent !== $content) {
                file_put_contents($file->getPathname(), $newContent);
                $count++;
            }
        }
    }
    return $count;
}

$total = 0;
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $total += replaceInDir($dir);
    }
}

// Also replace in .env
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    $newEnvContent = str_replace(['MusicStore Luxe', 'MusicStore'], ['DjudasMS', 'DjudasMS'], $envContent);
    if ($newEnvContent !== $envContent) {
        file_put_contents($envPath, $newEnvContent);
        $total++;
    }
}

echo "Modified $total files.\n";
