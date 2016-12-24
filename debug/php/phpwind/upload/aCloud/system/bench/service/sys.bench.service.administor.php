<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Sys_Bench_Service_Administor {
	
	function isOpen() {
		$this->checkTables ();
		$service = ACloud_Sys_Core_Common::loadSystemClass ( 'extras', 'config.service' );
		if (! $service->getExtra ( 'ac_isopen' )) {
			return false;
		}
		
		ACloud_Sys_Core_Common::setGlobal ( 'g_siteurl', $service->getExtra ( 'ac_apply_siteurl' ) );
		ACloud_Sys_Core_Common::setGlobal ( 'g_charset', $service->getExtra ( 'ac_apply_charset' ) );
		
		$isAdvanced = $service->getExtra ( 'ac_install_advanced' );
		list ( $lastaccess ) = $this->getLastAccessInfo ();
		list ( $bool, $message ) = ($isAdvanced != 1 && (time () - $lastaccess >= 7200)) ? $this->checkLogin () : array (true, '' );
		if (! $bool) {
			return false;
		}
		$extrasService = ACloud_Sys_Core_Common::loadSystemClass ( 'extras', 'config.service' );
		$extrasService->setExtra ( 'ac_access_lasttime', time () );
		return true;
	}
	
	function getSiteInfo() {
		return array (ACloud_Sys_Core_Common::getGlobal ( 'g_sitename' ), ACloud_Sys_Core_Common::getGlobal ( 'g_siteurl', 'http://' . $_SERVER ['HTTP_HOST'] ), ACloud_Sys_Core_Common::getGlobal ( 'g_charset' ), ACLOUD_V );
	}
	
	function getEnvInfo() {
		$envService = ACloud_Sys_Core_Common::loadSystemClass ( 'env', 'open.service' );
		$data = array ();
		$data [] = array ('k' => 'parse_url函数', 't' => 1, 'v' => function_exists ( "parse_url" ) ? 1 : 0 );
		$data [] = array ('k' => 'fsockopen函数', 't' => 1, 'v' => function_exists ( "fsockopen" ) ? 1 : 0 );
		$data [] = array ('k' => 'curl_init函数', 't' => 1, 'v' => function_exists ( "curl_init" ) ? 1 : 0 );
		$data [] = array ('k' => 'DNS解析函数', 't' => 1, 'v' => function_exists ( "gethostbyname" ) ? 1 : 0 );
		$data [] = array ('k' => '云服务域名解析', 't' => 0, 'v' => function_exists ( 'gethostbyname' ) ? gethostbyname ( ACLOUD_HOST_API ) : '0.0.0.0' );
		$data [] = array ('k' => '云服务端口测试', 't' => 0, 'v' => $envService->getNetWorkSpeed () . 's' );
		$data [] = array ('k' => '云服务网络互通', 't' => 1, 'v' => ($envService->getNetWorkInterflow ()) ? 1 : 0 );
		$data [] = array ('k' => '云服务入口文件', 't' => 1, 'v' => ($envService->hasIndexFile ()) ? 1 : 0 );
		$data [] = array ('k' => '云服务数据表缺失', 't' => 0, 'v' => implode ( ",", $this->getTableInfo () ) );
		return $data;
	}
	
	function getLastApplyInfo() {
		$extrasService = ACloud_Sys_Core_Common::loadSystemClass ( 'extras', 'config.service' );
		$data = array ();
		$data ['siteurl'] = ($siteurl = $extrasService->getExtra ( 'ac_apply_siteurl' )) ? $siteurl : '暂无';
		$data ['lasttime'] = ($lasttime = $extrasService->getExtra ( 'ac_apply_lasttime' )) ? date ( "Y-m-d H:i:s", $lasttime ) : '暂无';
		return $data;
	}
	
	function getLastAccessInfo() {
		$extrasService = ACloud_Sys_Core_Common::loadSystemClass ( 'extras', 'config.service' );
		$lastaccess = $extrasService->getExtra ( 'ac_access_lasttime' );
		return array ($lastaccess, date ( "Y-m-d H:i:s", $lastaccess ) );
	}
	
	function checkTables() {
		$dao = ACloud_Sys_Core_Common::loadSystemClass ( 'createtable', 'config.dao' );
		$dao->initTables ();
	}
	
	function getTableInfo() {
		$dao = ACloud_Sys_Core_Common::loadSystemClass ( 'createtable', 'config.dao' );
		$tables = $dao->checkTables ();
		$tmp = array ();
		foreach ( $tables as $table => $v ) {
			if (! $v)
				$tmp [] = $table;
		}
		return ($tmp) ? $tmp : array ('无' );
	}
	
	function getApplyStep($operate) {
		$operates = array ('check' => 2, 'reset' => 5 );
		if (isset ( $operates [$operate] ))
			return $operates [$operate];
		$extrasService = ACloud_Sys_Core_Common::loadSystemClass ( 'extras', 'config.service' );
		list ( , $timeout ) = $this->getApplyTimeOut ();
		$step = intval ( $extrasService->getExtra ( 'ac_apply_step' ) );
		return ($step == 4 && $timeout < 0) ? 1 : $step;
	}
	
	function getApplyTimeOut() {
		$extrasService = ACloud_Sys_Core_Common::loadSystemClass ( 'extras', 'config.service' );
		$lasttime = $extrasService->getExtra ( 'ac_apply_lasttime' );
		return array (date ( "Y-m-d H:i:s", $lasttime ), (600 - (time () - $lasttime)) );
	}
	
	function apply($siteurl, $sitename = '', $bossname = '', $bossphone = '') {
		ACloud_Sys_Core_Common::setGlobal ( 'g_siteurl', $siteurl );
		$extrasService = ACloud_Sys_Core_Common::loadSystemClass ( 'extras', 'config.service' );
		$extrasService->setExtra ( 'ac_apply_siteurl', ACloud_Sys_Core_Common::parseDomainName ( $siteurl ) );
		$extrasService->setExtra ( 'ac_apply_charset', ACloud_Sys_Core_Common::getGlobal ( 'g_charset' ) );
		$extrasService->setExtra ( 'ac_apply_lasttime', time () );
		list ( $bool, $message ) = $this->checkApply ( $siteurl );
		if (! $bool)
			return array ($bool, $message );
		$service = ACloud_Sys_Core_Common::loadSystemClass ( 'apply', 'open.service' );
		list ( $bool, $message ) = $service->submit ( $siteurl, $sitename, $bossname, $bossphone );
		if (! $bool)
			return array ($bool, $message );
		list ( $bool, $message ) = $service->verify ( array ('siteurl' => $siteurl ) );
		if (! $bool)
			return array ($bool, $message );
		list ( $bool, $message ) = $service->verified ( array ('siteurl' => $siteurl ) );
		if (! $bool)
			return array ($bool, $message );
		return array (true, '' );
	}
	
	function checkApply($siteurl) {
		if (! $siteurl)
			return array (false, '抱歉，论坛网址不能为空' );
		if (! function_exists ( "fsockopen" ) && ! function_exists ( "curl_init" ))
			return array (false, '抱歉，fsockopen 与 curl_init 两个PHP函数都不可用,请联系网站技术人员或空间服务提供商' );
		$envService = ACloud_Sys_Core_Common::loadSystemClass ( 'env', 'open.service' );
		if (! $envService->hasIndexFile ())
			return array (false, '抱歉，ACloud安装包的代码不完整,请重新覆盖' );
		if (! $envService->getNetWorkInterflow ())
			return array (false, '抱歉，论坛网址无法与云服务实现网络互通，请检查是否为本地环境' );
		return array (true, '' );
	}
	
	function checkLogin() {
		$params = array ();
		$params ['method'] = "login.check";
		$params ['ip'] = ACloud_Sys_Core_Common::getIp ();
		$params ['ua'] = $_SERVER ['HTTP_USER_AGENT'];
		$params ['posttime'] = time ();
		require_once ACLOUD_PATH . '/system/core/sys.core.verify.php';
		$params = $this->buildPostParams ( $params );
		$params ['sign'] = ACloud_Sys_Core_Verify::createSignWithOAuth ( $params );
		require_once ACLOUD_PATH . '/system/core/sys.core.http.php';
		$result = ACloud_Sys_Core_Http::sendPost ( $params );
		if (! is_object ( $result ) || $result->code != 100)
			return array (false, $result->msg );
		return array (true, $result->msg );
	}
	
	function getLink() {
		$params = $this->buildPostParams ();
		require_once ACLOUD_PATH . '/system/core/sys.core.verify.php';
		$params ['accesssign'] = ACloud_Sys_Core_Verify::createSignWithOAuth ( $params );
		require_once ACLOUD_PATH . '/system/core/sys.core.http.php';
		return sprintf ( "http://%s/index.php?%s", ACLOUD_HOST_API, ACloud_Sys_Core_Http::httpBuildQuery ( $params ) );
	}
	
	function localInstall($siteurl, $charset, $keys) {
		if (! $siteurl || ! $charset || ! is_array ( $keys ))
			return false;
		$this->checkTables ();
		$result = $this->localSetKeys ( 1, $keys );
		if ($result) {
			$this->localSetExtras ( ACloud_Sys_Core_Common::parseDomainName ( $siteurl ), $charset );
		}
		return $result;
	}
	
	function localSetKeys($id, $keys) {
		$id = intval ( $id );
		if (! $keys || ! isset ( $keys [$id] ))
			return false;
		$key1 = isset ( $keys [$id] ['key1'] ) ? $keys [$id] ['key1'] : '';
		$key2 = isset ( $keys [$id] ['key2'] ) ? $keys [$id] ['key2'] : '';
		$key3 = isset ( $keys [$id] ['key3'] ) ? $keys [$id] ['key3'] : '';
		$keyService = ACloud_Sys_Core_Common::loadSystemClass ( 'keys', 'config.service' );
		if (strlen ( $key1 ) != 128 || strlen ( $key2 ) != 128 || strlen ( $key3 ) != 128)
			return false;
		$result = $keyService->updateKey123 ( $id, $key1, $key2, $key3 );
		return ($result) ? true : false;
	}
	
	function localSetExtras($siteurl, $charset) {
		$extrasService = ACloud_Sys_Core_Common::loadSystemClass ( 'extras', 'config.service' );
		$extrasService->setExtra ( 'ac_apply_siteurl', $siteurl );
		$extrasService->setExtra ( 'ac_apply_charset', $charset );
		$extrasService->setExtra ( 'ac_apply_lasttime', time () );
		$extrasService->setExtra ( 'ac_apply_step', 9 );
		$extrasService->setExtra ( 'ac_isopen', 1 );
		$extrasService->setExtra ( 'ac_install_advanced', 1 );
		$extrasService->setExtra ( 'ac_access_lasttime', time () );
		return true;
	}
	
	function resetServer() {
		$resetService = ACloud_Sys_Core_Common::loadSystemClass ( 'reset', 'config.service' );
		$resetService->resetConfig ();
		
		require_once sprintf ( ACLOUD_PATH . '/version/%s/core/ver.core.reset.php', ACLOUD_VERSION );
		$hookService = new ACloud_Ver_Core_Reset ();
		$hookService->execute ();
		
		return true;
	}
	
	function buildPostParams($data = array()) {
		$params = array ();
		$params ['siteurl'] = ACloud_Sys_Core_Common::getGlobal ( 'g_siteurl', 'http://' . $_SERVER ['HTTP_HOST'] );
		$params ['charset'] = ACloud_Sys_Core_Common::getGlobal ( 'g_charset' );
		$params ['footprint'] = ACloud_Sys_Core_Common::randCode ( 60 );
		$params ['version'] = ACLOUD_V;
		$params ['accesstime'] = time ();
		return array_merge ( $params, is_array ( $data ) ? $data : array () );
	}

}