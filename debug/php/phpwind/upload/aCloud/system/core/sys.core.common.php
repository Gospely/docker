<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require ACLOUD_PATH . '/system/core/sys.core.define.php';
require ACLOUD_PATH . '/system/core/sys.core.s.php';
class ACloud_Sys_Core_Common {
	
	function getGlobal($key, $default = NULL) {
		return (isset ( $GLOBALS [$key] )) ? $GLOBALS [$key] : $default;
	}
	
	function setGlobal($key, $value) {
		$GLOBALS [$key] = $value;
	}
	
	function getSiteSign() {
		$sign = ACloud_Sys_Core_Common::parseDomainName ( ACloud_Sys_Core_Common::getSiteUnique () );
		return substr ( md5 ( $sign ), 8, 8 );
	}
	
	function getSiteUnique() {
		return isset ( $_SERVER ['SERVER_NAME'] ) ? trim ( $_SERVER ['SERVER_NAME'] ) : trim ( ACLOUD_V );
	}
	
	function showError($message) {
		echo $message;
		exit ();
	}
	
	function randCode($length = 32) {
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
		$chars_length = (strlen ( $chars ) - 1);
		$string = $chars {rand ( 0, $chars_length )};
		for($i = 1; $i < $length; $i = strlen ( $string )) {
			$r = $chars {rand ( 0, $chars_length )};
			if ($r != $string {$i - 1})
				$string .= $r;
		}
		return $string;
	}
	
	function getIp() {
		static $ip = null;
		if (! $ip) {
			if (isset ( $_SERVER ['HTTP_X_FORWARDED_FOR'] ) && $_SERVER ['HTTP_X_FORWARDED_FOR'] && $_SERVER ['REMOTE_ADDR']) {
				if (strstr ( $_SERVER ['HTTP_X_FORWARDED_FOR'], ',' )) {
					$x = explode ( ',', $_SERVER ['HTTP_X_FORWARDED_FOR'] );
					$_SERVER ['HTTP_X_FORWARDED_FOR'] = trim ( end ( $x ) );
				}
				if (preg_match ( '/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER ['HTTP_X_FORWARDED_FOR'] )) {
					$ip = $_SERVER ['HTTP_X_FORWARDED_FOR'];
				}
			} elseif (isset ( $_SERVER ['HTTP_CLIENT_IP'] ) && $_SERVER ['HTTP_CLIENT_IP'] && preg_match ( '/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER ['HTTP_CLIENT_IP'] )) {
				$ip = $_SERVER ['HTTP_CLIENT_IP'];
			}
			if (! $ip && preg_match ( '/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER ['REMOTE_ADDR'] )) {
				$ip = $_SERVER ['REMOTE_ADDR'];
			}
			! $ip && $ip = 'Unknown';
		}
		return $ip;
	}
	
	function getDirName($path = null) {
		if (! empty ( $path )) {
			if (strpos ( $path, '\\' ) !== false) {
				return substr ( $path, 0, strrpos ( $path, '\\' ) ) . '/';
			} elseif (strpos ( $path, '/' ) !== false) {
				return substr ( $path, 0, strrpos ( $path, '/' ) ) . '/';
			}
		}
		return './';
	}
	
	public function parseDomainName($url) {
		return ($url) ? trim ( str_replace ( array ("http://", "https://" ), array ("" ), $url ), "/" ) : "";
	}
	
	function listDir($path) {
		static $result = array ();
		$path = rtrim ( $path, "/" ) . "/";
		$folder_handle = opendir ( $path );
		while ( false !== ($filename = readdir ( $folder_handle )) ) {
			if (strpos ( $filename, '.' ) !== 0) {
				if (is_dir ( $path . $filename . "/" )) {
					ACloud_Sys_Core_Common::listDir ( $path . $filename . "/" );
				} else {
					$result [] = $path . $filename;
				}
			}
		}
		closedir ( $folder_handle );
		return $result;
	}
	
	function getSiteUserAgent() {
		list ( $key, $ua ) = array ('_ac_app_ua', '' );
		if (! $_COOKIE || ! isset ( $_COOKIE [$key] ) || ! $_COOKIE [$key]) {
			$ua = substr ( md5 ( $_SERVER ['HTTP_USER_AGENT'] . '\t' . rand ( 1000, 9999 ) . '\t' . time () ), 8, 18 );
			setcookie ( $key, $ua, time () + 86400 * 365 * 5 );
		}
		$ua = $ua ? $ua : $_COOKIE [$key];
		return (strlen ( $ua ) == 18) ? ACloud_Sys_Core_S::escapeChar ( $ua ) : '';
	}
	
	function simpleResponse($code, $info = null) {
		$info = ACloud_Sys_Core_Common::convert ( $info, 'UTF-8', ACloud_Sys_Core_Common::getGlobal ( 'g_charset' ) );
		return ACloud_Sys_Core_Common::jsonEncode ( array ('code' => $code, 'info' => $info ) );
	}
	
	function jsonEncode($data) {
		if (function_exists ( "json_encode" ))
			return json_encode ( $data );
		require_once ACLOUD_PATH . '/system/core/sys.core.json.php';
		$jsonClass = new Services_JSON ();
		return $jsonClass->encode ( $data );
	}
	
	function jsonDecode($data) {
		if (function_exists ( "json_decode" ))
			return json_decode ( $data );
		require_once ACLOUD_PATH . '/system/core/sys.core.json.php';
		$jsonClass = new Services_JSON ();
		return $jsonClass->decode ( $data );
	}
	
	function loadSystemClass($className, $module = 'core') {
		static $classes = array ();
		$class = sprintf ( "ACloud_Sys_%s", str_replace ( ".", "_", $module . '.' . $className ) );
		if (isset ( $classes [$class] ))
			return $classes [$class];
		$classPath = sprintf ( ACLOUD_PATH . "/system/%s/sys.%s.%s.php", str_replace ( ".", "/", $module ), $module, $className );
		if (! is_file ( $classPath ))
			ACloud_Sys_Core_Common::showError ( 'cann`t find classpath' );
		require_once ACloud_Sys_Core_S::escapePath ( $classPath );
		if (! class_exists ( $class ))
			ACloud_Sys_Core_Common::showError ( 'cann`t find class' );
		$classes [$class] = new $class ();
		return $classes [$class];
	}
	
	function loadAppClass($appName) {
		static $classes = array ();
		$appName = strtolower ( $appName );
		$class = sprintf ( "ACloud_App_%s_Guiding", $appName );
		if (isset ( $classes [$class] ))
			return $classes [$class];
		$classPath = sprintf ( ACLOUD_PATH . "/app/%s/app.%s.guiding.php", $appName, $appName );
		if (! is_file ( $classPath ))
			ACloud_Sys_Core_Common::showError ( 'cann`t find classpath' );
		require_once ACloud_Sys_Core_S::escapePath ( $classPath );
		if (! class_exists ( $class ))
			ACloud_Sys_Core_Common::showError ( 'cann`t find class' );
		$classes [$class] = new $class ();
		return $classes [$class];
	}
	
	function loadApps($page) {
		require_once ACLOUD_PATH . '/app/app.router.php';
		$router = new ACloud_App_Router ();
		$apps = $router->getAppsByPage ( $page );
		foreach ( $apps as $appname ) {
			$appClass = ACloud_Sys_Core_Common::loadAppClass ( $appname );
			$appClass->execute ();
		}
		return $apps;
	}
	
	function arrayCombination($array, $ik, $vk) {
		if (! is_array ( $array ))
			return array ();
		$tmp = array ();
		foreach ( $array as $a ) {
			(isset ( $a [$ik] ) && isset ( $a [$vk] )) && $tmp [$a [$ik]] = $a [$vk];
		}
		return $tmp;
	}
	
	function arrayIntersectAssoc($array1, $array2) {
		if (! is_array ( $array1 ) || ! is_array ( $array2 ))
			return array ();
		$tmp = array ();
		if (! function_exists ( "array_intersect_assoc" )) {
			$tmp = array_intersect_assoc ( $array1, $array2 );
		} else {
			foreach ( $array1 as $k => $v ) {
				if (! isset ( $array2 [$k] ) || $array2 [$k] != $v) {
					continue;
				}
				$tmp [$k] = $v;
			}
		}
		return $tmp;
	}
	
	function refresh($url) {
		echo '<meta http-equiv="expires" content="0">';
		echo '<meta http-equiv="Pragma" content="no-cache">';
		echo '<meta http-equiv="Cache-Control" content="no-cache">';
		echo "<meta http-equiv='refresh' content='0;url=$url'>";
		exit ();
	}
	
	function getMicrotime() {
		$t_array = explode ( ' ', microtime () );
		return $t_array [0] + $t_array [1];
	}
	
	function convertToUTF8($str) {
		$charset = ACloud_Sys_Core_Common::getGlobal ( 'g_charset', 'gbk' );
		return ACloud_Sys_Core_Common::convert ( $str, 'utf-8', $charset );
	}
	
	function convertFromUTF8($str) {
		$charset = ACloud_Sys_Core_Common::getGlobal ( 'g_charset', 'gbk' );
		return ACloud_Sys_Core_Common::convert ( $str, $charset, 'utf-8' );
	}
	
	function convert($str, $toEncoding, $fromEncoding, $ifMb = true) {
		if (strtolower ( $toEncoding ) == strtolower ( $fromEncoding ))
			return $str;
		is_object ( $str ) && $str = get_object_vars ( $str );
		if (is_array ( $str )) {
			foreach ( $str as $key => $value ) {
				is_object ( $value ) && $value = get_object_vars ( $value );
				$str [$key] = ACloud_Sys_Core_Common::convert ( $value, $toEncoding, $fromEncoding, $ifMb );
			}
			return $str;
		} else {
			if (function_exists ( 'mb_convert_encoding' ) && $ifMb) {
				return mb_convert_encoding ( $str, $toEncoding, $fromEncoding );
			} else {
				static $sConvertor = null;
				! $toEncoding && $toEncoding = 'GBK';
				! $fromEncoding && $fromEncoding = 'GBK';
				if (! isset ( $sConvertor ) && ! is_object ( $sConvertor )) {
					require_once ACLOUD_PATH . '/system/core/sys.core.charset.php';
					$sConvertor = new ACloud_Sys_Core_Charset ();
				}
				return $sConvertor->Convert ( $str, $fromEncoding, $toEncoding, ! $ifMb );
			}
		}
	}
}