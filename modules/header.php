<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 * @desc module list html of introduction
 */
class header extends Module {

    function header() {
        $this->file = 'header.html';
        parent::module();
    }

    function draw() {
        global $skin;
        $xtpl = new XTemplate($this->tpl);

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
        $xtpl->assign('link_search', createLink('tim-kiem'));

        // add style
        register_style('colorbox', $skin_path . 'asset/css/colorbox.css',4);
        register_style('bootstrap', $skin_path . 'asset/css/bootstrap.min.css',4);
        register_style('style', $skin_path . 'asset/css/style.css',6);
        register_style('response', $skin_path . 'asset/css/response.css',9);

        // keyword
        $q = Input::get('q','txt','');
        $xtpl->assign('q', $q);
        
        // add graph
        addGraphTags('og-image', 'image', $skin_path . 'asset/images/logo-490x294.png');

        $user = GetSession('user');
        $html_user = '';
        // neu ton tai session user
        if ($user && count($user) > 0) {
            $html_user = $this->hasLogin();
        } else {
            $html_user = $this->hasNotLogin();
        }
        $xtpl->assign('html_user', $html_user);


        $xtpl->parse('main');
        return $xtpl->out('main');
    }

    function hasLogin() {
        $user = GetSession('user');
        $user_id = $user['id'];
        
        $Recipe = new Recipe();
        $DiningVenues = new DiningVenues();
        $recipePost = $Recipe->countRecipeUser($user_id);
        $diningVenuesPost = $DiningVenues->countDiningVenuesUser($user_id);
        
        $display_name = ( $user['fullname'] != '') ? $user['fullname'] : $user['username'];
        $html = '<div id="userBox">
                    <div>
                        <img class="small-avt" src="' . getThumbnail('thumb-150', $user['avatar']) . '" />
                        <a href="' . createLink('profile') . '" class="white">' . $display_name . ' <i class="fa fa-angle-down"></i></a>
                    </div>
                    <div class="tip-dropdown">
                        <div class="wrap-dropdown">
                            <div class="arrow-up"></div>
                            <div id="large-avatar">
                                <a href="' . createLink('profile') . '"><img src="' . getThumbnail('thumb-150', $user['avatar']) . '" /></a>
                            </div>
                            <ul>
                                <li><a href="' . createLink('profile') . '"><i class="fa fa-edit"></i>Trang cá nhân</a></li>
                                <li><a href="' . createLink('chia-se-cong-thuc') . '"><i class="fa fa-fire"></i>Chia sẻ công thức<span>('.formatPrice($recipePost).')</span></a></li>
                                <li><a href="' . createLink('chia-se-dia-diem') . '"><i class="fa fa-map-marker"></i>Chia sẻ địa điểm<span>('.formatPrice($diningVenuesPost).')</span></a></li>                                
                                <li><a href="' . createLink('dang-xuat') . '"><i class="fa fa-sign-out"></i>Đăng xuất</a></li>
                            </ul>
                        </div>
                    </div>
                </div>';

        return $html;
    }

    function hasNotLogin() {
        $link_login = createLink('dang-nhap');
        
        $login_facebook = base_url().'auth/facebook';
        $login_google = base_url().'auth/google';
        
        $html_content = '
            <div id="userBox">
                <ul>
                    <li><a href="' . createLink('dang-nhap') . '" class="white">Đăng nhập</a></li>
                    <li class="white">/</li>
                    <li><a href="' . createLink('dang-ky') . '" class="white">Đăng ký</a></li>
                </ul>
                <form action="'.$link_login.'" method="POST">
                <div class="tip-dropdown login-dropdown">
                    <div class="wrap-dropdown">
                        <div class="arrow-up"></div>
                        <ul class="wr-login">
                            <li><input type="text" name="username" placeholder="Tên đăng nhập hoặc Email" /></li>
                            <li><input type="password" name="password" placeholder="Mật khẩu" /></li>
                        </ul>
                        <div id="rg-other">
                            <h3 class="title-rg-other">Hoặc đăng nhập tài khoản với</h3>
                            <ul>
                                <li><a id="facebook" onclick="openPopup(\''.$login_facebook.'\', \'Đăng nhập bằng Facebook\')" href="#"><span class="wr-social wr-facebook"><i class="fa fa-facebook"></i></span>Facebook</a></li>
                                <li><a id="google-plus" onclick="openPopup(\''.$login_google.'\', \'Đăng nhập bằng Google\')" href="#"><span class="wr-social wr-goolge-plus"><i class="fa fa-google-plus"></i></span>Google</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <input type="submit" style="display:none" />
                </form>
            </div>';
        return $html_content;
    }

}
