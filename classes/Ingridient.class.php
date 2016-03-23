<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2014
 */
class Ingridient extends Base {

    var $fields = array(
        "img",
        "group",
        "name",
        "slug",
        "is_basic"
    ); //fields in table (excluding Primary Key)
    var $table = "m_ingridient";

    /**
     * @Desc get ingridient by name
     * @param string $name: name of ingridient
     * @return array
     */
    function getIngridientByName($name) {
        $con = " AND INSTR('$name', `name`) AND `is_basic` = 1 ";
        $record = $this->getRecord('*', $con);
        
        return $record;
    }

    /**
     * @Desc check ingridient exists by slug
     * @return array
     */
    function checkSlugExists($slug) {
        $record = $this->getRecord('*', ' AND `slug` = "' . $slug . '"');
        if ($record) {
            return $record;
        }
        return false;
    }
    
    /**
     * @Desc get other ingridient
     * @return array
     */
    function getOtherIngridient($ingridient_id){
        $con = false;
        if($ingridient_id){
            $this->read($ingridient_id);
            $con = " AND id != $ingridient_id AND `group` = $this->group AND `is_basic` = 1 ";
        }
        return $this->get("*", $con, "RAND() , `img` DESC ", 0, 12);
    }
    
    
    /**
     * @Desc get ingridient by group
     * @return array
     */
    function getIngridientByGroup($group_id){
        $con = " AND `group` = $group_id AND `is_basic` = 1 ";
        return $this->get("*", $con, "RAND() , `img` DESC ", 0, 12);
    }
    
    
    
    /**
     * @Desc insert ingridient if not exists
     * @return int
     */
    function insertIngridient($name){
        $slug_ingridient = title_url($name);
        $check = $this->checkSlugExists($slug_ingridient);
        if(!$check){
            $this->name    =   trim($name);
            $this->slug    =   trim($slug_ingridient);
            return $this->insert();
        }else{
            return $check['id'];
        }
        
        return false;
    }
    
}
