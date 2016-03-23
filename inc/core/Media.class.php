<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 * @desc class render html admin media
 */
class Media extends Base {

    var $id = '';
    var $pk = 'id';
    var $pk_auto = true; //Primary key auto increment
    var $fields = array(
        'object_id',
        'object_type',
        'type',
        'path',
        'other_info',
        'user_id',
        'username',
        'date'
    ); //fields in table (excluding Primary Key)
    
    var $table = "t_media";
    var $name = "Media";
    var $rop = 48;
    var $totalPage = 0;
    var $totalRow = 0;
    var $view;
    var $q = "";
    var $arrMedia = array();
    var $arrFileExt = array(
        'audio' => array('3gp', 'act', 'aiff', 'aac', 'au', 'awb', 'dct', 'dss', 'dvf', 'flac', 'gsm', 'illax', 'ivs', 'm4a', 'mnf', 'mp3', 'mpc', 'msv', 'ogg', 'oga', 'ra', 'rm', 'raw', 'sln', 'tta', 'vox', 'wav', 'wma', 'wv'),
        'compressed' => array('rar', 'zip', 'a', 'ar', 'cpio', 'shar', 'lbr', 'iso', 'lbr', 'mar', 'tar', 'bz2', 'f', 'gz', 'gz', 'lz', 'lzma', 'lzo', 'rz', 'sfark', 'xz', 'infl'),
        'css' => array('css'),
        'developer' => array('exe', 'msi', 'dmg'),
        'excel' => array('xls', 'xlt', 'xlsx', 'xlsm', 'xltx', 'xlsb', 'xla', 'xlam', 'xll', 'xlw', 'stc', 'sxc'),
        'fireworks' => array('fw'),
        'flash' => array('swf', 'flv'),
        'html' => array('htm', 'html'),
        'illustrator' => array('ai'),
        'image' => array('jpg', 'jpeg', 'exif', 'tiff', 'rif', 'gif', 'bmp', 'png', 'ico'),
        'keynote' => array('key', 'keynote'),
        'numbers' => array('numbers'),
        'pages' => array('asp', 'aspx', 'adp', 'bml', 'cfm', 'cgi', 'ihtml', 'jsp', 'lasso', 'pl', 'php', 'rna', 'r', 'rnx', 'ssi', 'sql'),
        'pdf' => array('pdf'),
        'photoshop' => array('psd'),
        'powerpoint' => array('ppt', 'pptx', 'pot', 'potx', 'pps', 'ppsx', 'ppt', 'pptx', 'thmx', 'sti', 'sxi'),
        'video' => array('aaf', 'dat', 'webm', 'mkv', 'flv', 'ogv', 'ogg', 'drc', 'mng', 'avi', 'mov', 'qt', 'wmv', 'rm', 'rmvb', 'asf', 'mp4', 'm4p', 'm4v', 'mpg', 'mp2', 'mpeg', 'mpg', 'mpe', 'mpv', 'mpg', 'mpeg', 'm2v', 'm4v', 'svi', '3gp', '3g2', 'mxf', 'roq', 'nsv', 'mov'),
        'word' => array('doc', 'docx', 'docm', 'dot', 'dotx', 'odt', 'ott', 'odm')
    );

    public function __construct() {

        if (function_exists('admin_url')) {

            admin_register_style('media-simplemodal', admin_url() . 'css/simple-modal.css'); // simple modal
            admin_register_script('media-simplemodal', admin_url() . 'js/jquery.simplemodal.js', false, true); // simple modal

            admin_register_style('media-style', admin_url() . 'css/mediaelementplayer.css');
            admin_register_script('mediaelement', admin_url() . 'js/mediaelement.min.js', false, true);

            admin_register_script('jquery-ui-wiget', admin_url() . 'js/jquery.ui.widget.js', false, true);
            admin_register_script('jquery-fileupload', admin_url() . 'js/jquery.fileupload.js', false, true); // file upload

            admin_register_style('media-tipsy', admin_url() . 'css/tipsy.css'); // jquery tipsy
            admin_register_script('media-tipsy', admin_url() . 'js/jquery.tipsy.js', false, true);
        }

        $media_view = isset($_SESSION['media_view']) ? $_SESSION['media_view'] : "grid";
        $this->view = Input::get('view', 'txt', $media_view);
        $_SESSION['media_view'] = $this->view;
    }

    /**
     * @desc get media by path to the file
     * @param string $path: path to the file
     * @return array
     */
    public function getMediaByPath($path) {
        $con = " AND path = '$path' ";
        $result = $this->get('*', $con);
        if (is_array($result)) {
            return $result[0];
        }
        return false;
    }

    /**
     * @desc get all date media group by month
     * @param nothing
     * @return array
     */
    public function getAllDateMeida() {
        global $oDb;
        $sql = "SELECT `date`, count(*) as total FROM $this->table GROUP BY YEAR(`date`), MONTH(`date`) ORDER BY `date` DESC ";
        $rc = $oDb->query($sql);
        return $oDb->fetchAll($rc);
    }

    /**
     * @desc get option date for search
     * @return array('option_html'=> html,'total_row' => number )
     */
    public function getOptionDateMedia() {
        $cur_date = Input::get('search-date', 'txt', '');
        $arrDate = $this->getAllDateMeida();
        $option_date = '<option value="all">' . trans('all_time') . '</option>';
        foreach ($arrDate as $key => $value) {
            $unix_time = strtotime($value['date']);
            $date_txt = date('Y - m', $unix_time);
            $date_value = date('Y-m', $unix_time);
            $tab = '&nbsp;&nbsp;&nbsp;&#151;&nbsp;&nbsp;&nbsp;';
            if ($date_value == $cur_date) {
                $option_date .= '<option value="' . $date_value . '" selected="">' . $date_txt . $tab . '(&nbsp;' . formatPrice($value['total']) . ' ' . trans('files') . ' )</option>';
            } else {
                $option_date .= '<option value="' . $date_value . '">' . $date_txt . $tab . '(' . formatPrice($value['total']) . ')</option>';
            }
        }
        return $option_date;
    }

    /**
     * @desc get option type for search
     * @return array('option_html'=> html,'total_row' => number )
     */
    public function getOptionType() {
        $search_type = Input::get('search-type', 'txt', '');
        $arrType = array('all' => trans('all_media'), 'image' => trans('images'), 'file' => 'File');
        $html_option = '';
        foreach ($arrType as $type => $label) {
            if ($type == $search_type) {
                $html_option .= '<option value="' . $type . '" selected="">' . $label . '</option>';
            } else {
                $html_option .= '<option value="' . $type . '">' . $label . '</option>';
            }
        }
        return $html_option;
    }

    /**
     * @desc get media by condition input
     * @param string $con: sql condition
     * @param string $sort: sort field
     * @param string $order: direction DESC|ASC
     * @param $page: current page
     * @param $num_page: number return records 
     * @return array
     */
    public function getMedia($con = '', $sort = 'id', $order = 'DESC', $page = 1, $num_page = 50) {
        $page = ($page >= 1 ) ? $page : 1;
        $start_row = ($page - 1) * $num_page;
        return $this->get('*', $con, "$sort $order", $start_row, $num_page);
    }

    /**
     * @desc get src of file, return images url if that is images, return icon url if that is other type
     * @param string  $path: path to the file
     * @param $thumb: thumb folder if file is images
     * @return string
     */
    public function getSrcMedia($path, $thumb = 'thumb-150') {
        $arrFileExt = $this->arrFileExt;

        $icon_file = "fileicon";
        $pathinfo = pathinfo($path);
        $ext = $pathinfo['extension'];
        foreach ($arrFileExt as $icon => $arrExt) {
            if (in_array($ext, $arrExt)) {
                $icon_file = $icon;
            }
        }

        if ($icon_file == 'image') {
            switch ($thumb) {
                case "full":
                    return base_url() . $path;
                    break;

                default:
                    return getThumbnail($thumb, $path);
                    break;
            }
        } else {
            return base_url() . "/" . ADMIN_FOLDER . "/images/media/$icon_file.png";
        }
    }

    /**
     * @desc render html of file. Example: <img src="path/to/images.jpg"> file is the images or <video src="path/to/video.mp4"> if file is the video
     * @param string  $path: path to the file
     * @param $thumb: thumb folder if file is images
     * @return string
     */
    public function getMediaHtml($path, $thumb = 'thumb-150') {
        $arrFileExt = $this->arrFileExt;
        $other_info = $this->other_info;
        $html = '';

        $arrFileExt = $this->arrFileExt;

        $icon_file = "fileicon";
        $pathinfo = pathinfo($path);
        $ext = $pathinfo['extension'];
        foreach ($arrFileExt as $icon => $arrExt) {
            if (in_array($ext, $arrExt)) {
                $icon_file = $icon;
            }
        }

        if ($icon_file != '' && $icon_file != 'image') {
            $src = base_url() . $path;
            switch ($icon_file) {
                case "audio":
                    $html = '<span style="vertical-align:middle; display: table-cell; width: 100%;"><audio id="player1" class="media-audio" src="' . $src . '" type="audio/mp3" controls="controls"></audio></span>';
                    break;

                case "video":
                    $html = '<span style="vertical-align:middle; display: table-cell; width: 80%;"><video height="100%" width="100%" id="media-video" class="media-video" controls="controls" style="border: none;"><source src="' . $src . '" type="video/mp4"> </video></span>';
                    break;

                default:
                    $url = $this->getSrcMedia($path, 'full');
                    $html = '<span class="' . $icon_file . '" style="vertical-align:middle; display: table-cell; width: 100%; background: transparent url(' . $url . ') no-repeat center center;"></span>';
                    break;
            }
        } else {
            $url = $this->getSrcMedia($path, 'full');
            $html = '<span style="vertical-align:middle; display: table-cell; width: 100%; background: transparent url(' . $url . ') no-repeat center center; background-size: contain"></span>';
        }
        return $html;
    }

    /**
     * @desc the main method of the class
     * @return nothing
     */
    public function showList($ajax = false) {
        $html = '';

        if (isset($_POST['rop']) && (int) $_POST['rop'] > 0) {
            $_SESSION['media-info']['rop'] = (int) $_POST['rop'];
        }
        if (isset($_SESSION['media-info']['rop']) && $_SESSION['media-info']['rop'] > 0) {
            $this->rop = isset($_SESSION['media-info']) ? $_SESSION['media-info']['rop'] : 0;
        }

        if (isset($_POST['action']) && $_POST['action'] == 'del') {
            $this->deleteMedia();
        }

        global $oDb;
        $con = "";
        $q = Input::get('q', 'txt', '');
        // search query
        if ($q) {
            $this->q = $q;
            $con = " AND `path` LIKE '%$q%' ";
        }
        $search_type = Input::get('search-type', 'txt', 'all');
        if ($search_type != 'all') {
            switch ($search_type) {
                case "images":
                case "image":
                    $con .= " AND `type` LIKE 'image%' ";
                    break;

                default:
                    $con .= " AND `type` NOT LIKE 'image%' ";
                    break;
            }
        }

        $search_date = Input::get('search-date', 'txt', 'all');
        if ($search_date != 'all') {
            $con .= " AND `date` LIKE '%$search_date%'  ";
        }

        $strQuery = "SELECT * FROM $this->table WHERE 1 " . $con;

        $orderby = Input::get('orderby', 'txt', 'id');
        $orderby_dir = Input::get('orderby_dir', 'txt', 'DESC');

        $strQuery .= " ORDER BY `$orderby` $orderby_dir ";

        $page = isset($_REQUEST['current_page']) ? intval($_REQUEST['current_page']) : 0;

        if ($page == 0) {
            $page = isset($_SESSION["media-info"]['page']) ? (int) $_SESSION["media-info"]['page'] : 1;
        }
        $_SESSION["media-info"]['page'] = $page;

        $limit = ($page - 1) * $this->rop;

        $data = $oDb->query($strQuery . " limit " . (int) $limit . "," . $this->rop);

        $arrMedia = $oDb->fetchAll($data);
        $this->arrMedia = $arrMedia;

        $data_total = $oDb->query($strQuery);

        $totalRow = $oDb->numRows($data_total);

        $totalPage = (int) ($totalRow / $this->rop);
        if ($totalRow % $this->rop > 0) {
            $totalPage++;
        }
        $this->totalPage = $totalPage;
        $this->totalRow = $totalRow;

        if ($ajax == false) {
            $html .= $this->printHead();
            $html .= $this->showFormUpload();
            $html .= $this->showFormSearchGoogle();
        }

        switch ($this->view) {
            case "list":
                $html .= $this->listView($arrMedia);
                break;
            case "grid";
            default:
                $html .=$this->gridView($arrMedia);
                break;
        }
        $id = Input::get('id', 'int', '');
        if ($id) {
            $html .= $this->renderModel($id);
            $html .= '<script>
                    $(document).ready(function(){
                        if($().modal){
                            $("#modal-content-' . $id . '").modal();
                        }
                    })
                  </script>';
        }

        if ($ajax == false) {
            $html .= $this->printFooter();
        }

        return $html;
    }

    /**
     * @desc render html header of media in the admin
     * @return html
     */
    public function printHead() {
        $html = '';
        $this->is_fckeditor = false;
        if (isset($_REQUEST['CKEditor'])) {
            $this->is_fckeditor = true;
        }
        $html .= '<div class="table-stat">';
        $html .= '<span class="broad-title">' . mb_strtoupper($this->name, 'utf8') . ':</span>';
        $html .= trans('number_of_records') . ': <h1 class="broad-num">' . formatPrice($this->totalRow) . '</h1>';
        $html .= '<span style="float:right">' . trans('page') . ' <strong style="font-size:120%;">' . $_SESSION["media-info"]['page'] . '</strong> ' . trans('of_page') . ' <strong style="font-size:120%;">' . formatPrice($this->totalPage) . '</strong> ' . trans('page') . '</span>';
        $html .= '</div>';
        $html .= '<form name="frm" id="frm" action="" method="POST" class="media-frorm">';
        $html .= '<input type="hidden" name="listID" id="listID" value="" />';
        $html .= '<input type="hidden" name="listName" id="listName" value="Media" />';
        $html .= '<input type="hidden" name="action" id="action" value="view" />';
        $html .= '<div class="top_bar">';
        // not display if browse in FCKeditor 
        if (!$this->is_fckeditor) {
            if ($this->view == 'list') {
                $actived_grid = '';
                $actived_list = '-actived';
            } else {
                $actived_list = '';
                $actived_grid = '-actived';
            }
            $query_string = '&' . $_SERVER['QUERY_STRING'];
            $f = Input::get('f', 'txt', 'media');
            $query_string = removeQuerystringVar($query_string, 'f');
            $query_string = removeQuerystringVar($query_string, 'view');
            $html .= '<a href="?f=' . $f . '&view=list' . $query_string . '" class="icon-view icon-list' . $actived_list . '"></a>';
            $html .= '<a href="?f=' . $f . '&view=grid' . $query_string . '" class="icon-view icon-grid' . $actived_grid . '"></a>';
        }

        $html .= '<input type="text" name="q" value="' . $this->q . '" placeholder="' . trans('search') . '" style="width: 150px;" />
        <select name="search-type" onchange="submitForm(\'frm\')">' . $this->getOptionType() . '</select>
        <select name="search-date" onchange="submitForm(\'frm\')">' . $this->getOptionDateMedia() . '</select>
        <input type="submit" value="  ' . trans('search') . '  " />';
        $html .= "<div class='show_by'>" . trans('msg_row_perpage', array(mb_strtoupper($this->name, 'utf8'))) . ": <select name='rop' id='rop' onchange='set_rop();'>";

        $arrRop = array(8, 24, 48, 72, 160, 400, 800);
        foreach ($arrRop as $rop) {
            if ($rop == $this->rop) {
                $html .= "<option value='$rop' selected>$rop</option>";
            } else {
                $html .= "<option value='$rop'>$rop</option>";
            }
        }
        $html .= "</select></div>";

        $html .= '<div style="clear:both"></div>';
        $html .= '<div class="bottom_bar">';

        if (!$this->is_fckeditor) {
            if ($this->view == 'list') {
                $html .= '<input type="checkbox" id="checkAll" class="checkAll" />';
                if ((int) @$_SESSION['admin']['level'] >= 3) :
                    $html .= '<input type="button" id="btnDelete" class="btnDelete" value="' . trans('delete') . '" />';
                endif;
            }
            $html .= '<button id="btnAddModel" class="btnAddModel">' . trans('add') . '</button>';
            //$html .= '<button id="btnSearchGoogleImages" class="btnSearchGoogleImages">' . trans('search_from_google') . '</button>';
        }
        $html .= '<div class="pagination-right">' . $this->getPagination($this->totalPage) . '</div>';
        $html .= '</div>';
        $html .= '<div id="wrap-media-content">';
        return $html;
    }

    /**
     * @desc render html footer of media in the admin
     * @return html
     */
    public function printFooter() {
        $html = '</div>';
        $html .= "<input type='hidden' name='field' id='field' value=''>";
        $html .= "<input type='hidden' name='singleval' id='singleval' value=''>";
        $html .= "<input type='hidden' name='singleid' id='singleid' value=''>";
        $html .= "<input type='hidden' name='orderby' id='orderby' value='" . htmlspecialchars(isset($_POST['orderby']) ? $_POST['orderby'] : "") . "'>";
        $html .= "<input type='hidden' name='orderby_dir' id='orderby_dir' value='" . htmlspecialchars(isset($_POST['orderby_dir']) ? $_POST['orderby_dir'] : "") . "'>";
        $html .= "<input type='hidden' name='current_page' id='current_page' value='" . (isset($_POST['current_page']) ? $_POST['current_page'] : 1) . "'>";
        $html .= '<div class="bottom_bar">';
        if (!$this->is_fckeditor) {
            if ($this->view == 'list') {
                $html .= '<input type="checkbox" id="checkAll" class="checkAll" />';
                if ((int) @$_SESSION['admin']['level'] >= 3) :
                    $html .= '<input type="button" id="btnDelete" class="btnDelete" value="' . trans('delete') . '" />';
                endif;
            }
            $html .= '<button id="btnAddModel" class="btnAddModel">' . trans('add') . '</button>';
            //$html .= '<button id="btnSearchGoogleImages" class="btnSearchGoogleImages">' . trans('search_from_google') . '</button>';
        }
        $html .= "<div class='pagination-right'>" . $this->getPagination($this->totalPage) . "</div>";
        $html .= "</div>";
        $html .= "</form>";

        return $html;
    }

    /**
     * @desc render html footer of media in the admin
     * @param int $totalPage: total page  
     * @return html
     */
    public function getPagination($totalPage) {
        $output = "";
        if ($totalPage > 0) {
            $output = trans('page') . " <select name='pagination' id='pagination' onchange='change_page(this);'>";

            for ($i = 1; $i <= $totalPage; $i++) {
                if ((int) $_SESSION["media-info"]['page'] == $i) {
                    $select = "selected";
                } else {
                    $select = "";
                }
                $output .= "<option value='" . $i . "' " . $select . ">" . $i . "</option>";
            }
            $output .= "</select> / " . (int) $totalPage;
        }
        return $output;
    }

    /**
     * @desc render html of grid view
     * @param array $arrMedia: array of media 
     * @return html
     */
    function gridView($arrMedia) {

        $html = '';
        $html_model = '';

        $html .= '<div class="media-content-list">
                <div class="table-list">
                <div class="media-content-thumb">
                    <ul>';
        if (count($arrMedia) <= 0) {
            $html .= '<p style="text-align:center;">' . trans('file_not_found') . '</p>';
        } else {
            foreach ($arrMedia as $key => $value) {
                $media_info = json_decode($value['other_info'], true);
                $html_tooltip = '<p>'.trans('file_name').':&nbsp;&nbsp;&nbsp;<strong>' . $media_info['name'] . '</strong></p>';
                $html_tooltip .= '<p>'.trans('ext').':&nbsp;&nbsp;&nbsp;<strong>' . strtoupper($media_info['ext']) . '</strong></p>';
                $html_tooltip .= '<p>'.trans('file_size').':&nbsp;&nbsp;&nbsp;<strong>' . bytesToSize($media_info['size']) . '</strong></p>';
                if(isset($media_info['wh']) && $media_info['wh'] != ''){
                    $html_tooltip .= '<p>'.trans('size').':&nbsp;&nbsp;&nbsp;<strong>' . str_replace('x', ' x ', $media_info['wh']) . '</strong></p>';
                }
                $html_tooltip .= '<p>'.trans('uploaded').':&nbsp;&nbsp;&nbsp;<strong>' . $value['date'] . '</strong></p>';
                if (isset($media_info['desc']) && $media_info['desc'] != '') {
                    $html_tooltip .= '<p>'.trans('desc').':&nbsp;&nbsp;&nbsp;<strong>' . $media_info['desc'] . '</strong></p>';;
                }
                $html .= '<li rel="media-tooltip" title="' . $html_tooltip . '"  id="media-' . $value['id'] . '" onclick="setUrlParam(this)" data-id="' . $value['id'] . '" data-url="' . base_url() . $value['path'] . '" data-path="' . $value['path'] . '"><div class="media-thumbnail">
                            <img src="' . $this->getSrcMedia($value['path']) . '" /></div>
                          </li>';
                $html_model .= $this->renderModel($value['id']);
            }
        }

        $html .= '</ul></div>';
        $html .= '</div></div>';
        $html .= $html_model;

        return $html;
    }

    /**
     * @desc render html of list view
     * @param array $arrMedia: array of media 
     * @return html
     */
    function listView($arrMedia) {
        $html = '';
        if (isset($_POST['orderby']) && $_POST['orderby'] != "") {
            $order = $_POST['orderby'];
            $_SESSION["media-info"]['order'] = $order;
            if (isset($_POST['orderby_dir']) && $_POST['orderby_dir'] == "DESC") {
                $order_dir = " DESC";
                $_SESSION["media-info"]['order_dir'] = "DESC";
            } else {
                $order_dir = " ASC";
                $_SESSION["media-info"]['order_dir'] = "ASC";
            }
        } else {
            $order = isset($_SESSION["media-info"]['order']) ? $_SESSION["media-info"]['order'] : "";
            if ($order == "") {
                $order = $this->pk;
                $_SESSION["media-info"]['order'] = $order;
            }
            $order_dir = isset($_SESSION["media-info"]['order_dir']) ? $_SESSION["media-info"]['order_dir'] : "DESC";
            $_SESSION["media-info"]['order_dir'] = $order_dir;
        }

        foreach ($this->fields as $field) {
            $image[$field] = '';
        }

        if (($_SESSION["media-info"]['order'] == $order) && ($_SESSION["media-info"]['order_dir'] == "ASC")) {
            $dir = "DESC";
            $image[$order] = "<img align='absmiddle' src='images/arrow-up.gif' />";
        } else {
            if ($_SESSION["media-info"]['order'] == $order) {
                $image[$order] = "<img align='absmiddle' src='images/arrow-down.gif' />";
            } else {
                $image[$order] = '';
            }
            $dir = "ASC";
        }
        $html_model = '';
        $html .= '
        <div class="media-content-list">
        <div class="table-list">
            <table>
                <tbody>
                    <tr id="navigation">
                        <th class="check-cols">&nbsp;</th>
                        <th style="width: 80px;">&nbsp;</th>
                        <th><a href="javascript:void(0);">Media</a></th>
                        <!--<th><a href="javascript:order(\'object_id\',\'' . $dir . '\');">Object ID ' . $image['object_id'] . '</a></th>-->
                        <th style="width: 10%;"><a href="javascript:order(\'object_type\',\'' . $dir . '\')">' . trans('upload_to') . ' ' . $image['object_type'] . '</a></th>
                        <th style="width: 10%;"><a href="javascript:order(\'username\',\'' . $dir . '\')">' . trans('user_upload') . ' ' . $image['username'] . '</a></th>
                        <th style="width: 15%;"><a href="javascript:order(\'date\',\'' . $dir . '\')">' . trans('time') . ' ' . $image['date'] . '</a></th>
                    </tr>';
        if (count($arrMedia) <= 0) {
            $html .= '<tr><td align="center" colspan="100"><p style="text-align:center;">' . trans('file_not_found') . '</p></td></tr>';
        } else {
            foreach ($arrMedia as $key => $value) {
                $link_detail = curPageURL() . '&id=' . $value['id'];
                $media_info = json_decode($value['other_info'], true);
                if (isset($media_info['wh'])) {
                    $text_wh = ' / ' . str_replace('x', ' x ', $media_info['wh']);
                } else {
                    $text_wh = '';
                }
                $html .=
                        '<tr id="media-' . $value['id'] . '">
                                <td class="check-cols"><input type="checkbox" name="idItem" class="idItem" value="' . $value['id'] . '" /></td>
                                <td><a href="javascript:void(0)" onclick="showModel(' . $value['id'] . ')"><img src="' . $this->getSrcMedia($value['path']) . '" width="65" /></a></td>
                                <td class="media-file">
                                    <strong>' . $media_info['name'] . '</strong>
                                    <p><strong>(' . bytesToSize($media_info['size']) . ')</strong> / ' . strtoupper($media_info['ext']) . $text_wh . '</p>
                                    <p class="media-action">
                                    ';
                if ((int) @$_SESSION['admin']['level'] >= 3) :
                    $html .= '<span><a href="javascript:void(0)" onclick="deleteMedia(\'' . $value['id'] . '\')" style="color: red;">' . trans('remove_permanently') . '</a></span>
                                        <span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>';
                endif;
                $html .= '<span><a href="javascript:void(0)" onclick="showModel(' . $value['id'] . ')">' . trans('view') . '</a> </span>
                                    </p>
                                </td>
                                <!--<td>' . $value['object_id'] . '</td>-->
                                <td>' . $this->getObjectType($value['object_type']) . '</td>
                                <td>' . $value['username'] . '</td>
                                <td>' . date('H:i - d/m/Y', strtotime($value['date'])) . '</td>
                            </tr>';
                $html_model .= $this->renderModel($value['id']);
            }
        }
        $html .= '</tbody>
            </table>
        </div>     
    </div>';
        $html .= $html_model;

        return $html;
    }

    /**
     * @desc render html of grid view
     * @param int: id of modeld wanna render
     * @return html
     */
    function renderModel($id) {
        if ($id <= 0) {
            exit;
        }

        $this->read($id);
        $path = $this->path;
        $html_media = $this->getMediaHtml($path, 'full');
        $type = $this->type;
        $full_path = base_url() . $path;
        $other_info = json_decode($this->other_info, true);
        $date = $this->date;

        $img_next = '<img src="' . base_url() . '/' . ADMIN_FOLDER . '/images/media_icon_next.png' . '" height="20">';
        $img_prev = '<img src="' . base_url() . '/' . ADMIN_FOLDER . '/images/media_icon_prev.png' . '" height="20">';
        $img_close = '<img src="' . base_url() . '/' . ADMIN_FOLDER . '/images/media_icon_close.png' . '" height="20">';
        $img_loading = '<img id="img-loading-' . $id . '" src="' . base_url() . '/' . ADMIN_FOLDER . '/images/loading.gif" style="float:right; display:none" />';
        $prev_media = '<a href="javascript:void(0)" class="model-prev" title="' . trans('previous') . '">' . $img_prev . '</a>';
        $next_media = '<a href="javascript:void(0)" class="model-next" title="Sau">' . $img_next . '</a>';

        $arrMedia = $this->arrMedia;
        foreach ($arrMedia as $key => $value) {
            if ($value['id'] == $id) {
                break;
            }
        }

        if (count($arrMedia) > 1) {
            if ($key == 0) {
                $prev_media = '<button disabled="" class="model-prev disable" title="' . trans('previous') . '">' . $img_prev . '</button>';
                $next_media = '<button onclick="showModel(' . $arrMedia[$key + 1]['id'] . ')" class="model-next" title="' . trans('next') . '">' . $img_next . '</button>';

                $html_prev_modal = '<a href="#" class="arrow-prev-modal bg-none"></a>';
                $html_next_modal = '<a href="#" class="arrow-next-modal" onclick="showModel(' . $arrMedia[$key + 1]['id'] . ')"></a>';
            } elseif ($key == (count($arrMedia) - 1)) {
                $prev_media = '<button onclick="showModel(' . $arrMedia[$key - 1]['id'] . ')" class="model-prev" title="' . trans('previous') . '">' . $img_prev . '</button>';
                $next_media = '<button disabled="" class="model-next disable" title="Sau">' . $img_next . '</button>';

                $html_prev_modal = '<a href="#" class="arrow-prev-modal"  onclick="showModel(' . $arrMedia[$key - 1]['id'] . ')"></a>';
                $html_next_modal = '<a href="#" class="arrow-next-modal bg-none"></a>';
            } else {
                $prev_media = '<button onclick="showModel(' . $arrMedia[$key - 1]['id'] . ')" class="model-prev" title="' . trans('previous') . '">' . $img_prev . '</button>';
                $next_media = '<button onclick="showModel(' . $arrMedia[$key + 1]['id'] . ')" class="model-next" title="' . trans('next') . '">' . $img_next . '</button>';

                $html_prev_modal = '<a href="#" class="arrow-prev-modal"  onclick="showModel(' . $arrMedia[$key - 1]['id'] . ')"></a>';
                $html_next_modal = '<a href="#" class="arrow-next-modal" onclick="showModel(' . $arrMedia[$key + 1]['id'] . ')"></a>';
            }
        } else {
            $prev_media = '<button disabled="" class="model-prev disable" title="' . trans('previous') . '">' . $img_prev . '</button>';
            $next_media = '<button disabled="" class="model-next disable" title="' . trans('next') . '">' . $img_next . '</button>';

            $html_prev_modal = '<a href="#" class="arrow-prev-modal bg-none"></a>';
            $html_next_modal = '<a href="#" class="arrow-next-modal bg-none"></a>';
        }

        $html_wh = '';
        $html_alt = '';
        if (isset($other_info['wh']) && $other_info['wh'] != '') {
            $alt = isset($other_info['alt']) ? $other_info['alt'] : "";
            $html_wh = '<div class="media-solution"><strong>' . trans('size') . ': </strong>' . $other_info['wh'] . '</div>';
            $html_alt = '<div class="add-info">
                                <label>' . trans('alt_tag') . '</label>
                                <input type="text" name="alt" value="' . $alt . '" />
                            </div>';
        }
        $media_title = isset($other_info['title']) ? $other_info['title'] : "";
        $media_caption = isset($other_info['caption']) ? $other_info['caption'] : "";
        $media_desc = isset($other_info['desc']) ? $other_info['desc'] : "";
        $html = '
            <div class="wrap-media media-model-content" id="modal-content-' . $id . '">
            <div class="wrap-model">
                <div class="model-action">
                    <button class="model-close" id="model-close" title="' . trans('close') . '">' . $img_close . '</button>
                    ' . $next_media . $prev_media . '
                </div>
                <div class="model-title"><h1>' . trans('attachment_details') . '</h1></div>
                <div class="model-content">
                    <div class="left media-list media-content">
                    	<div style="width:100%; height: 100%; display: table; background: url(\'images/background-loading.gif\') no-repeat scroll center center; position: relative;">' . $html_prev_modal . $html_media . $html_next_modal . '</div>
                    </div>
                    <div class="right media-preview">
                        <h3>' . trans('detail') . '</h3>
                        <div class="media-detail">
                            <div class="info-detail">
                                <div class="media-name"><strong>' . trans('file_name') . ': </strong>' . $other_info['name'] . '</div>
                                <div class="media-solution"><strong>' . trans('file_type') . ': </strong>' . $type . '</div>
                                <div class="media-date"><strong>' . trans('date_upload') . ': </strong>' . date('H:i - d/m/Y', strtotime($date)) . '</div>
                                <div class="media-solution"><strong>' . trans('file_size') . ': </strong>' . bytesToSize($other_info['size']) . '</div>
                                ' . $html_wh;
        if ((int) @$_SESSION['admin']['level'] >= 3) :
            $html .= '<a href="#" onclick="deleteMedia(' . $id . ')" class="media-delete">' . trans('remove_permanently') . '</a>';
        endif;
        $html .= $img_loading . '
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="detail-form">
                        <input type="hidden" name="media_id" value="' . $id . '">
                            <div class="add-info">
                                <label>' . trans('path') . '</label>
                                <input type="text" name="path" value="' . $full_path . '" readonly="" style="color:#666" />
                            </div>
                            <div class="add-info">
                                <label>' . trans('title') . '</label>
                                <input type="text" name="title" value="' . $media_title . '" />
                            </div>
                            <div class="add-info">
                                <label>' . trans('caption') . '</label>
                                <textarea name="caption">' . $media_caption . '</textarea>
                            </div>
                            ' . $html_alt . '
                            <div class="add-info">
                                <label>' . trans('desc') . '</label>
                                <textarea name="desc">' . $media_desc . '</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        return $html;
    }

    /**
     * @desc show poup model form to upload media
     * @return html
     */
    function showFormUpload() {
        $img_close = '<img src="' . base_url() . '/' . ADMIN_FOLDER . '/images/media_icon_close.png' . '" height="20">';
        $html = '
            <div class="wrap-media media-model-content" id="modal-content-0">
                <div class="wrap-model">
                    <div class="model-action">
                        <button class="model-close" id="model-close" title="' . trans('close') . '">' . $img_close . '</button>
                    </div>
                    <div class="model-title"><h1>' . trans('add_file') . '</h1></div>
                    <div class="model-content">
                        <div class="media-upload-form">
                            <div id="drop"><a>' . trans('drag_drop_file') . '</a>
                				<input type="file" name="userfile" multiple />
                			</div>
                        </div>
                        <div style="clear: both;"></div>
                        <div class="content-model-upload">
                            <ul id="thumbnails"></ul>
                        </div>
                    </div>
                
                </div>
            </div>';

        return $html;
    }

    /**
     * @desc show poup model form to search google
     * @return html
     */
    function showFormSearchGoogle() {
        $img_close = '<img src="' . base_url() . '/' . ADMIN_FOLDER . '/images/media_icon_close.png' . '" height="20">';
        $html = '
            <div class="wrap-media media-model-content" id="modal-content-google">
                <div class="wrap-model">
                    <div class="model-action">
                        <button class="model-close" id="model-close" title="' . trans('close') . '">' . $img_close . '</button>
                    </div>
                    <div class="model-title"><h1>' . trans('search_from_google') . '</h1></div>
                    <div class="model-content">
                        <div class="search-google-input">
                           <div class="google-search-wrap">
                           <input type="text" name="keyword" id="search-google-input" placeholder="Nhập từ khóa tìm kiếm" />
                           <a href="javascript:void(0)" onclick="searchGoogleImages()" class="icon-search"></a>
                           </div>
                        </div>
                        <div style="clear: both;"></div>
                        <div class="content-model-upload">
                            <ul id="search-google-content">
                            </ul>
                        </div>
                    </div>
                    
                </div>
            </div>';

        return $html;
    }

    /**
     * @desc delete media by post listID
     * @return 
     */
    function deleteMedia() {

        if ((int) @$_SESSION['admin']['level'] < 3)
            return;

        $list_id = $_POST['listID'];
        if ($list_id) {
            $listID = explode(',', $list_id);
            foreach ($listID as $media_id) {
                if ($media_id <= 0)
                    continue;
                deleteMedia((int) $media_id);
            }
        }
    }

    /**
     * @desc get object type
     * @return 
     */
    function getObjectType($obj_type) {
        global $arrMenu;

        if ($obj_type == 'media') {
            return trans('unknow');
        }

        if (is_array($arrMenu) && count($arrMenu) > 0) {
            foreach ($arrMenu as $key => $value) {
                if ($value['id'] == $obj_type) {
                    return $value['name'];
                }
            }
        }

        return trans('unknow');
    }

}