<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Ver_Common_Diary extends ACloud_Ver_Common_Base {
	
	function getPrimaryKeyAndTable() {
		return array ('pw_diary', 'did' );
	}
	
	function getDiarysByRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		if ($startId < 0 || $startId > $endId || $endId < 1)
			return array ();
		$query = $GLOBALS ['db']->query ( "SELECT * FROM pw_diary WHERE privacy = 0 AND did >= " . S::sqlEscape ( $startId ) . " AND did <= " . S::sqlEscape ( $endId ) );
		$diarys = array ();
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$rt ['diaryurl'] = $GLOBALS ['db_bbsurl'] . '/apps.php?q=diary&a=detail&did=' . $rt ['did'] . '&uid=' . $rt ['uid'];
			$diarys [$rt ['did']] = $rt;
		}
		return $diarys;
	}
}