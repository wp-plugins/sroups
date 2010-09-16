<?php
  // Define path to application directory
  defined('APP_PATH')
          || define('APP_PATH', realpath(dirname(__FILE__) . '/../..'));

  // Ensure library/ is on include_path
  set_include_path(implode(PATH_SEPARATOR, array(
          APP_PATH,
          realpath(dirname(__FILE__) . '/library'),
          get_include_path(),
  )));
  
  require_once('../config/Bootstrap.php');

  $bootstrap = new Bootstrap();
