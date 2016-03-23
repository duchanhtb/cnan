<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 * @desc module list html of introduction
 */
class video extends Module {

    function video() {
        $this->file = 'video.html';
        parent::module();
    }

    function draw() {
        global $oDb, $skin;
        $xtpl = new XTemplate($this->tpl);
        
        
        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
      
        // add ajax script
        register_script('ajax-queue', $skin_path.'asset/js/ajax-queue.js');
        
        $Recipe = new Recipe();
        $Recipe->num_per_page = 15;
        $p = Input::get('p', 'int', 1);
        $start_row = ($p - 1) * $Recipe->num_per_page;
        $con = " AND `status` = 1 AND `has_video` = 1 AND `youtube_id` != '' ";        
        $total_row = $Recipe->count($con);
        
        $total_page = ceil($total_row / $Recipe->num_per_page);
        $paging = paging($p, $total_page, curPageURL());
        $xtpl->assign('paging', $paging);
        
        
        // recipe video moi nhat
        $arrRecipeVideo = $Recipe->get('*',$con, '`ordering` DESC', $start_row, $Recipe->num_per_page);
        
        foreach($arrRecipeVideo as $key=>$value){
            $xtpl->assign('name', $value['title']);
            $xtpl->assign('link', createLink('cong-thuc', array('id'=>$value['id'], 'title' => $value['title'])));
            $xtpl->assign('img', getThumbnail('thumb-480-crop', $value['img']));
            $xtpl->assign('youtube_id', $value['youtube_id']);
            $xtpl->parse('main.video');
        }
        
        
        // recipe video hot nhat
        $start_time = date('Y-m-d H:i', time() - 20 * 86400);
        $end_time = date('Y-m-d H:i', time());
        $sql = "SELECT id, t_recipe.user_id, username, title, youtube_id, img, COUNT( t_recipe_like.recipe_id ) AS tt
				FROM t_recipe
				LEFT JOIN t_recipe_like ON t_recipe.id = t_recipe_like.recipe_id
                                WHERE 1 ".$con."
				GROUP BY t_recipe_like.recipe_id
				ORDER BY tt DESC, hits DESC 
				LIMIT 0 , 12";
        $rc = $oDb->query($sql);
        $rs = $oDb->fetchAll($rc);
        foreach ($rs as $key => $recipe) {
            $xtpl->assign('hot_link', createLink('cong-thuc', array('id' => $recipe['id'], 'title' => $recipe['title'])));
            $xtpl->assign('hot_name', $recipe['title']);
            $xtpl->assign('hot_img', getThumbnail('thumb-480-crop', $recipe['img']));
            $xtpl->assign('hot_youtube_id', $value['youtube_id']);
            $xtpl->parse('main.hot_video');
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
