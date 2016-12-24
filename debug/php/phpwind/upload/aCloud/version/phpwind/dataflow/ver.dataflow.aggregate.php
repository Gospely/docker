<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
define ( 'AC_THREAD_SIG', 1 );
define ( 'AC_DIARY_SIG', 2 );
define ( 'AC_MEMBER_SIG', 3 );
define ( 'AC_FORUM_SIG', 4 );
define ( 'AC_COLONY_SIG', 5 );
define ( 'AC_POST_SIG', 6 );
define ( 'AC_ATTACH_SIG', 8 );
define ( 'AC_DELETE_SIG', 100 );
class ACloud_Ver_DataFlow_Aggregate {
	
	function getMonitorTables() {
		$tableConfigs = ACloud_Ver_DataFlow_Aggregate::getTableConfigs ();
		return array_keys ( $tableConfigs );
	}
	
	function getTypeByTableName($tableName) {
		$tableConfigs = ACloud_Ver_DataFlow_Aggregate::getTableConfigs ();
		return isset ( $tableConfigs [$tableName] ) ? $tableConfigs [$tableName] : null;
	}
	
	function getTableConfigs() {
		$tables = array ('pw_threads' => AC_THREAD_SIG, 'pw_diary' => AC_DIARY_SIG, 'pw_members' => AC_MEMBER_SIG, 'pw_forums' => AC_FORUM_SIG, 'pw_colonys' => AC_COLONY_SIG, 'pw_posts' => AC_POST_SIG, 'pw_attachs' => AC_ATTACH_SIG );
		if ($GLOBALS ['db_plist']) {
			foreach ( $GLOBALS ['db_plist'] as $k => $v ) {
				$tmpName = 'pw_posts' . ($k ? $k : '');
				$tables [$tmpName] = AC_POST_SIG;
			}
		}
		return $tables;
	}
	
	function getDeleteSig() {
		return AC_DELETE_SIG;
	}
}