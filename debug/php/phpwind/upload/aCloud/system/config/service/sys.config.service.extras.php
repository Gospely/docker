<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Sys_Config_Service_Extras {
	
	function setExtra($key, $value, $etype = 1) {
		$etype = ($etype) ? $etype : (is_array ( $value ) ? 2 : 1);
		$evalue = is_array ( $value ) ? serialize ( $value ) : $value;
		$fields = array ('ekey' => $key, 'evalue' => $evalue, 'etype' => $etype, 'created_time' => time (), 'modified_time' => time () );
		return $this->_setExtra ( $key, $fields );
	}
	
	function getExtrasByKeys($keys) {
		$extrasDao = $this->getExtrasDao ();
		return $extrasDao->getsByKeys ( $keys );
	}
	
	function getExtra($key) {
		$extra = $this->_getExtra ( $key );
		return ($extra && $extra ['evalue']) ? (($extra ['etype'] == 2) ? unserialize ( $extra ['evalue'] ) : $extra ['evalue']) : null;
	}
	
	function _setExtra($key, $data) {
		$fields = array ('ekey' => $key, 'evalue' => $data ['evalue'], 'etype' => $data ['etype'], 'created_time' => time (), 'modified_time' => time () );
		$extrasDao = $this->getExtrasDao ();
		return $extrasDao->insert ( $fields );
	}
	
	function _getExtra($key) {
		$extrasDao = $this->getExtrasDao ();
		return $extrasDao->get ( $key );
	}
	
	function getExtras() {
		$extrasDao = $this->getExtrasDao ();
		return $extrasDao->gets ();
	}
	
	function getExtrasDao() {
		return ACloud_Sys_Core_Common::loadSystemClass ( 'extras', 'config.dao' );
	}

}