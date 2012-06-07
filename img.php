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
		case "image/gif":
			$im = imagecreatefromgif($file);
			break;
        default:
            throw new Exception('Img Type not managed');
    }
    return $im;
}

if (isset($_POST['src'])) {
	$src = $_POST['src'];

	header('Content-Type: image/png');
	header("Content-Disposition: attachment; filename=".uniqid(microtime(true)).".png");
	$uri =  substr($src, strpos($src,",") + 1);
	$im2 = imagecreatefromstring(base64_decode($uri));
	imagepng($im2);
	imagedestroy($im2);
} else if (isset($_GET['url'])) {
	$url = $_GET['url'];
	
	try {
	    $image = loadImage($url);
	} catch(Exception $e) {
	    die('Unable to load this image');
	}
	
	$w = imagesx($image);
	$h = imagesy($image);
	
	$im2 = ImageCreateTrueColor($w, $h);
	imagecopyResampled ($im2, $image, 0, 0, 0, 0, $w, $h, $w, $h);
	
	$time = @filemtime($url);
	if ($time === null) {
		$time = time();
	}

	header('Content-Type: image/png');
	header("Content-Disposition: inline; filename=".basename($url).".png");
	imagepng($im2);
	imagedestroy($im2);
	imagedestroy($image);
} else {
	die('USAGE: img.php?url=&lt;imageURL&gt; or img.php + by POST src=&lt;base64&gt;');
}

header('Content-type: image/png');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $time) . ' GMT');
header("Cache-Control: public");
header("Pragma: public");