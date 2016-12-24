<?php
set_time_limit ( 0 );
define ( "ACLOUD_ADMINISTOR", true );
require_once R_P . 'aCloud/aCloud.php';
ACloud_Sys_Core_Common::setGlobal ( 'g_siteurl', $GLOBALS ['db_bbsurl'] );
ACloud_Sys_Core_Common::setGlobal ( 'g_sitename', $GLOBALS ['db_bbsname'] );
ACloud_Sys_Core_Common::setGlobal ( 'g_charset', $GLOBALS ['db_charset'] );
$service = ACloud_Sys_Core_Common::loadSystemClass ( 'administor', 'bench.service' );
list ( $operate ) = ACloud_Sys_Core_S::gp ( array ('operate' ) );
if (! $operate && $service->isOpen ()) {
	$ac_url = $service->getLink ();
} else {
	list ( $ac_step ) = ACloud_Sys_Core_S::gp ( array ('step' ) );
	$ac_step = ($_ac_step = $service->getApplyStep ( $operate )) ? $_ac_step : ($ac_step ? $ac_step : 1);
	if ($ac_step == 7) {
		list ( $ac_siteurl ) = ACloud_Sys_Core_S::gp ( array ('siteurl' ) );
		list ( $ac_bool, $ac_message ) = $service->apply ( $ac_siteurl );
		$ac_step = ($ac_bool) ? 4 : 3;
		if ($ac_bool)
			ACloud_Sys_Core_Common::refresh ( "$basename&step=1" );
	}
	if ($ac_step == 2) {
		list ( $ac_sitename, $ac_siteurl, $ac_charset, $ac_version ) = $service->getSiteInfo ();
		$ac_evninfo = $service->getEnvInfo ();
		$ac_applyinfo = $service->getLastApplyInfo ();
	}
	if ($ac_step == 4) {
		list ( $ac_lasttime, $ac_timeout ) = $service->getApplyTimeOut ();
	}
	if ($ac_step == 5) {
		$service->resetServer ();
	}
	if ($ac_step == 9) {
		list ( , $ac_lastaccesstime ) = $service->getLastAccessInfo ();
	}
}
include PrintEot ( 'acloud' );
