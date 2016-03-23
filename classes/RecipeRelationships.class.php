<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
class RecipeRelationships extends Base {

    var $fields = array(
        "recipe_id",
        "object_id",
        "type",
    ); //fields in table (excluding Primary Key)
    
    var $table = 't_recipe_relationships';

    
}
