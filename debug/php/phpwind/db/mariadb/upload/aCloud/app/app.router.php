<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_App_Router {
	
	function getAppsByPage($page) {
		$apps = $this->getApps ();
		$tmp = array ();
		foreach ( $apps as $app => $config ) {
			if (isset ( $config ['page'] ) && in_array ( $page, explode ( "|", $config ['page'] ) )) {
				$tmp [] = $app;
			}
		}
		return $tmp;
	}
	
	function getApps() {
		$apps = array ();
		$apps ['search'] = array ('page' => 'searcher|search' );
		return $apps;
	}
}