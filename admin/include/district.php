<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
$column = array(
    "name" => array(
        "title"         => "Tên Quận/Huyện",
        "type"          => "textarea:noeditor",
        "row"           => 2,
        "required"      => "Nhâp tên Quận/Huyện",
        "searchable"    => true,
        "editlink"      => true,
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
    "province_id" => array(
        "title"         => "Tỉnh thành",
        "type"          => "combobox",
        "required"      => "Chọn tỉnh thành",
        "editable"      => false,
        "searchable"    => true,
        "data"          => getProvince(),
        "sufix_title"   => "",
        "show_on_list"  => true
    ),
);

function getProvince() {
    global $oDb;
    $result = array();
    $sql = "SELECT id, name  FROM m_provinces WHERE 1 ";
    $rc = $oDb->query($sql);
    $rs = $oDb->fetchAll($rc);
    foreach ($rs as $key => $value) {
        $result[$value['id']] = $value['name'];
    }
    return $result;
}

?>