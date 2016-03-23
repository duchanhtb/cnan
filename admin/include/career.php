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
        "title"         => "Nghề nghiệp",
        "type"          => "textarea:noeditor",
        "row"           => 2,
        "required"      => "Nhập nghề nghiệp",
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "ordering" => array(
        "title"         => "Thứ tự",
        "type"          => "input:int10",
        "editable"      => true,
        "std"           => getMaxId($mt_prefix.'career'),
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
