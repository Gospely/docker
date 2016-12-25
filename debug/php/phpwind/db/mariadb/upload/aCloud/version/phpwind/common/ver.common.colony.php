<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Ver_Common_Colony extends ACloud_Ver_Common_Base {
	
	function getPrimaryKeyAndTable() {
		return array ('pw_colonys', 'id' );
	}
	
	function getColonysByRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		if ($startId < 0 || $startId > $endId || $endId < 1)
			return array ();
		$query = $GLOBALS ['db']->query ( "SELECT * FROM pw_colonys WHERE id >= " . S::sqlEscape ( $startId ) . " AND id <= " . S::sqlEscape ( $endId ) );
		$colonys = array ();
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$rt ['colonyurl'] = $GLOBALS ['db_bbsurl'] . '/apps.php?q=group&cyid=' . $rt ['id'];
			$colonys [$rt ['id']] = $rt;
		}
		return $colonys;
	}
}