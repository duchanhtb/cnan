<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class profile extends Module {

    function profile() {
        $this->file = 'profile.html';
        parent::module();
    }

    function draw() {
        global $skin;
        $xtpl = new XTemplate($this->tpl);

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
        $xtpl->assign('link_profile', createLink('profile'));
        
        register_style('user', $skin_path . 'asset/css/user.css');
        register_script('user', $skin_path . 'asset/js/user.js');

        $user = GetSession('user');
        if (!$user) {
            SetSession('ref', curPageURL());
            $link_login = createLink('dang-nhap');
            redirect($link_login);
        }
        
        $display_title = ($user['fullname'] != '') ? $user['fullname'] : $User['username'];
        addTitle('Trang cá nhân '.$display_title);
        
        // add login name
        $login_name = ($user['username']!='') ? $user['username'] : $user['email'];
        $xtpl->assign('login_name', $login_name);
        
        // add link share recipe
        $xtpl->assign('link_share_location', createLink('chia-se-dia-diem'));
        
        // add link share recipe
        $xtpl->assign('link_share_recipe', createLink('chia-se-cong-thuc'));


        // assign user infomation
        foreach ($user as $type => $value) {

            switch ($type) {
                case "gender":
                    switch ($value) {
                        case "1":
                            $xtpl->assign('gender', 'Nam');
                            break;
                        case "0":
                            $xtpl->assign('gender', 'Nữ');
                            break;
                        default:
                            $xtpl->assign('gender', '(Chưa xác định)<a class="update" href="">Cập nhật</a>');
                            break;
                    }
                    break;

                case "birthday":
                    if ($value != NULL) {
                        $xtpl->assign('birthday', date('d/m/Y', strtotime($value)));
                    } else {
                        $xtpl->assign('birthday', '(Chưa rõ)<a class="update" href="">Cập nhật</a>');
                    }
                    break;

                case "province_id":
                    if ($value > 0) {
                        $Province = new Provinces();
                        $Province->read($value);
                        $xtpl->assign('province', $Province->name);
                    } else {
                        $xtpl->assign('province', '(Chưa rõ)<a class="update" href="">Cập nhật</a>');
                    }
                    break;

                case "career_id":
                    if ($value > 0) {
                        $Career = new Career();
                        $Career->read($value);
                        $xtpl->assign('career', $Career->name);
                    } else {
                        $xtpl->assign('career', '(Chưa rõ)<a class="update" href="">Cập nhật</a>');
                    }
                    break;
            
                default:
                    if ($value != '') {
                        $xtpl->assign($type, $value);
                    } else {
                        $xtpl->assign($type, '(chưa có thông tin)<a class="update" href="">Cập nhật</a>');
                    }
                    break;
            }
        }
        
        //bao ve tai khoan
        $xtpl->assign('email_protect', obscure_email($user['email']));
        $question_id = $user['question_id'];
        
        if($user['question_id']){
            $SecurityQuestion = new SecurityQuestion();
            $SecurityQuestion->read($user['question_id']);
            $xtpl->assign('question', $SecurityQuestion->name);
            $xtpl->assign('answer', '******');
        }else{
            $xtpl->assign('question', '(chưa có thông tin)<a class="update" href="">Cập nhật</a>');
            $xtpl->assign('answer', '(chưa có thông tin)');
        }
        
        
        // user recipe
        $user_id = $user['id'];
        $Recipe = new Recipe();
        $Recipe->num_per_page = 10;
        
        $total_row = $Recipe->countRecipeUser($user_id);
        $p = Input::get('rp', 'int', 1);
        $start = ($p-1)*$Recipe->num_per_page;
        $total_page = ceil($total_row / $Recipe->num_per_page);
        $paging_recipe = paging($p, $total_page, curPageURL(),'rp');
        $xtpl->assign('paging_recipe', $paging_recipe);
        $userRecipe = $Recipe->getRecipeUser($user_id, 0, $Recipe->num_per_page, $start);
        
        if(count($userRecipe) > 0 ){
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
                $xtpl->assign('recipe_edit_link', $edit_link.'?id='.alphaID($recipe['id']));
                $xtpl->parse('main.recipe');
            }
        }else{
            $link_first_share_recipe = createLink('chia-se-cong-thuc');
            $xtpl->assign('first_share_recipe', '<h3 style="text-align:center"><a href="'.$link_first_share_recipe.'">Click để đăng món ăn đầu tiên</a></h3>');
        }
        
        
        // user dining venues
        $DiningVenues = new DiningVenues();
        $DiningVenues->num_per_page = 10;
        $total_row = $DiningVenues->countDiningVenuesUser($user_id);
        $p = Input::get('lp', 'int', 1);
        $start = ($p-1)*$DiningVenues->num_per_page;
        $total_page = ceil($total_row / $DiningVenues->num_per_page);
        $paging_locate = paging($p, $total_page, curPageURL(),'lp');
        $xtpl->assign('paging_locate', $paging_locate);
        $userDining = $DiningVenues->getDiningVenuesUser($user_id, $start, $DiningVenues->num_per_page);
        
        if(count($userDining) > 0 ){
            foreach($userDining as $key=>$dining_veneus){
                $xtpl->assign('locate_name', $dining_veneus['name']);
                $xtpl->assign('locate_link', createLink('dia-diem', array('id'=>$dining_veneus['id'], 'title' => $dining_veneus['name'])));
                $xtpl->assign('locate_img', getThumbnail('thumb-480-crop', $dining_veneus['img']));
                $xtpl->assign('locate_brief', $dining_veneus['brief']);
                $xtpl->assign('locate_date', date('H:i d/m/Y', strtotime($dining_veneus['date_created'])));
                $xtpl->assign('locate_gmap', createLink('map', array('id' => $dining_veneus['id'], 'title' => $dining_veneus['name'])));
                $locate_edit_link = createLink('chia-se-dia-diem');
                $xtpl->assign('locate_edit_link', $locate_edit_link.'?id='.alphaID($dining_veneus['id']));
                $xtpl->parse('main.locate');
            }
        }else{
            $link_first_share_locate = createLink('chia-se-dia-diem');
            $xtpl->assign('first_share_locate', '<h3 style="text-align:center"><a href="'.$link_first_share_locate.'">Click để đăng địa điểm đầu tiên đầu tiên</a></h3>');
        }

        $xtpl->parse('main');
        return $xtpl->out('main');
    }

}
