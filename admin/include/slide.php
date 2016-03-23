<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */

global $tbl_prefix;

$column = array(
    "img" => array(
        "title"         => "Ảnh",
        "type"          => "input:image",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "name" => array(
        "title"         => "Tên slider",
        "type"          => "input:text",
        "searchable"    => true,
        "editlink"      => true,
        "show_on_list"  => true
    ),
    "link" => array(
        "title"         => "Liên kết",
        "type"          => "input:text",
        "required"      => "Nhập liên kết",
        "editable"      => false,
        "sufix_title"   => "",
        "std"           => "#",
        "show_on_list"  => true
    ),
    "desc" => array(
        "title"         => "Miêu tả",
        "type"          => "input:text",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "date_created" => array(
        "title"         => "Ngày tạo",
        "type"          => "datetime:current",
        "required"      => "Nhập ngày tạo",
        "editable"      => false,
        "sufix_title"   => "",
        "searchable"    => false,
        "show_on_list"  => true
    ),
    "date_updated" => array(
        "title"         => "Ngày sửa",
        "type"          => "hidden",
        "editable"      => false,
        "sufix_title"   => "",
        "searchable"    => false,
        "show_on_list"  => true,
        "std"           => date("Y-m-d H:i", time())
    ),
    "ordering" => array(
        "title"         => "Thứ tự",
        "type"          => "input:int10",
        "editable"      => true,
        "std"           => getMaxId($tbl_prefix.'slide'),
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "status" => array(
        "title"         => "Hiển thị",
        "type"          => "checkbox",
        "label"         => "Có",
        "unlabel"       => "Không",
        "editable"      => true,
        "show_on_list"  => true,
        "sufix_title"   => ""
    )
);
