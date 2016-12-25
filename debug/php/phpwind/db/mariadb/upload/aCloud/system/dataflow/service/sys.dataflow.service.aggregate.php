<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_VERSION_PATH . '/dataflow/ver.dataflow.aggregate.php';
class ACloud_Sys_DataFlow_Service_Aggregate {
	
	var $service = array ();
	
	function __construct() {
		if (! isset ( $this->service ['parse'] ) || ! $this->service ['parse'])
			$this->service ['parse'] = new Aggregate_SQLParseExtension ();
		if (! isset ( $this->service ['operate'] ) || ! $this->service ['operate'])
			$this->service ['operate'] = new Aggregate_SQLLogExtension ();
	}
	
	function ACloud_Sys_DataFlow_Service_Aggregate() {
		$this->__construct ();
	}
	
	function collectSQL($sql) {
		list ( $bool, $operate, $tableName, $fields ) = $this->service ['parse']->parseSQL ( $sql );
		if (! $bool)
			return false;
		return $this->service ['operate']->operate ( $operate, $tableName, $sql, $fields );
	}

}

class Aggregate_SQLParseExtension {
	
	function parseSQL($sql) {
		list ( $sql, $info ) = array (trim ( $sql ), array () );
		if (! $sql)
			return array (false, '', '', $info );
		list ( $bool, $operate, $tableName ) = $this->matchOperateAndTableName ( $sql );
		if (! $bool)
			return array (false, '', '', $info );
		if (ACloud_Sys_Core_S::inArray ( $operate, array ('insert', 'replace' ) )) {
			$dao = ACloud_Sys_Core_Common::getGlobal ( ACLOUD_OBJECT_DAO );
			$insertId = $dao->insert_id ();
			$info = array ('insertid' => $insertId );
		}
		return array (true, $operate, $tableName, $info );
	}
	
	function matchOperateAndTableName($sql) {
		preg_match ( '/^(DELETE|INSERT|REPLACE)\s+(.+?\s)?`?(pw_\w+)`?\s+/i', $sql, $match );
		if (! $match)
			return array (false, false, false );
		list ( , $operate, , $tableName ) = $match;
		list ( $operate, $tableName ) = array (strtolower ( $operate ), strtolower ( $tableName ) );
		if (! ACloud_Sys_Core_S::inArray ( $tableName, $this->getTables () ))
			return array (false, false, false );
		return array (true, $operate, $tableName );
	}
	
	function getTables() {
		return ACloud_Ver_DataFlow_Aggregate::getMonitorTables ();
	}
}

class Aggregate_SQLLogExtension {
	
	function operate($operate, $tableName, $sql, $fields) {
		return ($operate == 'delete') ? $this->operateDeleteLog ( $sql ) : $this->operateAddLog ( $tableName, $fields );
	}
	
	function operateDeleteLog($sql) {
		$sign = ACloud_Sys_Core_Common::getSiteSign ();
		setcookie ( '_ac_' . $sign, intval ( ACloud_Ver_DataFlow_Aggregate::getDeleteSig () ), time () + 60 );
		$sqlLogService = ACloud_Sys_Core_Common::loadSystemClass ( 'sql.log', 'config.service' );
		$fields = array ('log' => $sql );
		return $sqlLogService->addSqlLog ( $fields );
	}
	
	function operateAddLog($tableName, $fields) {
		require_once ACLOUD_PATH . '/system/core/sys.core.common.php';
		list ( $type, $insertId ) = array (ACloud_Ver_DataFlow_Aggregate::getTypeByTableName ( $tableName ), $fields ['insertid'] );
		if (is_null ( $type ) || ! $insertId)
			return false;
		$sign = ACloud_Sys_Core_Common::getSiteSign ();
		return setcookie ( '_ac_' . $sign, intval ( $type ), time () + 60 );
	}
}