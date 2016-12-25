<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Sys_Config_Service_App_Configs {
	
	function addAppConfig($fields) {
		$type = ($fields ['app_type']) ? $fields ['app_type'] : (is_array ( $fields ['app_value'] ) ? 2 : 1);
		$value = is_array ( $fields ['app_value'] ) ? serialize ( $fields ['app_value'] ) : $fields ['app_value'];
		$data = array ();
		$data ['app_id'] = $fields ['app_id'];
		$data ['app_key'] = $fields ['app_key'];
		$data ['app_value'] = $value;
		$data ['app_type'] = intval ( $type );
		$fields ['created_time'] = $fields ['modified_time'] = time ();
		$configsDao = $this->getAppConfigsDao ( $fields );
		return $configsDao->insert ( $fields );
	}
	
	function getAppConfig($appId, $appKey) {
		$configsDao = $this->getAppConfigsDao ();
		return $configsDao->get ( $appId, $appKey );
	}
	
	function getAppConfigsByAppId($appId) {
		$configsDao = $this->getAppConfigsDao ();
		return $configsDao->getsByAppId ( $appId );
	}
	
	function updateAppConfig($appId, $appKey, $appValue, $appType = 1) {
		$configsDao = $this->getAppConfigsDao ();
		return $configsDao->update ( array ('app_value' => $appValue, 'app_type' => (in_array ( $appType, array (1, 2 ) ) ? $appType : 1), 'modified_time' => time () ), $appId, $appKey );
	}
	
	function deleteAppConfig($appId, $appKey) {
		$configsDao = $this->getAppConfigsDao ();
		return $configsDao->delete ( $appId, $appKey );
	}
	
	function deleteAppConfigByAppId($appId) {
		$configsDao = $this->getAppConfigsDao ();
		return $configsDao->deleteAppConfigByAppId ( $appId );
	}
	
	function getAppConfigs() {
		$configsDao = $this->getAppConfigsDao ();
		return $configsDao->gets ();
	}
	
	function getAppConfigsDao() {
		return ACloud_Sys_Core_Common::loadSystemClass ( 'app.configs', 'config.dao' );
	}
}