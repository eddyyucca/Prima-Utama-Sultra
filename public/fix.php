<?php
$dirs = [
    __DIR__ . '/../storage/framework/sessions',
    __DIR__ . '/../storage/framework/views',
    __DIR__ . '/../storage/framework/cache/data',
    __DIR__ . '/../storage/logs',
    __DIR__ . '/../bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
        echo "CREATED: $dir<br>";
    } else {
        echo "OK: $dir<br>";
    }
}

echo "<br><strong>Done!</strong> Hapus file ini setelah selesai.";
