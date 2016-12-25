<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Sys_Config_Service_Apis {
	
	function addApi($fields) {
		$fields = $this->checkFields ( $fields );
		if (! ACloud_Sys_Core_S::isArray ( $fields ) || ! $fields ['name'] || ! $fields ['template'])
			return false;
		(! isset ( $fields ['created_time'] ) || ! $fields ['created_time']) && $fields ['created_time'] = time ();
		(! isset ( $fields ['modified_time'] ) || ! $fields ['modified_time']) && $fields ['modified_time'] = time ();
		$apisDao = $this->getApisDao ();
		return $apisDao->insert ( $fields );
	}
	
	function getApiConfigByApiName($apiName) {
		$apiName = trim ( $apiName );
		if (! $apiName)
			return array ();
		$apisDao = $this->getApisDao ();
		return $apisDao->get ( $apiName );
	}
	
	function updateApiConfigByApiName($apiName, $fields) {
		list ( $apiName, $fields ) = array (trim ( $apiName ), $this->checkFields ( $fields ) );
		if (! $apiName || ! ACloud_Sys_Core_S::isArray ( $fields ))
			return false;
		$apisDao = $this->getApisDao ();
		return $apisDao->update ( $fields, $apiName );
	}
	
	function deleteApiConfigByApiName($apiName) {
		$apiName = trim ( $apiName );
		if (! $apiName)
			return false;
		$apisDao = $this->getApisDao ();
		return $apisDao->delete ( $apiName );
	}
	
	function getApis() {
		$apisDao = $this->getApisDao ();
		return $apisDao->gets ();
	}
	
	function checkFields($fields) {
		$result = array ();
		isset ( $fields ['id'] ) && $result ['id'] = intval ( $fields ['id'] );
		isset ( $fields ['name'] ) && $result ['name'] = trim ( $fields ['name'] );
		isset ( $fields ['template'] ) && $result ['template'] = trim ( $fields ['template'] );
		isset ( $fields ['argument'] ) && $result ['argument'] = trim ( $fields ['argument'] );
		isset ( $fields ['argument_type'] ) && $result ['argument_type'] = trim ( $fields ['argument_type'] );
		isset ( $fields ['fields'] ) && $result ['fields'] = trim ( $fields ['fields'] );
		isset ( $fields ['status'] ) && $result ['status'] = intval ( $fields ['status'] );
		isset ( $fields ['category'] ) && $result ['category'] = intval ( $fields ['category'] );
		isset ( $fields ['created_time'] ) && $result ['created_time'] = intval ( $fields ['created_time'] );
		isset ( $fields ['modified_time'] ) && $result ['modified_time'] = intval ( $fields ['modified_time'] );
		return $result;
	}
	
	function getApisDao() {
		return ACloud_Sys_Core_Common::loadSystemClass ( 'apis', 'config.dao' );
	}
}