<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 */
class search extends Module {

    function search() {
    }

    function draw() {
        $subpage = getSubPage();
        switch ($subpage){
            case 'cong-thuc':
                    return $this->recipe();
                break;
            
            case 'dia-diem':
                    return $this->locate();
                break;
            
            default:
                    return $this->main();
                break;
        }
    }
    
    
    function recipe(){
        
    }
    
    
    function locate(){
        
    }
    
    
    function main(){
        global $skin, $title, $description;
        $this->file = 'search.html';
        parent::module();
        
        $xtpl = new XTemplate($this->tpl);
        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
        
        $User = new User();
        $q  = Input::get('q','txt','');
        addTitle($q.' - các món ăn liên quan tới '.$q);
        addDescription($q.' - hướng dẫn nấu ăn liên quan tới '.$q);
        $xtpl->assign('q', $q);        
        
        $num_get = 12;
        
        // search recipe
        $Recipe = new Recipe();
        $con = " AND `status` = 1 AND `title` LIKE  '%$q%' ";
        $total_recipe = $Recipe->count($con);
        $arrRecipe = $Recipe->get('*', $con, ' ordering DESC ', 0, $num_get);
        if (count($arrRecipe) > 0) {
            $xtpl->assign('html_result_recipe', '<h3>Có ('.$total_recipe.') kết quả được tìm thấy</h3>');
            foreach ($arrRecipe as $key => $recipe) {
                $userInfo = $User->getDisplayInfo($recipe['user_id']);
                $xtpl->assign('recipe_link', createLink('cong-thuc', array('id' => $recipe['id'], 'title' => $recipe['title'])));
                $xtpl->assign('recipe_name', $recipe['title']);
                $xtpl->assign('recipe_img', getThumbnail('thumb-480-crop', $recipe['img']));
                $xtpl->assign('recipe_author', $userInfo['name']);
                $xtpl->assign('recipe_point', $userInfo['point']);
                $xtpl->assign('recipe_author_link', createLink('thanh-vien', array('id' => $recipe['user_id'], 'title' => $userInfo['name'])));
                $xtpl->assign('recipe_intruction', _substr(strip_tags($recipe['intruction']), 300));
                $ck_time = $Recipe->getDisplayCookingTime($recipe['cooking_time']);
                if ($ck_time) {
                    $xtpl->assign('recipe_cooking_time', '<div class="time-cooking"><i class="fa fa-clock-o"></i>' . $ck_time . '</div>');
                } else {
                    $xtpl->assign('recipe_cooking_time', '');
                }
                $xtpl->parse('main.recipe');
            }
        }else{
            $link_share_recipe = createLink('chia-se-cong-thuc');
            $html_share_recipe = '<h3 style="text-align:center">Hiện tại chưa có công thức nấu ăn nào liên quan tới <strong>'.$q.'</strong></h3>'
                    . '<p style="text-align:center"><a href="'.$link_share_recipe.'" class="cnan-button" style="margin: 0 auto;">Hãy là người đầu tiên chia sẻ &#8594;</a></p>';
            $xtpl->assign('html_share_recipe', $html_share_recipe);
        }        
        
        // search locate
        $DiningVenues = new DiningVenues();
        $DiningVenues->num_per_page = $num_get;
        $field = '`id`, `user_id`, `name`, `img`, `province_id`, `address` ';
        $con = " AND `status` = 1 AND `name` LIKE  '%$q%' ";
        $order = " `ordering` DESC,  `id` DESC ";
        $total_row = $DiningVenues->count($con);
        $start = 0;
        $total_locate = $DiningVenues->count($con);
        $arrLocate =  $DiningVenues->get($field, $con, $order, $start, $DiningVenues->num_per_page);
        if($arrLocate && count($arrLocate) > 0 ){
            $xtpl->assign('html_result_locate', '<h3>Có ('.$total_locate.') kết quả được tìm thấy</h3>');
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
        }else{
            $link_share_locate = createLink('chia-se-dia-diem');
            $html_share_locate = '<h3 style="text-align:center">Hiện tại chưa có địa điểm nào liên quan tới <strong>'.$q.'</strong></h3>'
                    . '<p style="text-align:center"><a href="'.$link_share_locate.'" class="cnan-button" style="margin: 0 auto;">Hãy là người đầu tiên chia sẻ &#8594;</a></p>';
            $xtpl->assign('html_share_locate', $html_share_locate);
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
