<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 * @desc class about news
 */
class Policy extends Base {

    var $fields = array(
        'name',
        'content',
        'date_created',
        'ordering',
        'status'
    ); //fields in table (excluding Primary Key)	
    
    
    var $table = "m_policy";

}
