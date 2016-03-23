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
        "title"         => "Tên tỉnh",
        "type"          => "textarea:noeditor",
        "row"           => 2,
        "required"      => "Nhâp tên tỉnh",
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    /*
    "regions" => array(
        "title"         => "Vùng miền",
        "type"          => "input:text",
        "editable"      => true,
        "searchable"    => true,
        "editlink"      => false,
        "sufix_title"   => "<em>Nhâp vùng miền</em>",
        "show_on_list"  => true
    ),
    "country_id" => array(
        "title"         => "ID Country",
        "type"          => "input:int10",
        "required"      => false,
        "searchable"    => true,
        "editlink"      => false,
        "show_on_list" => true
    ),
    "country_name" => array(
        "title"         => "Tên nước",
        "type"          => "input:text",
        "required"      => false,
        "searchable"    => true,
        "editlink"      => false,
        "show_on_list"  => true
    ),
     * */
    "ordering" => array(
        "title"         => "Thứ tự",
        "type"          => "input:int10",
        "editable"      => true,
        "std"         => getMaxId($mt_prefix.'provinces'),
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
);
