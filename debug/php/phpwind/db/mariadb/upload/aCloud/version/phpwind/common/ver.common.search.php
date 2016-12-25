<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Ver_Common_Search extends ACloud_Ver_Common_Base {
	
	function getHotwords() {
		global $db_hotwords;
		$hotwords = ($db_hotwords) ? explode ( ",", $db_hotwords ) : array ();
		return $this->buildResponse ( 0, $hotwords );
	}
}