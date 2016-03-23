<?php if(!defined('ALLOW_ACCESS')) exit('No direct script access allowed');


// define DIRECTORY_SEPARATOR
define('DS',DIRECTORY_SEPARATOR); 

// define ROOT_PATH
define('ROOT_PATH',dirname(__FILE__).DS);

// define ROOT PATH
define("USING_MEMCACHE","0");

// define ROOT PATH
define("INC_PATH",ROOT_PATH.'inc'.DS);

// define Module PATH
define("MODULE_PATH",ROOT_PATH.'modules'.DS);
    
// calculator query in class database
define("SHOW_QUERY_INFO","off");    

// define string encode password
define('PASS_ENDCODE','HANHCMS');


// define string encode password
define('FORGOTPASS_ENDCODE','hanhcms');


// max width and height images
define('MAX_SIZE_IMAGE', '1280x1024');

// DEBUG
define("CMS_DEBUG", true);  



