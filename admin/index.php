<?php

/**
 * @author duchanh
 * @copyright 2012
 */
$start_time = microtime(true);
define('ALLOW_ACCESS', TRUE);
include_once('config.php');
include_once('CmsAdmin.php');

// check permission
if (((int) $_SESSION['admin']['id'] == 0) || ((int) $_SESSION['admin']['level'] < 1)) {
    $ref = curPageURL();
    $_SESSION['ref'] = $ref;
    @header("Location: login.php");
    exit;
}

$CmsAdmin = new CmsAdmin();
$CmsAdmin->run();
$end_time = microtime(true);





