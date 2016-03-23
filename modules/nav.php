<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class nav extends Module {
    
    function nav() {
        $this->file = 'nav.html';
        parent::module();
    }

    function draw() {
        global $skin, $title, $keywords, $description;
        $xtpl = new XTemplate($this->tpl);

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
        $xtpl->assign('link_search', createLink('tim-kiem'));
        $q  = Input::get('q','txt','');
        $xtpl->assign('q', $q);     

        // add link
        $xtpl->assign('link_monngon', createLink('cach-lam-mon-ngon'));
        $xtpl->assign('link_diadiem', createLink('dia-diem-an-ngon'));
        $xtpl->assign('link_amthuc', createLink('am-thuc-co-truyen'));
        $xtpl->assign('link_video', createLink('video-nau-an'));
        
        
        // add class active
        $page = getPage();
        switch($page){
            case "cach-lam-mon-ngon":
                    $xtpl->assign('active_congthuc', 'class="active"');
                break;
            
            case "dia-diem-an-ngon":
                    $xtpl->assign('active_diadiem', 'class="active"');
                break;
            
            case "am-thuc-co-truyen":
                    $xtpl->assign('active_amthuc', 'class="active"');
                break;
            
            case "video-nau-an":
                    $xtpl->assign('active_video', 'class="active"');
                break;
            
            case "home":
            default:
                $xtpl->assign('active_home', 'class="active"');
                break;
        }
        


        $xtpl->parse('main');
        return $xtpl->out('main');
    }

}
