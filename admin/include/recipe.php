<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
global $tbl_prefix;
$column = array(
    "cat_id" => array(
        "title"         => "Danh mục",
        "type"          => "checkbox:relate:table",
        "relate"        => "t_recipe_relationships:recipe_id=t_recipe.id:object_id=t_recipe_category.id.name",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "img" => array(
        "title"         => "Ảnh mặc định",
        "type"          => "input:image",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "images" => array(
        "title"         => "Bộ sưu tập ảnh",
        "type"          => "input:multiimages",
        "table"         => array(
            'table_name'    => 't_recipe_images',
            'primary_key'   => 'id',
            'images_url'    => 'img',
            'relate_id'     => 'recipe_id'
        ),
        "editable"      => false,
        "required"      => true,
        "sufix_title"   => "<em>Đăng ảnh công thức</em>",
        "show_on_list"  => false
    ),
    "title" => array(
        "title"         => "Tiêu đề",
        "type"          => "textarea:noeditor",
        "row"           => 2,
        "required"      => true,
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "<em>Nhập tiêu đề công thức món ăn</em>",
        "show_on_list"  => true
    ),
    "youtube_id" => array(
        "title"         => "Youtube ID",
        "type"          => "input:text",
        "required"      => false,
        "searchable"    => true,
        "editlink"      => false,
        "show_on_list"  => false
    ),
    "price" => array(
        "title"         => "Giá thành",
        "type"          => "input:price",
        "searchable"    => true,
        "editable"      => true,
        "editlink"      => true,
        "searchable"    => true,
        "show_on_list"  => true
    ),
    "ingridients" => array(
        "title"         => "Nguyên liệu",
        "type"          => "input:attribute",
        "required"      => true,
        "sufix_title"   => "<em>Thành phần nguyên liệu</em>",
        "show_on_list"  => false
    ),
    "brief" => array(
        "title"         => "Tóm tắt",
        "type"          => "textarea:noeditor",
        "row"           => 4,
        "searchable"    => true,
        "show_on_list"  => false
    ),
    "intruction" => array(
        "title"         => "Giới thiệu",
        "type"          => "textarea",
        "searchable"    => true,
        "show_on_list"  => false
    ),
    "nutrition_facts" => array(
        "title"         => "Dinh dưỡng (K-Cal)",
        "type"          => "input:int10",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "yield" => array(
        "title"         => "Khẩu phần (người)",
        "type"          => "input:int10",
        "editable"      => false,
        "searchable"    => true,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "cooking_time" => array(
        "title"         => "T/Gian (Phút)",
        "type"          => "combobox",
        "data"          => getCookingTime(),
        "editable"      => false,
        "searchable"    => false,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "ordering" => array(
        "title"         => "Thứ tự",
        "type"          => "input:int10",
        "editable"      => true,
        "sufix_title"   => "",
        "std"           => getMaxId($tbl_prefix.'recipe'),
        "show_on_list"  => false
    ),
    "username" => array(
        "title"         => "Người đăng",
        "type"          => "input:hidden",
        "editable"      => false,
        "editlink"      => true,
        "show_on_list"  => false,
        "searchable"    => true,
        "std"           => $_SESSION['admin']['name']
    ),
    "hits" => array(
        "title"         => "Lượt xem",
        "type"          => "input:int10",
        "editable"      => false,
        "sufix_title"   => "",
        "std"           => 0,
        "show_on_list"  => false
    ),
    /*
    "home" => array(
        "title"         => "Trang chủ",
        "type"          => "checkbox",
        "label"         => "Có",
        "unlabel"       => "Không",
        "editable"      => true,
        "show_on_list"  => true,
        "searchable"    => true,
        "sufix_title"   => ""
    ),
     * */
    "traditional" => array(
        "title"         => "Món ăn truyền thống",
        "type"          => "checkbox",
        "label"         => "Có",
        "unlabel"       => "Không",
        "editable"      => true,
        "show_on_list"  => true,
        "searchable"    => true,
    ),    
    "has_picture" => array(
        "title"         => "Bài viết có ảnh",
        "type"          => "checkbox",
        "label"         => "Có",
        "unlabel"       => "Không",
        "editable"      => true,
        "show_on_list"  => false,
    ),
    "has_video" => array(
        "title"         => "Bài viết có Video",
        "type"          => "checkbox",
        "label"         => "Có",
        "unlabel"       => "Không",
        "editable"      => true,
        "show_on_list"  => false,
    ),
    "date_created" => array(
        "title"         => "Ngày đăng",
        "type"          => "datetime:current",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "date_updated" => array(
        "title"         => "Ngày sửa",
        "type"          => "datetime:current",
        "editable"      => false,
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
        "searchable"    => true,
    ),
);



function getCookingTime(){
    $result = array(
        1   => 'Dưới 15 phút  ',
        2   => 'Dưới 30 phút  ',
        3   => 'Dưới 1 tiếng  ',
        4   => 'Trên 1 tiếng  ',
    );
    
    return $result;
}