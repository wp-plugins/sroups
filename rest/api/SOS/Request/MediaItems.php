<?php

/*
 * Please see Sroups License file
 */
require_once(APP_PATH . '/SOS/Factory.php');
require_once(APP_PATH . '/SOS/Request/Abstract.php');
require_once(APP_PATH . '/SOS/Protocol/Abstract.php');
require_once(APP_PATH . '/SOS/Request/Guid.php');
require_once(APP_PATH . '/SOS/Request/InvalidException.php');
require_once(APP_PATH . '/SOS/Service/NotImplementedException.php');

class SOS_Request_MediaItems extends SOS_Request_Abstract {

    public function __construct(SOS_Protocol_Abstract $protocol) {
        parent::__construct($protocol);

        $this->_guid =
                new SOS_Request_Guid(
                        $this->getProtocol()->getGuidParam(),
                        array(SOS_Request_Guid::ME),
                        $this->getProtocol()->getScopeParam());

        $scope = $this->getProtocol()->getScopeParam();

        if ($scope) {
            // check if it is a valid scope
            $status = false;
            foreach ($this->getAvailableScopes() as $availableScore) {
                if (preg_match($availableScore, $scope)) {
                    $status = true;
                    break;
                }
            }
            if (! $status) {
                throw new SOS_Service_NotImplementedException("Scope:" . $scope . " is not supported!");
            }

            $this->_scope = $scope;
        }
    }

    /**
     *
     * @return array
     */
    public function getAvailableScopes() {
        return array("/@self/", "/[0-9]+/");
    }

    /**
     * Returns the person object as stdObject
     * if $json passed true, returns JSON string
     * @param boolean $json
     * @return String jsonObject
     */
    public function execute($method = SOS_Request_Method::MEDIAITEMS_GETITEMS, $json = true) {

        if (SOS_Request_Method::MEDIAITEMS_GETITEMS == $method) {
            $mediaItemService = SOS_Factory::getMediaItemService();
            $mediaItems = $mediaItemService->getMediaItems($this->_guid);

            $arr = array();
            foreach ($mediaItems as $m)
                array_push($arr, $m->getObject());

            return json_encode($arr);
        } elseif (SOS_Request_Method::MEDIAITEMS_ADDITEM == $method) {
            $mediaItemService = SOS_Factory::getMediaItemService();
            $mediaItem = new SOS_Model_MediaItem();
            $mediaItem->setId($this->getProtocol()->getPost('id'));
            $mediaItem->setDescription($this->getProtocol()->getPost('description'));
            $mediaItem->setThumbnailUrl($this->getProtocol()->getPost('thumbnailUrl'));
            $mediaItem->setTitle($this->getProtocol()->getPost('title'));
            $mediaItem->setType($this->getProtocol()->getPost('type'));
            $mediaItem->setUrl($this->getProtocol()->getPost('url'));

            return $mediaItemService->addMediaItem($this->_guid, $mediaItem);
        } elseif (SOS_Request_Method::MEDIAITEMS_ADDITEM == $method) {
            throw new Exception("Not implemented yet");
        }
    }

}
