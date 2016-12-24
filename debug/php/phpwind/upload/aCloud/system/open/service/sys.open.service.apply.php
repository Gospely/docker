<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Sys_Open_Service_Apply {
	
	function submit($siteurl, $sitename, $bossname, $bossphone) {
		if (! $siteurl) {
			return false;
		}
		$keysService = $this->getKeysService ();
		$key6 = $keysService->updateKey6 ( 2 );
		
		$data = array ('bossname' => $bossname, 'bossphone' => $bossphone );
		require_once ACLOUD_PATH . '/system/core/sys.core.http.php';
		$result = ACloud_Sys_Core_Http::sendPost ( $this->buildPostParams ( 'apply.submit', $key6, $data ) );
		if (! is_object ( $result ) || $result->code != 100)
			return array (false, $result->msg );
		return array (true, $result->msg );
	}
	
	function verify($data) {
		$keysService = $this->getKeysService ();
		$key6 = $keysService->getKey6 ( 2 );
		require_once ACLOUD_PATH . '/system/core/sys.core.http.php';
		$result = ACloud_Sys_Core_Http::sendPost ( $this->buildPostParams ( 'apply.verify', $key6, $data ) );
		if (! is_object ( $result ) || $result->code != 100)
			return array (false, $result->msg );
		
		$extrasService = ACloud_Sys_Core_Common::loadSystemClass ( 'extras', 'config.service' );
		$extrasService->setExtra ( 'ac_apply_step', 4 );
		return array (true, $result->msg );
	}
	
	function verified($data) {
		$keysService = $this->getKeysService ();
		$key6 = $keysService->getKey6 ( 2 );
		require_once ACLOUD_PATH . '/system/core/sys.core.http.php';
		$result = ACloud_Sys_Core_Http::sendPost ( $this->buildPostParams ( 'apply.verified', $key6, $data ) );
		if (! is_object ( $result ) || $result->code != 100)
			return array (false, $result->msg );
		
		$extrasService = ACloud_Sys_Core_Common::loadSystemClass ( 'extras', 'config.service' );
		$extrasService->setExtra ( 'ac_isopen', 1 );
		$extrasService->setExtra ( 'ac_apply_step', 9 );
		return array (true, $result->msg );
	}
	
	function verifying($data) {
		if (! is_array ( $data ) || count ( $data ) < 7)
			return false;
		$keysService = $this->getKeysService ();
		$key6 = $keysService->getKey6 ( 2 );
		require_once ACLOUD_PATH . '/system/core/sys.core.verify.php';
		if ($key6 && strlen ( $key6 ) == 128 && ACloud_Sys_Core_Verify::verifyWithOAuth ( $data, $key6 ))
			return true;
		return false;
	}
	
	function buildPostParams($method, $key, $data) {
		$params = array ();
		$params ['method'] = $method;
		$params ['version'] = ACLOUD_V;
		$params ['siteurl'] = ACloud_Sys_Core_Common::getGlobal ( 'g_siteurl' );
		$params ['sitename'] = ACloud_Sys_Core_Common::getGlobal ( 'g_sitename' );
		$params ['bossname'] = isset ( $data ['bossname'] ) ? $data ['bossname'] : 'phpwind';
		$params ['bossphone'] = isset ( $data ['bossphone'] ) ? $data ['bossphone'] : '';
		$params ['charset'] = ACloud_Sys_Core_Common::getGlobal ( 'g_charset' );
		$params ['key6'] = $key;
		$params ['ip'] = ACloud_Sys_Core_Common::getIp ();
		$params ['ua'] = $_SERVER ['HTTP_USER_AGENT'];
		$params ['posttime'] = time ();
		return $params;
	}
	
	function getKeysService() {
		return ACloud_Sys_Core_Common::loadSystemClass ( 'keys', 'config.service' );
	}
}