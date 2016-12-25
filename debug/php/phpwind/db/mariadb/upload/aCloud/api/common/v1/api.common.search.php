<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_VERSION_PATH . '/common/ver.common.factory.php';
class ACloud_Api_Common_Search {
	
	function getHotwords() {
		$customizedSearch = $this->getVersionCommonSearch ();
		return $customizedSearch->getHotwords ();
	}
	
	function getVersionCommonSearch() {
		$customizedFactory = ACloud_Ver_Common_Factory::getInstance ();
		return $customizedFactory->getVersionCommonSearch ();
	}
}