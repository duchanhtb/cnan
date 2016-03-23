<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class recipe_hot extends Module {

    function recipe_hot() {
        $this->file = 'recipe_hot.html';
        parent::module();
    }

    function draw() {
        global $skin, $oDb;
        $xtpl = new XTemplate($this->tpl);

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
        $xtpl->assign('skin_path', $skin_path);
        $xtpl->assign('link_top_monngon', createLink('mon-an-yeu-thich-nhat'));


        // mon an duoc yeu thich nhieu nhat lay trong 20 ngay gan day nhat
        $xtpl->assign('link_an_yeu_thich_nhat', createLink('mon-an-yeu-thich-nhat'));

        $start_time = date('Y-m-d H:i', time() - 20 * 86400);
        $end_time = date('Y-m-d H:i', time());
        $sql = "SELECT id, t_recipe.user_id, username, title, intruction, cooking_time, img, COUNT( t_recipe_like.recipe_id ) AS tt
				FROM t_recipe
				LEFT JOIN t_recipe_like ON t_recipe.id = t_recipe_like.recipe_id
				GROUP BY t_recipe_like.recipe_id
				ORDER BY tt DESC, hits DESC 
				LIMIT 0 , 30";
        $rc = $oDb->query($sql);
        $rs = $oDb->fetchAll($rc);
        $num = 1;
        $User = new User();
        foreach ($rs as $key => $value) {
            $userInfo = $User->getDisplayInfo($value['user_id']);
            if ($key == 0) {
                $xtpl->assign('title0', $value['title']);
                $xtpl->assign('img0', getThumbnail('thumb-480-crop', $value['img']));
                $xtpl->assign('link0', createLink('cong-thuc', array('id' => $value['id'], 'title' => $value['title'])));
                $xtpl->assign('num0', $num);
                $xtpl->assign('author_name0', $userInfo['name']); 
                $xtpl->assign('author_link0', createLink('thanh-vien', array('id' => $value['user_id'], 'title' => $userInfo['name']))); 
            } else {
                $xtpl->assign('num', $num);
                $xtpl->assign('title', $value['title']);
                $xtpl->assign('img', getThumbnail('thumb-480-crop', $value['img']));
                $xtpl->assign('link', createLink('cong-thuc', array('id' => $value['id'], 'title' => $value['title'])));
                $xtpl->assign('author_name', $userInfo['name']); 
                $xtpl->assign('author_point', $userInfo['point']); 
                $xtpl->assign('author_link', createLink('thanh-vien', array('id' => $value['user_id'], 'title' => $userInfo['name']))); 
                $xtpl->parse('main.recipe');
            }
            $num++;
        }

        $xtpl->parse('main');
        return $xtpl->out('main');
    }

}
