<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
$column = array(
    "name" => array(
        "title"         => "Tên đăng nhập",
        "type"          => "textarea:noeditor",
        "row"           => 1,
        "searchable"    => true,
        "editlink"      => true,
        "required"      => "Nhập tên đăng nhập",
        "show_on_list"  => true
    ),
    "pass" => array(
        "title"         => "Mật khẩu",
        "type"          => "input:password",
        "required"      => "Nhập mật khẩu",
        "editable"      => false,
        "editlink"      => false,
        "searchable"    => false,
        "show_on_list"  => false
    ),
    "email" => array(
        "title"         => "Email",
        "type"          => "textarea:noeditor",
        "row"           => 1,
        "required"      => "Nhập email",
        "editable"      => false,
        "editlink"      => true,
        "show_on_list"  => true
    ),
    "level" => array(
        "title"         => "Cấp độ",
        "type"          => "combobox",
        "data"          => array(1 => 'User', 1 => 'Mod', 2 => 'Admin', 3 => 'Supper Admin'),
        "editable"      => false,
        "searchable"    => true,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "last_login_time" => array(
        "title"         => "Đăng nhập",
        "type"          => "datetime:current",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "last_login_ip" => array(
        "title"         => "Ip đăng nhập lần cuối",
        "type"          => "input:hidden",
        "std"           => getUserIp(),
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
);
