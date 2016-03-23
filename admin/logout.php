<?php

/**
 * @author duchanh
 * @copyright 2012
 */
define('ALLOW_ACCESS', TRUE);
include('config.php');
$_SESSION['admin'] = array();
@header("Location: login.php");