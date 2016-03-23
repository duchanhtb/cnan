<?php if(!defined('ALLOW_ACCESS')) exit('No direct script access allowed');
/**
 * @author duchanh
 * @copyright 2012
 * @desc file configuration
 */
session_start();
ob_start();
ini_set('safe_mode','0');
date_default_timezone_set('Asia/Saigon');


/* Config skin 
----------------------------------------------------*/
$skin = 'default';

/* Config layout 
----------------------------------------------------*/
$layout = 'home';


/********** GLOBAL VARIBLE FOR SEO ************/ 
global $title, $keywords, $description, $lang, $imagesSize;
$title = 'Trang chủ';
$keywords = 'Trang chủ';
$description = 'Trang chủ';

/* include constant file
----------------------------------------------------*/
include_once('constant.php');


/* include function file
----------------------------------------------------*/
include(INC_PATH."function.php");


/* get file url
----------------------------------------------------*/
$sitePath = getSiteUrl();

/* add images size
----------------------------------------------------*/
add_image_size('thumb-150',150,150,true);	// $folder, $width, $height, $crop
add_image_size('thumb-480',480,9999,false);	// $folder, $width, $height, $crop
add_image_size('thumb-480-crop',480,480,true);	// $folder, $width, $height, $crop



/* get file url
----------------------------------------------------*/
if(CMS_DEBUG == true){
    ini_set("display_error", "on");
    ini_set('error_reporting',E_ALL);
}else{
    ini_set("display_error", "off");
    ini_set('error_reporting',0);
}



/* Config for localhost 
----------------------------------------------------*/
if(strpos(strtolower($sitePath),"localhost/cachnauanngon")> 0 ){    
    $base_url = "http://localhost/cms/";
    // database
    $dbinfo['dbHost']	  = "localhost";
    $dbinfo['dbUser']	  = "root";
    $dbinfo['dbPass']	  = "";
    $dbinfo['dbName']	  = "cachnauanngon";
    $dbinfo['dbSesName']  = "";
    $dbinfo['dbHostRead'] = "localhost";
}


/* Config for web
----------------------------------------------------*/
if(strpos(strtolower($sitePath),"cachnauanngon.com")> 0 ){	
    // config duong dan cua site
    $base_url = "http://cachnauanngon.com/";
	// database
    $dbinfo['dbHost']	  = "localhost";
    $dbinfo['dbUser']	  = "cachnaua_data";
    $dbinfo['dbPass']	  = "md9lNUGWXVQ";
    $dbinfo['dbName']	  = "cachnaua_data";
    $dbinfo['dbSesName']  = "";
    $dbinfo['dbHostRead'] = "localhost";
}


/* Load class
----------------------------------------------------*/
$arrClass = array(
    'CMysqlDB.class.php', // class xu ly lien quan toi mysql
    'CInput.class.php', // xy ly input du lieu dau vao
    'Base.class.php', // class base cho cac miniClass
);

foreach($arrClass as $class_file_name){
    includeClassFile($class_file_name);
}

/* global $oDb, load MysqlDb class
----------------------------------------------------*/
$oDb = new CMysqlDB( $dbinfo );


/* Config language
----------------------------------------------------*/
$language = isset($_REQUEST['language'])? $_REQUEST['language'] : '';
if($language){
    $_SESSION['language'] = $language;
}else if(!isset($_SESSION['language'])){
    $_SESSION['language'] = 'vi';
}