<?php
const COMMON_SIZE = 512;
const CELL_SIZE = 64;
const CELL_COUNT = COMMON_SIZE / CELL_SIZE;
class ImageCreatePattern {

    static function call($id) {
        if (!$id) return;
        $image = imagecreatetruecolor(COMMON_SIZE, COMMON_SIZE);
        $color = imagecolorallocate($image, rand(0, 255), rand(0, 255),rand(0, 255));
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $white);
        for ($i = 0; $i < CELL_COUNT; $i++) {
            for ($j = 0; $j < (CELL_COUNT / 2); $j++) {
                //Шанс заполнения ячейки цветом: 25%
                if (rand(0, 100) < 25) {
                    imagefilledrectangle($image, $j * CELL_SIZE, $i * CELL_SIZE,($j + 1) * CELL_SIZE, ($i + 1) * CELL_SIZE, $color);
                    imagefilledrectangle($image, (CELL_COUNT - $j - 1) * CELL_SIZE, $i * CELL_SIZE, (CELL_COUNT - $j) * CELL_SIZE, ($i + 1) * CELL_SIZE, $color);
                }
            }
        }
        imagepng($image, $_SERVER['DOCUMENT_ROOT'] . "/data/image/512/" . $id . ".png");
        $delimiter = 2;
        for ($i = 0; $i < 4; $i++) {
            $size = COMMON_SIZE / $delimiter;
            $thumb = imagecreatetruecolor($size,$size);
            imagecopyresized($thumb, $image,0,0,0,0, $size, $size,512, 512);
            imagepng($thumb, $_SERVER['DOCUMENT_ROOT'] . "/data/image/" . $size . "/" . $id . ".png");
            $delimiter *= 2;
        }

    }
}