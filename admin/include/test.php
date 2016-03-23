<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
$column = array(
    "ID" => array(
        "title"         => "ID",
        "type"          => "input:text",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true,
        "editable"      => false,
        "editlink"      => false
    ),
    "name" => array(
        "title"         => "Name",
        "type"          => "input:text",
        "editable"      => false, 
        "sufix_title"   => "",
        "show_on_list"  => true,
        "editable"      => false,
        "editlink"      => false
    ),
    "cat_id" => array(
        "title"         => "Category ID",
        "type"          => "input:text",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true,
        "editable"      => true,
        "editlink"      => false
    ),
);
