<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_PATH . '/system/core/sys.core.dao.php';
class ACloud_Sys_Config_Dao_Keys extends ACloud_Sys_Core_Dao {
	
	var $tablename = "pw_acloud_keys";
	
	function insert($fields) {
		$sql = sprintf ( "INSERT INTO %s %s", $this->tablename, $this->buildClause ( $fields ) );
		return $this->query ( $sql );
	}
	
	function update($fields, $id) {
		$sql = sprintf ( "UPDATE %s %s WHERE id = %s", $this->tablename, $this->buildClause ( $fields ), ACloud_Sys_Core_S::sqlEscape ( $id ) );
		return $this->query ( $sql );
	}
	
	function get($id) {
		return $this->fetchOne ( sprintf ( "SELECT * FROM %s WHERE id = %s", $this->tablename, ACloud_Sys_Core_S::sqlEscape ( $id ) ) );
	}
	
	function delete($id) {
		return $this->query ( sprintf ( "DELETE FROM %s WHERE id = %s", $this->tablename, ACloud_Sys_Core_S::sqlEscape ( $id ) ) );
	}
}