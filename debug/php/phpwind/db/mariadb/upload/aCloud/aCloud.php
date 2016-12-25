<?php
! defined ( "ACLOUD_PATH" ) && define ( "ACLOUD_PATH", dirname ( __FILE__ ) );
require_once ACLOUD_PATH . '/system/core/sys.core.common.php';
define ( 'ACLOUD_VERSION_PATH', ACLOUD_PATH . '/version/' . ACLOUD_VERSION );
require_once sprintf ( ACLOUD_PATH . '/version/%s/%s.bootstrap.php', ACLOUD_VERSION, ACLOUD_VERSION );

class ACloud_Router {
	function run() {
		list ( $a ) = ACloud_Sys_Core_S::gp ( array ('a' ) );
		$action = ($a) ? $a . "Action" : "";
		if ($action && method_exists ( $this, $action )) {
			ACloud_Init::execute ();
			$this->$action ();
		}
	}
	function sysAction() {
		require_once ACLOUD_PATH . '/system/sys.router.php';
		$sysRouter = new ACloud_Sys_Router ();
		return $sysRouter->run ();
	}
	function apiAction() {
		require_once ACLOUD_PATH . '/api/api.router.php';
		$apiRouter = new ACloud_Api_Router ();
		return $apiRouter->run ();
	}
}

class ACloud_Init {
	function execute() {
		$_extrasService = ACloud_Sys_Core_Common::loadSystemClass ( 'extras', 'config.service' );
		ACloud_Sys_Core_Common::setGlobal ( 'g_ips', explode ( "|", ACLOUD_APPLY_IPS ) );
		ACloud_Sys_Core_Common::setGlobal ( 'g_siteurl', ACLOUD_APPLY_SITEURL ? ACLOUD_APPLY_SITEURL : $_extrasService->getExtra ( 'ac_apply_siteurl' ) );
		ACloud_Sys_Core_Common::setGlobal ( 'g_charset', ACLOUD_APPLY_CHARSET ? ACLOUD_APPLY_CHARSET : $_extrasService->getExtra ( 'ac_apply_charset' ) );
	}
}

class ACloud_App_Guiding {
	function getApp() {
		ACloud_Init::execute ();
		require sprintf ( ACLOUD_PATH . '/version/%s/core/ver.core.app.php', ACLOUD_VERSION );
		return ACloud_Ver_Core_App::getAppOutPut ();
	}
	function runApps($page) {
		ACloud_Init::execute ();
		return ACloud_Sys_Core_Common::loadApps ( $page );
	}
	
	function collectSql($sql) {
		$aggregateService = ACloud_Sys_Core_Common::loadSystemClass ( 'aggregate', 'dataflow.service' );
		return $aggregateService->collectSQL ( $sql );
	}
}