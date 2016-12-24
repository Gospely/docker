<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

define ( 'THREAD_INVALID_PARAMS', 301 );

class ACloud_Ver_Common_Thread extends ACloud_Ver_Common_Base {
	
	function getPrimaryKeyAndTable() {
		return array ('pw_threads', 'tid' );
	}
	
	function shieldThread($tid, $fid) {
		list ( $tid, $fid ) = array (intval ( $tid ), intval ( $fid ) );
		if ($tid < 1 || $fid < 1)
			return $this->buildResponse ( THREAD_INVALID_PARAMS );
		$GLOBALS ['db']->query ( "UPDATE pw_threads SET ifcheck=0 WHERE tid=" . S::sqlEscape ( $tid ) );
		if (class_exists ( "Perf" )) {
			Perf::gatherInfo ( 'changeThreadWithForumIds', array ('fid' => $fid ) );
		} else {
			$threadList = L::loadClass ( "threadlist", 'forum' );
			($threadList) && $threadList->refreshThreadIdsByForumId ( $fid );
		}
		return $this->buildResponse ( 0 );
	}
	
	function getThreadsByRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		if ($startId < 0 || $startId > $endId || $endId < 1)
			return array ();
		$query = $GLOBALS ['db']->query ( "SELECT t.*,f.name as forumname FROM pw_threads t LEFT JOIN pw_forums f USING(fid) WHERE t.fid != 0 AND t.ifcheck = 1 AND t.tid >= " . S::sqlEscape ( $startId ) . " AND t.tid <= " . S::sqlEscape ( $endId ) );
		return $this->getThreadDataWithTmsgs ( $query );
	}
	
	function getThreadDeltaCount($startTime, $endTime) {
		return $GLOBALS ['db']->get_value ( "SELECT COUNT(*) as count FROM pw_threads WHERE fid != 0 AND ifcheck = 1 AND lastpost >= " . S::sqlEscape ( $startTime ) . " AND lastpost <= " . S::sqlEscape ( $endTime ) );
	}
	
	function getThreadsByLastPost($startTime, $endTime, $page, $perpage) {
		list ( $startTime, $endTime, $page, $perpage ) = array (intval ( $startTime ), intval ( $endTime ), intval ( $page ), intval ( $perpage ) );
		if ($startTime < 1 || $endTime < 1 || $startTime > $endTime || $page < 1 || $perpage < 1)
			return array ();
		$offset = ($page - 1) * $perpage;
		$query = $GLOBALS ['db']->query ( "SELECT t.*,f.name as forumname FROM pw_threads t LEFT JOIN pw_forums f USING(fid) WHERE t.fid != 0 AND t.ifcheck = 1 AND t.lastpost >= " . S::sqlEscape ( $startTime ) . " AND lastpost <= " . S::sqlEscape ( $endTime ) . S::sqlLimit ( $offset, $perpage ) );
		return $this->getThreadDataWithTmsgs ( $query );
	}
	
	function getThreadDataWithTmsgs($query) {
		$threads = $tmsgsTables = array ();
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$threads [$rt ['tid']] = $rt;
			$tmsgsTableName = GetTtable ( $rt ['tid'] );
			$tmsgsTables [$tmsgsTableName] [] = $rt ['tid'];
		}
		if (! S::isArray ( $threads ))
			return array ();
		foreach ( $tmsgsTables as $tableName => $tids ) {
			$tmsgsQuery = $GLOBALS ['db']->query ( "SELECT * FROM " . S::sqlMetaData ( $tableName ) . " WHERE tid IN(" . S::sqlImplode ( $tids ) . ")" );
			while ( $rt = $GLOBALS ['db']->fetch_array ( $tmsgsQuery ) ) {
				$rt ['threadurl'] = $GLOBALS ['db_bbsurl'] . '/read.php?tid=' . $rt ['tid'];
				$rt ['forumurl'] = $GLOBALS ['db_bbsurl'] . '/thread.php?fid=' . $threads [$rt ['tid']] ['fid'];
				$threads [$rt ['tid']] = array_merge ( $threads [$rt ['tid']], $rt );
			}
		}
		return $threads;
	}
}