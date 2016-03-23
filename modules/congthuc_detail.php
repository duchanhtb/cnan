<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 * @desc module list html of introduction
 */
class congthuc_detail extends Module {

    function congthuc_detail() {
        $this->file = 'congthuc_detail.html';
        parent::module();
    }

    function draw() {
        global $skin, $title, $keywords, $description;
        $xtpl = new XTemplate($this->tpl);

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
        $xtpl->assign('current_page', curPageURL());


        $id = getCmsId();
        if ($id) {
            $Recipe = new Recipe();
            $Recipe->read($id);
            if($Recipe->status == 0){
                show_404_page();
                die;
            }
            
            
            $xtpl->assign('fix_rand', ($id % 100));
            // add history 
            $Recipe->addHistory($id);
            

            // recipe category
            $RecipeCategory = new RecipeCategory();
            $arrCategory = $RecipeCategory->getCategory($id);
            $html_category = '';
            foreach ($arrCategory as $cat_id => $cat_name) {
                $cat_link = createLink('danh-muc', array('id' => $cat_id, 'title' => $cat_name));
                $html_category .= '<h2><a title="' . $cat_name . '" href="' . $cat_link . '">' . $cat_name . '</a></h2>,';
            }
            $xtpl->assign('html_category', trim(trim($html_category),','));
            
            $Member = GetSession('user');
            $html_edit = '';
            if($Member['id'] == $Recipe->user_id){
                $edit_link = createLink('chia-se-cong-thuc').'?id='.alphaID($id);
                $html_edit = '&nbsp;&nbsp;<a href="'.$edit_link.'" style="font-size:12px; display:inline-block">[Sửa]</a>';
                $xtpl->assign('edit_link', $html_edit);
            }
            
            
            // recipe detail
            $xtpl->assign('recipe_id', alphaID($id));
            $intruction = cleanHTMLContent($Recipe->intruction);
            $xtpl->assign('intruction', $intruction);
            $brief = cleanHTMLContent(strip_tags($Recipe->brief));
            $xtpl->assign('title', $Recipe->title);
            $xtpl->assign('brief', $brief);
            $xtpl->assign('hits', $Recipe->hits);
            $xtpl->assign('recipe_link', curPageURL());
            $xtpl->assign('img', getThumbnail('thumb-500-crop', $Recipe->img));
            $xtpl->assign('year', date('Y', strtotime($Recipe->date_created)));
            $xtpl->assign('recipe_info', $Recipe->getHtmlRecipeInfo($id));


            // thanh phan nguyen lieu
            $xtpl->assign('link_list_ingridient', createLink('danh-sach-nguyen-lieu'));
            $ingridients = unserialize($Recipe->ingridients);
            if ($ingridients && count($ingridients) > 0) {
                $i = 1;
                foreach ($ingridients as $value) {
                    $xtpl->assign('ingridient_order', $i);
                    $xtpl->assign('ingridient_name', $value['name']);
                    $xtpl->assign('ingridient_value', $value['value']);

                    $ingridient_name = trim($value['name']);
                    $Ingridient = new Ingridient();
                    $ingridient_info = $Ingridient->getIngridientByName($ingridient_name);
                    if (is_array($ingridient_info) && count($ingridient_info) > 0 ) {
                        $xtpl->assign('ingridient_link', createLink('nguyen-lieu', array('id' => $ingridient_info['id'], 'title' => $ingridient_info['name'])));
                    } else {
                        $xtpl->assign('ingridient_link', '#');
                    }
                    $xtpl->parse('main.ingridient.ingridient_detail');
                    $i++;
                }
                $xtpl->parse('main.ingridient');
            }

            if ($ingridients && count($ingridients) > 0) {
                $xtpl->assign('title_content', 'Các bước thực hiện');
            } else {
                $xtpl->assign('title_content', 'Chi tiết');
            }


            // gallery 
            $RecipeImages = new RecipeImages();
            $arrRecipeImages = $RecipeImages->getRecipeImage($id);
            if (count($arrRecipeImages) > 0) {
                foreach ($arrRecipeImages as $image) {
                    $xtpl->assign('gallery_img_full', base_url() . $image['img']);
                    $xtpl->assign('gallery_img', getThumbnail('thumb-500-crop', $image['img']));
                    $xtpl->assign('gallery_alt', ($image['alt'] != '') ? $image['alt'] : $Recipe->title);
                    $xtpl->parse('main.gallery.detail');
                }
                $xtpl->parse('main.gallery');
            }

            
            // video
            if($Recipe->youtube_id && $Recipe->has_video){
                $xtpl->assign('youtube_id', $Recipe->youtube_id);
                $xtpl->parse('main.video');
            }

            // thong tin tac gia
            $user_id = $Recipe->user_id;
            $User = new User();
            $User->read($user_id);
            $xtpl->assign('author_img', getThumbnail('thumb-500-crop', $User->avatar));
            $xtpl->assign('author_fullname', $User->fullname);
            $xtpl->assign('author_link', createLink('thanh-vien', array('id' => $user_id, 'title' => $User->fullname)));
            $xtpl->assign('author_info', $User->getHtmlInfo($user_id));
            $link_recipe_author = createLink('thanh-vien', array('id' => $User->id, 'title' => $User->fullname));
            $xtpl->assign('link_recipe_author', $link_recipe_author);

            // cong thuc da dang boi cung nguoi dung
            $recipeUser = $Recipe->getRecipeUser($user_id, $id);
            if (count($recipeUser) > 0) {
                $xtpl->assign('link_recipe_author', $link_recipe_author);
                foreach ($recipeUser as $k => $v) {
                    $userInfo = $User->getDisplayInfo($v['user_id']);
                    $xtpl->assign('ru_title', $v['title']);
                    $xtpl->assign('ru_fullname', $userInfo['name']);
                    $xtpl->assign('ru_link', createLink('cong-thuc', array('title' => $v['title'], 'id' => $v['id'])));
                    $xtpl->assign('ru_img', getThumbnail('thumb-480-crop', $v['img']));
                    $xtpl->assign('ru_intruction', _substr(strip_tags($v['intruction']), 300));
                    $xtpl->assign('ru_author', $userInfo['name']);
                    $xtpl->assign('ru_point', $userInfo['point']);
                    $xtpl->assign('ru_author_link', createLink('thanh-vien', array('id' => $user_id, 'title' => $userInfo['name'])));
                    $ck_time = $Recipe->getDisplayCookingTime($v['cooking_time']);
                    if ($ck_time) {
                        $xtpl->assign('ru_cooking_time', '<div class="time-cooking"><i class="fa fa-clock-o"></i>' . $ck_time . '</div>');
                    } else {
                        $xtpl->assign('ru_cooking_time', '');
                    }

                    $xtpl->parse('main.author.author_recipe');
                }
                $xtpl->parse('main.author');
            }


            // report error
            $html_email_report = '';
            $UserLogin = GetSession('user');
            if ($UserLogin && count($UserLogin) > 0) {
                $html_email_report = '<div class="post-input" style="display:none">
					   <input class="no-padding" type="hidden" name="email" value="' . $UserLogin['email'] . '" id="report-email" placeholder="Email của bạn" />
				    </div>';
            } else {
                $html_email_report = '<div class="post-input">
                                        <input class="no-padding" type="text" name="email" value="" id="report-email" placeholder="Email của bạn" />
                                    </div>';
            }
            $xtpl->assign('html_email_report', $html_email_report);


            // update hits view
            $hits = $Recipe->hits;
            $Recipe->hits = $hits + 1;
            $Recipe->update($id, array('hits'));

            // print
            $xtpl->assign('link_print', createLink('print', array('id' => $id, 'title' => $Recipe->title)));

            // for SEO
            addTitle($Recipe->title);
            addKeywords($Recipe->title);
            addDescription(strip_tags($Recipe->brief));
            if ($Recipe->img) {
                addGraphTags('og-image', 'image', getThumbnail('og-image', $Recipe->img));
            }
            addGraphTags('og-title', 'title', $Recipe->title);
            addGraphTags('og-url', 'url', curPageURL());
            addGraphTags('og-description', 'description', strip_tags($Recipe->brief));
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
