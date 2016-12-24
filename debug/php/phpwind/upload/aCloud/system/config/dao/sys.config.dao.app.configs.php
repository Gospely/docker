<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_PATH . '/system/core/sys.core.dao.php';
class ACloud_Sys_Config_Dao_App_Configs extends ACloud_Sys_Core_Dao {
	
	var $tablename = "pw_acloud_app_configs";
	
	function insert($fields) {
		$sql = sprintf ( "REPLACE INTO %s %s", $this->tablename, $this->buildClause ( $fields ) );
		$this->query ( $sql );
		return $this->get ( $fields ['app_id'], $fields ['app_key'] );
	}
	
	function update($fields, $appId, $appKey) {
		$sql = sprintf ( "UPDATE %s %s WHERE app_id = %s AND app_key = %s", $this->tablename, $this->buildClause ( $fields ), ACloud_Sys_Core_S::sqlEscape ( $appId ), ACloud_Sys_Core_S::sqlEscape ( $appKey ) );
		$this->query ( $sql );
		return $this->get ( $appId, $appKey );
	}
	
	function get($appId, $appKey) {
		return $this->fetchOne ( sprintf ( "SELECT * FROM %s WHERE app_id = %s AND app_key = %s", $this->tablename, ACloud_Sys_Core_S::sqlEscape ( $appId ), ACloud_Sys_Core_S::sqlEscape ( $appKey ) ) );
	}
	
	function delete($appId, $appKey) {
		return $this->query ( sprintf ( "DELETE FROM %s WHERE app_id = %s AND app_key = %s", $this->tablename, ACloud_Sys_Core_S::sqlEscape ( $appId ), ACloud_Sys_Core_S::sqlEscape ( $appKey ) ) );
	}
	
	function deleteAppConfigByAppId($appId) {
		return $this->query ( sprintf ( "DELETE FROM %s WHERE app_id = %s", $this->tablename, ACloud_Sys_Core_S::sqlEscape ( $appId ) ) );
	}
	
	function deleteAll() {
		return $this->query ( sprintf ( "DELETE FROM %s ", $this->tablename ) );
	}
	
	function gets() {
		return $this->fetchAll ( sprintf ( "SELECT * FROM %s ", $this->tablename ) );
	}
	
	function getsByAppId($appId) {
		return $this->fetchAll ( sprintf ( "SELECT * FROM %s WHERE app_id = %s ", $this->tablename, ACloud_Sys_Core_S::sqlEscape ( $appId ) ) );
	}
}