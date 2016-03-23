<?php

define('ALLOW_ACCESS', TRUE);
include_once(dirname(dirname(dirname(__FILE__))).'/config.php');

header("content-type: application/javascript");
$lang = getLanguage();
echo 'var lang=' . json_encode($lang);
