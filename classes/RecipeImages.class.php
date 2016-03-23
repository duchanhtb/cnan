<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class RecipeImages extends Base {

    var $fields = array(
        "recipe_id",
        "img",
        "desc",
        "alt",
        "ordering",
        "status"
    ); //fields in table (excluding Primary Key)
    var $table = "t_recipe_images";

    /**
     * @Desc get recipe images by id
     * @param int $recipe_id: the recipe id
     * @return array
     */
    function getRecipeImage($recipe_id) {
        $result = array();
        $result = $this->get("*", " AND `recipe_id` =  $recipe_id ", " `ordering` ASC, `id` ASC ");
        return $result;
    }
    
    
    
    /**
     * @Desc insert recipe image
     * @param int $recipe_id: the recipe id
     * @param array $arrImg array img path
     * @return boolean
     */
    function insertRecipeImage($recipe_id, $arrImg){
        
        if(count($arrImg) <= 0){
            return false;
        }
        foreach($arrImg as $img){
            $this->img = $img;
            $this->insert();
        }
        return true;
    }
    
    
    
    /**
     * @Desc delete image
     * @param int $recipe_id: the recipe id
     * @param mixed $mixed the primary key or img path
     * @return boolean
     */
    function deleteImage($recipe_id, $mixed){
        global $oDb;
        
        if(is_numeric($mixed)){
            $this->remove($mixed);
        }else{
            $sql = "DELETE FROM ".$this->table." WHERE 1 AND `recipe_id` = $recipe_id AND `img` = '$mixed'";
            $oDb->query($sql);
        }
        return true;
    }
    
    
    
    /**
     * @Desc  insert recipe image from session 
     * @param int $recipe_id: the recipe id
     * @return boolean
     */
    function addRecipeSessionImage($recipe_id){
        $arrImg = GetSession('multiimages');
        if(count($arrImg) <= 0 ){
            return;
        }
        
        foreach($arrImg as $img_id){
            $this->recipe_id    = $recipe_id;
            $this->update($img_id, array('recipe_id'));
        }
        SetSession('multiimages', NULL);
    }
            
}
