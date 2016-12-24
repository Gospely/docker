<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_VERSION_PATH . '/common/ver.common.factory.php';
class ACloud_Api_Common_Permissions {
	
	function isUserBanned($uid) {
		$customizedPermissions = $this->getVersionCommonPermissions ();
		return $customizedPermissions->isUserBanned ( $uid );
	}
	
	function readForum($uid, $fid) {
		$customizedPermissions = $this->getVersionCommonPermissions ();
		return $customizedPermissions->readForum ( $uid, $fid );
	}
	
	function getVersionCommonPermissions() {
		$customizedFactory = ACloud_Ver_Common_Factory::getInstance ();
		return $customizedFactory->getVersionCommonPermissions ();
	}
}