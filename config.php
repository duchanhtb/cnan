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
include('constant.php');


/* include function file
----------------------------------------------------*/
include(ROOT_PATH."function.php");

/* include constant file
----------------------------------------------------*/
include(ROOT_PATH.'autoload.php');



/* add images size
----------------------------------------------------*/
add_image_size('thumb-150', 150, 150, true);	// $folder, $width, $height, $crop
add_image_size('thumb-480' ,480, 999999, false);	// $folder, $width, $height, $crop
add_image_size('thumb-480-crop', 480, 480, true);	// $folder, $width, $height, $crop
add_image_size('thumb-500', 500, 99999, true);	// $folder, $width, $height, $crop
add_image_size('thumb-500-crop', 500, 500, true);	// $folder, $width, $height, $crop
add_image_size('thumb-500-crop', 500, 500, true);	// $folder, $width, $height, $crop
add_image_size('origin', 1024, 999999, false);	// $folder, $width, $height, $crop
add_image_size('og-image',490, 294, true);	// $folder, $width, $height, $crop



/* cms debug ON/OFF
----------------------------------------------------*/
if(CMS_DEBUG == true){
    ini_set("display_error", "on");
    ini_set('error_reporting',E_ALL);
}else{
    ini_set("display_error", "off");
    ini_set('error_reporting',0);
}



$sitePath = getSiteUrl();
/* Config for localhost 
----------------------------------------------------*/
if(strpos(strtolower($sitePath),"localhost/cnan")> 0 ){    
    $base_url = "http://localhost/cnan/";
    // database
    $dbinfo['dbHost']	  = "localhost";
    $dbinfo['dbUser']	  = "root";
    $dbinfo['dbPass']	  = "";
    $dbinfo['dbName']	  = "cnan";
    $dbinfo['dbSesName']  = "";
    $dbinfo['dbHostRead'] = "localhost";
}


/* Config for web
----------------------------------------------------*/
if(strpos(strtolower($sitePath),"cachnauanngon.com") > 0 ){	
    // config duong dan cua site
    $base_url = "http://cachnauanngon.com/";
	// database
    $dbinfo['dbHost']	  = "localhost";
    $dbinfo['dbUser']	  = "thanhtru_cms";
    $dbinfo['dbPass']	  = "Xc3JRBVZ92pGCRVT";
    $dbinfo['dbName']	  = "thanhtru_cms";
    $dbinfo['dbSesName']  = "";
    $dbinfo['dbHostRead'] = "localhost";
}



/* global $oDb, load MysqlDb class
----------------------------------------------------*/
$oDb = new CMysqlDB( $dbinfo );
DB::config($dbinfo);

/* Config language
----------------------------------------------------*/
$language = isset($_REQUEST['language'])? $_REQUEST['language'] : '';
if($language){
    $_SESSION['language'] = $language;
}else if(!isset($_SESSION['language'])){
    $_SESSION['language'] = 'vi';
}