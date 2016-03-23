<?php

/**
 * @author duchanh
 * @copyright 2012
 */
define('ALLOW_ACCESS', TRUE);
include("config.php");

/**
 * @Desc Class Ajax for admin CMS
 */
class ajax {

    /**
     * @Desc function construck
     */
    function ajax() {
        $cmd = Input::get('cmd', 'txt', '');
        switch ($cmd) {
            case "delete_default_images":
                $this->delete_default_images();
                break;

            case "delete_file":
                $this->delete_file();
                break;

            case "save_images":
                $this->save_images();
                break;

            case "update_media_info":
                $this->update_media_info();
                break;

            case "delete_media":
                $this->delete_media();
                break;
           
            case "switchval":
                $this->switchval();
                break;

            case "of_ajax_post_action":
                $this->of_ajax_post_action();
                break;

            case "update_page_info":
                $this->update_page_info();
                break;

            case "delete_module":
                $this->delete_module();
                break;
            
            case "get_google_image":
                $this->get_google_image();
                break;
            
            case "save_google_image":
                $this->save_google_image();
                break;
            
            case "reload_media_content":
                $this->reload_media_content();
                break;
            
            
        }
    }

    function delete_module() {
        $page = Input::get('page', 'txt', '');
        $pos = Input::get('pos', 'txt', '');
        $module = Input::get('module', 'txt', '');
        $eq = Input::get('eq', 'int', 0);
        $miniPage = new Page();
        $pageInfo = $miniPage->getPageInfoAdmin($page);
        $position_info = $pageInfo['position'];
        if ($position_info) {
            $position_info = json_decode($position_info, true);
            if (isset($position_info[$pos][$eq])) {
                unset($position_info[$pos][$eq]);
            }
        }
        $position_info[$pos] = array_values($position_info[$pos]);
        $miniPage->position = json_encode($position_info);
        $miniPage->update($pageInfo['id'], array('position'));
        echo 'ss';
    }

    function update_page_info() {
        $page = Input::get('page', 'txt', '');
        $miniPage = new Page();
        $pageInfo = $miniPage->getPageInfoAdmin($page);
        $page_id = $pageInfo['id'];
        $pos_update = json_encode($_POST['position']);
        $miniPage->position = $pos_update;
        $miniPage->update($page_id, array('position'));
        echo 'ss';
    }

    /**
     * @desc update options, add new if not exists
     * @param 
     * @return nothing
     */
    function of_ajax_post_action() {
        $type = Input::get('type', 'txt', 0);
        switch ($type) {
            case 'save':
                $data = Input::get('data', 'txt', '');
                if (!$data) {
                    return;
                }
                parse_str(html_entity_decode($data), $data);

                // call update options
                update_multi_options($data);
                echo '1';

                break;

            case 'reset':
                $func = Input::get('f', 'txt', 'options');
                include("include/$func.php");
                $data = array();
                foreach ($of_options as $key => $value) {
                    if (isset($value['id']) && isset($value['std'])) {
                        $data[$value['id']] = $value['std'];
                    }
                }
                update_multi_options($data);
                echo '1';
                break;
        }
    }

    /**
     * @Desc delete media
     * @return ss
     */
    function delete_media() {
        $list_id = Input::get('list_id', 'txt', '');
        if ($list_id) {
            $listID = explode(',', $list_id);
            foreach ($listID as $media_id) {
                deleteMedia((int) $media_id);
            }
        }
        echo 'ss';
    }

    /**
     * @Desc update media infomation include alt, name, des...
     * @return ss
     */
    function update_media_info() {
        $id = Input::get('id', 'int', 0);
        if (!$id) {
            return false;
        }
        $Media = new Media();
        $Media->read($id);
        $other_info = $Media->other_info;
        $other_info = json_decode($other_info, true);


        $title = Input::get('title', 'txt', '');
        $caption = Input::get('caption', 'txt', '');
        $alt = Input::get('alt', 'txt', '');
        $desc = Input::get('desc', 'txt', '');

        $other_info['title'] = $title;
        $other_info['caption'] = $caption;
        $other_info['alt'] = $alt;
        $other_info['desc'] = $desc;
        $other_info = json_encode($other_info);
        $Media->other_info = $other_info;
        $Media->update($id, array('other_info'));
        echo 'ss';
    }

    /**
     * @Desc delete defautt images
     * @param 
     * @return nothing
     */
    function delete_default_images() {
        $src = Input::get('src', 'txt', '');
        if ($src) {
            $image_path = '..' . trim($src, '.');
            if (@file_exists($image_path)) {
                @unlink($image_path);
                deleteMedia($src);
            }
        }
    }

    /**
     * @Desc delete file
     * @param 
     * @return ss
     */
    function delete_file() {
        $id = Input::get('id', 'int', 0);
        $f = Input::get('f', 'txt', '');
        switch ($f) {
            case "media":
                if ($id) {
                    deleteMedia($id);
                }
                break;

            default:
                if ($id && $f) {
                    include_once("include/$f.php");
                    foreach ($column as $field => $value) {
                        if ($value['type'] == 'input:multiimages') {
                            $table_info = $value['table'];
                            break;
                        }
                    }
                    if ($table_info) {
                        deleteImagesTable($table_info, $id);
                    }
                }
                break;
        }
        echo 'ss';
    }

   

    
    
    /**
     * @Desc search images from google
     * @param string $q: the keyword
     * @param int $num: number want to get, default is 24
     * @return nothing, print json
     */
    function get_google_image() {
        $q = Input::get('q', 'txt', '');
        $num = Input::get('num', 'int', 32);
        $result = array();
        $page = floor($num / 8);
        
        if ($q != '') {
            for($n = 1; $n <= $page; $n++){
                $apiURL = "http://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=" . urlencode($q) . "&rsz=8&start=".($n*8);
                $requestContent = json_decode(file_get_contents($apiURL), true);
                $resultImages = $requestContent['responseData']['results'];
                for ($i = 0; $i < 8; $i++) {
                    $imageInfo = $resultImages[$i];
                    $img = array(
                        'url' => $imageInfo['url'],
                        'thumb' => $imageInfo['tbUrl'],
                        'info'  => $imageInfo['width'].' x '.$imageInfo['height']
                    );
                    $result[]= $img;
                }
            }
        }
        
        echo json_encode($result);
        
    }
    
    
    /**
     * @Desc function save images from url, add this to media
     * @param string $url: images url
     * @return nothing, print json
     */
    function save_google_image(){
        $src = Input::get('src','txt','');
        if($src){
            
            $file_name = getFileName($src);
            $file_name = remove_special_char($file_name);
            
            $type = Input::get('f', 'txt', 'media');
            $dir = "../uploads/images/" . $type . "/" . date('Y_m_d') . '/';

            // create folder
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            // download file
            $file_path = trim($dir, '.') . $file_name;
            $ok = download_file($src, ROOT_PATH . $file_path);
            if(!$ok){
                $result = array('status' => 403, 'msg' => 'Folder can not writable');
                echo json_encode($result);
                die;
            }

            // insert to the media
            insertMedia($file_path, 0, $type);
            
            $result = array('status' => 200, 'msg' => 'sucsess');
            echo json_encode($result); die;
        }
    }
    
    
    // reload media content
    function reload_media_content(){
        $Media = new Media();
        echo $Media->showList(true);
    }

}

new ajax();
