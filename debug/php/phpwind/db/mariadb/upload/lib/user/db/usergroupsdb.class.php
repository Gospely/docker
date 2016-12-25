<?php
!defined('P_W') && exit('Forbidden');
/**
 * 用户组信息DAO服务
 * @package PW_TopicDB
 */
class PW_UserGroupsDB extends BaseDB{
	
	var $_tableName = 'pw_usergroups';
	var $_primaryKey = 'gid';
	
	function getUserGroups($type){
		if(!$type) return false;
		$query = $this->_db->query("SELECT gid FROM $this->_tableName WHERE gptype=".S::sqlEscape($type));
		return array_keys($this->_getAllResultFromQuery($query,'gid'));
	}
}
	