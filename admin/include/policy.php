<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */

global $mt_prefix;

$column = array(
    "name" => array(
        "title"         => "Name",
        "type"          => "textarea:noeditor",
        "row"           => 2,
        "editable"      => false, 
        "required"      => 'Bạn chưa nhập tên điều khoản sử dụng',
        "sufix_title"   => "",
        "show_on_list"  => true,
        "editlink"      => true
    ),
    "content" => array(
        "title"         => "Nội dung",
        "type"          => "textarea",
        "editable"      => false,
        "required"      => "Bạn chưa nhập nội dung",
        "show_on_list"  => false,
        "editlink"      => false
    ),
    "ordering" => array(
        "title"         => "Thứ tự",
        "type"          => "input:int10",
        "editable"      => true,
        "sufix_title"   => "",
        "std"           => getMaxId($mt_prefix.'policy'),
        "show_on_list"  => true
    ),
    "date_created" => array(
        "title"         => "Ngày đăng",
        "type"          => "datetime:current",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
);
