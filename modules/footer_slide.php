<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class footer_slide extends Module {

    function footer_slide() {

        $this->file = 'footer_slide.html';
        parent::module();
    }

    function draw() {
        global $skin, $title, $keywords, $description;
        $xtpl = new XTemplate($this->tpl);

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);

        
        $xtpl->parse('main');
        return $xtpl->out('main');
    }

}
