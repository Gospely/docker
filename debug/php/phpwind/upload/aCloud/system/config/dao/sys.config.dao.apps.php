<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_PATH . '/system/core/sys.core.dao.php';
class ACloud_Sys_Config_Dao_Apps extends ACloud_Sys_Core_Dao {
	
	var $tablename = "pw_acloud_apps";
	
	function insert($fields) {
		$sql = sprintf ( "INSERT INTO %s %s", $this->tablename, $this->buildClause ( $fields ) );
		$this->query ( $sql );
		return $this->get ( $fields ['app_id'] );
	}
	
	function update($fields, $id) {
		$sql = sprintf ( "UPDATE %s %s WHERE app_id = %s", $this->tablename, $this->buildClause ( $fields ), ACloud_Sys_Core_S::sqlEscape ( $id ) );
		return $this->query ( $sql );
	}
	
	function get($id) {
		return $this->fetchOne ( sprintf ( "SELECT * FROM %s WHERE app_id = %s", $this->tablename, ACloud_Sys_Core_S::sqlEscape ( $id ) ) );
	}
	
	function delete($id) {
		return $this->query ( sprintf ( "DELETE FROM %s WHERE app_id = %s", $this->tablename, ACloud_Sys_Core_S::sqlEscape ( $id ) ) );
	}
	
	function deleteAll() {
		return $this->query ( sprintf ( "DELETE FROM %s ", $this->tablename ) );
	}
	
	function gets() {
		return $this->fetchAll ( sprintf ( "SELECT * FROM %s ", $this->tablename ) );
	}

}
