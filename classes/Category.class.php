<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
class Category extends Base {

    var $fields = array(
        "id",
        "parent_id",
        "name",
        "img",
        "home",
        "status",
        "ordering",
        "description",
        "meta_title",
        "meta_keyword",
        "meta_description"
    ); //fields in table (excluding Primary Key)	
    var $table = "t_category";

    /**
     * @Desc get category with condition 
     * @param string $sort: field want to sort 
     * @param string $order: DESC or ASC
     * @return array
     */
    function getAll($sort = "ordering", $order = "DESC") {
        global $allCat;
        if (is_array($allCat) && count($allCat) > 0) {
            return $allCat;
        }
        $this->category = $this->get("*", false, "$sort $order, parent_id ASC ");
        $allCat = $this->category;
        return $this->category;
    }

    /**
     * @Desc get category with condition 
     * @param string $con: condition of the query, default is false
     * @param string $order: field in the table want to sort, default is ordering field
     * @param string $order: DESC or ASC
     * @return array
     */
    function getCategory($con = false, $sort = "ordering", $order = "DESC", $page = 1) {
        $page = ($page > 1 ) ? $page : 1;
        $start = ($page - 1) * ($this->num_per_page);
        return $this->get("*", $con, "$sort $order, ordering asc ", $start, $this->num_per_page);
    }

    /**
     * @Desc get list id of category
     * @param array $allCat: array of category
     * @param int $parent_id: parent id  
     * @return string example: 1,5,23,3
     */
    function getListSubCategory($allCat, $parent_id = 0) {
        $list_id = $parent_id . ',';
        if (!is_array($allCat) || count($allCat) <= 0) {
            return "";
        }

        $temp_array = $allCat;
        foreach ($allCat as $key => $value) {
            if ($value['parent_id'] == $parent_id) {
                unset($temp_array[$key]);
                $list_id .= $this->getListSubCategory($temp_array, $value['id']);
            }
        }
        return $list_id;
    }

    /**
     * @Desc get list id of category
     * @param array $allCat: array of category
     * @param int $parent_id: parent id, default value is 0  
     * @return array
     */
    function getSubCat($allCat, $parent_id = 0) {

        // the function will return this variable
        $result = array();

        if (!is_array($allCat) || count($allCat) <= 0) {
            return "";
        }

        // temple array 
        $temp_array = $allCat;
        foreach ($allCat as $key => $value) {
            if ($value['parent_id'] == $parent_id && $value['status'] == 1) {
                array_push($result, $value);
            } else {
                continue;
            }
        }
        return $result;
    }

    /**
     * @Desc get parent id of category
     * @param array $allCat: array of category
     * @param int $parent_id: parent id  
     * @return array
     */
    function getParentCat($allCat, $cid) {
        // check array input
        if (!is_array($allCat) || count($allCat) <= 0) {
            return array(); // return empry array 
        }

        foreach ($allCat as $key => $value) {
            $subCat = $this->getSubCat($allCat, $value['id']);
            foreach ($subCat as $k => $v) {
                if ($v['id'] == $cid) {
                    return $value;
                }
            }
        }
        return false;
    }

    /**
     * @Desc get category info based on category id
     * @param array $allCat: array of category
     * @param int $cid: id of category 
     * @package string $field: fiend want to get, example: name
     * @return mix
     */
    function getCategoryInfo($allCat, $cid, $field = 'name') {
        // check input array
        if (!is_array($allCat) || count($allCat) <= 0) {
            return false; // return nothing if dirty input
        }

        foreach ($allCat as $value) {
            if ($value['id'] == $cid) {
                return $value[$field];
            }
        }

        return false;
    }

}
