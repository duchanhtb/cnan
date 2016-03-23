<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
$column = array(
    "dining_venues_id" => array(
        "title"         => "Bài viết",
        "type"          => "input:function",
        "function"      => "getDiningVenuesLink",
        "required"      => "Nhập ID địa điểm",
        "searchable"    => true,
        "editlink"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "user_id" => array(
        "title"         => "Người đăng",
        "type"          => "input:function",
        "function"      => "getUserLink",
        "required"      => "Nhập id người đăng",
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
    "content" => array(
        "title"         => "Nội dung",
        "type"          => "input:text",
        "required"      => "Nhập nội dung",
        "searchable"    => true,
        "editlink"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "date_report" => array(
        "title"         => "Ngày tháng",
        "type"          => "input:text",
        "required"      => "Nhập ngày tháng",
        "editable"      => false,
        "sufix_title"   => "",
        "show_on_list"  => true
    )
);

function getDiningVenuesLink($dining_venues_id) {
    $DiningVenues = new Recipe();
    $DiningVenues->read($dining_venues_id);
    $link = createLink('cong-thuc', array('id' => $dining_venues_id, 'title' => $DiningVenues->title));
    return '<a href="' . $link . '" target="_blank">' . $DiningVenues->title . '</a>';
}

function getUserLink($user_id) {
    if ($user_id > 0) {
        $User = new User();
        $User->read($user_id);
        $title = ($User->fullname != '') ? $User->fullname : $User->name;
        $link = createLink('thanh-vien', array('id' => $user_id, 'title' => $title));
        return '<a href="' . $link . '" target="_blank">' . $title . '</a>';
    } else {
        return 'Unknow';
    }

    return 'Unknow';
}
