<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Sys_Core_Http {
	
	function getCloudApi() {
		return sprintf ( "http://%s/api.php?", ACLOUD_HOST_API );
	}
	
	function sendPost($data) {
		require_once ACLOUD_PATH . '/system/core/sys.core.httpclient.php';
		$data = ACloud_Sys_Core_Http::httpBuildQuery ( $data );
		$result = ACloud_Sys_Core_HttpClient::post ( ACloud_Sys_Core_Http::getCloudApi (), $data );
		return ACloud_Sys_Core_Common::jsonDecode ( $result );
	}
	
	function httpBuildQuery($params) {
		if (function_exists ( "http_build_query" ))
			return http_build_query ( $params );
		
		if (! $params || ! is_array ( $params )) {
			return '';
		}
		$query = '';
		foreach ( $params as $key => $value ) {
			$query .= "$key=" . urlencode ( $value ) . '&';
		}
		return $query;
	}
	
	function splitHttpQuery($query) {
		if (! $query) {
			return array ();
		}
		$query = explode ( "&", $query );
		$params = array ();
		foreach ( $query as $q ) {
			list ( $key, $value ) = explode ( "=", $q );
			$params [$key] = urldecode ( $value );
		}
		return $params;
	}
}