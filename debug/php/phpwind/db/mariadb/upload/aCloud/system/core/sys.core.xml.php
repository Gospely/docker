<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Sys_Core_XML {
	
	function createXML($docs, $charset, $totalPages = null) {
		$charset = strtolower ( $charset );
		$xml = '';
		$xml .= '<?xml version="1.0" encoding="' . $charset . '"?>';
		$xml .= '<root>';
		$xml .= '<docinfo ' . ACloud_Sys_Core_XML::createAttribute ( 'version', ACLOUD_XML_VERSION ) . ' ' . ACloud_Sys_Core_XML::createAttribute ( 'charset', $charset ) . ' ' . ACloud_Sys_Core_XML::createAttribute ( 'totalpages', $totalPages ) . ' />';
		$xml .= '<docs>';
		$xml .= ACloud_Sys_Core_XML::buildXML ( $docs );
		$xml .= '</docs>';
		$xml .= "</root>";
		return $xml;
	}
	
	function createAttribute($key, $value) {
		return ($value !== null) ? sprintf ( '%s="%s"', $key, $value ) : '';
	}
	
	function buildXML($docs) {
		if (! $docs || ! is_array ( $docs ))
			return '';
		$docStr = '';
		foreach ( $docs as $doc ) {
			$docStr .= "<doc>";
			foreach ( $doc as $k => $v ) {
				$docStr .= sprintf ( '<%s><![CDATA[%s]]></%s>', $k, $v, $k );
			}
			$docStr .= "</doc>";
		}
		return $docStr;
	}

}