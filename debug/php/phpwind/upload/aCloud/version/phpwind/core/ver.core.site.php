<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Ver_Core_Site {
	function execute() {
		$data = array ();
		$data ['site_version'] = WIND_VERSION;
		$data ['site_url'] = $GLOBALS ['db_bbsurl'];
		$data ['site_charset'] = $GLOBALS ['db_charset'];
		$data ['site_name'] = $GLOBALS ['db_bbsname'];
		$data ['site_time'] = time ();
		return $data;
	}
}