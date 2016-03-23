<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class register extends Module {

    function register() {
        $this->file = 'register.html';
        parent::module();
    }

    function draw() {
        cacheSetting(true);
        global $skin;
        $xtpl = new XTemplate($this->tpl);
       
        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
        $xtpl->assign('rand', rand());
        
        register_style('user', $skin_path . 'asset/css/user.css');
        register_script('user', $skin_path . 'asset/js/user.js');
        
        // login via social network
        $xtpl->assign('login_facebook', base_url().'auth/facebook');
        $xtpl->assign('login_google', base_url().'auth/google');
        $xtpl->assign('login_yahoo', base_url().'auth/yahoo');
        
        // link dieu khoan su dung
        $xtpl->assign('link_policy', createLink('dieu-khoan-su-dung'));
        
        if(isset($_POST) && count($_POST) > 0 ){
            // check captcha
            $captcha = Input::get('captcha','txt','');
            if (trim($captcha) != $_SESSION['security_code']) {
                $html_msg = '<div class="user-msg" style="color:red; display:block; margin-bottom: 20px">Mã bảo vệ chưa đúng</div>';
                $xtpl->assign('html_msg', $html_msg);
                $xtpl->parse('main');
                return $xtpl->out('main');
                die;
            }
            
            $fullname = Input::get('fullname', 'txt' , '');
            $email  = Input::get('email', 'txt', '');
            $re_email  = Input::get('re-email', 'txt', '');
            
            // check re-email
            if($email != $re_email){
                $html_msg = '<div class="user-msg" style="color:red; display:block; margin-bottom: 20px">Nhập lại email chưa đúng</div>';
                $xtpl->assign('html_msg', $html_msg);
                $xtpl->parse('main');
                return $xtpl->out('main');
                die;
            }
            
            $password = Input::get('password', 'txt', '');
            $User = new User();
            
            // check email exsits
            $member = $User->getUserByEmail($email);
            if($member && count($member) > 0 ){
                $html_msg = '<div class="user-msg" style="color:red; display:block; margin-bottom: 20px">Email '.$email.' đã được đăng ký</div>';
                $xtpl->assign('html_msg', $html_msg);
                $xtpl->parse('main');
                return $xtpl->out('main');
                die;
            }
            
            $User->fullname     = $fullname;
            $User->email        = $email;
            $User->password     = createMd5Password($password);
            $User->avatar       = '/uploads/images/useravatar/default-avatar.png';
            $User->status       = 1;
            $User->insert();

            $User->login($email, $password);
            redirect(base_url());
        }

        $xtpl->parse('main');
        return $xtpl->out('main');
    }

}
