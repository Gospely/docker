<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Ver_Common_Forum extends ACloud_Ver_Common_Base {
	
	function getPrimaryKeyAndTable() {
		return array ('pw_forums', 'fid' );
	}
	
	function getForumsByRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		if ($startId < 0 || $startId > $endId || $endId < 1)
			return array ();
		$query = $GLOBALS ['db']->query ( "SELECT * FROM pw_forums WHERE fid >= " . S::sqlEscape ( $startId ) . " AND fid <= " . S::sqlEscape ( $endId ) );
		$forums = array ();
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$rt ['forumurl'] = $GLOBALS ['db_bbsurl'] . '/thread.php?fid=' . $rt ['fid'];
			$forums [$rt ['fid']] = $rt;
		}
		return $forums;
	}
}