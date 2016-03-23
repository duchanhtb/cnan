<?php

define('ALLOW_ACCESS', TRUE);
include('config.php');

$from = array(
    'name'  => 'Hanh Nguyen',
    'email' => 'hancoltech@gmail.com'
);

$to = array(
  'name'    => 'Tesst' ,
  'email'  => 'hanhcoltech@gmail.com'
);

$subject = 'Test send mail';
$content = 'This is content send mail ';


$ok = sendMail($from, $to, false, $subject, $content);
var_dump($ok.'aa');