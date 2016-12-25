<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Sys_Core_OAuth {
	
	function createHttpQuery($params) {
		if (! $params || ! is_array ( $params )) {
			return '';
		}
		ksort ( $params );
		require_once ACLOUD_PATH . '/system/core/sys.core.http.php';
		return ACloud_Sys_Core_Http::httpBuildQuery ( $params );
	}
	
	function createHttpSign($plaintext) {
		return md5 ( $plaintext );
	}

}