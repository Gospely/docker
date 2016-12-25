<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Ver_Common_Base {
	
	function ACloud_Ver_Common_Base() {
		$daoObject = ACloud_Sys_Core_Common::getGlobal ( ACLOUD_OBJECT_DAO );
		$daoObject->getDB ();
	}
	
	function getDeletedId($startId, $endId) {
		list ( $tableName, $primaryKey ) = $this->getPrimaryKeyAndTable ();
		list ( $existIds, $allIds ) = array ($this->getIdsFromTable ( $startId, $endId ), range ( $startId, $endId ) );
		if (! S::isArray ( $existIds ))
			return $this->formatDeleteIds ( $allIds );
		return $this->formatDeleteIds ( array_diff ( $allIds, $existIds ) );
	}
	
	function getIdsFromTable($startId, $endId) {
		list ( $tableName, $primaryKey ) = $this->getPrimaryKeyAndTable ();
		$result = array ();
		$query = $GLOBALS ['db']->query ( sprintf ( "SELECT %s FROM %s WHERE %s >= %s AND %s <= %s", S::sqlMetaData ( $primaryKey ), S::sqlMetaData ( $tableName ), S::sqlMetaData ( $primaryKey ), S::sqlEscape ( $startId ), S::sqlMetaData ( $primaryKey ), S::sqlEscape ( $endId ) ) );
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$result [] = $rt [$primaryKey];
		}
		return $result;
	}
	
	function formatDeleteIds($ids) {
		if (! S::isArray ( $ids ))
			return array ();
		list ( $tableName, $primaryKey ) = $this->getPrimaryKeyAndTable ();
		$result = array ();
		foreach ( $ids as $id ) {
			$result [] = array ($primaryKey => intval ( $id ) );
		}
		return $result;
	}
	
	function buildResponse($errorCode, $responseData = array()) {
		return array ($errorCode, $responseData );
	}
	
	function getCommonUtilityService() {
		$commonFactory = ACloud_Ver_Common_Factory::getInstance ();
		return $commonFactory->getVersionCommonUtility ();
	}
}