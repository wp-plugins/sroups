<?php
  die('This file should be overriden in the production environment');
  /*
  error_reporting(0);
  ini_set('display_errors', '0');

  require_once('config/Bootstrap.php');
  require_once(APP_PATH . '/SOS/Factory.php');

  require_once('PersonHandler.php');
  require_once('MediaItemHandler.php');

  $bootstrap = new Bootstrap();
  $bootstrap->setDebug(true);
  $bootstrap->useQueryString(false);
  $bootstrap->setBaseUrl("http://192.168.1.209:88/sroups.sos/");
  try {
    SOS_Factory::setConsumerSecret("742a980d88544278bda294c3555f22f171f728547f7341acb6ca7211cb9a9a33");
    SOS_Factory::setDomain("http://www.example.com");
    SOS_Factory::setPersonService(new PersonHandler());
    SOS_Factory::setMediaItemService(new MediaItemHandler());
    echo $bootstrap->dispatch();
  }catch(Exception $ex) {   
    if($bootstrap->isDebug()) {
      echo 'ERROR: ' . $ex->getMessage();
      echo '<hr />Trace:<br /><ul>';
      foreach($ex->getTrace() as $msg) {
        echo '<li>' . $msg['file'] . ' -> ' . $msg['function'] . '<br />' . '</li>';
      }
      echo '</ul>';
      echo $bootstrap->info();

      echo '<hr />';
    } else {
      echo $ex->getMessage();
    }
  }
*/