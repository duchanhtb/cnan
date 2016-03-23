<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class list_category extends Module {

    function list_category() {
        $this->file = 'list_category.html';
        parent::module();
    }

    function draw() {
      
        global $skin;
        $xtpl = new XTemplate($this->tpl);

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
        
        // add script for custom scroll bar
        register_script('slick', $skin_path.'asset/js/jquery.mCustomScrollbar.js');        
        
        $RecipeCategory = new RecipeCategory();
        $arrRecipeCategory  = $RecipeCategory->getGroupCategory();
        
        
        // left
        foreach($arrRecipeCategory as $key=>$value){
            $link= createLink('danh-muc', array('id'=>$value['id'], 'title' => $value['name']));
            $xtpl->assign('gr_link', $link);
            $xtpl->assign('gr_name', $value['name']);
            
            $html_subcat = '';
            if(isset($value['subCat']) && count($value['subCat']) > 0 ){
                $html_subcat = $this->getHtmlSubCategory($value['subCat']);
            }
            $xtpl->assign('html_subcat', $html_subcat);
            
            $xtpl->parse('main.group');
        }
        
        
        // right content
        foreach($arrRecipeCategory as $key=>$value){
            $xtpl->assign('cat_name', $value['name']);
            $xtpl->assign('cat_link', createLink('danh-muc', array('id' => $value['id'], 'title' => $value['name'])));
            if(isset($value['subCat']) && count($value['subCat']) > 0 ){
                foreach($value['subCat'] as $k=>$v){
                    if($k==6) break;
                    $xtpl->assign('sub_name', $v['name']);
                    $xtpl->assign('sub_link', createLink('danh-muc', array('id' => $v['id'], 'title' => $v['name'])));
                    $xtpl->assign('sub_img', getThumbnail('thumb-480-crop', $v['img']));
                    $xtpl->parse('main.category.sub');
                }
            }
            $xtpl->parse('main.category');
        }
        
        $xtpl->parse('main');
        return $xtpl->out('main');
        
    }
    
    
    function getHtmlSubCategory($arrSub){
        $html = '';
        foreach ($arrSub as $key=>$value){
            $link= createLink('danh-muc', array('id'=>$value['id'], 'title' => $value['name']));
            $html .= '<li><a href="'.$link.'">'.$value['name'].'</a></li>';
        }
        if($html != ''){
            $html = '<ul class="sub-category">'.$html.'</ul>';
        }
       
        return $html;
    }

}
