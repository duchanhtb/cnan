<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class RecipeLike extends Base {

    var $fields = array("recipe_id", "username", "date_updated"); //fields in table (excluding Primary Key)	
    var $table = "t_recipe_like";

}
