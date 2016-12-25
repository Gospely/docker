<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_VERSION_PATH . '/common/ver.common.factory.php';
class ACloud_Api_Common_Site {
	
	function getTablePartitions($type = '') {
		$commonSite = $this->getVersionCommonSite ();
		return $commonSite->getTablePartitions ( $type );
	}
	
	function checkTableField($table, $field) {
		$commonSite = $this->getVersionCommonSite ();
		return $commonSite->checkTableField ( $table, $field );
	}
	
	function getVersionCommonSite() {
		$customizedFactory = ACloud_Ver_Common_Factory::getInstance ();
		return $customizedFactory->getVersionCommonSite ();
	}
}
