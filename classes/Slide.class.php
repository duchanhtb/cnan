<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
class Slide extends Base {

    var $fields = array(
        'name',
        'desc',
        'link',
        'img',
        'ordering',
        'date_created',
        'date_updated',
        'status'
    ); //fields in table (excluding Primary Key)
    var $table = "t_slide";

    /**
     * @Desc function get banner 
     * @param $con: condition 
     * @param $sort: sort 
     * @param $order: 
     * @param $page:
     * @return array
     */
    function getAll($sort = false, $order = false) {
        global $allBanner;
        if (isset($allBanner) && count($allBanner) > 0) {
            return $allBanner;
        }
        $con = '';
        if ($sort && $order) {
            $allBanner = $this->get("*", $con, "$sort $order");
            return $allBanner;
        } else {
            $allBanner = $this->get('*', $con, " ordering DESC ");
            return $allBanner;
        }
        return false;
    }

    /**
     * @Desc function get banner 
     * @param string $con: condition 
     * @param string $sort: sort 
     * @param string $order: 
     * @param int $page: page
     * @return array
     */
    function getBanner($con = "", $sort = false, $order = false, $page = 1) {
        $page = ($page > 1 ) ? $page : 1;

        $start = ($page - 1) * ($this->num_per_page);
        if ($sort && $order) {
            return $this->get("*", $con, "$sort $order", $start, $this->num_per_page);
        } else {
            return $this->get("*", $con, " ordering DESC ", $start, $this->num_per_page);
        }

        return false;
    }

    /**
     * @Desc function get banner by position
     * @param string $con: condition 
     * @param string $sort: sort 
     * @param string $order: 
     * @param int $page: page
     * @return array
     */
    function getBannerByPosition($pos) {
        $allBanner = $this->getAll();
        $result = array();
        foreach ($allBanner as $key => $value) {
            if ($value['position'] == $pos && $value['status'] == 1) {
                array_push($result, $value);
            }
        }
        return $result;
    }

}
