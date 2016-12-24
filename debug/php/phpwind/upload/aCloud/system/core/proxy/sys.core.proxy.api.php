<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Sys_Core_Proxy_Api {
	
	function call($api, $request) {
		$api = trim ( $api );
		if (! $api)
			return $this->buildResponse ( 10000 );
		$apiConfigService = ACloud_Sys_Core_Common::loadSystemClass ( 'apis', 'config.service' );
		$apiConfig = $apiConfigService->getApiConfigByApiName ( $api );
		if (! ACloud_Sys_Core_S::isArray ( $apiConfig ))
			return $this->buildResponse ( 10001 );
		if (! $apiConfig ['status'])
			return $this->buildResponse ( 10002 );
		list ( $apiClass, $method, $arguments ) = $this->getClassAndMethodAndArguments ( $apiConfig, $request );
		if (! $apiClass)
			return $this->buildResponse ( 10003 );
		list ( $errorCode, $data ) = call_user_func_array ( array ($apiClass, $method ), $arguments );
		return $this->buildResponse ( $errorCode, $data );
	}
	
	function getClassAndMethodAndArguments($apiConfig, $request) {
		return ! $apiConfig ['category'] ? $this->getCommonClassAndMethodAndArguments ( $apiConfig, $request ) : ($apiConfig ['category'] == 1 ? $this->getCustomizedClassAndMethodAndArguments ( $apiConfig, $request ) : $this->getGeneralClassAndMethodAndArguments ( $apiConfig, $request ));
	}
	
	function getCommonClassAndMethodAndArguments($apiConfig, $request) {
		list ( , $module ) = explode ( '.', $apiConfig ['name'] );
		$classPath = sprintf ( ACLOUD_PATH . '/api/common/' . ACLOUD_API_VERSION . '/api.common.%s.php', strtolower ( $module ) );
		$className = sprintf ( 'ACloud_Api_Common_%s', ucfirst ( $module ) );
		return $this->getRealClassAndMethodAndArguments ( $classPath, $className, $apiConfig ['template'], $apiConfig ['argument'], $request );
	}
	
	function getCustomizedClassAndMethodAndArguments($apiConfig, $request) {
		list ( , $module ) = explode ( '.', $apiConfig ['name'] );
		$classPath = sprintf ( ACLOUD_PATH . '/api/customized/' . ACLOUD_API_VERSION . '/api.customized.%s.php', strtolower ( $module ) );
		$className = sprintf ( 'ACloud_Api_Customized_%s', ucfirst ( $module ) );
		return $this->getRealClassAndMethodAndArguments ( $classPath, $className, $apiConfig ['template'], $apiConfig ['argument'], $request );
	}
	
	function getGeneralClassAndMethodAndArguments($apiConfig, $request) {
		$classPath = ACLOUD_PATH . '/api/common/' . ACLOUD_API_VERSION . '/api.common.generalapi.php';
		list ( $className, $method ) = array ('ACloud_Api_Common_GeneralApi', 'get' );
		list ( $apiClass, $method ) = $this->getRealClassAndMethodAndArguments ( $classPath, $className, $method );
		if (! $apiClass)
			return array ('', '', array () );
		return array ($apiClass, $method, array ($apiConfig, $request ) );
	}
	
	function getRealClassAndMethodAndArguments($classPath, $className, $method, $arguments, $request) {
		if (! is_file ( $classPath ))
			return array ('', '', array () );
		require_once ACloud_Sys_Core_S::escapePath ( $classPath );
		if (! class_exists ( $className ))
			return array ('', '', array () );
		$apiClass = new $className ();
		if (! method_exists ( $apiClass, $method ))
			return array ('', '', array () );
		$arguments = $arguments ? explode ( ',', $arguments ) : array ();
		$arguments = $this->buildRequestArguments ( $arguments, $request );
		return array ($apiClass, $method, $arguments );
	}
	
	function buildRequestArguments($arguments, $request) {
		$result = array ();
		$charset = ACloud_Sys_Core_Common::getGlobal ( 'g_charset' );
		foreach ( $arguments as $arg ) {
			$result [] = isset ( $request [$arg] ) ? ACloud_Sys_Core_Common::convert ( $request [$arg], $charset, 'UTF-8' ) : null;
		}
		return $result;
	}
	
	function buildResponse($errorCode, $responseData = array()) {
		$response = ACloud_Sys_Core_Common::loadSystemClass ( 'response' );
		$response->setErrorCode ( $errorCode );
		$response->setResponseData ( $responseData );
		return $response->getOutputData ();
	}
}