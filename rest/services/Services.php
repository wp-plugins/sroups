<?php
/*
 * Please see Oyun Studyosu License file
 */
  require_once(APP_PATH . '/SOS/Service/Person.php');
  require_once(APP_PATH . '/SOS/Request/Guid.php');
  require_once(APP_PATH . '/SOS/Model/Person.php');
  require_once(APP_PATH . '/SOS/Model/MediaItem.php');

  class PersonHandler implements SOS_Service_Person
  {
    public function getPerson(SOS_Request_Guid $guid) {
      $user = wp_get_current_user();

      $person = new SOS_Model_Person();

      if( $user->ID ){
        $person->setId( $user->ID );
        $person->setAnonymous("false");
      } else {
        $person->setId("");
        $person->setAnonymous("true");
      }

      $person->setAge( "" );

      if(isset($user->display_name)) {
        $person->setDisplayName( $user->display_name );
      } else {
        $person->setDisplayName("");
      }

      $person->setGender( "" );

      if (!empty ($user->user_firstname) && !empty ($user->user_lastname)) {
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
  class MediaItemsHandler implements SOS_Service_MediaItem
  {
    /**
     *
     * @param SOS_Request_Guid $guid
     * @return array of SOS_Model_MediaItem
     */
    public function getMediaItems(SOS_Request_Guid $guid) {

      $res = array();

      // fetch media items of wordpress
      $list = $this->fetchAttachments();

      if(count($list) > 0) {
        foreach( $list as $item ) {
          $m = new SOS_Model_MediaItem();
          $m->setDescription( $item->post_content );
          $m->setId( $item->page_id );
          $resized = wp_get_attachment_image_src( $item->page_id, array(80, 60), true );
          if(is_array($resized)) {
            $m->setThumbnailUrl( $resized[0] );
          } else {
            $m->setThumbnailUrl( "" );
          }
          $m->setTitle( $item->page_title );
          $m->setUrl( $item->guid );
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
		for ( $i = 0; $i < $num_pages; $i++ ) {
			$post_date = mysql2date("Ymd\TH:i:s", $page_list[$i]->post_date, false);
			$post_date_gmt = mysql2date("Ymd\TH:i:s", $page_list[$i]->post_date_gmt, false);
		}

		return($page_list);
    }
  }

  class EnvironmentHandler implements SOS_Service_Environment
  {
    public function getEnvironment(SOS_Request_Guid $guid) {
      // @not yet implemented
    }
  }
