<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class RecipeReport extends Base {

    var $fields = array("recipe_id", "email", "content", "user_id", "date_report"); //fields in table (excluding Primary Key)	
    var $table = "t_recipe_report";

}
