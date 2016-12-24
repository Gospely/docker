<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Sys_Config_Service_GeneralData {
	
	function executeSql($sql) {
		$sql = trim ( $sql );
		if (! $sql)
			return false;
		$versionFilterService = $this->getVersionFilterService ();
		$generalDao = $this->getGeneralDao ();
		return $versionFilterService->filterFields ( $generalDao->executeSql ( $sql ) );
	}
	
	function getVersionFilterService() {
		static $service = null;
		if (! is_null ( $service ))
			return $service;
		require_once ACLOUD_VERSION_PATH . '/config/ver.config.filter.php';
		$service = new ACloud_Ver_Config_Filter ();
		return $service;
	}
	
	function getGeneralDao() {
		return ACloud_Sys_Core_Common::loadSystemClass ( 'generaldata', 'config.dao' );
	}
}