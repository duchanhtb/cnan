<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 * @desc Core CMS admin
 */
/* 	
  Type:
  - input:text
  - input:text:readonly
  - input:image
  - input:file
  - input:multiimages
  - input:int10
  - input:price
  - input:attribute
  - input:password
  - input:function
  - input:hidden
  - textarea
  - textarea:noeditor
  - checkbox
  - combobox
  - combobox:relate:int
  - checkbox:relate:int
  - checkbox:relate:table  
  - datetime:current
  - map
  Attribute
  - title        // string title of field example: Danh mục
  - required     // true|false if the value is "true", that must be have value to submit form
  - editlink     // true|false if the value is "true" the content will have a link, click this link will show the edit form
  - searchable   // true|false if the value is "true", this field will be searchable
  - label        // using with type = checkbox, the label using to describe about checkbox when it checked
  - unlabel      // using with type = checkbox, the unlabel using to describe about checkbox when it unchecked
  - relate       // format is table.title.field
  - editable     // true|false if the value is "true", the value will editable on list rows
  - show_on_list // true|false if the value is "true", the value will display on list rows
  - separator    //
  - sufix_title  // string, the string will display after edit/add form to describe what is this
  - suffix_query // suffix_query using with relate above. Example: relate = t_category.name.id and suffix_query = " where id > 100 ". The sql_relate will be " SELECT * FROM t_category WHERE id > 100 "
  - rows         // int, using with type="textarea". The value will be the row of textarea
  - data         // array, using with type="combobox". Example: array(6=>"Clothing", 7=> "Shoe"), that's the same type=combobox:relate:int
  - function     // mixed. Example function => "getCategory", that will display the return value of the function. Thế function getCategory will explain below
  - table        // table relations for 'type = input:multiimages'
  - std          // the default value of column

  $action: list, add, edit, doAdd, doEdit
  $value: the value param
  getCategory($value, $action)
 */

class CmsTable extends Base {

    public $name = "Bản ghi";
    public $column = array(); // array field config
    public $idField = null; // primary key
    public $action = array(); // add, edit, addForm, editForm 
    public $showAddButton = true;
    public $rop = 50; // row per page
    public $totalPage = 0;
    public $totalRow = 0;
    public $mylevel = 1;
    public $info = "";
    public $showfooter = true;
    public $class_addbtn = 'btnNew';
    public $hasParrent = false;
    public $headBackground = "#009bdc";
    public $suffix_sql = '';

    /**
     * @desc constuct function
     * @param string $table: table of function
     * @param array $colum: array field config
     * @param $idField: primary key
     * @return nothing
     */
    public function __construct($table, $column, $idField) {
        global $oDb;
        $this->mylevel = (int) $_SESSION['admin']['level'];
        $this->pk = $idField;
        $this->fields = array();
        $this->key_prefix = $table . '_';


        $this->fields[] = $idField;
        foreach ($column as $k => $v) {
            if ($v['type'] == 'input:multiimages')
                continue;
            
            if($v['type'] == 'input:function' || $v['type'] == 'checkbox:relate:table'){
                continue;
            }
            
            $this->fields[] = $k;
            $this->$k = '';
        }

        $this->table = $table;
        $this->column = $column;
        $this->idField = $idField;
    }

    /**
     * @desc show list admin
     * @return HTML
     */
    public function showList() {
        global $oDb;
        $html = '';
        $list_field = '';
        foreach ($this->column as $field => $info) {
            if (isset($info['table']) && count($info['table']) > 0)
                continue;
            
            if($info['type'] == 'input:function' || $info['type'] == 'checkbox:relate:table'){
                continue;
            }
            $list_field .= "`$field`,";
        }
        $list_field .= " `$this->idField` ";
        $strQuery = "select $list_field from " . $this->table;
        if (isset($_REQUEST['search-input']) && $_REQUEST['search-input'] != "") {
            $keyword = htmlspecialchars(addslashes($_REQUEST['search-input']));
            $_SESSION['search-input'] = $keyword;
            if ($_REQUEST['search-column'] != "") {
                $column_attr = array();
                foreach ($this->column as $column_name => $attr) {
                    if ($column_name == $_REQUEST['search-column'])
                        $column_attr = $attr;
                }
                if ($column_attr != NULL && isset($column_attr['data'])) {
                    foreach ($column_attr['data'] as $key => $value) {
                        if (strtolower($keyword) == strtolower(trim($value, '-'))) {
                            $where = " where `" . $this->idField . "`='" . (int) $keyword . "' OR `" . $_REQUEST['search-column'] . "` = " . $key;
                            break;
                        } else {
                            $where = " where `" . $this->idField . "`='" . (int) $keyword . "' OR `" . $_REQUEST['search-column'] . "` like '%" . $keyword . "%' ";
                        }
                    }
                } if($column_attr != NULL && isset($column_attr['relate'])){
                    $dataRelate = $this->getIdFromRelateTable($column_attr['relate'], $keyword);
                    if(count($dataRelate) > 0 ){
                        $wh = '';
                        foreach($dataRelate as $dr){
                            $wh .= "OR `" . $_REQUEST['search-column'] . "` like '%" . $dr . "%' ";
                        }
                        if($wh){
                            $where = " where (".trim($wh,"OR").")";
                        }
                    }
                }else {
                    $where = " where `" . $this->idField . "`='" . (int) $keyword . "' OR `" . $_REQUEST['search-column'] . "` like '%" . $keyword . "%' ";
                }
            } else {
                $where = " where `" . $this->idField . "`='%" . (int) $keyword . "%' ";
            }
            $strQuery .= $where;
        }
        if ($this->suffix_sql) {
            $strQuery = (strpos($strQuery, 'where') > 0 ) ? $strQuery : ($strQuery . ' where 1 ');
            $strQuery .= $this->suffix_sql;
        }

        if (isset($_POST['orderby']) && $_POST['orderby'] != "") {
            $order = $_POST['orderby'];
            $_SESSION[$this->table . "-page"]['order'] = $order;
            if (isset($_POST['orderby_dir']) && $_POST['orderby_dir'] == "DESC") {
                $order_dir = " DESC";
                $_SESSION[$this->table . "-page"]['order_dir'] = "DESC";
            } else {
                $order_dir = " ASC";
                $_SESSION[$this->table . "-page"]['order_dir'] = "ASC";
            }
        } else if ($this->hasParrent == true) {
            $order = $this->hasParrent;
            $order_dir = "ASC";
            $_SESSION[$this->table . "-page"]['order'] = $order;
        } else {
            $order = isset($_SESSION[$this->table . "-page"]['order']) ? $_SESSION[$this->table . "-page"]['order'] : "";
            if ($order == "") {
                $order = $this->idField;
                $_SESSION[$this->table . "-page"]['order'] = $order;
            }
            $order_dir = isset($_SESSION[$this->table . "-page"]['order_dir']) ? $_SESSION[$this->table . "-page"]['order_dir'] : "DESC";
            $_SESSION[$this->table . "-page"]['order_dir'] = $order_dir;
        }

        $strQuery .= " order by `" . trim($order) . "` " . $order_dir;
        //tinh so trang
        $page = isset($_POST['current_page']) ? intval($_POST['current_page']) : 0;
        if ($page == 0) {
            $page = isset($_SESSION[$this->table . "-page"]['page']) ? (int) $_SESSION[$this->table . "-page"]['page'] : 1;
        }
        $_SESSION[$this->table . "-page"]['page'] = $page;

        $limit = ($page - 1) * $this->rop;

        $data = $oDb->query($strQuery . " limit " . (int) $limit . "," . $this->rop);
        $data_total = $oDb->query($strQuery);


        $totalRow = $oDb->numRows($data_total);

        $totalPage = (int) ($totalRow / $this->rop);
        if ($totalRow % $this->rop > 0) {
            $totalPage++;
        }
        $this->totalPage = $totalPage;
        $this->totalRow = $totalRow;

        $html .= $this->printHead();
        $html .= $this->printRow($data);

        if ($this->showfooter) {
            $html .= $this->printFooter();
        }

        return $html;
    }

    /**
     * @desc function header
     * @return HTML
     */
    public function printHead() {
        $html = '';
        $html.= '<span>' . $this->info . '</span>
    				<div class="table-stat">
                        <span class="broad-title">' . mb_strtoupper($this->name, 'utf8') . ':</span>                        
    					' . trans('number_of_records') . ': <h1 class="broad-num">' . $this->totalRow . '</h1>
                        <span style="float:right">' . trans('page') . ' <strong style="font-size:120%;">' . $_SESSION[$this->table . "-page"]['page'] . '</strong> ' . trans('of_page') . ' <strong style="font-size:120%;">' . $this->totalPage . '</strong> ' . trans('page') . '</span>
    				</div>';
        $html.= "<form name='frm' id='frm' action='index.php?f=" . htmlspecialchars($_GET['f']) . "' method='post'>";
        $html.= "<input type='hidden' name='listID' id='listID' value='0' />";
        $html.= "<input type='hidden' name='listName' id='listName' value='" . $this->name . "' />";
        $html.= "<input type='hidden' name='action' id='action' value='view' />";
        $html.= "<div class='top_bar'>";
        $search_option = '';
        foreach ($this->column as $key => $value) {
            $selected = '';
            if (isset($_REQUEST['search-column']) && $_REQUEST['search-column'] == $key) {
                $selected = 'selected=""';
            }
            if (isset($value['searchable']) && $value['searchable'] == true) {
                $search_option .= "<option value='" . $key . "' " . $selected . ">" . strip_tags($value['title']) . "</option>";
            }
        }
        if ($search_option != '') {
            $html.= "<input type='text' size='30' name='search-input' value='" . htmlspecialchars(isset($_REQUEST['search-input']) ? $_REQUEST['search-input'] : "") . "' />";
            $html.= "<select name='search-column'>$search_option</select><input type='submit' value='  " . trans('search') . "  ' style='margin-left:10px'>";
        }
        $html.= "<div class='show_by'>" . trans('msg_row_perpage', array(mb_strtoupper($this->name, 'utf8'))) . ":<select name='rop' id='rop' onchange='set_rop();'>";

        $arrRop = array(5, 10, 25, 50, 100, 250, 500, 1000);
        foreach ($arrRop as $rop) {
            if ($rop == $this->rop) {
                $html.= "<option value='$rop' selected>$rop</option>";
            } else {
                $html.= "<option value='$rop'>$rop</option>";
            }
        }
        $html.= "</select></div>";
        $html.= '<div style="clear:both"></div>';
        $html.= '<div class="bottom_bar">';
        if ($this->mylevel > 1) {
            $html.= '<input type="checkbox" id="checkAll" class="checkAll"> 
                  <input type="button" id="btnDelete" class="btnDelete" value="' . trans('delete') . '" />';
            if ($this->showAddButton) {
                $html.= '<button id="btnNew" class="' . $this->class_addbtn . '">' . trans('add') . '</button>';
            }
        }
        $html.= "<div class='pagination-right'>" . $this->getPagination($this->totalPage) . "</div>";
        $html.= "</div>";

        $html.= "</div>";

        // table
        $html.= '<div class="table-list">';

        $html.= "<table><tr id=\"navigation\"><th class='check-cols'>&nbsp;</th>";

        if (($_SESSION[$this->table . "-page"]['order'] == $this->idField) && ($_SESSION[$this->table . "-page"]['order_dir'] == "ASC")) {
            $dir = "DESC";
            $image = "<img align='absmiddle' src='images/arrow-up.gif' />";
        } else {
            if ($_SESSION[$this->table . "-page"]['order'] == $this->idField) {
                $image = "<img align='absmiddle' src='images/arrow-down.gif' />";
            } else {
                $image = '';
            }
            $dir = "ASC";
        }

        foreach ($this->column as $key => $value) {
            $image = "";
            if (isset($value['show_on_list']) && $value['show_on_list'] == true) {
                if (($_SESSION[$this->table . "-page"]['order'] == $key) && (isset($_SESSION[$this->table . "-page"]['order_dir']) && $_SESSION[$this->table . "-page"]['order_dir'] == "ASC")) {
                    $dir = "DESC";
                    $image = "<img align='absmiddle' src='images/arrow-up.gif' />";
                } else {
                    if ($_SESSION[$this->table . "-page"]['order'] == $key) {
                        $image = "<img align='absmiddle' src='images/arrow-down.gif' />";
                        $dir = "ASC";
                    } else {
                        $dir = "DESC";
                    }
                }
                if (isset($value['editable']) && ($value['editable'] == true) && ($value['type'] != "checkbox") && ($this->mylevel > 1)) {
                    $save_icon = '&nbsp; &nbsp;<a href="javascript:save();" class="save-field" rel="' . $key . '"><img title="' . trans('update') . ' ' . $value['title'] . '" src="images/save_icon.gif" align="absmiddle" />';
                } else {
                    $save_icon = "";
                }
                $html.= "<th><a href='javascript:order(\"" . $key . "\",\"" . $dir . "\");'>" . $value['title'] . "</b> " . $image . "</a>" . $save_icon . "</th>";
            }
        }
        $html.= "</tr>";

        return $html;
    }

    /**
     * @desc function footer html
     * @return HTML
     */
    public function printFooter() {
        $html = '';
        $html.= "</table></div>";
        $html.= "<input type='hidden' name='field' id='field' value=''>";
        $html.= "<input type='hidden' name='singleval' id='singleval' value=''>";
        $html.= "<input type='hidden' name='singleid' id='singleid' value=''>";
        $html.= "<input type='hidden' name='orderby' id='orderby' value='" . htmlspecialchars(isset($_POST['orderby']) ? $_POST['orderby'] : "") . "'>";
        $html.= "<input type='hidden' name='orderby_dir' id='orderby_dir' value='" . htmlspecialchars(isset($_POST['orderby_dir']) ? $_POST['orderby_dir'] : "") . "'>";
        $html.= "<input type='hidden' name='current_page' id='current_page' value='" . (isset($_POST['current_page']) ? $_POST['current_page'] : 1) . "'>";
        $html.= '<div class="bottom_bar">';
        if ($this->mylevel > 1) {
            $html.= '<input type="checkbox" id="checkAll" class="checkAll"> <input type="button" id="btnDelete" class="btnDelete" value="' . trans('delete') . '" />';
            if ($this->showAddButton) {
                $html.= ' <button id="btnNew" class="' . $this->class_addbtn . '">' . trans('add') . '</button>';
            }
        }
        $html.= "<div class='pagination-right'>" . $this->getPagination($this->totalPage) . "</div>";
        $html.= "</div>";
        $html.= "</form>";

        return $html;
    }

    /**
     * @desc pagination function
     * @param int $totalPage: total page number
     * @return html
     */
    public function getPagination($totalPage) {
        $output = "";
        if ($totalPage > 0) {
            $output = trans('page') . " <select name='pagination' id='pagination' onchange='change_page(this);'>";
            for ($i = 1; $i <= $totalPage; $i++) {
                if ((int) $_SESSION[$this->table . "-page"]['page'] == $i) {
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
     * @desc print row data, list content included many rows content
     * @param array $data: array data
     * @return HTML html rows content
     */
    public function printRow($data) {
        $html = '';
        $f = Input::get('f', 'txt', '');
        global $oDb;
        if (@$oDb->numRows($data) > 0) {
            $count = 0;
            $stickytooltip = '';
            while ($item = $oDb->fetchArray($data)) {
                $count++;
                if ($count % 2 == 0) {
                    $html.= "<tr>";
                } else {
                    $html.= "<tr>";
                }
                if ($this->mylevel > 1) {
                    $html.= "<td class='check-cols'><input type='checkbox' name='idItem' class='idItem' value='" . $item[$this->idField] . "' /></td>";
                } else {
                    $html.= "<td class='check-cols'></td>";
                }


                foreach ($this->column as $key => $value) {
                    if (isset($value['show_on_list']) && $value['show_on_list'] == true) {
                        if (isset($value['relate']) && $value['relate'] != "") {
                            
                            switch($value['type']){
                                case "checkbox:relate:table":
                                    $relate = explode(":", $value['relate']);
                                    $relateTable = $relate[0];
                                    $relateField1 = explode("=", $relate[1]);
                                    $relateField2 = explode("=", $relate[2]);
                                    $field1 = $relateField1[0];
                                    $field2 = $relateField2[0];
                                    $table1 = explode(".", $relateField1[1]);
                                    $table2 = explode(".", $relateField2[1]);
                                    
                                    $relate_query = "SELECT * FROM $relateTable WHERE `$field1` = ".$item[$this->idField];
                                    $relate_rc = $oDb->query($relate_query);
                                    $relate_arr = $oDb->fetchAll($relate_rc);
                                    $list_id = '';
                                    foreach($relate_arr as $k=>$v){
                                        $list_id .= $v[$field2].',';
                                    }
                                    $list_id = trim($list_id,',');
                                    if ($list_id) {
                                        $relate_rs = $oDb->query("select * from " . $table2[0] . " where " . $table2[1] . " IN ($list_id)");
                                        if ($oDb->numRows($relate_rs) == 0) {
                                            $title = trans('unknow');
                                        } else {
                                            
                                            $arrRelate = $oDb->fetchAll($relate_rs);
                                            $title = '';
                                            foreach ($arrRelate as $relate) {
                                                $title .= '<p>&#8226; ' . htmlspecialchars(stripslashes($relate[$table2[2]])) . '</p>';
                                            }
                                            $title = trim($title, ',&nbsp;');
                                        }
                                    } else {
                                        $title = trans('unknow');
                                    }
                                    
                                    break;
                                
                                default :
                                    $relate = explode(".", $value['relate']);
                                    $relateTable = $relate[0];
                                    $relateTitle = $relate[1];
                                    $relateField = $relate[2];
                                    $list_id = str_replace('::', ',', trim($item[$key], ':'));
                                    if ($list_id) {
                                        $relate_rs = $oDb->query("select * from " . $relateTable . " where " . $relateField . " IN ($list_id)");
                                        if ($oDb->numRows($relate_rs) == 0) {
                                            $title = trans('unknow');
                                        } else {
                                            $arrRelate = $oDb->fetchAll($relate_rs);
                                            $title = '';
                                            foreach ($arrRelate as $relate) {
                                                $title .= '<p>&#8226; ' . htmlspecialchars(stripslashes($relate[$relateTitle])) . '</p>';
                                            }
                                            $title = trim($title, ',&nbsp;');
                                        }
                                    } else {
                                        $title = trans('unknow');
                                    }
                                    break;
                            }
                        } else {
                            switch ($value['type']) {
                                case "checkbox":
                                    if ((int) $item[$key] == 1) {
                                        if (($value['editable'] == true) && ($this->mylevel > 1)) {
                                            $title = "<a href='javascript:switchval(\"" . $key . "\",\"" . $item[$this->idField] . "\",0);' id='$key-" . $item[$this->idField] . "' class='active-link'><img src='images/check.png' title='" . htmlspecialchars($value['label']) . "'></a>";
                                        } else {
                                            $title = '<img src="images/check.png" title="' . htmlspecialchars($value['label']) . '">';
                                        }
                                    } else {
                                        if (($value['editable'] == true) && ($this->mylevel > 1)) {
                                            $title = "<a href='javascript:switchval(\"" . $key . "\",\"" . $item[$this->idField] . "\",1);' id='$key-" . $item[$this->idField] . "' class='unactive-link'><img src='images/uncheck.png' title='" . htmlspecialchars($value['unlabel']) . "'></a>";
                                        } else {
                                            $title = '<img src="images/uncheck.png" title="' . htmlspecialchars($value['unlabel']) . '">';
                                        }
                                    }
                                    break;

                                case "combobox":
                                    $title = '---';
                                    $tmp = '';
                                    foreach ($value['data'] as $k => $v) {
                                        if ($item[$key] == $k) {
                                            $tmp = $v;
                                        }
                                    }
                                    if (isset($value['editlink']) && ($value['editlink'] == true) && ($this->mylevel > 1)) {
                                        $title = '<a href="javascript:edit(\'' . $item[$this->idField] . '\');">' . stripslashes(strip_tags($tmp)) . "</a>";
                                    } else {
                                        $title = stripslashes(strip_tags($tmp));
                                    }
                                    break;

                                case "input:function":
                                    $function = $value['function'];
                                    if (!function_exists($function)) {
                                        $title = "Function <strong>$function</strong> does not exists";
                                    } else if (isset($item[$key]) && $item[$key] != '') {
                                        $title = $function($item[$key], 'list');
                                    } else {
                                        $title = $function($item[$this->idField], 'list');
                                    }

                                    break;

                                case "input:image":
                                    $url = ($item[$key] != "") ? $item[$key] : base_url() . ADMIN_FOLDER . "/images/noimage.jpg";
                                    if (@file_exists('..' . $url)) {
                                        $title = "<a href='#' data-tooltip=\"sticky_" . $item[$this->idField] . $key . "\"><img src=" . getThumbnail('thumb-150', $url) . " class='image_review' width='100' /></a>";
                                        $stickytooltip .= "<div id=\"sticky_" . $item[$this->idField] . $key . "\" class=\"atip\"><img src=" . getThumbnail('origin', $url) . " class=\"sticky_images\" /></div>";
                                    } else if (strpos('http', $url) === false) {
                                        $title = "<a href='#' data-tooltip=\"sticky_" . $item[$this->idField] . $key . "\"><img src=" . getThumbnail('thumb-150', $url) . " class='image_review' width='100' /></a>";
                                    } else {
                                        $title = "<a href='#' data-tooltip=\"sticky_" . $item[$this->idField] . $key . "\"><img src=" . base_url(). $url . " class='image_review' width='100' /></a>";
                                    }
                                    break;

                                case "input:file":
                                    if (isset($item[$key]) && $item[$key] != '') {
                                        $title = getFilename($item[$key]);
                                    } else {
                                        $title = 'not found';
                                    }

                                    break;


                                case "input:multiimages":
                                    $title = '';
                                    $arrImage = getImagesTable($value['table'], $item[$this->idField]);
                                    if (count($arrImage) == 0) {
                                        $url = ADMIN_FOLDER . "/images/noimage.jpg";
                                    } else {
                                        foreach ($arrImage as $k => $v) {
                                            $url = $v[$value['table']['images_url']];
                                            if (file_exists('..' . $url)) {
                                                $stickytooltip .= "<div id=\"sticky_" . $v[$this->idField] . "\" class=\"atip\"><img src=" . base_url() . $url . " class=\"sticky_images\" /></div>";
                                            }
                                            $small = getThumbnail('thumb-150', $url);
                                            $img_delete = '<div class="rwmb-image-bar"><a href="javascript:voiid(0)" class="rwmb-delete-file" onclick="deleteFileUpload(' . $v[$this->idField] . ',\'' . $f . '\')">×</a></div>';
                                            $title .= "<div class='wrap_images' id='img_" . $v[$this->idField] . "' ><a href='#' data-tooltip=\"sticky_" . $v[$this->idField] . "\"><img src=" . $small . " class='image_review' width='50' />" . $img_delete . "</div></div>";
                                        }
                                    }
                                    break;

                                case "input:price":
                                    if (isset($value['editable']) && ($value['editable'] == true) && ($this->mylevel > 1)) {
                                        $class = "";
                                        $size = 100;
                                        $width = "width:80px;";
                                        $title = '<input type="text" name="' . $key . '[]" style="' . $width . '" class="' . $key . ' ' . $class . '"  maxlength="' . $size . '"  value="' . price($item[$key]) . '" /> VNĐ';
                                    } else {
                                        if (isset($value['editlink']) && ($value['editlink'] == true) && ($this->mylevel > 1)) {
                                            $title = '<a href="javascript:edit(\'' . $item[$this->idField] . '\');">' . price($item[$key]) . "</a>";
                                        } else {
                                            $title = stripslashes(strip_tags($item[$key]));
                                        }
                                    }
                                    break;


                                case "input:attribute":
                                    $attribute = unserialize($item[$key]);
                                    $title = '';
                                    if ($attribute && count($attribute) > 0) {
                                        foreach ($attribute as $att_value) {
                                            $title .= '<div>' . $att_value['name'] . ': ' . $att_value['value'] . '</div>';
                                        }
                                    }
                                    break;

                                case "textarea:noeditor":
                                    $class = isset($class) ? $class : '';
                                    if (isset($value['editable']) && ($value['editable'] == true) && ($this->mylevel > 1)) {
                                        $title = '<textarea name="' . $key . '[]" ' . $class . ' style="resize:vertical; ">' . stripslashes(htmlspecialchars(trim($item[$key]))) . '</textarea>';
                                    } else if(isset($value['editlink']) && ($value['editlink'] == true) && ($this->mylevel > 1)){
                                        $title = '<a href="javascript:edit(\'' . $item[$this->idField] . '\');">' . stripslashes(strip_tags($item[$key])) . "</a>";
                                    }else{
                                        $title = stripslashes(strip_tags($item[$key]));
                                    }
                                    break;


                                default:
                                    if (isset($value['editable']) && ($value['editable'] == true) && ($this->mylevel > 1)) {
                                        if ($value['type'] == "input:int10") {
                                            $size = 10;
                                            $width = "width:80px;";
                                            $class = "input-number";
                                            $item[$key] = price($item[$key]);
                                        } else {
                                            $class = "";
                                            $size = 255;
                                            $width = "width:200px;";
                                        }
                                        $title = '<input type="text" name="' . $key . '[]" class="' . $key . ' ' . $class . '" style="' . $width . '" maxlength="' . $size . '" value="' . stripslashes(htmlspecialchars(trim($item[$key]))) . '" />';
                                    } else {
                                        if (isset($value['editlink']) && ($value['editlink'] == true) && ($this->mylevel > 1)) {
                                            $title = '<a href="javascript:edit(\'' . $item[$this->idField] . '\');">' . stripslashes(strip_tags($item[$key])) . "</a>";
                                        } else {
                                            $title = stripslashes(strip_tags($item[$key]));
                                        }
                                    }
                                    break;
                            }
                        }
                        $html.= "<td>" . $title . "</td>";
                    }
                }

                $html.= "</tr>";
            }
            $html.= '<tr><td><div id="mystickytooltip" class="stickytooltip">' . $stickytooltip . '</div></td></tr>';
        } else {
            $html.= "<tr><td colspan='100' align='center'><p>" . trans('msg_nodata', array($this->name)) . "</p></td></tr>";
        }

        return $html;
    }

    /**
     * @desc constuct function
     * @param int $id: get row by id
     * @return array
     */
    public function getRow($id) {
        global $oDb;
        $list_field = '';
        foreach ($this->column as $field => $info) {
            if (isset($info['table']) && count($info['table']) > 0)
                continue;
            
            if($info['type'] == 'input:function' || $info['type'] == 'checkbox:relate:table'){
                continue;
            }
            
            $list_field .= "`$field`,";
        }
        $list_field .= " `$this->idField` ";
        $strSql = "SELECT $list_field FROM " . $this->table . " where `" . $this->idField . "` IN('" . $id . "')";
        $rs = $oDb->query($strSql) or die("Lỗi MySql: " . $strSql);
        return $rs;
    }

    /**
     * @desc controler function, this function will call other functions base on $_POST['action']
     * @return nothing
     */
    public function eventHander() {        
        global $oDb;
        $html = '';
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        switch ($action) {
            case "del":
                //array_map("addSlash", $_POST['listID']);
                $_POST['listID'] = $this->addSlashToListKey($_POST['listID']);
                $strSql = "delete from " . $this->table . " where `" . $this->idField . "` in (" . $_POST['listID'] . ")";
                $oDb->query($strSql) or die("Lỗi MySql: " . $strSql);
                
                $_SESSION[$this->table . "-page"]['page'] = 1;
                $html.= "<script>window.location.href='index.php?f=" . htmlspecialchars($_GET['f']) . "';</script>";
                break;
            case "new":
                $html.= $this->viewAddForm();
                break;
            case "cnew":
                $html.= $this->doAdd();
                break;

            case "edit":
                $html.= $this->viewEditForm();
                break;

            case "cedit":
                $html.= $this->doEdit();
                break;

            case "save":
                $html.= $this->saveField();
                break;

            case "switchval":
                $html.= $this->switchval();
                break;

            case "set_rop":
                $_SESSION[$this->table . "-page"]['page'] = 1;
                $html.= $this->view();
                break;

            case "order":
                $_SESSION[$this->table . "-page"]['page'] = 1;
                $html.= $this->view();
                break;

            default:
                $html.= $this->view();
                break;
        }
        return $html;
    }

    /**
     * @desc view function
     * @return nothing
     */
    function view() {
        if (isset($_POST['rop']) && (int) $_POST['rop'] > 0) {
            $_SESSION[$this->table]['rop'] = (int) $_POST['rop'];
        }
        if (isset($_SESSION[$this->table]) && $_SESSION[$this->table]['rop'] > 0) {
            $this->rop = isset($_SESSION[$this->table]) ? $_SESSION[$this->table]['rop'] : 0;
        }
        return $this->showList();
    }

    /**
     * @desc this function will render html add form
     * @param string $msg: the message
     * @return HTML
     */
    public function viewAddForm($msg = null) {
        global $oDb;
        admin_register_script('jquery-maskedinput', admin_url() . 'js/jquery.maskedinput-1.0.js', false, true);
        admin_register_script('jquery-metadata', admin_url() . 'js/jquery.metadata.js', false, true);
        admin_register_script('jquery-validate', admin_url() . 'js/jquery.validate.js', false, true);
        admin_register_script('jquery-alphanumeric', admin_url() . 'js/jquery.alphanumeric.js', false, true);


        $html = '<h2 class="broad-title">' . $this->name . '</h2>';
        if ($msg != null) {
            $html.= $msg;
        }
        $html.= '<span>' . $this->info . '</span>';
        $html.= '<div class="add-bar"><span>' . trans('add') . ' ' . $this->name . '</span></div>';
        $html.= "<form name='frm' id='frm' action='index.php?f=" . htmlspecialchars($_GET['f']) . "' method='post'>";
        $html.= "<input type='hidden' name='action' value='cnew' />";
        $html.= "<div class='table-list table-form'><table>";
        foreach ($this->column as $key => $value) {
            if (isset($value['separator']) && $value['separator'] != "") {
                $html.= "<tr><td colspan=2><div class='add-bar'><span>" . $value['separator'] . "</span></div></td></tr>";
            }
            
            $hidden_row = '';
            if($value['type'] == 'input:hidden'){
                $hidden_row = 'style="display:none"';
            }
            $html.= "<tr ".$hidden_row.">";
            $html.= "<td width='15%'>" . $value['title'] . "</td>";

            //validate
            if (isset($value['required']) && $value['required'] != "") {
                $class = "{required:true,messages:{required:'" . addslashes($value['required']) . "'}}";
            } else {
                $class = "";
            }

            $default_val = isset($value['std']) ? htmlspecialchars($value['std']) : "";
            switch ($value['type']) {
                case "input:text":
                    $input = '<input type="text" name="' . $key . '" class="' . $class . '" value="' . $default_val . '" />';
                    break;

                case "input:function":
                    $function = $value['function'];
                    if (function_exists($function)) {
                        $input = '<input type="hidden" name="' . $key . '" value="' . $default_val . '" />';
                        $input .= $function($default_val, 'add');
                    } else {
                        $input = '<input type="text" name="' . $key . '" class="' . $class . '" value="' . $default_val . '" />';
                    }
                    break;

                case "input:text:readonly":
                    $input = '<input type="text" name="' . $key . '" class="' . $class . '" value="' . $default_val . '" />';
                    break;
                case "input:password":
                    $input = '<input type="password" name="' . $key . '" class="' . $class . '" />';
                    break;
                case "input:image":
                    admin_register_script('ajax-upload', admin_url() . 'js/ajaxupload.js', false, true);
                    if($default_val == ''){
                        $img_default = 'images/noimage.jpg';
                    }
                    
                    $f = Input::get('f', 'txt', 'media');
                    $input = '<input type="text" value="'.$default_val.'" name="' . $key . '" class="' . $class . ' input-image" id="' . $key . '" /> 
                                    [<a href="#" class="' . $key . ' btnUpload" id="btnUpload' . $key . '">' . trans('select_images') . '</a>]
                                    <span>' . trans('or') . '</span>
                                    [<a href="javascript:void(0)" onclick="popitup(\'browser.php?view=grid&search-type=image&f=' . $f . '&inputId=' . $key . '\')" class="' . $key . '" id="btnUpload' . $key . '">' . trans('select_from_library') . '</a>]
                                    <br/>
                             <div class="wrap-images">
                                <img class="image_review" src="'.$img_default.'" width="100" />
                                <div class="rwmb-image-bar"><a onclick="deleteDefaultImages(this)" class="rwmb-delete-file" href="javascript:void(0)">×</a></div>
                             </div>
                             <br/>
                             <span class="lblUpload" id="lblUpload' . $key . '"></span>';
                    break;

                case "input:file":
                    $input = '<input type="text" name="' . $key . '" class="' . $class . ' input-file" /> [<a href="#" class="browse ' . $key . '" id="btnUpload' . $key . '">' . trans('select_file') . '</a>]&nbsp;&nbsp;(<a href="upload-file.php?ext=1" target="_blank" title="' . trans('msg_allow_filetype') . '">?</a>)<br/><em class="file"></em>';
                    break;

                case "input:multiimages":
                    admin_register_script('jquery-widget', admin_url() . 'js/jquery.ui.widget.js');
                    admin_register_script('jquery-fileupload', admin_url() . 'js/jquery.fileupload.js');

                    unset($_SESSION['multiimages'][$key]);
                    $input = '<ul id="thumbnails">
                                <li>
                                    <div id="drop"><a>' . trans('select_images') . '</a>
                        				<input type="file" name="userfile" multiple />
                        			</div>
                                </li>   
                             </ul>
                             <div style="clear: both;"></div>';
                    break;

                case "input:price":
                    $input = '<input type="hidden" id="' . $key . '" name="' . $key . '"  value="' . $default_val . '" >
                              <input type="text" name="' . $key . '_view" valto="' . $key . '" style="width:150px;" class="' . $class . ' price-input" value="0" /> VNĐ';
                    break;

                case "input:attribute":
                    $input = '<div class="wrap-attribute">
                                  <div class="attribute">
                                    <div class="rwmb-image-bar"><a onclick="deleteAttribute(this)" class="rwmb-delete-file" href="javascript:void(0)">×</a></div>
                                    <div class="attr-name">    
                                        <label>' . trans('name') . ':</label>
                                        <input type="text" name="' . $key . '_name[]" value="" placeholder="' . trans('placeholder_att_name') . '" />
                                    </div>
                                    <div class="attr-value">
                                        <label>' . trans('value') . ':</label>
                                        <textarea name="' . $key . '_value[]" placeholder="' . trans('placeholder_att_value') . '"></textarea>
                                    </div>
                                    <div class="clear"></div>
                                  </div>
                            </div>
                            <button type="button" class="attr-button" onclick="addAttribute()">' . trans('add') . '</button>
                            <div class="clear"></div>';
                    break;

                case "input:int10":
                    $input = '<input type="text" name="' . $key . '" style="width:100px;"  value="' . $default_val . '" class="' . $class . ' input-number" maxlength="10" />';
                    break;

                case "textarea":
                    admin_register_script('ckeditor', admin_url() . 'editor/ckeditor/ckeditor.js');
                    admin_register_script('ckfinder', admin_url() . 'editor/ckfinder/ckfinder.js');
                    
                    if (!isset($value['row']) || (int) $value['rows'] == 0) {
                        $rows = 10;
                    } else {
                        $rows = (int) $value['row'];
                    }
                    
                    $input = '<textarea name="' . $key . '" id="' . $key . '" rows="' . $rows . '" class="' . $class . ' txa" cols="60">' . $default_val . '</textarea>';
                    $input .= '
                    <script type="text/javascript">
                        //<![CDATA[
                    	var editor = CKEDITOR.replace("' . $key . '");
                         CKFinder.SetupCKEditor( editor, { 
                                BasePath : "editor/ckfinder/", 
                                RememberLastFolder : false
                         }) ;
                        //]]>                                
                    </script>';
                    break;

                case "textarea:noeditor":
                    $rows = isset($value['row']) ? (int) $value['row'] : "8";
                    $input = '<textarea name="' . $key . '" id="' . $key . '" rows="' . $rows . '" class="' . $class . ' txa" cols="60" style="resize: vertical;">' . $default_val . '</textarea>';
                    break;

                case "checkbox":
                    $checked = "checked=''"; // default is checked
                    $input = '<input type="checkbox" name="' . $key . '" value="1" class="' . $class . '" ' . $checked . '  /> ' . strip_tags($value['label']);
                    break;

                case "combobox:relate:int":
                    if ($value['required'] != "") {
                        $class = "{min:1,messages:{min:'" . addslashes($value['required']) . "'}}";
                    } else {
                        $class = "";
                    }
                    $input = '<select name="' . $key . '" class="' . $class . '" >';
                    $input .= $value['add_option'];
                    $relate = explode(".", $value['relate']);
                    $relateTable = $relate[0]; // table related
                    $relateTitle = $relate[1]; // title related
                    $relateField = $relate[2]; // field related
                    $suffix_query = isset($value['suffix_query']) ? $value['suffix_query'] : "";
                    $strSql = "select * from " . $relateTable . " " . $suffix_query;
                    $relate_rs = $oDb->query($strSql) or die("Lỗi MySql: " . $strSql);
                    while ($relate = $oDb->fetchArray($relate_rs)) {
                        $input .= '<option value="' . $relate[$relateField] . '">' . htmlspecialchars(stripslashes($relate[$relateTitle])) . '</option>';
                    }
                    $input .= "</select>";
                    break;
                
                
                case "combobox":
                    if (isset($value['required']) && $value['required'] != "") {
                        $class = "{min:1,messages:{min:'" . addslashes($value['required']) . "'}}";
                    } else {
                        $class = "";
                    }
                    $input = '<select name="' . $key . '" class="' . $class . '" >';
                    foreach ($value['data'] as $k => $v) {
                        $input .= '<option value="' . $k . '">' . htmlspecialchars(stripslashes($v)) . '</option>';
                    }
                    $input .= "</select>";
                    break;

                case "checkbox:relate:int":
                    $input = "";
                    $relate = explode(".", $value['relate']);
                    $relateTable = $relate[0]; // table related
                    $relateTitle = $relate[1]; // title related, example name of category
                    $relateField = $relate[2]; // field related, example id of category
                    $suffix_query = isset($value['suffix_query']) ? $value['suffix_query'] : "";
                    $strSql = "SELECT $relateField, $relateTitle FROM " . $relateTable . " " . $suffix_query;
                    $relate_rs = $oDb->query($strSql) or die("Lỗi MySql: " . $strSql);
                    $relateData = $oDb->fetchAll($relate_rs);
                    $num_col = $this->getCheckboxCol(count($relateData));
                    for($i = 0; $i < $num_col ; $i++){
                        $withPercent = floor(100/$num_col);
                        $input .= '<div class="inline-block v-alin-top" style="width: '.$withPercent.'%">';
                        
                        $recordBreak = ceil(count($relateData)/$num_col);
                        for($n = $i*$recordBreak; $n < ($i+1)* $recordBreak ; $n++){
                            if(isset($relateData[$n])){
                                $relate  = $relateData[$n];
                                $input .= '<input type="checkbox" name="' . $key . '[]" value="' . $relate[$relateField] . '" />' . htmlspecialchars(stripslashes($relate[$relateTitle])) . '<br>';
                            }
                        }
                        $input .= '</div>';
                    }
                    $input .= '<p>&nbsp;</p>';
                    break;
                    
                case "checkbox:relate:table":
                    $input = "";
                    $relate = explode(":", $value['relate']);
                    $relateTable = $relate[0];
                    $relateField1 = explode("=", $relate[1]);
                    $relateField2 = explode("=", $relate[2]);
                    $field1 = $relateField1[0];
                    $field2 = $relateField2[0];
                    
                    $table1 = explode(".", $relateField1[1]);
                    $table2 = explode(".", $relateField2[1]);
                    
                    $suffix_query = isset($value['suffix_query']) ? $value['suffix_query'] : "";
                    $strSql = "SELECT `$table2[1]`, `$table2[2]` FROM " . $table2[0] . " ORDER BY `$table2[2]` " . $suffix_query;
                    $relate_rs = $oDb->query($strSql) or die("Lỗi MySql: " . $strSql);
                    $relateData = $oDb->fetchAll($relate_rs);
                    $num_col = $this->getCheckboxCol(count($relateData));
                    for($i = 0; $i < $num_col ; $i++){
                        $withPercent = floor(100/$num_col);
                        $input .= '<div class="inline-block v-alin-top" style="width: '.$withPercent.'%">';
                        
                        $recordBreak = ceil(count($relateData)/$num_col);
                        for($n = $i*$recordBreak; $n < ($i+1)* $recordBreak ; $n++){
                            if(isset($relateData[$n])){
                                $relateRow  = $relateData[$n];
                                $input .= '<input type="checkbox" name="' . $key . '[]" value="' . $relateRow[$table2[1]] . '" />' . htmlspecialchars(stripslashes($relateRow[$table2[2]])) . '<br>';
                            }
                        }
                        $input .= '</div>';
                    }
                    $input .= '<p>&nbsp;</p>';
                    break;

                case "datetime:current":
                    admin_register_style('jquery-ui-datetime', admin_url() . 'css/ui-lightness/jquery-ui-1.8.6.custom.css');
                    admin_register_script('jquery-ui', 'js/jquery-ui.min.js', false, true);
                    admin_register_script('jquery-ui-timepicker-addon', 'js/jquery-ui-timepicker-addon.min.js', false, true);
                    $input = '<input type="text" name="' . $key . '" value="' . date('Y-m-d H:i:s', time()) . '" class="' . $class . ' inputdate" />';
                    break;

                case "map":
                    $input = '<input type="hidden" name="' . $key . '" id="' . $key . '" /><a href="javascript:void(0);" class="maplink" rel="' . $key . '" x="' . $value['x'] . '" y="' . $value['y'] . '">' . trans('mark_map') . '</a>';
                    break;

                case "input:hidden":
                    $default_val = isset($value['std']) ? $value['std'] : '';
                    $input = '<input type="hidden" name="' . $key . '" id="' . $key . '" value="' . $default_val . '" />';
                    break;

                default:
                    $input = '<input type="text" name="' . $key . '"  value="' . $default_val . '" class="' . $class . '" />';
                    break;
            }
            $sufix_title = isset($value['sufix_title']) ? $value['sufix_title'] : '';
            $html.= '<td width="80%">' . $input . ' ' . $sufix_title . '<span class="error"></span></td>';
            $html.= "</tr>";
        }
        $html.= "<tr><td>&nbsp;</td><td><input type='submit' value='" . trans('submit') . "' id='btnAddSubmit' /></td></tr>";
        $html.= "</table></form></div>";

        return $html;
    }

    /**
     * @desc this function will render html edit form with the old input value
     * @param int $id: the primary key of record in the table
     * @param string $msg: message string
     * @return HTML
     */
    public function viewEditForm($id = null, $msg = null) {
        global $oDb;

        admin_register_script('jquery-maskedinput', admin_url() . 'js/jquery.maskedinput-1.0.js', false, true);
        admin_register_script('jquery-metadata', admin_url() . 'js/jquery.metadata.js', false, true);
        admin_register_script('jquery-validate', admin_url() . 'js/jquery.validate.js', false, true);
        admin_register_script('jquery-alphanumeric', admin_url() . 'js/jquery.alphanumeric.js', false, true);

        $html = '';

        $f = Input::get('f', 'txt', '');
        if ($id != null) {
            $editID = $id;
        } else {
            $editID = $_POST['listID'];
        }
        $row = $oDb->fetchArray($this->getRow($editID));
        $html.= '<h2 class="broad-title">' . $this->name . '</h2>';
        if ($msg != null) {
            $html.= $msg;
        }
        $html.= '<span>' . $this->info . '</span>';
        $html.= '<div class="add-bar"><span>' . trans('edit') . ' ' . $this->name . '</span></div>';
        $html.= "<form name='frm' id='frm' action='index.php?f=" . htmlspecialchars($_GET['f']) . "' method='post'>";
        $html.= "<input type='hidden' name='action' value='cedit' />";
        $html.= "<input type='hidden' name='listID' value='" . $row[$this->idField] . "' />";
        $html.= "<input type='hidden' name='search-input' value='" . @$_REQUEST['search-input'] . "' />";
        $html.= "<input type='hidden' name='search-column' value='" . @$_REQUEST['search-column'] . "' />";
        $html.= "<div class='table-list table-form'><table>";
        foreach ($this->column as $key => $value) {

            //validate
            if (isset($value['required']) && $value['required'] != "") {
                $class = "{required:true,messages:{required:'" . addslashes($value['required']) . "'}}";
            } else {
                $class = "";
            }

            if (isset($value['separator']) && $value['separator'] != "") {
                $html.= "<tr><td colspan=2><div class='add-bar'><span>" . $value['separator'] . "</span></div></td></tr>";
            }
            
            $hidden_row = '';
            if($value['type'] == 'input:hidden'){
                $hidden_row = 'style="display:none"';
            }
            
            $html.= "<tr ".$hidden_row.">";            
            $html.= "<td width='15%'>" . $value['title'] . "</td>";
            switch ($value['type']) {
                case "input:text":
                    $input = '<input type="text" name="' . $key . '" class="' . $class . '" value="' . htmlspecialchars(stripslashes($row[$key])) . '"/>';
                    break;

                case "input:function":
                    $function = $value['function'];
                    $data_value = isset($row[$key]) ? $function($row[$key], 'edit') : '';
                    if (function_exists($function)) {
                        $input = '<input type="hidden" name="' . $key . '" value="' . $data_value . '" />';
                        $input .= $function($data_value, 'edit');
                    } else {
                        $input = '<input type="text" name="' . $key . '" class="' . $class . '" value="' . $data_value . '"/>';
                    }
                    break;

                case "input:text:readonly":
                    $input = '<input readonly="" style="background-color: silver;" type="text" name="' . $key . '" class="' . $class . '" value="' . htmlspecialchars(stripslashes($row[$key])) . '"/>';
                    break;

                case "input:password":
                    $input = '<input type="password" name="' . $key . '" class="' . $class . '" value=""/>';
                    break;

                case "input:image":

                    admin_register_script('ajax-upload', admin_url() . 'js/ajaxupload.js', false, true);

                    $f = Input::get('f', 'txt', 'media');
                    if ($row[$key] == "") {
                        $url = ADMIN_FOLDER . "/images/noimage.jpg";
                    } else {
                        $url = $row[$key];
                    }
                    $input = '<input type="text" class="' . $class . ' input-image" id="' . $key . '"  name="' . $key . '" value="' . htmlspecialchars(stripslashes($row[$key])) . '" /> 
							 [<a href="#" class="' . $key . ' btnUpload" id="btnUpload' . $key . '">' . trans('select_images') . '</a>]
							 <span>' . trans('or') . '</span>
							 [<a href="javascript:void(0)" onclick="popitup(\'browser.php?view=grid&search-type=image&f=' . $f . '&inputId=' . $key . '\')" class="' . $key . '" id="btnUpload' . $key . '">' . trans('select_from_library') . '</a>] 
							 <br/>
                             <div class="wrap-images">
                                <img class="image_review" src="' . getThumbnail('thumb-150', $url) . '" width="100" />
                                <div class="rwmb-image-bar"><a onclick="deleteDefaultImages(this)" class="rwmb-delete-file" href="javascript:void(0)">×</a></div>
                             </div>
                             <br/>
                             <span  class="lblUpload" id="lblUpload' . $key . '"></span>';
                    break;

                case "input:file":
                    if ($row[$key] == "") {
                        $file = trans('no_file');
                    } else {
                        $file = $row[$key];
                    }
                    $input = '<input type="text" class="' . $class . ' input-file"  name="' . $key . '" value="' . htmlspecialchars(stripslashes($row[$key])) . '" /> [<a href="#" class="browse ' . $key . '" id="btnUpload' . $key . '">Chọn file</a>]&nbsp;&nbsp;(<a href="upload-file.php?ext=1" target="_blank" title="' . trans('msg_allow_filetype') . '">?</a>)</span><br/><em class="file">' . getFileName($file) . '</em>';
                    break;

                case "input:multiimages":
                    admin_register_script('jquery-widget', admin_url() . 'js/jquery.ui.widget.js');
                    admin_register_script('jquery-fileupload', admin_url() . 'js/jquery.fileupload.js');

                    unset($_SESSION['multiimages'][$key]); // delete session                         
                    $html_img = '';
                    $arrImage = getImagesTable($value['table'], $row[$this->idField]);
                    if (count($arrImage) == 0) {
                        $url = ADMIN_FOLDER . "/images/noimage.jpg";
                    } else {
                        $row[$key] = '';
                        foreach ($arrImage as $k => $v) {
                            $url = $v[$value['table']['images_url']];
                            $small = getThumbnail('thumb-150', $url);
                            $img_delete = '<div class="rwmb-image-bar"><a href="javascript:void(0)" class="rwmb-delete-file" onclick="deleteFileUpload(' . $v[$this->idField] . ',\'' . $f . '\')">×</a></div>';
                            if ($small) {
                                $html_img.= "<li id=\"img_" . $v[$this->idField] . "\"><img height=\"100\" class='image_review'  src=\"" . $small . "\">" . $img_delete . "</li>";
                            }
                        }
                    }
                    $input = '<ul id="thumbnails">
                                    <li>
                                        <div id="drop"><a>' . trans('select_images') . '</a>
                        				    <input type="file" name="userfile" multiple />
                        			    </div>
                                    </li>
                                    ' . $html_img . '
                                   </ul>
                                <div style="clear: both;"></div>';
                    break;

                case "input:price":
                    $input = '<input type="hidden" id="' . $key . '" name="' . $key . '" value="' . htmlspecialchars(stripslashes(price($row[$key]))) . '" rel="' . htmlspecialchars(stripslashes(price($row[$key]))) . '">
                              <input type="text" name="' . $key . '_view" valto="' . $key . '" style="width:150px;" class="' . $class . ' price-input" value="' . htmlspecialchars(stripslashes(price($row[$key]))) . '" rel="' . htmlspecialchars(stripslashes(price($row[$key]))) . '" /> VNĐ';
                    break;

                case "input:attribute":
                    $attribute = unserialize($row[$key]);
                    $attribute_html = '';
                    if (!$attribute || count($attribute) <= 0) {
                        $attribute[] = array(
                            'name' => '',
                            'value' => ''
                        );
                    }
                    foreach ($attribute as $att_value) {
                        $attribute_html .=
                                '<div class="attribute">
                                <div class="rwmb-image-bar"><a onclick="deleteAttribute(this)" class="rwmb-delete-file" href="javascript:void(0)">×</a></div>
                                <div class="attr-name">    
                                    <label>' . trans('name') . ':</label>
                                    <input type="text" name="' . $key . '_name[]" value="' . $att_value['name'] . '" placeholder="' . trans('placeholder_att_name') . '" />
                                </div>
                                <div class="attr-value">
                                    <label>' . trans('value') . ':</label>
                                    <textarea name="' . $key . '_value[]" placeholder="' . trans('placeholder_att_value') . '">' . $att_value['value'] . '</textarea>
                                </div>
                                <div class="clear"></div>
                              </div>';
                    }

                    $input = '<div class="wrap-attribute">
                                  ' . $attribute_html . '
                             </div>
                             <button type="button" class="attr-button" onclick="addAttribute()">' . trans('add') . '</button>
                             <div class="clear"></div>';
                    break;

                case "input:int10":
                    $input = '<input type="text" name="' . $key . '" style="width:100px;" class="' . $class . ' input-number" maxlength="10" value="' . (int) $row[$key] . '" />';
                    break;

                case "textarea":
                    admin_register_script('ckeditor', admin_url() . 'editor/ckeditor/ckeditor.js');
                    admin_register_script('ckfinder', admin_url() . 'editor/ckfinder/ckfinder.js');

                    if (!isset($value['row']) || (int) $value['row'] == 0) {
                        $rows = 10;
                    } else {
                        $rows = (int) $value['row'];
                    }
                    $input = '<textarea name="' . $key . '" class="' . $class . ' txa" rows="' . $rows . '" cols="60">' . htmlspecialchars(stripslashes(stripslashes(stripslashes(stripslashes($row[$key]))))) . '</textarea>';
                    $input .= '
                    <script type="text/javascript">
                        //<![CDATA[
                    	var editor = CKEDITOR.replace("' . $key . '");
                         CKFinder.SetupCKEditor( editor, { 
                                BasePath : "editor/ckfinder/", 
                                RememberLastFolder : false
                         }) ;
                        //]]>                                
                    </script>';
                    break;

                case "textarea:noeditor":
                    $rows = isset($value['row']) ? (int) $value['row'] : "8";
                    $input = '<textarea name="' . $key . '" class="' . $class . ' txa" rows="' . $rows . '" cols="60" style="resize: vertical;">' . htmlspecialchars(stripslashes($row[$key])) . '</textarea>';
                    break;

                case "checkbox":
                    if ((int) $row[$key] == 1) {
                        $check = "checked=''";
                    } else {
                        $check = "";
                    }
                    $input = '<input type="checkbox" class="' . $class . '" name="' . $key . '" value="1" ' . $check . '  /> ' . strip_tags($value['label']);
                    break;

                case "combobox:relate:int":
                    if ($value['required'] != "") {
                        $class = "{min:1,messages:{min:'" . addslashes($value['required']) . "'}}";
                    } else {
                        $class = "";
                    }
                    $input = '<select name="' . $key . '" class="' . $class . '" >';
                    $relate = explode(".", $value['relate']);
                    $relateTable = $relate[0];
                    $relateTitle = $relate[1];
                    $relateField = $relate[2];
                    $suffix_query = isset($value['suffix_query']) ? $value['suffix_query'] : "";
                    $strSql = "select * from " . $relateTable . " " . $suffix_query;
                    $relate_rs = $oDb->query($strSql) or die("Lỗi MySql: " . $strSql);
                    
                    while ($relate = $oDb->fetchArray($relate_rs)) {
                        if ($relate[$relateField] == $row[$key]) {
                            $check = "selected class='optSelected'";
                        } else {
                            $check = "";
                        }
                        $input .= '<option value="' . $relate[$relateField] . '" ' . $check . ' >' . htmlspecialchars(stripslashes($relate[$relateTitle])) . '</option>';
                    }
                    $input .= "</select>";
                    break;

                case "combobox":
                    if (isset($value['required']) && $value['required'] != "") {
                        $class = "{min:1,messages:{min:'" . addslashes($value['required']) . "'}}";
                    } else {
                        $class = "";
                    }
                    $input = '<select name="' . $key . '" class="' . $class . '" >';
                    foreach ($value['data'] as $k => $v) {
                        if ($row[$key] == $k) {
                            $check = "selected class='optSelected'";
                        } else {
                            $check = "";
                        }
                        $input .= '<option value="' . $k . '" ' . $check . '>' . htmlspecialchars(stripslashes($v)) . '</option>';
                    }
                    $input .= "</select>";
                    break;

                case "checkbox:relate:int":
                    $input = "";
                    $relate = explode(".", $value['relate']);
                    $relateTable = $relate[0]; // table related
                    $relateTitle = $relate[1]; // title related, example name of category
                    $relateField = $relate[2]; // field related, example id of category
                    $suffix_query = isset($value['suffix_query']) ? $value['suffix_query'] : "";
                    $strSql = "SELECT $relateField, $relateTitle FROM " . $relateTable . " " . $suffix_query;
                    $relate_rs = $oDb->query($strSql) or die("Lỗi MySql: " . $strSql);
                    $relateData = $oDb->fetchAll($relate_rs);
                    $num_col = $this->getCheckboxCol(count($relateData));
                    for($i = 0; $i < $num_col ; $i++){
                        $withPercent = floor(100/$num_col);
                        $input .= '<div class="inline-block v-alin-top" style="width: '.$withPercent.'%">';
                        
                        $recordBreak = ceil(count($relateData)/$num_col);
                        for($n = $i*$recordBreak; $n < ($i+1)* $recordBreak ; $n++){
                            if(isset($relateData[$n])){
                                $relate  = $relateData[$n];
                                $hasKey = explode($relate[$relateField], $row[$key]);
                                if (count($hasKey) > 1) {
                                    $check = "checked=''";
                                } else {
                                    $check = "";
                                }
                                $input .= '<input type="checkbox" name="' . $key . '[]" value="' . $relate[$relateField] . '" ' . $check . ' />' . htmlspecialchars(stripslashes($relate[$relateTitle])) . '<br>';
                            }
                        }
                        $input .= '</div>';
                    }
                    $input .= '<p>&nbsp;</p>';
                    break;
                    
                    
                case "checkbox:relate:table":
                    $input = "";
                    $relate = explode(":", $value['relate']);
                    $relateTable = $relate[0];
                    $relateField1 = explode("=", $relate[1]);
                    $relateField2 = explode("=", $relate[2]);
                    $field1 = $relateField1[0];
                    $field2 = $relateField2[0];
                    
                    $table1 = explode(".", $relateField1[1]);
                    $table2 = explode(".", $relateField2[1]);
                    
                    $suffix_query = isset($value['suffix_query']) ? $value['suffix_query'] : "";
                    $strSql = "SELECT `$table2[1]`, `$table2[2]` FROM " . $table2[0] . " ORDER BY `$table2[2]` " . $suffix_query;
                    $relate_rs = $oDb->query($strSql) or die("Lỗi MySql: " . $strSql);
                    $relateData = $oDb->fetchAll($relate_rs);
                    $num_col = $this->getCheckboxCol(count($relateData));
                    for($i = 0; $i < $num_col ; $i++){
                        $withPercent = floor(100/$num_col);
                        $input .= '<div class="inline-block v-alin-top" style="width: '.$withPercent.'%">';
                        
                        $recordBreak = ceil(count($relateData)/$num_col);
                        for($n = $i*$recordBreak; $n < ($i+1)* $recordBreak ; $n++){
                            if(isset($relateData[$n])){
                                $relateRow  = $relateData[$n];
                                $sqlRelateTable = "SELECT * FROM $relateTable WHERE `$relateField1[0]` = ".$row[$this->pk]." AND `$relateField2[0]` = ".$relateRow[$table2[1]];
                                if ($this->checkRecordExists($sqlRelateTable)) {
                                    $check = "checked=''";
                                } else {
                                    $check = "";
                                }
                                $input .= '<input type="checkbox" name="' . $key . '[]" value="' . $relateRow[$table2[1]] . '" ' . $check . ' />' . htmlspecialchars(stripslashes($relateRow[$table2[2]])) . '<br>';
                            }
                        }
                        $input .= '</div>';
                    }
                    $input .= '<p>&nbsp;</p>';
                    break;

                case "datetime:current":
                    admin_register_style('jquery-ui-lightness', admin_url() . 'css/ui-lightness/jquery-ui-1.8.6.custom.css');
                    admin_register_script('jquery-ui', 'js/jquery-ui.min.js', false, true);
                    admin_register_script('jquery-ui-timepicker-addon', 'js/jquery-ui-timepicker-addon.min.js', false, true);

                    $input = '<input type="text" class="' . $class . ' inputdate" name="' . $key . '" value="' . $row[$key] . '" />';
                    break;

                case "map":
                    $map_pos = explode(":", $row[$key]);
                    $x = $map_pos[0];
                    $y = $map_pos[1];
                    if (($x == "") || ($y == "")) {
                        $x = $value['x'];
                        $y = $value['y'];
                        if (($x == "") || ($y == "")) {
                            $x = 21.03191538070277;
                            $y = 105.81503391265869;
                        }
                    }
                    $input = '<input type="hidden" name="' . $key . '" id="' . $key . '" value="' . $row[$key] . '" /><a href="javascript:void(0);" class="maplink" rel="' . $key . '" x="' . $x . '" y="' . $y . '">' . trans('marked') . ' <span style="color: #777;">(' . $x . ',' . $y . ')</span></a>';
                    break;

                case "input:hidden":
                    $hidden_value = ($row[$key] != '') ? $row[$key] : $value['std'];
                    $input = '<input type="hidden" name="' . $key . '" id="' . $key . '" value="' . $hidden_value . '" />';
                    break;

                default:
                    $input = '<input type="text" name="' . $key . '" class="' . $class . '" value="' . htmlspecialchars(stripslashes($row[$key])) . '" />';
                    break;
            }
            $txt_sufixtitle = isset($value['sufix_title']) ? $value['sufix_title'] : "";
            $html.= "<td>" . $input . " " . $txt_sufixtitle . "<span class='error'></span></td>";
            $html.= "</tr>";
        }
        $html.= "<tr><td>&nbsp;</td><td><input type='reset' value='" . trans('reset_value') . "' /> <input type='submit' id='btnAddSubmit' value='" . trans('submit') . "' /> </td></tr>";
        $html.= "</table></form>";

        return $html;
    }

    /**
     * @desc add new value: this value was submit from add form
     * @return nothing
     */
    function doAdd() {
        global $oDb;
        $html = '';
        $column = "";
        $data = "";
        $count = 0;
        $update_ordering = false;
        
        foreach ($this->column as $key => $value) {
            if ($value['type'] == 'input:multiimages') {
                continue;
            }
            
            if($value['type'] == 'checkbox:relate:table'){
                continue;
            }

            if ($key == 'ordering') {
                $update_ordering = true;
            }
            
            if (isset($value['unique']) && $value['unique'] == true) {
                $checkuni_rs = $oDb->query("select * from " . $this->table . " where " . $key . "='" . addslashes($_POST[$key]) . "'");
                $label = $value['title'];
            }
            $arr_data = isset($_POST[$key]) ? $_POST[$key] : "";
            $data_item = "";
            if ($value['type'] == "checkbox:relate:int" && count($arr_data) > 0) {
                if ($arr_data && count($arr_data) > 0) {
                    foreach ($arr_data as $item) {
                        $data_item .= $item . ",";
                    }
                }
                $data_item = trim($data_item, ",");
            } else if ($value['type'] == "input:price" || $value['type'] == 'input:int10') {
                $data_item = cleanNumber($_POST[$key]);
            } else if ($value['type'] == "input:password" && $_POST[$key] != '') {
                $data_item = md5(md5(md5($_POST[$key])));
            } else if ($value['type'] == 'input:attribute') {
                if ($_POST[$key . '_name'] && $_POST[$key . '_value']) {
                    for ($m = 0; $m < count($_POST[$key . '_name']); $m++) {
                        $data_item[] = array(
                            'name' => $_POST[$key . '_name'][$m],
                            'value' => $_POST[$key . '_value'][$m],
                        );
                    }
                }
                $data_item = serialize($data_item);
            } else if ($value['type'] == 'input:function') {
                $function = $value['function'];
                if (function_exists($function)) {
                    $function($_POST[$key], 'doAdd');
                }
            } else if($value['type'] == 'textarea'){
                $data_item = downloadImagesFromHTML($_POST[$key]);
            } else {
                $data_item = isset($_POST[$key]) ? $_POST[$key] : '';
            }
            $this->$key = $data_item;
            $count++;
        }
        
        if (isset($value['unique']) && $value['unique'] == true) {
            if ($oDb->numRows($checkuni_rs) > 0) {
                $this->viewAddForm("<div class='red'>" . trans('msg_exists', array($label)) . "</div>");
            }
        } else {            
            $chk = $this->insert();
            
            // update relation table
            foreach ($this->column as $key => $value) {
                if($value['type'] == 'checkbox:relate:table'){
                    $this->updateRelationTable($chk ,$key, $value);
                    continue;
                }
            }
            
            // update media info
            $this->updateMedia($chk);
            
            
            foreach ($this->column as $field => $field_info) {
                // update ordering = id insert if ordering field exists // HanhND    
                if ($field == 'ordering') {
                    $sql = "UPDATE $this->table SET `$field` = $chk WHERE " . $this->pk . " = $chk ";
                    $oDb->query($sql);
                }

                // update relation table for multiimages
                if ($field_info['type'] == 'input:multiimages') {
                    $table_info = $field_info['table'];
                    $table_name = $table_info['table_name'];
                    $primary_key = $table_info['primary_key'];
                    $relate_id = $table_info['relate_id'];

                    $arrImg = isset($_SESSION['multiimages'][$field]) ? $_SESSION['multiimages'][$field] : NULL;
                    if (is_array($arrImg) && count($arrImg) > 0) {
                        $list_table_id = implode(",", $arrImg);
                        $sql_update = " UPDATE `$table_name` SET `$relate_id` = $id  WHERE `$primary_key` IN($list_table_id) ";
                        echo $sql_update;
                        $oDb->query($sql_update);
                    }
                    unset($_SESSION['multiimages'][$field]);
                }
            } // end foreach
            $html.= "<script>window.location.href='index.php?f=" . htmlspecialchars($_GET['f']) . "';</script>";
        }
               
        
        // reload cache 
        cacheSetting(true);
        
        return $html;
    }

    /**
     * @desc update value. this value was submit by edit form
     * @return noghing
     */
    function doEdit() {
        global $oDb;
        $html = '';
        $column = "";
        $data = "";
        $count = 0;
        $id = $_POST['listID'];
        foreach ($this->column as $key => $value) {
            if ($value['type'] == 'input:multiimages') {
                continue;
            }
            if($value['type'] == 'checkbox:relate:table'){
                continue;
            }
            if (isset($value['unique']) && $value['unique'] == true) {
                $checkuni_rs = $oDb->query("select * from " . $this->table . " where `" . $key . "` ='" . addslashes($_POST[$key]) . "' and " . $this->idField . "<>'" . $id . "'");
                $label = $value['title'];
            }
            $arr_data = isset($_POST[$key]) ? $_POST[$key] : "";
            $data_item = "";
            if ($value['type'] == "checkbox:relate:int" && count($arr_data) > 0) {
                foreach ($arr_data as $item) {
                    $data_item .= $item . ",";
                }
                $data_item = trim($data_item, ",");
            } else if ($value['type'] == "input:price" || $value['type'] == 'input:int10') {
                $data_item = cleanNumber($_POST[$key]);
            } else if ($value['type'] == "input:password" && $_POST[$key] != '') {
                $check_rs = $oDb->query("select * from " . $this->table . " where `" . $this->idField . "` = '" . $id . "'");
                $check = $oDb->fetchArray($check_rs);
                $newval = md5(md5(md5($_POST[$key])));
                if ($check[$key] != $_POST[$key]) {
                    $data_item = $newval;
                } else {
                    $data_item = $check[$key];
                }
            } else if ($value['type'] == 'input:attribute') {
                if ($_POST[$key . '_name'] && $_POST[$key . '_value']) {
                    for ($m = 0; $m < count($_POST[$key . '_name']); $m++) {
                        $data_item[] = array(
                            'name' => $_POST[$key . '_name'][$m],
                            'value' => $_POST[$key . '_value'][$m],
                        );
                    }
                }
                $data_item = serialize($data_item);
            } else if ($value['type'] == 'input:function') {
                $function = $value['function'];
                if (function_exists($function)) {
                    $function($_POST[$key], 'doEdit');
                }
            } else if($value['type'] == 'textarea'){
                $data_item = downloadImagesFromHTML($_POST[$key]);            
            } else {
                $data_item = isset($_POST[$key]) ? $_POST[$key] : "";
            }

            $this->$key = $data_item;
            $count++;
        }

        if (isset($checkuni_rs) && (int) $oDb->numRows($checkuni_rs) > 0) {
            $this->viewEditForm($id, "<div class='red'>" . trans('msg_exists', array($label)) . "</div>");
        } else {
            $chk = $this->update($id, $this->fields);
            
            // update relation table
            foreach ($this->column as $key => $value) {
                if($value['type'] == 'checkbox:relate:table'){
                    $this->updateRelationTable($id ,$key, $value);
                    continue;
                }
            }
            
            // update object_id for t_media in the database
            $this->updateMedia($id);
            foreach ($this->column as $field => $field_info) {
                // update relation table for multiimages type
                if ($field_info['type'] == 'input:multiimages') {
                    $table_info = $field_info['table'];
                    $table_name = $table_info['table_name'];
                    $primary_key = $table_info['primary_key'];
                    $relate_id = $table_info['relate_id'];
                    $arrImg = isset($_SESSION['multiimages'][$field]) ? $_SESSION['multiimages'][$field] : NULL;
                    if (is_array($arrImg) && count($arrImg) > 0) {
                        $list_table_id = implode(",", $arrImg);
                        $sql_update = " UPDATE `$table_name` SET `$relate_id` = $id  WHERE `$primary_key` IN($list_table_id) ";
                        $oDb->query($sql_update);
                    }
                    unset($_SESSION['multiimages'][$field]);
                }
            } // end foreach

            $redirect = "index.php?f=" . htmlspecialchars($_GET['f']);
            if (trim($_REQUEST['search-input']) != '') {
                $redirect .= "&search-input=" . $_REQUEST['search-input'];
            }
            if (trim($_REQUEST['search-column']) != '') {
                $redirect .= "&search-column=" . $_REQUEST['search-column'];
            }
            $html.= "<script>window.location.href='index.php?f=" . htmlspecialchars($_GET['f']) . "';</script>";
        }// end else

        // reload cache 
        cacheSetting(true);
        
        return $html;
    }

    /**
     * @desc save field
     * @return nothing
     */
    function saveField() {
        global $oDb;
        $html = '';
        
        $fields = explode(",", $_POST['field']);
        foreach ($fields as $field){
            $column = $this->column;
            $listID = explode(',', $_POST['listID']);
            $listVal = $_POST[$field];
            for ($i = 1; $i < count($listID); $i++) {
                if($column[$field]['type'] == 'input:price' || $column[$field]['type'] == 'input:int10'){
                    $this->$field = str_replace(',', '', str_replace('.', '', $listVal[$i - 1]));
                } else {
                    $this->$field = $listVal[$i - 1];
                }
                $this->update($listID[$i], array($field));
            }
        }

        $redirect = "index.php?f=" . htmlspecialchars($_GET['f']) . "&search-input=" . $_REQUEST['search-input'] . "&search-column=" . $_REQUEST['search-column'];
        $html.= "<script>window.location.href='$redirect';</script>";

        return $html;
    }

    /**
     * @desc update value 0 to 1 or 1 to 0 with "type"	=> "checkbox",
     * @param 
     * @return nothing
     */
    function switchval() {
        global $oDb;
        $html = '';
        $this->$_POST['field'] = (int) $_POST['singleval'];
        $this->update((int) $_POST['singleid'], array($_POST['field']));
        $html.= "<script>window.location.href='index.php?f=" . htmlspecialchars($_GET['f']) . "&search-input=" . $_REQUEST['search-input'] . "&search-column=" . $_REQUEST['search-column'] . "';</script>";
        
        // reload cache 
        cacheSetting(true);
        
        return $html;
    }

    /**
     * @desc add slash for list key
     * @param string $str
     * @return string
     */
    function addSlashToListKey($str) {
        $str = str_replace(",", "','", $str);
        return "'" . $str . "'";
    }

    /**
     * @desc update object_id for table media (this feature using for upload file or images)
     * @param int $object_id: the product_id, news_id ...
     * @return nothing
     */
    function updateMedia($object_id) {
        global $oDb;
        $Media = new Media();
        $table = $Media->table;
        if (isset($_SESSION['media']) && count($_SESSION['media']) > 0) {
            $list_id = implode(',', $_SESSION['media']);
            $sql = "UPDATE $table SET `object_id` = '$object_id' WHERE id IN($list_id) ";
            $oDb->query($sql);
        }
        unset($_SESSION['media']);
    }
    
    
    
    /**
     * @desc get primary key from ralate table
     * @param string $relate: table, field, id. Example: t_news_category.name.id
     * @return id
     */
    function getIdFromRelateTable($relate, $keyword){
        global $oDb;
        $relateInfo = explode('.', $relate);
        $result = array();
        if($keyword){
            $sql = "SELECT * FROM ".$relateInfo[0]." WHERE `".$relateInfo[1]."` LIKE '%".strtolower($keyword)."%' ";
            $rc = $oDb->query($sql);
            $rs = $oDb->fetchArray($rc);
            $result[] = $rs[$relateInfo[2]];
        }
        return $result;
    }
    
    
    
    /**
     * @desc get checkbox col for checkbox:relate:int
     * @param int $recordNumber: total record number
     * @return int
     */
    function getCheckboxCol($recordNumber, $maxCol = 3){
        if($recordNumber >= 15){
            return $maxCol;
        }
        if($recordNumber >= 10){
            return 2;
        }
        return 1;
    }
    
    
    /**
     * @desc check result with sql statement
     * @param string $sql: sql
     * @return boolean
     */
    function checkRecordExists($sql){
        global $oDb;
        $rc = $oDb->query($sql);
        if($oDb->numRows($rc)){
            return true;
        }
        
        return false;
    }
    
    
    /**
     * @desc update relation table
     * @param string $key: field in $_POST
     * @param array $value: array field relate infomation
     * @return nothing
     */
    function updateRelationTable($id , $key, $value){
        global $oDb;
        $relate = explode(":", $value['relate']);
        $relateTable = $relate[0];
        $relateField1 = explode("=", $relate[1]);
        $relateField2 = explode("=", $relate[2]);
        $field1 = $relateField1[0];
        $field2 = $relateField2[0];

        $table1 = explode(".", $relateField1[1]);
        $table2 = explode(".", $relateField2[1]);
        
        
        if($id && is_array($_POST[$key]) && count($_POST[$key])){ // update
            // delete old data
            $sql = "DELETE FROM $relateTable WHERE `$field1` = $id ";
            $oDb->query($sql);
            foreach($_POST[$key] as $postValue){
                $sqlInsert = "INSERT INTO `$relateTable` (`$field1`, `$field2`) VALUES ('$id', '$postValue')";
                $oDb->query($sqlInsert);
            }
        }
    }

}
