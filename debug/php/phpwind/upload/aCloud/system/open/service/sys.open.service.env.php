<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Sys_Open_Service_Env {
	
	function checkFunctions() {
		$keys = array ('fsockopen', 'parse_url', 'gethostbyname', 'md5_file', 'http_build_query', 'curl_init' );
		$data = array ();
		foreach ( $keys as $key ) {
			$data [$key] = function_exists ( $key );
		}
		return $data;
	}
	
	function getNetWorkSpeed() {
		$time_start = ACloud_Sys_Core_Common::getMicrotime ();
		require_once ACLOUD_PATH . '/system/core/sys.core.http.php';
		$result = ACloud_Sys_Core_Http::sendPost ( $this->buildPostParams ( 'env.ping', array ('speed' => $time_start ) ) );
		$time_end = (is_object ( $result ) && $result->code == 100) ? ACloud_Sys_Core_Common::getMicrotime () : $time_start - 1;
		return number_format ( $time_end - $time_start, 4 );
	}
	
	function getNetWorkInterflow() {
		require_once ACLOUD_PATH . '/system/core/sys.core.http.php';
		$result = ACloud_Sys_Core_Http::sendPost ( $this->buildPostParams ( 'env.interflow', array () ) );
		return (is_object ( $result ) && $result->code == 100) ? true : false;
	}
	
	function hasIndexFile() {
		return is_file ( ACLOUD_PATH . '/index.php' ) ? true : false;
	}
	
	function getServerInfo() {
		$keys = array ('SERVER_SOFTWARE', 'SERVER_PROTOCOL', 'HTTP_USER_AGENT', 'HTTP_ACCEPT_LANGUAGE', 'HTTP_ACCEPT_ENCODING', 'HTTP_ACCEPT_CHARSET', 'HTTP_CONNECTION' );
		$params = array ();
		foreach ( $keys as $key ) {
			$params [$key] = isset ( $_SERVER [$key] ) ? $_SERVER [$key] : 'unknow';
		}
		$params ['ACLOUD_V'] = ACLOUD_V;
		$params ['ACLOUD_VERSION'] = ACLOUD_VERSION;
		$params ['ACLOUD_HOST_API'] = ACLOUD_HOST_API;
		$params ['ACLOUD_HOST_APP'] = ACLOUD_HOST_APP;
		$params ['ACLOUD_API_VERSION'] = ACLOUD_API_VERSION;
		
		require_once sprintf ( ACLOUD_PATH . '/version/%s/core/ver.core.site.php', ACLOUD_VERSION );
		$hookService = new ACloud_Ver_Core_Site ();
		$sites = $hookService->execute ();
		
		return array_merge ( $params, $sites );
	}
	
	function getFilesInfo() {
		$sysFiles = ACloud_Sys_Core_Common::listDir ( ACLOUD_PATH . '/system/' );
		$tmp = array ();
		foreach ( $sysFiles as $file ) {
			$filename = basename ( $file );
			$tmp [$filename] = md5_file ( $file );
		}
		return $tmp;
	}
	
	function getIpLists($ips = array()) {
		$ips = array_merge ( $ips, ( array ) ACloud_Sys_Core_Common::getGlobal ( 'g_ips' ) );
		return array_merge ( $ips, array ('110.75.164.x', '110.75.168.x', '110.75.171.x', '110.75.172.x', '110.75.173.x', '110.75.174.x', '110.75.175.x', '110.75.176.x', '110.75.167.x' ) );
	}
	
	function buildPostParams($method, $data) {
		$params = array ();
		$params ['method'] = $method;
		$params ['version'] = ACLOUD_V;
		$params ['siteurl'] = ACloud_Sys_Core_Common::getGlobal ( 'g_siteurl' );
		$params ['sitename'] = ACloud_Sys_Core_Common::getGlobal ( 'g_sitename' );
		$params ['charset'] = ACloud_Sys_Core_Common::getGlobal ( 'g_charset' );
		$params ['ip'] = ACloud_Sys_Core_Common::getIp ();
		$params ['ua'] = $_SERVER ['HTTP_USER_AGENT'];
		$params ['posttime'] = time ();
		return $params;
	}
}