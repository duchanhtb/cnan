<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');


/* Load core class
  ---------------------------------------------------- */
$autoLoad = array(
    'core/Base.class.php',
    'core/Cms.class.php',
    'core/CMysqlDB.class.php',
    'core/Images.class.php',
    //'core/Memcache.class.php',
    'core/Module.class.php',
    'core/Xtemplate.class.php',
    'core/Input.class.php',
    'core/Images.class.php',
    'core/Media.class.php',
    'core/CFile.class.php',
    'core/cmsOptions.class.php',
    'core/DB.class.php'
);

foreach ($autoLoad as $path) {
    include_once(INC_PATH . $path);
}


$modelsDir = ROOT_PATH . 'classes';
$arrModels = CFile::getFileDir($modelsDir, '.php');
foreach ($arrModels as $model) {
    $model_path = $modelsDir . '/' . $model;
    if (!file_exists($model_path))
        continue;
    include_once($model_path);
}