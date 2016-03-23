<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 * @desc module list html of introduction
 */
class list_diadiem extends Module {

    function list_diadiem() {
        $this->file = 'list_diadiem.html';
        parent::module();
    }

    function draw() {
        global $oDb, $skin;
        $xtpl = new XTemplate($this->tpl);

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
                
        $DiningVenues = new DiningVenues();
        
        $DiningVenues->num_per_page = 12;
        $p = Input::get('p','int',1);
        $start = ($p - 1)*$DiningVenues->num_per_page;
        $field = '`id`, `user_id`, `name`, `img`, `province_id`, `address` ';
        $con = " AND `status` = 1 ";
        $order = " `ordering` DESC,  `id` DESC ";
        
        $total_row = $DiningVenues->count($con);
        
        $total_page = ceil($total_row / $DiningVenues->num_per_page);
        $paging = paging($p, $total_page, curPageURL());
        $xtpl->assign('paging', $paging);
        
        $arrLocate =  $DiningVenues->get($field, $con, $order, $start, $DiningVenues->num_per_page);        
        if($arrLocate && count($arrLocate) > 0 ){
            foreach($arrLocate as $key=>$value){
                $xtpl->assign('name', $value['name']);
                $xtpl->assign('link', createLink('dia-diem', array('id'=>$value['id'], 'title' =>$value['name'])));
                $xtpl->assign('img', getThumbnail('thumb-500-crop', $value['img']));
                $html_address = '';
                if($value['address'] != ''){
                    $html_address = '<div class="author ellipsis"><h4>Add: '.$value['address'].'</h4></div>';
                }
                $xtpl->assign('html_address', $html_address);
                
                $html_locate = '';
                if($value['province_id'] > 0 ){
                    $Province = new Provinces();
                    $Province->read($value['province_id']);
                    $html_locate = '<div class="locate"><i class="fa fa-map-marker"></i>'.$Province->name.'</div>';
                }
                $xtpl->assign('html_locate', $html_locate);
                $xtpl->parse('main.locate');
            }
        }
        
        // dia diem gan ban
        $arrNearDining = $DiningVenues->getDiningVenuesByLocation();
        foreach($arrNearDining as $key=>$value){
            $xtpl->assign('near_link', createLink('dia-diem', array('id'=>$value['id'], 'title' => $value['name'])));
            $xtpl->assign('near_name', $value['name']);
            $xtpl->assign('near_img', getThumbnail('thumb-500-crop', $value['img']));
            $html_near_address = '';
            if($value['address'] != ''){
                $html_near_address = '<div class="author ellipsis"><h4>ADD: '.$value['address'].'</h4></div>';
            }
            $xtpl->assign('html_near_address', $html_near_address);
            $xtpl->parse('main.near_locate');
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
