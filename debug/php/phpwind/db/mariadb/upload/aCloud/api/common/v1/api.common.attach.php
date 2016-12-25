<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_VERSION_PATH . '/common/ver.common.factory.php';
class ACloud_Api_Common_Attach {
	
	function getImgAttaches($aids) {
		$commonAttach = $this->getVersionCommonAttach ();
		return $commonAttach->getImgAttaches ( $aids );
	}
	
	function getImgAttachesByTids($tids) {
		$commonAttach = $this->getVersionCommonAttach ();
		return $commonAttach->getImgAttachesByTids ( $tids );
	}
	
	function getVersionCommonAttach() {
		$customizedFactory = ACloud_Ver_Common_Factory::getInstance ();
		return $customizedFactory->getVersionCommonAttach ();
	}
}