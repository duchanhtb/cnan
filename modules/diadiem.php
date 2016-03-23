<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class diadiem extends Module {

    function diadiem() {
        $this->file = 'diadiem.html';
        parent::module();
    }

   
    function draw() {
        global $skin;
        $xtpl = new XTemplate($this->tpl);

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
        
        register_script('google-map', '//maps.google.com/maps/api/js?sensor=true');
        register_script('gmap', $skin_path.'asset/js/gmaps.js');
        $xtpl->assign('current_page', curPageURL());
        
        $id = getCmsId();
        if($id){
            $xtpl->assign('locate_id', alphaID($id));
            
            $DiningVenous = new DiningVenues();
            $DiningVenous->read($id);
            
            if($DiningVenous->status == 0){
                show_404_page();
                die;
            }
            
            
            $Member = GetSession('user');
            if($DiningVenous->user_id == $Member['id']){
                $edit_link = createLink('chia-se-dia-diem').'?id='.alphaID($id);
                $html_edit = '&nbsp;&nbsp;<a href="'.$edit_link.'" style="font-size:12px; display:inline-block">[Sửa]</a>';
                $xtpl->assign('edit_link', $html_edit);
            }
            
            
            // rich snippets
            $xtpl->assign('day_publish', date('Y-m-d',  strtotime($DiningVenous->date_created)));
            $xtpl->assign('day_publish_txt', date('F j, Y',strtotime($DiningVenous->date_created)));
            $xtpl->assign('fix_rand', ($id % 100));
            
            // map
            if($DiningVenous->latitude != '' && $DiningVenous->longitude != ''){
                $xtpl->assign('latitude', $DiningVenous->latitude);
                $xtpl->assign('longitude', $DiningVenous->longitude);
                $xtpl->parse('main.map');
            }
            
            // content
            $xtpl->assign('name', $DiningVenous->name);
            $xtpl->assign('content', $DiningVenous->content);
            
            
            // html info
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
                if($DiningVenous->$type != ''){
                    if($type == 'website'){
                        $html_info .= '<li><label>Website:</label> <a href="'.$DiningVenous->$type.'" target="_blank">'.$DiningVenous->$type.'</a></li>';
                    }else{
                        $html_info .= '<li><label>'.$value.':</label> '.$DiningVenous->$type.'</li>';
                    }
                }
            }
            if($html_info != ''){
                $html_info = '<div class="contact-info" style="margin-bottom: 40px;">'
                            . '<h1 class="title-section" style="margin-top: 40px;">Liên hệ</h1>'
                            . '<ul class="no-padding">'.$html_info.'</ul></div>';
            }
            $xtpl->assign('html_info', $html_info);
            
            
            // report error
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
            
            
            
            // gallery 
            $DiningVenousImage = new DiningVenousImage();
            $arrDiningVenousImages = $DiningVenousImage->getDiningVenousImage($id);
            if (count($arrDiningVenousImages) > 0) {
                foreach ($arrDiningVenousImages as $image) {
                    $xtpl->assign('gallery_img_full', base_url() . $image['img']);
                    $xtpl->assign('gallery_img', getThumbnail('thumb-480-crop', $image['img']));
                    $xtpl->assign('gallery_alt', $image['alt']);
                    $xtpl->parse('main.gallery.detail');
                }
                $xtpl->parse('main.gallery');
            }
            
            
            // thong tin tac gia
            $user_id = $DiningVenous->user_id;
            $User = new User();
            $User->read($user_id);
            $xtpl->assign('author_img', getThumbnail('thumb-500-crop', $User->avatar));
            $xtpl->assign('author_fullname', $User->fullname);
            $xtpl->assign('author_link', createLink('thanh-vien', array('id' => $user_id, 'title' => $User->fullname)));
            $xtpl->assign('author_info', $User->getHtmlInfo($user_id));
            
            
            //other locate
            $otherLocate = $DiningVenous->get('*',"AND status = 1 AND `id`!=  $id ", 0, 6);
            foreach($otherLocate as $key=>$locate){
                $xtpl->assign('other_name', $locate['name']);
                $xtpl->assign('other_address', $locate['address']);
                $xtpl->assign('other_link', createLink('dia-diem', array('id' => $locate['id'], 'title' => $locate['name'])));
                $xtpl->assign('other_img', getThumbnail('thumb-480-crop', $locate['img']));
                
                $html_province = '';
                if($locate['province_id'] > 0 ){
                    $Province = new Provinces();
                    $Province->read($locate['province_id']);
                    $html_province = '<div class="locate"><i class="fa fa-map-marker"></i>'.$Province->name.'</div>';
                    $xtpl->assign('html_province', $html_province);
                }
                $xtpl->parse('main.other');
            }
            
            
            // for SEO
            addTitle($DiningVenous->name);
            addKeywords($DiningVenous->name);
            addDescription(strip_tags(_substr($DiningVenous->content, 150)));
            if ($DiningVenous->img) {
                addGraphTags('og-image', 'image', getThumbnail('og-image', $DiningVenous->img));
            }
            addGraphTags('og-title', 'title', $DiningVenous->name);
            addGraphTags('og-url', 'url', curPageURL());
            addGraphTags('og-description', 'description', strip_tags(_substr($DiningVenous->content,150)));
            
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
