<?php

ob_start('ob_gzhandler');

function loadImage($file)
{
    $data = getimagesize($file);
    switch ($data['mime']) {
        case 'image/jpeg':
            $im = imagecreatefromjpeg($file);
            break;
        case 'image/png':
            $im = imagecreatefrompng($file);
            break;
    case 'image/gif':
        $im = imagecreatefromgif($file);
        break;
        default:
            throw new Exception('Img Type not managed');
    }

    return $im;
}

if (isset($_GET['url']) || (isset($_FILES['img']) && !$_FILES['img']['error'] && is_uploaded_file($_FILES['img']['tmp_name']))) {
    if (isset($_GET['url'])) {
        $file = $_GET['url'];
    } else {
        $file = $_FILES['img']['tmp_name'];
    }

    try {
        $image = loadImage($file);
    } catch (Exception $e) {
        die('Unable to load this image');
    }

    $w = imagesx($image);
    $h = imagesy($image);

    $im2 = ImageCreateTrueColor($w, $h);
    imagecopyResampled($im2, $image, 0, 0, 0, 0, $w, $h, $w, $h);

    $time = @filemtime($file);
    if ($time === null) {
        $time = time();
    }

    $filename = 'tmp/'.md5($file.md5('clevercover-salt')).'.png';
    imagepng($im2, $filename);
    imagedestroy($im2);
    imagedestroy($image);

    echo substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')).'/'.$filename.'#'.time();
} elseif (isset($_POST['src'])) {
    $src = $_POST['src'];

    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename='.md5($src).'.png');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT');
    header('Cache-Control: public');
    header('Pragma: public');

    $uri = substr($src, strpos($src, ',') + 1);
    $im2 = imagecreatefromstring(base64_decode($uri));
    imagepng($im2);
    imagedestroy($im2);
} else {
    die('USAGE: img.php?url=&lt;imageURL&gt; or img.php + by POST src=&lt;base64&gt;');
}
