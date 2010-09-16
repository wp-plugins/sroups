<?php
/* 
 * Please see Oyun Studyosu License file
 */

  require_once(APP_PATH . '/SOS/Service/MediaItem.php');
  require_once(APP_PATH . '/SOS/Model/MediaItem.php');
  require_once(APP_PATH . '/SOS/Request/Guid.php');

  class MediaItemHandler implements SOS_Service_MediaItem
  {
    /**
     *
     * @param SOS_Request_Guid $guid
     * @return SOS_Model_Person
     */
    public function getMediaItems(SOS_Request_Guid $guid) {
      $mitems = array();

      $mi = new SOS_Model_MediaItem();
      $mi->setDescription("My smaple description");
      $mi->setId("12345678");
      $mi->setThumbnailUrl("http://example.com/myThumb.png");
      $mi->setTitle("My sample title");
      $mi->setUrl("http://www.example.com");

      array_push($mitems, $mi);

      $mi = new SOS_Model_MediaItem();
      $mi->setDescription("My smaple description 2");
      $mi->setId("45645645645");
      $mi->setThumbnailUrl("http://example.com/myThum1b.png");
      $mi->setTitle("My sample title 1");
      $mi->setUrl("http://www.example.com");

      array_push($mitems, $mi);

      return $mitems;
    }
  }