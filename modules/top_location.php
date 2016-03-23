<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class top_location extends Module {

    function top_location() {
        $this->file = 'top_location.html';
        parent::module();
    }

    function draw() {
        global $skin, $title, $keywords, $description;
        $xtpl = new XTemplate($this->tpl);

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);


        $arrRegion = array(
            'mien-bac'      => 'Miền Bắc',
            'mien-trung'    => 'Miền Trung',
            'mien-nam'      => 'Miền Nam'
        );
        
        $i = 1;
        foreach($arrRegion as $region =>$tabname){
            $DiningVenues = new DiningVenues();
            $arrDiningVenues = $DiningVenues->getDiningVenuesByRegion($region);
            $num = 1;
            foreach ($arrDiningVenues as $key => $value) {
                if($key == 0){
                    $xtpl->assign('name0', $value['name']);
                    $xtpl->assign('img0', getThumbnail('thumb-480-crop', $value['img']));
                    $xtpl->assign('link0', createLink('dia-diem', array('id'=>$value['id'], 'title' => $value['name'])));
                    
                    $html_map0 = '';
                    if($value['latitude'] != '' && $value['longitude'] != ''){
                        $html_map0 = '<div class="location"><a href="'. createLink('map', array('id'=>$value['id'], 'title' => $value['name'])). '" class="iframe-colorbox"><i class="fa fa-map-marker"></i><span>Bản đồ</span></a></div>';
                    }
                    $xtpl->assign('html_map0', $html_map0);
                    $xtpl->assign('num0', $num);
                }else{
                    $xtpl->assign('num', $num);
                    $xtpl->assign('name', $value['name']);
                    $xtpl->assign('link', createLink('dia-diem', array('id'=>$value['id'], 'title' => $value['name'])));
                    $html_map = '';
                    if($value['latitude'] != '' && $value['longitude'] != ''){
                        $html_map =  '<div><a href="'. createLink('map', array('id'=>$value['id'], 'title' => $value['name'])). '" class="iframe-colorbox"><i class="fa fa-map-marker"></i> <span>Bản đồ</span></a></div>';
                    }
                    $xtpl->assign('html_map', $html_map);
                    $xtpl->parse('main.region.location');
                }
                $num++;
            }
            $xtpl->assign('i', $i);
            if($i == 1){
                $xtpl->assign('display', 'display: block;');
                $xtpl->assign('class', 'active');
            }else{
                $xtpl->assign('display', 'display: none;');
                $xtpl->assign('class', '');
            }
            
            $xtpl->assign('tabname', $tabname);
            $xtpl->parse('main.tab_region');
            $xtpl->parse('main.region');
            $i++;
        }



        $xtpl->parse('main');
        return $xtpl->out('main');
    }

}
