<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class member extends Module {

    function member() {
        $this->file = 'member.html';
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
        
        register_script('google-map', '//maps.google.com/maps/api/js?sensor=true');
        register_script('gmap', $skin_path . 'asset/js/gmaps.js');
        $xtpl->assign('current_page', curPageURL());

        $user_id = getCmsId();
        if ($user_id) {
            $loginMember = GetSession('user');
            if($loginMember['id'] == $user_id){
                redirect(createLink('profile'));
            }
            $User = new User();
            $User->read($user_id);
            
            // add title
            $display_title = ($User->fullname != '') ? $User->fullname : $User->username;
            addTitle('Trang cá nhân '.$display_title);
            
            $display_name = ($User->fullname != '') ? $User->fullname : $User->username;
            $xtpl->assign('display_name', $display_name);
            $xtpl->assign('avatar', getThumbnail('thumb-150', $User->avatar));
            if(strpos($User->avatar, 'http') === false){
                $avatar_full    = base_url().$User->avatar;
            }else{
                $avatar_full    = $User->avatar;
            }
            $xtpl->assign('avatar_full', $avatar_full);
            
            $gender = ($User->gender == 1) ? 'Nam' : 'Nữ';
            $xtpl->assign('gender', $gender);
            $xtpl->assign('date_register', date('d/m/Y', strtotime($User->date_register)));
            $xtpl->assign('point', formatPrice($User->point));
            
            $Province = new Provinces();
            $Province->read($User->province_id);
            $xtpl->assign('province', $Province->name);
            
            // user recipe
            $Recipe = new Recipe();
            $Recipe->num_per_page = 10;

            $total_row = $Recipe->countRecipeUser($user_id);
            $p = Input::get('rp', 'int', 1);
            $start = ($p - 1) * $Recipe->num_per_page;
            $total_page = ceil($total_row / $Recipe->num_per_page);
            $paging_recipe = paging($p, $total_page, curPageURL(), 'rp');
            $xtpl->assign('paging_recipe', $paging_recipe);
            $userRecipe = $Recipe->getRecipeUser($user_id, 0, $Recipe->num_per_page, $start);
            
            $xtpl->assign('total_recipe', $total_row);

            if (count($userRecipe) > 0) {
                $User = new User();
                foreach ($userRecipe as $key => $recipe) {
                    $userInfo = $User->getDisplayInfo($recipe['user_id']);
                    $xtpl->assign('recipe_link', createLink('cong-thuc', array('id' => $recipe['id'], 'title' => $recipe['title'])));
                    $xtpl->assign('recipe_title', $recipe['title']);
                    $xtpl->assign('recipe_img', getThumbnail('thumb-480-crop', $recipe['img']));
                    $xtpl->assign('recipe_author', $userInfo['name']);
                    $xtpl->assign('recipe_point', $userInfo['point']);
                    $xtpl->assign('recipe_date', date('H:i d/m/Y', strtotime($recipe['date_created'])));
                    $xtpl->assign('recipe_author_link', createLink('thanh-vien', array('id' => $recipe['user_id'], 'title' => $userInfo['name'])));
                    $xtpl->assign('recipe_intruction', _substr(strip_tags($recipe['intruction']), 300));
                    $ck_time = $Recipe->getDisplayCookingTime($recipe['cooking_time']);
                    if ($ck_time) {
                        $xtpl->assign('recipe_cooking_time', '<div class="time-cooking"><i class="fa fa-clock-o"></i>' . $ck_time . '</div>');
                    } else {
                        $xtpl->assign('recipe_cooking_time', '');
                    }
                    $edit_link = createLink('chia-se-cong-thuc');
                    $xtpl->assign('recipe_edit_link', $edit_link . '?id=' . alphaID($recipe['id']));
                    $xtpl->parse('main.recipe');
                }
            } else {
                $html_no_recipe  = '<p style="text-align:center">'.$display_name.' chưa từng chia sẻ công thức nào</p>';
                $xtpl->assign('html_no_recipe', $html_no_recipe);
            }


            // user dining venues
            $DiningVenues = new DiningVenues();
            $DiningVenues->num_per_page = 10;
            $total_row = $DiningVenues->countDiningVenuesUser($user_id);
            $p = Input::get('lp', 'int', 1);
            $start = ($p - 1) * $DiningVenues->num_per_page;
            $total_page = ceil($total_row / $DiningVenues->num_per_page);
            $paging_locate = paging($p, $total_page, curPageURL(), 'lp');
            $xtpl->assign('paging_locate', $paging_locate);
            $userDining = $DiningVenues->getDiningVenuesUser($user_id, $start, $DiningVenues->num_per_page);
            
            $xtpl->assign('total_locate', $total_row);
            
            if (count($userDining) > 0) {
                foreach ($userDining as $key => $dining_veneus) {
                    $xtpl->assign('locate_name', $dining_veneus['name']);
                    $xtpl->assign('locate_link', createLink('dia-diem', array('id' => $dining_veneus['id'], 'title' => $dining_veneus['name'])));
                    $xtpl->assign('locate_img', getThumbnail('thumb-480-crop', $dining_veneus['img']));
                    $xtpl->assign('locate_brief', $dining_veneus['brief']);
                    $xtpl->assign('locate_date', date('H:i d/m/Y', strtotime($dining_veneus['date_created'])));
                    $xtpl->assign('locate_gmap', createLink('map', array('id' => $dining_veneus['id'], 'title' => $dining_veneus['name'])));
                    $locate_edit_link = createLink('chia-se-dia-diem');
                    $xtpl->assign('locate_edit_link', $locate_edit_link . '?id=' . alphaID($dining_veneus['id']));
                    $xtpl->parse('main.locate');
                }
            } else {
                $html_no_locate  = '<p style="text-align:center">'.$display_name.' chưa từng chia sẻ địa điểm nào</p>';
                $xtpl->assign('html_no_locate', $html_no_locate);
            }


            //addGraphTags('og-title', 'title', $DiningVenues->name);
            //addGraphTags('og-url', 'url', curPageURL());
            //addGraphTags('og-description', 'description', strip_tags(_substr($DiningVenues->content, 150)));
        }

        // sidebar
        $ads = loadModule('ads');
        $xtpl->assign('ads', $ads);

        $recipe_hot = loadModule('recipe_hot');
        $xtpl->assign('recipe_hot', $recipe_hot);

        $top_location = loadModule('top_location');
        $xtpl->assign('top_location', $top_location);

        $facebook = loadModule('facebook');
        $xtpl->assign('facebook', $facebook);

        $xtpl->parse('main');
        return $xtpl->out('main');
    }

}
