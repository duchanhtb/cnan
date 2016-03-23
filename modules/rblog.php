<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 * @desc module list html of introduction
 */
class rblog extends Module {

    function rblog() {

        $id = getCmsId();
        if ($id) {
            $this->file = 'blog_detail.html';
        } else {
            $this->file = 'blog_list.html';
        }

        parent::module();
    }

    function draw() {
        global $skin;
        $xtpl = new XTemplate($this->tpl);

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
        $xtpl->assign('current_page', curPageURL());

        $xtpl->assign('heading', 'Blog ẩm thực');


        $Blog = new Blog();
        $id = getCmsId();
        if ($id) {
            $Blog->read($id);

            // recipe detail
            $xtpl->assign('title', $Blog->title);
            $xtpl->assign('content', $Blog->content);

            // for SEO
            addTitle($Blog->title);
            addKeywords($Blog->meta_keyword);
            addDescription($Blog->meta_description);
            if ($Blog->img) {
                addGraphTags('og-image', 'image', getThumbnail('og-image', $Blog->img));
            }
            addGraphTags('og-title', 'title', $Blog->title);
            addGraphTags('og-url', 'url', curPageURL());
            addGraphTags('og-description', 'description', $Blog->brief);
        } else {
            $con = " AND status = 1 ";
            $Blog->num_per_page = 10;
            $p = Input::get('p', 'int', 1);
            $total_row = $Blog->count($con);
            $total_page = ceil($total_row / $Blog->num_per_page);
            $paging = paging($p, $total_page, curPageURL());

            $xtpl->assign('paging', $paging);

            $order = Input::get('order', 'txt', 'DESC');
            $sort = Input::get('sort', 'txt', 'ordering');
            $listBlog = $Blog->getBlog($con, $sort, $order, $p);
           
            foreach ($listBlog as $key => $value) {
                $User = new User();
                $userInfo = $User->getDisplayInfo($value['user_id'], true);
                $link = createLink('blog-am-thuc', array('id' => $value['id'], 'title' => $value['title']));
                $xtpl->assign('link', $link);
                $xtpl->assign('title', $value['title']);
                $xtpl->assign('img', getThumbnail('thumb-480-crop', $value['img']));
                $xtpl->assign('author_fullname', $userInfo['name']);
                $xtpl->assign('author_avatar', $userInfo['avatar']);
                $xtpl->assign('author_point', $userInfo['point']);
                $xtpl->assign('author_avatar', $userInfo['avatar']);
                $xtpl->assign('author_recipe_post', $userInfo['num_recipe_post']);
                $xtpl->assign('brief', $value['brief']);
                $xtpl->assign('date', date('d/m/Y', strtotime($value['date_created'])));
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
