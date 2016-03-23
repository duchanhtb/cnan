<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */

class gmap extends Module {

    function gmap() {
        $this->file = 'gmap.html';
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
            $DiningVenues = new DiningVenues();
            $DiningVenues->read($id);
            
            $arrayInfo  = array(
                'address'   => 'Địa chỉ',
                'phone'     => 'Điện thoại',
                'mobile'    => 'Di động',
                'email'     => 'Email',
                'facebook'  => 'Facebook',
                'yahoo'     => 'Yahoo',
                'skype'     => 'Skype',
                'website'   => 'Website'
            );
            
            $html_info = '';
            foreach($arrayInfo as $type=>$value){
                if($DiningVenues->$type != ''){
                    if($type == 'website'){
                        $html_info .= '<li><label>Website:</label> <a href="'.$DiningVenues->$type.'" target="_blank">'.$DiningVenues->$type.'</a></li>';
                    }else{
                        $html_info .= '<li><label>'.$value.':</label> '.$DiningVenues->$type.'</li>';
                    }
                }
            }
            $xtpl->assign('html_info', $html_info);
            $xtpl->assign('latitude', $DiningVenues->latitude);
            $xtpl->assign('longitude', $DiningVenues->longitude);
        }else{
            return '';
        }
        
        $xtpl->parse('main');
        return $xtpl->out('main');
    }

    
}
