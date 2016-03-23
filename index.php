<?php

/**
 * @author duchanh
 * @copyright 2012
 */
/* * ******* load file config ********* */
define('ALLOW_ACCESS', TRUE);
include("config.php");

$s_time = microtime(true);
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
    ob_start("ob_gzhandler");
else
    ob_start('sanitize_output');


/* * ******* Run cms ********* */
$cms = new cms();
$cms->run();


if (isset($_REQUEST['query'])) {
    $_SESSION['query'] = $_REQUEST['query'];
}



$e_time = microtime(true);
$total_time = ($e_time - $s_time);
if (SHOW_QUERY_INFO == 'on' || (isset($_SESSION['query']) && $_SESSION['query'] == 1)) {
    echo '<pre>';
    foreach ($oDb->listQuery as $query) {
        echo '<p style="text-align:left">' . trim($query) . '</p>';
    }
    echo '<p style="color:red">Time executed: <strong>' . $total_time . '</strong>s</p>';
    echo '</pre>';
}
if ($oDb)
    @$oDb->close();
