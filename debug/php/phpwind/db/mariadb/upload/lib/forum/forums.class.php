<?php
!defined('P_W') && exit('Forbidden');
/**
 * 版块服务层
 * @author liuhui @2010-4-25
 * @version phpwind 8.0
 */
class PW_Forums {
	function getForum($forumId){
		$forumId = intval($forumId);
		if( 1 > $forumId) return false;
		$forumsDao = $this->getForumsDao();
		return $forumsDao->get($forumId);
	}
	
	function getForumsByFids($fids) {
		if (!S::isArray($fids)) return array();
		$tmpFids = array(0);
		foreach ($fids as $fid) {
			if (!$fid) continue;
			$tmpFids[] = intval($fid);
		}
		if (!S::isArray($tmpFids)) return array();
		$forumsDao = $this->getForumsDao();
		return $forumsDao->getFormusByFids($tmpFids);
	}
	
	function getsNotCategory(){
		$forumsDao = $this->getForumsDao();
		return $forumsDao->getsNotCategory();
	}
	
	function getForumsDao(){
		static $sForumsDao;
		if(!$sForumsDao){
			$sForumsDao = L::loadDB('forums', 'forum');
		}
		return $sForumsDao;
	}
	
	/*
	 * 删除版块管理员
	 * */
	function deleteForumAdmin($username,$fid = 0) {
		$fid = intval($fid);
		$forumsDao = $this->getForumsDao();
		$f_admin = $forumsDao->getForumAdmin($fid);
		
		foreach($f_admin as $k=>$v){
			if(false !== $key = array_search($username,$v)){
				unset($v[$key]);
				$forumsDao->_update(array('forumadmin',implode(',',$v)), $k);
			}
		}
	}
	
	/**
	 * 获取开启图酷的版块
	 * @return array
	 */
	function getTucoolForums(){
		$tucoolForums = array();
		$fids = $this->getAllForumIds();
		$forumsDao = $this->getForumsDao();
		$forumSets = $forumsDao->getForumSetsByFids($fids);
		if ($forumSets) {
			foreach ($forumSets as $k=>$v) {
				$forumset = array();
				$v = @unserialize($v['forumset']);
				if(!$v['iftucool']) continue;
				$forumset['tucoolpic'] = intval($v['tucoolpic']);
				S::isArray($forumset) && $tucoolForums[$k] = $forumset;
			}
		}
		if ($tucoolForums) {
			$forums = $forumsDao->getFormusByFids(array_keys($tucoolForums),'fid,name');
			foreach ($forums as $k=>$v) {
				$tucoolForums[$k] = array_merge($tucoolForums[$k],$v);
			}
		}
		return $tucoolForums;
	}
	
	/**
	 * 获取开启孔明灯的版块
	 * @return array
	 */
	function getKmdForums(){
		$kmdForums = array();
		$fids = $this->getAllForumIds();
		$forumsDao = $this->getForumsDao();
		$forumSets = $forumsDao->getForumSetsByFids($fids);
		$forumset = array();
		if ($forumSets) {
			foreach ($forumSets as $k=>$v) {
				$v = @unserialize($v['forumset']);
				if(!$v['ifkmd']) continue;
				$forumset[] = $k;
			}
		}
		if(!$forumset) return array();
		return $forumsDao->getFormusByFids($forumset,'fid,name');
	}
	
	function getAllForumIds() {
		$forums = getForumCache();
		foreach ($forums as $v) {
			$fids[] = $v['fid'];
		}
		return $fids;
	}
}