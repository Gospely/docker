<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

define ( 'ATTACH_INVALID_PARAMS', 851 );

class ACloud_Ver_Common_Attach extends ACloud_Ver_Common_Base {
	
	function getPrimaryKeyAndTable() {
		return array ('pw_attachs', 'aid' );
	}
	
	function getImgAttaches($aids) {
		if (! S::isArray ( $aids ))
			return $this->buildResponse ( ATTACH_INVALID_PARAMS );
		$result = array ();
		$query = $GLOBALS ['db']->query ( "SELECT aid, attachurl FROM pw_attachs WHERE aid IN (" . S::sqlImplode ( $aids ) . ") AND type='img'" );
		$utilityService = $this->getCommonUtilityService ();
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			list ( $attachurl, $attachThumbUrl ) = $utilityService->getThumbAttach ( $rt ['attachurl'], false );
			$result [$rt ['aid']] = array ('url' => $attachurl );
		}
		return $this->buildResponse ( 0, $result );
	}
	
	function getImgAttachesByTids($tids) {
		if (! S::isArray ( $tids ))
			return $this->buildResponse ( ATTACH_INVALID_PARAMS );
		$result = array ();
		$query = $GLOBALS ['db']->query ( "SELECT tid, attachurl FROM pw_attachs WHERE tid IN (" . S::sqlImplode ( $tids ) . ") AND pid=0 AND did=0 AND type='img'" );
		$utilityService = $this->getCommonUtilityService ();
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			list ( $attachurl, $attachThumbUrl ) = $utilityService->getThumbAttach ( $rt ['attachurl'], false );
			$result [$rt ['tid']] [] = $attachurl;
		}
		return $this->buildResponse ( 0, $result );
	}
	
	function getAttachsByRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		if ($startId < 0 || $startId > $endId || $endId < 1)
			return array ();
		$query = $GLOBALS ['db']->query ( "SELECT * FROM pw_attachs WHERE aid >= " . S::sqlEscape ( $startId ) . " AND aid <= " . S::sqlEscape ( $endId ) );
		$attachs = array ();
		$utilityService = $this->getCommonUtilityService ();
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			list ( $rt ['attachurl'] ) = $utilityService->getThumbAttach ( $rt ['attachurl'], false );
			$attachs [$rt ['aid']] = $rt;
		}
		return $attachs;
	}
}