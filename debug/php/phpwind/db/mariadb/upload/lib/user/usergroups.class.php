<?php 
!defined('P_W') && exit('Forbidden');
/**
 * 用户组信息service
 * @package PW_UserGroups
 */
class PW_UserGroups{
	
	/**
	 * 根据用户组类型获取用户组id
	 * 
	 * @param string $type 用户组类型
	 * @return int gid 用户组id
	 */
	function getUserGroupIds($type){
		$type = trim($type);
		if(!$type) return false;
		$userGroupsDb = $this->_getUserGroupsDao(); 
		return $userGroupsDb->getUserGroups($type);
	}
	
	function _getUserGroupsDao(){
		return L::loadDB('usergroups', 'user');
	}
}
