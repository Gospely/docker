<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Ver_Common_Utility {
	
	function getThumbAttach($attachurl, $ifthumb = false) {
		global $attachpath, $attachdir, $db_ftpweb;
		$a_url = geturl ( $attachurl, 'show', $ifthumb );
		list ( $wind_version ) = explode ( ',', WIND_VERSION );
		if ($wind_version >= 8.0) {
			$thumburl = $this->getMiniUrl ( $attachurl, $ifthumb, $a_url [1] );
		} else {
			$thumburl = $a_url [0];
		}
		if ($a_url [1] == 'Local') {
			$sourceurl = $attachpath . '/' . $attachurl;
		} elseif ($a_url [1] == 'Ftp') {
			$sourceurl = $db_ftpweb . '/' . $attachurl;
		}
		return array ($sourceurl, $thumburl );
	}
	
	function getMiniUrl($path, $ifthumb, $where) {
		$dir = '';
		($ifthumb & 1) && $dir = 'thumb/';
		($ifthumb & 2) && $dir = 'thumb/mini/';
		if ($where == 'Local')
			return $GLOBALS ['attachpath'] . '/' . $dir . $path;
		if ($where == 'Ftp')
			return $GLOBALS ['db_ftpweb'] . '/' . $dir . $path;
		if (! is_array ( $GLOBALS ['attach_url'] ))
			return $GLOBALS ['attach_url'] . '/' . $dir . $path;
		return $GLOBALS ['attach_url'] [0] . '/' . $dir . $path;
	}
	
	function getUserIcon($iconData) {
		require_once R_P . 'require/showimg.php';
		list ( $icon ) = showfacedesign ( $iconData, '1', 'm' );
		return $icon;
	}
}