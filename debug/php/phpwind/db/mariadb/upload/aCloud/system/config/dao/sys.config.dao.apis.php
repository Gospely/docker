<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_PATH . '/system/core/sys.core.dao.php';
class ACloud_Sys_Config_Dao_Apis extends ACloud_Sys_Core_Dao {
	
	var $tablename = "pw_acloud_apis";
	
	function insert($fields) {
		$sql = sprintf ( "REPLACE INTO %s %s", $this->tablename, $this->buildClause ( $fields ) );
		$this->query ( $sql );
		return $this->insert_id ();
	}
	
	function delete($name) {
		$this->query ( sprintf ( "DELETE FROM %s WHERE name = %s", $this->tablename, ACloud_Sys_Core_S::sqlEscape ( $name ) ) );
		return $this->affected_rows ();
	}
	
	function update($fields, $name) {
		$sql = sprintf ( "UPDATE %s %s WHERE name = %s", $this->tablename, $this->buildClause ( $fields ), ACloud_Sys_Core_S::sqlEscape ( $name ) );
		$this->query ( $sql );
		return $this->affected_rows ();
	}
	
	function get($name) {
		return $this->fetchOne ( sprintf ( "SELECT * FROM %s WHERE name = %s", $this->tablename, ACloud_Sys_Core_S::sqlEscape ( $name ) ) );
	}
	
	function gets() {
		return $this->fetchAll ( sprintf ( "SELECT * FROM %s ", $this->tablename ) );
	}
}