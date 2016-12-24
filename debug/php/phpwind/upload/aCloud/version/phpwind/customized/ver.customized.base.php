<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.functions.php';
class ACloud_Ver_Customized_Base {
	
	function ACloud_Ver_Customized_Base() {
		$daoObject = ACloud_Sys_Core_Common::getGlobal ( ACLOUD_OBJECT_DAO );
		$daoObject->getDB ();
		list ( $currentUid ) = ACloud_Sys_Core_S::gp ( 'current_uid' );
		ACloud_Sys_Core_Common::setGlobal ( 'customized_current_uid', $currentUid );
	}
	
	function buildResponse($errorCode, $responseData = array()) {
		return array ($errorCode, $responseData );
	}
	
	function getCurrentUser($rights = array()) {
		static $user = null;
		if (! is_null ( $user ))
			return $user;
		require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.user.module.php';
		$user = new ACloud_Ver_Customized_User_Module ( $rights );
		return $user;
	}
	
	function getCustomizedCommonService() {
		$customizedFactory = ACloud_Ver_Customized_Factory::getInstance ();
		return $customizedFactory->getVersionCustomizedCommon ();
	}
}