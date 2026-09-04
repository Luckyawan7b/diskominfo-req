<?php

$directories = [
    __DIR__ . '/app/Livewire',
    __DIR__ . '/resources/views',
    __DIR__ . '/routes',
    __DIR__ . '/tests',
    __DIR__ . '/app/Http',
    __DIR__ . '/app/Models', // already manually edited, but just in case
];

function processDirectory($dir) {
    if (!is_dir($dir)) return;
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            processDirectory($path);
            
            // Rename directory if it's named 'Desa' or 'desa'
            if (basename($path) === 'Desa') {
                rename($path, dirname($path) . '/Dinas');
            } elseif (basename($path) === 'desa') {
                rename($path, dirname($path) . '/dinas');
            }
        } else {
            if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                $content = file_get_contents($path);
                $originalContent = $content;

                // Basic renames
                $content = str_replace('kode_desa', 'alias', $content);
                $content = str_replace('nama_desa', 'nama_dinas', $content);
                $content = str_replace('desa_id', 'dinas_id', $content);
                $content = str_replace('DesaIndex', 'DinasIndex', $content);
                $content = str_replace('desaList', 'dinasList', $content);
                $content = str_replace('desas', 'dinasList', $content); // $desas to $dinasList

                // Word boundaries for Desa, desa, DESA
                $content = preg_replace('/\bDesa\b/', 'Dinas', $content);
                $content = preg_replace('/\bdesa\b/', 'dinas', $content);
                $content = preg_replace('/\bDESA\b/', 'DINAS', $content);

                if ($content !== $originalContent) {
                    file_put_contents($path, $content);
                    echo "Updated: $path\n";
                }

                // Rename file if it contains 'Desa' or 'desa'
                $filename = basename($path);
                if (strpos($filename, 'Desa') !== false || strpos($filename, 'desa') !== false) {
                    $newFilename = str_replace(['Desa', 'desa'], ['Dinas', 'dinas'], $filename);
                    rename($path, dirname($path) . '/' . $newFilename);
                    echo "Renamed file to: $newFilename\n";
                }
            }
        }
    }
}

foreach ($directories as $dir) {
    processDirectory($dir);
}
echo "Done.\n";
