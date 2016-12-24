<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Sys_Config_Service_Sql_Log {
	
	function getSqlLogsByTimestamp($startTime, $endTime, $page, $perpage) {
		list ( $startTime, $endTime, $page, $perpage ) = array (intval ( $startTime ), intval ( $endTime ), intval ( $page ), intval ( $perpage ) );
		if ($startTime > $endTime)
			return array ();
		$page < 1 && $page = 1;
		$perpage < 1 && $perpage = 100;
		$startTime < 0 && $startTime = 0;
		$offset = ($page - 1) * $perpage;
		$sqlLogDao = $this->getSqlLogDao ();
		return $sqlLogDao->getSqlLogsByTimestamp ( $startTime, $endTime, $offset, $perpage );
	}
	
	function countSqlLogsByTimestamp($startTime, $endTime) {
		list ( $startTime, $endTime ) = array (intval ( $startTime ), intval ( $endTime ) );
		if ($startTime > $endTime)
			return 0;
		$sqlLogDao = $this->getSqlLogDao ();
		return $sqlLogDao->countSqlLogsByTimestamp ( $startTime, $endTime );
	}
	
	function deleteSqlLogByTimestamp($startTime, $endTime) {
		list ( $startTime, $endTime ) = array (intval ( $startTime ), intval ( $endTime ) );
		if ($startTime > $endTime)
			return false;
		$sqlLogDao = $this->getSqlLogDao ();
		return $sqlLogDao->deleteSqlLogByTimestamp ( $startTime, $endTime );
	}
	
	function getAllSqlLogs() {
		$sqlLogDao = $this->getSqlLogDao ();
		return $sqlLogDao->getAllSqlLogs ();
	}
	
	function addSqlLog($fields) {
		$fields = $this->checkFields ( $fields );
		if (! ACloud_Sys_Core_S::isArray ( $fields ))
			return false;
		(! isset ( $fields ['created_time'] ) || ! $fields ['created_time']) && $fields ['created_time'] = time ();
		$sqlLogDao = $this->getSqlLogDao ();
		return $sqlLogDao->insert ( $fields );
	}
	
	function deleteSqlLogsByIds($ids) {
		if (! ACloud_Sys_Core_S::isArray ( $ids ))
			return false;
		$sqlLogDao = $this->getSqlLogDao ();
		return $sqlLogDao->deleteSqlLogsByIds ( $ids );
	}
	
	function checkFields($fields) {
		$result = array ();
		isset ( $fields ['id'] ) && $result ['id'] = intval ( $fields ['id'] );
		isset ( $fields ['log'] ) && $result ['log'] = trim ( $fields ['log'] );
		isset ( $fields ['created_time'] ) && $result ['created_time'] = intval ( $fields ['created_time'] );
		return $result;
	}
	
	function getSqlLogDao() {
		return ACloud_Sys_Core_Common::loadSystemClass ( 'sql.log', 'config.dao' );
	}
}