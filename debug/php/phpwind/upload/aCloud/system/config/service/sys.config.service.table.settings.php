<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Sys_Config_Service_Table_Settings {
	
	function addTableSetting($fields) {
		$fields = $this->checkFields ( $fields );
		if (! ACloud_Sys_Core_S::isArray ( $fields ) || ! $fields ['name'])
			return false;
		(! isset ( $fields ['created_time'] ) || ! $fields ['created_time']) && $fields ['created_time'] = time ();
		(! isset ( $fields ['modified_time'] ) || ! $fields ['modified_time']) && $fields ['modified_time'] = time ();
		$apisDao = $this->getTableSettingsDao ();
		return $apisDao->insert ( $fields );
	}
	
	function getSettingByTableName($tableName) {
		$tableName = trim ( $tableName );
		if (! $tableName)
			return array ();
		$tableSettingDao = $this->getTableSettingsDao ();
		return $tableSettingDao->get ( $tableName );
	}
	
	function getSettingByTableNameWithReplace($tableName) {
		$tableName = trim ( $tableName );
		if (! $tableName)
			return array ();
		$tableSettingDao = $this->getTableSettingsDao ();
		$result = $tableSettingDao->get ( $tableName );
		if (! $result)
			return array ();
		$result ['name'] = str_replace ( 'prefix_', ACLOUD_TABLE_PREFIX, $result ['name'] );
		return $result;
	}
	
	function updateTableSettingByTableName($tableName, $fields) {
		list ( $tableName, $fields ) = array (trim ( $tableName ), $this->checkFields ( $fields ) );
		if (! $tableName || ! ACloud_Sys_Core_S::isArray ( $fields ))
			return false;
		$apisDao = $this->getTableSettingsDao ();
		return $apisDao->update ( $fields, $tableName );
	}
	
	function deleteTableSettingByTableName($tableName) {
		$tableName = trim ( $tableName );
		if (! $tableName)
			return false;
		$apisDao = $this->getTableSettingsDao ();
		return $apisDao->delete ( $tableName );
	}
	
	function getTableSettings() {
		$apisDao = $this->getTableSettingsDao ();
		return $apisDao->gets ();
	}
	
	function checkFields($fields) {
		$result = array ();
		isset ( $fields ['name'] ) && $result ['name'] = trim ( $fields ['name'] );
		isset ( $fields ['status'] ) && $result ['status'] = intval ( $fields ['status'] );
		isset ( $fields ['category'] ) && $result ['category'] = intval ( $fields ['category'] );
		isset ( $fields ['primary_key'] ) && $result ['primary_key'] = trim ( $fields ['primary_key'] );
		isset ( $fields ['created_time'] ) && $result ['created_time'] = intval ( $fields ['created_time'] );
		isset ( $fields ['modified_time'] ) && $result ['modified_time'] = intval ( $fields ['modified_time'] );
		return $result;
	}
	
	function getTableSettingsDao() {
		return ACloud_Sys_Core_Common::loadSystemClass ( 'table.settings', 'config.dao' );
	}

}