<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
define ( 'ACLOUD_API_ILLEGAL_CALL', 10000 );

class ACloud_Api_Router {
	
	function run() {
		list ( $method ) = ACloud_Sys_Core_S::gp ( array ('method' ) );
		$this->registerSystemParams ();
		$request = $_GET + $_POST;
		$controlService = ACloud_Sys_Core_Common::loadSystemClass ( 'control', 'verify.service' );
		if (! $controlService->apiControl ( $request ))
			$this->outputControlError ();
		$apiProxy = ACloud_Sys_Core_Common::loadSystemClass ( 'api', 'core.proxy' );
		$result = $apiProxy->call ( $method, $request );
		$this->output ( $result );
	}
	
	function outputControlError() {
		$response = ACloud_Sys_Core_Common::loadSystemClass ( 'response' );
		$response->setErrorCode ( ACLOUD_API_ILLEGAL_CALL );
		$response->setResponseData ( 'Illegal Call' );
		return $this->output ( $response->getOutputData () );
	}
	
	function registerSystemParams() {
		list ( $format ) = ACloud_Sys_Core_S::gp ( array ('format' ) );
		ACloud_Sys_Core_Common::setGlobal ( 'acloud_api_output_format', $format );
		return true;
	}
	
	function output($data) {
		print_r ( $data );
		exit ();
	}
}