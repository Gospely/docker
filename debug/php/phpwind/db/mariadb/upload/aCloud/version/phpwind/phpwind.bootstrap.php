<?php
if (defined ( 'ACLOUD_INDEX' )) {
	error_reporting ( E_ERROR | E_PARSE );
	! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
	! defined ( 'R_P' ) && define ( 'R_P', ACloud_Sys_Core_Common::getDirName ( ACLOUD_PATH ) );
	! defined ( 'D_P' ) && define ( 'D_P', R_P );
	! defined ( 'P_W' ) && define ( 'P_W', 'ACLOUD' );
	define ( "COL", 1 );
	require_once R_P . 'require/common.php';
	if (class_exists ( "pwCache" ) && method_exists ( "pwCache", "getData" )) {
		include (D_P . 'data/bbscache/baseconfig.php');
		pwCache::getData ( D_P . 'data/bbscache/config.php' );
	} else {
		include D_P . 'data/bbscache/config.php';
	}
	ACloud_Sys_Core_Common::setGlobal ( 'attachpath', ($db_attachurl != 'N') ? $db_attachurl : ($db_bbsurl . '/' . $db_attachname) );
	ACloud_Sys_Core_Common::setGlobal ( 'imgpath', ($db_http != 'N') ? $db_http : ($db_bbsurl . '/' . $db_picpath) );
	ACloud_Sys_Core_Common::setGlobal ( 'imgdir', R_P . $db_picpath );
	ACloud_Sys_Core_Common::setGlobal ( 'attachdir', R_P . $db_attachname );
	ACloud_Sys_Core_Common::setGlobal ( 'tablepre', 'pw_' );
}
if (! ACloud_Sys_Core_Common::getGlobal ( ACLOUD_OBJECT_DAO )) {
	require sprintf ( ACLOUD_PATH . '/version/%s/core/ver.core.dao.php', ACLOUD_VERSION );
	ACloud_Sys_Core_Common::setGlobal ( ACLOUD_OBJECT_DAO, new ACloud_Ver_Core_Dao () );
}