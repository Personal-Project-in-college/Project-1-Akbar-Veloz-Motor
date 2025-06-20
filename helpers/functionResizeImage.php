<?php

function resizeImage($sourcePath, $destinationPath, $newWidth =  495 , $newHeight = 335)
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
        case 'image/webp':
            $image = imagecreatefromwebp($sourcePath);
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
    imagejpeg($resizedImage, $destinationPath, 100); // quality 90
    imagedestroy($image);
    imagedestroy($resizedImage);

    return true;
}
