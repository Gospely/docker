<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Ver_Core_App {
	
	function getAppOutPut() {
		$data = array ();
		$sign = ACloud_Sys_Core_Common::getSiteSign ();
		$data ['src'] = SCR;
		$data ['url'] = ACloud_Sys_Core_Common::getGlobal ( 'g_siteurl', $_SERVER ['SERVER_NAME'] );
		$data ['sn'] = ACloud_Sys_Core_Common::getSiteUnique ();
		$data ['fid'] = ACloud_Sys_Core_Common::getGlobal ( 'fid', 0 );
		$data ['uid'] = ACloud_Sys_Core_Common::getGlobal ( 'winduid', 0 );
		$data ['tid'] = ACloud_Sys_Core_Common::getGlobal ( 'tid', 0 );
		$data [$sign] = ACloud_Ver_Core_App::getSyncData ( $sign );
		$data ['charset'] = ACloud_Sys_Core_Common::getGlobal ( 'g_charset', 'gbk' );
		$data ['username'] = ACloud_Sys_Core_Common::getGlobal ( 'windid', 0 );
		$data ['title'] = ACloud_Sys_Core_Common::getGlobal ( 'subject', '' );
		$data ['_ua'] = ACloud_Sys_Core_Common::getSiteUserAgent ();
		$data ['_shr'] = base64_encode ( isset ( $_SERVER ['HTTP_REFERER'] ) ? $_SERVER ['HTTP_REFERER'] : '' );
		$data ['_sqs'] = base64_encode ( isset ( $_SERVER ['QUERY_STRING'] ) ? $_SERVER ['QUERY_STRING'] : '' );
		$data ['_ssn'] = base64_encode ( isset ( $_SERVER ['SCRIPT_NAME'] ) ? $_SERVER ['SCRIPT_NAME'] : '' );
		$data ['_t'] = ACloud_Sys_Core_Common::getGlobal ( 'timestamp' ) + 86400;
		$data ['_v'] = rand ( 1000, 9999 );
		
		require_once ACLOUD_PATH . '/system/core/sys.core.http.php';
		$url = sprintf ( "http://%s/?%s", ACLOUD_HOST_APP, ACloud_Sys_Core_Http::httpBuildQuery ( $data ) );
		$output = "<script type=\"text/javascript\">(function(d,t){var url=\"$url\";var g=d.createElement(t);g.async=1;g.src=url;d.body.insertBefore(g,d.body.firstChild);}(document,\"script\"));</script>";
		return $output;
	}
	
	function getSyncData($sign) {
		$syncType = isset ( $_COOKIE ['_ac_' . $sign] ) ? intval ( $_COOKIE ['_ac_' . $sign] ) : 0;
		if (! $syncType)
			return 0;
		setcookie ( '_ac_' . $sign, '', time () - 3600 );
		return $syncType;
	}
}