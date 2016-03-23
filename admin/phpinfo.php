<?php
date_default_timezone_set('Asia/Saigon');
$code = @$_REQUEST['code'];
$code_compare = md5('HCMS'.date('Y-m-d H', time()));
if($code != $code_compare){
    header("location: index.php?f=restrict");
}
phpinfo();
