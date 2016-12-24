<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Sys_Core_SqlBuilder {
	
	function buildSelectSql($config, $request) {
		if (! ACloud_Sys_Core_S::isArray ( $config ) || ! ACloud_Sys_Core_S::isArray ( $request ))
			return '';
		$arguments = $this->buildArguments ( $request, $config ['argument'], $config ['argument_type'] );
		$fields = $this->buildFields ( $request, $config ['fields'] );
		return $this->replaceSql ( $config ['template'], $arguments, $fields );
	}
	
	function buildArguments($request, $arguments, $argumentTypes) {
		if (! $arguments)
			return array ();
		list ( $arguments, $argumentTypes, $result ) = array (explode ( ',', $arguments ), $this->buildArgumentTypes ( $argumentTypes ), array () );
		foreach ( $arguments as $argument ) {
			$argument = strtolower ( $argument );
			$argumentType = isset ( $argumentTypes [$argument] ) ? $argumentTypes [$argument] : 'int';
			$argumentValue = $this->getRequestArgumentValue ( $argument, $request );
			$result [$argument] = is_null ( $argumentValue ) ? null : $this->filterArgument ( $argument, $argumentType, $argumentValue );
		}
		return $result;
	}
	
	function buildArgumentTypes($argumentTypes) {
		list ( $result, $argumentTypes, $allowedTypes ) = array (array (), trim ( $argumentTypes ), $this->getAllowedTypes () );
		if (! $argumentTypes)
			return $result;
		$argumentTypes = explode ( ',', $argumentTypes );
		foreach ( $argumentTypes as $argumentType ) {
			list ( $argument, $type ) = explode ( '|', $argumentType );
			$type = strtolower ( $type );
			! in_array ( $type, $allowedTypes ) && $type = 'int';
			$result [$argument] = $type;
		}
		return $result;
	}
	
	function getAllowedTypes() {
		return array ('int', 'array', 'string' );
	}
	
	function getRequestArgumentValue($param, $request) {
		$charset = ACloud_Sys_Core_Common::getGlobal ( 'g_charset' );
		return isset ( $request [$param] ) ? ACloud_Sys_Core_Common::convert ( $request [$param], $charset, 'UTF-8' ) : null;
	}
	
	function filterArgument($argument, $argumentType, $value) {
		if ($argumentType == 'array')
			return explode ( ',', $value );
		return $argumentType == 'int' ? intval ( $value ) : ACloud_Sys_Core_S::sqlEscape ( $value );
	}
	
	function buildFields($request, $fields) {
		$requestFields = $this->getRequestArgumentValue ( 'fields', $request );
		$fields = $fields ? explode ( ',', $fields ) : array ();
		if (! ACloud_Sys_Core_S::isArray ( $fields ))
			return '*';
		if (is_null ( $requestFields ))
			return $this->formatFields ( $fields );
		$requestFields = explode ( ',', $requestFields );
		$intersect = (! ACloud_Sys_Core_S::isArray ( $requestFields )) ? $fields : array_intersect ( $fields, $requestFields );
		! ACloud_Sys_Core_S::isArray ( $intersect ) && $intersect = $fields;
		return $this->formatFields ( $intersect );
	}
	
	function formatFields($fields) {
		$result = array ();
		if (! ACloud_Sys_Core_S::isArray ( $fields ))
			return '*';
		foreach ( $fields as $field ) {
			list ( $tableAlias, $fieldName ) = strpos ( $field, '.' ) === false ? array ('', $field ) : explode ( '.', $field );
			$result [] = ($tableAlias ? $tableAlias . '.' : '') . ACloud_Sys_Core_S::sqlMetadata ( $fieldName );
		}
		return implode ( ',', $result );
	}
	
	function replaceSql($sqlTemplate, $argumentValues, $fields) {
		preg_match_all ( '/\{(\w+)\}/', $sqlTemplate, $matches );
		if (! ACloud_Sys_Core_S::isArray ( $matches ))
			return '';
		$seg = $this->getRandString ( 4 );
		$sql = preg_replace ( '/\{(\w+)\}/', $seg . '{${1}}' . $seg, $sqlTemplate );
		foreach ( $matches [0] as $k => $v ) {
			$value = ($v != '{fields}') ? (is_array ( $argumentValues [$matches [1] [$k]] ) ? ACloud_Sys_Core_S::sqlImplode ( $argumentValues [$matches [1] [$k]] ) : $argumentValues [$matches [1] [$k]]) : $fields;
			$sql = str_replace ( $seg . $v . $seg, $value, $sql );
		}
		return $sql;
	}
	
	function getRandString($length) {
		return substr ( md5 ( $this->getRandNum ( $length ) ), mt_rand ( 0, 32 - $length ), $length );
	}
	
	function getRandNum($length) {
		mt_srand ( ( double ) microtime () * 1000000 );
		$randVal = mt_rand ( 1, 9 );
		for($i = 1; $i < $length; $i ++) {
			$randVal .= mt_rand ( 0, 9 );
		}
		return $randVal;
	}
}