<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_PATH . '/system/core/sys.core.dao.php';
class ACloud_Sys_Config_Dao_Sql_Log extends ACloud_Sys_Core_Dao {
	
	var $tablename = "pw_acloud_sql_log";
	
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
	
	function getSqlLogsByTimestamp($startTime, $endTime, $offset, $perpage) {
		$sqlCondition = $endTime > 0 ? ' AND created_time <= ' . ACloud_Sys_Core_S::sqlEscape ( $endTime ) : '';
		return $this->fetchAll ( sprintf ( "SELECT * FROM %s WHERE created_time >= %s $sqlCondition %s", $this->tablename, ACloud_Sys_Core_S::sqlEscape ( $startTime ), ACloud_Sys_Core_S::sqlLimit ( $offset, $perpage ) ), 'id' );
	}
	
	function countSqlLogsByTimestamp($startTime, $endTime) {
		$sqlCondition = $endTime > 0 ? ' AND created_time <= ' . ACloud_Sys_Core_S::sqlEscape ( $endTime ) : '';
		return $this->getField ( sprintf ( "SELECT COUNT(*) as count FROM %s WHERE created_time >= %s $sqlCondition", $this->tablename, ACloud_Sys_Core_S::sqlEscape ( $startTime ) ) );
	}
	
	function deleteSqlLogByTimestamp($startTime, $endTime) {
		$this->query ( sprintf ( "DELETE FROM %s WHERE created_time >= %s AND created_time <= %s", $this->tablename, ACloud_Sys_Core_S::sqlEscape ( $startTime ), ACloud_Sys_Core_S::sqlEscape ( $endTime ) ) );
		return $this->affected_rows ();
	}
	
	function getAllSqlLogs() {
		return $this->fetchAll ( sprintf ( "SELECT * FROM %s", $this->tablename ), 'id' );
	}
	
	function deleteSqlLogsByIds($ids) {
		if (! ACloud_Sys_Core_S::isArray ( $ids ))
			return false;
		$this->query ( sprintf ( "DELETE FROM %s WHERE id IN (%s)", $this->tablename, ACloud_Sys_Core_S::sqlImplode ( $ids ) ) );
		return $this->affected_rows ();
	}
}