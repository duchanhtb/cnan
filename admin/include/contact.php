<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
$column = array(
    "fullname" => array(
        "title"         => "Họ tên",
        "type"          => "input:text",
        "required"      => "Nhập họ tên",
        "searchable"    => true,
        "editlink"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "email" => array(
        "title"         => "Email ",
        "type"          => "input:text",
        "required"      => "Nhập email",
        "searchable"    => true,
        "editlink"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "phone" => array(
        "title"         => "Số điện thoại",
        "type"          => "input:text",
        "required"      => "Nhập số điện thoại",
        "searchable"    => true,
        "editlink"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "title" => array(
        "title"         => "Tiêu đề",
        "type"          => "input:text",
        "required"      => "Nhập tiêu đề",
        "searchable"    => true,
        "editlink"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "content" => array(
        "title"         => "Nội dung",
        "type"          => "input:text",
        "required"      => "Nhập nội dung",
        "searchable"    => true,
        "editlink"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "date" => array(
        "title"         => "Ngày tháng",
        "type"          => "input:text",
        "required"      => "Nhập ngày tháng",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    /* ,    
      "status"	=> array(
      "title"		=> "Hiển thị",
      "type"		=> "checkbox",
      "label"		=> "Có",
      "unlabel"         => "Không",
      "editable"	=> true,
      "show_on_list"    => true,
      "sufix_title"     => ""
      )
     */
);
