<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 * @desc module list html of introduction
 */
class home extends Module {

    function home() {
        $this->file = 'home.html';
        parent::module();
    }

    function draw() {
        global $oDb, $skin;
        $xtpl = new XTemplate($this->tpl);

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
        
        $User = new User();
        $Recipe = new Recipe();

        // slide
        register_style('slick', $skin_path . 'asset/css/pgwslider.min.css',1);
        register_script('slick', $skin_path.'asset/js/pgwslider.min.js');
        
        $con = " AND `status` = 1 AND `traditional` = 1 ";
        $homeSlider = $Recipe->get('*', $con, 'ordering DESC, id DESC', 0, 3);
        $list_id_slide = '';
        foreach ($homeSlider as $key => $slide) {
            $list_id_slide .= $slide['id'] . ',';
            $xtpl->assign('slide_link', createLink('cong-thuc', array('title' => $slide['title'], 'id' => $slide['id'])));
            $xtpl->assign('slide_name', $slide['title']);
            $xtpl->assign('slide_img', getThumbnail('og-image', $slide['img']));
            $xtpl->parse('main.slide');
        }
        $list_id_slide = trim($list_id_slide, ',');
       
        // mon an moi nhat
        $xtpl->assign('link_mon_ngon_moi_nhat', createLink('mon-ngon-moi-nhat'));
        $con = " AND status = 1 AND `id` NOT IN ($list_id_slide) ";
        $homeRecipe = $Recipe->get('id, title, img, username, user_id ', $con, ' ordering DESC ', 0, 12);
        foreach ($homeRecipe as $key => $recipe) {
            $userInfo = $User->getDisplayInfo($recipe['user_id']);
            $xtpl->assign('recipe_new_link', createLink('cong-thuc', array('id' => $recipe['id'], 'title' => $recipe['title'])));
            $xtpl->assign('recipe_new_name', $recipe['title']);
            $xtpl->assign('recipe_new_img', getThumbnail('thumb-500-crop', $recipe['img']));
            $xtpl->assign('recipe_new_author', $userInfo['name']);
            $xtpl->parse('main.recipe_new');
        }

        // mon an duoc yeu thich nhieu nhat lay trong 20 ngay gan day nhat
        $xtpl->assign('link_an_yeu_thich_nhat', createLink('mon-an-yeu-thich-nhat'));

        $start_time = date('Y-m-d', time() - 20 * 86400);
        $end_time = date('Y-m-d', time());
        $sql = "SELECT t_recipe.id, t_recipe.user_id, t_recipe.username, t_recipe.title, t_recipe.intruction, t_recipe.cooking_time, t_recipe.img, t_recipe_views.`date`, COUNT( t_recipe_views.hits ) AS tt
				FROM t_recipe
				LEFT JOIN t_recipe_views ON t_recipe.id = t_recipe_views.recipe_id
                                WHERE t_recipe_views.`date` >= '$start_time'  AND `date` <= '$end_time'
				GROUP BY t_recipe_views.recipe_id
				ORDER BY tt DESC 
				LIMIT 0 , 5";
     
        $rc = $oDb->query($sql);
        $rs = $oDb->fetchAll($rc);

        foreach ($rs as $key => $recipe) {
            $userInfo = $User->getDisplayInfo($recipe['user_id']);
            $xtpl->assign('recipe_like_link', createLink('cong-thuc', array('id' => $recipe['id'], 'title' => $recipe['title'])));
            $xtpl->assign('recipe_like_name', $recipe['title']);
            $xtpl->assign('recipe_like_img', getThumbnail('thumb-480-crop', $recipe['img']));
            $xtpl->assign('recipe_like_author', $userInfo['name']);
            $xtpl->assign('recipe_like_point', $userInfo['point']);
            $xtpl->assign('recipe_like_author_link', createLink('thanh-vien', array('id' => $recipe['user_id'], 'title' => $userInfo['name'])));
            $xtpl->assign('recipe_like_intruction', _substr(strip_tags($recipe['intruction']), 300));
            $ck_time = $Recipe->getDisplayCookingTime($recipe['cooking_time']);
            if ($ck_time) {
                $xtpl->assign('recipe_like_cooking_time', '<div class="time-cooking"><i class="fa fa-clock-o"></i>' . $ck_time . '</div>');
            } else {
                $xtpl->assign('recipe_like_cooking_time', '');
            }

            $xtpl->parse('main.recipe_like');
        }



        // top dau bep
        $link_topdaubep = createLink('top-dau-bep');
        $xtpl->assign('link_top_dau_bep', $link_topdaubep);
        $User = new User();
        $topUser = $User->getTopUser();
        foreach ($topUser as $key => $value) {
            $display_name = $User->getDisplayName($value['id']);
            $xtpl->assign('user_link', createLink('thanh-vien', array('id' => $value['id'], 'title' => $value['fullname'])));
            $xtpl->assign('user_name', $display_name);
            $xtpl->assign('user_avatar', $User->getUserAvatar($value['avatar']));
            $xtpl->assign('html_info', $User->getHtmlInfoHomepage($value['id']));
            $xtpl->parse('main.top_user');
        }


        // blog am thuc
        $link_blog = createLink('blog-am-thuc');
        $xtpl->assign('link_blog', $link_blog);

        $Blog = new Blog();
        $Blog->num_per_page = 8;
        $arrBlog = $Blog->getBlog(' AND `status` = 1 AND `home` = 1 ');
        foreach ($arrBlog as $key => $value) {
            if ($key == 0) {
                $xtpl->assign('blog_title0', $value['title']);
                $xtpl->assign('blog_link0', createLink('blog-am-thuc', array('id' => $value['id'], 'title' => $value['title'])));
                $xtpl->assign('blog_img0', getThumbnail('thumb-500', $value['img']));
                $xtpl->assign('blog_brief0', _substr($value['brief'], 200));
            } else {
                $xtpl->assign('blog_title', $value['title']);
                $xtpl->assign('blog_link', createLink('blog-am-thuc', array('id' => $value['id'], 'title' => $value['title'])));
                $xtpl->parse('main.blog');
            }
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
