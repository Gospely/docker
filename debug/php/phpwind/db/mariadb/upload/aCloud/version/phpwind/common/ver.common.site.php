<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
define ( 'SITE_INVALID_PARAMS', 401 );
class ACloud_Ver_Common_Site extends ACloud_Ver_Common_Base {
	
	function getTablePartitions($type) {
		list ( $type, $allPartitions ) = array (trim ( $type ), $this->getAllTablePartitions () );
		if (! $type)
			return $this->buildResponse ( 0, $allPartitions );
		$result = isset ( $allPartitions [$type] ) ? $allPartitions [$type] : array ();
		return $this->buildResponse ( 0, $result );
	}
	
	function checkTableField($table, $field) {
		list ( $table, $field ) = array (trim ( $table ), trim ( $field ) );
		if (! $table || ! $field)
			return $this->buildResponse ( SITE_INVALID_PARAMS );
		$result = $GLOBALS ['db']->get_one ( sprintf ( 'SHOW COLUMNS FROM %s LIKE %s', S::sqlMetadata ( $table ), S::sqlEscape ( $field ) ) );
		return $this->buildResponse ( (S::isArray ( $result ) ? 0 : 1) );
	}
	
	function getAllTablePartitions() {
		list ( $result, $threadInfo, $postInfo ) = array (array (), ACloud_Sys_Core_Common::getGlobal ( 'db_tlist' ), ACloud_Sys_Core_Common::getGlobal ( 'db_plist' ) );
		$result ['tmsgs'] = array ('pw_tmsgs' );
		$result ['posts'] = array ('pw_posts' );
		if ($threadInfo) {
			foreach ( $threadInfo as $k => $v ) {
				if (! $k)
					continue;
				$result ['tmsgs'] [] = 'pw_tmsgs' . intval ( $k );
			}
		}
		if ($postInfo) {
			foreach ( $postInfo as $k => $v ) {
				if (! $k)
					continue;
				$result ['posts'] [] = 'pw_posts' . intval ( $k );
			}
		}
		return $result;
	}
}