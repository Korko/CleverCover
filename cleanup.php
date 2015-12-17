#!/usr/bin/php
<?php

$dir = 'tmp/';
$files = scandir($dir);
$delay = time() - 2 * 60;

foreach ($files as $file) {
    if (strpos($file, '.png') !== false && filemtime($dir.$file) <= $delay) {
        echo $dir.$file."\n";
        unlink($dir.$file);
    }
}

?>
