<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Sys_Verify_Service_Control {
	
	function doubleControl($data) {
		return ($this->ipControl () && $this->oauthControl ( $data ) && $this->aseControl ( $data )) ? true : false;
	}
	
	function aseControl($data) {
		if (! is_array ( $data ) || ! isset ( $data ['ciphertext'] ) || ! $data ['ciphertext']) {
			return false;
		}
		$keysService = ACloud_Sys_Core_Common::loadSystemClass ( 'keys', 'config.service' );
		$keys = $keysService->getKey123 ( 1 );
		if (! $keys || strlen ( $keys ['key1'] ) != 128 || strlen ( $keys ['key2'] ) != 128 || strlen ( $keys ['key3'] ) != 128) {
			return false;
		}
		require_once ACLOUD_PATH . '/system/core/sys.core.aes.php';
		$aesService = new ACloud_Sys_Core_Aes ();
		$key = $aesService->encrypt ( $keys ['key3'], $keys ['key2'], 256 );
		if (! $key) {
			return false;
		}
		$plaintext = $aesService->strcode ( $data ['ciphertext'], $key, 'DECODE' );
		if (! $plaintext) {
			return false;
		}
		$params = ACloud_Sys_Core_Http::splitHttpQuery ( $plaintext );
		if (! is_array ( $params )) {
			return false;
		}
		$tmp = ACloud_Sys_Core_Common::arrayIntersectAssoc ( $params, $data );
		if (is_array ( $tmp ) && count ( $tmp ) > 0 && (count ( $tmp ) == count ( $params )) && ($tmp ['securecode'] === $data ['securecode'])) {
			return true;
		}
		return false;
	}
	
	function oauthControl($data) {
		$keysService = ACloud_Sys_Core_Common::loadSystemClass ( 'keys', 'config.service' );
		$key1 = $keysService->getKey1 ( 1 );
		if (! $key1 || strlen ( $key1 ) != 128 || ! is_array ( $data ) || count ( $data ) < 4)
			return false;
		require_once ACLOUD_PATH . '/system/core/sys.core.verify.php';
		if (ACloud_Sys_Core_Verify::verifyWithOAuth ( $data, $key1 ))
			return true;
		return false;
	}
	
	function apiControl($data) {
		$appsService = ACloud_Sys_Core_Common::loadSystemClass ( 'apps', 'config.service' );
		if (! $data || ! is_array ( $data ) || ! isset ( $data ['app_id'] ) || ! $data ['app_id'])
			return false;
		$app = $appsService->getApp ( $data ['app_id'] );
		if (! $app || strlen ( $app ['app_token'] ) != 128)
			return false;
		require_once ACLOUD_PATH . '/system/core/sys.core.verify.php';
		if (ACloud_Sys_Core_Verify::verifyWithOAuth ( $data, $app ['app_token'] ))
			return true;
		return false;
	}
	
	function identifyControl($data) {
		if (! is_array ( $data ) || ! $data)
			return false;
		$initService = ACloud_Sys_Core_Common::loadSystemClass ( 'init', 'open.service' );
		list ( $bool ) = $initService->identifyKey ( $data );
		return $bool;
	}
	
	function ipControl($ips = array()) {
		$ip = ACloud_Sys_Core_Common::getIp ();
		if ($this->spiderControl () || ! $ip) {
			return false;
		}
		list ( $ip1, $ip2, $ip3 ) = explode ( ".", $ip );
		$envService = ACloud_Sys_Core_Common::loadSystemClass ( 'env', 'open.service' );
		if (! in_array ( $ip1 . "." . $ip2 . "." . $ip3 . ".x", $envService->getIpLists () )) {
			return false;
		}
		return true;
	}
	
	function spiderControl() {
		$user_agent = strtolower ( $_SERVER ['HTTP_USER_AGENT'] );
		$allow_spiders = array ('Baiduspider', 'Googlebot' );
		foreach ( $allow_spiders as $spider ) {
			$spider = strtolower ( $spider );
			if (strpos ( $user_agent, $spider ) !== false) {
				return true;
			}
		}
		return false;
	}

}