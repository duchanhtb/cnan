<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
class Input {

    function Input() { // nothign to do
    }

    public static function get($varname, $type = '', $vartext = '', $method = '') {
        global $HTTP_POST_VARS, $HTTP_GET_VARS, $HTTP_COOKIE_VARS, $_REQUEST;
        $value = "";

        if (!empty($_POST[$varname])) {
            $value = $_POST[$varname];
        } else if (!empty($_GET[$varname])) {
            $value = $_GET[$varname];
        } else if (!empty($_REQUEST[$varname])) {
            $value = $_REQUEST[$varname];
        } else if (!empty($_FILES[$varname])) {
            $value = $_FILES[$varname];
        }
        if (!$value)
            $value = $vartext;
        $value = @trim($value);


        switch ($type) {
            case 'txt':
                $value = Input::def($value);
                break;

            case 'con':
                $value = Input::content($value);
                break;
            case 'int':
                $value = Input::cint($value);
                break;
            case 'sql':
                $value = Input::csql($value);
                break;
            case 'big':
                $value = Input::cbigint($value);
                break;
            case 'dte':
                $value = Input::cdate($value);
                break;
            default:
                $value = Input::cstr($value);
        }
        return $value;
    }

    public static function cstr($strval) {
        if (get_magic_quotes_gpc() == 0)
            $strval = addslashes($strval);

        if (strlen($strval))
            $strval = htmlspecialchars($strval);

        $strval = str_replace("script", "", $strval);

        return $strval;
    }

    public static function cdate($strval) {
        if (get_magic_quotes_gpc() == 0)
            $strval = addslashes($strval);

        if (strlen($strval))
            $strval = htmlspecialchars($strval);
        return date("Y-m-d", strtotime($strval));
    }

    public static function def($strval) {
        if (get_magic_quotes_gpc() == 0)
            $strval = addslashes($strval);

        $strval = htmlspecialchars($strval);

        return $strval;
    }

    public static function content($strval) {
        return $strval;
    }

    public static function csql($strval) {
        if (get_magic_quotes_gpc() == 0)
            $strval = addslashes($strval);
        return $strval;
    }

    public static function cint($intval) {
        if (get_magic_quotes_gpc() == 0)
            $intval = addslashes($intval);

        $intval = (int) $intval;

        return $intval;
    }

    public static function cbigint($intval) {
        $intval = str_replace(",", "", $intval);
        if (is_numeric(trim($intval)))
            return $intval;
        return 0;
    }

}