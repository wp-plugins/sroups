<?php

  /*
   * Please see Sroups License file
   */
  require_once(APP_PATH . '/SOS/Service/Person.php');
  require_once(APP_PATH . '/SOS/Request/Guid.php');
  require_once(APP_PATH . '/SOS/Model/Person.php');
  require_once(APP_PATH . '/SOS/Model/MediaItem.php');
  require_once(APP_PATH . '/SOS/Model/Environment/Data.php');
  

  function getPost($val) {
    if(isset($_POST[$val]))
      return htmlentities($_POST[$val]);
    else
      return null;
  }

  class PersonHandler implements SOS_Service_Person {

    public function getPerson(SOS_Request_Guid $guid) {
      $user = wp_get_current_user();

      $person = new SOS_Model_Person();

      if ($user->ID) {
        $person->setId($user->ID);
        $person->setAnonymous("false");
      } else {
        $person->setId("");
        $person->setAnonymous("true");
      }

      $person->setAge("");

      if (isset($user->display_name)) {
        $person->setDisplayName($user->display_name);
      } else {
        $person->setDisplayName("");
      }

      $person->setGender("");

      if (!empty($user->user_firstname) && !empty($user->user_lastname)) {
        $person->setName($user->user_firstname . ' ' . $user->user_lastname);
      } else {
        $person->setName("");
      }

      $person->setUserType(SOS_Model_Person::USER_TYPE_NORMAL);
      if (!empty($user->user_url)) {
        $person->setProfileUrl($user->user_url);
      } else {
        $person->setProfileUrl("");
      }

      if ($user->ID) {
        preg_match('/^.*src=\'(.*)\'.*class.*$/', get_avatar($user->ID, 50), $matches);
        if (!empty($matches[1])) {
          $userThumbnail = $matches[1];
        }
        preg_match('/^.*src=\'(.*)\'.*class.*$/', get_avatar($user->ID, 100), $matches);
        if (!empty($matches[1])) {
          $userMedium = $matches[1];
        }
        preg_match('/^.*src=\'(.*)\'.*class.*$/', get_avatar($user->ID, 150), $matches);
        if (!empty($matches[1])) {
          $userLarge = $matches[1];
        }
        if (!empty($userThumbnail)) {
          $person->setThumbnailUrl($userThumbnail);
        }
        if (!empty($userMedium)) {
          $person->setMediumUrl($userMedium);
        }
        if (!empty($userLarge)) {
          $person->setLargeUrl($userLarge);
        }
      }

      return $person;
    }

  }

  /**
   *
   */
  class MediaItemsHandler implements SOS_Service_MediaItem {

    /**
     *
     * @param SOS_Request_Guid $guid
     * @return array of SOS_Model_MediaItem
     */
    public function getMediaItems(SOS_Request_Guid $guid) {

      // check the guid for userId == @me and groupId == @self

      $res = array();

      // fetch media items of wordpress
      $list = $this->fetchAttachments();

      if (count($list) > 0) {
        foreach ($list as $item) {
          $m = new SOS_Model_MediaItem();
          $m->setDescription($item->post_content);
          $m->setId($item->page_id);
          $resized = wp_get_attachment_image_src($item->page_id, array(80, 60), true);
          if (is_array($resized)) {
            $m->setThumbnailUrl($resized[0]);
          } else {
            $m->setThumbnailUrl("");
          }
          $m->setTitle($item->page_title);
          $m->setUrl($item->guid);
          array_push($res, $m);
        }
      }

      return $res;
    }

    private function fetchAttachments() {
      global $wpdb;

      // Get list of pages ids and titles
      $page_list = $wpdb->get_results("
			SELECT ID page_id,
				post_title page_title,
				post_content,
                guid
			FROM {$wpdb->posts}
			WHERE post_type = 'attachment'
			ORDER BY ID
		");

      // The date needs to be formated properly.
      $num_pages = count($page_list);
      
      for ($i = 0; $i < $num_pages; $i++) {
        //$post_date = mysql2date("Ymd\TH:i:s", $page_list[$i]->post_date, false);
        //$post_date_gmt = mysql2date("Ymd\TH:i:s", $page_list[$i]->post_date_gmt, false);
      }

      return($page_list);
    }

    /**
     * this method is the post behavior of the REST service
     * that saves the media item to wordpress database <br /><br />
     *
     * required fields are<br />
     * <ul>
     * <li>type - the mime type of the object</li>
     * <li>url</li>
     * <li>title</li>
     * <li>description</li>
     * </ul>
     * @example curl -v -d "description=mydesc&type=image/jpeg&title=myimage&url=http://myimage.com/image.jpg" "http://localhost/wordpress/wp-content/plugins/sroups/rest/mediaItems/@me/@self?sauth_ts=1295515151&sauth_sig=13b8c63bac90480c06c9cd6db333e330&sid=2"
     *
     * @param SOS_Request_Guid $guid
     * @param SOS_Model_MediaItem $item
     * @return boolean
     */
    public function addMediaItem(SOS_Request_Guid $guid, SOS_Model_MediaItem $item) {
      // check the guid for userId == @me and groupId == @self

      // validate the request method
      if($_SERVER['REQUEST_METHOD'] != 'POST')
        throw new Exception ("invalid request type post expected");

      // validate the media type
      // only videos, images and audios are available
      // @todo

      // save the media item
      $this->saveAttachment($item);

      return "true";

    }

    private function saveAttachment(SOS_Model_MediaItem $item) {
      // Construct the attachment array
      $item = $item->getObject();
      $attachment = array(
          'post_mime_type' => $item->type,
          'guid' => $item->url,
          'post_parent' => 0, // post_id
          'post_title' => $item->title,
          'post_content' => $item->description,
      );

      // Save the data
      $file = false;
      $id = wp_insert_attachment($attachment, $file, 0); // no file and no parent post
      if ( !is_wp_error($id) ) {
          wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
      }
      
    }

  }

  class EnvironmentHandler implements SOS_Service_Environment {

    public function getEnvironment(SOS_Request_Guid $guid) {
      $data = new SOS_Model_Environment_Data();
      $data->setContainerName(get_bloginfo());

      $env = new SOS_Model_Environment();
      $env->setDomain($this->getDomain());
      $env->setEnvironment($data);
      $env->setFields($this->getSupportedFields());

      return $env;
    }

    public function supportsField($field) {
      return in_array($field, $this->getSupportedFields());
    }

    public function getSupportedFields() {
      return array('people', 'mediaItems');
    }

    /**
     * returns the domain name ex: orkut.com
     * @return String
     */
    public function getDomain() {
      return $_SERVER['HTTP_HOST'];
    }

  }

