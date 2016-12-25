<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

define ( 'POST_INVALID_PARAMS', 301 );

class ACloud_Ver_Common_Post extends ACloud_Ver_Common_Base {
	
	function getPrimaryKeyAndTable() {
		return array ('pw_posts', 'pid' );
	}
	
	function getDeletedId($startId, $endId) {
		list ( $tableName, $tmpStart, $tmpEnd ) = $this->getTableInfos ( $startId, $endId );
		if (! $tableName)
			return array ();
		list ( $existIds, $allIds ) = array (array (), range ( $startId, $endId ) );
		$query = $GLOBALS ['db']->query ( sprintf ( "SELECT pid FROM %s WHERE pid >= %s AND pid <= %s", S::sqlMetaData ( $tableName ), S::sqlEscape ( $tmpStart ), S::sqlEscape ( $tmpEnd ) ) );
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$existIds [] = $rt ['pid'];
		}
		if (! S::isArray ( $existIds ))
			return array ();
		return $this->formatDeleteIds ( array_diff ( $allIds, $existIds ) );
	}
	
	function shieldPost($pid, $tid) {
		list ( $pid, $tid ) = array (intval ( $pid ), intval ( $tid ) );
		if ($pid < 1 || $tid < 1)
			return $this->buildResponse ( POST_INVALID_PARAMS );
		$postTable = GetPtable ( 'N', $tid );
		$GLOBALS ['db']->query ( "UPDATE " . S::sqlMetadata ( $postTable ) . " SET ifshield=1 WHERE pid=" . S::sqlEscape ( $pid ) );
		$bool = $GLOBALS ['db']->affected_rows ();
		return $this->buildResponse ( $bool == 1 ? 0 : 1 );
	}
	
	function getPostsByRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		if ($startId < 0 || $startId > $endId || $endId < 1)
			return array ();
		list ( $tableName, $tmpStart, $tmpEnd ) = $this->getTableInfos ( $startId, $endId );
		if (! $tableName || ! $tmpEnd)
			return array ();
		$query = $GLOBALS ['db']->query ( "SELECT p.*,f.name as forumname FROM " . S::sqlMetaData ( $tableName ) . " p LEFT JOIN pw_forums f USING(fid) WHERE p.ifcheck = 1 AND p.ifshield = 0 AND p.pid >= " . S::sqlEscape ( $startId ) . " AND p.pid <= " . S::sqlEscape ( $endId ) );
		$posts = array ();
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$rt ['posturl'] = $GLOBALS ['db_bbsurl'] . '/job.php?action=topost&tid=' . $rt ['tid'] . '&pid=' . $rt ['pid'];
			$rt ['forumurl'] = $GLOBALS ['db_bbsurl'] . '/thread.php?fid=' . $rt ['fid'];
			$posts [$rt ['pid']] = $rt;
		}
		return $posts;
	}
	
	function getPostMaxId() {
		$table = ($GLOBALS ['db_plist'] && count ( $GLOBALS ['db_plist'] ) > 1) ? 'pw_pidtmp' : 'pw_posts';
		$result = $this->getMinAndMaxIdByTableName ( $table );
		return $result ['max'];
	}
	
	function getTableInfos($minId, $maxId) {
		$tableInfo = $this->getTableMaxIds ();
		if (! $tableInfo)
			return array ('', 0, 0 );
		$tableName = '';
		$tmpStart = $tmpEnd = 0;
		foreach ( $tableInfo as $table => $info ) {
			list ( $bool, $tmpStart, $tmpEnd ) = $this->countPage ( $minId, $maxId, $info ['min'], $info ['max'] );
			if ($bool) {
				$tableName = $table;
				break;
			}
		}
		if (! $tableName || ! $tmpEnd)
			return array ('', 0, 0 );
		return array ($tableName, $tmpStart, $tmpEnd );
	}
	
	function getTableMaxIds() {
		$dbposts = ($GLOBALS ['db_plist']) ? $GLOBALS ['db_plist'] : array (0 );
		$tables = $tableInfo = array ();
		foreach ( $dbposts as $k => $v ) {
			$k = ($k > 0) ? $k : '';
			$tables [] = 'pw_posts' . $k;
		}
		foreach ( $tables as $table ) {
			$tableInfo [$table] = $this->getMinAndMaxIdByTableName ( $table );
		}
		return ($tableInfo) ? $tableInfo : array ();
	}
	
	function getMinAndMaxIdByTableName($tableName) {
		if ($tableName != 'pw_pidtmp' && ! preg_match ( '|^pw_posts\d*$|i', $tableName ))
			return array ('min' => 0, 'max' => 0 );
		return $GLOBALS ['db']->get_one ( "SELECT min(pid) AS min,max(pid) AS max FROM " . S::sqlMetaData ( $tableName ) );
	}
	
	function countPage($start, $end, $min, $max) {
		if ($start >= $min && $end <= $max) {
			return array (true, $start, $end );
		}
		if ($start >= $min && $start < $max && $end > $max) {
			return array (true, $start, $max );
		}
		if ($start < $min && $end >= $min && $end < $max) {
			return array (true, $min, $end );
		}
		if ($start < $min && $start < $max && $end >= $max) {
			return array (true, $min, $max );
		}
		return array (false, 0, 0 );
	}
}