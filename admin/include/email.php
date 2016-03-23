<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
$column = array(
    "name" => array(
        "title"         => "Họ tên",
        "type"          => "textarea:noeditor",
        "row"           => 1,
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true,
        "editable"      => false,
        "editlink"      => false
    ),
    "email" => array(
        "title"         => "Email",
        "type"          => "textarea:noeditor",
        "row"           => 1,
        "editable"      => false,
        "required"      => "",
        "show_on_list"  => true,
        "editlink"      => false
    ),
    "date" => array(
        "title"         => "Ngày dang ký",
        "type"          => "input:datetime",
        "editable"      => false,
        "required"      => "",
        "show_on_list"  => true,
        "editlink"      => false
    ),
);
