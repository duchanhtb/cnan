<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
class Recipe extends Base {

    var $fields = array(
        'user_id',
        'username',
        'title',
        'brief',
        'ingridients',
        'intruction',
        'cooking_time',
        'price',
        'nutrition_facts',
        'yield',
        'has_picture',
        'has_video',
        'youtube_id',
        'date_created',
        'date_updated',
        'img',
        'hits',
        'home',
        'status',
        'traditional',
        'ordering'
    ); //fields in table (excluding Primary Key)	
    var $table = "t_recipe";
    var $cookingTime = array(
        '1' => 'Dưới 15 phút',
        '2' => 'Dưới 30 phút',
        '3' => 'Dưới 1 tiếng',
        '4' => 'Trên 1 tiếng'
    );

    /**
     * @Desc 1: lưu lịch sử recipe sử dụng cho thống kê hoặc lọc những recipe xem nhiều nhất theo khoảng thời gian
     *       2: Nếu user đã đăng nhập thì lưu thông tin user đã từng xem bài viết này 
     * @param int $recipe_id: id của công thức hoặc blog hoặc địa điểm
     * @return boolean
     */
    function addHistory($recipe_id, $type = 'recipe') {
        // update total hits
        // update view for recipe by day
        $RecipeViews = new RecipeViews();
        $date_today = date('Y-m-d', time());
        $check = $RecipeViews->getRecord("*", "AND `recipe_id` = $recipe_id AND `date` = '$date_today' ");
        if (is_array($check) && count($check) > 0) {
            // update
            $RecipeViews->hits = $check['hits'] + 1;
            $RecipeViews->update($check['id'], array('hits'));
        } else {
            // insert
            $RecipeViews->recipe_id = $recipe_id;
            $RecipeViews->date = date('Y-m-d');
            $RecipeViews->hits = 1;
            $RecipeViews->insert();
        }

        // update history user
        $user = GetSession('user');
        if (is_array($user) && count($user) > 0) {
            $UserHistory = new UserHistory();
            $history = $UserHistory->getRecord("*", " AND `object_id` = $recipe_id AND DATE_FORMAT(`date`, '%Y-%m-%d')  = '$date_today' ");
            if (is_array($history) && count($history) > 0) {
                // update
                $UserHistory->date = date('Y-m-d H:i:s', time());
                $UserHistory->update($history['id'], array('date'));
            } else {
                // insert
                $UserHistory->user_id = $user['id'];
                $UserHistory->object_id = $recipe_id;
                $UserHistory->type = $type;
                $UserHistory->date = date('Y-m-d H:i:s', time());
                $UserHistory->insert();
            }
        }

        return 1;
    }

    /**
     * @Desc get recipe by user_id without current_id
     * @param $user_id: user id
     * @param $current_id: current_id 
     * @param $num_get: number record 
     * @return array
     */
    function getRecipeUser($user_id, $current_id = 0, $num_get = 5, $start = 0) {
        return $this->get("*", "AND status = 1 AND user_id = $user_id AND id != $current_id ", "`date_created` DESC", $start, $num_get);
    }

    /**
     * @Desc count recipe by UserId
     * @param int $user_id: User Id
     * @return int
     */
    function countRecipeUser($user_id) {
        global $oDb;
        $sql = "SELECT COUNT( * ) as tt 
				FROM $this->table
				WHERE user_id = $user_id ";
        $rc = $oDb->query($sql);
        $rs = $oDb->fetchArray($rc);
        return $rs['tt'];
    }

    /**
     * @Desc get string display cooking time
     * @param int $ct: key
     * @return string
     */
    function getDisplayCookingTime($ct) {
        if (isset($this->cookingTime[$ct])) {
            return $this->cookingTime[$ct];
        }
        return '';
    }

    /**
     * @Desc get html recipe info in detail page
     * @param int $recipe_id: recipe_id
     * @return string
     */
    function getHtmlRecipeInfo($recipe_id) {

        $html = '';

        // cooking time
        $this->read($recipe_id);
        $cooking_time = $this->cooking_time;
        if ($cooking_time > 0) {
            $html .= '<li><i class="fa fa-clock-o"></i><br /><span  datetime="PT30M" itemprop="cookTime">' . $this->getDisplayCookingTime($cooking_time) . '</span></li>';
        }

        //yield
        $yield = $this->yield;
        if ($yield > 0) {
            $html .= '<li><i class="fa fa-user"></i><br /><span itemprop="recipeYield">' . $yield . '</span>  người ăn</li>';
        }

        // natritional value
        $nutrition_facts = $this->nutrition_facts;
        if ($nutrition_facts > 0) {
            $html .= '<li><i class="fa fa-tachometer"></i><br /><span itemprop="calories">' . $nutrition_facts . '</span></li>';
        }

        // price
        $price = $this->price;
        if ($price > 0) {
            $html .= '<li><i class="fa fa-usd"></i><br />' . formatPrice($price) . ' VNĐ</li>';
        }

        if ($html != '') {
            $html = '<div id="recipe-other-info"><span itemprop="nutrition" itemscope itemtype="http://schema.org/NutritionInformation"><ul>' . $html . '</ul></span></div>';
        }
        
        return $html;
    }

}