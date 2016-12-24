<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class Sys_Core_GDO {
	
	var $gdo = array ();
	
	function get($key, $default = NULL) {
		return isset ( $this->gdo [$key] ) ? $this->gdo [$key] : $default;
	}
	
	function set($key, $value) {
		$this->gdo [$this->getKeyName ( $key )] = $value;
	}
	
	function gets(array $keys) {
		$tmp = array ();
		foreach ( $keys as $key ) {
			$tmp [] = $this->get ( $key );
		}
		return $tmp;
	}
	
	function getAll() {
		return $this->gdo;
	}

}