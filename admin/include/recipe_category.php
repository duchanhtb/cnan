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
        "title"         => "Tên danh mục",
        "type"          => "textarea:noeditor",
        "row"           => 2,
        "required"      => "Nhập tên danh mục",
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "Nhâp tên danh mục",
        "show_on_list"  => true
    ),
    "parent_id" => array(
        "title"         => "Danh mục cha",
        "type"          => "combobox",
        "data"          => getRecipeCategory(),
        "editable"      => false,
        "searchable"    => true,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "ordering" => array(
        "title"         => "Thứ tự",
        "type"          => "input:int10",
        "editable"      => true,
        "sufix_title"   => "",
        "std"           => getMaxId($tbl_prefix.'recipe_category'),
        "show_on_list"  => true
    ),
    "status" => array(
        "title"         => "Hiển thị",
        "type"          => "checkbox",
        "label"         => "Có",
        "unlabel"       => "Không",
        "editable"      => true,
        "show_on_list"  => true,
        "searchable"    => true,
    )
);


/* get news category ( t_blog_category)
  -------------------------------------------------------------------------- */

function getRecipeCategory() {
    global $oDb;
    $arr_category = array();
    $arr_category[0] = 'Trang chủ';

    $RecipeCategory = new RecipeCategory();
    $con = " AND id != 0 ";
    $allCat = $RecipeCategory->get('*', $con, ' `name` ASC');

    foreach ($allCat as $key => $value) {
        if ($value['parent_id'] == 0) {
            $name = $value['name'];
            $id = $value['id'];
            $arr_category[$id] = $name;
        }
    }
    return $arr_category;
}
