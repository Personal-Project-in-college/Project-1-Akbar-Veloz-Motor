<?php

function resizeImageKTP($sourcePath, $destinationPath, $newWidth = 472, $newHeight = 354)
{
    $imageInfo = getimagesize($sourcePath);
    $mime = $imageInfo['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $image = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }

    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled(
        $resizedImage,
        $image,
        0,
        0,
        0,
        0,
        $newWidth,
        $newHeight,
        imagesx($image),
        imagesy($image)
    );

    // Simpan ke path tujuan
    imagejpeg($resizedImage, $destinationPath, 90); // quality 90
    imagedestroy($image);
    imagedestroy($resizedImage);

    return true;
}
