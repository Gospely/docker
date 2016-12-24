<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Sys_Core_Response {
	
	var $errorCode = '';
	var $responseData = '';
	
	function setErrorCode($errorCode) {
		$this->errorCode = $errorCode;
		return true;
	}
	
	function getErrorCode() {
		return $this->errorCode;
	}
	
	function setResponseData($responseData) {
		$this->responseData = $responseData;
		return true;
	}
	
	function getResponseData() {
		return $this->responseData;
	}
	
	function getOutputData() {
		$format = ACloud_Sys_Core_Common::getGlobal ( 'acloud_api_output_format' );
		list ( $data, $charset ) = array (array ('code' => $this->getErrorCode (), 'info' => $this->getResponseData () ), ACloud_Sys_Core_Common::getGlobal ( 'g_charset' ) );
		$formatService = ACloud_Sys_Core_Common::loadSystemClass ( 'format' );
		return $formatService->format ( $data, $format, $charset );
	}
}