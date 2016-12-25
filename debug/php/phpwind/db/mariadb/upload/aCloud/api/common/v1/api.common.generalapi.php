<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Api_Common_GeneralApi {
	
	function get($apiSetting, $request) {
		$sqlBuilder = ACloud_Sys_Core_Common::loadSystemClass ( 'sqlbuilder' );
		$sql = $sqlBuilder->buildSelectSql ( $apiSetting, $request );
		if (! $sql)
			return array (4006, array () );
		$generalDataService = ACloud_Sys_Core_Common::loadSystemClass ( 'generaldata', 'config.service' );
		$data = $generalDataService->executeSql ( $sql );
		if ($data === false)
			return array (4007, array () );
		return array (0, $data );
	}
}