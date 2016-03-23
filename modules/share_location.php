<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class share_location extends Module {

    function share_location() {
        $this->file = 'share_location.html';
        parent::module();
    }

    function draw() {
        global $skin, $title, $keywords, $description;
        $xtpl = new XTemplate($this->tpl);

        $Member = GetSession('user');
        if (!$Member) {
            $link_login = createLink('dang-nhap');
            redirect($link_login);
        }

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);


        register_script('user', $skin_path . 'asset/js/user.js');
        register_style('post', $skin_path . 'asset/css/post.css');
        register_script('widget', $skin_path . 'asset/js/jquery.ui.widget.js');
        register_script('ajaxupload', $skin_path . 'asset/js/ajaxupload.js');
        register_script('fileupload', $skin_path . 'asset/js/jquery.fileupload.js');
        register_script('prettyCheckable', $skin_path . 'asset/js/prettyCheckable.min.js');
        //register_script('editor', '//cdn.ckeditor.com/4.4.7/standard/ckeditor.js', 3.0, false);
        register_script('ckeditor', base_url() . 'editor/ckeditor/ckeditor.js', 3.0, true);
        register_script('ckfinder', base_url() . 'editor/ckfinder/ckfinder.js', 3.0, true);
        register_script('googlemap', 'http://maps.google.com/maps/api/js?sensor=false&libraries=places');
        register_script('locationpicker', $skin_path . 'asset/js/locationpicker.jquery.min.js');

        $data = $this->setDefaultData();
        $id = Input::get('id', 'txt', '');
        if ($id) {
            $locate_id = alphaID($id, true);
            $data = $this->getLocateData($locate_id);
            addTitle($data['locate_name']);
        }

        
        if ($data && count($data) > 0) {
            foreach ($data as $key => $value) {
                $xtpl->assign($key, $value);
                
                if($key == 'images' && $value != NULL){
                    foreach($value as $key=>$lcImg){
                        $xtpl->assign('img_src', getThumbnail('thumb-150', $lcImg['img']));
                        $xtpl->assign('img_id', $lcImg['id']);
                        $xtpl->parse('main.locate_image');
                    }
                }
            }
        }

        $xtpl->parse('main');
        return $xtpl->out('main');
    }

    // get locate infomation by id, return array
    function getLocateData($id) {
        $Member = GetSession('user');
        $data = array();
        $DiningVenues = new DiningVenues();
        $DiningVenousImage = new DiningVenousImage();

        $DiningVenues->read($id);
        $data = array(
            'src_img'   => base_url() . 'uploads/noimage.jpg'
        );
        
        if ($DiningVenues->user_id == $Member['id']) {
            
            $data['locate_id'] = alphaID($id);
            $data['locate_name'] = $DiningVenues->name;
            $data['img'] = $DiningVenues->img;
            $data['src_img'] = ($DiningVenues->img != '') ? getThumbnail('thumb-150', $DiningVenues->img) : base_url() . 'uploads/noimage.jpg';
            $data['brief'] = strip_tags($DiningVenues->brief);
            $data['content'] = $DiningVenues->content;
            $data['address'] = $DiningVenues->address;
            $data['latitude'] = $DiningVenues->latitude;
            $data['longitude'] = $DiningVenues->longitude;
            $data['phone'] = $DiningVenues->phone;
            $data['email'] = $DiningVenues->email;
            $data['facebook'] = $DiningVenues->facebook;
            $data['website'] = $DiningVenues->website;
            $data['images'] = $DiningVenousImage->getDiningVenousImage($id);
        }
        
        return $data;
    }

    // set default data
    function setDefaultData() {
        // default data
        $data = array(
            'locate_id' => '',
            'src_img' => base_url() . 'uploads/noimage.jpg',
        );
        return $data;
    }

}
