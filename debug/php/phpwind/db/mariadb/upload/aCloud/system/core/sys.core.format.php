<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Sys_Core_Format {
	
	var $formats = array ('json', 'serialize' );
	
	function format($data, $format, $charset) {
		$format = ACloud_Sys_Core_S::inArray ( strtolower ( $format ), $this->formats ) ? strtolower ( $format ) : 'json';
		$action = $format . 'Format';
		return $this->$action ( $data, $charset );
	}
	
	function jsonFormat($data, $charset) {
		$data = ACloud_Sys_Core_Common::convert ( $data, 'UTF-8', $charset );
		return ACloud_Sys_Core_Common::jsonEncode ( $data );
	}
	
	function serializeFormat($data, $charset = '') {
		return serialize ( $data );
	}
	
	function dataFlowXmlFormat($docs, $charset, $totalPages = null) {
		$docs = ACloud_Sys_Core_Common::convert ( $docs, 'UTF-8', $charset );
		require_once ACLOUD_PATH . '/system/core/sys.core.xml.php';
		return ACloud_Sys_Core_XML::createXML ( $docs, 'UTF-8', $totalPages );
	}
	
	function dataFlowXmlFormatWithDOMDocument($docs, $charset, $totalPages = null) {
		$xml = new DOMDocument ( "1.0", 'utf-8' );
		$xmlRoot = $xml->createElement ( "root" );
		$xmlDocInfo = $xml->createElement ( "docinfo" );
		$xmlDocInfo->setAttribute ( "charset", 'utf-8' );
		
		! is_null ( $totalPages ) && $xmlDocInfo->setAttribute ( "totalpages", $totalPages );
		
		$xmlDocs = $xml->createElement ( "docs" );
		foreach ( $docs as $doc ) {
			$xmlDoc = $xml->createElement ( "doc" );
			foreach ( $doc as $k => $v ) {
				$element = $xml->createElement ( $k );
				$element->appendChild ( $xml->createCDATASection ( $v ) );
				$xmlDoc->appendChild ( $element );
			}
			$xmlDocs->appendChild ( $xmlDoc );
		}
		
		$xmlRoot->appendChild ( $xmlDocInfo );
		$xmlRoot->appendChild ( $xmlDocs );
		$xml->appendChild ( $xmlRoot );
		$output = $xml->saveXML ();
		return $output;
	}
}