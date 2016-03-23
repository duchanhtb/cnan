<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2014
 */
class Provinces extends Base {

    var $fields = array(
        "name",
        "regions",
        "country_id",
        "country_name",
        "ordering"
    ); //fields in table (excluding Primary Key)	
    var $table = "m_provinces";
    var $table_district = 'm_districts';

    /**
     * @Desc get all province
     * @return array
     */
    function getAllProvince() {
        return $this->get('*', false, 'name');
    }

    /**
     * @Desc get all province with districts
     * @return array
     */
    function getAllProvinceDistrict() {
        $Districts = new Districts();
        $allDistrict = $Districts->getAllDistrict();
        foreach ($allDistrict as $k => $v) {
            $district[$v['province_id']][] = array('id' => $v['id'], 'name' => $v['name']);
        }


        $allProvince = $this->getAllProvince();
        foreach ($allProvince as &$value) {
            $value['district'] = $district[$value['id']];
        }
        return $allProvince;
    }
}
