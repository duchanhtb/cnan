<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author DucHanh
 * @copyright 2011
 */
//////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// COMMON FUNCTION //////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * @Desc get infomation by IP
 * @param none
 * @return array
 */
function getGeoInfo(){
    $geoInfo = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=' . getUserIp()));
    $arrayInfo = array(
        'region_name'           => @$geoInfo['geoplugin_regionName'],
        'country_code'          => @$geoInfo['geoplugin_countryCode'],
        'country_name'          => @$geoInfo['geoplugin_countryName'],
        'latitude'              => @$geoInfo['geoplugin_latitude'],
        'longitude'             => @$geoInfo['geoplugin_longitude'],
        'currency_code'         => @$geoInfo['geoplugin_currencyCode']
    );
    return $arrayInfo;
}


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
    } else {
        return substr($str, $start, $len) . '...';
    }
}

/**
 * @Desc get id of youtube
 * @param $ytURL: link of video from youtube example: http://www.youtube.com/watch?v=aQPIPaH4eAE 
 * @return string example: aQPIPaH4eAE
 */
function get_youtube_id($ytURL) {
    if(!$ytURL){
        return '';
    }
    
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
        return '';

    // Offset the start location to match the beginning of the ID string
    $idStarts += 3;

    // Get the ID string and return it
    $ytvID = substr($ytURL, $idStarts, $ytvIDlen);

    return $ytvID;
}



/**
 * @Desc function get youtube duration by Youtube ID
 * @param $ytid: id of youtube video. Example aQPIPaH4eAE 
 * @return string example: H:m:s
 */
function getYouTubeDuration($ytid){
    $api_key = 'AIzaSyAJHqwLb82C9wb9-1iHKG9GykKf8Jv7qZI';
    $dur = file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=$ytid&key=$api_key");
    $duration =json_decode($dur, true);
    $time = '00:00';
    if(is_array($duration) && isset($duration['items'][0]['contentDetails']['duration'])){
        $youtube_duration = $duration['items'][0]['contentDetails']['duration'];
        $di = new DateInterval($youtube_duration);
        $string = '';
        if ($di->h > 0) {
          $string .= $di->h.':';
        }

        $time =  $string.$di->i.':'.$di->s;
    }
    return $time;
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
    
    $save_to = @trim($save_to, '..');
    $path_info = pathinfo($save_to);
    $dir_name = $path_info['dirname'];
    if(!is_writable($dir_name)){
        return false;
    }
    
    if (!is_dir($dir_name)) {
        mkdir($dir_name, 0777, true);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_URL, $file_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $file_content = curl_exec($ch);
    curl_close($ch);
    
    if(!$file_content){
        return false;
    }
    
    $downloaded_file = fopen($save_to, 'w');
    fwrite($downloaded_file, $file_content);
    fclose($downloaded_file);
    
    return true;
}

/**
 * @Desc remove  whitespace from between html tag
 * @param string $html: html string
 * @return string
 */
function compressHtml($html) {
    return preg_replace('~>\\s+<~m', '><', $html);
}

/**
 * @Desc get file name of string: example: the input is "/var/www/html/abc.txt" the output is abc.txt 
 * @param string $str: table in the database
 * @return string
 */
if (!function_exists('getFileName')) {

    function getFileName($str) {
        $path_info = pathinfo($str);
        $ext = $path_info['extension'];
        $filename = $path_info['filename'];
        return $filename . '.' . $ext;
    }

}

/**
 * @Desc function remove VNI string. example ê->e, â->a, ẹ->e
 * @param string $str: the input string
 * @return string
 */
if (!function_exists('remove_special_char')) {

    function remove_special_char($str) {
        // chuyen co dau sang khong dau
        $vietChar = 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ|é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|ó|ò|ỏ|õ|ọ|ơ|ớ|ờ|ở|ỡ|ợ|ô|ố|ồ|ổ|ỗ|ộ|ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|í|ì|ỉ|ĩ|ị|ý|ỳ|ỷ|ỹ|ỵ|đ|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ|Ó|Ò|Ỏ|Õ|Ọ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự|Í|Ì|Ỉ|Ĩ|Ị|Ý|Ỳ|Ỷ|Ỹ|Ỵ|Đ';
        $engChar = 'a|a|a|a|a|a|a|a|a|a|a|a|a|a|a|a|a|e|e|e|e|e|e|e|e|e|e|e|o|o|o|o|o|o|o|o|o|o|o|o|o|o|o|o|o|u|u|u|u|u|u|u|u|u|u|u|i|i|i|i|i|y|y|y|y|y|d|A|A|A|A|A|A|A|A|A|A|A|A|A|A|A|A|A|E|E|E|E|E|E|E|E|E|E|E|O|O|O|O|O|O|O|O|O|O|O|O|O|O|O|O|O|U|U|U|U|U|U|U|U|U|U|U|I|I|I|I|I|Y|Y|Y|Y|Y|D';
        $arrVietChar = explode("|", $vietChar);
        $arrEngChar = explode("|", $engChar);
        $str = str_replace($arrVietChar, $arrEngChar, $str);

        // url title 
        $separator = 'dash';
        $lowercase = false;
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
        $str = trim(stripslashes($str));

        // return value
        return strtolower($str);
    }

}

//////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// END COMMON FUNCTION //////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// CMS FUNCTION /////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////

function sanitize_output($buffer) {
    $search = array(
        '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
        '/[^\S ]+\</s',  // strip whitespaces before tags, except space
        '/(\s)+/s'       // shorten multiple whitespace sequences
    );

    $replace = array(
        '>',
        '<',
        '\\1'
    );

    $buffer = preg_replace($search, $replace, $buffer);

    return $buffer;
}


function is_admin() {
    
}

/**
 * @Desc get language from table m_language
 * @return array
 */
function getLanguage() {
    global $lang, $oDb;
    $cur_lang = $_SESSION['language'];
    if (is_array($lang) && count($lang) > 0) {
        return $lang;
    } else {
        $sql = "SELECT * FROM m_language WHERE 1 ";
        $rc = $oDb->query($sql);
        $rs = $oDb->fetchAll();
        foreach ($rs as $key => $value) {
            $lang[$value['key']] = $value[$cur_lang];
        }
    }
    return $lang;
}

/**
 * Thực hiện việc set và update sesion cho các bản
 *
 * @param str $name
 * @param unknow type $value
 * @return true
 */
function SetSession($name, $value) {
    $_SESSION['HCMS_' . $name] = $value;
    return true;
}

/**
 * Thực hiện việc get dữ liệu từ sesion
 *
 * @param unknown_type $name
 * @return session value
 */
function GetSession($name, $default = NULL) {
    return isset($_SESSION['HCMS_' . $name]) ? $_SESSION['HCMS_' . $name] : $default;
}



/**
 * luu trang thai database vua thay doi vao file, neu la 1 thi reload lai cache, 0 thi bo qua
 *
 * @param boolean $change: true neu db vua thay doi, false neu vua set cache xong
 * @return nothing
 */
function cacheSetting($changed){
    $arrSetting = array('reload_cache' => 0, 'cache_time' => 604800); // cache one week
    if($changed == true){
        $arrSetting['reload_cache'] = 1;
    }
    $cache_file = ROOT_PATH . 'cache/setting.json';
    $fp = @fopen($cache_file, 'w');
    @fwrite($fp, json_encode($arrSetting));
    @fclose($fp);
}



/**
 *  function doc content tu file /cache/setting.json
 *
 * @return int
 */
function getCacheSetting(){
    $cache_file = ROOT_PATH . 'cache/setting.json';
    $setting = file_get_contents($cache_file);
    if($setting){
        return json_decode($setting, true);
    }
    return null;
}


/**
 * get current url on address bar
 *
 * @return string
 */
function getSiteUrl() {
    $ffc_ar = explode("/", $_SERVER['PHP_SELF']);
    $ffc_ar_count = count($ffc_ar);
    $ffc_ar2 = array();
    for ($i = 0; $i < $ffc_ar_count - 1; $i++) {
        $ffc_ar2[$i] = $ffc_ar[$i];
    }
    $ffc_webFolderName = implode("/", $ffc_ar2);
    if (strpos($_SERVER['SERVER_SOFTWARE'], "IIS")) {
        $sPhysicPath = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '\\') + 1);
    } else {
        $sPhysicPath = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '/') + 1);
    }
    $sProtocol = ( strpos($_SERVER['SERVER_PROTOCOL'], "HTTPS") ) ? "https" : "http";
    $sProtocol .= "://";
    $sHostName = $_SERVER['HTTP_HOST'];
    $sitePath = $sProtocol . $sHostName . $ffc_webFolderName . "/";
    return $sitePath;
}

/**
 * Thực hiện việc load module
 *
 * @param str $name: module name excluding extension php
 * @return HTML code
 */
function loadModule($name) {
    $module_path = ROOT_PATH . "modules/$name.php";
    if (file_exists($module_path)) {
        include_once($module_path);
    } else {
        return 'module dose not exists';
    }
    $module = new $name();
    return $module->draw();
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
function add_image_size($folder, $width, $height, $crop = false) {
    global $imagesSize;
    if (!isset($imagesSize[$folder])) {
        $imagesSize[$folder] = array('width' => $width, 'height' => $height, 'crop' => $crop);
    }
    return $imagesSize;
}

/**
 * @Desc paging ajax
 * @param $current_page: number current page
 * @param $total_page: total pages we have
 * @param $func_callback: function javascript call when click to the page
 * @param $item_each_page: number items wanna display on a page
 * @param $searchTxt: 
 * @return HTML paging
 */
function paging_ajax($current_page, $total_page, $func_callback, $item_each_page, $searchTxt = "") {
    if ($total_page <= 1) {
        return "";
    }

    $data = '<ul class="pagination clearfix">';
    if ($total_page <= 10) {
        if ($current_page > 1) {
            $data .= '<li><a href="#null" onclick="' . $func_callback . '(' . ($current_page - 1) . ',' . $item_each_page . ',\'' . $searchTxt . '\')"></a></li>';
        }

        for ($i = 1; $i <= $total_page; $i++) {
            if ($i == $current_page) {
                $data .= '<li class="active"><a href="#null">' . $i . '</a></li>';
            } else {
                $data .= '<li><a href="#null" onclick="' . $func_callback . '(' . $i . ',' . $item_each_page . ',\'' . $searchTxt . '\')">' . $i . '</a></li>';
            }
        }
        if ($current_page != $total_page) {
            $data .= '<li><a href="#null" onclick="' . $func_callback . '(' . ($current_page + 1) . ',' . $item_each_page . ',\'' . $searchTxt . '\')"></a></li>';
        }
    }

    if ($total_page > 10) {
        $minpage = ($current_page - 5) > 1 ? ($current_page - 5) : 1;
        $maxpage = ($current_page + 5) < $total_page ? ($current_page + 5) : $total_page;
        if ($current_page > 1) {
            $data .= '<li><a href="#null" onclick="' . $func_callback . '(' . ($current_page -
                    1) . ',' . $item_each_page . ',\'' . $searchTxt . '\')"></li>';
        }

        for ($i = $minpage; $i <= $maxpage; $i++) {
            if ($i == $current_page) {
                $data .= '<li class="active"><a href="#null">' . $i . '</a></li>';
            } else {
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
 * @Desc function return HTML paging
 * @param $current_page: nunber current of the page
 * @param $total_page: total page we have
 * @param $linkpage: link page we have example: http://hanhnguyen.co.cc/?p=
 * @return HTML paging
 */
function paging($current_page, $total_page, $linkpage, $p = 'p') {
    if ($linkpage == false) {
        $linkpage = curPageURL();
    }
    $linkpage = removeQuerystringVar($linkpage, $p);

    if (parse_url($linkpage, PHP_URL_QUERY) == NULL) {
        $linkpage .= "?$p=";
    } else {
        $linkpage .= "&$p=";
    }

    if ($total_page <= 1) {
        return "";
    }

    $data = '<ul class="pages">';
    if ($total_page <= 10) {
        if ($current_page > 1) {
            $data .= '<li class="btn-back"><a href="' . $linkpage . ($current_page - 1) .
                    '">&lt;&lt; Trước</a></li>';
        }

        for ($i = 1; $i <= $total_page; $i++) {
            if ($i == $current_page) {
                $data .= '<li class="active"><a href="javascript:void(0)">' . $i .
                        '</a></li>';
            } else {
                $data .= '<li><a href="' . $linkpage . $i . '" >' . $i . '</a></li>';
            }
        }

        if ($current_page != $total_page) {
            $data .= '<li class="btn-next"><a href="' . $linkpage . ($current_page + 1) .
                    '">Tiếp &gt;&gt;</a></li>';
        }
    }

    if ($total_page > 10) {
        $minpage = ($current_page - 4) > 1 ? ($current_page - 4) : 1;
        $maxpage = ($current_page + 4) < $total_page ? ($current_page + 4) : $total_page;

        if ($current_page > 1) {
            $data .= '<li class="btn-back"><a href="' . $linkpage . ($current_page - 1) .
                    '">&lt;&lt; Trước</a></li>';
        }

        for ($i = $minpage; $i <= $maxpage; $i++) {
            if ($i == $current_page) {
                $data .= '<li class="active"><a href="javascript:void(0)">' . $i .
                        '</a></li>';
            } else {
                $data .= '<li><a href="' . $linkpage . $i . '" >' . $i . '</a></li>';
            }
        }

        if ($current_page != $maxpage) {
            $data .= '<li class="btn-next"><a href="' . $linkpage . ($current_page + 1) . '">Tiếp &gt;&gt;</a>';
        }
    }
    $data .= '</ul>';
    return $data;
}

/**
 * @Desc function return HTML paging
 * @param $current_page: nunber current of the page
 * @param $total_page: total page we have
 * @param $linkpage: link page we have example: http://hanhnguyen.co.cc/?page=
 * @return HTML paging
 */
function getHtmlPaging($cur_page, $total_page, $linkpage = false) {
    if ($linkpage == false) {
        $linkpage = curPageURL();
    }
    $linkpage = removeQuerystringVar($linkpage, 'page');
    $pageHtml = '';
    if ($total_page >= 1) {
        $option_page = '';
        for ($i = 1; $i <= $total_page; $i++) {
            if ($i == $cur_page) {
                $option_page .= '<option value="' . $i . '" selected="">' . $i . '</option>';
            } else {
                $option_page .= '<option value="' . $i . '">' . $i . '</option>';
            }
        }
        if ($total_page == 1) {
            $first_page_link = $linkpage;
            $last_page_link = $linkpage . '&page=' . ($total_page);

            $link_next = $linkpage . '&page=' . ($cur_page);
            $link_prev = $linkpage . '&page=' . ($cur_page);
        } else
        if ($cur_page == 1) {
            $first_page_link = $linkpage;
            $last_page_link = $linkpage . '&page=' . ($total_page);

            $link_next = $linkpage . '&page=' . ($cur_page + 1);
            $link_prev = $linkpage . '&page=' . ($cur_page);
        } else
        if ($cur_page == $total_page) {
            $first_page_link = $linkpage;
            $last_page_link = $linkpage . '&page=' . ($total_page);

            $link_next = $linkpage . '&page=' . ($cur_page);
            $link_prev = $linkpage . '&page=' . ($cur_page - 1);
        } else {
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
function removeQuerystringVar($url, $key) {
    $url = preg_replace('/(?:&|(\?))' . $key . '=[^&]*(?(1)&|)?/i', "$1", $url);
    return rtrim(rtrim($url, '&'), '?');
}

/**
 * @Desc create link of cms, when we want to rewrite link, we only change this function
 * @param $page: page of the cms 
 * @param $param: array parram example array('id'=>1,'name'=>2)
 * @return URL
 */
function createLink($page, $param = false, $rewrite = true) {
    $link = base_url();
    if ($rewrite == false) {
        $link .= '?page=' . $page;
        foreach ($param as $key => $value) {
            $link .= '&' . $key . '=' . $value;
        }
        return $link;
    }
    $link .= $page;
    if(isset($param['subpage']) && $param['subpage'] !=''){
        $link .= '/'.$page['subpage'];
    }
    if ($param && $param['id'] > 0 && $param['title'] != '') {
        $id_endcode = alphaID($param['id'], false);
        $link .= '/' . title_url($param['title']) . '/' . $id_endcode;
    }
    return $link . '.html';
}


/**
 * @Desc get page from url. Example: url is http://cnan.com/recipe/abc123/U5K5.html -> page is recipe
 * @return string
 */
function getPage() {
    $current_page = curPageURL();
    $current_page = str_replace(base_url(), '', $current_page);
    $current_page = explode('.', $current_page);
    $current_page = $current_page[0];

    if ($current_page) {
        $current_page = explode('/', $current_page);
        if (is_array($current_page) && count($current_page) > 0) {
            return $current_page[0];
        }
    }
    return 'home';
}


/**
 * @Desc get page from url. Example: url is http://cnan.com/recipe/timkiem.html -> subpage is timkiem
 * @return string
 */
function getSubPage(){
    $current_page = curPageURL();
    $current_page = str_replace(base_url(), '', $current_page);
    $current_page = explode('.', $current_page);
    $current_page = $current_page[0];

    if ($current_page) {
        $current_page = explode('/', $current_page);
        if (count($current_page) > 0 && isset($current_page[1])) {
            return $current_page[1];
        }
    }
    return '';
}


/**
 * @Desc get id from url, example url = http://cnan.com/congthuc/cach-kho-ca/XuT14.html => id = alpha('XuT14',true);
 * @param
 * @return int
 */
function getCmsId() {
    $current_page = curPageURL();
    $current_page = str_replace(base_url(), '', $current_page);
    if ($current_page) {
        $current_page = explode('/', $current_page);
        if (is_array($current_page) && count($current_page) > 0) {
            $max_key = max(array_keys($current_page));
            $max_value = $current_page[$max_key];
            $max_value = explode('.', $max_value);
            $max_value = $max_value[0];
            if ($max_value) {
                return alphaID($max_value, true);
            }
        }
    }
    return NULL;
}

/**
 * @Desc redrect to the adress
 * @param $url: address we want to redirect 
 * @return nothing
 */
function redirect($url) {
    @header("location: $url");
}

function upload($dir, $field_name, $resize = false, $create_thumb = false) {
    if (isset($_FILES) && $_FILES["$field_name"]["name"] != '') {
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
        if ($resize) {
            $yukle->resize($resize['width'], $resize['height']);
        }


        if ($create_thumb) {
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
function curPageURL() {
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    $pageURL = removeQuerystringVar($pageURL, 'ref');
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
function getHtmlOptionCategory($allCat, $parent_id = 0, $space = '--', $id_selected = 0) {
    $html = '';
    $cur_id = Input::get('id', 'int', 0);
    $submenu = Input::get('submenu', 'txt', "");
    if ($submenu != 'category') {
        $cur_id = 0;
    }

    if (is_array($allCat) && count($allCat) > 0) {
        $temp_array = $allCat;
        foreach ($allCat as $key => $value) {
            if ($value['parent_id'] == $parent_id && ($value['id'] != $cur_id)) {
                $selected = '';
                if ($value['id'] == $id_selected) {
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

function addDrashCategory(&$allCat, $parent_id = 0, $drash = '-') {
    if (is_array($allCat) && count($allCat) > 0) {
        foreach ($allCat as $key => &$value) {
            if ($value['parent_id'] == $parent_id) {
                if ($parent_id != 0) {
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
function getHtmlOptionPage($allCat, $parent_id = 0, $space = '--', $id_selected = 0) {
    $html = '';
    $cur_id = Input::get('id', 'int', 0);
    $submenu = Input::get('submenu', 'txt', "");
    if ($submenu != 'category') {
        $cur_id = 0;
    }

    if (is_array($allCat) && count($allCat) > 0) {
        $temp_array = $allCat;
        foreach ($allCat as $key => $value) {
            if ($value['parent'] == $parent_id && ($value['id'] != $cur_id)) {
                $selected = '';
                if ($value['id'] == $id_selected) {
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
 * @Desc show error 404 
 * @return 
 */
function show_404_page($return = false) {
    $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
            <title>404 Page Not Found</title>
            <meta name="description" content="404 Page Not Found" />
            <meta name="keywords" content="404 Page Not Found" />
        </head>
        <body style="text-align:center; background:#fff">
            <div style="width:60%; margin: 0 auto; position: relative;">
                <img border="0" src="' . base_url() . '404.png" alt="404 page" width="800">
                <div style="color:#262625;font-weight:bold;font-size:14px; margin-bottom:20px; text-transform: uppercase">Bài viết không tồn tại hoặc đã bị gỡ bỏ.</div>
                <div style="text-align: center; width: 100%">
                Quay trở lại <a style="color: #262625; font-weight: bold; text-transform: uppercase" href="' . base_url() . '">Trang chủ</a>
                để xem các thông tin mới nhất trên <a style="color: #262625; font-weight: bold; text-transform: uppercase" href="' . base_url() . '">
                ' . $_SERVER['SERVER_NAME'] . '</a></div>
        	</div>
        </body>
        </html>';
    
    if($return){
        return $html;
    }else{
        echo $html;
    }
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
function DecryptData($input, $key_seed = 'KEY_ENCRYPT_DECRYPT_HANHCMS') {
    $input = base64_decode($input);
    $key = substr(md5($key_seed), 0, 24);
    $text = mcrypt_decrypt(MCRYPT_TRIPLEDES, $key, $input, MCRYPT_MODE_ECB, '12345678');
    $block = mcrypt_get_block_size('tripledes', 'ecb');
    $packing = ord($text{strlen($text) - 1});
    if ($packing and ( $packing < $block)) {
        for ($P = strlen($text) - 1; $P >= strlen($text) - $packing; $P--) {
            if (ord($text{$P}) != $packing) {
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
function list_module() {
    $arrModule = array();
    $module_dir = ROOT_PATH . "modules/";
    $arrFile = scandir($module_dir, 0);
    if (count($arrFile) > 0) {
        foreach ($arrFile as $key => $value) {
            if ($key == 0 || $key == 1) {
                continue;
            }

            if (!strpos($value, '.php')) {
                continue;
            }

            $arrModule[] = substr($arrFile[$key], 0, -4);
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
function insertMedia($path, $object_id = '0', $object_type = '', $other_info = '', $date = false) {

    $path = trim($path, '.');
    if ($path == '') {
        return '';
    }

    $object_type = ($object_type != '') ? $object_type : $_REQUEST['f'];

    /** other information * */
    // size
    $size = @filesize(ROOT_PATH . $path);
    if (!isset($other_info['size']))
        $other_info['size'] = $size;

    // with and height with images
    if (@is_array(getimagesize(ROOT_PATH . $path))) {
        $data = getimagesize(ROOT_PATH . $path);
        $width = $data[0];
        $height = $data[1];
        $other_info['wh'] = $width . 'x' . $height;
    }

    // file info
    $file_info = pathinfo(ROOT_PATH . $path);
    $other_info['ext'] = $file_info['extension'];
    $other_info['name'] = $file_info['basename'];

    $Media = new Media();
    $Media->path = $path;
    $Media->object_id = $object_id;
    $Media->object_type = $object_type;
    $Media->type = mime_content_type(ROOT_PATH . $path);
    $Media->other_info = json_encode($other_info);
    if ($date) {
        $Media->date = $date;
    } else {
        $Media->date = date('Y-m-d H:i:s');
    }
    if(isset($_SESSION['admin'])){
        $Media->user_id = $_SESSION['admin']['id'];
        $Media->username = $_SESSION['admin']['name'];
    }else{
        $user = GetSession('user');
        $Media->user_id = $user['id'];
        $Media->username = $user['username'];
    }
    

    $id = $Media->insert();
    $_SESSION['media'][] = $id; // add to session, using this session to update object_id in the table
    return $id;
}

/**
 * @Desc delete media in the database and remove file
 * @param mix $media: id in the table or path to the file
 * @return int
 */
function deleteMedia($media) {
    global $imagesSize;
    $Media = new Media();
    if (is_int($media)) { // if $media variable is the id in the database
        $id = $media;
        $Media->read($id);
        $path = $Media->path;
        $path = ROOT_PATH . trim($path, '.');

        // delete origion file
        if (file_exists($path)) {
            @unlink($path); // delete file  
        }

        // delete thumb file
        foreach ($imagesSize as $folder => $wh) {
            $path_info = pathinfo($path);
            $thumb = $path_info['dirname'] . '/' . $folder . '/' . $path_info['basename'];
            // delete origion file
            if (file_exists($thumb)) {
                @unlink($thumb); // delete file  
            }
        }
        $Media->remove($id);
    } else { // if $media variable is the path in the database
        $path = $media;
        $media_info = $Media->getMediaByPath($path);
        $path = ROOT_PATH . trim($media_info['path']);
        // delete origion file
        if (file_exists($path)) {
            @unlink($path);
        }

        // delete thumb file
        foreach ($imagesSize as $folder => $wh) {
            $path_info = pathinfo($path);
            $thumb = $path_info['dirname'] . '/' . $folder . '/' . $path_info['basename'];
            // delete origion file
            if (file_exists($thumb)) {
                @unlink($thumb); // delete file  
            }
        }

        $id = $media_info['id'];
        $Media->remove($id);
    }

    return true;
}

/**
 * @Desc hack mime type function
 * @param string $filename: path to the file
 * @return string
 */
if (!function_exists('mime_content_type')) {

    function mime_content_type($filename) {

        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(@array_pop(@explode('.', $filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        } else {
            return 'application/octet-stream';
        }
    }

}

/**
 * @Desc translate multi language
 * @param string $key: key of word in the database or string input. Example $lang[language] = 'Ngôn ngữ' in Vietnamese / $language['language'] = Language in English
 * @param array $replace: using for replace. Example the string language is: 
 * 		  	language['test'] = "Những %s đã chọn phải lớn hơn %s ký tự".
 * 			the array input: array('mật khẩu', '10').
 * 			the string after replace will be: Những mật khẩu đã chọn phải lớn hơn 10 ký tự 
 * @return string
 */
function trans($key, $replace = false) {
    $lang = getLanguage();
    if (isset($lang[$key])) {
        $arrStr = explode('%s', $lang[$key]);
        if (count($replace) > 0 && count($arrStr) > 1) {
            $str = '';
            foreach ($arrStr as $k => $value) {
                if (isset($replace[$k])) {
                    $str .= $arrStr[$k] . $replace[$k];
                } else {
                    $str .= $arrStr[$k];
                }
            }
            return $str;
        } else {
            return $lang[$key];
        }
    }
    return $key;
}

/**
 * @Desc add title for header
 * @global type $title
 * @param type $title_content
 * @return none
 */
function addTitle($title_content) {
    global $title;
    $title = $title_content;
}

/**
 * @Desc add keywords for headear
 * @global type $keywords
 * @param type $keyword_content
 * @return none
 */
function addKeywords($keyword_content) {
    global $keywords;
    $keywords = $keyword_content;
}

/**
 * @Desc add description for headear
 * @global type $description
 * @param type $description_content
 * @return none
 */
function addDescription($description_content) {
    global $description;
    $description = $description_content;
}

/**
 * @Desc register css to header
 * @param string $handle: unique id
 * @param string $src: url to css
 * @param mix $ver: version
 * @param string $media: media attribute of css, default is all
 * @return nothing
 */
function register_style($handle, $src, $ver = false, $media = 'all') {
    global $cms_style;
    $cms_style[$handle] = array(
        'src' => $src,
        'ver' => $ver,
        'media' => $media,
    );
}

/**
 * @Desc remove css from header
 * @param string $handle: unique id
 * @return nothing
 */
function remove_style($handle) {
    global $cms_style;
    unset($cms_style[$handle]);
}

/**
 * @Desc add inline style to cms header
 * @param string $handle: unique id
 * @param string $data: css without <style> ... </style> tags
 * @return nothing
 */
function add_inline_style($handle, $data) {
    global $cms_inline_style;
    $cms_inline_style[$handle] = $data;
}

/**
 * @Desc register javascript 
 * @param string $handle: unique id
 * @param string $src: url to script file
 * @param mix $ver: version
 * @param boolean $in_footer: set true if add to the footer
 * @return none
 */
function register_script($handle, $src, $ver = false, $in_footer = true) {
    global $cms_script;
    $cms_script[$handle] = array(
        'src' => $src,
        'ver' => $ver,
        'in_footer' => $in_footer,
    );
}

/**
 * @Desc remove script from header
 * @param string $handle: unique id
 * @return nothing
 */
function remove_script($handle) {
    global $cms_script;
    unset($cms_style[$handle]);
}

/**
 * @Desc The Open Graph protocol
 * @param string $property: property, example title, type, url, images, description
 * @param string $content: content of property
 * @return none
 */
function addGraphTags($handle, $property, $content) {
    global $graph_tags;
    $graph_tags[$handle] = array(
        'property' => $property,
        'content' => $content,
    );
}

/**
 * @Desc register javascript and css for header
 * @return string
 */
function cms_header() {
    global $graph_tags, $cms_style, $cms_inline_style, $cms_script, $title, $keywords, $description;

    $html = '';

    // title
    if ($title != '') {
        $html .= "<title>" . $title . "</title>" . PHP_EOL;
    } else {
        $html .= "<title>" . getCmsTitle() . "</title>" . PHP_EOL;
    }

    //keyword
    if ($keywords != '') {
        $html .= '<meta name="keywords" content="' . $keywords . '" >' . PHP_EOL;
    } else {
        $html .= '<meta name="keywords" content="' . getCmsKeywords() . '" >' . PHP_EOL;
    }

    //description
    if ($description != '') {
        $html .= '<meta name="description" content="' . $description . '" >' . PHP_EOL;
    } else {
        $html .= '<meta name="description" content="' . getCmsDescription() . '" >' . PHP_EOL;
    }

    // graph tags
    if (is_array($graph_tags) && count($graph_tags) > 0) {
        foreach ($graph_tags as $handle => $graph) {
            $html .= '<meta property="og:' . $graph['property'] . '" content="' . $graph['content'] . '" />' . PHP_EOL;
        }
    }


    // style
    if (is_array($cms_style) && count($cms_style) > 0) {
        foreach ($cms_style as $handle => $style) {
            $ver = (isset($style['ver']) && $style['ver'] != '') ? '?ver=' . $style['ver'] : '';
            $media = isset($style['meida']) ? $style['meida'] : 'all';
            $html .= '<link rel="stylesheet" href="' . $style['src'] . $ver . '" media=' . $media . ' />' . PHP_EOL;
        }
    }

    // script
    if ($cms_script && count($cms_script) > 0) {
        foreach ($cms_script as $handle => $script) {
            if ($script['in_footer'] == true)
                continue;
            $ver = isset($style['ver']) ? '?ver=' . $style['ver'] : '';
            $media = isset($style['meida']) ? $style['meida'] : 'all';
            $html .= '<script src="' . $script['src'] . '" type="text/javascript" name="' . $handle . '"></script>' . PHP_EOL;
        }
    }


    if (is_array($cms_inline_style) && count($cms_inline_style)) {
        $html .= '<style type="text/css">';
        foreach ($cms_inline_style as $data) {
            $html .= $data . PHP_EOL;
        }
        $html .= '</style>';
    }

    return $html;
}

/**
 * @Desc register javascript for footer
 * @return string
 */
function cms_footer() {
    global $cms_script;
    if (!$cms_script || count($cms_script) <= 0)
        return;
    $html = '';
    foreach ($cms_script as $handle => $script) {
        if ($script['in_footer'] == false)
            continue;
        $ver = (isset($style['ver']) && $style['ver'] != '') ? '?ver=' . $style['ver'] : '';
        $html .= '<script src="' . $script['src'] . '" type="text/javascript" name="' . $handle . '"></script>' . PHP_EOL;
    }


    return $html;
}

/**
 * @Desc get cms title
 * @return string
 */
function getCmsTitle() {
    $page = Input::get('page', 'txt', 'home');
    $Page = new Page();
    $pageInfo = $Page->getPageInfo($page);
    return $pageInfo['meta_title'];
}

/**
 * @Desc get cms title
 * @return string
 */
function getCmsKeywords() {
    $page = Input::get('page', 'txt', 'home');
    $Page = new Page();
    $pageInfo = $Page->getPageInfo($page);
    return $pageInfo['meta_keyword'];
}

/**
 * @Desc get cms title
 * @return string
 */
function getCmsDescription() {
    $page = Input::get('page', 'txt', 'home');
    $Page = new Page();
    $pageInfo = $Page->getPageInfo($page);
    return $pageInfo['meta_description'];
}

//////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// END CMS FUNCTION /////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
//
//
//
//
//
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
    if (!@eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)) {
        $result = false;
    }
    return $result;
}

/**
 * @Desc get IP address of user 
 * @return IP address
 */
function getUserIp() {
    if(trim($_SERVER["REMOTE_ADDR"]) == "::1"){
        return '127.0.0.1';
    }
    return trim($_SERVER["REMOTE_ADDR"]);
}

/**
 * @Desc get infomation by IP  
 * @return array
 */
function getInfoByIP($user_id) {
    $IpInfo = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=' . getUserIp()));
    $arrayInfo = array(
        'city' => $userIpInfo['geoplugin_city'],
        'region_name' => $userIpInfo['geoplugin_regionName'],
        'country_code' => $userIpInfo['geoplugin_countryCode'],
        'country_name' => $userIpInfo['geoplugin_countryName'],
        'latitude' => $userIpInfo['geoplugin_latitude'],
        'longitude' => $userIpInfo['geoplugin_longitude'],
        'code' => $userIpInfo['geoplugin_currencyCode']
    );
    return $arrayInfo;
}

/**
 * @Desc send mail to a user
 * @param $to: email want to send
 * @param $subject: Subject of email
 * @param $content: the content of mail
 * @return true|false
 */
function sendMail($form, $to, $reply = false, $subject, $content) {
    require_once (INC_PATH . 'lib/PhpMailer/class.phpmailer.php');
    $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
    $mail->IsSendmail(); // telling the class to use SendMail transport
    try {
        $mail->AddAddress($to['email'], $to['name']);
        $mail->SetFrom($form['email'], $form['name']);
        if (is_array($reply) && count($reply) > 0) {
            $mail->AddReplyTo($reply['email'], $reply['name']);
        }
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
        $mail->MsgHTML($content);
        $mail->Send();
        return "SS";
    } catch (phpmailerException $e) {
        return $e->errorMessage(); //Pretty error messages from PHPMailer
    } catch (exception $e) {
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
    $sql = "SELECT * FROM t_user WHERE `$type` = '$value' ";
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
    $ref = Input::get('ref', 'txt', '');

    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = Input::get('username');
        $password = Input::get('password');

        if (isset($_POST['remember'])) {
            setcookie("cms_username", $username, time() + 3600); // Sets the cookie username
            setcookie("cms_password", $password, time() + 3600); // Sets the cookie password
        }

        if ($username != '' && $password != '') {
            $User = new User();
            $user = $User->login($username, $password);
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
        $User = new User();
        $user = $User->login($username, $password);
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
    $ref = Input::get('ref', 'txt', '');
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
function getOptionYear($yy, $start = 1960, $num = 50) {
    $html = '';
    for ($i = $start; $i <= $start + $num; $i++) {
        if ($i == 0) {
            $html .= '<option value="">yyyy</option>';
        } else {
            if ($i == $yy) {
                $html .= '<option value="' . $i . '" selected="selected">' . $i . '</option>';
            } else {
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
 * @Desc get value from t_option
 * @param string $option_name: the option name
 * @return string
 */
function get_option($option_name) {
    global $oDb, $all_option;

    if (isset($all_option) && count($all_option) > 0) {
        if (isset($all_option[$option_name]))
            return $all_option[$option_name];
        else
            return "";
    }else {
        $all_option = array();
        $sql = 'SELECT * FROM  `m_options` ';
        $query = $oDb->query($sql);
        $result_array = $oDb->fetchAll($query);
        foreach ($result_array as $key => $value) {
            $all_option[$value['name']] = $value['value'];
        }
        return isset($all_option[$option_name]) ? $all_option[$option_name] : "";
    }
}

/**
 * @Desc add options, do nothing if options exists
 * @param string $option_name, option name
 * @param string $option_value, option value
 * @return boolean
 */
function add_option($option_name, $option_value) {
    global $oDb;
    $sql = " SELECT `name`, `value` FROM `m_options` WHERE `name` = '$option_name' ";
    $rc = $oDb->query($sql);
    if ($oDb->numRows($rc) > 0) {
        return true;
    } else {
        $sql = " INSERT INTO `m_options` (`name`, `value`) VALUES ('$option_name', '$option_value') ";
        $oDb->query($sql);
        return true;
    }
    return false;
}

/**
 * @Desc update option, add news if not exists
 * @param string $option_name, option name
 * @param string $option_value, option value
 * @return boolean
 */
function update_option($option_name, $option_value) {
    global $oDb;
    $current_value = get_option($option_name);
    if ($current_value) { // update if exists;
        $sql = " UPDATE `m_options` SET `value` = '$option_value' WHERE `name` = '$option_name' ";
        $oDb->query($sql);
        return true;
    } else { // add if not exists
        $sql = " INSERT INTO `m_options` (`name`, `value`) VALUES ('$option_name', '$option_value') ";
        $oDb->query($sql);
        return true;
    }
    return false;
}

/**
 * @Desc update multi option, add news if not exists
 * @param string $option_name, option name
 * @param string $option_value, option value
 * @return boolean
 */
function update_multi_options($arrValue) {
    global $oDb;
    $sql_insert = '';
    $sql_update = '';
    $sql_update_name = array();
    foreach ($arrValue as $option_name => $option_value) {
        if (get_option($option_name) != '') {
            update_option($option_name, $option_value);
        } else {
            $sql_insert .= " ('$option_name', '$option_value'),";
        }
    }
    if ($sql_insert) {
        $sql_insert = "INSERT INTO `m_options` (`name`, `value`) VALUES " . trim($sql_insert, ',');
        $oDb->query($sql_insert);
    }
}

/**
 * @Desc get small images and create thumbnail if not exists
 * @param string $folder: example thumb-150
 * @param mix $path_or_id: path of the images or id in the t_media
 * @return string
 */
function getThumbnail($folder, $path_or_id) {
    $path = '';
    $thumb = 'uploads/noimage.jpg';
    
    $pos = strpos($path_or_id, 'http');
    if ($pos === false) {
    } else {
        return $path_or_id;
    }

    if (is_int($path_or_id)) {
        $id = $path_or_id;
        $Media = new Media();
        $Media->read($id);
        $path = $Media->path;
    } else {
        $path = trim($path_or_id, '.');
    }

    global $imagesSize;
    $size_info = $imagesSize[$folder];
    $with = $size_info['width'];
    $height = $size_info['height'];
    $crop = $size_info['crop'];
    if ($path) {
        $thumb = createThumbnail($folder, $path, $with, $height, $crop);
    } else {
        $thumb = createThumbnail($folder, '/uploads/noimage.jpg', $with, $height, $crop);
    }
    return base_url() . ltrim($thumb, '/');
}

/**
 * @Desc create thumbnail if not exists
 * @param string $folder: folder of the thumbnail
 * @param $path: path of the origin images
 * @return array
 */
function createThumbnail($folder, $path, $width, $height, $crop = false) {
    if ($path) {
        // remove base_url
        $img_path = str_replace(base_url(), '', $path);
        $img_path = trim($img_path, '.');
        $img_path = ROOT_PATH . $img_path;
        
        if (!file_exists($img_path)) {
            return 'admin/images/noimage.jpg';
        }

        // create Folder
        $path_info = pathinfo($img_path);
        $filename = $path_info['basename'];
        $file_folder = $path_info['dirname'];
        $thumb_dir = $file_folder . '/' . $folder . '/';
        if (!is_dir($thumb_dir)) {
            mkdir($thumb_dir, 0775, true);
        }

        // create images if not exists;
        $thumb_path = $thumb_dir . $filename;
        if (!file_exists($thumb_path)) {
            $crop = ($crop == true) ? 'crop' : 'fit';
            $imageThumb = new Image($img_path);
            $imageThumb->createThumb($thumb_path, $width, $height, $crop);
        }
        return str_replace(ROOT_PATH, '', $thumb_path);
    }
    return 'admin/images/noimage.jpg';
}

/**
 * @Desc get small image example thumb, thumb300
 * @param $link_img: link img full
 * @param $type: type wanna get
 * @return array
 */
function getSmallImages($link_img, $type = 'thumb', $crop = false) {
    if ($link_img) {
        // remove base_url
        $img_path = str_replace(base_url(), '', $link_img);
        $img_path = trim($img_path, '.');
        $img_path = '..' . $img_path;

        if (!file_exists($img_path)) {
            return 'admin/images/noimage.jpg';
        }

        // create Folder 
        $last_pos_slash = strrpos($img_path, '/');
        $first = substr($img_path, 0, $last_pos_slash - strlen($img_path) + 1);
        $filename = substr($img_path, $last_pos_slash + 1, strlen($img_path));
        $thumb_dir = $first . $type . '/';
        if (!is_dir($thumb_dir)) {
            mkdir($thumb_dir, 0775, true);
        }
        $thumb_path = $thumb_dir . $filename;
        // create images if not exists;
        if (!file_exists($thumb_path)) {
            $size = intval(str_replace('thumb', '', $type));
            $size = ($size > 0 ) ? $size : 150;
            $crop = ($crop == true) ? 'crop' : 'fit';
            $imageThumb = new Image($img_path);
            $imageThumb->createThumb($thumb_path, $size, $size, $crop);
        }
        $last_pos_slash = strrpos($link_img, '/');
        $first = substr($link_img, 0, $last_pos_slash - strlen($link_img) + 1);
        $filename = substr($link_img, $last_pos_slash + 1, strlen($link_img));
        return $first . $type . '/' . $filename;
    }
    return 'admin/images/noimage.jpg';
}

/**
 * @Desc convert bytes to size (Kb, Mb, Gb)
 * @param int $bytes: input byetes
 * @return Kb/Mb/Gb
 */
if (!function_exists('bytesToSize')) {

    function bytesToSize($bytes) {
        $sizes = array(
            0 => 'Bytes',
            1 => 'KB',
            2 => 'MB',
            3 => 'GB',
            4 => 'TB'
        );
        if ($bytes == 0)
            return '0 Bytes';
        $i = intval(floor(log($bytes) / log(1024)));
        return round($bytes / pow(1024, $i), 2) . ' ' . $sizes[$i];
    }

    ;
}

/**
 * Translates a number to a short alhanumeric version
 *
 * Translated any number up to 9007199254740992
 * to a shorter version in letters e.g.:
 * 9007199254740989 --> PpQXn7COf
 *
 * specifiying the second argument true, it will
 * translate back e.g.:
 * PpQXn7COf --> 9007199254740989
 *
 * this function is based on any2dec && dec2any by
 * fragmer[at]mail[dot]ru
 * see: http://nl3.php.net/manual/en/function.base-convert.php#52450
 *
 * If you want the alphaID to be at least 3 letter long, use the
 * $pad_up = 3 argument
 *
 * In most cases this is better than totally random ID generators
 * because this can easily avoid duplicate ID's.
 * For example if you correlate the alpha ID to an auto incrementing ID
 * in your database, you're done.
 *
 * The reverse is done because it makes it slightly more cryptic,
 * but it also makes it easier to spread lots of IDs in different
 * directories on your filesystem. Example:
 * $part1 = substr($alpha_id,0,1);
 * $part2 = substr($alpha_id,1,1);
 * $part3 = substr($alpha_id,2,strlen($alpha_id));
 * $destindir = "/".$part1."/".$part2."/".$part3;
 * // by reversing, directories are more evenly spread out. The
 * // first 26 directories already occupy 26 main levels
 *
 * more info on limitation:
 * - http://blade.nagaokaut.ac.jp/cgi-bin/scat.rb/ruby/ruby-talk/165372
 *
 * if you really need this for bigger numbers you probably have to look
 * at things like: http://theserverpages.com/php/manual/en/ref.bc.php
 * or: http://theserverpages.com/php/manual/en/ref.gmp.php
 * but I haven't really dugg into this. If you have more info on those
 * matters feel free to leave a comment.
 *
 * The following code block can be utilized by PEAR's Testing_DocTest
 * <code>
 * // Input //
 * $number_in = 2188847690240;
 * $alpha_in  = "SpQXn7Cb";
 *
 * // Execute //
 * $alpha_out  = alphaID($number_in, false, 8);
 * $number_out = alphaID($alpha_in, true, 8);
 *
 * if ($number_in != $number_out) {
 * 	 echo "Conversion failure, ".$alpha_in." returns ".$number_out." instead of the ";
 * 	 echo "desired: ".$number_in."\n";
 * }
 * if ($alpha_in != $alpha_out) {
 * 	 echo "Conversion failure, ".$number_in." returns ".$alpha_out." instead of the ";
 * 	 echo "desired: ".$alpha_in."\n";
 * }
 *
 * // Show //
 * echo $number_out." => ".$alpha_out."\n";
 * echo $alpha_in." => ".$number_out."\n";
 * echo alphaID(238328, false)." => ".alphaID(alphaID(238328, false), true)."\n";
 *
 * // expects:
 * // 2188847690240 => SpQXn7Cb
 * // SpQXn7Cb => 2188847690240
 * // aaab => 238328
 *
 * </code>
 *
 * @author	Kevin van Zonneveld <kevin@vanzonneveld.net>
 * @author	Simon Franz
 * @author	Deadfish
 * @author  SK83RJOSH
 * @copyright 2008 Kevin van Zonneveld (http://kevin.vanzonneveld.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
 * @version   SVN: Release: $Id: alphaID.inc.php 344 2009-06-10 17:43:59Z kevin $
 * @link	  http://kevin.vanzonneveld.net/
 *
 * @param mixed   $in	  String or long input to translate
 * @param boolean $to_num  Reverses translation when true
 * @param mixed   $pad_up  Number or boolean padds the result up to a specified length
 * @param string  $pass_key Supplying a password makes it harder to calculate the original ID
 *
 * @return mixed string or long
 */
function alphaID($in, $to_num = false, $pad_up = false, $pass_key = 'HCMS') {
    $out = '';
    $index = 'FMGIJKCDPLNOARSTUVWXEBYQHZ1046785293';
    $base = strlen($index);

    if ($pass_key !== null) {
        // Although this function's purpose is to just make the
        // ID short - and not so much secure,
        // with this patch by Simon Franz (http://blog.snaky.org/)
        // you can optionally supply a password to make it harder
        // to calculate the corresponding numeric ID

        for ($n = 0; $n < strlen($index); $n++) {
            $i[] = substr($index, $n, 1);
        }

        $pass_hash = hash('sha256', $pass_key);
        $pass_hash = (strlen($pass_hash) < strlen($index) ? hash('sha512', $pass_key) : $pass_hash);

        for ($n = 0; $n < strlen($index); $n++) {
            $p[] = substr($pass_hash, $n, 1);
        }

        array_multisort($p, SORT_DESC, $i);
        $index = implode($i);
    }

    if ($to_num) {
        // Digital number  <<--  alphabet letter code
        $len = strlen($in) - 1;

        for ($t = $len; $t >= 0; $t--) {
            $bcp = bcpow($base, $len - $t);
            $out = $out + strpos($index, substr($in, $t, 1)) * $bcp;
        }

        if (is_numeric($pad_up)) {
            $pad_up--;

            if ($pad_up > 0) {
                $out -= pow($base, $pad_up);
            }
        }
    } else {
        // Digital number  -->>  alphabet letter code
        if (is_numeric($pad_up)) {
            $pad_up--;

            if ($pad_up > 0) {
                $in += pow($base, $pad_up);
            }
        }

        for ($t = ($in != 0 ? floor(log($in, $base)) : 0); $t >= 0; $t--) {
            $bcp = bcpow($base, $t);
            $a = floor($in / $bcp) % $base;
            $out = $out . substr($index, $a, 1);
            $in = $in - ($a * $bcp);
        }
    }

    return $out;
}

/**
 * @Desc clean HTML content
 * @param string $str: input string
 * @return string
 */
function cleanHTMLContent($str) {
    return $str;
    /*
    $str = trim($str);
    $str = str_replace("\n", "<br />", $str); // replace \n to <br/>
    $str = preg_replace('#<br />(\s*<br />)+#', '<br />', $str);
    $str = preg_replace('#</p>(\s*<br />)+#', '</p>', $str);
    return trim($str);
     * 
     */
}

/**
 * @Desc add start to charactor
 * @param string $str: input string
 * @return 
 */
function obscure_email($email) {
    $email_array = explode('@', $email);
    $email_word = $email_array[0];
    $email_domain = $email_array[1];
    $obscured_email = substr($email_word, 0, 3);
    for ($i = 3; $i < strlen($email_word); $i++) {
        $obscured_email.='*';
    }
    $obscured_email.='@';
    for ($i = 0; $i < strlen($email_domain) - 2; $i++) {
        $obscured_email.='*';
    }
    return $obscured_email . substr($email, -2, strlen($email));
}
