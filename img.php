<?php

ob_start("ob_gzhandler");

function loadImage ($file) {
    $data = getimagesize($file);
    switch($data["mime"]){
        case "image/jpeg":
            $im = imagecreatefromjpeg($file);
            break;
        case "image/png":
            $im = imagecreatefrompng($file);
            break;
        default:
            throw new Exception('Img Type not managed');
    }
    return $im;
}

if (!isset($_GET['src'])) {
    die('USAGE: img.php?src=&lt;imageURL&gt;');
}

$src = $_GET['src'];

try {
    $image = loadImage($src);
} catch(Exception $e) {
    die('Unable to load this image');
}

$w = imagesx($image);
$h = imagesy($image);

$im2 = ImageCreateTrueColor($w, $h);
imagecopyResampled ($im2, $image, 0, 0, 0, 0, $w, $h, $w, $h);

$time = @filemtime($src);
if ($time === null) {
	$time = time();
}

header('Content-type: image/png');
header("Content-Disposition: inline; filename=".basename($src).".png");
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $time) . ' GMT');
header("Cache-Control: public");
header("Pragma: public");
imagepng($im2);
imagedestroy($im2);
imagedestroy($image);