<?php

/**
 * @author duchanh
 * @copyright 2012
 */
define('ALLOW_ACCESS', TRUE);
include("config.php");

class ajax {

    function ajax() {
        $cmd = Input::get('cmd', 'txt', '');

        switch ($cmd) {
            case "recipe_error_report":
                $this->recipe_error_report();
                break;
            
            case "locate_error_report":
                $this->locate_error_report();
                break;
            case "post_recipe":
                $this->post_recipe();
                break;

            case "post_locate":
                $this->post_locate();
                break;

            case "get_youtube_duration":
                $this->get_youtube_duration();
                break;

            case "get_ingridient_image":
                $this->get_ingridient_image();
                break;

            case "delete_image":
                $this->delete_image();
                break;
        }
    }

    // error reporting recipe
    function recipe_error_report() {
        $result = array();
        $recipe_id = Input::get('report_id', 'txt', '');
        $email = Input::get('email', 'txt', '');
        $content = Input::get('content', 'txt', '');
        if (!checkValidEmail($email)) {
            $result = array(
                'status'    => 'error',
                'msg'       => 'Email không hợp lệ',
            );
            echo json_encode($result);
            exit;
        }
        if (strlen($content) < 20) {
            $result = array(
                'status'    => 'error',
                'msg'       => 'Nội dung quá ngắn <br/> Nội dung phải nhiều hơn 20 ký tự',
            );
            echo json_encode($result);
            exit;
        }
        $RecipeReport = new RecipeReport();
        $RecipeReport->recipe_id = alphaID($recipe_id, true);
        $RecipeReport->email = $email;
        $RecipeReport->content = $content;
        $RecipeReport->date_report = date('Y-m-d H:i:s', time());

        $user = GetSession('user');
        if ($user && count($user) > 0) {
            $user_id = $user['id'];
            $RecipeReport->user_id = $user_id;
        }
        $insert_id = $RecipeReport->insert();
        
        $result  = array(
            'status'    => 'success',
            'msg'       => 'Cảm ơn bạn đã gửi thông báo <br /> cho chúng tôi',
            'id'        => $insert_id,
        );
        
        // send mail
        $from = array(
            'name'  => 'Cach Nau An Ngon',
            'email' => 'cachnauanngon@gmail.com'
        );

        $to = array(
          'name'    => 'Hanh Nguyen' ,
          'email'  => 'hanhcoltech@gmail.com'
        );
        
        $recipe_id = alphaID($recipe_id, true);
        $Recipe = new Recipe();
        $Recipe->read($recipe_id);
           
        $subject = 'Báo lỗi: '.$Recipe->title;
        $link = createLink('cong-thuc', array('id' => $recipe_id, 'title' => $Recipe->title));
        sendMail($from, $to, false, $subject, $content);
        
        echo json_encode($result);
        exit;
    }
    
    
    // error reporting recipe
    function locate_error_report() {
        $report_id = Input::get('report_id', 'txt', '');
        $email = Input::get('email', 'txt', '');
        $content = Input::get('content', 'txt', '');
        if (!checkValidEmail($email)) {
            $result = array(
                'status'    => 'error',
                'msg'       => 'Email không hợp lệ',
            );
            echo json_encode($result);
            exit;
        }
        if (strlen($content) < 20) {
            $result = array(
                'status'    => 'error',
                'msg'       => 'Nội dung quá ngắn <br/> Nội dung phải nhiều hơn 20 ký tự',
            );
            echo json_encode($result);
            exit;
        }
        $DiningVenuesReport = new DiningVenuesReport();
        $DiningVenuesReport->dining_venues_id = alphaID($report_id, true);
        $DiningVenuesReport->email = $email;
        $DiningVenuesReport->content = $content;
        $DiningVenuesReport->date_report = date('Y-m-d H:i:s', time());
        $user = GetSession('user');
        if ($user && count($user) > 0) {
            $user_id = $user['id'];
            $DiningVenuesReport->user_id = $user_id;
        }
        $insert_id = $DiningVenuesReport->insert();

        $result  = array(
            'status'    => 'success',
            'msg'       => 'Cảm ơn bạn đã gửi thông báo <br /> cho chúng tôi',
            'id'        => $insert_id,
        );
        
        
        // send mail
        $from = array(
            'name'  => 'Cach Nau An Ngon',
            'email' => 'cachnauanngon@gmail.com'
        );

        $to = array(
          'name'    => 'Hanh Nguyen' ,
          'email'  => 'hanhcoltech@gmail.com'
        );
        
        $report_id = alphaID($report_id, true);
        $DiningVenues = new DiningVenues();
        $DiningVenues->read($report_id);
           
        $subject = 'Báo lỗi: '.$DiningVenues->name;
        sendMail($from, $to, false, $subject, $content);
        
        echo json_encode($result);
        exit;
    }

    // get youtube duration
    function get_youtube_duration() {
        $id = Input::get('id', 'txt', '');
        if ($id != '') {
            echo getYouTubeDuration($id);
        }
    }

    // get ingridient images
    function get_ingridient_image() {
        $q = Input::get('q', 'txt', '');
        $ingridient_id = Input::get('id', 'int', 0);
        if ($q != '' && $ingridient_id > 0) {
            $apiURL = "http://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=" . urlencode($q) . "&rsz=8&start=0";
            $requestContent = json_decode(file_get_contents($apiURL), true);
            $result = $requestContent['responseData']['results'];
            $ok = false;
            for ($i = 0; $i < 8; $i++) {
                if ($ok == true)
                    break;

                $imageInfo = $result[$i];
                $src = $imageInfo['url'];

                $file_name = getFileName($src);
                $file_name = remove_special_char($file_name);


                $type = Input::get('type', 'txt', 'ingridient');
                $dir = "../uploads/images/" . $type . "/" . date('Y_m_d') . '/';

                // create folder
                if (!is_dir($dir)) {
                    mkdir($dir, 0775, true);
                }

                // download file
                $file_path = trim($dir, '.') . $file_name;
                download_file($src, ROOT_PATH . $file_path);
                if (!file_exists(ROOT_PATH . $file_path)) {
                    continue;
                }
                $imgsize = getimagesize(ROOT_PATH . $file_path);
                if ($imgsize) {
                    $ok = true;
                    echo getThumbnail('thumb-500-crop', $file_path);

                    // insert to ingridient
                    $Ingridient = new Ingridient();
                    $Ingridient->img = $file_path;
                    $Ingridient->update($ingridient_id, array('img'));
                    die;
                } else if ($i == 7) {
                    echo getThumbnail('thumb-500-crop', '/uploads/noimage.jpg');
                }
                continue;
            }
        }
    }

    function post_recipe() {
        if ($_POST) {
            $Recipe = new Recipe();
            $RecipeCategory = new RecipeCategory();
            $Ingridient = new Ingridient();
            $RecipeIngridient = new RecipeIngridient();
            $RecipeImage = new RecipeImages();

            $recipe_id = Input::get('recipe_id', 'txt', '');
            $Recipe->title = Input::get('recipe_name', 'txt', '');
            $Recipe->brief = html_entity_decode(Input::get('brief', 'txt', ''));
            $Recipe->intruction = $_POST['intruction']; //Input::get('intruction', 'con', '');

            $ingriName = $_POST['ingridient'];
            $unit = $_POST['unit'];
            foreach ($ingriName as $key => $ingri) {
                if($ingri != ''){
                    $ingridients[] = array('name' => trim($ingriName[$key]), 'value' => trim($unit[$key]));
                }
            }
            if (count($ingridients) > 0) {
                $Recipe->ingridients = serialize($ingridients);
            } else {
                $Recipe->ingridients = '';
            }

            $youtube_id = '';
            $youtube = Input::get('youtube', 'txt', '');
            if ($youtube) {
                $youtube_id = get_youtube_id($youtube);
            }
            $Recipe->youtube_id = $youtube_id;
            if($youtube_id != ''){
                $Recipe->has_video = 1;
            }else{
                $Recipe->has_video = 0;
            }
            $Recipe->cooking_time = Input::get('cooking_time', 'int', 2);
            $price = str_replace(',', '', Input::get('price', 'txt', ''));
            $Recipe->price = str_replace('.', '', $price);
            $Recipe->nutrition_facts = Input::get('nutrition_facts', 'txt', '');
            $Recipe->yield = Input::get('yield', 'txt', '');
            $Recipe->img = Input::get('recipe_img', 'txt', '');
            $Recipe->date_updated = date('Y-m-d H:i', time());


            if ($recipe_id != '') {
                $array_update = array(
                    'title',
                    'brief',
                    'intruction',
                    'ingridients',
                    'youtube_id',
                    'has_video',
                    'cooking_time',
                    'price',
                    'nutrition_facts',
                    'yield',
                    'img',
                    'date_updated'
                );
                $id = alphaID($recipe_id, true);
                // update infomation
                $Recipe->update($id, $array_update);
                $RecipeImage->addRecipeSessionImage($id);

                // update category
                if (isset($_POST['category'])) {
                    $RecipeCategory->updateRecipeCategory($id, $_POST['category']);
                }

                // update ingridient
                foreach ($ingriName as $key => $ingri) {
                    // insert ingridient
                    $ingridient_id = $Ingridient->insertIngridient($ingri);

                    // add relation ship
                    if ($ingridient_id) {
                        $RecipeIngridient->insertRelation($id, $ingridient_id);
                    }
                }
            } else {
                $Member = GetSession('user');
                $Recipe->user_id = $Member['id'];
                $Recipe->username = $Member['username'];
                if(GetSession('multiimages')){
                    $Recipe->has_picture = 1;
                }else{
                    $Recipe->has_picture = 0;
                }
                $Recipe->date_created = date('Y-m-d H:i', time());
                $Recipe->hits = 0;
                $Recipe->traditional = 0;
                $Recipe->home = 0;
                $Recipe->status = 1;

                $id = $Recipe->insert();
                // update ordering
                if ($id > 0) {
                    $Recipe->ordering = $id;
                    $Recipe->update($id, array('ordering'));
                }

                // add recipe images
                $RecipeImage->addRecipeSessionImage($id);

                // update category
                if (isset($_POST['category'])) {
                    $RecipeCategory->updateRecipeCategory($id, $_POST['category']);
                }

                // update ingridient
                foreach ($ingriName as $key => $ingri) {
                    // insert ingridient
                    $ingridient_id = $Ingridient->insertIngridient($ingri);

                    // add relation ship
                    if ($ingridient_id) {
                        $RecipeIngridient->insertRelation($id, $ingridient_id);
                    }
                }
                
                // update point
                $User = new User();
                $User->addPoint($Member['id'], 'add_recipe');
            }
            
            $msg = ($recipe_id != '') ? 'Cập nhật công thức thành công!' : 'Đăng công thức thành công!';
            $link = createLink('cong-thuc', array('id' => $id, 'title' => $Recipe->title));
            $result = array(
                'status'    => 'success',
                'msg'       => '<p class="center">'.$msg.'</p>'
                            . '<p class="center"><img src="'.base_url().'skin/default/asset/images/success-icon.png"></p>'
                            . '<p class="center"><a href="'.$link.'" target="_blank">Xem bài viết: <strong>'.$Recipe->title.'</strong></a></p>',
                'id'        => alphaID($id),
                'title'     => $Recipe->title
            );
            echo json_encode($result);
            exit;
        }
    }
    
    

    function post_locate() {
        $DiningVenues = new DiningVenues();
        $DiningVenousImage = new DiningVenousImage();
        
        $DiningVenues->name = Input::get('locate_name', 'txt', '');
        $DiningVenues->img = Input::get('locate_img', 'txt', '');
        $DiningVenues->brief = Input::get('locate_brief', 'txt', '');
        $DiningVenues->content = html_entity_decode(Input::get('content', 'con', ''));
        $DiningVenues->address = Input::get('address', 'txt', '');
        $DiningVenues->latitude = Input::get('latitude', 'txt', '');
        $DiningVenues->longitude = Input::get('longitude', 'txt', '');
        $DiningVenues->phone = Input::get('phone', 'txt', '');
        $DiningVenues->email = Input::get('email', 'txt', '');
        $DiningVenues->facebook = Input::get('facebook', 'txt', '');
        $DiningVenues->website = Input::get('website', 'txt', '');
        $DiningVenues->date_updated = date('Y-m-d H:i:s', time());
        $DiningVenues->meta_title = Input::get('locate_name', 'txt', '');

        $locate_id = Input::get('locate_id', 'txt', '');
        if ($locate_id) {
            $array_update = array(
                'name',
                'img',
                'brief',
                'content',
                'address',
                'latitude',
                'longitude',
                'phone',
                'email',
                'facebook',
                'website',
                'date_updated'
            );
            $id = alphaID($locate_id, true);
            // update infomation
            $DiningVenues->update($id, $array_update);
            $DiningVenousImage->addLocateSessionImage($id);
        } else {
            $Member = GetSession('user');
            $DiningVenues->user_id = $Member['id'];
            $DiningVenues->username = $Member['username'];
            $DiningVenues->date_created    = date('Y-m-d H:i:s',time());
            $DiningVenues->hits = 0;
            $DiningVenues->home = 1;
            $DiningVenues->status = 1;
            
            $id = $DiningVenues->insert();
            if($id){
                $DiningVenues->ordering = $id;
                $DiningVenues->update($id, array('ordering'));
                $DiningVenousImage->addLocateSessionImage($id);
            }
            
            // update point
            $User = new User();
            $User->addPoint($Member['id'], 'add_locate');
                    
        }
        
        $msg = ($locate_id != '') ? 'Cập nhật địa điểm thành công!' : 'Đăng địa điểm thành công!';
        $link = createLink('dia-diem', array('id' => $id, 'title' => $DiningVenues->name));
        $result = array(
            'status'    => 'success',
            'msg'       => '<p class="center">'.$msg.'</p>'
                        . '<p class="center"><img src="'.base_url().'skin/default/asset/images/success-icon.png"></p>'
                        . '<p class="center"><a href="'.$link.'" target="_blank">Xem bài viết: <strong>'.$DiningVenues->name.'</strong></a></p>',
            'id'        => alphaID($id),
            'title'     => $DiningVenues->name
        );
        echo json_encode($result);
        exit;
    }

    function delete_image() {
        $type = Input::get('type', 'txt', '');
        $id = Input::get('id', 'txt', '0');
        if ($id) {
            switch ($type) {
                case 'recipe':
                    $RecipeImages = new RecipeImages();
                    $RecipeImages->read($id);
                    $img = $RecipeImages->img;

                    // delete from t_recipe_images
                    $RecipeImages->remove($id);

                    // delete from t_media
                    deleteMedia($img);

                    echo 'SS';

                    break;
                
                case 'locate':
                    $DiningVenousImage = new DiningVenousImage();
                    $DiningVenousImage->read($id);
                    $img = $DiningVenousImage->img;

                    // delete from t_recipe_images
                    $DiningVenousImage->remove($id);

                    // delete from t_media
                    deleteMedia($img);

                    echo 'SS';

                    break;
            }
        }
    }

}

new ajax();

