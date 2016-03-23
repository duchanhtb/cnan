<?php

/** for upload file with type:file
 * @author duchanh
 * @copyright 2012
 */
define('ALLOW_ACCESS', TRUE);
include("config.php");
include_once(INC_PATH . "core/CFile.class.php");

// disable error
ini_set("html_errors", "0");

// Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
if (isset($_POST["PHPSESSID"])) {
    session_id($_POST["PHPSESSID"]);
}

$aTypeUpload = array(
    // document    
    'pdf',
    'doc',
    'docx',
    'ppt',
    'pptx',
    'xls',
    'xlsx',
    // zip
    'zip',
    '7zip',
    'gz',
    'tar',
    'rar',
    'iso',
    // txt
    'txt',
    'sql',
    '7zip',
    // movie        
    'flv',
    'mov',
    'mp3',
    'mp4',
    'mkv',
    'avi',
    'swf',
    // images
    'jpg',
    'jpeg',
    'gif',
    'png',
    'ico',
    // web text
    'js',
    'xml',
    'html',
    'htm',
    'tpl',
    'ini',
);

// show file file extension accept on server
if (isset($_GET['ext']) && $_GET['ext'] == '1') {
    $space = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*.';
    foreach ($aTypeUpload as $ext) {
        echo $space . $ext . '<br/>';
    }
    exit;
}


// Check the upload
if (!isset($_FILES["userfile"]) || !is_uploaded_file($_FILES["userfile"]["tmp_name"]) || $_FILES["userfile"]["error"] != 0) {
    echo "ERROR:invalid upload";
    exit(0);
}

// create folder to upload if not exists
$dir = "../uploads/files/";
if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
}

if ($_FILES['userfile']['name'] != 'none' && $_FILES['userfile']['name'] != '') {
    $file = remove_special_char($_FILES['userfile']['name']); //@ereg_replace("[^a-zA-Z0-9_.]", "_",$_FILES['userfile']['name']);
    $file_name = CFile::uploadFile($_FILES['userfile']['tmp_name'], $file, $dir, $aTypeUpload);
    if ($file_name == 'none') {
        $result = array('status' => 'error', 'msg' => 'This file cannot be accepted to upload');
        echo json_encode($result);
        exit;
    }

    $file_path = trim($dir, '.') . $file_name;
    // insert to the media
    $type = Input::get('type', 'txt', '');
    insertMedia($file_path, 0, $type);
    $result = array('status' => 'sucsess', 'path' => $file_path);
    echo json_encode($result);
} else {
    $result = array('status' => 'error', 'msg' => "Error when upload this file");
    echo json_encode($result);
}