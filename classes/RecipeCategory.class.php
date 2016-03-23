<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
class RecipeCategory extends Base {

    var $fields = array(
        "name",
        "img",
        "parent_id",
        "ordering"
    ); //fields in table (excluding Primary Key)
    var $table = "t_recipe_category";
    var $table_relationships = 't_recipe_relationships';

    
    // get category
    function getCategory($recipe_id) {
        global $oDb;
        $sql = "SELECT * FROM " . $this->table . " 
				JOIN " . $this->table_relationships . " 
				ON " . $this->table . ".id = " . $this->table_relationships . ".object_id
				WHERE " . $this->table_relationships . ".`recipe_id` = $recipe_id  AND " . $this->table_relationships . ".`type` = 'category'";
        $rc = $oDb->query($sql);
        $rs = $oDb->fetchAll($rc);
        if (count($rs) > 0) {
            foreach ($rs as $key => $value) {
                $result[$value['id']] = $value['name'];
            }
        }else{
            $result = array(0 => 'Chưa xác định');
        }

        return $result;
    }
    
    
    
    // get category group by parrent id
    function getGroupCategory(){
        $result = array();
        $arrCategory = $this->get('*', " AND id > 0 AND `status` = 1 ");
        foreach($arrCategory as $key=>$value){
            if($value['parent_id'] == 0){
                $result[$value['id']] = $value;
            }
        }
        
        foreach($result as $id=>$value){
            foreach($arrCategory as $k=>$sub){
                if($sub['parent_id'] == $id)
                    $result[$id]['subCat'][] = $sub;
            }
        }
      
        return $result;
    }
    
    
    
    function updateRecipeCategory($recipe_id, $arrCategory){
        global $oDb;
        if(count($arrCategory) <= 0 ){
            return false;
        }
        
        // delete old category first
        $sql = "DELETE FROM $this->table_relationships WHERE recipe_id = $recipe_id AND type = 'category' ";
        $oDb->query($sql);
        
        foreach($arrCategory as $cat_id){
            $sql = "INSERT INTO  `$this->table_relationships` (
                    `recipe_id` ,
                    `object_id` ,
                    `type`
                    )
                    VALUES (
                    '$recipe_id',  '$cat_id',  'category'
                    )";
            $oDb->query($sql);
        }
        return true;
        
    }
    
}
