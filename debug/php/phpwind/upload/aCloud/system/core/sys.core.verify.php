<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Sys_Core_Verify {
	
	function verifyWithAES($ciphertext, $plaintext) {
		if (! $ciphertext || ! $plaintext)
			return false;
		$keysService = ACloud_Sys_Core_Common::loadSystemClass ( 'keys', 'config.service' );
		$keys = $keysService->getKey123 ( 1 );
		if (! $keys || strlen ( $keys ['key1'] ) != 128 || strlen ( $keys ['key2'] ) != 128 || strlen ( $keys ['key3'] ) != 128) {
			return false;
		}
		require_once ACLOUD_PATH . '/system/core/sys.core.aes.php';
		$aesService = new ACloud_Sys_Core_Aes ();
		$key = $aesService->encrypt ( $keys ['key3'], $keys ['key2'], 256 );
		if (! $key) {
			return false;
		}
		if ($plaintext === ($ciphertext = $aesService->strcode ( $ciphertext, $key, 'DECODE' )) && strlen ( $plaintext ) == strlen ( $ciphertext )) {
			return true;
		}
		return false;
	}
	
	function verifyWithOAuth($data, $key) {
		if (! is_array ( $data ) || ! isset ( $data ['sign'] ) || ! $data ['sign'] || strlen ( $data ['sign'] ) != 32 || strlen ( $key ) != 128) {
			return false;
		}
		$source_sign = $data ['sign'];
		unset ( $data ['sign'] );
		require_once ACLOUD_PATH . '/system/core/sys.core.oauth.php';
		$verify_sign = ACloud_Sys_Core_OAuth::createHttpSign ( ACloud_Sys_Core_OAuth::createHttpQuery ( $data ) . $key );
		if ($verify_sign === $source_sign && strlen ( $verify_sign ) == strlen ( $source_sign )) {
			return true;
		}
		return false;
	}
	
	function createSignWithOAuth($data) {
		$keysService = ACloud_Sys_Core_Common::loadSystemClass ( 'keys', 'config.service' );
		$key1 = $keysService->getKey1 ( 1 );
		if (! $key1 || strlen ( $key1 ) != 128 || ! is_array ( $data ) || count ( $data ) < 4)
			return '';
		require_once ACLOUD_PATH . '/system/core/sys.core.oauth.php';
		return ACloud_Sys_Core_OAuth::createHttpSign ( ACloud_Sys_Core_OAuth::createHttpQuery ( $data ) . $key1 );
	}
}