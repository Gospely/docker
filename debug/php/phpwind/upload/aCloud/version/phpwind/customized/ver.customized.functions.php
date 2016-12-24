<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

function includefile($file) {
	list ( $windVersion ) = explode ( ',', WIND_VERSION );
	if ($windVersion && $windVersion < '8.5') {
		include_once ($file);
	} else {
		pwCache::getData ( $file );
	}
}

function getBbsUrl() {
	global $pwServer, $db_dir;
	$dirstrpos = strpos ( $pwServer ['PHP_SELF'], $db_dir );
	if ($dirstrpos !== false) {
		$tmp = substr ( $pwServer ['PHP_SELF'], 0, $dirstrpos );
		$pwServer ['PHP_SELF'] = "$tmp.php";
	} else {
		$tmp = $pwServer ['PHP_SELF'];
	}
	return S::escapeChar ( "http://" . $pwServer ['HTTP_HOST'] . substr ( $tmp, 0, strrpos ( $tmp, '/' ) ) );
}

function getTimestamp() {
	global $db_cvtime;
	return ($db_cvtime != 0) ? (time () + $db_cvtime * 60) : time ();
}

function encryptString($string, $encode = true) {
	global $db_siteownerid;
	! $encode && $string = base64_decode ( $string );
	$code = '';
	$key = substr ( md5 ( md5 ( $db_siteownerid ) ), 8, 18 );
	$keylen = strlen ( $key );
	$strlen = strlen ( $string );
	for($i = 0; $i < $strlen; $i ++) {
		$k = $i % $keylen;
		$code .= $string [$i] ^ $key [$k];
	}
	return ($encode ? base64_encode ( $code ) : $code);
}

function isAllowMethod($httpMethod = 'GET') {
	global $pwServer;
	if (($pwServer ['REQUEST_METHOD'] == 'POST' && strtolower ( $httpMethod ) == 'post') || ($pwServer ['REQUEST_METHOD'] != 'POST' && strtolower ( $httpMethod ) == 'get'))
		return true;
	return false;
}

function Showmsg($msg_info) {
	$msg_info = getLangInfo ( 'msg', $msg_info );
	$response = ACloud_Sys_Core_Common::loadSystemClass ( 'response' );
	$response->setErrorCode ( 99999 );
	$response->setResponseData ( $msg_info );
	echo $response->getOutputData ();
	exit ();
}

function mShowface($message) {
	global $face, $db_cvtimes;
	include_once (D_P . 'data/bbscache/postcache.php');
	$message = preg_replace ( "/\[s:(.+?)\]/eis", "mPostcache('\\1')", $message );
	return $message;
}

function mPostcache($key) {
	global $face, $imgpath, $tpc_author;
	is_array ( $face ) && ! $face [$key] && $face [$key] = current ( $face );
	return "<img src=\"$imgpath/post/smile/{$face[$key][0]}\" smile=\"1\" />";
}

function UnMShowFace($facename) {
	global $face;
	foreach ( $face as $key => $value ) {
		if ($value [0] == $facename) {
			$faceId = $key;
			break;
		}
	}
	return '[s:' . $faceId . ']';
}

function setstatus(&$status, $b, $setv = '1') {
	-- $b;
	for($i = strlen ( $setv ) - 1; $i >= 0; $i --) {
		if ($setv [$i]) {
			$status |= 1 << $b;
		} else {
			$status &= ~ (1 << $b);
		}
		++ $b;
	}
}

function GetLang($lang, $EXT = 'php') {
	$tplpath = L::style ( 'tplpath' );
	$tplpath || $tplpath = 'wind';
	if (file_exists ( R_P . "template/$tplpath/lang_$lang.$EXT" )) {
		return R_P . "template/$tplpath/lang_$lang.$EXT";
	} elseif (file_exists ( R_P . "template/wind/lang_$lang.$EXT" )) {
		return R_P . "template/wind/lang_$lang.$EXT";
	} else {
		exit ( "Can not find lang_$lang.$EXT file" );
	}
}