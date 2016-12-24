<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.factory.php';
class ACloud_Api_Customized_Friend {
	
	function getAllFriend($uid, $offset, $limit) {
		$customizedFriend = $this->getVersionCustomizedFriend ();
		return $customizedFriend->getAllFriend ( $uid, $offset, $limit );
	}
	
	function searchAllFriend($uid, $keyword, $offset, $limit) {
		$customizedFriend = $this->getVersionCustomizedFriend ();
		return $customizedFriend->searchAllFriend ( $uid, $keyword, $offset, $limit );
	}
	
	function getFollowByUid($uid, $offset, $limit) {
		$customizedFriend = $this->getVersionCustomizedFriend ();
		return $customizedFriend->getFollowByUid ( $uid, $offset, $limit );
	}
	
	function addFollowByUid($uid, $uid2) {
		$customizedFriend = $this->getVersionCustomizedFriend ();
		return $customizedFriend->addFollowByUid ( $uid, $uid2 );
	}
	
	function deleteFollowByUid($uid, $uid2) {
		$customizedFriend = $this->getVersionCustomizedFriend ();
		return $customizedFriend->deleteFollowByUid ( $uid, $uid2 );
	}
	
	function getFanByUid($uid, $offset, $limit) {
		$customizedFriend = $this->getVersionCustomizedFriend ();
		return $customizedFriend->getFanByUid ( $uid, $offset, $limit );
	}
	
	function getVersionCustomizedFriend() {
		$customizedFactory = ACloud_Ver_Customized_Factory::getInstance ();
		return $customizedFactory->getVersionCustomizedFriend ();
	}
}