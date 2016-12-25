<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_PATH . '/system/core/sys.core.dao.php';
class ACloud_Sys_Config_Dao_Table_Settings extends ACloud_Sys_Core_Dao {
	
	var $tablename = "pw_acloud_table_settings";
	
	function insert($fields) {
		$sql = sprintf ( "INSERT INTO %s %s", $this->tablename, $this->buildClause ( $fields ) );
		return $this->query ( $sql );
	}
	
	function update($fields, $name) {
		$sql = sprintf ( "UPDATE %s %s WHERE name = %s", $this->tablename, $this->buildClause ( $fields ), ACloud_Sys_Core_S::sqlEscape ( $name ) );
		return $this->query ( $sql );
	}
	
	function get($name) {
		return $this->fetchOne ( sprintf ( "SELECT * FROM %s WHERE name = %s", $this->tablename, ACloud_Sys_Core_S::sqlEscape ( $name ) ) );
	}
	
	function delete($name) {
		return $this->query ( sprintf ( "DELETE FROM %s WHERE name = %s", $this->tablename, ACloud_Sys_Core_S::sqlEscape ( $name ) ) );
	}
	
	function gets() {
		return $this->fetchAll ( sprintf ( "SELECT * FROM %s ", $this->tablename ) );
	}
}