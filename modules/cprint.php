<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class cprint extends Module {

    function cprint() {
        $this->file = 'print.html';
        parent::module();
    }

    function draw() {
        global $skin;
        $xtpl = new XTemplate($this->tpl);

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
        
        $id = getCmsId();
        if($id){
            $Recipe = new Recipe();
            $Recipe->read($id);
            $xtpl->assign('title', $Recipe->title);
            
            // recipe detail
            $xtpl->assign('recipe_id', alphaID($id));
            $intruction = cleanHTMLContent($Recipe->intruction);
            $xtpl->assign('intruction', preg_replace("/<img[^>]+\>/i", "", $intruction));
            $brief = cleanHTMLContent(strip_tags($Recipe->brief));
            $xtpl->assign('title', $Recipe->title);
            $xtpl->assign('brief', $brief);
            $xtpl->assign('hits', $Recipe->hits);
            $xtpl->assign('recipe_link', curPageURL());
            $xtpl->assign('img', getThumbnail('thumb-500-crop', $Recipe->img));
            $xtpl->assign('year', date('Y', strtotime($Recipe->date_created)));
            $xtpl->assign('recipe_info', $Recipe->getHtmlRecipeInfo($id, 'right'));


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
        }

        $xtpl->parse('main');
        return $xtpl->out('main');
    }

}
