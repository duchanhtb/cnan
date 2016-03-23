<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */

global $tbl_prefix;

$column = array(
    "name" => array(
        "title"         => "Tên địa điểm",
        "type"          => "textarea:noeditor",
        "row"           => 2,
        "required"      => "Nhập tên địa điểm",
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "img" => array(
        "title"         => "Ảnh miêu tả",
        "type"          => "input:image",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "images" => array(
        "title"         => "Ảnh demo Full",
        "type"          => "input:multiimages",
        "table"         => array(
            'table_name'    => 't_dining_venues_images',
            'primary_key'   => 'id',
            'images_url'    => 'img',
            'relate_id'     => 'dining_venues_id'
        ),
        "editable"      => false,
        "required"      => true,
        "sufix_title"   => "<em>Đăng ảnh công thức</em>",
        "show_on_list"  => false
    ),
    "brief" => array(
        "title"         => "Nội dung tóm tắt",
        "type"          => "textarea:noeditor",
        "row"           => 4,
        "required"      => "Nhập nội dung tóm tắt",
        "searchable"    => false,
        "editlink"      => false,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "content" => array(
        "title"         => "Nội dung",
        "type"          => "textarea",
        "required"      => "Nhập nội dung",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "address" => array(
        "title"         => "Địa chỉ",
        "type"          => "input:text",
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "phone" => array(
        "title"         => "Điện thoại",
        "type"          => "input:text",
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "mobile" => array(
        "title"         => "Di động",
        "type"          => "input:text",
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "",
        "show_on_list" => false
    ),
    "email" => array(
        "title"         => "Email",
        "type"          => "input:text",
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "website" => array(
        "title"         => "Website",
        "type"          => "textarea:noeditor",
        "row"           => 1,
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "facebook" => array(
        "title"         => "Facebook URL",
        "type"          => "input:text",
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "yahoo" => array(
        "title"         => "Yahoo",
        "type"          => "input:text",
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "skype" => array(
        "title"         => "Skype",
        "type"          => "input:text",
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "province_id" => array(
        "title"         => "Tỉnh thành",
        "type"          => "combobox",
        "editable"      => false,
        "searchable"    => true,
        "data"          => getProvince(),
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "latitude" => array(
        "title"         => "Vĩ độ",
        "type"          => "input:text",
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "longitude" => array(
        "title"         => "Kinh độ",
        "type"          => "input:text",
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "hits" => array(
        "title"         => "Lượt xem",
        "type"          => "input:int10",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "username" => array(
        "title"         => "Người đăng",
        "type"          => "input:hidden",
        "editable"      => false,
        "editlink"      => false,
        "show_on_list"  => true,
        "searchable"    => true,
        "std"           => $_SESSION['admin']['name']
    ),
    "date_created" => array(
        "title"         => "Ngày đăng",
        "type"          => "datetime:current",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "date_updated" => array(
        "title"         => "Ngày sửa",
        "type"          => "datetime:current",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => false
    ),
    "ordering" => array(
        "title"         => "Thứ tự",
        "type"          => "input:int10",
        "editable"      => true,
        "std"           => getMaxId($tbl_prefix.'dining_venues'),
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "home" => array(
        "title"         => "Trang chủ",
        "type"          => "checkbox",
        "label"         => "Có",
        "unlabel"       => "Không",
        "editable"      => true,
        "show_on_list"  => true,
        "sufix_title"   => ""
    ),
    "meta_title" => array(
        "title"         => "Meta title",
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
    "home" => array(
        "title"         => "Trang chủ",
        "type"          => "checkbox",
        "label"         => "Có",
        "unlabel"       => "Không",
        "editable"      => true,
        "show_on_list"  => true,
        "sufix_title"   => ""
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

function getProvince() {
    global $oDb;
    $result = array();
    $sql = "SELECT id, name  FROM m_provinces WHERE 1 ORDER BY ordering DESC, `name` ASC ";
    $rc = $oDb->query($sql);
    $rs = $oDb->fetchAll($rc);
    foreach ($rs as $key => $value) {
        $result[$value['id']] = $value['name'];
    }
    return $result;
}