<?php
/**
 * @author duchanh
 * @copyright 2012
 */
define('ALLOW_ACCESS', TRUE);
include('config.php');

$user = isset($_POST['txtName']) ? addslashes($_POST['txtName']) : '';
$pass = isset($_POST['pwdPass']) ? addslashes($_POST['pwdPass']) : '';
$code = isset($_POST['security_code']) ? addslashes($_POST['security_code']) : '';
$msg = "";
if (isset($_SESSION['numError']) && ((int) $_SESSION['numError'] > 2) && ($_SESSION['security_code'] != $code)) {
    $msg = '<p class="msg error">' . trans('msg_wrong_captcha') . '</p>';
} else {
    if ($user && $pass) {
        $checkLogin = LoginAdmin($user, $pass);

        if ($checkLogin) {
            $_SESSION['numError'] = 0;
            $ref = isset($_SESSION['ref']) ? $_SESSION['ref'] : NULL;
            if ($ref && strrpos($ref, 'login.php') == false) {
                header("Location: $ref");
            } else {
                header("Location: index.php");
            }

            $msg = "";
        } else {
            $msg = '<p class="msg error">' . trans('msg_login_fail') . '</p>';
            $_SESSION['numError'] = isset($_SESSION['numError']) ? $_SESSION['numError'] + 1 : 1;
        }
    }
}

function LoginAdmin($user, $pass_in) {
    global $mt_prefix, $oDb;
    $pass = md5(md5(md5($pass_in)));
    $admin_rs = $oDb->query("SELECT * FROM " . $mt_prefix . "admin WHERE `name`='" . addslashes($user) . "' AND `pass`='" . $pass . "'");
    if ((int) $oDb->numRows($admin_rs) > 0) {
        $admin = $oDb->fetchArray($admin_rs);
        $_SESSION['admin']['id'] = (int) $admin['id'];
        $_SESSION['admin']['name'] = $admin['name'];
        $_SESSION['admin']['level'] = (int) $admin['level'];
        $_SESSION['admin']['last_login_time'] = $admin['last_login_time'];

        // update time and ip login
        $last_login_time = date("Y-m-d H:i:s", time());
        $last_login_ip = getUserIp();
        $sql = " UPDATE " . $mt_prefix . "admin SET `last_login_time` = '$last_login_time', `last_login_ip` = '$last_login_ip' WHERE `id` = ".$admin['id'];
        $oDb->query($sql);

        return true;
    } else {
        return false;
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="Author" content="nguyenduchanh.com" />
        <meta name="generator" content="H.CMS v3.5.00; Copyright 2011-<?php echo date('Y');?>." />
        <title><?php echo $admin_title; ?> Login Administration</title>
        <link rel="stylesheet" type="text/css" href="css/core.css" />
        <script src="js/jquery.js" type="text/javascript"></script>
        <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png">
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
                <form action="" method="post" name="frm" id="form">
                    <?php echo $msg; ?>   
                    <ul>
                        <li>
                            <label for="name"><?php echo trans('username')?></label>
                            <input type="text" class="input" name="txtName" />
                        </li>
                        <li>
                            <label for="password"><?php echo trans('password')?></label>
                            <input id="password" value="******" name="pwdPass" type="password" class="input" onfocus="this.value = ''" />
                        </li>
                        <?php if (isset($_SESSION['numError']) && (int) $_SESSION['numError'] > 2) { ?>
                            <li>
                                <label for="capcha"></label>
                                <input name="security_code" type="text" style="width: 180px; margin-left: 10px;" class="input" />
                                <img title="<?php echo trans('reload_captcha'); ?>" src="captcha/captcha.php?width=80&height=30&characters=4" id="imageCaptcha" style="float:left;" />
                            </li>
                        <?php } ?>
                        <li><input type="submit" value="Login" name="btnSubmit" class="submit" /></li>
                    </ul>
                    <p><a title="Forgot your password" href="forgotpass.php"><?php echo trans('forgot_password'); ?>?</a></p>
                </form>
            </div>
            <!--end loginWrapper-->
            <p class="backToWeb"><a title="Back to Website" href="<?php echo base_url(); ?>"><?php echo trans('back_to_website') ?></a></p>
            <p class="copyright">&copy; 2011-<?php echo date('Y');?> <a href="http://nguyenduchanh.com">NguyenDucHanh.com</a>. H.CMS 3.5.00 build 110225.</p>
        </div>
        <!--end login-->
    </body>
</html>