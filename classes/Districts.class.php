<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
class Districts extends Base {

    var $fields = array(
        "province_id",
        "name",
        "latitude",
        "longitude"
    ); //fields in table (excluding Primary Key)	
    var $table = "m_districts";

    /**
     * @Desc get all district
     * @return array
     */
    function getAllDistrict() {
        return $this->get('*', false);
    }

    /**
     * @Desc get district by province
     * @param string $province_id: id of province
     * @return array
     */
    function getDistrictByProvince($province_id) {
        $con = " AND `province_id` = $province_id ";
        return $this->get('*', $con);
    }

}
