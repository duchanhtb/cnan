<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class share_recipe extends Module {

    function share_recipe() {
        $this->file = 'share_recipe.html';
        parent::module();
    }

    function draw() {
        global $skin;
        $xtpl = new XTemplate($this->tpl);

        $user = GetSession('user');
        if (!$user) {
            $link_login = createLink('dang-nhap');
            redirect($link_login);
            die;
        }

        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);

        // unset session multiimage
        $arrImg = GetSession('multiimages');
        if(count($arrImg) > 0 ){
            $RecipeImages = new RecipeImages();
            foreach($arrImg as $img_id){
                $RecipeImages->remove($img_id);
            }
        }
        

        // add style and register
        register_script('user', $skin_path . 'asset/js/user.js');
        register_style('post', $skin_path . 'asset/css/post.css');
        register_script('widget', $skin_path . 'asset/js/jquery.ui.widget.js');
        register_script('ajaxupload', $skin_path . 'asset/js/ajaxupload.js');
        register_script('fileupload', $skin_path . 'asset/js/jquery.fileupload.js');
        register_script('prettyCheckable', $skin_path . 'asset/js/prettyCheckable.min.js');
        //register_script('editor', '//cdn.ckeditor.com/4.4.7/standard/ckeditor.js', 3.0, false);
        register_script('ckeditor', base_url() . 'editor/ckeditor/ckeditor.js', 3.0, true);
        register_script('ckfinder', base_url() . 'editor/ckfinder/ckfinder.js', 3.0, true);

        $RecipeCategory = new RecipeCategory();
        $arrGroup = $RecipeCategory->getGroupCategory();

        $data = $this->setDefaultData();
        $Recipe = new Recipe();
        $Member = GetSession('user');

        $id = Input::get('id', 'txt', '');
        if ($id) {
            $recipe_id = alphaID($id, true);
            $data = $this->getRecipeData($recipe_id);
            if($data){
                addTitle($data['recipe_name']);
            }
        }

        if ($data && count($data) > 0) {
            foreach ($data as $key => $value) {
                $xtpl->assign($key, $value);
                if ($key == 'ingridients') {
                    foreach ($value as $ing) {
                        $xtpl->assign('ingridient_name', $ing['name']);
                        $xtpl->assign('ingridient_unit', $ing['value']);
                        $xtpl->parse('main.ingridient');
                    }
                }
                if ($key == 'yield' && $value != NULL) {
                    $xtpl->assign('yield_' . $value, 'selected=""');
                }else{
                    $xtpl->assign('yield_null', 'selected=""');
                }
                
                if ($key == 'cooking_time' && $value != NULL) {
                    switch ($value){
                        case '1':
                            $xtpl->assign('checked_1', 'checked=""');
                            break;
                        case '2':
                            $xtpl->assign('checked_2', 'checked=""');
                            break;
                        case '3':
                            $xtpl->assign('checked_3', 'checked=""');
                            break;
                        case '4':
                            $xtpl->assign('checked_4', 'checked=""');
                            break;
                        default:
                            $xtpl->assign('checked_2', 'checked=""');
                            break;
                            break;
                    }
                }
                
                
                if($key == 'images' && $value != NULL){
                    foreach($value as $key=>$rcImg){
                        $xtpl->assign('img_src', getThumbnail('thumb-150', $rcImg['img']));
                        $xtpl->assign('img_id', $rcImg['id']);
                        $xtpl->parse('main.recipe_image');
                    }
                }
            }
        }
        
      
        // begin category
        foreach ($arrGroup as $key => $group) {
            $xtpl->assign('group_name', $group['name']);
            $arrCat = $group['subCat'];
            foreach ($arrCat as $key => $value) {
                $xtpl->assign('cat_name', $value['name']);
                $xtpl->assign('cat_id', $value['id']);
                if (isset($data['category']) && array_key_exists($value['id'], $data['category'])) {
                    $xtpl->assign('checked', 'checked=""');
                }else{
                    $xtpl->assign('checked', '');
                }
                $xtpl->parse('main.group.cat');
            }
            $xtpl->parse('main.group');
        }


        $xtpl->parse('main');
        return $xtpl->out('main');
    }

    
    // get  recipe data by ID
    function getRecipeData($id) {
        $Member = GetSession('user');
        $Recipe = new Recipe();
        $RecipeCategory = new RecipeCategory();
        $RecipeImages = new RecipeImages();
        
        $Recipe->read($id);
        $data = array(
            'src_img'   => base_url() . 'uploads/noimage.jpg'
        );
        if ($Recipe->user_id == $Member['id']) {
            $data['recipe_id']          = alphaID($id);
            $data['recipe_name']        = $Recipe->title;
            $data['youtube']            = ($Recipe->youtube_id != '') ? 'https://www.youtube.com/watch?v='.$Recipe->youtube_id : '';
            $data['brief']              = strip_tags($Recipe->brief);
            $data['ingridients']        = (count(unserialize($Recipe->ingridients)) > 0 ) ? unserialize($Recipe->ingridients) : array(0 => array('name' => '', 'value' => ''));
            $data['intruction']         = $Recipe->intruction;
            $data['cooking_time']       = $Recipe->cooking_time;
            $data['price']              = $Recipe->price;
            $data['nutrition_facts']    = $Recipe->nutrition_facts;
            $data['yield']              = $Recipe->yield;
            $data['img']                = $Recipe->img;
            $data['src_img']            = ($Recipe->img != '') ? getThumbnail('thumb-150', $Recipe->img) : base_url() . 'uploads/noimage.jpg';
            $data['category']           = $RecipeCategory->getCategory($id);
            $data['images']             = $RecipeImages->getRecipeImage($id);                    
        }

        return $data;
    }

    
    // set default value
    function setDefaultData() {
        // default data
        $data = array(
            'recipe_id' => '',
            'src_img' => base_url() . 'uploads/noimage.jpg',
            'ingridients' => array(
                0 => array('name' => '', 'value' => ''),
                1 => array('name' => '', 'value' => ''),
                2 => array('name' => '', 'value' => ''),
            ),
            'cooking_time'  => 2,
            'yield' => 4,
            'category' => array()
        );
        return $data;
    }

}
