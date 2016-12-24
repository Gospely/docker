<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Sys_DataFlow_Service_Crawler {
	
	var $perpage = 100;
	
	function crawlTable($tableName, $page, $perpage) {
		list ( $tableName, $page, $perpage ) = array (trim ( $tableName ), intval ( $page ), intval ( $perpage ) );
		$page < 1 && $page = 1;
		$this->setPerpage ( $perpage );
		if (! $tableName)
			return '';
		$tableSettingService = ACloud_Sys_Core_Common::loadSystemClass ( 'table.settings', 'config.service' );
		$tableSetting = $tableSettingService->getSettingByTableNameWithReplace ( $tableName );
		if (! ACloud_Sys_Core_S::isArray ( $tableSetting ))
			return '';
		if (! $tableSetting ['status'])
			return '';
		list ( $total, $data ) = $tableSetting ['primary_key'] ? $this->getTableDataByPrimaryKey ( $tableSetting, $tableName, $page ) : $this->getTableDataWithoutPrimaryKey ( $tableSetting, $tableName, $page );
		if ($total < 1)
			return '';
		$totalPages = ceil ( $total / $this->perpage );
		return $this->outputDataFlow ( $data, $totalPages );
	}
	
	function crawlTableMaxId($tableName) {
		$tableName = trim ( $tableName );
		if (! $tableName)
			return '';
		$tableSettingService = ACloud_Sys_Core_Common::loadSystemClass ( 'table.settings', 'config.service' );
		$tableSetting = $tableSettingService->getSettingByTableNameWithReplace ( $tableName );
		if (! ACloud_Sys_Core_S::isArray ( $tableSetting ))
			return '';
		if (! $tableSetting ['status'])
			return '';
		$maxId = $this->getMaxPrimaryKeyId ( $tableSetting );
		return $this->outputDataFlow ( array (array ('maxid' => $maxId ) ) );
	}
	
	function crawlTableByIdRange($startId, $endId, $tableName) {
		list ( $tableName, $startId, $endId ) = array (trim ( $tableName ), intval ( $startId ), intval ( $endId ) );
		if (! $tableName)
			return '';
		if ($startId < 0 || $startId > $endId || $endId < 1)
			return '';
		$tableSettingService = ACloud_Sys_Core_Common::loadSystemClass ( 'table.settings', 'config.service' );
		$tableSetting = $tableSettingService->getSettingByTableNameWithReplace ( $tableName );
		if (! ACloud_Sys_Core_S::isArray ( $tableSetting ))
			return '';
		if (! $tableSetting ['status'])
			return '';
		if (! $tableSetting ['primary_key'])
			return '';
		$data = $this->getDataByPrimaryKeyRange ( $tableSetting, $startId, $endId );
		return $this->outputDataFlow ( $data );
	}
	
	function crawlDelta() {
	
	}
	
	function crawlSqlLog($startTime, $endTime, $page, $perpage) {
		list ( $startTime, $endTime, $page, $perpage ) = array (intval ( $startTime ), intval ( $endTime ), intval ( $page ), intval ( $perpage ) );
		if ($startTime > $endTime)
			return '';
		$page < 1 && $page = 1;
		$perpage < 1 && $perpage = $this->perpage;
		$sqlLogService = ACloud_Sys_Core_Common::loadSystemClass ( 'sql.log', 'config.service' );
		$result = $sqlLogService->getSqlLogsByTimestamp ( $startTime, $endTime, $page, $perpage );
		if (! ACloud_Sys_Core_S::isArray ( $result ))
			return '';
		return $this->outputDataFlow ( $result );
	}
	
	function crawlSqlLogCount($startTime, $endTime) {
		list ( $startTime, $endTime ) = array (intval ( $startTime ), intval ( $endTime ) );
		if ($startTime > $endTime)
			return '';
		$sqlLogService = ACloud_Sys_Core_Common::loadSystemClass ( 'sql.log', 'config.service' );
		$result = $sqlLogService->countSqlLogsByTimestamp ( $startTime, $endTime );
		return $this->outputDataFlow ( array (array ('count' => intval ( $result ) ) ) );
	}
	
	function deleteSqlLog($startTime, $endTime) {
		list ( $startTime, $endTime ) = array (intval ( $startTime ), intval ( $endTime ) );
		$sqlLogService = ACloud_Sys_Core_Common::loadSystemClass ( 'sql.log', 'config.service' );
		$result = $sqlLogService->deleteSqlLogByTimestamp ( $startTime, $endTime );
		return $this->outputDataFlow ( array (array ('delete' => intval ( $result ) ) ) );
	}
	
	function crawlDeletedId($type, $startId, $endId) {
		list ( $type, $startId, $endId ) = array (trim ( strtolower ( $type ) ), intval ( $startId ), intval ( $endId ) );
		if (! $type)
			return '';
		if ($startId < 0 || $startId > $endId || $endId < 1)
			return '';
		list ( $commonFactory, $method ) = array ($this->getVerCommonFactory (), 'getVersionCommon' . ucfirst ( $type ) );
		if (! method_exists ( $commonFactory, $method ))
			return '';
		$service = $commonFactory->$method ();
		if (! $service || ! is_object ( $service ) || ! method_exists ( $service, 'getDeletedId' ))
			return '';
		$result = $service->getDeletedId ( $startId, $endId );
		return $this->outputDataFlow ( $result );
	}
	
	function crawlThreadRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		$commonFactory = $this->getVerCommonFactory ();
		$commonThread = $commonFactory->getVersionCommonThread ();
		$result = $commonThread->getThreadsByRange ( $startId, $endId );
		return $this->outputDataFlow ( $result );
	}
	
	function crawlMemberRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		$commonFactory = $this->getVerCommonFactory ();
		$commonUser = $commonFactory->getVersionCommonUser ();
		$result = $commonUser->getUsersByRange ( $startId, $endId );
		return $this->outputDataFlow ( $result );
	}
	
	function crawlPostRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		$commonFactory = $this->getVerCommonFactory ();
		$commonPost = $commonFactory->getVersionCommonPost ();
		$result = $commonPost->getPostsByRange ( $startId, $endId );
		return $this->outputDataFlow ( $result );
	}
	
	function crawlPostMaxId() {
		$commonFactory = $this->getVerCommonFactory ();
		$commonPost = $commonFactory->getVersionCommonPost ();
		$maxId = $commonPost->getPostMaxId ();
		return $this->outputDataFlow ( array (array ('maxid' => $maxId ) ) );
	}
	
	function crawlAttachRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		$commonFactory = $this->getVerCommonFactory ();
		$commonAttach = $commonFactory->getVersionCommonAttach ();
		$result = $commonAttach->getAttachsByRange ( $startId, $endId );
		return $this->outputDataFlow ( $result );
	}
	
	function crawlForumRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		$commonFactory = $this->getVerCommonFactory ();
		$commonForum = $commonFactory->getVersionCommonForum ();
		$result = $commonForum->getForumsByRange ( $startId, $endId );
		return $this->outputDataFlow ( $result );
	}
	
	function crawlDiaryRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		$commonFactory = $this->getVerCommonFactory ();
		$commonDiary = $commonFactory->getVersionCommonDiary ();
		$result = $commonDiary->getDiarysByRange ( $startId, $endId );
		return $this->outputDataFlow ( $result );
	}
	
	function crawlColonyRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		$commonFactory = $this->getVerCommonFactory ();
		$commonColony = $commonFactory->getVersionCommonColony ();
		$result = $commonColony->getColonysByRange ( $startId, $endId );
		return $this->outputDataFlow ( $result );
	}
	
	function crawlThreadDelta($startTime, $endTime, $page, $perpage) {
		list ( $startTime, $endTime, $page, $perpage ) = array (intval ( $startTime ), intval ( $endTime ), intval ( $page ), intval ( $perpage ) );
		$page < 1 && $page = 1;
		$perpage < 1 && $perpage = $this->perpage;
		$commonFactory = $this->getVerCommonFactory ();
		$commonThread = $commonFactory->getVersionCommonThread ();
		$result = $commonThread->getThreadsByLastPost ( $startTime, $endTime, $page, $perpage );
		return $this->outputDataFlow ( $result );
	}
	
	function crawlThreadDeltaCount($startTime, $endTime) {
		list ( $startTime, $endTime ) = array (intval ( $startTime ), intval ( $endTime ) );
		$commonFactory = $this->getVerCommonFactory ();
		$commonThread = $commonFactory->getVersionCommonThread ();
		$result = $commonThread->getThreadDeltaCount ( $startTime, $endTime );
		return $this->outputDataFlow ( array (array ('count' => $result ) ) );
	}
	
	function getTableDataByPrimaryKey($tableSetting, $tableName, $page) {
		list ( $start, $end ) = $this->getIdRange ( $page );
		$generalDataService = ACloud_Sys_Core_Common::loadSystemClass ( 'generaldata', 'config.service' );
		$maxId = $this->getMaxPrimaryKeyId ( $tableSetting );
		if ($maxId < 1)
			return array (0, array () );
		$data = $this->getDataByPrimaryKeyRange ( $tableSetting, $start, $end );
		return array ($maxId, $data );
	}
	
	function getTableDataWithoutPrimaryKey($tableSetting, $tableName, $page) {
		list ( $offset, $limit ) = $this->getPageRange ( $page );
		$generalDataService = ACloud_Sys_Core_Common::loadSystemClass ( 'generaldata', 'config.service' );
		$countSql = sprintf ( 'SELECT COUNT(*) as count FROM %s', ACloud_Sys_Core_S::sqlMetadata ( $tableSetting ['name'] ) );
		list ( $count ) = $generalDataService->executeSql ( $countSql );
		$count = $count ['count'];
		if ($count < 1)
			return array (0, array () );
		$dataSql = sprintf ( 'SELECT * FROM %s %s', ACloud_Sys_Core_S::sqlMetadata ( $tableSetting ['name'] ), ACloud_Sys_Core_S::sqlLimit ( $offset, $limit ) );
		$data = $generalDataService->executeSql ( $dataSql );
		return array ($count, $data );
	}
	
	function outputDataFlow($data, $totalPages = null) {
		$charset = ACloud_Sys_Core_Common::getGlobal ( 'g_charset' );
		$formatService = ACloud_Sys_Core_Common::loadSystemClass ( 'format' );
		return $formatService->dataFlowXmlFormat ( $data, $charset, $totalPages );
	}
	
	function getMaxPrimaryKeyId($tableSetting) {
		$generalDataService = ACloud_Sys_Core_Common::loadSystemClass ( 'generaldata', 'config.service' );
		$countSql = sprintf ( 'SELECT MAX(%s) as count FROM %s', ACloud_Sys_Core_S::sqlMetadata ( $tableSetting ['primary_key'] ), ACloud_Sys_Core_S::sqlMetadata ( $tableSetting ['name'] ) );
		list ( $result ) = $generalDataService->executeSql ( $countSql );
		return $result ['count'];
	}
	
	function getDataByPrimaryKeyRange($tableSetting, $start, $end) {
		$generalDataService = ACloud_Sys_Core_Common::loadSystemClass ( 'generaldata', 'config.service' );
		$dataSql = sprintf ( 'SELECT * FROM %s WHERE %s >= %s AND %s <= %s', ACloud_Sys_Core_S::sqlMetadata ( $tableSetting ['name'] ), ACloud_Sys_Core_S::sqlMetadata ( $tableSetting ['primary_key'] ), ACloud_Sys_Core_S::sqlEscape ( $start ), ACloud_Sys_Core_S::sqlMetadata ( $tableSetting ['primary_key'] ), ACloud_Sys_Core_S::sqlEscape ( $end ) );
		return $generalDataService->executeSql ( $dataSql );
	}
	
	function getIdRange($page) {
		$page = intval ( $page ) > 0 ? intval ( $page ) : 1;
		$start = ($page - 1) * $this->perpage + 1;
		$end = $start + $this->perpage - 1;
		return array ($start, $end );
	}
	
	function getPageRange($page) {
		$page = intval ( $page ) > 0 ? intval ( $page ) : 1;
		$start = ($page - 1) * $this->perpage;
		$start = intval ( $start );
		return array ($start, $this->perpage, $page );
	}
	
	function setPerpage($perpage) {
		$perpage = intval ( $perpage );
		if ($perpage < 1)
			return false;
		$this->perpage = $perpage;
		return true;
	}
	
	function getVerCommonFactory() {
		require_once ACLOUD_VERSION_PATH . '/common/ver.common.factory.php';
		return ACloud_Ver_Common_Factory::getInstance ();
	}
}