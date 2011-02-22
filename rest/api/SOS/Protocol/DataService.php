<?php
/*
 * Please see Sroups License file
 */
  require_once(APP_PATH . '/SOS/Protocol/Abstract.php');
  require_once(APP_PATH . '/SOS/Request/People.php');
  require_once(APP_PATH . '/SOS/Request/MediaItems.php');
  require_once(APP_PATH . '/SOS/Request/Environment.php');
  require_once(APP_PATH . '/SOS/Service/NotImplementedException.php');
  require_once(APP_PATH . '/SOS/SAuth.php');
  require_once(APP_PATH . '/SOS/Request/Method.php');


  class SOS_Protocol_DataService extends SOS_Protocol_Abstract
  {

    public function handle() {

      // SAuth operations
      $sig = (isset($_GET['sauth_sig'])) ? $_GET['sauth_sig'] : null;
      $timestamp = (isset($_GET['sauth_ts'])) ? $_GET['sauth_ts'] : null;
      $originalUri = $this->getAppUri();

      $sauth = new SOS_Sauth($originalUri, $timestamp, $sig);
      $sauth->authenticate(); // throws exception if fails

      $protocolParam = strtolower($this->getProtocolParam());

      $res = new stdClass();
      $obj = "";
      if($protocolParam == 'people.get' || $protocolParam == 'people') {
        $req = new SOS_Request_People($this);
        $obj = $req->execute(false);

        $res->entry = json_decode($obj);

      } elseif(strtolower ($protocolParam) == 'mediaitems') {
        $req = new SOS_Request_MediaItems($this);
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $obj = $req->execute(SOS_Request_Method::MEDIAITEMS_ADDITEM, false);
        } else {
            $obj = $req->execute(SOS_Request_Method::MEDIAITEMS_GETITEMS, false);
        }


        $res = new stdClass();
        $res->entry = json_decode($obj);

      } elseif($protocolParam == 'environment') {
        $req = new SOS_Request_Environment($this);
        $obj = $req->execute(false);

        $res = new stdClass();
        $res->entry = json_decode($obj);
      } else {
        // this is not a valid request
        throw new SOS_Service_NotImplementedException(
                "Type:" . $protocolParam . " is not implemented yet!");
      }

      // sign the response header
      SOS_Sauth::signResponse($res, $originalUri);

      return json_encode($res);

    }
  }
