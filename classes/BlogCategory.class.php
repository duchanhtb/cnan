<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
class BlogCategory extends Base {

    var $fields = array(
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
    
    var $table = "t_blog_category";

    /**
     * @Desc get category with condition 
     * @param string $sort: field want to sort 
     * @param string $order: DESC or ASC
     * @param string $con: condition     
     * @return array
     */
    function getAll($sort = false, $order = false) {
        global $allCat;
        if (is_array($allCat) && count($allCat) > 0) {
            return $allCat;
        }

        if ($sort && $order) {
            $allCat = $this->get("*", $con, "$sort $order ");
        } else {
            $allCat = $this->get("*", $con, "ordering DESC, parent_id ASC ");
        }
        return $allCat;
    }

    /**
     * @Desc get category with condition 
     * @param string $con: condition
     * @param string $order: field want to sort
     * @param string $order: DESC or ASC
     * @return array
     */
    function getCategory($con = "", $sort = "id", $order = "DESC", $page = 1) {
        $page = ($page > 1 ) ? $page : 1;

        $start = ($page - 1) * ($this->num_per_page);

        if ($sort && $order) {
            return $this->get("*", $con, "$sort $order, ordering asc ", $start, $this->num_per_page);
        } else {
            return $this->get("*", $con, " id DESC, ordering DESC ", $start, $this->num_per_page);
        }
        return false;
    }

    /**
     * @Desc get list id of category
     * @param array $allCat: array of category
     * @param int $parent_id: parent id  
     * @return string example: 1,5,23,3
     */
    function getListSubCategory($allCat, $parent_id = 0) {
        $list_id = $parent_id . ',';
        if (is_array($allCat) && count($allCat) > 0) {
            $temp_array = $allCat;
            foreach ($allCat as $key => $value) {
                if ($value['parent_id'] == $parent_id) {
                    unset($temp_array[$key]);
                    $list_id .= $this->getListSubCategory($temp_array, $value['id']);
                }
            }
        }
        //$list_id = trim($list_id); 
        return $list_id;
    }

    /**
     * @Desc get list id of category
     * @param array $allCat: array of category
     * @param int $parent_id: parent id  
     * @return string example: 1,5,23,3
     */
    function getSubCat($allCat, $parent_id = 0, $status = false) {
        $result = array();
        if (is_array($allCat) && count($allCat) > 0) {
            $temp_array = $allCat;
            foreach ($allCat as $key => $value) {
                if ($value['parent_id'] == $parent_id) {
                    if ($status === false) {
                        array_push($result, $value);
                    } else {
                        if ($value['status'] == 1) {
                            array_push($result, $value);
                        } else {
                            continue;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @Desc get parent id of category
     * @param array $allCat: array of category
     * @param int $parent_id: parent id  
     * @return string example: 1,5,23,3
     */
    function getParentCat($allCat, $cid) {
        if (is_array($allCat) && count($allCat) > 0) {
            foreach ($allCat as $key => $value) {
                $subCat = $this->getSubCat($allCat, $value['id']);
                foreach ($subCat as $k => $v) {
                    if ($v['id'] == $cid) {
                        return $value;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @Desc get category info based on cateogry id
     * @param array $allCat: array of category
     * @param int $cid: id of category 
     * @package string $field: fiend want to get
     * @return string
     */
    function getCategoryInfo($allCat, $cid, $field = 'name') {
        if (count($allCat) > 0) {
            foreach ($allCat as $k => $value) {
                if ($value['id'] == $cid) {
                    return $value[$field];
                }
            }
        }
        return "";
    }

}
