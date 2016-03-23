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
ini_set("html_errors", "0");
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
$type = Input::get('type', 'txt', 'media');
if (!$type) {
    echo '';
    exit;
}
// if media upload, not include file
$cur_folder = date('Y_m_d') . '/';
$dir = "uploads/images/" . $type . "/" . $cur_folder;

// create folder
if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
}

if ($_FILES['userfile']['name'] != 'none' && $_FILES['userfile']['name'] != '') {
    $image_image = remove_special_char($_FILES['userfile']['name']); //@ereg_replace("[^a-zA-Z0-9_.]", "_",$_FILES['userfile']['name']);
    $file_name = CFile::uploadFile($_FILES['userfile']['tmp_name'], $image_image, $dir);
    $file_upload = $dir . $file_name;
    $file_upload = ($file_upload != 'none') ? $file_upload : "";
    if ($file_upload) {
        // insert to the t_media table
        $media_id = insertMedia(trim($file_upload, '.'), 0, $type);
        switch ($type) {
            case 'recipe':
                    $RecipeImages       = new RecipeImages();
                    $RecipeImages->img  = $file_upload;
                    $media_id           = $RecipeImages->insert();
                    if($media_id){
                        $RecipeImages->ordering = $media_id;
                        $RecipeImages->update($media_id, array('ordering'));
                    }
                break;
            case 'locate':
                    $DiningVenousImage          = new DiningVenousImage();
                    $DiningVenousImage->img     = $file_upload;
                    $media_id                   = $DiningVenousImage->insert();
                    if($media_id){
                        $DiningVenousImage->ordering = $media_id;
                        $DiningVenousImage->update($media_id, array('ordering'));
                    }
                break;    
        }
        
    } else {
        $arrResult = array('status' => 'error', 'msg' => 'invalid file upload');
    }

    // Create thumb if file upload is a images
    if (@is_array(getimagesize($file_upload))) {
        // resize with large size
        list($width, $height) = getimagesize(ROOT_PATH.$file_upload);
        if($width > 900  || $height > 900){
            $imageThumb = new Image($file_upload);
            $imageThumb->createThumb($file_upload, 900, 9999, 'fit');
        }
        
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
        
        $type_upload = Input::get('type_upload', 'txt', '');
        if ($type_upload == 'multiimages') { // if not upload default images
            $arrId = GetSession('multiimages', array());
            $arrId[] = $media_id;
            SetSession('multiimages', $arrId);
        }
        $thumb = createThumbnail('thumb-150', $file_upload, 150, 150, true);
        $arrResult = array('status' => 'success', 'id' => $media_id, 'thumb' => trim($thumb, '.'), 'src' => trim($file_upload, '.'));
    } else {
        $Media      = new Media();
        $img        = $Media->getSrcMedia($file_upload);
        $img        = str_replace(base_url(), '', $img);
        $arrResult  = array('status' => 'success', 'id' => $media_id, 'src' => trim($img, '.'));
    }

    echo json_encode($arrResult);
} else {
    $arrResult = array('status' => 'error', 'id' => 0, 'src' => ADMIN_FOLDER . '/images/not-available.png', 'msg' => 'This images not available');
    echo json_encode($arrResult);
}

