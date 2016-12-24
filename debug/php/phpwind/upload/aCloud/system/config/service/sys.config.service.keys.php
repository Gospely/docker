<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Sys_Config_Service_Keys {
	
	function updateKey6($id) {
		$id = intval ( $id );
		$keysDao = $this->getKeysDao ();
		$keysDao->update ( array ('key6' => ACloud_Sys_Core_Common::randCode ( 128 ) ), $id );
		return $this->getKey6 ( $id );
	}
	
	function getKey6($id) {
		$id = intval ( $id );
		$keysDao = $this->getKeysDao ();
		$key = $keysDao->get ( $id );
		return ($key && $key ['key6'] && strlen ( $key ['key6'] ) == 128) ? $key ['key6'] : '';
	}
	
	function getKey1($id) {
		$id = intval ( $id );
		$keysDao = $this->getKeysDao ();
		$key = $keysDao->get ( $id );
		return ($key && $key ['key1'] && strlen ( $key ['key1'] ) == 128) ? $key ['key1'] : '';
	}
	
	function updateKey123($id, $key1, $key2, $key3) {
		if (strlen ( $key1 ) != 128 || strlen ( $key2 ) != 128 || strlen ( $key3 ) != 128)
			return false;
		
		$keysDao = $this->getKeysDao ();
		return $keysDao->update ( array ('key1' => $key1, 'key2' => $key2, 'key3' => $key3, 'modified_time' => time () ), $id );
	}
	
	function updateKey456($id, $key4, $key5, $key6) {
		if (strlen ( $key4 ) != 128 || strlen ( $key5 ) != 128 || strlen ( $key6 ) != 128)
			return false;
		
		$keysDao = $this->getKeysDao ();
		return $keysDao->update ( array ('key4' => $key4, 'key5' => $key5, 'key6' => $key6, 'modified_time' => time () ), $id );
	}
	
	function getKey123($id) {
		$keysDao = $this->getKeysDao ();
		return $keysDao->get ( $id );
	}
	
	function getKeysDao() {
		return ACloud_Sys_Core_Common::loadSystemClass ( 'keys', 'config.dao' );
	}
}