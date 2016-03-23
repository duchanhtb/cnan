<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2015
 * @desc module list html of introduction
 */
class recipe_category extends Module {

    var $heading;

    function recipe_category() {
    }

    function draw() {
        global $skin, $oDb;
        $page = getPage();

        $User = new User();
        $Recipe = new Recipe();
        switch ($page) {
            case "mon-ngon-moi-nhat":
                $this->heading = 'Món ngon mới nhất';

                $this->file = 'category.html';
                parent::module();

                $recipeNew = $this->recipeNew();
                $paging = $recipeNew['html_paging'];
                $arrRecipe = $recipeNew['arr_recipe'];
                break;

            case "mon-an-yeu-thich-nhat":
                $this->heading = 'Món ăn được yêu thích nhất';
                $this->file = 'category.html';
                parent::module();

                $recipeHot = $this->recipeHot();
                $paging = $recipeHot['html_paging'];
                $arrRecipe = $recipeHot['arr_recipe'];
                break;

            case "nguyen-lieu":
                return $this->recipeIngredients();
                break;

            case "am-thuc-co-truyen":
                $this->heading = 'Ẩm thực cổ truyền, những món ăn truyền thống';
                $this->file = 'category.html';
                parent::module();

                $recipeHot = $this->recipeTraditional();
                $paging = $recipeHot['html_paging'];
                $arrRecipe = $recipeHot['arr_recipe'];
                break;

            case "danh-muc":
            default:
                $this->file = 'category.html';
                parent::module();

                $recipeCategory = $this->recipeCategory();
                $paging = $recipeCategory['html_paging'];
                $arrRecipe = $recipeCategory['arr_recipe'];

                break;
        }
        
        addTitle($this->heading);
        
        
        // add template link
        $xtpl = new XTemplate($this->tpl);
        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);
        
        // add heading for category
        $xtpl->assign('heading', $this->heading);

        // paging 
        $xtpl->assign('paging', $paging);

        // loop content
        if (count($arrRecipe) > 0) {
            foreach ($arrRecipe as $key => $recipe) {
                $userInfo = $User->getDisplayInfo($recipe['user_id']);
                $xtpl->assign('recipe_link', createLink('cong-thuc', array('id' => $recipe['id'], 'title' => $recipe['title'])));
                $xtpl->assign('recipe_name', $recipe['title']);
                $xtpl->assign('recipe_img', getThumbnail('thumb-480-crop', $recipe['img']));
                $xtpl->assign('recipe_author', $userInfo['name']);
                $xtpl->assign('author_point', formatPrice($userInfo['point']));
                $xtpl->assign('author_link', createLink('thanh-vien', array('id' => $recipe['user_id'], 'title' => $userInfo['name'])));
                $xtpl->assign('recipe_intruction', _substr(strip_tags($recipe['intruction']), 300));
                $ck_time = $Recipe->getDisplayCookingTime($recipe['cooking_time']);
                if ($ck_time) {
                    $xtpl->assign('recipe_cooking_time', '<div class="time-cooking"><i class="fa fa-clock-o"></i>' . $ck_time . '</div>');
                } else {
                    $xtpl->assign('recipe_cooking_time', '');
                }

                $xtpl->parse('main.recipe');
            }
        } else {
            $html_no_content = '
                <style>
                    .button a {
                        border-radius: 5px;
                        color: #fff;
                        display: block;
                        font-size: 18px;
                        font-weight: 400;
                        height: 40px;
                        line-height: 40px;
                        width:300px;
                        background: rgba(0, 0, 0, 0) linear-gradient(#449bd3, #1579ba) repeat scroll 0 0;
                        margin:0 auto;
                      }
                      .button a:hover{
                        background: rgba(0, 0, 0, 0) linear-gradient(#48aff2, #1a95e5) repeat scroll 0 0;
                      }
                </style>';
            $link = createLink('chia-se-cong-thuc');
            $html_no_content .= '<p>Hiện tại chưa có bài viết nào thuộc mục này, bạn hãy trở thành người đầu tiên chia sẻ công thức</p>';
            $html_no_content .= '<p>&nbsp;</p>
                                <p>&nbsp;</p>
                                <div class="button">
                                    <a  href="'.$link.'">Bắt đầu chia sẻ công thức nấu ăn</a>
                                </div>';
            $xtpl->assign('html_no_content', $html_no_content);
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

    /**
     * @Desc function lay nhung recipe moi nhat
     * @param 
     * @return html
     */
    function recipeNew() {
        global $oDb;

        $result = array(
            'html_paging' => '',
            'arr_recipe' => array()
        );

        $User = new User();
        $Recipe = new Recipe();
        $Recipe->num_per_page = 12;
        $con = " AND status = 1 ";



        // paging
        $p = Input::get('p', 'int', 1);
        $start_row = ($p - 1) * $Recipe->num_per_page;
        $sql = "SELECT count(*) as total FROM " . $Recipe->table . " WHERE 1 " . $con;
        $rc = $oDb->query($sql);
        $rs = $oDb->fetchArray($rc);
        $total_row = $rs['total'];
        $total_page = ceil($total_row / $Recipe->num_per_page);
        $paging = paging($p, $total_page, curPageURL());
        $result['html_paging'] = $paging;

        $arrRecipe = $Recipe->get("*", $con, "id DESC", $start_row, $Recipe->num_per_page);
        $result['arr_recipe'] = $arrRecipe;

        return $result;
    }

    /**
     * @Desc function lay nhung recipe xem nhieu nhat trong khoang thoi gian
     * @param 
     * @return html
     */
    function recipeHot() {
        global $oDb;

        $result = array(
            'html_paging' => '',
            'arr_recipe' => array()
        );

        $User = new User();
        $Recipe = new Recipe();


        // mon an duoc yeu thich nhieu nhat lay trong 20 ngay gan day nhat
         $start_time = date('Y-m-d', time() - 20 * 86400);
        $end_time = date('Y-m-d', time());
        $sql = "SELECT t_recipe.id, t_recipe.user_id, t_recipe.username, t_recipe.title, t_recipe.intruction, t_recipe.cooking_time, t_recipe.img, t_recipe_views.`date`, COUNT( t_recipe_views.hits ) AS tt
				FROM t_recipe
				LEFT JOIN t_recipe_views ON t_recipe.id = t_recipe_views.recipe_id
                                WHERE t_recipe_views.`date` >= '$start_time'  AND `date` <= '$end_time'
				GROUP BY t_recipe_views.recipe_id
				ORDER BY tt DESC 
				LIMIT 0 , 30";
     
        $rc = $oDb->query($sql);
        $rs = $oDb->fetchAll($rc);
        
        $result['arr_recipe'] = $rs;
        return $result;
    }

    /**
     * @Desc function lay nhung recipe theo nguyen lieu truyen vao
     * @param 
     * @return html
     */
    function recipeIngredients() {
        global $oDb, $skin;

        $this->file = 'category_ingredients.html';
        parent::module();

        $xtpl = new XTemplate($this->tpl);
        $skin_path = base_url() . 'skin/' . $skin . '/';
        $xtpl->assign('link_home', base_url());
        $xtpl->assign('skin_path', $skin_path);

        // add library ajax queue
        register_script('ajax-queue', $skin_path . '/asset/js/ajax-queue.js');


        $User = new User();
        $Recipe = new Recipe();
        $Ingridient = new Ingridient();
        $RecipeIngridient = new RecipeIngridient();

        $Recipe->num_per_page = 12;
        $id = getCmsId();
        if ($id) {

            // add heading for category
            $Ingridient->read($id);
            $this->heading = 'Những món ăn có nguyên liệu ' . $Ingridient->name;
            $xtpl->assign('heading', $this->heading);

            $sql = "SELECT count(*) as total FROM  $Recipe->table 
                    WHERE id IN (
                        SELECT recipe_id FROM $RecipeIngridient->table WHERE `ingridient_id` = $id
                    )
                    AND `status` = 1 ";
            $rc = $oDb->query($sql);
            $rs = $oDb->fetchArray($rc);
            $total_row = $rs['total'];
            

            // paging
            $p = Input::get('p', 'int', 1);
            $start_row = ($p - 1) * $Recipe->num_per_page;
            $total_page = ceil($total_row / $Recipe->num_per_page);
            $paging = paging($p, $total_page, curPageURL());
            $xtpl->assign('paging', $paging);
            
            $sql = "SELECT * FROM  $Recipe->table 
                    WHERE id IN (
                        SELECT recipe_id FROM $RecipeIngridient->table WHERE `ingridient_id` = $id
                    )
                    AND `status` = 1 
                    ORDER BY ordering DESC 
                    LIMIT $start_row, $Recipe->num_per_page ";
            $rc = $oDb->query($sql);
            $arrRecipe = $oDb->fetchAll($rc);

            // loop content
            foreach ($arrRecipe as $key => $recipe) {
                $userInfo = $User->getDisplayInfo($recipe['user_id']);
                $xtpl->assign('recipe_id', $recipe['id']);
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


            $xtpl->assign('link_nguyenlieu', createLink('danh-sach-nguyen-lieu'));

            // loop ingridient
            $arrIngridient = $Ingridient->getOtherIngridient($id);
            if ($arrIngridient && count($arrIngridient) > 0) {
                foreach ($arrIngridient as $key => $value) {
                    if ($value['img'] != '') {
                        $xtpl->assign('ingridient_img', getThumbnail('thumb-150', $value['img']));
                        $xtpl->assign('gimage', '');
                    } else {
                        $xtpl->assign('ingridient_img', getThumbnail('thumb-150', $value['img']));
                        $xtpl->assign('gimage', 'g-image');
                    }
                    $xtpl->assign('ingridient_id', $value['id']);
                    $xtpl->assign('ingridient_name', $value['name']);
                    $xtpl->assign('ingridient_link', createLink('nguyen-lieu', array('id' => $value['id'], 'title' => $value['name'])));
                    $xtpl->parse('main.ingridient');
                }
            }


            // for SEO
            addTitle('Những món ăn làm từ ' . $Ingridient->name);
            addKeywords($Ingridient->name);
            addDescription(strip_tags('Những món ăn làm từ ' . $Ingridient->name));
            if ($Ingridient->img) {
                addGraphTags('og-image', 'image', getThumbnail('og-image', $Ingridient->img));
            }
            addGraphTags('og-title', 'title', $Ingridient->name);
            addGraphTags('og-url', 'url', curPageURL());
            addGraphTags('og-description', 'description', strip_tags('Những món ăn làm từ ' . $Ingridient->name));
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

    function recipeTraditional() {
        global $oDb;

        $result = array(
            'html_paging' => '',
            'arr_recipe' => array()
        );

        $User = new User();
        $Recipe = new Recipe();
        $Recipe->num_per_page = 12;
        $p = Input::get('p', 'int', 1);
        $start_row = ($p - 1) * $Recipe->num_per_page;

        $con = " AND `status` = 1 AND `traditional` = 1 ";
        $sql = "SELECT count(*) as total FROM " . $Recipe->table . " WHERE 1 " . $con;
        $rc = $oDb->query($sql);
        $rs = $oDb->fetchArray($rc);
        $total_row = $rs['total'];

        $sql = "SELECT * FROM " . $Recipe->table . " WHERE 1 " . $con . " ORDER BY ordering DESC, id DESC LIMIT $start_row, $Recipe->num_per_page";
        $rc = $oDb->query($sql);
        $result['arr_recipe'] = $oDb->fetchAll($rc);


        $total_page = ceil($total_row / $Recipe->num_per_page);
        $paging = paging($p, $total_page, curPageURL());
        $result['html_paging'] = $paging;


        return $result;
    }

    /**
     * @Desc function lay nhung recipe theo category
     * @param 
     * @return html
     */
    function recipeCategory() {
        global $oDb;

        $result = array(
            'html_paging' => '',
            'arr_recipe' => array()
        );

        $User = new User();
        $Recipe = new Recipe();
        $Recipe->num_per_page = 12;
        $p = Input::get('p', 'int', 1);
        $start_row = ($p - 1) * $Recipe->num_per_page;
        $id = getCmsId();
        if ($id) {
            // add heading for category
            $RecipeCategory = new RecipeCategory();
            $RecipeCategory->read($id);

            $this->heading = $RecipeCategory->name;

            // paging
            $sql = "SELECT count(*) as total FROM t_recipe WHERE `id`
                    IN ( SELECT recipe_id FROM t_recipe_relationships 
                    WHERE object_id = '$id' AND `type` = 'category' )
                    AND t_recipe.status = 1 ";
            $rc = $oDb->query($sql);
            $rs = $oDb->fetchArray($rc);
            $total_row = $rs['total'];

            $sql = "SELECT * FROM t_recipe WHERE `id`
                    IN ( SELECT recipe_id FROM t_recipe_relationships 
                    WHERE object_id = '$id' AND `type` = 'category' )
                    AND t_recipe.status = 1 
                    LIMIT $start_row, $Recipe->num_per_page ";
            $rc = $oDb->query($sql);
            $result['arr_recipe'] = $oDb->fetchAll($rc);
        } else {
            $this->heading = 'Công thức nấu ăn';

            $con = "AND status = 1 ";
            $sql = "SELECT count(*) as total FROM " . $Recipe->table . " WHERE 1 " . $con;
            $rc = $oDb->query($sql);
            $rs = $oDb->fetchArray($rc);
            $total_row = $rs['total'];

            $sql = "SELECT * FROM " . $Recipe->table . " WHERE 1 " . $con . " LIMIT $start_row, $Recipe->num_per_page";
            $rc = $oDb->query($sql);
            $result['arr_recipe'] = $oDb->fetchAll($rc);
        }

        $total_page = ceil($total_row / $Recipe->num_per_page);
        $paging = paging($p, $total_page, curPageURL());
        $result['html_paging'] = $paging;


        return $result;
    }

}