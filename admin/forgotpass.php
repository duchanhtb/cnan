<?php
/**
 * @author duchanh
 * @copyright 2012
 */
define('ALLOW_ACCESS', TRUE);
include('config.php');


$email = isset($_POST['txtEmail']) ? addslashes($_POST['txtEmail']) : '';
$code = isset($_POST['security_code']) ? addslashes($_POST['security_code']) : '';
$msg = "";

if (isset($_SESSION['numError']) && ((int) $_SESSION['numError'] > 2) && ($_SESSION['security_code'] != $code)) {
    $msg = '<p class="msg info">' . trans('msg_wrong_captcha');
} else {
    if ($email) {
        $ok = resetPass($email);
        if ($ok) {
            $_SESSION['numError'] = 0;
            $msg = '<p class="msg info">' . trans('msg_forgotpass_email', array($email)) . '</p>';
        } else {
            $msg = '<p class="msg error">' . trans('msg_email_not_exists') . '</p>';
            $_SESSION['numError'] = isset($_SESSION['numError']) ? ((int) $_SESSION['numError'] + 1) : 1;
        }
    }
}

function resetPass($email) {
    global $mt_prefix, $oDb;
    $admin_rs = $oDb->query("select * from " . $mt_prefix . "admin where `email`='" . addslashes($email) . "'");
    if ((int) $oDb->numRows($admin_rs) > 0) {
        $host = $_SERVER['HTTP_HOST'];
        $admin = $oDb->fetchArray($admin_rs);
        $id = $admin['id'];
        $email = $admin['email'];
        $username = $admin['name'];
        $link_getpass = admin_url() . "resetpass.php?id=" . alphaID($id, false, 5) . '&hash=' . md5('HCMS_' . $email);
        $content = "<h3>" . trans('hello') .": ". $username."!</h3><p style='line-height: 20px; font-size: 14px;'>" . trans('msg_mail_content_link', array($host)) . "</p>";
        $content.= "<a href=" . $link_getpass . ">$link_getpass</a>";   
        
        $to = array(
            'name' => $admin['name'],
            'email' => $email
        );


        try {
            $subject = trans('msg_mail_content_info') . ' ' . $host;
            $content = @eregi_replace("[\]", '', $content);
            sendMail($to, false, $subject, $content);
         
        } catch (Exception $e) {
            return $e;
        }
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
        <meta name="generator" content="H.CMS v3.5.00; Copyright 2011-2014." />
        <title><?php echo $admin_title; ?> - <?php echo trans('forgot_password'); ?> - Administration</title>
        <link rel="stylesheet" type="text/css" href="css/core.css" />
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
                    <?php echo $msg; ?>   
                    <ul>
                        <li>
                            <label for="name"><?php echo trans('email_address')?></label>
                            <input name="txtEmail" type="text" class="input" />
                        </li>
                        <?php if (isset($_SESSION['numError']) && (int) $_SESSION['numError'] > 2) { ?>
                        <li>
                            <label for="capcha"></label>
                            <input name="security_code" type="text" style="width: 180px; margin-left: 10px;" class="input" />
                            <img title="<?php echo trans('reload_captcha'); ?>" src="captcha/captcha.php?width=80&height=30&characters=4" id="imageCaptcha" style="float:left;" />
                        </li>
                        <?php } ?>
                        <li><input type="submit" value="<?php echo trans('reset_password'); ?>" name="btnSubmit" class="forgot" /></li>
                    </ul>
                    <p><a title="<?php echo trans('login'); ?>" href="login.php"><?php echo trans('login'); ?>? </a></p>
                </form>
            </div>
            <!--end loginWrapper-->
            <p class="backToWeb"><a title="Back to Website" href="<?php echo base_url(); ?>"><?php echo trans('back_to_website') ?></a></p>
            <p class="copyright">&copy; 2011-2014 <a href="http://nguyenduchanh.com">NguyenDucHanh.com</a>. H.CMS 3.5.00 build 110225.</p>
        </div>
        <!--end login-->
    </body>
</html>