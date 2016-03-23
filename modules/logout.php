<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 * @desc module list html of introduction
 */
class logout extends Module {

    function logout() {
        $this->file = 'logout.html';
        parent::module();
    }

    function draw() {
        cacheSetting(true);
        SetSession('user', NULL);
        
        $ref = GetSession('ref');
        if(!$ref) $ref = base_url();
        redirect($ref);
    }

}
