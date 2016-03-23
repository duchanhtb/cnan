<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
global $tbl_prefix;

$column = array(
    "parent_id" => array(
        "title"         => "Danh mục cha",
        "type"          => "combobox",
        "data"          => getNewsCategory(),
        "editable"      => false,
        "searchable"    => true,
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
        "show_on_list"  => true
    ),
    "description" => array(
        "title"         => "Miêu tả",
        "type"          => "textarea:noeditor",
        "row"           => 2,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "ordering" => array(
        "title"         => "Thứ tự",
        "type"          => "input:int10",
        "editable"      => true,
        "std"           => getMaxId($tbl_prefix.'blog_category'),
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "meta_title" => array(
        "title"         => "Meta title SEO",
        "type"          => "textarea:noeditor",
        "row"           => 2,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "meta_keyword" => array(
        "title"         => "Từ khóa SEO",
        "type"          => "textarea:noeditor",
        "row"           => 2,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "meta_description" => array(
        "title"         => "Description SEO",
        "type"          => "textarea:noeditor",
        "row"           => 2,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "ordering" => array(
        "title"         => "Thứ tự",
        "type"          => "input:int10",
        "editable"      => true,
        "sufix_title"   => "",
        "std"           => getMaxId($tbl_prefix.'blog_category'),
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
    ),
);


/* get news category ( t_blog_category)
  -------------------------------------------------------------------------- */

function getNewsCategory() {
    global $oDb;
    $arr_category = array();
    $arr_category[0] = 'Trang chủ';
    $BlogCategory = new BlogCategory();
    
    $sql = "SELECT `id`, `name`, `parent_id` FROM ".$BlogCategory->table." WHERE 1 ";
    $sql .= 'ORDER BY `parent_id` ASC ';

    $rs = $oDb->query($sql);
    $allCat = $oDb->fetchAll($rs);
    foreach ($allCat as $key => $value) {
        if ($value['parent_id'] == 0) {
            $name = '----' . $value['name'];
            $id = $value['id'];
            $arr_category[$id] = $name;

            // sub1
            foreach ($allCat as $key1 => $value1) {
                if ($value1['parent_id'] == $value['id']) {
                    $name = '---------' . $value1['name'];
                    $id = $value1['id'];
                    $arr_category[$id] = $name;

                    // sub2
                    foreach ($allCat as $key2 => $value2) {
                        if ($value2['parent_id'] == $value1['id']) {
                            $name = '------------' . $value2['name'];
                            $id = $value2['id'];
                            $arr_category[$id] = $name;

                            // sub3
                            foreach ($allCat as $key3 => $value3) {
                                if ($value3['parent_id'] == $value2['id']) {
                                    $name = '----------------' . $value3['name'];
                                    $id = $value3['id'];
                                    $arr_category[$id] = $name;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $arr_category;
}

function addDrashRecursive($arr, $parent_id = 0, $id, $drash = '----') {
    $result = array();
    if (count($arr) > 0) {
        foreach ($arr as $key => $value) {
            if ($value[$id]) {
                
            }
        }
    }
    return $result;
}
