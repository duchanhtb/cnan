<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
class Module extends Base {

    var $file = '';
    var $tpl = '';
    var $skin_path = '';
    var $cache_html = false;
    var $cache_time = 0;  // cache time live (second)

    function module() {
        global $skin;
        $this->tpl = "skin/$skin/module/" . $this->file;
        $this->skin_path = base_url() . 'skin/' . $skin . '/';
    }
}