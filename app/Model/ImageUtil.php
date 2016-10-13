<?php

namespace App\Helpers;

use Nette\Utils\Image;

/**
 * Description of ImageUtil
 *
 * @author Max-xa - Vojtěch Müller
 * @email muller.voj@gmail.com
 */
class ImageUtil {

    /**
     * @param Image $image
     * @param $preferWidth
     * @param $preferHeight
     * @return Image
     */
    public static function resizePhoto(Image $image, $preferWidth, $preferHeight) {
        $img_width = $preferWidth;
        $img_height = $preferHeight;

        // get width and height of original image
        $original_width = $image->width;
        $original_height = $image->height;


        if ($original_width > $original_height) {
            $new_height = $preferHeight;
            $new_width = $new_height * ($original_width / $original_height);
        }
        if ($original_height > $original_width) {
            $new_width = $preferWidth;
            $new_height = $new_width * ($original_height / $original_width);
        }
        if ($original_height == $original_width) {
            $new_width = $img_width;
            $new_height = $img_height;
        }

        $new_width = round($new_width);
        $new_height = round($new_height);

        $smaller_image = imagecreatetruecolor($new_width, $new_height);
        $square_image = imagecreatetruecolor($img_width, $img_height);

        imagecopyresampled($smaller_image, $image->getImageResource(), 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);

        if ($new_width > $new_height) {
            $difference = $new_width - $new_height;
            $half_difference = round($difference / 2);
            imagecopyresampled($square_image, $smaller_image, 0 - $half_difference + 1, 0, 0, 0, $img_width + $difference, $img_height, $new_width, $new_height);
        }
        if ($new_height > $new_width) {
            $difference = $new_height - $new_width;
            $half_difference = round($difference / 2);
            imagecopyresampled($square_image, $smaller_image, 0, 0 - $half_difference + 1, 0, 0, $img_width, $img_height + $difference, $new_width, $new_height);
        }
        if ($new_height == $new_width) {
            imagecopyresampled($square_image, $smaller_image, 0, 0, 0, 0, $img_width, $img_height, $new_width, $new_height);
        }

        return new Image($square_image);
    }

}
