<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Sys_Config_Service_ReSet {
	
	function resetConfig() {
		$extrasDao = ACloud_Sys_Core_Common::loadSystemClass ( 'extras', 'config.dao' );
		$extrasDao->deleteAll ();
		
		$appsDao = ACloud_Sys_Core_Common::loadSystemClass ( 'apps', 'config.dao' );
		$appsDao->deleteAll ();
		
		$appConfigsDao = ACloud_Sys_Core_Common::loadSystemClass ( 'app.configs', 'config.dao' );
		$appConfigsDao->deleteAll ();
		return true;
	}
}