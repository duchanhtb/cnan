<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 * @desc module list html of introduction
 */
class nguyenlieu extends Module {

    function nguyenlieu() {
        $this->file = 'nguyenlieu.html';
        parent::module();
    }

    function draw() {
        global $skin, $oDb;
        $xtpl = new XTemplate($this->tpl);

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
        
        // add library ajax queue
        register_script('ajax-queue', $skin_path.'/asset/js/ajax-queue.js');
        
        
        $sql = "SELECT * FROM m_ingridient_group WHERE 1 ";
        $rc =  $oDb->query($sql);
        $rs =  $oDb->fetchAll($rc);
        foreach($rs as $key=>$group){
            if($key == 0){
                $xtpl->assign('class', 'class');
            }else{
                $xtpl->assign('class', 'section');
            }
            $Ingridient = new Ingridient();
            $arrIngridient = $Ingridient->getIngridientByGroup($group['id']);
            foreach($arrIngridient as $k=>$value){
                $xtpl->assign('link', createLink('nguyen-lieu', array('id' => $value['id'], 'title' => $value['name'])));
                $xtpl->assign('name', $value['name']);
                $xtpl->assign('id', $value['id']);
                
                if ($value['img'] != '') {
                    $xtpl->assign('img', getThumbnail('thumb-150', $value['img']));
                    $xtpl->assign('gimage', '');
                } else {
                    $xtpl->assign('img', getThumbnail('thumb-150', $value['img']));
                    $xtpl->assign('gimage', 'g-image');
                }
                
                
                $xtpl->parse('main.group.ingridient');
            }
            
            $xtpl->assign('group_name', $group['name']);
            $xtpl->parse('main.group');
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
