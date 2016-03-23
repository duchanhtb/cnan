<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
$column = array(
    "avatar" => array(
        "title"         => "Ảnh avatar",
        "type"          => "input:image",
        "std"           => "/uploads/images/useravatar/default-avatar.png",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "username" => array(
        "title"         => "Tên đăng nhập",
        "type"          => "textarea:noeditor",
        "row"           => 1,
        "searchable"    => true,
        "editlink"      => true,
        "show_on_list"  => true
    ),
    "email" => array(
        "title"         => "Email",
        "type"          => "input:text",
        "required"      => "Nhập email",
        "searchable"    => true,
        "editable"      => false,
        "editlink"      => true,
        "show_on_list"  => true
    ),
    "fullname" => array(
        "title"         => "Họ và tên",
        "type"          => "input:text",
        "searchable"    => true,
        "editlink"      => false,
        "show_on_list"  => true
    ),
    "phone" => array(
        "title"         => "Điện thoại",
        "type"          => "input:text",
        "searchable"    => true,
        "editlink"      => false,
        "show_on_list"  => true
    ),
    "province_id" => array(
        "title"         => "Tỉnh thành",
        "type"          => "combobox:relate:int",
        "editable"      => false,
        "searchable"    => true,
        "relate"        => "m_provinces.name.id",
        "show_on_list"  => false
    ),
    "career_id" => array(
        "title"         => "Nghề nghiệp",
        "type"          => "combobox:relate:int",
        "editable"      => false,
        "searchable"    => true,
        "relate"        => "m_career.name.id",
        "show_on_list"  => false
    ),
    "question_id" => array(
        "title"         => "Câu hỏi bí mật",
        "type"          => "combobox:relate:int",
        "editable"      => false,
        "searchable"    => true,
        "relate"        => "m_security_questions.name.id",
        "show_on_list"  => false
    ),
    "answer" => array(
        "title"         => "Câu trả lời",
        "type"          => "input:text",
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => false
    ),
    "address" => array(
        "title"         => "Địa chỉ",
        "type"          => "input:text",
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => false
    ),
    "region_name" => array(
        "title"         => "Tên vùng",
        "type"          => "input:text",
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => false
    ),
    "gender" => array(
        "title"         => "Giới tính",
        "type"          => "combobox",
        "data"          => array(0 => 'Nữ', 1 => 'Nam'),
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => true
    ),
    "date_register" => array(
        "title"         => "Ngày đăng ký",
        "type"          => "datetime:current",
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => false
    ),
    "birthday" => array(
        "title"         => "Ngày sinh",
        "type"          => "datetime:current",
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => false
    ),
    "slogan" => array(
        "title"         => "Khẩu hiệu",
        "type"          => "input:text",
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => false
    ),
    "website" => array(
        "title"         => "Website",
        "type"          => "textarea:noeditor",
        "row"           => 1,
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => false
    ),
    "link_facebook" => array(
        "title"         => "Link Facebook",
        "type"          => "textarea:noeditor",
        "row"           => 1,
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => false
    ),
    "link_googleplus" => array(
        "title"         => "Link Google",
        "type"          => "textarea:noeditor",
        "row"           => 1,
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => false
    ),
    "link_twitter" => array(
        "title"         => "Link Twitter",
        "type"          => "textarea:noeditor",
        "row"           => 1,
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => false
    ),
    "facebook_id" => array(
        "title"         => "ID Facebook",
        "type"          => "input:text",
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => false
    ),
    "googleplus_id" => array(
        "title"         => "ID Google Plus",
        "type"          => "input:text",
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => false
    ),
    "twitter_id" => array(
        "title"         => "ID Twitter",
        "type"          => "input:text",
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => false
    ),
    "yahoo_id" => array(
        "title"         => "ID Yahoo",
        "type"          => "input:text",
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => false
    ),
    "skype_id" => array(
        "title"         => "ID Skype",
        "type"          => "input:text",
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => false
    ),
    "point" => array(
        "title"         => "Điểm",
        "type"          => "input:int10",
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => true
    ),
    "last_login_time" => array(
        "title"         => "Đăng nhập lần cuối",
        "type"          => "datetime:current",
        "searchable"    => false,
        "editlink"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "last_login_ip" => array(
        "title"         => "IP Đăng nhập lần cuối",
        "type"          => "input:text",
        "std"           => getUserIp(),
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => true
    ),
    "user_type" => array(
        "title"         => "User đăng ký từ",
        "type"          => "combobox",
        "data"          => array('' => 'Đăng ký', 'facebook' => 'Facebook', 'google' => 'Google'),
        "searchable"    => false,
        "editlink"      => false,
        "show_on_list"  => true
    ),
    "status" => array(
        "title"         => "Trạng thái",
        "type"          => "checkbox",
        "label"         => "Có",
        "unlabel"       => "Không",
        "editable"      => true,
        "show_on_list"  => true,
        "sufix_title"   => ""
    )
);