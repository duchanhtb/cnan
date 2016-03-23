<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class DiningVenousImage extends Base {

    var $fields = array(
        "dining_venues_id",
        "img",
        "desc",
        "alt",
        "ordering",
        "status"
    ); //fields in table (excluding Primary Key)
    var $table = "t_dining_venues_images";

    /**
     * @Desc get recipe images by id
     * @param int $recipe_id: the recipe id
     * @return array
     */
    function getDiningVenousImage($dining_venues_id) {
        $result = array();
        $result = $this->get("*", " AND `dining_venues_id` =  $dining_venues_id ", " `ordering` DESC, `id` DESC ");
        return $result;
    }
    
    
    /**
     * @Desc  insert recipe image from session 
     * @param int $recipe_id: the recipe id
     * @return boolean
     */
    function addLocateSessionImage($dining_venues_id){
        $arrImg = GetSession('multiimages');
        if(count($arrImg) <= 0 ){
            return;
        }
        
        foreach($arrImg as $img_id){
            $this->dining_venues_id    = $dining_venues_id;
            $this->update($img_id, array('dining_venues_id'));
        }
        SetSession('multiimages', NULL);
    }

}
