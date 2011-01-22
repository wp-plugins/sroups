<?php

//error_reporting(E_ALL || E_STRICT);
//ini_set('display_errors', '1');

/** Include the bootstrap for setting up WordPress environment */
require_once( dirname(__FILE__) . '/../../../../wp-load.php' );
require_once( ABSPATH . '/wp-includes/pluggable.php' );
require_once(ABSPATH . '/wp-includes/post.php' );
require_once(ABSPATH . '/wp-admin/includes/image.php' );

require_once('api/config/Bootstrap.php');
require_once('api/config/utils.php');
require_once(APP_PATH . '/SOS/Factory.php');
require_once('services/Services.php');


$bootstrap = new Bootstrap();
$bootstrap->setDebug(false);
Bootstrap::setBaseUrl(get_bloginfo('url') . '/wp-content/plugins/sroups/rest/');

try {
    if (!isset($_GET['sauth_sig']))
        die('Authentication Failed');
    
    $signature = get_option('sroups_sig');
    if(empty ($signature)) die('Authentication Failed');

    SOS_Factory::setConsumerSecret(getConsumerSecretFromSignature($signature));

    SOS_Factory::setDomain($_SERVER['HTTP_HOST']);
    SOS_Factory::setPersonService(new PersonHandler());
    SOS_Factory::setMediaItemService(new MediaItemsHandler());
    echo $bootstrap->dispatch();
} catch (Exception $ex) {
    echo $ex->getMessage();
}

