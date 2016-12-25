<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Ver_Core_Dao {
	
	function __construct() {
		$this->getDB ();
	}
	
	function ACloud_Ver_Core_Dao() {
		$this->__construct ();
	}
	
	function getDB() {
		if (! isset ( $GLOBALS ['db'] ) || ! is_object ( $GLOBALS ['db'] )) {
			include D_P . 'data/sql_config.php';
			$database = 'mysqli';
			if ($database == 'mysqli' && $this->checkDatabase ( 'mysqli' ) === false) {
				$database = 'mysql';
			}
			require_once ACloud_Sys_Core_S::escapePath ( R_P . "require/db_$database.php" );
			$GLOBALS ['db'] = new DB ( $dbhost, $dbuser, $dbpw, $dbname, $PW, $charset, $pconnect );
		}
		return $GLOBALS ['db'];
	}
	
	function checkDatabase($module, $checkFunction = 'mysqli_get_client_info') {
		return extension_loaded ( $module ) && $checkFunction && function_exists ( $checkFunction ) ? true : false;
	}
	
	function fetchOne($sql) {
		return $this->getDB ()->get_one ( $sql );
	}
	
	function fetchAll($sql, $resultIndexKey = null, $type = MYSQL_ASSOC) {
		$query = $this->getDB ()->query ( $sql );
		$rt = array ();
		if ($resultIndexKey) {
			while ( $row = $this->getDB ()->fetch_array ( $query, $type ) ) {
				$rt [$row [$resultIndexKey]] = $row;
			}
		} else {
			while ( $row = $this->getDB ()->fetch_array ( $query, $type ) ) {
				$rt [] = $row;
			}
		}
		return $rt;
	}
	
	function getField($sql) {
		return $this->getDB ()->get_value ( $sql );
	}
	
	function query($sql) {
		return $this->getDB ()->query ( $sql );
	}
	
	function insert_id() {
		return $this->getDB ()->insert_id ();
	}
	
	function affected_rows() {
		return $this->getDB ()->affected_rows ();
	}

}