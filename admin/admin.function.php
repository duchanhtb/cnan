<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * function for CMS admin
 * @author duchanh
 * @copyright 2012
 */

/**
 * @Desc get admin url
 * @param 
 * @return string
 */
function admin_url() {
    return base_url() . ADMIN_FOLDER . '/';
}

/**
 * @Desc get ajax url
 * @param 
 * @return string
 */
function ajax_url() {
    return admin_url() . 'ajax.php';
}

/**
 * @Desc register css to header
 * @param string $handle: unique id
 * @param string $src: url to css
 * @param mix $ver: version
 * @param string $media: media attribute of css, default is all
 * @return nothing
 */
function admin_register_style($handle, $src, $ver = false, $media = 'all') {
    global $admin_style;
    $admin_style[$handle] = array(
        'src' => $src,
        'ver' => $ver,
        'media' => $media,
    );
}

/**
 * @Desc register javascript 
 * @param string $handle: unique id
 * @param string $src: url to script file
 * @param mix $ver: version
 * @param boolean $in_footer: set true if add to the footer
 * @return string
 */
function admin_register_script($handle, $src, $ver = false, $in_footer = false) {
    global $admin_script;
    $admin_script[$handle] = array(
        'src' => $src,
        'ver' => $ver,
        'in_footer' => $in_footer,
    );
}

/**
 * @Desc admin footer
 * @param 
 * @return footer html
 */
function admin_header() {
    global $admin_style, $admin_script, $admin_title;
    $arrScriptGlobal = array(
        'type' => Input::get('f', 'txt', 'media'),
        'base_url' => base_url(),
        'admin_url' => base_url() . ADMIN_FOLDER . '/',
        'ajax_url' => admin_url() . 'ajax.php',
        'session_id' => session_id(),
        'current_lang' => $_SESSION['language']
    );

    $html = '<!DOCTYPE html>
    <html>
    <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>' . $admin_title . ' - Administration</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png">
    <script type="text/javascript">';
    foreach ($arrScriptGlobal as $key => $value) {
        $html .= $key . ' = "' . $value . '";' . PHP_EOL;
    }
    $html .= '
    </script>';

    if ($admin_style && count($admin_style) > 0) {
        foreach ($admin_style as $handle => $style) {
            $ver = (isset($style['ver']) && $style['ver'] != '') ? '?ver=' . $style['ver'] : '';
            $media = isset($style['meida']) ? $style['meida'] : 'all';
            $html .= '<link rel="stylesheet" href="' . $style['src'] . $ver . '" media=' . $media . ' />' . PHP_EOL;
        }
    }

    if ($admin_script && count($admin_script) > 0) {
        foreach ($admin_script as $handle => $script) {
            if ($script['in_footer'] == true)
                continue;
            $ver = (isset($style['ver']) && $style['ver'] != '') ? '?ver=' . $style['ver'] : '';
            $html .= '<script src="' . $script['src'] . '" type="text/javascript" name="' . $handle . '"></script>' . PHP_EOL;
        }
    }
    $html .='
    </head>
    <body>';

    return trim($html);
}

/**
 * @Desc admin header
 * @param 
 * @return footer html
 */
function admin_footer() {
    global $admin_script;
    $html = '';
    if ($admin_script && count($admin_script) >= 0) {
        foreach ($admin_script as $handle => $script) {
            if ($script['in_footer'] == false)
                continue;
            $ver = (isset($style['ver']) && $style['ver'] != '') ? '?ver=' . $style['ver'] : '';
            $html .= '<script src="' . $script['src'] . '" type="text/javascript" name="' . $handle . '"></script>' . PHP_EOL;
        }
    }

    $html .= '
    <script type="text/javascript">
    $(document).ready(function(){
        // show function clock with time on server
        cmsClock("#cms_clock", "' . date("m/d/Y H:i:s", time() + 2) . '");
        
    });
    </script>
    
    </body>
    </html>';

    return trim($html);
}

/**
 * @Desc render admin menu 
 * @param 
 * @return echo HTML menu
 */
function show_admin_menu() {
    global $arrMenu;
    $html = '';
    if ((int) $_SESSION['admin']['level'] >= 1) {
        /*         * ************* render list menu admin ***************** */
        foreach ($arrMenu as $value) {
            $type = isset($value['type']) ? $value['type'] : "";
            switch ($type) {
                case "heading":
                    $html .= '<li class="heading"><a href="javascript:void(0)"><strong style="font-size: 120%;">' . $value['name'] . '</strong></a></li>' . PHP_EOL;
                    break;

                default:
                    $html .= '<li><a href="index.php?f=' . $value['id'] . '" ' . getClassMenu($value) . '>&#151; ' . $value['name'] . '</a></li>' . PHP_EOL;
                    break;
            }
        }
    }
    return $html;
}

/**
 * @Desc render admin content
 * @param
 * @return echo HTML admin content
 */
function show_admin_content() {
    global $arrMenu;
    /*     * ************* content admin ***************** */

    $func = isset($_GET['f']) ? $_GET['f'] : "";
    if ($func == '') {
        redirect("index.php?f=home");
    }

    $html = '';
    if ($func && file_exists(ADMIN_PATH . "include/$func.php")) {
        include_once(ADMIN_PATH . "include/$func.php");
        $func_info = getSubArrayValue($arrMenu, 'id', $func);
        switch ($func) {
            case 'options':
                $cmsOptions = new cmsOptions($of_options);
                $cmsOptions->name = $func_info['name'];
                $html .= $cmsOptions->main();
                break;

            case 'media':
                // show media
                $Media = new Media();
                $html .= $Media->showList();
                break;

            case 'home':
                $html .= '
                <style>
                body{
                    overflow: hidden;
                }
                #hcms_document{
                	border: none;
                }
                </style>
                <iframe id="hcms_document" width="100%" src="https://docs.google.com/document/d/1_4fMRIxQX9niPgbC-13jDJeDnqVF4oAiigb3HVTAk20/pub?embedded=true"></iframe>
                <script type="text/javascript">
                    document.body.style.overflow = "hidden";
                    $("#hcms_document").attr("height", window.innerHeight - 50);
                </script>';
                break;

            case 'changepass':
                $html .= $html_changepass;
                break;

            case 'restrict':
                $html .= $html_restrict;
                break;

            default:
                if (isset($func_info['table']) && $func_info['table'] != '') {
                    $func_table = explode(":", $func_info['table']);
                    $cmsTable = new CmsTable($func_table[0], $column, $func_table[1]);
                    $cmsTable->name = $func_info['name'];
                    if (isset($func_info['suffix_sql'])) {
                        $cmsTable->suffix_sql = $func_info['suffix_sql'];
                    }
                    $html .= $cmsTable->eventHander();
                }
                break;
        }
    }
    return $html;
}

/**
 * @Desc check yahoo status
 * @param string $yid: yahoo id example: hanhnguyen_rav
 * @return true|false
 */
function getAdminTitle($arrConfig) {
    $f = isset($_REQUEST['f']) ? $_REQUEST['f'] : "";
    if ($f) {
        foreach ($arrConfig as $key => $value) {
            if (isset($value['id']) && $f == $value['id']) {
                return $value['name'];
            }
        }
    }
    return 'HCMS';
}

/**
 * @Desc function remove sign
 * @param string $str: the input string
 * @return string
 */
function remove_sign($str) {
    $str = remove_sign_1($str);
    $str = str_replace(array('–', '…', '“', '”', "~", "!", "@", "#", "$", "%", "^", "&", "*", "/", "\\", "?", "<", ">", "'", "\"", ":", ";", "{", "}", "[", "]", "|", "(", ")", ",", ".", "`", "+", "=", "-"), "", $str);
    $str = preg_replace("/[^_A-Za-z0-9- ]/i", '', $str);
    return strtolower($str);
}

/**
 * @Desc function remove VNI string. example ê->e, â->a, ẹ->e
 * @param string $str: the input string
 * @return string
 */
function remove_sign_1($str) {
    $str = str_replace(array("à", "á", "ạ", "ả", "ã", "ă", "ằ", "ắ", "ặ", "ẳ", "ẵ", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ"), "a", $str);
    $str = str_replace(array("À", "Á", "Ạ", "Ả", "Ã", "Ă", "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ"), "A", $str);
    $str = str_replace(array("è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề", "ế", "ệ", "ể", "ễ"), "e", $str);
    $str = str_replace(array("È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ"), "E", $str);
    $str = str_replace("đ", "d", $str);
    $str = str_replace("Đ", "D", $str);
    $str = str_replace(array("ỳ", "ý", "ỵ", "ỷ", "ỹ", "ỹ"), "y", $str);
    $str = str_replace(array("Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ"), "Y", $str);
    $str = str_replace(array("ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ"), "u", $str);
    $str = str_replace(array("Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ"), "U", $str);
    $str = str_replace(array("ì", "í", "ị", "ỉ", "ĩ"), "i", $str);
    $str = str_replace(array("Ì", "Í", "Ị", "Ỉ", "Ĩ"), "I", $str);
    $str = str_replace(array("ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ", "ờ", "ớ", "ợ", "ở", "ỡ"), "o", $str);
    $str = str_replace(array("Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ", "Ờ", "Ớ", "Ợ", "Ở", "Ỡ"), "O", $str);
    return $str;
}

/**
 * @Desc get current address
 * @return string
 */
function getAddress() {
    $adr = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://';
    $adr .= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : getenv('HTTP_HOST');
    $adr .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : getenv('REQUEST_URI');
    return $adr;
}

/**
 * @Desc get ip address
 * @return string
 */
function getIP() {
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
    return $ip;
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

/**
 * @Desc convert date time to "time ago"
 * @param datetime $date: mysql datetime Y-m-d H:i
 * @return string
 */
function print_date($date) {
    $input_date = explode(" ", trim($date));

    $in_date = explode("-", $input_date[0]);
    $in_date = $in_date[2] . "-" . $in_date[1] . "-" . $in_date[0];

    $in_time = explode(':', $input_date[1]);
    $in_hour = $in_time[0];
    $in_minute = $in_time[1];
    $in_second = $in_time[2];


    $date_now = date('d-m-Y');
    $hour_now = date('H');
    $minute_now = date('i');
    $second_now = date('s');

    if ($date_now == $in_date) {
        if ($in_hour == $hour_now) {
            if ($in_minute == $minute_now) {
                $date_output = (int) $second_now - (int) $in_second;
                $date_output = $date_output . " giây trước";
            } else {
                $date_output = (int) $minute_now - (int) $in_minute;
                $date_output = $date_output . " phút trước";
            }
        } else {
            $date_output = (int) $hour_now - (int) $in_hour;
            $date_output = $date_output . " giờ trước";
        }
    } else {
        $date_output = $in_date; //." &nbsp;". implode(":",$in_time);
    }

    return $date_output;
}

/**
 * @Desc clear session favorite post
 * @return nothing
 */
function clear_favorite() {
    $_SESSION['favorite-post'] = null;
}

/**
 * @Desc check favorite post
 * @param int $id: id of favorite post
 * @return boolean
 */
function isfavorite_post($id) {
    if (isset($_SESSION['favorite-post'][$id])) {
        return true;
    } else {
        return false;
    }
}

/**
 * @Desc convert string to price
 * @param string $str: the input string
 * @return int (with comma)
 */
function price($str) {
    $output = preg_replace("/[^0-9]/", "", $str);
    $arr = str_split($output);
    $count = 0;
    $result = "";
    for ($i = sizeof($arr) - 1; $i >= 0; $i--) {
        if ($arr[$i] != "") {
            $count++;
            $result = $arr[$i] . $result;
            if (($count >= 3) && ($i > 0)) {
                $count = 0;
                $result = "," . $result;
            }
        }
    }
    return $result;
}

/**
 * @Desc I don't remember
 * @param string $date: 
 * @param int $num_day:
 * @param string $format:
 * @return date time
 */
function cal_date($date, $num_day, $format = 'Y-m-d H:i:s') {
    $newdate = strtotime($num_day . ' day', strtotime($date));
    $newdate = date($format, $newdate);
    return $newdate;
}

/**
 * @Desc get images table
 * @param string $table_info: table infomation
 * @param int $rid: record id
 * @return array
 */
function getImagesTable($table_info, $rid) {
    if (!$rid) {
        return array();
    }
    global $oDb;
    $table_name = $table_info['table_name'];
    $primary_key = $table_info['primary_key'];
    $url = $table_info['images_url'];
    $relate_id = $table_info['relate_id'];
    $sql = "SELECT `$primary_key`, `$url`  FROM  `$table_name` WHERE `$relate_id` = $rid ";
    $query = $oDb->query($sql);
    $result = $oDb->fetchAll($query);
    return $result;
}

/**
 * @Desc convert string to price
 * @param string $str: the input string
 * @return int (with comma)
 */
function deleteImagesTable($table_info, $id) {
    if (!$id) {
        return;
    }
    global $oDb;
    $table_name = $table_info['table_name'];
    $primary_key = $table_info['primary_key'];
    $url = $table_info['images_url'];
    $sql = "SELECT `$primary_key`, `$url` FROM  `$table_name` WHERE `$primary_key` = $id ";
    $query = $oDb->query($sql);
    $images = $oDb->fetchArray($query);
    $image_path = ROOT_PATH . trim($images[$url], '.');

    // delete origin images
    if (@file_exists($image_path)) {
        @unlink($image_path);
        deleteMedia(trim($images[$url], '.'));
    }
    // delete thumbnail
    global $imagesSize;
    foreach ($imagesSize as $folder => $images_config) {
        $path_info = pathinfo($image_path);
        $thumb_path = $path_info['dirname'] . '/' . $folder . '/' . $path_info['basename'];
        if (@file_exists($thumb_path)) {
            @unlink($thumb_path);
        }
    }

    // delete in database
    $sql = "DELETE FROM `$table_name` WHERE `$table_name`.`$primary_key` = $id LIMIT 1 ";
    $oDb->query($sql);
    return true;
}

/**
 * @Desc get list category (with dash before sub category name)
 * @param 
 * @return array
 */
function getCategory() {
    global $oDb;
    $arr_category = array();
    $arr_category[0] = 'Trang chủ';

    $sql = "SELECT `id`, `name`, `parent_id` FROM t_category WHERE 1 ";
    $sql .= 'ORDER BY `name` ASC ';

    $rs = $oDb->query($sql);
    $allCat = $oDb->fetchAll($rs);
    foreach ($allCat as $key => $value) {
        if ($value['parent_id'] == 0) {
            $name = $value['name'];
            $id = $value['id'];
            $arr_category[$id] = $name;

            // sub1
            foreach ($allCat as $key1 => $value1) {
                if ($value1['parent_id'] == $value['id']) {
                    $name = '-----' . $value1['name'];
                    $id = $value1['id'];
                    $arr_category[$id] = $name;

                    // sub2
                    foreach ($allCat as $key2 => $value2) {
                        if ($value2['parent_id'] == $value1['id']) {
                            $name = '----------' . $value2['name'];
                            $id = $value2['id'];
                            $arr_category[$id] = $name;

                            // sub3
                            foreach ($allCat as $key3 => $value3) {
                                if ($value3['parent_id'] == $value2['id']) {
                                    $name = '---------------' . $value3['name'];
                                    $id = $value3['id'];
                                    $arr_category[$id] = $name;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $arr_category;
}

/**
 * @Desc custom function for admin  
 * @param string $page: page name
 * @return html
 */
function pageLink($page_id, $act = 'list') {
    switch ($act) {
        case "add":
        case "edit":
            return $page_id;
            break;


        case "list":
        default :
            $miniPage = new Page();
            $miniPage->read($page_id);

            return '<a href="page.php?id=' . $page_id . '">' . $miniPage->name . '</a>';
            break;
    }
}

/**
 * @Desc get class for admin menu, class="mnu_select tooltip" or class="tooltip"  
 * @param string $menu: menu name
 * @return string
 */
function getClassMenu($menu) {
    $f = Input::get('f', 'txt', '');
    $menu_select = '';
    $class_tooltip = '';
    $title_tooltip = '';
    if ($f == $menu['id']) {
        $menu_select = 'mnu_select';
    }
    if (trim($menu['desc']) != '' && isset($menu['desc'])) {
        $class_tooltip = 'tooltip';
        $title_tooltip = 'title="&ldquo;  ' . $menu['desc'] . '&bdquo;"';
    }

    $class_name = $menu_select . " " . $class_tooltip;

    if ($class_name) {
        return "class='$class_name' $title_tooltip";
    } else {
        return '';
    }
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
 * @Desc get max value of primary key
 * @param string $table: table in the database
 * @param string $id_field: the id of the table
 * @return int
 */
function getMaxId($table, $id_field = 'id') {
    global $oDb;
    $sql = "SELECT $id_field FROM $table ORDER BY $id_field DESC LIMIT 0,1";
    $rc = $oDb->query($sql);
    $rs = $oDb->fetchArray($rc);
    return $rs[$id_field] + 1;
}

/* return value of array.
  example:
  $arr[] = array(a=>b);
  $arr[] = array(c=>d, n=>m);
  $arr[] = array(e=>f);

  $result = getSubArrayValue($arr, 'c', 'd'); // return array(c=>d, 'n'=>m);
  -------------------------------------------------------------------------- */

function getSubArrayValue($arr, $sub_key, $sub_value) {
    foreach ($arr as $value) {
        if (isset($value[$sub_key]) && $value[$sub_key] == $sub_value) {
            return $value;
            break;
        }
    }
    return NULL;
}

/**
 * @Desc convert bytes to size (Kb, Mb, Gb)
 * @param int $bytes: input byetes
 * @return Kb/Mb/Gb
 */
if (!function_exists('bytesToSize')) {

    function bytesToSize($bytes) {
        $sizes = array(
            0 => 'Byte',
            1 => 'Kb',
            2 => 'Mb',
            3 => 'Gb',
            4 => 'Tb'
        );
        if ($bytes == 0)
            return '0 Byte';
        $i = intval(floor(log($bytes) / log(1024)));
        return round($bytes / pow(1024, $i), 2) . ' ' . $sizes[$i];
    }

    ;
}

/**
 * @Desc download images from copy content to host
 * @param string $str: the input string
 * @return int (with comma)
 */
function downloadImagesFromHTML($html) {
    include_once(INC_PATH . 'lib/simple_html_dom.php');

    $html = str_get_html($html);

    foreach ($html->find('img') as $e) {
        $src = $e->src;
        // check if image exists on this hosting
        if (strpos($src, base_url()) === false) {

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
            download_file($src, ROOT_PATH . $file_path);

            // insert to the media
            insertMedia($file_path);

            // update dom attribute
            $e->src = base_url() . trim($file_path, '/');
        }
    }

    $content = $html->save();
    return $content;
}

/**
 * @Desc remove commar and dot in string
 * @param string $str: the input string
 * @return int (without comma, dots)
 */
function cleanNumber($a) {
    $a = str_replace(',', '', $a);
    $a = str_replace('.', '', $a);
    return $a;
}
