<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 * @desc class about news
 */
class DiningVenues extends Base {

    var $fields = array(
        'user_id',
        'username',
        'name',
        'brief',
        'content',
        'address',
        'phone',
        'mobile',
        'email',
        'website',
        'facebook',
        'yahoo',
        'skype',
        'province_id',
        'district_id',
        'latitude',
        'longitude',
        'date_created',
        'date_updated',
        'img',
        'hits',
        'home',
        'status',
        'ordering',
        'meta_title',
        'meta_keyword',
        'meta_description',
    ); //fields in table (excluding Primary Key)	
    var $table = "t_dining_venues";

    function getDiningVenuesByRegion($region) {
        global $oDb;

        $result = array();
        $Province = new Provinces();
        $sql = "SELECT t_dining_venues.*, m_provinces.name as province_name FROM t_dining_venues 
                LEFT JOIN m_provinces ON t_dining_venues.`province_id` = m_provinces.`id`
                WHERE m_provinces.`regions` = '$region' AND t_dining_venues.status = 1
                LIMIT 0,5 ";

        $rc = $oDb->query($sql);
        $result = $oDb->fetchAll($rc);

        return $result;
    }

    /**
     * @Desc get recipe by user_id without current_id
     * @param $user_id: user id
     * @param $current_id: current_id 
     * @param $num_get: number record 
     * @return array
     */
    function getDiningVenuesUser($user_id, $start = 0, $num_get = 10) {
        return $this->get("*", " AND `status` = 1 AND user_id = $user_id ", "ordering DESC", $start, $num_get);
    }

    /**
     * @Desc count recipe by UserId
     * @param int $user_id: User Id
     * @return int
     */
    function countDiningVenuesUser($user_id) {
        global $oDb;
        $sql = "SELECT COUNT( * ) as tt 
				FROM $this->table
				WHERE user_id = $user_id ";
        $rc = $oDb->query($sql);
        $rs = $oDb->fetchArray($rc);
        return $rs['tt'];
    }

    /**
     * @Desc get dining venues by location
     * @param none
     * @return int
     */
    function getDiningVenuesByLocation($start = 0, $num_get = 12) {
        global $oDb;
        $result = array();

        $geoInfo = getGeoInfo();
        $latitude =  isset($geoInfo['latitude']) ? (int)$geoInfo['latitude'] : 0 ;
        $longitude = isset($geoInfo['longitude']) ? (int) $geoInfo['longitude'] : 0;
        if ($latitude && $longitude) {
            $con = " AND status = 1 ";
            $sql = "SELECT *, SQRT(POW((`latitude` - $latitude), 2) + POW((`longitude` - $longitude), 2)) AS `distance` FROM t_dining_venues "
                    . " WHERE 1 $con ORDER BY `distance` ASC "
                    . " LIMIT $start, $num_get";
            $rc = $oDb->query($sql);
            $result = $oDb->fetchAll($rc);
        } else {
            $con = " AND status = 1 ";
            $sql = "SELECT * FROM t_dining_venues "
                    . " WHERE 1 $con ORDER BY RAND() "
                    . " LIMIT $start, $num_get";
            $rc = $oDb->query($sql);
            $result = $oDb->fetchAll($rc);
        }

        return $result;
    }

}
