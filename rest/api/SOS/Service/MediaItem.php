<?php
/* 
 * Please see Sroups License file
 */
  require_once(APP_PATH . '/SOS/Request/Guid.php');

  interface SOS_Service_MediaItem
  {
    /**
     *
     * @param SOS_Request_Guid $guid
     * @return array of SOS_Model_MediaItem
     */
    public function getMediaItems(SOS_Request_Guid $guid);

    public function addMediaItem(SOS_Request_Guid $guid, SOS_Model_MediaItem $item);
  }
