<?php
/* 
 * Please see Oyun Studyosu License file
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
  }
