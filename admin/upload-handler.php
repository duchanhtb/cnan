<?php

/**
 * @author duchanh
 * @copyright 2012
 * @desc process upload file with type = images
 */
define('ALLOW_ACCESS', TRUE);
include("config.php");
include_once(INC_PATH . "core/CFile.class.php");
include_once(INC_PATH . "core/Images.class.php");

// disable error
//ini_set("html_errors", "0");
ini_set("display_error", "on");
ini_set('error_reporting',E_ALL);
@session_start();
// Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
if (isset($_POST["PHPSESSID"])) {
    session_id($_POST["PHPSESSID"]);
} else if (isset($_GET["PHPSESSID"])) {
    session_id($_GET["PHPSESSID"]);
}

// Check the upload
if (!isset($_FILES["userfile"]) || !is_uploaded_file($_FILES["userfile"]["tmp_name"]) || $_FILES["userfile"]["error"] != 0) {
    $arrResult = array('status' => 'error', 'msg' => 'invalid upload');
    echo json_encode($arrResult);
    exit(0);
}

// create directory for upload if it not exists
$type = Input::get('type', 'txt', '');
if (!$type) {
    echo '';
    exit;
}
// if media upload, not include file
$cur_folder = date('Y_m_d') . '/';
if ($type != 'media') {
    $file_include = "include/$type.php";
    if (file_exists($file_include)) {
        include_once("include/$type.php");
    }
    $dir = "../uploads/images/" . $type . "/" . $cur_folder;
} else {
    $dir = "../uploads/" . $type . "/" . $cur_folder;
}

// create folder
if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
}

if ($_FILES['userfile']['name'] != 'none' && $_FILES['userfile']['name'] != '') {
    $image_image = remove_special_char($_FILES['userfile']['name']); //@ereg_replace("[^a-zA-Z0-9_.]", "_",$_FILES['userfile']['name']);
    $file_name = CFile::uploadFile($_FILES['userfile']['tmp_name'], $image_image, $dir);
    $file_upload = $dir . $file_name;
    $file_upload = ($file_upload != 'none') ? $file_upload : "";
    
    // insert to media if upload sucsess
    if ($file_upload) {
        
        // resize if very large images
        if(defined('MAX_SIZE_IMAGE') && @is_array(getimagesize($file_upload))){
            $image = new Image($file_upload);
            list($width, $height) = explode('x', MAX_SIZE_IMAGE);
            $image->resize($width, $height);
            $file_upload_info = pathinfo($file_upload);
            $image->save($file_upload_info['filename'], $file_upload_info['dirname'], $file_upload_info['extension']);
        }
        
        // insert to the t_media table
        $media_id = insertMedia(trim($file_upload, '.'), 0, $type);
    } else {
        $arrResult = array('status' => 'error', 'msg' => 'invalid file upload');
    }
    
    // Create thumb if file upload is a images
    if (@is_array(getimagesize($file_upload))) {
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
            $path_info = pathinfo($file_upload);
            $thumb_path = $thumb_dir . $file_name;
            $image = new Image($file_upload);
            $image->createThumb($thumb_path, $thumb_width, $thumb_height, $crop);
        }
        $id = 0;
        $type_upload = Input::get('type_upload', 'txt', '');
        if ($type_upload != 'single' && isset($column)) { // if not upload default images
            foreach ($column as $image_field => $col) {
                if ($col['type'] == 'input:multiimages') {
                    $table_info = $col['table'];
                    $table_name = $table_info['table_name'];
                    $primary_key = $table_info['primary_key'];
                    $images_url = $table_info['images_url'];
                    $relate_id = $table_info['relate_id'];
                    // insert to db
                    $base = new Base();
                    $base->table = $table_name;
                    $base->pk = $primary_key;
                    $base->fields = array($images_url);
                    $base->$images_url = trim($file_upload, '.');
                    $id = $base->insert();
                    $arrId = isset($_SESSION['multiimages'][$image_field]) ? $_SESSION['multiimages'][$image_field] : array();

                    $arrId[] = $id;
                    $_SESSION['multiimages'][$image_field] = $arrId;
                    break;
                }
            }
        }
        if ($id == 0)
            $id = $media_id;
        if ($type_upload != 'single') {
            $img = createThumbnail('thumb-150', $file_upload, 150, 150, true);
        } else {
            $img = $file_upload;
        }

        $arrResult = array('status' => 'success', 'id' => $id, 'src' => trim($img, '.'));
    } else {
        $Media = new Media();
        $img = $Media->getSrcMedia($file_upload);
        $img = str_replace(base_url(), '', $img);
        $arrResult = array('status' => 'success', 'id' => $media_id, 'src' => trim($img, '.'));
    }

    echo json_encode($arrResult);
} else {
    $arrResult = array('status' => 'error', 'id' => 0, 'src' => ADMIN_FOLDER . '/images/not-available.png', 'msg' => 'This images not available');
    echo json_encode($arrResult);
}

