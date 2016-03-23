<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class footer extends Module {

    
    function footer() {
        $this->file = 'footer.html';
        parent::module();
    }

    function draw() {
        global $skin;
        $xtpl = new XTemplate($this->tpl);

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
     
        register_script('jquery', $skin_path.'asset/js/jquery-2.1.0.min.js');
        register_script('mousewheel', $skin_path.'asset/js/jquery.mousewheel.min.js');
        register_script('bootstrap', $skin_path.'asset/js/bootstrap.min.js');
        register_script('cookie', $skin_path.'asset/js/jquery.cookie.js');
        register_script('coorbox', $skin_path.'asset/js/jquery.colorbox-min.js');
        register_script('main', $skin_path.'asset/js/main.js',4);
        
        $xtpl->parse('main');
        return $xtpl->out('main');
    }

}
