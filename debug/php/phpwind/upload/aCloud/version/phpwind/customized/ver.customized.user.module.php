<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Ver_Customized_User_Module {
	
	var $_db;
	var $uid;
	var $username;
	var $groupid;
	var $info = array ();
	
	var $_G;
	var $_SYSTEM;
	var $_gp_gptype;
	
	function __construct($rights = array()) {
		$this->_db = $GLOBALS ['db'];
		$this->_G = null;
		$this->init ( $rights );
	}
	
	function ACloud_Ver_Customized_User_Module($rights = array()) {
		$this->__construct ( $rights );
	}
	
	function getUserByUid($uid, $rights) {
		$sqladd = $sqltab = '';
		if ($rights) {
			$sqladd = ',sr.' . implode ( ',sr.', $rights );
			$sqltab = "LEFT JOIN pw_singleright sr ON m.uid=sr.uid";
		}
		$detail = $this->_db->get_one ( "SELECT m.uid,m.username,m.password,m.safecv,m.email,m.bday,m.oicq,m.groupid,m.memberid,m.groups,m.icon,m.regdate,m.honor,m.timedf, m.style,m.datefm,m.t_num,m.p_num,m.yz,m.newpm,m.userstatus,m.shortcut,m.medals,md.lastmsg,md.postnum,md.rvrc,md.money,md.credit,md.currency,md.lastvisit,md.thisvisit,md.onlinetime,md.lastpost,md.todaypost,md.monthpost,md.onlineip,md.uploadtime,md.uploadnum,md.starttime,md.pwdctime,md.monoltime,md.digests,md.f_num,md.creditpop,md.jobnum,md.lastgrab,md.follows,md.fans,md.newfans,md.newreferto,md.newcomment,md.postcheck $sqladd FROM pw_members m LEFT JOIN pw_memberdata md ON m.uid=md.uid $sqltab WHERE m.uid=" . S::sqlEscape ( $uid ) . " AND m.groupid<>'0' AND md.uid IS NOT NULL" );
		return $detail;
	}
	
	function init($rights) {
		global $winduid, $windid, $groupid, $winddb;
		$uid = ACloud_Sys_Core_Common::getGlobal ( 'customized_current_uid', 0 );
		if ($uid > 0) {
			$detail = $this->getUserByUid ( $uid, $rights );
			$this->uid = $uid;
			$this->username = $detail ['username'];
			$this->groupid = $groupid = $detail ['groupid'] == '-1' ? $detail ['memberid'] : $detail ['groupid'];
			$detail ['groups'] = $detail ['groups'] ? explode ( ',', trim ( ',', $detail ['groups'] ) ) : array ();
			list ( $detail ['shortcut'], $detail ['appshortcut'] ) = explode ( "\t", $detail ['shortcut'] );
			unset ( $detail ['password'] );
			$this->info = $detail;
			$winddb = $detail;
			$winduid = $uid;
			$windid = $this->username;
		} else {
			$this->username = '';
			$this->uid = 0;
			$this->groupid = 'guest';
		}
	}
	
	function isLogin() {
		return ($this->uid > 0);
	}
	
	function getRight($vKey) {
		$this->initRight ();
		return $this->_G [$vKey];
	}
	
	function getSystemRight($vKey) {
		$this->initRight ();
		return $this->_SYSTEM [$vKey];
	}
	
	function initRight() {
		global $gp_gptype, $SYSTEM;
		if (! is_null ( $this->_G ))
			return;
		if ($this->groupid == 'guest') {
			require (D_P . 'data/groupdb/group_2.php');
		} elseif (file_exists ( D_P . "data/groupdb/group_{$this->groupid}.php" )) {
			require S::escapePath ( D_P . "data/groupdb/group_{$this->groupid}.php" );
		} else {
			require (D_P . 'data/groupdb/group_1.php');
		}
		$this->_G = $_G;
		$this->_SYSTEM = $SYSTEM;
		
		$this->_gp_gptype = $gp_gptype;
	}
	
	function allowcheck($allowgroup, $fid = 0, $singleRinght = '') {
		if (empty ( $allowgroup ) || strpos ( $allowgroup, ",{$this->groupid}," ) !== false) {
			return true;
		}
		if (S::inArray ( $this->username, $GLOBALS ['manager'] )) {
			return true;
		}
		if ($this->info ['groups']) {
			foreach ( $this->info ['groups'] as $value ) {
				if (strpos ( $allowgroup, ",$value," ) !== false) {
					return true;
				}
			}
		}
		if ($fid && $singleRinght && $this->info [$singleRinght] && strpos ( ',' . $this->info [$singleRinght] . ',', ",$fid," ) !== false) {
			return true;
		}
		return false;
	}
}