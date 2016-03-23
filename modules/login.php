<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 * @desc module list html of introduction
 */
class login extends Module {

    function login() {
        $this->file = 'login.html';
        parent::module();
    }

    function draw() {
        global $skin;
        $xtpl = new XTemplate($this->tpl);
        //check if userlogin
        $user = GetSession('user');
        if ($user && count($user) > 0) {
            $ref = GetSession('ref');
            if (!$ref)
                $ref = base_url();
            redirect($ref);
        }

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
        $xtpl->assign('link_register', createLink('dang-ky'));
        $xtpl->assign('link_forgotpass', createLink('quen-mat-khau'));
        $xtpl->assign('rand', rand());
        
        // login via social network
        $xtpl->assign('login_facebook', base_url().'auth/facebook');
        $xtpl->assign('login_google', base_url().'auth/google');
        $xtpl->assign('login_yahoo', base_url().'auth/yahoo');
        

        register_style('user', $skin_path . 'asset/css/user.css');
        register_script('user', $skin_path . 'asset/js/user.js');

        $numError = GetSession('numError');
        SetSession('numError', $numError + 1);
        if ($numError > 2) {
            $xtpl->parse('main.captcha');
        }
        
        
        if (isset($_POST) && count($_POST) > 0 ) {
            
            $html_msg = ''; // alert messenger
            $captcha = Input::get('captcha', 'txt', '');
            if (isset($_POST['captcha']) && trim($captcha) != $_SESSION['security_code']) {
                $html_msg = '<div class="user-msg" style="color:red; display:block;">Mã bảo vệ chưa đúng</div>';
                $xtpl->assign('html_msg', $html_msg);
            } else {
                $username = Input::get('username', 'txt', '');
                $password = Input::get('password', 'txt', '');

                $User = new User();
                $member = $User->login($username, $password);

                if ($member && count($member) > 0) {
                    SetSession('numError', NULL);
                    $ref = GetSession('ref');
                    if (!$ref)
                        $ref = base_url();
                    redirect($ref);
                }else {
                    $html_msg = '<div class="user-msg" style="color:red; display:block;">'.$User->msg.'</div>';
                    $xtpl->assign('html_msg', $html_msg);
                }
            }
            
            // reload cache file 
            cacheSetting(true);
        }

        $xtpl->parse('main');
        return $xtpl->out('main');
    }

}
