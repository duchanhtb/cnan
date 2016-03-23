<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class policy extends Module {

    function policy() {
        $this->file = 'policy.html';
        parent::module();
    }

    function draw() {
        global $skin;
        $xtpl = new XTemplate($this->tpl);

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
        
        register_style('user', $skin_path . 'asset/css/user.css');
        register_script('user', $skin_path . 'asset/js/user.js');
        
        $Policy = new Policy();
        $arrPolicy = $Policy->get('*',' AND status = 1 ','ordering ASC');
        
        $n = 1;
        foreach($arrPolicy as $key=>$value){
            $xtpl->assign('name', $value['name']);
            $xtpl->assign('content', $value['content']);
            $xtpl->assign('id', $value['id']);
            $xtpl->assign('n', $n); 
            $n++;
            $xtpl->parse('main.policy');
            $xtpl->parse('main.tab');
        }
        
        $xtpl->parse('main');
        return $xtpl->out('main');
    }

}
