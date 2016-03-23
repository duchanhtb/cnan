<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
$column = array(
    "key" => array(
        "title"         => "Key",
        "type"          => "input:text",
        "required"      => "Nhập key",
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "vi" => array(
        "title"         => trans('vietnamese'),
        "type"          => "textarea:noeditor",
        "row"           => 2,
        "required"      => "Nhập tiếng Việt",
        "searchable"    => true,
        "editlink"      => false,
        "editable"      => true,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "en" => array(
        "title"         => trans('english'),
        "type"          => "textarea:noeditor",
        "row"           => 2,
        "required"      => "Nhập tiếng Anh",
        "searchable"    => true,
        "editlink"      => false,
        "editable"      => true,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
);
