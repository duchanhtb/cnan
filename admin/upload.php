<?php

/** for upload file with type:file
 * @author duchanh
 * @copyright 2014
 * @desc file process upload from FCKeditor
 */
define('ALLOW_ACCESS', TRUE);
require("config.php");
include_once(INC_PATH . "core/CFile.class.php");
include_once(INC_PATH . "core/Images.class.php");
// disable error
ini_set("html_errors", "0");

$funcNum = Input::get('CKEditorFuncNum', 'txt', '');
// Optional: instance name (might be used to load a specific configuration file or anything else).
$CKEditor = Input::get('CKEditor', 'txt', '');
// Optional: might be used to provide localized messages.
$langCode = Input::get('langCode', 'txt', '');


// Check the upload
if (!isset($_FILES["upload"]) || !is_uploaded_file($_FILES["upload"]["tmp_name"]) || $_FILES["upload"]["error"] != 0) {
    $message = trans('invalid_file_upload');
    $url = '';
    echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
    exit(0);
}

// create directory for upload if it not exists
$type = Input::get('type', 'txt', '');
if (!$type) {
    echo '';
    exit;
}
include("include/$type.php");
$cur_folder = date('Y_m_d') . '/';
$dir = "../uploads/images/" . $type . "/" . $cur_folder;

// create folder
if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
}

if ($_FILES['upload']['name'] != 'none' && $_FILES['upload']['name'] != '') {
    $image_image = remove_special_char($_FILES['upload']['name']); //@ereg_replace("[^a-zA-Z0-9_.]", "_",$_FILES['userfile']['name']);
    $file_name = CFile::uploadFile($_FILES['upload']['tmp_name'], $image_image, $dir);
    $img = $dir . $file_name;
    $img = ($img != 'none') ? $img : "";

    // if file upload is images
    if (@is_array(getimagesize($img))) {
        // create thumb 
        global $imagesSize;
        foreach ($imagesSize as $folder => $images_config) {
            $thumb_width = $images_config['width']; // width
            $thumb_height = $images_config['height']; // height 
            $crop = (isset($images_config['crop']) && $images_config['crop'] == true) ? 'crop' : 'fit'; // crop
            $thumb_dir = $dir . $folder . '/';

            // create thumb folder
            if (!is_dir($thumb_dir)) {
                mkdir($thumb_dir, 0775, true);
            }
            // create Thumb Images
            $path_info = pathinfo($img);
            $thumb_path = $thumb_dir . $file_name;
            $image = new Image($img);
            $image->createThumb($thumb_path, $thumb_width, $thumb_height, $crop);
        }
    }

    // insert to the t_media table
    $path = trim($img, '.');
    insertMedia($path, 0, $type);
    $url = base_url() . $path;
    $message = '';
    echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
} else {
    $message = 'File tải lên không hợp lệ"';
    echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
}
