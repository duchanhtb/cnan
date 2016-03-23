<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 * @desc module list html of introduction
 */
class forgotpass extends Module {

    function forgotpass() {
    }

    function draw() {
        
        $step = GetSession('forgotpass_step', 1);
        addTitle('Quên mật khẩu - bước '.$step);
        
        $this->file = 'forgotpass.html';
        parent::module();
        
        global $skin;
        $xtpl = new XTemplate($this->tpl);

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
        
        $xtpl->assign('link_login', createLink('dang-nhap'));
        
        register_style('user', $skin_path . 'asset/css/user.css');
        register_script('user', $skin_path . 'asset/js/user.js');
        
        $xtpl->parse('main');
        return $xtpl->out('main');
    }

}
