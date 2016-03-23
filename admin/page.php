<?php
/**
 * @author duchanh
 * @copyright 2012
 */
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

$id = Input::get('id', 'txt', 'home');
$cms = new Cms();
$miniPage = new Page();

$miniPage->read($id);
$page = $miniPage->name;
$pageInfo = $miniPage->getPageInfoAdmin($page);
$position = json_decode($pageInfo['position'], true);

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <title><?php echo $admin_title; ?> - Module - Administration</title>
        <script type="text/javascript" src="<?php echo admin_url(); ?>js/jquery-1.7.min.js"></script>
        <script type="text/javascript" src="<?php echo admin_url(); ?>js/cookie.js"></script>        
        <script type="text/javascript" src="<?php echo admin_url(); ?>js/custom.page.js"></script>
        <script type="text/javascript" src="<?php echo admin_url(); ?>js/lang.php"></script>
        <script src="<?php echo admin_url(); ?>js/jquery-ui.js"></script>
        <link rel="stylesheet" href="<?php echo admin_url(); ?>css/custom.page.css" />
        <style>
            #update-page-loading{
                display:none; 
                position: absolute; 
                width: 300px; 
                height: 80px; 
                background-color:#000; 
                color:#fff; 
                text-align:  center; 
                line-height: 80px; 
                font-size: 20px; 
                z-index:9999999;
                border-radius: 20px !important;
                -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=60)" !important;
                filter: alpha(opacity=60) !important;
                -moz-opacity:0.6 !important;
                -khtml-opacity: 0.6 !important;
                opacity: 0.6 !important;
            }
        </style>
    </head>
    <body>
        <div id="update-page-loading">Updating...</div>
        <div class="cms-module">
            <div class="cms-module-left">
                <ul class="cms-pos">
                    <h2 class="header"><a href="<?php echo admin_url() . 'index.php?f=page' ?>"><?php echo trans('admin_page'); ?></a></h2>
                    <li class="top">
                        <img id="img-loading" src="<?php echo admin_url(); ?>images/loading.gif" />
                        <button id="hcmsSavePosition" onclick="savePosition('<?php echo $page; ?>')"><?php echo trans('save'); ?></button>
                    </li>
                    <?php
                    $arrPost = $cms->getPositionPage($page);
                    foreach ($arrPost as $key => $pos) {
                        ?>
                        <li class="post-name">
                            <h3><?php echo $pos; ?></h3>
                            <div class="wrap-module">
                                <ul class="module" data-pos="<?php echo 'pos_' . $pos; ?>" >
                                    <?php
                                    if (isset($position['pos_' . $pos])) {
                                        $arrPostModule = $position['pos_' . $pos];
                                        foreach ($arrPostModule as $md) {
                                            echo '<li data-module="' . $md . '" class="module-name pos_' . $page . '-' . $md . '"><h4>' . $md . '<a href="#" onclick="deleteModule(this)">' . trans('delete') . '?</a></h4></li>';
                                        }
                                    }
                                    ?>
                                </ul>
                                <div class="add-module"><button class="module-1"><?php echo trans('add'); ?> module</button></div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="list-module" id="list-module">
                <div class="module-header">
                    <input type="text" name="seach_module" id="seach_module" value="" placeholder="<?php echo trans('endter_module_name'); ?>" onkeyup="seachModule()" />
                </div>
                <ul class="module-list">
                    <?php
                    $arrModule = list_module();
                    foreach ($arrModule as $module) {
                        ?>
                        <li data-module="<?php echo $module; ?>" class="<?php echo $module; ?>"><h3><a href="#"><?php echo $module; ?></a></h3></li>
                    <?php } ?>
                </ul>
            </div>

            <div class="cms-module-content">
                <iframe src="<?php echo base_url() . 'index.php?page=' . $page . '&tpl=1'; ?>" id="cms-iframe"></iframe>
            </div>
        </div>
        <button class="menu-toggle" id="menu-toggle">
            <img height="20" src="<?php echo admin_url(); ?>images/media_icon_prev.png" />
        </button>
    </body>
</html>