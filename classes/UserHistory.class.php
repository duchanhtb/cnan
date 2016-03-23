<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
class UserHistory extends Base {

    var $fields = array(
        'user_id',
        'object_id',
        'type',
        'action',
        'date'
        ); //fields in table (excluding Primary Key)
    var $table = "t_user_history";

}
