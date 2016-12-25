<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Sys_Config_Service_Apps {
	
	function addApp($fields) {
		if (! $fields ['app_id'] || ! $fields ['app_token'] || strlen ( $fields ['app_token'] ) != 128)
			return false;
		$data = array ();
		$data ['app_id'] = $fields ['app_id'];
		$data ['app_name'] = $fields ['app_name'];
		$data ['app_token'] = $fields ['app_token'];
		$data ['created_time'] = $data ['modified_time'] = time ();
		$appsDao = $this->getAppsDao ( $data );
		return $appsDao->insert ( $data );
	}
	
	function getApp($appId) {
		$appsDao = $this->getAppsDao ();
		return $appsDao->get ( $appId );
	}
	
	function deleteApp($appId) {
		$appsDao = $this->getAppsDao ();
		return $appsDao->delete ( $appId );
	}
	
	function updateApp($fields, $appId) {
		$appsDao = $this->getAppsDao ();
		$fields ['modified_time'] = time ();
		return $appsDao->update ( $fields, $appId );
	}
	
	function getApps() {
		$appsDao = $this->getAppsDao ();
		return $appsDao->gets ();
	}
	
	function getAppsDao() {
		return ACloud_Sys_Core_Common::loadSystemClass ( 'apps', 'config.dao' );
	}
}