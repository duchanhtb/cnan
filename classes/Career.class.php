<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2014
 */
class Career extends Base {

    var $fields = array(
        "name",
        "ordering",
        "status"
    ); //fields in table (excluding Primary Key)	
    
    var $table = "m_career";

}
