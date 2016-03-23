<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
class RecipeIngridient extends Base {
   
    var $fields = array("recipe_id", "ingridient_id"); //fields in table (excluding Primary Key)
    var $table = "t_recipe_ingridient";
    
  
    /**
     * @Desc insert relationship if not exists
     * @param int $recipe_id: recipe id
     * @param int $ingridient_id: ingridient id
     * @return boolean
     */
    function insertRelation($recipe_id, $ingridient_id){
        $con = " AND `recipe_id` = $recipe_id AND `ingridient_id` = $ingridient_id ";
        $check = $this->getRecord('*', $con);
        if(!$check){
            $this->recipe_id        = $recipe_id;
            $this->ingridient_id    = $ingridient_id;
            $this->insert();
            return true;
        }
        return false;
    }

}
