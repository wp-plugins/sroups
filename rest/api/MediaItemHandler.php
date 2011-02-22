<?php

/*
 * Please see Sroups License file
 */

require_once(APP_PATH . '/SOS/Service/MediaItem.php');
require_once(APP_PATH . '/SOS/Model/MediaItem.php');
require_once(APP_PATH . '/SOS/Request/Guid.php');

class MediaItemHandler implements SOS_Service_MediaItem
{

    /**
     * Used to instantiate a new SOS media item model and populate it
     * with data.
     * 
     * @param SOS_Request_Guid $guid
     * @return SOS_Model_Person
     */
    public function getMediaItems(SOS_Request_Guid $guid) {
        // media items array. possible item types are as follows:
        // - SOS_Model_MediaItem::TYPE_AUDIO
        // - SOS_Model_MediaItem::TYPE_IMAGE
        // - SOS_Model_MediaItem::TYPE_VIDEO
        $mitems = array();

        // create a new SOS media item model instance
        $mi = new SOS_Model_MediaItem();

        // set the description
        $mi->setDescription("Sample photo");

        // set the unique identifier for the media item handler
        $mi->setId("12345678");

        // set the thumbnail image URL
        $mi->setThumbnailUrl("http://example.com/myThumb.png");

        // set the title
        $mi->setTitle("My sample title");

        // set the image URL
        $mi->setUrl("http://www.example.com");

        // add the item to the items array
        array_push($mitems, $mi);

        return $mitems;
    }

    /**
     * Used to add a new media item to the items array and store it on the
     * custom website.
     *
     * @param SOS_Request_Guid $guid
     * @param SOS_Model_MediaItem $item
     * @return boolean
     */
    public function addMediaItem(SOS_Request_Guid $guid, SOS_Model_MediaItem $item) {
        return TRUE;
    }

}
