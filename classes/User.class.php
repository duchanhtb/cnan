<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
class User extends Base {

    var $table = "t_user";
    var $Cache = "CacheUser";
    var $msg   = '';
    
    //fields in table (excluding Primary Key)
    var $fields = array(
        'username',
        'password',
        'email',
        'avatar',
        'phone',
        'fullname',
        'province_id',
        'career_id',
        'question_id',
        'answer',
        'address',
        'region_name',
        'gender',
        'date_register',
        'birthday',
        'slogan',
        'website',
        'link_facebook',
        'link_googleplus',
        'link_twitter',
        'facebook_id',
        'googleplus_id',
        'twitter_id',
        'yahoo_id',
        'skype_id',
        'point',
        'last_login_time',
        'last_login_ip',
        'last_country_code',
        'last_country_name',
        'last_latitude',
        'last_longitude',
        'last_currency_code',
        'group_id',
        'user_type',
        'social_id',
        'status'
    );

    /**
     * @Desc get user
     * @param string $con: condition
     * @param string $sort: field want to sort    
     * @param string $order: DESC OR ASC
     * @param int $page: number of page
     * @return array
     */
    function getUser($con = "", $sort = false, $order = false, $page = 1) {
        $page = ($page > 1 ) ? $page : 1;

        $start = ($page - 1) * ($this->num_per_page);

        if ($sort && $order) {
            return $this->get("*", $con, "$sort $order", $start, $this->num_per_page);
        } else {
            return $this->get("*", $con, " id ASC ", $start, $this->num_per_page);
        }
        return false;
    }

    /**
     * @Desc function login
     * @param string $username: username
     * @param string $pass: password     
     * @return array
     */
    function login($username, $pass) {
        $password = createMd5Password($pass);

        $con = " AND (`username` = '$username' OR `email` = '$username' ) AND `password` = '$password' ";
        $user = $this->getRecord('*', $con);
        if (is_array($user) && count($user) > 0) {
            // check if member is blocked
            if($user['status'] == 1){
                // set session
                SetSession('user', $user);
                $this->updateInfoByIP($user['id']);
            
                $this->msg = 'Đăng nhập thành công';
                return $user;
            }else{
                $this->msg = 'Tài khoản của bạn đã bị khóa, vui lòng liên hệ Admin';
                return false;
            }
        }
        $this->msg = 'Tên đăng nhập hoặc mật khẩu không đúng';
        return false;
    }

    /**
     * @Desc class update info by IP: example: ip, country, city...
     * @param int $user_id: user id      
     * @return boolean
     */
    function updateInfoByIP($user_id) {
        $userIpInfo = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=' . getUserIp()));
        $arrayUpdate = array(
            'last_login_time' => date("y-m-d H:i:s", time()),
            'last_login_ip' => getUserIp(),
            'region_name' => $userIpInfo['geoplugin_regionName'],
            'last_country_code' => $userIpInfo['geoplugin_countryCode'],
            'last_country_name' => $userIpInfo['geoplugin_countryName'],
            'last_latitude' => $userIpInfo['geoplugin_latitude'],
            'last_longitude' => $userIpInfo['geoplugin_longitude'],
            'last_currency_code' => $userIpInfo['geoplugin_currencyCode']
        );
        $this->save($user_id, $arrayUpdate);
        return true;
    }

    /**
     * @Desc function get user by id
     * @param string $username: username      
     * @return array
     */
    function getUserById($user_id) {
        $con = " AND `id` = '$user_id' AND status = 1 ";
        $result = $this->getRecord("*", $con);
        if (is_array($result) && count($result) > 0) {
            return $result;
        }
        return false;
    }

    /**
     * @Desc function get user by username
     * @param string $username: username      
     * @return array
     */
    function getUserByUsername($username) {
        $con = " AND `username` = '$username' AND status = 1 ";
        $result = $this->getRecord("*", $con);
        if (is_array($result) && count($result) > 0) {
            return $result;
        }
        return false;
    }

    /**
     * @Desc function get user by email
     * @param string $email: user email    
     * @return array
     */
    function getUserByEmail($email) {
        $con = " AND `email` = '$email' AND status = 1 ";
        $result = $this->getRecord("*", $con);
        if (is_array($result) && count($result) > 0) {
            return $result;
        }
        return false;
    }

    /**
     * @Desc function block user
     * @param int $uid: user id      
     * @return boolean
     */
    function blockUser($uid) {
        $this->read($uid);
        $this->status = 0;
        $this->update($uid, array('status'));
        return true;
    }

    /**
     * @Desc function block user
     * @param int $uid: user id      
     * @return boolean
     */
    function unBlockUser($uid) {
        $this->read($uid);
        $this->status = 1;
        $this->update($uid, array('status'));
        return true;
    }

    /**
     * @Desc function remove user
     * @param int $uid: user id       
     * @return boolean
     */
    function deleteUser($uid) {
        $this->remove($uid);
        return true;
    }

    /**
     * @Desc function get display name
     * @param int $mixed: user_id or username
     * @return boolean
     */
    function getDisplayName($mixed) {
        if (is_numeric($mixed)) {
            $this->read($mixed);
            if ($this->fullname != '') {
                return $this->fullname;
            }
            return $this->username;
        } else {
            $user = $this->getUserByUsername($mixed);
            if ($user && $user['fullname'] != '') {
                return $user['fullname'];
            }
            return $mixed;
        }
    }

    /**
     * @Desc function get display name
     * @param int $mixed: user_id or username
     * @return boolean
     */
    function getDisplayInfo($mixed, $count_recipe_post = false) {
        $info = array(
                'name'      => $mixed, 
                'point'     => 0,
                'email'     => '',
                'avatar'    => 0
                );

        if (is_numeric($mixed)) {
            $this->read($mixed);
            if ($this->fullname != '') {
                $info['name'] = $this->fullname;
            }
            $info['point']  = $this->point;
            $info['avatar'] = $this->getUserAvatar($this->avatar);
            $info['email']  = $this->email;
            if($count_recipe_post){
                $Recipe = new Recipe();
                $info['num_recipe_post']    =  $Recipe->countRecipeUser($mixed);
            }
            
        } else {
            $user = $this->getUserByUsername($mixed);
            if ($user && $user['fullname'] != '') {
                $info['name'] = $user['fullname'];
            }
            $info['point']  = $user['point'];
            $info['avatar'] = $this->getUserAvatar($user['avatar']);
            $info['email']  = $user['email'];
            if($count_recipe_post){
                $Recipe = new Recipe();
                $info['num_recipe_post']    =  $Recipe->countRecipeUser($user['id']);
            }
        }
        return $info;
    }
    
    

    /**
     * @Desc function html user for homepage
     * @param int $mixed: user_id or username
     * @return boolean
     */
    function getHtmlInfoHomepage($user_id) {
        $this->read($user_id);
        $html = '';

        $Recipe = new Recipe();
        $recipe_post = $Recipe->countRecipeUser($user_id);
        $point = $this->point;
        $html .= '<a href="javascript:void(0)"><span class="view"><i class="fa fa-thumbs-o-up"></i>' . formatPrice($point) . '</span></a>';
        $html .= '<span>&nbsp;&nbsp;&nbsp;</span>';
        $html .= '<a href="javascript:void(0)"><span class="view"><i class="fa fa-pencil-square-o"></i>' . formatPrice($recipe_post) . '</span></a>';

        return $html;
    }

    /**
     * @Desc function get user avatar, this will be return url of avatar
     * @param int $mixed: user_id or link avatar
     * @return boolean
     */
    function getUserAvatar($mixed, $folder = 'thumb-150') {
        $avatar = '';
        if (is_numeric($mixed)) {
            $this->read($mixed);
            $avatar = $this->avatar;
        } else {
            $avatar = $mixed;
        }
        if (strpos($avatar, 'http') === false) {
            return getThumbnail($folder, $avatar);
        }
        return $avatar;
    }

    /**
     * @Desc function html user
     * @param int $mixed: user_id or username
     * @return boolean
     */
    function getHtmlInfo($user_id) {
        $this->read($user_id);
        $html = '';

        // province
        $province_id = $this->province_id;
        if ($province_id) {
            $Province = new Provinces();
            $Province->read($province_id);
            $html .= '<li><a href="javascript:void(0)"><i class="fa fa-map-marker"></i> Ở ' . $Province->name . '</a></li>';
        }

        $Recipe = new Recipe();
        $recipe_post = $Recipe->countRecipeUser($user_id);
        if ($recipe_post > 0) {
            $html .= '<li><a href="javascript:void(0)"><i class="fa fa-pencil-square-o"></i>Đã chia sẻ ' . formatPrice($recipe_post) . ' bài</a></li>';
        }

        $point = $this->point;
        if ($point > 0) {
            $html .= '<li><a href="javascript:void(0)"><i class="fa fa-thumbs-o-up"></i>điểm ' . formatPrice($point) . '</a></li>';
        }

        if ($html != '') {
            $html = '<ul id="user-info">' . $html . '</ul>';
        }

        if ($this->slogan) {
            $html .= '<p>' . $this->slogan . '</p>';
        }

        return $html;
    }

    /**
     * @Desc get top user for home page
     * @param 
     * @return array
     */
    function getTopUser() {
        return $this->get('*', " AND status = 1 ", " point DESC, id ASC ", 0, 9);
    }
    
    
    
    /**
     * @Desc get top user for home page
     * @param 
     * @return array
     */
    function addPoint($user_id, $type){
        $add_point = 0;
        switch ($type){
            case 'add_recipe':
                    $add_point = 5;
                break;
            
            case 'like_recipe':
                    $add_point = 1;
                break;
            
            case 'add_locate':
                    $add_point = 5;
                break;
            
            case 'like_locate':
                    $add_point = 1;
                break;
        }
        
        $this->read($user_id);
        $this->point = $this->point + $add_point;
        $this->update($user_id, array('point'));
    }

}
