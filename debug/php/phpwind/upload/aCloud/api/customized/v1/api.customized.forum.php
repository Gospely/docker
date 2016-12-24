<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.factory.php';
class ACloud_Api_Customized_Forum {
	
	function getAllForum() {
		$customizedForum = $this->getVersionCustomizedForum ();
		return $customizedForum->getAllForum ();
	}
	
	function getForumByFid($fid) {
		$customizedForum = $this->getVersionCustomizedForum ();
		return $customizedForum->getForumByFid ( $fid );
	}
	
	function getChildForumByFid($fid) {
		$customizedForum = $this->getVersionCustomizedForum ();
		return $customizedForum->getChildForumByFid ( $fid );
	}
	
	function getVersionCustomizedForum() {
		$customizedFactory = ACloud_Ver_Customized_Factory::getInstance ();
		return $customizedFactory->getVersionCustomizedForum ();
	}
}