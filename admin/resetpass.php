<?php
/**
 * @author duchanh
 * @copyright 2012
 */
define('ALLOW_ACCESS', TRUE);
include('config.php');


$hash = Input::get('hash', 'txt', '');
$id = Input::get('id', 'txt', '');
if ($id && $hash) {
    global $mt_prefix, $oDb;
    $id = alphaID($id, true, 5);
    $admin_rs = $oDb->query("select * from " . $mt_prefix . "admin where `id`= $id");
    $admin = $oDb->fetchArray($admin_rs);
    $code_compare = md5('HCMS_' . $admin['email']);

    if ($hash == $code_compare) {
        $_SESSION['hcms_resetpass'] = $admin;
        redirect("resetpass.php");
    } else {
        echo 'Keep me away';
        exit;
    }
} else if (!isset($_SESSION['hcms_resetpass'])) {
    exit;
}

if ($_POST) {
    if ($_POST['txtNewspas'] && $_POST['txtRenewspas']) {
        $admin = $_SESSION['hcms_resetpass'];
        $password = $_POST['txtNewspas'];
        $repassword = $_POST['txtRenewspas'];
        $msg = '';
        if ($password != $repassword) {
            $msg = '<p class="msg error">' . trans('msg_wrong_password') . '</p>';
        } else {
            $id = $admin['id'];
            global $mt_prefix, $oDb;
            $pass = md5(md5(md5($password)));
            $admin_rs = $oDb->query("UPDATE " . $mt_prefix . "admin SET `pass`='" . $pass . "' WHERE id = $id");
            $msg = '<p class="msg info">' . trans('msg_change_passsword_ss') . '</p>';
            // delete session
            unset($_SESSION['hcms_resetpass']);
        }
    } else {
        $msg = '<p class="msg error">' . trans('msg_empty_password') . '</p>';
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="Author" content="nguyenduchanh.com" />
        <meta name="generator" content="H.CMS v3.5.00; Copyright 2011-<?php echo date('Y'); ?>." />
        <title><?php echo $admin_title; ?> <?php echo trans('get_new_password'); ?> - Administration</title>
        <link rel="stylesheet" type="text/css" href="css/core.css" />
        <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png">
            <script src="js/jquery.js" type="text/javascript"></script>
            <style>
                .forgotpass{
                    text-decoration: none !important;
                }
                .forgotpass:hover{
                    text-decoration: underline !important;
                }
            </style>
    </head>
    <body>
        <!--begin login-->
        <div id="login">
            <!--begin loginWrapper-->
            <div class="loginWrapper clearfix">
                <form action="" method="post" name="login" id="form">
                    <?php if (isset($msg)) echo $msg; ?>
                    <ul>
                        <li>
                            <label for="txtNewspas"><?php echo trans('news_password'); ?></label>
                            <input name="txtNewspas" type="password" class="input" />
                        </li>
                        <li>
                            <label for="txtRenewspas"><?php echo trans('repassword'); ?></label>
                            <input name="txtRenewspas" type="password" class="input" />
                        </li>

                        <?php if (isset($_SESSION['numError']) && (int) $_SESSION['numError'] > 2) { ?>
                            <li>
                                <label for="capcha"></label>
                                <input name="security_code" type="text" style="width: 180px; margin-left: 10px;" class="input" />
                                <img title="<?php echo trans('reload_captcha'); ?>" src="captcha/captcha.php?width=80&height=30&characters=4" id="imageCaptcha" style="float:left;" />
                            </li>
                        <?php } ?>
                        <li><input type="submit" value="<?php echo trans('submit'); ?>" name="btnSubmit" class="forgot" /></li>
                    </ul>
                    <p><a title="<?php echo trans('submit'); ?>" href="login.php"><?php echo trans('login'); ?>? </a></p>
                </form>
            </div>
            <!--end loginWrapper-->
            <p class="backToWeb"><a title="Back to Website" href="<?php echo base_url(); ?>">Back to Website</a></p>
            <p class="copyright">&copy; 2011-<?php echo date('Y'); ?> <a href="http://nguyenduchanh.com">NguyenDucHanh.com</a>. H.CMS 3.5.00 build 110225.</p>
        </div>
        <!--end login-->
    </body>
</html>