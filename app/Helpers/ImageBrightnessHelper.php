<?php
namespace App\Helpers;

class ImageBrightnessHelper
{
    function run($filename, $extension, $num_samples=10) {
        $img = ($extension=='png'?
            imagecreatefrompng($filename):
            imagecreatefromjpeg($filename));
        $width = imagesx($img);
        $height = imagesy($img);
        $x_step = intval($width/$num_samples);
        $y_step = intval($height/$num_samples);
        $total_lum = 0;
        $sample_no = 1;
        for ($x=0; $x<$width; $x+=$x_step) {
            for ($y=0; $y<$height; $y+=$y_step) {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $lum = ($r+$r+$b+$g+$g+$g)/6;
                $total_lum += $lum;
                $sample_no++;
            }
        }
        // work out the average
        $avg_lum  = $total_lum/$sample_no;
        return ($avg_lum > 170 ? 'dark' :'light');
    }
}