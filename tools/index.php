<?php
define('ALLOW_ACCESS',TRUE);
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();


/********* load file config **********/
include("config.php");
echo '<meta http-equiv="refresh" content="1" />';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
echo '<pre>';
global $oDb;
/*
$sql = "SELECT post.ID, post.post_title, post.post_date, post.post_content, attachment_meta.meta_value AS upload_relative_path, intruction.meta_value as intruction, ingridients.meta_value as ingridients 
        FROM wp_posts AS post
            JOIN wp_postmeta AS post_meta ON (post_meta.post_id = post.ID AND post_meta.meta_key = '_thumbnail_id')
            JOIN wp_postmeta AS attachment_meta ON (attachment_meta.post_id = post_meta.meta_value AND attachment_meta.meta_key = '_wp_attached_file')
            JOIN wp_postmeta AS intruction ON (intruction.post_id = post.ID AND intruction.meta_key = 'cookingpressinstructions')
            JOIN wp_postmeta AS ingridients ON (ingridients.post_id = post.ID AND ingridients.meta_key = 'cookingpressingridients')
        WHERE post.post_status = 'publish'
            AND post.post_type = 'post'
        ORDER BY post.post_date DESC
        LIMIT 10";
*/
$sql = "SELECT post.ID, post.post_title, attachment_meta.meta_value AS upload_relative_path
        FROM wp_posts AS post
            JOIN wp_postmeta AS post_meta ON (post_meta.post_id = post.ID AND post_meta.meta_key = '_thumbnail_id')
            JOIN wp_postmeta AS attachment_meta ON (attachment_meta.post_id = post_meta.meta_value AND attachment_meta.meta_key = '_wp_attached_file')
          
        WHERE post.post_status = 'publish'
            AND post.post_type = 'post' AND post.chk = '-1'
        ORDER BY post.post_date DESC
        LIMIT 100";
        
$rc = $oDb->query($sql);
$rs = $oDb->fetchAll($rc);
$root_path = "/var/www/html/cachnauanngon.com/wp-content/uploads/";
foreach($rs as $key=>$value){
    $path =  $root_path.$value['upload_relative_path'];
    if(!file_exists($path)){
        $sql = "update wp_posts SET `chk` = '0' where ID = ".$value['ID'];
        $oDb->query($sql);
        echo $sql.'<br/>';
    }else{
        $sql = "update wp_posts SET `chk` = '1' where ID = ".$value['ID'];
        $oDb->query($sql);
        echo $sql.'<br/>';
    }
}



