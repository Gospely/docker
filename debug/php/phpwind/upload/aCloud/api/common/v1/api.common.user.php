<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_VERSION_PATH . '/common/ver.common.factory.php';
class ACloud_Api_Common_User {
	
	function banUser($uid) {
		$commonUser = $this->getVersionCommonUser ();
		return $commonUser->banUser ( $uid );
	}
	
	function getIconsByUids($uids) {
		$commonUser = $this->getVersionCommonUser ();
		return $commonUser->getIconsByUids ( $uids );
	}
	
	function getVersionCommonUser() {
		$customizedFactory = ACloud_Ver_Common_Factory::getInstance ();
		return $customizedFactory->getVersionCommonUser ();
	}
}