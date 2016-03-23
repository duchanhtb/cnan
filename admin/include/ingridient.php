<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
$column = array(
    "img" => array(
        "title"         => "Ảnh miêu tả",
        "type"          => "input:image",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "name" => array(
        "title"         => "Tên nguyên liệu",
        "type"          => "textarea:noeditor",
        "row"           => 2,
        "required"      => "Nhập tên nguyên liệu",
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "group" => array(
        "title"         => "Nhóm nguyên liệu",
        "type"          => /*"input:int10",*/"checkbox:relate:int",
        "required"      => "Nhập tên nhóm nguyên liệu",
        "editable"      => true,
        "relate"        => "m_ingridient_group.name.id",
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "slug" => array(
        "title"         => "Slug",
        "type"          => "input:text",
        "required"      => "Nhập slug",
        "searchable"    => false,
        "editlink"      => false,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "is_basic" => array(
        "title"         => "Nguyên liệu cơ bản",
        "type"          => "checkbox",
        "label"         => "Có",
        "unlabel"       => "Không",
        "editable"      => true,
        "show_on_list"  => true,
        "sufix_title"   => ""
    )
    
);