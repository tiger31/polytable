<?php
namespace Files;

use Security\Shield;
use Interaction\Response;

class Files {
    static function store_file() {
        global $mysql, $user;
        $filename = strtolower($_FILES['image']['name']);
        $file_type = strtolower($_FILES['image']['type']);
        $is_image = false;
        $image = null;
        //check if contain php and kill it
        $pos = strpos($filename, 'php');
        if (!($pos === false)) {
            Response::create()->error(403, array("info" => "File contains \"php\" string"))->response();
        }
        //check double file type (image with comment)
        if (substr_count($file_type, '/') > 1) {
            Response::create()->error(403, array("info" => "Multiple MIME-TYPE detected"))->response();
        }
        if (($_FILES['image']['size'] / 1024) / 1024 > 5) {
            Response::create()->error(403, array("info" => "File is too large"))->response();
        }
        $file = null;
        //get the file ext
        $file_ext = strrchr($filename, '.');
        //check if its allowed or not
        $whitelist = array(".jpg", ".jpeg", ".bmp", ".png");
        $mime = array('image/bmp', 'image/jpeg', 'image/jpg', 'image/png');
        $pos = strpos($file_type, 'image');
        if ((in_array($file_ext, $whitelist)) && $pos !== false) {
            $image_info = getimagesize($_FILES['image']['tmp_name']);
            if (in_array($image_info['mime'], $mime)) {
                $is_image = true;
                $image = new Images\ImageResize($_FILES['image']['tmp_name']);
                $image->resizeToLongSide(1920);
            } else {
                $is_image = false;
                $file = $_FILES['image']['tmp_name'];
            }
        } else {
            $is_image = false;
            $file = $_FILES['image']['tmp_name'];
        }
        $upload_dir = 'uploads/' . (($is_image) ? 'images' : 'files') . '/';
        $thumbnail_dir = 'uploads/thumbnails/';

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777);
            if ($is_image && !file_exists($thumbnail_dir))
                mkdir($thumbnail_dir, 0777);
        }
        $upload_filename = md5(Shield::rnd_str(16) . time()) . $file_ext;
        $upload_file = $upload_dir . $upload_filename;
        if ($is_image) {
            $thumbnail_file = $thumbnail_dir . $upload_filename;
            $image->save($upload_file);
            $image->crop(50, 50, Images\ImageResize::CROPCENTER);
            $image->save($thumbnail_file);
        } else
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_file);
        chmod($upload_file, 644);
        $time = new \DateTime("tomorrow");
        $time->modify("next day");
        $file_data = array("name" => $upload_filename, "original_name" => (strlen($filename) > 64) ? substr($filename, 0, (64 - strlen($file_ext) - 1)) . $file_ext : $filename, "showable" => (int)$is_image, "size" => $_FILES['image']['size'], "hash" => md5_file($upload_file), "adder_id" => $user->id, "stored_untill" => $time->format("Y-m-d"));
        $mysql->exec(QUERY_FILE_INSERT, RETURN_IGNORE, $file_data);
        Response::create()->response(true, array("filename" => $upload_filename));
    }
}



