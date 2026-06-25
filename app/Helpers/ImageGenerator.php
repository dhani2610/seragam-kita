<?php

namespace App\Helpers;

class ImageGenerator
{
    public static function generatePlaceholder($path, $width, $height, $text, $bgColor = [220, 53, 69], $textColor = [255, 255, 255])
    {
        $fullPath = public_path($path);
        $dir = dirname($fullPath);

        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // Create canvas
        $img = imagecreatetruecolor($width, $height);
        
        // Define colors
        $bg = imagecolorallocate($img, $bgColor[0], $bgColor[1], $bgColor[2]);
        $textCol = imagecolorallocate($img, $textColor[0], $textColor[1], $textColor[2]);

        // Fill background
        imagefilledrectangle($img, 0, 0, $width, $height, $bg);

        // Draw border
        $borderColor = imagecolorallocate($img, max(0, $bgColor[0] - 30), max(0, $bgColor[1] - 30), max(0, $bgColor[2] - 30));
        imagerectangle($img, 0, 0, $width - 1, $height - 1, $borderColor);

        // Add text (using standard font size 5)
        $font = 5;
        $fontWidth = imagefontwidth($font);
        $fontHeight = imagefontheight($font);
        $textLen = strlen($text);
        
        // Calculate coordinate to center the text
        $x = ($width - ($textLen * $fontWidth)) / 2;
        $y = ($height - $fontHeight) / 2;

        imagestring($img, $font, $x, $y, $text, $textCol);

        // Save image
        if (str_ends_with($path, '.jpg') || str_ends_with($path, '.jpeg')) {
            imagejpeg($img, $fullPath, 90);
        } else {
            imagepng($img, $fullPath);
        }

        imagedestroy($img);
    }
}
