<?php

/**
 * @author duchanh
 * @copyright 2014
 */
define('ALLOW_ACCESS', TRUE);
include_once('config.php');
include_once('CmsAdmin.php');

if (((int) $_SESSION['admin']['id'] == 0) || ((int) $_SESSION['admin']['level'] < 1)) {
    $ref = curPageURL();
    $_SESSION['ref'] = $ref;
    @header("Location: login.php");
}

$CmsAdmin = new CmsAdmin();
$Media = new Media();

$html = '';

$html .= $CmsAdmin->adminHeader();
$html .= $Media->showList();
$html .= $CmsAdmin->adminFooter();

echo $html;


