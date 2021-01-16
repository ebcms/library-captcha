<?php

namespace Ebcms;

class Captcha
{

    public function create(string $str, $level = 3, int $font_size = 25, int $width = null, int $height = null)
    {
        $width = $width ?: ((mb_strlen($str) + 0.7) * $font_size);
        $height = $height ?: $font_size * 2;
        $im = imagecreatetruecolor($width, $height);

        $im_tmp = imagecreatetruecolor($width, $height);
        foreach (str_split($str) as $i => $char) {
            imagefttext($im_tmp, $font_size, rand(-10 * $level, 10 * $level), $font_size / 2 + $font_size * $i, $font_size * 1.5, imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)), __DIR__ . '/font/1.ttf', $char);
        }

        for ($i = 0; $i < $width; $i++) {
            $offset = $level;
            $round = intval($level / 2);
            $posY = round(sin($i * $round * 2 * M_PI / $width) * $offset);
            imagecopy($im, $im_tmp, $i, $posY, $i, 0, 1, $height);
        }

        for ($i = 0; $i < imagesx($im); $i++) {
            for ($j = 0; $j < imagesy($im); $j++) {
                if (imagecolorat($im, $i, $j)) {
                    if (($i + $j) % 3 == 1) {
                        imagesetpixel($im, $i, $j, imagecolorallocate($im, 0, 255, 0));
                    } else {
                        imagesetpixel($im, $i, $j, imagecolorallocate($im, 0, 0, 0));
                    }
                } else {
                    if (($i - $j + 1000) % 3 == 1) {
                        imagesetpixel($im, $i, $j, imagecolorallocate($im, 0, 0, 255));
                    } else {
                        imagesetpixel($im, $i, $j, imagecolorallocate($im, 0, 0, 0));
                    }
                }
            }
        }

        ob_start();
        imagepng($im);
        $content = ob_get_clean();
        imagedestroy($im);
        imagedestroy($im_tmp);
        return $content;
    }
}
