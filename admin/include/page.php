<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
$column = array(
    "name" => array(
        "title"         => trans('page_name'),
        "type"          => "input:function",
        "editable"      => false,
        "required"      => trans('enter_page_name'),
        "sufix_title"   => "<em style=\"color:red\">Tuyệt đối không thay đổi phần này</em>",
        "show_on_list"  => true,
        "function"      => 'pageLink'
    ),
    "layout" => array(
        "title"         => "Layout",
        "type"          => "input:text",
        "editable"      => false,
        "required"      => "Nhập layout",
        "sufix_title"   => "<em style=\"color:red\">Tuyệt đối không thay đổi phần này</em>",
        "show_on_list"  => true,
        "editable"      => false,
        "editlink"      => true
    ),
    "meta_title" => array(
        "title"         => "Title trang <meta tags>",
        "type"          => "textarea:noeditor",
        "editable"      => true,
        "required"      => "Title",
        "show_on_list"  => false,
        "editlink"      => false
    ),
    "meta_keyword" => array(
        "title"         => "Nhập keywords <meta tags>",
        "type"          => "textarea:noeditor",
        "editable"      => true,
        "required"      => "",
        "show_on_list"  => false,
        "editlink"      => false
    ),
    "meta_description" => array(
        "title"         => "Nhập description <meta tags>",
        "type"          => "textarea:noeditor",
        "editable"      => true,
        "editable"      => true,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "act" => array(
        "title"         => "Cắm module",
        "type"          => "input:function",
        "function"      => "pageAction",
        "editable"      => false,
        "required"      => "Bạn chưa nhập layout",
        "sufix_title"   => "",
        "show_on_list"  => true,
        "editable"      => false,
        "editlink"      => false
    )
);


function pageAction($id, $act){
    $link = "page.php?id=".$id;
    switch ($act){
        case "list":
            return '<a href="'.$link.'"><img src="images/icon-module.png" alt="add module"></a>';
            break;
        
        case "add":
        case "edit":
            return '<img src="images/icon-module.png" alt="add module">';
            break;
    }
}