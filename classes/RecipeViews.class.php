<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class RecipeViews extends Base {

    var $fields = array(
        "recipe_id",
        "date",
        "hits"
    ); //fields in table (excluding Primary Key)
    var $table = "t_recipe_views";

}
