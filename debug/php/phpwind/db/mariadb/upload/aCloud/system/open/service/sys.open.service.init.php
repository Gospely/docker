<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_PATH . '/system/core/sys.core.http.php';
require_once ACLOUD_PATH . '/system/core/sys.core.verify.php';

class ACloud_Sys_Open_Service_Init {
	
	function initKey($data) {
		$result = ACloud_Sys_Core_Http::sendPost ( $this->buildPostParams ( 'start.initkey', $data ) );
		if (! is_object ( $result ) || $result->code != ACLOUD_HTTP_OK || $result->info->verifycode !== $data ['verifycode'])
			return array (false, 'responce is error' );
		
		$keysService = ACloud_Sys_Core_Common::loadSystemClass ( 'keys', 'config.service' );
		list ( $key1, $key2, $key3 ) = array ($result->info->key1, $result->info->key2, $result->info->key3 );
		if (strlen ( $key1 ) != 128 || strlen ( $key2 ) != 128 || strlen ( $key3 ) != 128)
			return array (false, 'key is invalided' );
		
		if (! ($result = $keysService->updateKey123 ( 1, $key1, $key2, $key3 ))) {
			return array (false, 'update key fail' );
		}
		return array (true, 'init key success' );
	}
	
	function checkKey($data) {
		$result = ACloud_Sys_Core_Http::sendPost ( $this->buildPostParams ( 'start.checkkey', $data ) );
		if (! is_object ( $result ) || $result->code != ACLOUD_HTTP_OK || $result->info->verifycode !== $data ['verifycode'])
			return array (false, 'responce is error' );
		
		if (ACloud_Sys_Core_Verify::verifyWithAES ( $result->info->ciphertext, $result->info->plaintext ))
			return array (true, 'check key success' );
		return array (false, 'check key fail' );
	}
	
	function identifyKey($data) {
		$result = ACloud_Sys_Core_Http::sendPost ( $this->buildPostParams ( 'start.identifykey', $data ) );
		if (! is_object ( $result ) || $result->code != ACLOUD_HTTP_OK || $result->info->verifycode !== $data ['verifycode'])
			return array (false, 'responce is error' );
		
		$info = array ('sign' => $result->info->sign, 'verifycode' => $result->info->verifycode, 'rand' => $result->info->rand, 'identifytime' => $result->info->identifytime, 'footprint' => $result->info->footprint, 'step' => $result->info->step, 'verify' => $result->info->verify );
		$keysService = ACloud_Sys_Core_Common::loadSystemClass ( 'keys', 'config.service' );
		$key6 = $keysService->getKey6 ( 2 );
		
		if (! $key6 || strlen ( $key6 ) != 128)
			return array (false, 'key is invalided' );
		
		if (ACloud_Sys_Core_Verify::verifyWithOAuth ( $info, $key6 ))
			return array (true, 'identify key success' );
		return array (false, 'identify key fail' );
	}
	
	function buildPostParams($method, $data) {
		$params = array ();
		$params ['method'] = $method;
		$params ['charset'] = ACloud_Sys_Core_Common::getGlobal ( 'g_charset' );
		$params ['verify'] = $data ['verify'];
		$params ['sourcesign'] = $data ['sourcesign'];
		$params ['footprint'] = $data ['footprint'];
		$params ['identifier'] = $data ['identifier'];
		$params ['verifycode'] = $data ['verifycode'];
		$params ['securecode'] = $data ['securecode'];
		$params ['launchtime'] = $data ['launchtime'];
		$params ['step'] = $data ['step'];
		$params ['install'] = $data ['install'];
		$params ['ip'] = ACloud_Sys_Core_Common::getIp ();
		$params ['ua'] = $_SERVER ['HTTP_USER_AGENT'];
		$params ['posttime'] = time ();
		$params ['siteurl'] = ACloud_Sys_Core_Common::getGlobal ( 'g_siteurl' );
		$params ['sitename'] = ACloud_Sys_Core_Common::getGlobal ( 'g_sitename' );
		return $params;
	}

}










































