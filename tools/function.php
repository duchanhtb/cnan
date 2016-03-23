<?php // if(!defined('ALLOW_ACCESS')) exit('No direct script access allowed');

/**
 * @author DucHanh
 * @copyright 2011
 */

//////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// COMMON FUNCTION //////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * @Desc check yahoo status
 * @param string $yid: yahoo id example: hanhnguyen_rav
 * @return true|false
 */
function check_yahoo_status($yid) {
    $status = file_get_contents("http://opi.yahoo.com/online?u={$yid}&m=a&t=1");
    if ($status === '00')
        return false;
    elseif ($status === '01')
        return true;
}


/**
 * @Desc check skype status
 * @param string $skype: skype id example: hanhcoltech
 * @return true|false
 */
function check_skype_status($skype) {
    $status = file_get_contents("http://mystatus.skype.com/{$skype}.num");
    if ($status === '2')
        return true;
    else
        return false;
}


/**
 * @Desc Break string from start to $length charactor, (only break on space letter)
 * @param $str: string to break
 * @param $length: number of charactor
 * @param $minword: min charactor of word
 * @return string
 */
function _substr($str, $length, $minword = 3) {
    $str = strip_tags($str);
    $sub = '';
    $len = 0;

    foreach (explode(' ', $str) as $word) {
        $part = (($sub != '') ? ' ' : '') . $word;
        $sub .= $part;
        $len += strlen($part);
        if (strlen($word) > $minword && strlen($sub) >= $length) {
            break;
        }
    }
    return $sub . (($len < strlen($str)) ? '...' : '');
}


/**
 * @Desc Break string from start to $length charactor
 * @param $str: string to break
 * @param $length: number of charactor
 * @param $minword: min charactor of word
 * @return string
 */
function _cutStr($str, $start = 0, $len) {
    $str = strip_tags($str);
    if ($len > strlen($str)) {
        return $str;
    } else{
        return substr($str, $start, $len) . '...';
    }
}


/**
 * @Desc get id of youtube
 * @param $ytURL: link of video from youtube example: http://www.youtube.com/watch?v=aQPIPaH4eAE 
 * @return string example: aQPIPaH4eAE
 */
function get_youtube_id($ytURL) {
    $ytvIDlen = 11; // This is the length of YouTube's video IDs

    // The ID string starts after "v=", which is usually right after
    // "youtube.com/watch?" in the URL
    $idStarts = strpos($ytURL, "?v=");

    // In case the "v=" is NOT right after the "?" (not likely, but I like to keep my
    // bases covered), it will be after an "&":
    if ($idStarts === false)
        $idStarts = strpos($ytURL, "&v=");
    // If still FALSE, URL doesn't have a vid ID
    if ($idStarts === false)
        die("YouTube video ID not found. Please double-check your URL.");

    // Offset the start location to match the beginning of the ID string
    $idStarts += 3;

    // Get the ID string and return it
    $ytvID = substr($ytURL, $idStarts, $ytvIDlen);

    return $ytvID;
}


/**
 * @Desc function get extensions of file
 * @param $filename: filename include extension
 * @return string
 */
function getFileExt($filename) {
    $path_info = pathinfo($filename);
    return $path_info['extension'];
}


/**
 * @Desc function download file from url
 * @param string $file_url: filename include extension
 * @param string $save_to: part save file
 * @return boolean
 */
function download_file($file_url, $save_to) {
    $save_to = trim($save_to, '..');
    $path_info = pathinfo($save_to);
    $dir_name = $path_info['dirname'];
    if (!is_dir($dir_name)) {
        mkdir($dir_name, 0777, true);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_URL, $file_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $file_content = curl_exec($ch);
    curl_close($ch);

    $downloaded_file = fopen($save_to, 'w');
    fwrite($downloaded_file, $file_content);
    fclose($downloaded_file);
}

//////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// END COMMON FUNCTION //////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////





//////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// CMS FUNCTION /////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * @Desc get language from table tbl_language
 * @return array
 */
function getLanguage(){
    global $lang, $oDb;
    
    $cur_lang = $_SESSION['language'];
    if(is_array($lang) && count($lang)>0){
        return $lang;
    }else{
        $sql = "SELECT * FROM tbl_language WHERE 1 ";
        $rc = $oDb->query($sql);
        $rs = $oDb->fetchAll();
        foreach($rs as $key=>$value){
            $lang[$value['key']] = $value[$cur_lang];
        }
    }
    
    return $lang;
}


/**
 * Thực hiện việc include class trong folder class
 *
 * @param str $file_name
 * @return none
 */

function includeClassFile($file_name){
    $file_path = INC_PATH."/classes/$file_name";
    if(file_exists($file_path)){
        include_once($file_path);    
    }
}


/**
 * Thực hiện việc set và update sesion cho các bản
 *
 * @param str $name
 * @param unknow type $value
 * @return true
 */
function SetSession($name, $value){	
	$_SESSION['HCMS_'.$name]	= $value;
	return true;
}



/**
 * Thực hiện việc get dữ liệu từ sesion
 *
 * @param unknown_type $name
 * @return session value
 */
function GetSession($name){
	return isset($_SESSION['HCMS_'.$name])?$_SESSION['HCMS_'.$name]:'';
}



/**
 * get current url on address bar
 *
 * @return string
 */
function getSiteUrl(){    
    $ffc_ar = explode( "/", $_SERVER['PHP_SELF'] );
    $ffc_ar_count = count( $ffc_ar );
    $ffc_ar2 = array();
    for( $i = 0; $i < $ffc_ar_count - 1; $i++ ) {
    	$ffc_ar2[$i] = $ffc_ar[$i];
    }
    $ffc_webFolderName = implode( "/", $ffc_ar2 );
    if( strpos( $_SERVER['SERVER_SOFTWARE'], "IIS" ) ) {
    	$sPhysicPath = substr( $_SERVER['SCRIPT_FILENAME'], 0, strrpos( $_SERVER['SCRIPT_FILENAME'], '\\' ) + 1 );
    } else {
    	$sPhysicPath = substr( $_SERVER['SCRIPT_FILENAME'], 0, strrpos( $_SERVER['SCRIPT_FILENAME'], '/' ) + 1 );
    }
    $sProtocol = ( strpos( $_SERVER['SERVER_PROTOCOL'], "HTTPS" ) ) ? "https" : "http";
    $sProtocol .= "://";
    $sHostName 	= $_SERVER['HTTP_HOST'];
    $sitePath 	= $sProtocol . $sHostName . $ffc_webFolderName . "/";
    return $sitePath;
}



/**
 * Thực hiện việc load module
 *
 * @param str $name: module name excluding extension php
 * @return HTML code
 */
function loadModule($name){
    $module_path = INC_PATH."/modules/$name.php";
    if(file_exists($module_path)){
        include_once($module_path);    
    }else{
        return 'module dose not exists';
    }            
    $module = new $name(); 
    return $module->draw();
}


/**
 * Thực hiện việc get dữ liệu từ sesion
 *
 * @param str $name: class name excluding extension php
 * @return object
 */
function loadClass($name){
    $class_path = INC_PATH."/classes/$name.php";
    $class_path_ext = INC_PATH."/classes/$name.class.php";
    if(file_exists($class_path)){
        include_once($class_path);
    }else if(file_exists($class_path_ext)){
        include_once($class_path_ext);
    }else{
        return 'class dose not exists';
    }
    $class_obj = new $name();
    return $class_obj;   
}



/**
 * @Desc format price add dot after 3 charactor of number
 * @param $price: number 
 * @return number
 */
function formatPrice($price) {
    return number_format($price, 0, '', '.');
}



/**
 * @Desc get root url from website 
 * @param 
 * @return address of the page
 */
function base_url() {
    global $base_url;
    return $base_url;
}



/**
 * @Desc add images size 
 * @param folder, width, height, crop
 * @return global varible
 */
function add_image_size($folder, $width, $height, $crop = false){
    global $imagesSize; 
    if(!isset($imagesSize[$folder])){
        $imagesSize[$folder] = array('width'=>$width, 'height'=>$height, 'crop'=>$crop);
    }
    return $imagesSize;
}



/**
 * @Desc pagging ajax
 * @param $current_page: number current page
 * @param $total_page: total pages we have
 * @param $func_callback: function javascript call when click to the page
 * @param $item_each_page: number items wanna display on a page
 * @param $searchTxt: 
 * @return HTML pagging
 */
function pagging_ajax($current_page, $total_page, $func_callback, $item_each_page, $searchTxt = "") {
    if ($total_page <= 1){
        return "";
    }

    $data = '<ul class="pagination clearfix">';
    if ($total_page <= 10) {
        if ($current_page > 1) {
            $data .= '<li><a href="#null" onclick="'.$func_callback.'('.($current_page - 1).','.$item_each_page.',\''.$searchTxt.'\')"></a></li>';
        }

        for ($i = 1; $i <= $total_page; $i++) {
            if ($i == $current_page) {
                $data .= '<li class="active"><a href="#null">'.$i.'</a></li>';
            } else {
                $data .= '<li><a href="#null" onclick="'.$func_callback.'('.$i.','.$item_each_page.',\''.$searchTxt.'\')">'.$i.'</a></li>';
            }
        }
        if ($current_page != $total_page) {
            $data .= '<li><a href="#null" onclick="'.$func_callback.'('.($current_page + 1).','.$item_each_page.',\''.$searchTxt.'\')"></a></li>';
        }
    }
    
    if ($total_page > 10){
        $minpage = ($current_page - 5) > 1 ? ($current_page - 5) : 1;
        $maxpage = ($current_page + 5) < $total_page ? ($current_page + 5) : $total_page;
        if ($current_page > 1) {
            $data .= '<li><a href="#null" onclick="' . $func_callback . '(' . ($current_page -
                1) . ',' . $item_each_page . ',\'' . $searchTxt . '\')"></li>';
        }

        for ($i = $minpage; $i <= $maxpage; $i++) {
            if ($i == $current_page) {
                $data .= '<li class="active"><a href="#null">' . $i . '</a></li>';
            } else{
                $data .= '<li><a href="#null" onclick="' . $func_callback . '(' . $i . ',' . $item_each_page .
                    ',\'' . $searchTxt . '\')">' . $i . '</a></li>';
            }
        }

        if ($current_page != $maxpage) {
            $data .= '<li><a href="#null" onclick="' . $func_callback . '(' . ($current_page +
                1) . ',' . $item_each_page . ',\'' . $searchTxt . '\')"></li>';
        }
    }
    $data .= '</ul>';
    return $data;
}


/**
 * @Desc function return HTML pagging
 * @param $current_page: nunber current of the page
 * @param $total_page: total page we have
 * @param $linkpage: link page we have example: http://hanhnguyen.co.cc/?page=
 * @return HTML pagging
 */
function pagging($current_page, $total_page, $linkpage){
    if ($linkpage == false){
        $linkpage = curPageURL();
    }
    $linkpage = removeQuerystringVar($linkpage, 'p');
    $linkpage .= '&p=';
    
    if ($total_page <= 1){
        return "";
    }    
    
    $data = '<ul class="paging">';
    if ($total_page <= 10){
        if ($current_page > 1){
            $data .= '<li class="btn-back active"><a href="' . $linkpage . ($current_page - 1) .
                '">Trước</a></li>';
        }

        for ($i = 1; $i <= $total_page; $i++){
            if ($i == $current_page){
                $data .= '<li class="active"><a href="javascript:void(0)">' . $i .
                    '</a></li>';
            } else{
                $data .= '<li><a href="' . $linkpage . $i . '" >' . $i . '</a></li>';
            }
        }

        if ($current_page != $total_page){
            $data .= '<li class="btn-next active"><a href="' . $linkpage . ($current_page + 1) .
                '">Tiếp</a></li>';
        }
    }

    if ($total_page > 10){
        $minpage = ($current_page - 8) > 1 ? ($current_page - 8) : 1;
        $maxpage = ($current_page + 8) < $total_page ? ($current_page + 8) : $total_page;

        if ($current_page > 1){
            $data .= '<li class="btn-back active"><a href="' . $linkpage . ($current_page - 1) .
                '">Trước</a></li>';
        }

        for ($i = $minpage; $i <= $maxpage; $i++){
            if ($i == $current_page){
                $data .= '<li class="active"><a href="javascript:void(0)">' . $i .
                    '</a></li>';
            } else{
                $data .= '<li><a href="' . $linkpage . $i . '" >' . $i . '</a></li>';
            }
        }

        if ($current_page != $maxpage){
            $data .= '<li class="btn-next active"><a href="' . $linkpage . ($current_page + 1) . '">Tiếp</a>';
        }
    }
    $data .= '</ul>';
    return $data;
}


/**
 * @Desc function return HTML pagging
 * @param $current_page: nunber current of the page
 * @param $total_page: total page we have
 * @param $linkpage: link page we have example: http://hanhnguyen.co.cc/?page=
 * @return HTML pagging
 */
function getHtmlPagging($cur_page, $total_page, $linkpage = false){
    if ($linkpage == false){
        $linkpage = curPageURL();
    }
    $linkpage = removeQuerystringVar($linkpage, 'page');
    $pageHtml = '';
    if ($total_page >= 1){
        $option_page = '';
        for ($i = 1; $i <= $total_page; $i++){
            if ($i == $cur_page){
                $option_page .= '<option value="' . $i . '" selected="">' . $i . '</option>';
            } else{
                $option_page .= '<option value="' . $i . '">' . $i . '</option>';
            }
        }
        if ($total_page == 1){
            $first_page_link = $linkpage;
            $last_page_link = $linkpage . '&page=' . ($total_page);

            $link_next = $linkpage . '&page=' . ($cur_page);
            $link_prev = $linkpage . '&page=' . ($cur_page);
        } else
            if ($cur_page == 1){
                $first_page_link = $linkpage;
                $last_page_link = $linkpage . '&page=' . ($total_page);

                $link_next = $linkpage . '&page=' . ($cur_page + 1);
                $link_prev = $linkpage . '&page=' . ($cur_page);
            } else
                if ($cur_page == $total_page){
                    $first_page_link = $linkpage;
                    $last_page_link = $linkpage . '&page=' . ($total_page);

                    $link_next = $linkpage . '&page=' . ($cur_page);
                    $link_prev = $linkpage . '&page=' . ($cur_page - 1);
                } else{
                    $first_page_link = $linkpage;
                    $last_page_link = $linkpage . '&page=' . ($total_page);

                    $link_next = $linkpage . '&page=' . ($cur_page + 1);
                    $link_prev = $linkpage . '&page=' . ($cur_page - 1);
                }

                $pageHtml = '<table border="0" cellpadding="0" cellspacing="0" id="paging-table">
    			<tr>
    			<td>
    				<a href="' . $first_page_link . '" class="page-far-left"></a>
    				<a href="' . $link_prev . '" class="page-left"></a>
    				<div id="page-info">Page <strong>' . $cur_page . '</strong> / ' . $total_page .
                        '</div>
    				<a href="' . $link_next . '" class="page-right"></a>
    				<a href="' . $last_page_link . '" class="page-far-right"></a>
    			</td>
    			<td>
    			<select id="page" onchange="ChangePage();">
                    ' . $option_page . '				
    			</select>
    			</td>
    			</tr>
    			</table>';

    }
    return $pageHtml;
}


/**
 * @Desc remove varible of the url example http://hanhnguyen.co.cc?page=1 and we want to remove string 'page=1';
 * @param $url: url include varible to remove
 * @param $key: varible we wanna remove 
 * @return URL after remove varible
 */
function removeQuerystringVar($url, $key){
    $url = preg_replace('/(?:&|(\?))' . $key . '=[^&]*(?(1)&|)?/i', "$1", $url);
    return $url;
}



/**
 * @Desc create link of cms, when we want to rewrite link, we only change this function
 * @param $page: page of the cms 
 * @param $param: array parram example array('id'=>1,'name'=>2)
 * @return URL
 */
function createLink($page, $param = false, $rewrite = true){
    $link = base_url();
    if($rewrite==false){
        $link .= '?page='.$page;
        foreach($param as $key=>$value){
            $link .= '&'.$key.'='.$value;
        }
        return $link;
    }
  
    switch ($page){
        case "intro":
            $page = 'aboutus';
        break;

        case "news_detail":
            if (isset($param['category']) && $rewrite == true){
                $link .= title_url($param['category']) . '/' . $param['id'] . '-' . title_url($param['title']);
            } else{
                $link .= 'news/' . $param['id'] . '-' . title_url($param['title']);
            }
            return $link . '.html';
        break;
    
        case "preview":
            $param['name'] = ($param['name']!='')?$param['name']:'preview';
            if (is_array($param) && count($param > 0) && $rewrite == true){
                $link .= 'preview/'. $param['id'].'-' . title_url($param['name']);
                $link .= '.html';
                if(isset($param['start'])){
                    $link .= '&start='.$param['start'];
                }
                return $link;
            }
        break;
            
        case "microbiz":
            $param['title'] = ($param['title']!='')?$param['title']:'microbiz';
            if (is_array($param) && count($param > 0) && $rewrite == true){
                $link .= 'microbiz/'. $param['id'].'-' . title_url($param['title']);
                $link .= '.html';
                return $link;
            }
        break;
            
        case "news":
            if (is_array($param) && count($param > 0) && $rewrite == true){
                $link .= 'news/'. $param['cid'].'/' . title_url($param['name']);
                return $link . '.html';
            }
        break;

        case "template":
            if (is_array($param) && count($param > 0) && $rewrite == true){
                $link .= 'template/' . title_url($param['name']) . '-' . $param['id'];
                return $link . '.html';
            }
        break;

        case "product":
            if (is_array($param) && count($param > 0) && $rewrite == true){
                $link .= 'product/'. $param['cid'].'/'.title_url($param['name']);
                return $link . '.html';
            }
        break;

        case "product_detail":
            if (is_array($param) && count($param > 0) && $rewrite == true){
                $link .= 'product/' . $param['id'] . '-' . title_url($param['name']);
                return $link . '.html';
            }
        break;

        case "search":
            if (is_array($param) && count($param > 0) && $rewrite == true){
                $link .= 'product-detail/' . title_url($param['name']) . '_' . $param['id'];
                return $link . '.html';
            }
        break;
    }

    if ($page != 'search'){
        $link .= $page;
    } else{
        $link .= '?page=' . $page;
    }

    if (is_array($param) && count($param > 0)){
        $check = false;
        if (strstr($link, '?')){
            $check = true;
        }
        foreach ($param as $key => $value){
            if ($check == false){
                $str = '?';
                $check = true;
            } else{
                $str = '&';
            }
            if ($key == 'title' || $key == 'name'){
                $link .= $str . $key . '=' . title_url($value);
            } else{
                $link .= $str . $key . '=' . $value;
            }
        }
    }
    //return $link;
    if($page=='rss' || $page =='feed'){
        return $link;
    }
    return $link . '.html';

    $link = base_url() . "index.php?page=" . $page;
    if (is_array($param) && count($param > 0)){
        foreach ($param as $key => $value){
            $link .= '&' . $key . '=' . $value;
        }
    }
    return $link;
}


/**
 * @Desc redrect to the adress
 * @param $url: address we want to redirect 
 * @return nothing
 */
function redirect($url){
    @header("location: $url");
}


function upload($dir, $field_name, $resize = false, $create_thumb = false){
    if (isset($_FILES) && $_FILES["$field_name"]["name"] != ''){
        // Defining Class
        $yukle = new upload;

        // Set Max Size
        $yukle->set_max_size(18000000);

        // Set Directory
        $yukle->set_directory($dir);

        // Do not change
        // Set Temp Name for upload, $_FILES['file']['tmp_name'] is automaticly get the temp name
        $yukle->set_tmp_name($_FILES["$field_name"]["tmp_name"]);

        // Do not change
        // Set file size, $_FILES['file']['size'] is automaticly get the size
        $yukle->set_file_size($_FILES["$field_name"]["size"]);

        // Do not change
        // Set File Type, $_FILES['file']['type'] is automaticly get the type
        $yukle->set_file_type($_FILES["$field_name"]["type"]);

        // Set File Name, $_FILES['file']['name'] is automaticly get the file name.. you can change
        $_time = time();
        $file_name = $_FILES["$field_name"]["name"];
        $path_info = pathinfo($file_name);
        $file_name = $path_info['filename'] . '_' . $_time . '.' . $path_info['extension'];
        $yukle->set_file_name($file_name);

        // Start Copy Process
        $yukle->start_copy();

        // If uploaded file is image, you can resize the image width and height
        // Support gif, jpg, png
        if ($resize){
            $yukle->resize($resize['width'], $resize['height']);
        }


        if ($create_thumb){
            // Set a thumbnail name
            $thumb_name = $path_info['filename'] . '_' . $_time . '_thumb.' . $path_info['extension'];
            $yukle->set_thumbnail_name($thumb_name);

            // create thumbnail
            $yukle->create_thumbnail();

            // change thumbnail size
            $yukle->set_thumbnail_size($create_thumb['width'], $create_thumb['height']);
        }

        $url_file = substr(base_url(), 0, -6) . @trim($dir, '..') . '/' . $file_name;
        // Control File is uploaded or not
        // If there is error write the error message
        if ($yukle->is_ok())
            return $url_file;
        else
            return $yukle->error();
        return flase;

    }
}


/**
 * @Desc get cur url in address bar of the browser
 * @return URL
 */
function curPageURL(){
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"){
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80"){
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else{
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    $pageURL = removeQuerystringVar($pageURL,'ref');
    return $pageURL;
}


/**
 * @Desc get <option></option> of array, add charactor example '--' of child option
 * @param $allCat: all category
 * @param $parent_id: id of parent category 
 * @param $space: space want to add to child option
 * @param $id_selected: Id want to add selected
 * @return URL
 */
function getHtmlOptionCategory($allCat, $parent_id = 0, $space = '--', $id_selected = 0){
    $html = '';
    $cur_id = CInput::get('id', 'int', 0);
    $submenu = CInput::get('submenu', 'txt', "");
    if ($submenu != 'category'){
        $cur_id = 0;
    }

    if (is_array($allCat) && count($allCat) > 0){
        $temp_array = $allCat;
        foreach ($allCat as $key => $value){
            if ($value['parent_id'] == $parent_id && ($value['id'] != $cur_id)){
                $selected = '';
                if ($value['id'] == $id_selected)
                {
                    $selected = 'selected=""';
                }
                $html .= '<option value="' . $value['id'] . '" ' . $selected . '>' . $space . $value['name'] .
                    '</option>';
                unset($temp_array[$key]);
                $html .= getHtmlOptionCategory($temp_array, $value['id'], $space . '--', $id_selected);
            }
        }
    }
    return $html;
}



function addDrashCategory(&$allCat, $parent_id = 0, $drash = '-'){
    if (is_array($allCat) && count($allCat) > 0){
        foreach ($allCat as $key => &$value){
            if ($value['parent_id'] == $parent_id){
                if ($parent_id != 0){
                    $value['name'] = $drash . $value['name'];
                }
                addDrashCategory($allCat, $value['id'], $drash);
            }
        }
    }
}


/**
 * @Desc get <option></option> of array, add charactor example '--' of child option
 * @param $allCat: all category
 * @param $parent_id: id of parent page 
 * @param $space: space want to add to child option
 * @param $id_selected: Id want to add selected
 * @return URL
 */
function getHtmlOptionPage($allCat, $parent_id = 0, $space = '--', $id_selected = 0){
    $html = '';
    $cur_id = CInput::get('id', 'int', 0);
    $submenu = CInput::get('submenu', 'txt', "");
    if ($submenu != 'category'){
        $cur_id = 0;
    }

    if (is_array($allCat) && count($allCat) > 0){
        $temp_array = $allCat;
        foreach ($allCat as $key => $value){
            if ($value['parent'] == $parent_id && ($value['id'] != $cur_id)){
                $selected = '';
                if ($value['id'] == $id_selected){
                    $selected = 'selected=""';
                }
                $html .= '<option value="' . $value['id'] . '" ' . $selected . '>' . $space . $value['name'] .
                    '</option>';
                unset($temp_array[$key]);
                $html .= getHtmlOptionPage($temp_array, $value['id'], $space . '--', $id_selected);
            }
        }
    }
    return $html;
}


/**
 * @Desc get info from database
 * @param $name: name of info want to get
 * @return corresponding values
 */
function getInfo($name){
    global $info;
    //var_dump($info);
    if ($info && count($info) > 0){
        foreach ($info as $key => $value){
            if ($name == $value['name']){
                return $value['values'];
            }
        }
    }
}



/**
 * @Desc show error 404 
 * @return 
 */
function show_404_page(){
    echo '
        <!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
            <title>404 Page Not Found</title>
            <meta name="description" content="" />
            <meta name="keywords" content="" />
        </head>
        <body style="text-align:center; background:#fff">
            <div style="width:60%; margin: 0 auto; position: relative;">
                <img border="0" src="'.base_url().'404.png" alt="404 page" width="800">
                <div style="color:#262625;font-weight:bold;font-size:14px;margin-bottom:20px">CHÚNG TÔI KHÔNG TÌM THẤY BÀI VIẾT NÀY.</div>
                <div style="text-align: center; width: 100%">
                Quay trở lại <a style="color: #262625; font-weight: bold" href="'.base_url().'">Trang chủ</a>
                để xem các thông tin mới nhất trên <a style="color: #262625; font-weight: bold" href="'.base_url().'">
                '.$_SERVER['SERVER_NAME'].'</a></div>
        	</div>
        </body>
        </html>';
}


/**
 * @Desc endcode id
 * @param $input: id want to endcode
 * @param $key_seed: string end code
 * @return string
 */
function EncryptData($input, $key_seed = 'KEY_ENCRYPT_DECRYPT_HANHCMS') {
    $input = trim($input);
    $block = mcrypt_get_block_size('tripledes', 'ecb');
    $len = strlen($input);
    $padding = $block - ($len % $block);
    $input .= str_repeat(chr($padding), $padding);
    $key = substr(md5($key_seed), 0, 24);
    $iv_size = mcrypt_get_iv_size(MCRYPT_TRIPLEDES, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $encrypt_data = mcrypt_encrypt(MCRYPT_TRIPLEDES, $key, $input, MCRYPT_MODE_ECB, $iv);
    return base64_encode($encrypt_data);
}


/**
 * @Desc decode id
 * @param $input: id want to decode
 * @param $key_seed: string end code
 * @return string
 */
function DecryptData($input, $key_seed = 'KEY_ENCRYPT_DECRYPT_HANHCMS'){
    $input = base64_decode($input);
    $key = substr(md5($key_seed), 0, 24);
    $text = mcrypt_decrypt(MCRYPT_TRIPLEDES, $key, $input, MCRYPT_MODE_ECB, '12345678');
    $block = mcrypt_get_block_size('tripledes', 'ecb');
    $packing = ord($text{strlen($text) - 1});
    if ($packing and ($packing < $block)){
        for ($P = strlen($text) - 1; $P >= strlen($text) - $packing; $P--){
            if (ord($text{$P}) != $packing){
                $packing = 0;
            }
        }
    }
    $text = substr($text, 0, strlen($text) - $packing);
    return $text;
}


/**
 * @Desc count user online
 * @return array: include total visitor and current online array('total'=>123, 'current'=>123)
 */
function counter() {
    $rip = $_SERVER['REMOTE_ADDR'];
    $sd = date("H:i:s Y/m/d", time());
    $count = 1;
    $maxu = 1;
    $total = 0;

    $file1 = "ip.txt";
    $lines = file($file1);
    $line2 = "";

    foreach ($lines as $line_num => $line) {
        if ($line == 0) {
            $pos_haicham = strpos($line, ':');
            $strlen = strlen($line);
            $total = trim(substr($line, $pos_haicham + 1, $strlen - $pos_haicham));
        } else if ($line_num == 1) {
                $pos_haicham = strpos($line, ':');
                $strlen = strlen($line);
                $maxu = trim(substr($line, $pos_haicham + 1, $strlen - $pos_haicham));
        } else {
            $fp = strpos($line, "****");
            $nam = substr($line, 0, $fp);
            $sp = strpos($line, "++++");
            $val = substr($line, $fp + 4, $sp - ($fp + 4));

            $diff = strtotime($sd) - strtotime($val);

            if ($diff < 300 && $nam != $rip) {
                $count = $count + 1;
                $line2 = $line2 . $line;
                $total++;
            }
        }
    }

    $my = $rip . "****" . $sd . "++++\n";
    if ($count > $maxu)
        $maxu = $count;

    $open1 = fopen($file1, "w");
    fwrite($open1, "total_user: $total\n");
    fwrite($open1, "current online: $maxu\n");
    fwrite($open1, "$line2");
    fwrite($open1, "$my");
    fclose($open1);
    $arrCounter = array('total' => $total, 'online' => $maxu);
    return $arrCounter;
}


/**
 * @Desc convert string to url
 * @param string $str: string to conver
 * @param string $separator: drash url is this-is-url
 * @param true|false $lowercase
 * @return string
 */
function url_title($str, $separator = 'dash', $lowercase = false) {
    if ($separator == 'dash') {
        $search = '_';
        $replace = '-';
    } else {
        $search = '-';
        $replace = '_';
    }

    $trans = array('&\#\d+?;' => '', '&\S+?;' => '', '\s+' => $replace, '[^a-z0-9\-\._]' =>
        '', $replace . '+' => $replace, $replace . '$' => $replace, '^' . $replace => $replace,
        '\.+$' => '');

    $str = strip_tags($str);
    foreach ($trans as $key => $val) {
        $str = preg_replace("#" . $key . "#i", $val, $str);
    }

    if ($lowercase === true) {
        $str = strtolower($str);
    }
    return trim(stripslashes($str));
}

/**
 * @Desc convert string to url
 * @param string $str: string to convert
 * @param string $separator: drash url is this-is-url
 * @param true|false $lowercase
 * @return string
 */
function codau_to_khongdau($str) {
    $vietChar = 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ|é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|ó|ò|ỏ|õ|ọ|ơ|ớ|ờ|ở|ỡ|ợ|ô|ố|ồ|ổ|ỗ|ộ|ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|í|ì|ỉ|ĩ|ị|ý|ỳ|ỷ|ỹ|ỵ|đ|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ|Ó|Ò|Ỏ|Õ|Ọ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự|Í|Ì|Ỉ|Ĩ|Ị|Ý|Ỳ|Ỷ|Ỹ|Ỵ|Đ';
    $engChar = 'a|a|a|a|a|a|a|a|a|a|a|a|a|a|a|a|a|e|e|e|e|e|e|e|e|e|e|e|o|o|o|o|o|o|o|o|o|o|o|o|o|o|o|o|o|u|u|u|u|u|u|u|u|u|u|u|i|i|i|i|i|y|y|y|y|y|d|A|A|A|A|A|A|A|A|A|A|A|A|A|A|A|A|A|E|E|E|E|E|E|E|E|E|E|E|O|O|O|O|O|O|O|O|O|O|O|O|O|O|O|O|O|U|U|U|U|U|U|U|U|U|U|U|I|I|I|I|I|Y|Y|Y|Y|Y|D';
    $arrVietChar = explode("|", $vietChar);
    $arrEngChar = explode("|", $engChar);
    return str_replace($arrVietChar, $arrEngChar, $str);
}


/**
 * @Desc convert title to url
 * @param string $str: string to convert
 * @return string
 */
function title_url($str) {
    $str = strtolower(url_title(codau_to_khongdau($str)));
    return str_replace('.', '', $str);
}

/**
 * @Desc convert string to url
 * @return array
 */
function list_module(){
    $arrModule = array();
    $module_dir =   INC_PATH."modules/";     
    $arrFile = scandir($module_dir,0);
    if(count($arrFile)>0){
        foreach($arrFile as $key=>$value){
            if($key==0 || $key==1){
                continue;
            }
            
            if(!strpos($value,'.php')){
                continue;
            }
            
            $arrModule[] = substr($arrFile[$key],0,-4);
        }
    }
    return $arrModule;
}


/**
 * @Desc insert media infomation to the database
 * @param string $path: path to the file
 * @param int $object_id: id of product, news, etc...
 * @param string $type: news, product, etc...
 * @param array $other_info: additional information of the file
 * @return int
 */
function insetMedia($path, $object_id = '0', $type = '', $other_info = ''){
    
    $path = trim($path,'.');
    if($path==''){
        return '';
    }
    
    /** other information **/
    // size
    $size = filesize(INC_PATH.$path);
    if(!isset($other_info['size'])) $other_info['size'] = $size;
    
    // with and height with images
    if(@is_array(getimagesize(INC_PATH.$path))){
        $data = getimagesize(INC_PATH.$path);
        $width = $data[0];
        $height = $data[1];
        $other_info['wh'] = $width.'x'.$height;
    }
    
    // file info
    $file_info = pathinfo(INC_PATH.$path);
    $other_info['ext']  = $file_info['extension'];
    $other_info['name'] = $file_info['basename'];
    
    includeClassFile('miniMedia.class.php');
    $miniMedia = new miniMedia();
    $miniMedia->path        = $path;
    $miniMedia->object_id   = $object_id;
    $miniMedia->type        = $type;
    $miniMedia->other_info  = json_encode($other_info);
    $miniMedia->date        = date('Y-m-d H:i:s');
    return $miniMedia->insert();
}



/**
 * @Desc delete media in the database and remove file
 * @param mix $media: id in the table or path to the file
 * @return int
 */
function deleteMedia($media){
    includeClassFile('miniMedia.class.php');
    $miniMedia = new miniMedia();
    if(is_int($media)){ // if $media variable is the id in the database
        $id = $media;
        $miniMedia->read($id);
        $path = $miniMedia->path;
        $path = INC_PATH.trim($path,'.');
        
        // delete origion file
        if(file_exists($path)){
            @unlink($path); // delete file  
        }
        $miniMedia->remove($id);
        
    }else{ // if $media variable is the path in the database
        $path = $media;
        $media_info = $miniMedia->getMediaByPath($path);
        $path = INC_PATH.trim($media_info['path']);
        // delete origion file
        if(file_exists($path)){
            @unlink($path);
        }
        $id = $media_info['id'];
        $miniMedia->remove($id);
    }
    
    return true;
}

//////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// END CMS FUNCTION /////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////






//////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// USER FUNCTION //////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * @Desc create hash password
 * @param $password: password before hash
 * @return password hash
 */

function createMd5Password($password) {
    return md5(PASS_ENDCODE . $password);
}


/**
 * @Desc check string if that it a email
 * @param $email: string want to check is a email
 * @return true|false
 */
function checkValidEmail($email) {
    $result = true;
    if (!@eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)){
        $result = false;
    }
    return $result;
}


/**
 * @Desc get IP address of user 
 * @return IP address
 */
function getUserIp() {
    return trim($_SERVER["REMOTE_ADDR"]);
}


/**
 * @Desc send mail to a user
 * @param $to: email want to send
 * @param $subject: Subject of email
 * @param $content: the content of mail
 * @return true|false
 */
function sendMail($form, $to, $reply = false, $subject, $content) {
    require_once (INC_PATH . 'classes/PhpMailer/class.phpmailer.php');
    $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
    $mail->IsSendmail(); // telling the class to use SendMail transport
    try {
        $mail->AddAddress($to['email'], $to['name']);
        $mail->SetFrom($form['email'], $form['name']);
        if (is_array($reply) && count($reply) > 0)
        {
            $mail->AddReplyTo($reply['email'], $reply['name']);
        }

        $mail->Subject = $subject;
        $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
        $mail->MsgHTML($content);
        $mail->Send();
        return "SS";
    }
    catch (phpmailerException $e) {
        return $e->errorMessage(); //Pretty error messages from PHPMailer
    }
    catch (exception $e) {
        return $e->getMessage(); //Boring error messages from anything else!
    }
}


/**
 * @Desc check pemisstion of user Admin
 * @return true|false
 */
function checkAdminPermission() {
    global $_SESSION;
    return $_SESSION;
    if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
        if ($user['group_id'] == 100) {
            return $user;
        }
    }
    return false;
}


/**
 * @Desc check pemisstion of Admin
 * @return true|false
 */
function checkUserAdmin() {
    global $_SESSION;
    if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
        if ($user['group_id'] >= 50) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}


/**
 * @Desc get info from session user (after signed)
 * @param $name: info want to get
 * @return corresponding values
 */
function getUserInfo($name) {
    global $_SESSION;
    if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
        return $user['username'];
    }
    return '';
}


/**
 * @Desc check user exists via type: example username, email, id...
 * @param $type: field in database example email, username...
 * @param $value: value of type example hanhcoltech@gmail.com, duchanhtb
 * @return array
 */
function checkUserExits($type, $value) {
    global $oDb;
    $sql = "SELECT * FROM tbl_user WHERE `$type` = '$value' ";
    $query = $oDb->query($sql);
    if ($oDb->numRows($query) > 0) {
        $result = $oDb->fetchAll($query);
        return $result[0];
    } else {
        return false;
    }
}


/**
 * @Desc function process user when login 
 * @return unidentified
 */
function userLogin() {
    global $_SESSION, $error;
    $username = isset($_COOKIE['cms_username']) ? $_COOKIE['cms_username'] : '';
    $password = isset($_COOKIE['cms_password']) ? $_COOKIE['cms_password'] : '';
    $ref = CInput::get('ref', 'txt', '');

    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = CInput::get('username');
        $password = CInput::get('password');
        
        if (isset($_POST['remember'])) {
            setcookie("cms_username", $username, time() + 3600); // Sets the cookie username
            setcookie("cms_password", $password, time() + 3600); // Sets the cookie password
        }
        
        if ($username != '' && $password != '') {
            $miniUser = new miniUser();
            $user = $miniUser->login($username, $password);
            if ($user) {
                $_SESSION['user'] = $user;
                redirect(urldecode($ref));
            } else {
                $error = 'Username or Password not match';
            }
        } else {
            $error = 'Invalid Username or Password';
        }
    } else if ($username && $password) {
        $miniUser = new miniUser();
        $user = $miniUser->login($username, $password);
        if ($user) {
            $_SESSION['user'] = $user;
            redirect(urldecode($ref));
        } else {
            $error = 'Invalid Username or Password';
        }
    }
}


/**
 * @Desc function process user when logout 
 * @return unidentified
 */
function userLogout() {
    unset($_SESSION['user']);
    setcookie("cms_username", "", time() - 3600);
    setcookie("cms_password", "", time() - 3600);
    redirect('login.php');
}


/**
 * @Desc function process get new pass of user
 * @return unidentified
 */
function getPass() {
    $ref = CInput::get('ref', 'txt', '');
    $email = $_POST['email'];
    $user = checkUserExits('email', $email);
    if ($user && count($user) > 0) {
        $from_email = trim(str_replace('www', '', $_SERVER['SERVER_NAME']), '.');
        $from['email'] = 'admin@' . $from_email;
        $from['name'] = 'Admin';
        $to['email'] = $user['email'];
        $to['name'] = ($user['fullname'] != '') ? $user['fullname'] : $user['username'];
        $subject = "GET NEW PASSWORD";

        $link_getpass = base_url() . 'dred-cms/login.php?cmd=resetpass_form&username=' .
            $user['username'] . '&code=' . md5(FORGOTPASS_ENDCODE . $user['username']);

        $content = 'To get new password please click <a href="' . $link_getpass .
            '">here</a> or link below <br/>';
        $content .= '<a href="' . $link_getpass . '">' . $link_getpass . '</a>';
        sendMail($from, $to, false, $subject, $content);
        $msg = "Check email $email to get new password";
        redirect(base_url() . "dred-cms/login.php?cmd=forgotpass&msg=" . $msg);
    } else {
        $msg = "Email $email does not exists";
        redirect("login.php?cmd=forgotpass&msg=" . $msg);
    }

}

//////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// END USER FUNCTION ////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////




//////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// TIME FUNCTION ////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * @Desc return string of month example: January, December...
 * @param $month_number: number in(1...12)
 * @return string
 */
function getMonthOfNumber($month_number) {
    $month_name = date('F', mktime(0, 0, 0, $month_number));
    return $month_name;
}



/**
 * @Desc return string since $unix_time_stamp to current time 
 * @param $unix_time_stamp: unix time stamp
 * @return string
 */
function getTimeAgo($unix_time_stamp) {
    $cur_time = time();
    if ($cur_time > $unix_time_stamp) {
        $diff_second = $cur_time - $unix_time_stamp;
        //return $diff_second;
        if ($diff_second < 60) {
            return $diff_second . 'seconds before';
        } else if ($diff_second < 3600) {
            $minutes = ceil($diff_second / 60);
            return $minutes . ' minutes before';
        } else if ($diff_second < 86400) {
                $hour = ceil($diff_second / 3600);
                return $hour . ' hours ago';
        } else if ($diff_second < (86400 * 30)) {
                $day = ceil($diff_second / 86400);
                return $day . ' days ago';
        } else if ($diff_second < (12 * 30 * 86400)) {
            return ceil($diff_second / (30 * 86400)) . ' months ago';
        } else {
            return ceil($diff_second / (13 * 30 * 86400)) . ' years ago';
        }
    } else {
        return '';
    }
}


/**
 * @Desc get html of Minute
 * @param $minute: current minute (selected="") 
 * @return HTML
 */
function getOptionMinute($minute) {
    $html = '';
    for ($i = 0; $i <= 59; $i++) {
        if ($i < 10) {
            $ii = '0' . $i;
        } else {
            $ii = $i;
        }

        if ($i == $minute) {
            $html .= '<option value="' . $i . '" selected="selected">' . $ii . '</option>';
        } else {
            $html .= '<option value="' . $i . '">' . $ii . '</option>';
        }
    }
    return $html;
}


/**
 * @Desc get html of Hour
 * @param $hour: current hour (selected="") 
 * @return HTML
 */
function getOptionHour($hour) {
    $html = '';
    for ($i = 0; $i <= 23; $i++) {
        if ($i < 10) {
            $ii = '0' . $i;
        } else {
            $ii = $i;
        }
        if ($i == $hour) {
            $html .= '<option value="' . $i . '" selected="selected">' . $ii . '</option>';
        } else {
            $html .= '<option value="' . $i . '">' . $ii . '</option>';
        }
    }
    return $html;
}


/**
 * @Desc get html of Day
 * @param $dd: current day (selected="") 
 * @return HTML
 */
function getOptionDay($dd) {
    $html = '';
    for ($i = 0; $i <= 31; $i++) {
        if ($i < 10) {
            $ii = '0' . $i;
        } else {
            $ii = $i;
        }

        if ($i == 0) {
            $html .= '<option value="">dd</option>';
        } else {
            if ($i == $dd) {
                $html .= '<option value="' . $i . '" selected="selected">' . $ii . '</option>';
            } else {
                $html .= '<option value="' . $i . '">' . $ii . '</option>';
            }
        }
    }
    return $html;
}



/**
 * @Desc get html of Month
 * @param $mm: current month (selected="") 
 * @return HTML
 */
function getOptionMonth($mm) {
    $html = '';
    for ($i = 1; $i <= 12; $i++) {
        if ($i == 0) {
            $html .= '<option value="">mm</option>';
        } else {
            $t = strtotime('2005-' . $i . '-20');
            $month_name = date('M', $t);
            if ($i == $mm) {
                $html .= '<option value="' . $i . '" selected="selected">' . $month_name .
                    '</option>';
            } else {
                $html .= '<option value="' . $i . '">' . $month_name . '</option>';
            }
        }
    }
    return $html;
}


/**
 * @Desc get html of year
 * @param $yy: current year (selected="")
 * @param $start: start year
 * @param $num: number of year display (from start);
 * @return HTML
 */
function getOptionYear($yy, $start = 1960, $num = 50)
{
    $html = '';
    for ($i = $start; $i <= $start + $num; $i++)
    {
        if ($i == 0)
        {
            $html .= '<option value="">yyyy</option>';
        } else
        {
            if ($i == $yy)
            {
                $html .= '<option value="' . $i . '" selected="selected">' . $i . '</option>';
            } else
            {
                $html .= '<option value="' . $i . '">' . $i . '</option>';
            }

        }

    }
    return $html;
}

//////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// END TIME FUNCTION ////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////





//////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// OTHER FUNCTION ////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////


/**
 * @Desc get value from table tbl_config by label
 * @param $label: label wanna to get
 * @return array
 */
function getConfig($label){
    global $oDb, $allConfig;
    if(isset($allConfig) && count($allConfig)>0){
        if(isset($allConfig[$label])) return $allConfig[$label];
        else return "";
    }else{
        $allConfig = array();
        $sql = 'SELECT * FROM  `tbl_config` ';
        $query	= $oDb->query($sql);        
    	$result_array 	= $oDb->fetchAll($query);
        foreach($result_array as $key=>$value){
            $allConfig[$value['label']] = $value['value'];
        }        
        return $allConfig[$label];    
    }    
}


/**
 * @Desc get small images and create thumbnail if not exists
 * @param string $folder: example thumb-150
 * @param mix $path_or_id: path of the images or id in the tbl_media
 * @return string
 */
function getThumbnail($folder, $path_or_id){
    $path   = '';
    $thumb  = '';
    
    if(is_int($path_or_id)){
        $id = $path_or_id;
        includeClassFile('miniMedia.class.php');
        $miniMedia = new miniMedia();
        $miniMedia->read($id);
        $path = $miniMedia->path;   
    }else{
        $path = trim($path_or_id,'.');
    }
    
    
    if($path){
        global $imagesSize;
        $size_info  = $imagesSize[$folder];
        $with       = $size_info['width'];
        $height     = $size_info['height'];
        $crop       = $size_info['crop'];
        $thumb      = createThumbnail($folder, $path, $with, $height, $crop);
    }
    return $thumb;
}



/**
 * @Desc create thumbnail if not exists
 * @param string $folder: folder of the thumbnail
 * @param $path: path of the origin images
 * @return array
 */
function createThumbnail($folder, $path, $width, $height, $crop = false){
    include_once(INC_PATH."classes/Images.class.php"); // library crop, resize images
    if($path){
        // remove base_url
        $img_path = str_replace(base_url(),'',$path);
        $img_path = trim($img_path,'.');
        $img_path = INC_PATH.$img_path;
        
        if(!file_exists($img_path)){
            return 'admin/images/noimage.jpg'; 
        }
        
        // create Folder
        $path_info      = pathinfo($img_path);
        $filename       = $path_info['basename'];
        $file_folder    = $path_info['dirname'];
        $thumb_dir      = $file_folder.'/'.$folder.'/';
        if(!is_dir($thumb_dir)){
            mkdir($thumb_dir,0775,true);
        }
       
        // create images if not exists;
        $thumb_path = $thumb_dir.$filename;
        if(!file_exists($thumb_path)){
            $crop = ($crop==true) ? 'crop' : 'fit';
            $imageThumb = new Image($img_path);
            $imageThumb->createThumb($thumb_path,$width,$height,$crop);    
        }
        return str_replace(INC_PATH,'',$thumb_path);   
    }
    return 'admin/images/noimage.jpg';
}



/**
 * @Desc get small image example thumb, thumb300
 * @param $link_img: link img full
 * @param $type: type wanna get
 * @return array
 */
function getSmallImages($link_img, $type = 'thumb', $crop = false){
    include_once(INC_PATH."classes/Images.class.php");
    if($link_img){
        // remove base_url
        $img_path = str_replace(base_url(),'',$link_img);
        $img_path = trim($img_path,'.');
        $img_path = '..'.$img_path;
        
        if(!file_exists($img_path)){
            return 'admin/images/noimage.jpg'; 
        }
        
        // create Folder 
        $last_pos_slash =  strrpos($img_path,'/');
        $first = substr($img_path,0,$last_pos_slash-strlen($img_path)+1);
        $filename =  substr($img_path,$last_pos_slash+1,strlen($img_path));
        $thumb_dir = $first.$type.'/';
        if(!is_dir($thumb_dir)){
            mkdir($thumb_dir,0775,true);
        }
        $thumb_path = $thumb_dir.$filename;
        // create images if not exists;
        if(!file_exists($thumb_path)){
            $size = intval(str_replace('thumb','',$type));
            $size = ($size > 0 ) ? $size : 150;
            $crop = ($crop==true) ? 'crop' : 'fit';
            $imageThumb = new Image($img_path);
            $imageThumb->createThumb($thumb_path,$size,$size,$crop);    
        }
        $last_pos_slash =  strrpos($link_img,'/');
        $first = substr($link_img,0,$last_pos_slash-strlen($link_img)+1);
        $filename =  substr($link_img,$last_pos_slash+1,strlen($link_img));
        return $first.$type.'/'.$filename;   
    }
    return 'admin/images/noimage.jpg';
}

