<?php

/**
 * Callback for Opauth
 * 
 * This file (callback.php) provides an example on how to properly receive auth response of Opauth.
 * 
 * Basic steps:
 * 1. Fetch auth response based on callback transport parameter in config.
 * 2. Validate auth response
 * 3. Once auth response is validated, your PHP app should then work on the auth response 
 *    (eg. registers or logs user in to your site, save auth data onto database, etc.)
 * 
 */
/**
 * Define paths
 */
define('ALLOW_ACCESS', TRUE);
define('CONF_FILE', dirname(__FILE__) . '/' . 'opauth.conf.php');
define('OPAUTH_LIB_DIR', dirname(__FILE__) . '/lib/Opauth/');
define('CMS_DIR', dirname(dirname(__FILE__)) . '/');


require CONF_FILE;

require CMS_DIR . 'config.php';

/**
 * Load config
 */
if (!file_exists(CONF_FILE)) {
    trigger_error('Config file missing at ' . CONF_FILE, E_USER_ERROR);
    exit();
}
require CONF_FILE;

/**
 * Instantiate Opauth with the loaded config but not run automatically
 */
require OPAUTH_LIB_DIR . 'Opauth.php';
$Opauth = new Opauth($config, false);


/**
 * Fetch auth response, based on transport configuration for callback
 */
$response = null;

switch ($Opauth->env['callback_transport']) {
    case 'session':
        @session_start();
        $response = $_SESSION['opauth'];
        unset($_SESSION['opauth']);
        break;
    case 'post':
        $response = unserialize(base64_decode($_POST['opauth']));
        break;
    case 'get':
        $response = unserialize(base64_decode($_GET['opauth']));
        break;
    default:
        echo '<strong style="color: red;">Error: </strong>Unsupported callback_transport.' . "<br>\n";
        break;
}

/**
 * Check if it's an error callback
 */
if (array_key_exists('error', $response)) {
    echo '<strong style="color: red;">Authentication error: </strong> Opauth returns error auth response.' . "<br>\n";
    die;
}

/**
 * Auth response validation
 * 
 * To validate that the auth response received is unaltered, especially auth response that 
 * is sent through GET or POST.
 */ else {
    if (empty($response['auth']) || empty($response['timestamp']) || empty($response['signature']) || empty($response['auth']['provider']) || empty($response['auth']['uid'])) {
        echo '<strong style="color: red;">Invalid auth response: </strong>Missing key auth response components.' . "<br>\n";
    } elseif (!$Opauth->validate(sha1(print_r($response['auth'], true)), $response['timestamp'], $response['signature'], $reason)) {
        echo '<strong style="color: red;">Invalid auth response: </strong>' . $reason . ".<br>\n";
    } else {
        SetSession('social', 'google');
        
        $socialInfo = $response['auth']['info'];
        $socialRaw = $response['auth']['raw'];
        $social_email = $socialInfo['email'];
        
        $User = new User();
        $checkExists = $User->getUserByEmail($social_email);
        if (is_array($checkExists) && count($checkExists) > 0) {
            SetSession('user', $checkExists);
            echo '
                <script>window.close();
                    if (window.opener && !window.opener.closed) {
                        window.opener.location.reload();
                    } 
                </script>';
            exit;
        }

        foreach ($User->fields as $field) {
            $User->$field = '';
        }

        $User->username = $socialInfo['nickname'];
        $User->fullname = $socialInfo['name'];
        $User->email = $socialInfo['email'];
        $User->avatar = $socialInfo['image'];
        $User->address = $socialInfo['location'];
        $User->gender = ($socialRaw['gender'] == 'male') ? 1 : 0;
        $User->date_register = date('Y-m-d H:i', time());
        $User->link_facebook = $socialInfo['urls']['facebook'];
        $User->link_googleplus = $socialInfo['urls']['google'];
        $User->link_twitter = $socialInfo['urls']['twitter'];
        $User->user_type = strtolower($response['auth']['provider']);
        $User->social_id = $socialRaw['id'];
        $User->status = 1;
        $user_id = $User->insert();
        
        
        
        $User->updateInfoByIP($user_id);

        $newUser = $User->getUserById($user_id);
        SetSession('user', $newUser);
        echo '
            <script>window.close();
                if (window.opener && !window.opener.closed) {
                    window.opener.location.reload();
                } 
            </script>';
        
        // reload cache 
        cacheSetting(true);
        exit;
    }
}
