<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_PATH . '/system/core/sys.core.dao.php';
class ACloud_Sys_Config_Dao_Extras extends ACloud_Sys_Core_Dao {
	
	var $tablename = "pw_acloud_extras";
	
	function insert($fields) {
		$sql = sprintf ( "REPLACE INTO %s %s", $this->tablename, $this->buildClause ( $fields ) );
		$this->query ( $sql );
		return $this->get ( $fields ['ekey'] );
	}
	
	function update($fields, $ekey) {
		$sql = sprintf ( "UPDATE %s %s WHERE ekey = %s", $this->tablename, $this->buildClause ( $fields ), ACloud_Sys_Core_S::sqlEscape ( $ekey ) );
		return $this->query ( $sql );
	}
	
	function get($ekey) {
		if (! $this->checkTable ())
			return array ();
		return $this->fetchOne ( sprintf ( "SELECT * FROM %s WHERE ekey = %s", $this->tablename, ACloud_Sys_Core_S::sqlEscape ( $ekey ) ) );
	}
	
	function getsByKeys($keys) {
		if (! $this->checkTable ())
			return array ();
		return $this->fetchAll ( sprintf ( "SELECT * FROM %s WHERE ekey in (%s) ", $this->tablename, ACloud_Sys_Core_S::sqlImplode ( $keys ) ) );
	}
	
	function delete($ekey) {
		return $this->query ( sprintf ( "DELETE FROM %s WHERE ekey = %s", $this->tablename, ACloud_Sys_Core_S::sqlEscape ( $ekey ) ) );
	}
	
	function deleteAll() {
		return $this->query ( sprintf ( "DELETE FROM %s ", $this->tablename ) );
	}
	
	function gets() {
		if (! $this->checkTable ())
			return array ();
		return $this->fetchAll ( sprintf ( "SELECT * FROM %s ", $this->tablename ) );
	}
	
	function checkTable() {
		static $tableStatus = null;
		if (null === $tableStatus) {
			$result = $this->fetchOne ( "SHOW TABLES LIKE '" . $this->tablename . "'" );
			$tableStatus = ($result) ? true : false;
		}
		return $tableStatus;
	}
}