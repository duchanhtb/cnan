<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
$column = array(
    "recipe_id" => array(
        "title"         => "Công thức nấu ăn",
        "type"          => "combobox",
        "data"          => getRecipe(),
        "editable"      => false,
        "editlink"      => true,
        "searchable"    => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "img" => array(
        "title"         => "Ảnh",
        "type"          => "input:image",
        "editable"      => false,
        "editlink"      => true,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "alt" => array(
        "title"         => "Thẻ alt",
        "type"          => "input:text",
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "status" => array(
        "title"         => "Hiển thị",
        "type"          => "checkbox",
        "label"         => "Có",
        "unlabel"       => "Không",
        "editable"      => true,
        "show_on_list"  => true,
        "sufix_title"   => ""
    ),
);

function getRecipe() {
    global $oDb;
    $recipe = array();
    $sql = "SELECT * FROM t_recipe WHERE status = 1 ";
    $rc = $oDb->query($sql);
    $rs = $oDb->fetchAll($rc);
    foreach ($rs as $key => $value) {
        $recipe[$value['id']] = $value['title'];
    }
    return $recipe;
}
