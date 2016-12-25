<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_PATH . '/system/core/sys.core.dao.php';
class ACloud_Sys_Config_Dao_GeneralData extends ACloud_Sys_Core_Dao {
	
	function executeSql($sql) {
		$sql = trim ( $sql );
		if (! $sql)
			return false;
		return $this->fetchAll ( $sql );
	}
}