<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2014
 * @desc class render html for options
 */
class cmsOptions extends Base {

    public $name;
    public $options;

    /**
     * @desc class render html for options
     * @param array $options
     * @return nothing
     */
    public function __construct($options) {

        if (function_exists('admin_url')) {

            admin_register_style('cms-options', admin_url() . 'css/admin-options.css'); // options style
            admin_register_style('cms-options-picker', admin_url() . 'css/color-picker.min.css'); // options-picker
            admin_register_style('jquery-ui-custom', admin_url() . 'css/jquery-ui-custom.css'); // jquery ui-custom
            admin_register_style('jquery-colpick', admin_url() . 'css/colpick.css'); // jquery colpick

            admin_register_script('option-maskedinput', admin_url() . 'js/jquery.maskedinput-1.2.2.js', false, true);
            admin_register_script('option-cookie', admin_url() . 'js/cookie.js', false, true);
            admin_register_script('option-colpick', admin_url() . 'js/colpick.js', false, true);
            admin_register_script('option-smof', admin_url() . 'js/smof.js', false, true);
            admin_register_script('jquery-ui', admin_url() . 'js/jquery-ui.min.js', false, true);
            admin_register_script('jquery-wiget', admin_url() . 'js/jquery.ui.widget.js', false, true);
            admin_register_script('option-iris', admin_url() . 'js/iris.min.js', false, true);
        }

        $this->options = $options;
    }

    /**
     * @desc function render header
     * @param 
     * @return HTML
     */
    public function printHead() {
        $html = '';
        $hook = array();
        foreach ($this->options as $option) {
            if ($option['type'] == 'heading') {
                $hook[] = strtolower(str_replace(' ', '', $option['name']));
            }
        }
        $hook = implode('","', $hook);
        $html .= '
        <div id="of_container" class="wrap">
        <span style="display: none;" id="hooks">["' . $hook . '"]</span>
        <span style="display: none;" id="options_url">' . ajax_url() . '</span>
            <input type="hidden" id="reset" value="" />
            <input type="hidden" id="security" name="security" value="2fc684f2b9" />
            <div id="of-popup-save" class="of-save-popup">
                    <div class="of-save-save">' . trans('options_updated') . '</div>
            </div>
            <div id="of-popup-reset" class="of-save-popup">
                    <div class="of-save-reset">' . trans('options_was_reset') . '</div>
            </div>
            <div id="of-popup-fail" class="of-save-popup">
                    <div class="of-save-fail">' . trans('error') . '!</div>
            </div>
            <form name="of_form" method="post" id="of_form" action="" enctype="multipart/form-data">
            <div id="header">
            <div class="logo">
                <h2>' . $this->name . '</h2>
            </div>
            <div class="icon-option"></div>
            <div class="clear"></div>
        </div>
        <div id="info_bar">
            <a><div id="expand_options" class="expand">Expand</div></a> 
            <img style="display:none" src="' . OPTIONS_IMAGES . 'loading-bottom.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="Working...">
            <button type="button" id="of_save" class="button-primary">' . trans('save_all_change') . '</button>
        </div>';

        return $html;
    }

    /**
     * @desc function render footer
     * @param 
     * @return HTML
     */
    public static function printFooter() {
        $html = '
        <div class="save_bar">
            <img style="display:none" src="' . OPTIONS_IMAGES . 'loading-bottom.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="Working...">
            <button type="button" id="of_save" class="button-primary">' . trans('save_all_change') . '</button>
            <button type="button" id="of_reset" class="button submit-button reset-button">' . trans('options_reset') . '</button>
            <img style="display:none; float:left; margin: 5px;" src="' . OPTIONS_IMAGES . 'loading-bottom.gif" class="ajax-reset-loading-img ajax-loading-img-bottom alt="Working...">
        </div>
        </div>';

        return $html;
    }

    /**
     * @desc function render left menu
     * @param 
     * @return HTML
     */
    public function printMenu() {
        $html = '<div id="of-nav">';
        $html .= '<ul>';
        $i = 0;
        foreach ($this->options as $option) {
            if ($option['type'] == 'heading') {
                $curent = ($i == 0) ? 'current' : '';
                $i++;
                $html .= '<li class="' . strtolower(title_url($option['name'])) . '"><a title="' . $option['name'] . '" style="background-image: url(' . $option['icon'] . ');" href="#' . strtolower(title_url($option['name'])) . '" data-option="' . strtolower(title_url($option['name'])) . '">' . $option['name'] . '</a></li>';
            }
        }
        $html .= '</ul>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Sanitize option
     *
     * Sanitize & returns default values if don't exist
     * 
     * Notes:
      - For further uses, you can check for the $value['type'] and performs
      more speficic sanitization on the option
      - The ultimate objective of this function is to prevent the "undefined index"
      errors some authors are having due to malformed options array
     */
    public function sanitize_option($value) {
        $result = array();
        $defaults = array(
            "name" => "",
            "desc" => "",
            "id" => "",
            "std" => "",
            "mod" => "",
            "type" => ""
        );
        foreach ($defaults as $k => $v) {
            $result[$k] = isset($value[$k]) ? $value[$k] : $v;
        }

        foreach ($value as $k => $v) {
            if (!isset($result[$k])) {
                $result[$k] = $v;
            }
        }
        return $result;
    }

    /**
     * @desc get options value
     * @param 
     * @return array
     */
    function getOptionData() {
        $result = array();
        $options = $this->options;
        foreach ($options as $key => $value) {
            if (!isset($value['id']))
                continue;
            $default_value = isset($value['std']) ? $value['std'] : '';
            $result[$value['id']] = (get_option($value['id']) != '') ? get_option($value['id']) : $default_value;
        }
        return $result;
    }

    /**
     * @desc detail content
     * @param 
     * @return html
     */
    public function printContent() {
        $options = $this->options;
        if (empty($options))
            return;
        if (empty($smof_data))
            $smof_data = $this->getOptionData(); //of_get_options();
        $data = $smof_data;

        $defaults = array();
        $counter = 0;
        $menu = '';
        $output = '';
        $update_data = false;
        $smof_output = '';

        if ($smof_output != "") {
            $output .= $smof_output;
            $smof_output = "";
        }


        foreach ($options as $value) {
            // sanitize option
            if ($value['type'] != "heading")
                $value = self::sanitize_option($value);

            $counter++;
            $val = '';

            //create array of defaults		
            if ($value['type'] == 'multicheck') {
                if (is_array($value['std'])) {
                    foreach ($value['std'] as $i => $key) {
                        $defaults[$value['id']][$key] = true;
                    }
                } else {
                    $defaults[$value['id']][$value['std']] = true;
                }
            } else {
                if (isset($value['id']))
                    $defaults[$value['id']] = $value['std'];
            }

            /* condition start */
            if (!empty($smof_data) || !empty($data)) {//
                if (array_key_exists('id', $value) && !isset($smof_data[$value['id']])) {
                    $smof_data[$value['id']] = $value['std'];
                    if ($value['type'] == "checkbox" && $value['std'] == 0) {
                        $smof_data[$value['id']] = 0;
                    } else {
                        $update_data = true;
                    }
                }
                if (array_key_exists('id', $value) && !isset($smof_details[$value['id']])) {
                    $smof_details[$value['id']] = $smof_data[$value['id']];
                }

                //Start Heading
                if ($value['type'] != "heading") {
                    $class = '';
                    if (isset($value['class'])) {
                        $class = $value['class'];
                    }

                    //hide items in checkbox group
                    $fold = '';
                    if (array_key_exists("fold", $value)) {
                        if (isset($smof_data[$value['fold']]) && $smof_data[$value['fold']]) {
                            $fold = "f_" . $value['fold'] . " ";
                        } else {
                            $fold = "f_" . $value['fold'] . " temphide ";
                        }
                    }

                    $output .= '<div id="section-' . $value['id'] . '" class="' . $fold . 'section section-' . $value['type'] . ' ' . $class . '">' . "\n";

                    //only show header if 'name' value exists
                    if ($value['name'])
                        $output .= '<h3 class="heading">' . $value['name'] . '</h3>' . "\n";

                    $output .= '<div class="option">' . "\n" . '<div class="controls">' . "\n";
                }
                //End Heading
                //if (!isset($smof_data[$value['id']]) && $value['type'] != "heading")
                //	continue;
                //switch statement to handle various options type                              
                switch ($value['type']) {

                    //text input
                    case 'text':
                        $t_value = '';
                        $t_value = stripslashes($smof_data[$value['id']]);

                        $mini = '';
                        if (!isset($value['mod']))
                            $value['mod'] = '';
                        if ($value['mod'] == 'mini') {
                            $mini = 'mini';
                        }

                        $output .= '<input class="of-input ' . $mini . '" name="' . $value['id'] . '" id="' . $value['id'] . '" type="' . $value['type'] . '" value="' . $t_value . '" />';
                        break;

                    //select option
                    case 'select':
                        $mini = '';
                        if (!isset($value['mod']))
                            $value['mod'] = '';
                        if ($value['mod'] == 'mini') {
                            $mini = 'mini';
                        }
                        $output .= '<div class="select_wrapper ' . $mini . '">';
                        $output .= '<select class="select of-input" name="' . $value['id'] . '" id="' . $value['id'] . '">';

                        foreach ($value['options'] as $select_ID => $option) {
                            $theValue = $option;
                            if (!is_numeric($select_ID))
                                $theValue = $select_ID;
                            $output .= '<option id="' . $select_ID . '" value="' . $theValue . '" ' . selected($smof_data[$value['id']], $theValue, false) . ' />' . $option . '</option>';
                        }
                        $output .= '</select></div>';
                        break;

                    //textarea option
                    case 'textarea':
                        $cols = '8';
                        $ta_value = '';

                        if (isset($value['options'])) {
                            $ta_options = $value['options'];
                            if (isset($ta_options['cols'])) {
                                $cols = $ta_options['cols'];
                            }
                        }

                        $ta_value = stripslashes($smof_data[$value['id']]);
                        $output .= '<textarea class="of-input" name="' . $value['id'] . '" id="' . $value['id'] . '" cols="' . $cols . '" rows="3">' . $ta_value . '</textarea>';
                        break;

                    //radiobox option
                    case "radio":
                        $checked = (isset($smof_data[$value['id']])) ? checked($smof_data[$value['id']], $option, false) : '';
                        foreach ($value['options'] as $option => $name) {
                            $output .= '<input class="of-input of-radio" name="' . $value['id'] . '" type="radio" value="' . $option . '" ' . checked($smof_data[$value['id']], $option, false) . ' /><label class="radio">' . $name . '</label><br/>';
                        }
                        break;

                    //checkbox option
                    case 'checkbox':
                        if (!isset($smof_data[$value['id']])) {
                            $smof_data[$value['id']] = 0;
                        }

                        $fold = '';
                        if (array_key_exists("folds", $value))
                            $fold = "fld ";

                        $output .= '<input type="hidden" class="' . $fold . 'checkbox of-input" name="' . $value['id'] . '" id="' . $value['id'] . '" value="0"/>';
                        $output .= '<input type="checkbox" class="' . $fold . 'checkbox of-input" name="' . $value['id'] . '" id="' . $value['id'] . '" value="1" ' . checked($smof_data[$value['id']], 1, false) . ' />';
                        break;

                    //multiple checkbox option
                    case 'multicheck':
                        (isset($smof_data[$value['id']])) ? $multi_stored = $smof_data[$value['id']] : $multi_stored = "";

                        foreach ($value['options'] as $key => $option) {
                            if (!isset($multi_stored[$key])) {
                                $multi_stored[$key] = '';
                            }
                            $of_key_string = $value['id'] . '_' . $key;
                            $output .= '<input type="checkbox" class="checkbox of-input" name="' . $value['id'] . '[' . $key . ']' . '" id="' . $of_key_string . '" value="1" ' . checked($multi_stored[$key], 1, false) . ' /><label class="multicheck" for="' . $of_key_string . '">' . $option . '</label><br />';
                        }
                        break;

                    // Color picker
                    case "color":
                        $default_color = '';
                        if (isset($value['std'])) {
                            $default_color = ' data-default-color="' . $value['std'] . '" ';
                        }
                        $output .= '<input type="text" name="' . $value['id'] . '" value="' . $smof_data[$value['id']] . '" class="hcms-color" id="' . $value['id'] . '"></input>';
                        $output .= '<span class="hcms-color-preview" style="background-color: ' . $smof_data[$value['id']] . '"></span>';
                        break;

                    //typography option	
                    case 'typography':

                        $typography_stored = isset($smof_data[$value['id']]) ? $smof_data[$value['id']] : $value['std'];

                        /* Font Size */

                        if (isset($typography_stored['size'])) {
                            $output .= '<div class="select_wrapper typography-size" original-title="Font size">';
                            $output .= '<select class="of-typography of-typography-size select" name="' . $value['id'] . '[size]" id="' . $value['id'] . '_size">';
                            for ($i = 9; $i < 20; $i++) {
                                $test = $i . 'px';
                                $output .= '<option value="' . $i . 'px" ' . selected($typography_stored['size'], $test, false) . '>' . $i . 'px</option>';
                            }

                            $output .= '</select></div>';
                        }

                        /* Line Height */
                        if (isset($typography_stored['height'])) {

                            $output .= '<div class="select_wrapper typography-height" original-title="Line height">';
                            $output .= '<select class="of-typography of-typography-height select" name="' . $value['id'] . '[height]" id="' . $value['id'] . '_height">';
                            for ($i = 20; $i < 38; $i++) {
                                $test = $i . 'px';
                                $output .= '<option value="' . $i . 'px" ' . selected($typography_stored['height'], $test, false) . '>' . $i . 'px</option>';
                            }

                            $output .= '</select></div>';
                        }

                        /* Font Face */
                        if (isset($typography_stored['face'])) {

                            $output .= '<div class="select_wrapper typography-face" original-title="Font family">';
                            $output .= '<select class="of-typography of-typography-face select" name="' . $value['id'] . '[face]" id="' . $value['id'] . '_face">';

                            $faces = array('arial' => 'Arial',
                                'verdana' => 'Verdana, Geneva',
                                'trebuchet' => 'Trebuchet',
                                'georgia' => 'Georgia',
                                'times' => 'Times New Roman',
                                'tahoma' => 'Tahoma, Geneva',
                                'palatino' => 'Palatino',
                                'helvetica' => 'Helvetica');
                            foreach ($faces as $i => $face) {
                                $output .= '<option value="' . $i . '" ' . selected($typography_stored['face'], $i, false) . '>' . $face . '</option>';
                            }

                            $output .= '</select></div>';
                        }

                        /* Font Weight */
                        if (isset($typography_stored['style'])) {

                            $output .= '<div class="select_wrapper typography-style" original-title="Font style">';
                            $output .= '<select class="of-typography of-typography-style select" name="' . $value['id'] . '[style]" id="' . $value['id'] . '_style">';
                            $styles = array('normal' => 'Normal',
                                'italic' => 'Italic',
                                'bold' => 'Bold',
                                'bold italic' => 'Bold Italic');

                            foreach ($styles as $i => $style) {

                                $output .= '<option value="' . $i . '" ' . selected($typography_stored['style'], $i, false) . '>' . $style . '</option>';
                            }
                            $output .= '</select></div>';
                        }

                        /* Font Color */
                        if (isset($typography_stored['color'])) {

                            $output .= '<div id="' . $value['id'] . '_color_picker" class="colorSelector typography-color"><div style="background-color: ' . $typography_stored['color'] . '"></div></div>';
                            $output .= '<input class="of-color of-typography of-typography-color" original-title="Font color" name="' . $value['id'] . '[color]" id="' . $value['id'] . '_color" type="text" value="' . $typography_stored['color'] . '" />';
                        }

                        break;

                    //border option
                    case 'border':

                        /* Border Width */
                        $border_stored = $smof_data[$value['id']];

                        $output .= '<div class="select_wrapper border-width">';
                        $output .= '<select class="of-border of-border-width select" name="' . $value['id'] . '[width]" id="' . $value['id'] . '_width">';
                        for ($i = 0; $i < 21; $i++) {
                            $output .= '<option value="' . $i . '" ' . selected($border_stored['width'], $i, false) . '>' . $i . '</option>';
                        }
                        $output .= '</select></div>';

                        /* Border Style */
                        $output .= '<div class="select_wrapper border-style">';
                        $output .= '<select class="of-border of-border-style select" name="' . $value['id'] . '[style]" id="' . $value['id'] . '_style">';

                        $styles = array('none' => 'None',
                            'solid' => 'Solid',
                            'dashed' => 'Dashed',
                            'dotted' => 'Dotted');

                        foreach ($styles as $i => $style) {
                            $output .= '<option value="' . $i . '" ' . selected($border_stored['style'], $i, false) . '>' . $style . '</option>';
                        }

                        $output .= '</select></div>';

                        /* Border Color */
                        $output .= '<div id="' . $value['id'] . '_color_picker" class="colorSelector"><div style="background-color: ' . $border_stored['color'] . '"></div></div>';
                        $output .= '<input class="of-color of-border of-border-color" name="' . $value['id'] . '[color]" id="' . $value['id'] . '_color" type="text" value="' . $border_stored['color'] . '" />';

                        break;

                    //images checkbox - use image as checkboxes
                    case 'images':

                        $i = 0;

                        $select_value = (isset($smof_data[$value['id']])) ? $smof_data[$value['id']] : '';

                        foreach ($value['options'] as $key => $option) {
                            $i++;

                            $checked = '';
                            $selected = '';
                            if (NULL != checked($select_value, $key, false)) {
                                $checked = checked($select_value, $key, false);
                                $selected = 'of-radio-img-selected';
                            }
                            $output .= '<span>';
                            $output .= '<input type="radio" id="of-radio-img-' . $value['id'] . $i . '" class="checkbox of-radio-img-radio" value="' . $key . '" name="' . $value['id'] . '" ' . $checked . ' />';
                            $output .= '<div class="of-radio-img-label">' . $key . '</div>';
                            $output .= '<img src="' . $option . '" alt="" class="of-radio-img-img ' . $selected . '" onClick="document.getElementById(\'of-radio-img-' . $value['id'] . $i . '\').checked = true;" />';
                            $output .= '</span>';
                        }

                        break;

                    //info (for small intro box etc)
                    case "info":
                        $info_text = $value['std'];
                        $output .= '<div class="of-info">' . $info_text . '</div>';
                        break;

                    //display a single image
                    case "image":
                        $src = $value['std'];
                        $output .= '<img src="' . $src . '">';
                        break;

                    //tab heading
                    case 'heading':
                        if ($counter >= 2) {
                            $output .= '</div>' . "\n";
                        }
                        //custom icon
                        $icon = '';
                        if (isset($value['icon'])) {
                            $icon = ' style="background-image: url(' . $value['icon'] . ');"';
                        }
                        $header_class = strtolower(title_url($value['name']));
                        $jquery_click_hook = "of-option-" . strtolower(title_url($value['name']));

                        $menu .= '<li class="' . $header_class . '"><a title="' . $value['name'] . '" href="#' . $jquery_click_hook . '"' . $icon . '>' . $value['name'] . '</a></li>';
                        $output .= '<div class="group" id="' . $jquery_click_hook . '"><h2>' . $value['name'] . '</h2>' . "\n";
                        break;

                    //drag & drop slide manager
                    case 'slider':
                        $output .= '<div class="slider"><ul id="' . $value['id'] . '">';
                        $slides = $smof_data[$value['id']];
                        $count = count($slides);
                        if ($count < 2) {
                            $oldorder = 1;
                            $order = 1;
                            //$output .= $this->optionsframework_slider_function($value['id'],$value['std'],$oldorder,$order);
                        } else {
                            $i = 0;
                            foreach ($slides as $slide) {
                                $oldorder = $slide['order'];
                                $i++;
                                $order = $i;
                                //$output .= $this->optionsframework_slider_function($value['id'],$value['std'],$oldorder,$order);
                            }
                        }
                        $output .= '</ul>';
                        $output .= '<a href="#" class="button slide_add_button">' . trans('add_new_slide') . '</a></div>';

                        break;

                    //drag & drop block manager
                    case 'sorter':

                        // Make sure to get list of all the default blocks first
                        $all_blocks = $value['std'];

                        $temp = array(); // holds default blocks
                        $temp2 = array(); // holds saved blocks

                        foreach ($all_blocks as $blocks) {
                            $temp = array_merge($temp, $blocks);
                        }

                        $sortlists = isset($data[$value['id']]) && !empty($data[$value['id']]) ? $data[$value['id']] : $value['std'];

                        foreach ($sortlists as $sortlist) {
                            $temp2 = array_merge($temp2, $sortlist);
                        }

                        // now let's compare if we have anything missing
                        foreach ($temp as $k => $v) {
                            if (!array_key_exists($k, $temp2)) {
                                $sortlists['disabled'][$k] = $v;
                            }
                        }

                        // now check if saved blocks has blocks not registered under default blocks
                        foreach ($sortlists as $key => $sortlist) {
                            foreach ($sortlist as $k => $v) {
                                if (!array_key_exists($k, $temp)) {
                                    unset($sortlist[$k]);
                                }
                            }
                            $sortlists[$key] = $sortlist;
                        }

                        // assuming all sync'ed, now get the correct naming for each block
                        foreach ($sortlists as $key => $sortlist) {
                            foreach ($sortlist as $k => $v) {
                                $sortlist[$k] = $temp[$k];
                            }
                            $sortlists[$key] = $sortlist;
                        }

                        $output .= '<div id="' . $value['id'] . '" class="sorter">';


                        if ($sortlists) {

                            foreach ($sortlists as $group => $sortlist) {

                                $output .= '<ul id="' . $value['id'] . '_' . $group . '" class="sortlist_' . $value['id'] . '">';
                                $output .= '<h3>' . $group . '</h3>';

                                foreach ($sortlist as $key => $list) {

                                    $output .= '<input class="sorter-placebo" type="hidden" name="' . $value['id'] . '[' . $group . '][placebo]" value="placebo">';

                                    if ($key != "placebo") {

                                        $output .= '<li id="' . $key . '" class="sortee">';
                                        $output .= '<input class="position" type="hidden" name="' . $value['id'] . '[' . $group . '][' . $key . ']" value="' . $list . '">';
                                        $output .= $list;
                                        $output .= '</li>';
                                    }
                                }

                                $output .= '</ul>';
                            }
                        }

                        $output .= '</div>';
                        break;

                    //background images option
                    case 'tiles':

                        $i = 0;
                        $select_value = isset($smof_data[$value['id']]) && !empty($smof_data[$value['id']]) ? $smof_data[$value['id']] : '';
                        if (is_array($value['options'])) {
                            foreach ($value['options'] as $key => $option) {
                                $i++;

                                $checked = '';
                                $selected = '';
                                if (NULL != checked($select_value, $option, false)) {
                                    $checked = checked($select_value, $option, false);
                                    $selected = 'of-radio-tile-selected';
                                }
                                $output .= '<span>';
                                $output .= '<input type="radio" id="of-radio-tile-' . $value['id'] . $i . '" class="checkbox of-radio-tile-radio" value="' . $option . '" name="' . $value['id'] . '" ' . $checked . ' />';
                                $output .= '<div class="of-radio-tile-img ' . $selected . '" style="background: url(' . $option . ')" onClick="document.getElementById(\'of-radio-tile-' . $value['id'] . $i . '\').checked = true;"></div>';
                                $output .= '</span>';
                            }
                        }

                        break;

                    //backup and restore options data
                    case 'backup':

                        $instructions = $value['desc'];
                        $backup = get_option('BACKUPS');
                        $init = get_option('smof_init');


                        if (!isset($backup['backup_log'])) {
                            $log = 'No backups yet';
                        } else {
                            $log = $backup['backup_log'];
                        }

                        $output .= '<div class="backup-box">';
                        $output .= '<div class="instructions">' . $instructions . "\n";
                        $output .= '<p><strong>' . trans('Last Backup : ') . '<span class="backup-log">' . $log . '</span></strong></p></div>' . "\n";
                        $output .= '<a href="#" id="of_backup_button" class="button" title="' . trans('backup') . ' Options">' . trans('backup') . ' Options</a>';
                        $output .= '<a href="#" id="of_restore_button" class="button" title="' . trans('restore') . ' Options">' . trans('restore') . ' Options</a>';
                        $output .= '</div>';

                        break;

                    //export or import data between different installs
                    case 'transfer':

                        $instructions = $value['desc'];
                        $output .= '<textarea id="export_data" rows="8">' . base64_encode(serialize($smof_data)) /* 100% safe - ignore theme check nag */ . '</textarea>' . "\n";
                        $output .= '<a href="#" id="of_import_button" class="button" title="Restore Options">Import Options</a>';

                        break;

                    // google font field
                    case 'select_google_font':
                        $output .= '<div class="select_wrapper">';
                        $output .= '<select class="select of-input google_font_select" name="' . $value['id'] . '" id="' . $value['id'] . '">';
                        foreach ($value['options'] as $select_key => $option) {

                            $output .= '<option value="' . $select_key . '" ' . selected((isset($smof_data[$value['id']])) ? $smof_data[$value['id']] : "", $option, false) . ' />' . $option . '</option>';
                        }
                        $output .= '</select></div>';

                        if (isset($value['preview']['text'])) {
                            $g_text = $value['preview']['text'];
                        } else {
                            $g_text = '0123456789 ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz';
                        }
                        if (isset($value['preview']['size'])) {
                            $g_size = 'style="font-size: ' . $value['preview']['size'] . ';"';
                        } else {
                            $g_size = '';
                        }
                        $hide = " hide";
                        if ($smof_data[$value['id']] != "none" && $smof_data[$value['id']] != "")
                            $hide = "";

                        $output .= '<p class="' . $value['id'] . '_ggf_previewer google_font_preview' . $hide . '" ' . $g_size . '>' . $g_text . '</p>';
                        break;

                    //JQuery UI Slider
                    case 'sliderui':
                        $s_val = $s_min = $s_max = $s_step = $s_edit = ''; //no errors, please

                        $s_val = stripslashes($smof_data[$value['id']]);

                        if (!isset($value['min'])) {
                            $s_min = '0';
                        } else {
                            $s_min = $value['min'];
                        }
                        if (!isset($value['max'])) {
                            $s_max = $s_min + 1;
                        } else {
                            $s_max = $value['max'];
                        }
                        if (!isset($value['step'])) {
                            $s_step = '1';
                        } else {
                            $s_step = $value['step'];
                        }

                        if (!isset($value['edit'])) {
                            $s_edit = ' readonly="readonly"';
                        } else {
                            $s_edit = '';
                        }

                        if ($s_val == '')
                            $s_val = $s_min;

                        //values
                        $s_data = 'data-id="' . $value['id'] . '" data-val="' . $s_val . '" data-min="' . $s_min . '" data-max="' . $s_max . '" data-step="' . $s_step . '"';
                        //html output
                        $output .= '<input type="text" name="' . $value['id'] . '" id="' . $value['id'] . '" value="' . $s_val . '" class="mini" ' . $s_edit . ' />';
                        $output .= '<div id="' . $value['id'] . '-slider" class="smof_sliderui" style="margin-left: 7px;" ' . $s_data . '></div>';

                        break;


                    //Switch option
                    case 'switch':
                        if (!isset($smof_data[$value['id']])) {
                            $smof_data[$value['id']] = 0;
                        }

                        $fold = '';
                        if (array_key_exists("folds", $value))
                            $fold = "s_fld ";

                        $cb_enabled = $cb_disabled = ''; //no errors, please
                        //Get selected
                        if ($smof_data[$value['id']] == 1) {
                            $cb_enabled = ' selected';
                            $cb_disabled = '';
                        } else {
                            $cb_enabled = '';
                            $cb_disabled = ' selected';
                        }

                        //Label ON
                        if (!isset($value['on'])) {
                            $on = "On";
                        } else {
                            $on = $value['on'];
                        }

                        //Label OFF
                        if (!isset($value['off'])) {
                            $off = "Off";
                        } else {
                            $off = $value['off'];
                        }

                        $output .= '<p class="switch-options">';
                        $output .= '<label class="' . $fold . 'cb-enable' . $cb_enabled . '" data-id="' . $value['id'] . '"><span>' . $on . '</span></label>';
                        $output .= '<label class="' . $fold . 'cb-disable' . $cb_disabled . '" data-id="' . $value['id'] . '"><span>' . $off . '</span></label>';

                        $output .= '<input type="hidden" class="' . $fold . 'checkbox of-input" name="' . $value['id'] . '" id="' . $value['id'] . '" value="0"/>';
                        $output .= '<input type="checkbox" id="' . $value['id'] . '" class="' . $fold . 'checkbox of-input main_checkbox" name="' . $value['id'] . '"  value="1" ' . checked($smof_data[$value['id']], 1, false) . ' />';

                        $output .= '</p>';

                        break;

                    // Uploader 3.5
                    case "upload":
                    case "media":

                        if (!isset($value['mod']))
                            $value['mod'] = '';

                        $u_val = '';
                        if ($smof_data[$value['id']]) {
                            $u_val = stripslashes($smof_data[$value['id']]);
                        }
                        $output .= $this->optionsframework_media_uploader_function($value['id'], $u_val, $value['mod']);


                        break;
                }


                if ($smof_output != "") {
                    $output .= $smof_output;
                    $smof_output = "";
                }

                //description of each option
                if ($value['type'] != 'heading') {
                    if (!isset($value['desc'])) {
                        $explain_value = '';
                    } else {
                        $explain_value = '<div class="explain">' . $value['desc'] . '</div>' . "\n";
                    }
                    $output .= '</div>' . $explain_value . "\n";
                    $output .= '<div class="clear"> </div></div></div>' . "\n";
                }
            } /* condition empty end */
        }



        $output .= '</div>';

        if ($smof_output != "") {
            $output .= $smof_output;
            $smof_output = "";
        }

        $html = '<div id="content">' . $output . '</div>';

        return $html;
    }

    /**
     * @desc main function
     * @param 
     * @return HTML
     */
    public function main() {
        $html = '';
        $html .= $this->printHead();
        $html .= '<div id="main">';

        $html .= $this->printMenu();
        $html .= $this->printContent();

        $html .= '<div class="clear"></div>';
        $html .= '</div>';
        $html .= $this->printFooter();

        return $html;
    }

    /**
     * Native media library uploader
     *
     * @uses get_theme_mod()
     *
     * @access public
     * @since 1.0.0
     *
     * @return string
     */
    public function optionsframework_media_uploader_function($id, $std, $mod) {

        if (empty($smof_data))
            $smof_data = $this->getOptionData(); //of_get_options();
        $data = $smof_data;
        $output = '';
        $uploader = '';
        $upload = "";
        if (isset($smof_data[$id]))
            $upload = $smof_data[$id];
        $hide = '';

        if ($mod == "min") {
            $hide = 'hide';
        }

        if ($upload != "") {
            $val = $upload;
        } else {
            $val = $std;
        }

        $uploader .= '<input class="' . $hide . ' upload of-input" name="' . $id . '" id="' . $id . '_upload" value="' . $val . '" />';

        //Upload controls DIV
        $uploader .= '<div class="upload_button_div">';
        //If the user has WP3.5+ show upload/remove button
        if (1) {
            $uploader .= '<span class="button hcms-upload-button" onclick="popitup(\'browser.php?view=grid&search-type=image&inputId=' . $id . '_upload\')" id="' . $id . '">' . trans('upload') . '</span>';

            if (!empty($upload)) {
                $hide = '';
            } else {
                $hide = 'hide';
            }
            $uploader .= '<span class="button remove-image hcms-upload-button ' . $hide . '" id="reset_' . $id . '" title="' . $id . '">' . trans('remove') . '</span>';
        }

        $uploader .='</div>' . "\n";

        //Preview
        $uploader .= '<div class="screenshot">';
        if (!empty($upload)) {
            $uploader .= '<a class="of-uploaded-image" href="' . base_url() . $upload . '" target="_blank">';
            $uploader .= '<img class="of-option-image image_review" id="image_' . $id . '" src="' . base_url() . $upload . '" alt="" />';
            $uploader .= '</a>';
        } else {
            $uploader .= '<a class="of-uploaded-image" href="" target="_blank" style="display:none">';
            $uploader .= '<img class="of-option-image image_review" id="image_' . $id . '" src=""  style="display:none" />';
            $uploader .= '</a>';
        }
        $uploader .= '</div>';
        $uploader .= '<div class="clear"></div>' . "\n";
        //var_dump($uploader);
        return $uploader;
    }

    /**
     * Drag and drop slides manager
     *
     * @uses get_theme_mod()
     *
     * @access public
     * @since 1.0.0
     *
     * @return string
     */
    public function optionsframework_slider_function($id, $std, $oldorder, $order) {
        $smof_data = $this->getOptionData(); //of_get_options();
        $data = $smof_data;

        $slider = '';
        $slide = array();
        if (isset($smof_data[$id]))
            $slide = $smof_data[$id];

        if (isset($slide[$oldorder])) {
            $val = $slide[$oldorder];
        } else {
            $val = $std;
        }

        //initialize all vars
        $slidevars = array('title', 'url');

        foreach ($slidevars as $slidevar) {
            if (!isset($val[$slidevar])) {
                $val[$slidevar] = '';
            }
        }

        //begin slider interface	
        if (!empty($val['title'])) {
            $slider .= '<li><div class="slide_header"><strong>' . stripslashes($val['title']) . '</strong>';
        } else {
            $slider .= '<li><div class="slide_header"><strong>Slide ' . $order . '</strong>';
        }

        $slider .= '<input type="hidden" class="slide of-input order" name="' . $id . '[' . $order . '][order]" id="' . $id . '_' . $order . '_slide_order" value="' . $order . '" />';

        $slider .= '<a class="slide_edit_button" href="#">' . trans('edit') . '</a></div>';

        $slider .= '<div class="slide_body">';

        $slider .= '<label>' . trans('alt_tag') . '</label>';
        $slider .= '<input class="slide of-input of-slider-title" name="' . $id . '[' . $order . '][title]" id="' . $id . '_' . $order . '_slide_title" value="' . stripslashes($val['title']) . '" />';

        $slider .= '<label>' . trans('image_url') . '</label>';
        $slider .= '<input class="upload slide of-input" name="' . $id . '[' . $order . '][url]" id="' . $id . '_' . $order . '_slide_url" value="' . $val['url'] . '" />';

        $slider .= '<div class="upload_button_div"><span onclick="popitup(\'browser.php?view=grid&search-type=image&inputId=' . $id . '_' . $order . '_slide_title\')" class="button" id="' . $id . '_' . $order . '">' . trans('upload') . '</span>';

        if (!empty($val['url'])) {
            $hide = '';
        } else {
            $hide = 'hide';
        }
        $slider .= '<span class="button remove-image ' . $hide . '" id="reset_' . $id . '_' . $order . '" title="' . $id . '_' . $order . '">' . trans('remove') . '</span>';
        $slider .='</div>' . "\n";
        $slider .= '<div class="screenshot">';
        if (!empty($val['url'])) {

            $slider .= '<a class="of-uploaded-image" href="' . $val['url'] . '">';
            $slider .= '<img class="of-option-image" id="image_' . $id . '_' . $order . '" src="' . $val['url'] . '" alt="" />';
            $slider .= '</a>';
        }
        $slider .= '</div>';

        $slider .= '<a class="slide_delete_button" href="#">' . trans('delete') . '</a>';
        $slider .= '<div class="clear"></div>' . "\n";

        $slider .= '</div>';
        $slider .= '</li>';

        return $slider;
    }

}
// end class