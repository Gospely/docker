<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Sys_Core_Dao {
	
	function fetchOne($sql) {
		return ACloud_Sys_Core_Common::getGlobal ( ACLOUD_OBJECT_DAO )->fetchOne ( $sql );
	}
	
	function fetchAll($sql, $resultIndexKey = null, $type = MYSQL_ASSOC) {
		return ACloud_Sys_Core_Common::getGlobal ( ACLOUD_OBJECT_DAO )->fetchAll ( $sql, $resultIndexKey, $type );
	}
	
	function getField($sql) {
		return ACloud_Sys_Core_Common::getGlobal ( ACLOUD_OBJECT_DAO )->getField ( $sql );
	}
	
	function query($sql) {
		return ACloud_Sys_Core_Common::getGlobal ( ACLOUD_OBJECT_DAO )->query ( $sql );
	}
	
	function insert_id() {
		return ACloud_Sys_Core_Common::getGlobal ( ACLOUD_OBJECT_DAO )->insert_id ();
	}
	
	function affected_rows() {
		return ACloud_Sys_Core_Common::getGlobal ( ACLOUD_OBJECT_DAO )->affected_rows ();
	}
	
	function getDB() {
		return ACloud_Sys_Core_Common::getGlobal ( ACLOUD_OBJECT_DAO )->getDB ();
	}
	
	function buildClause($arrays, $expr = null) {
		if (! is_array ( $arrays ) && ! $expr) {
			return '';
		}
		$sets = " SET ";
		if ($expr) {
			foreach ( $expr as $v ) {
				$sets .= " " . $v . ",";
			}
		}
		if ($arrays) {
			foreach ( $arrays as $k => $v ) {
				$sets .= " " . ACloud_Sys_Core_S::sqlMetadata ( $k ) . " = " . ACloud_Sys_Core_S::sqlEscape ( $v ) . ",";
			}
		}
		$sets = trim ( $sets, "," );
		return ($sets) ? $sets : '';
	}

}