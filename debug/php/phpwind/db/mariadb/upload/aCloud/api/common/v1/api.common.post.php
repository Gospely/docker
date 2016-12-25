<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_VERSION_PATH . '/common/ver.common.factory.php';
class ACloud_Api_Common_Post {
	
	function shieldPost($pid, $tid) {
		$customizedPost = $this->getVersionCommonPost ();
		return $customizedPost->shieldPost ( $pid, $tid );
	}
	
	function getVersionCommonPost() {
		$customizedFactory = ACloud_Ver_Common_Factory::getInstance ();
		return $customizedFactory->getVersionCommonPost ();
	}
}