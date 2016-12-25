<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

define ( 'PERMISSIONS_INVALID_PARAMS', 701 );
define ( 'PERMISSIONS_USER_NOT_EXISTS', 701 );

class ACloud_Ver_Common_Permissions extends ACloud_Ver_Common_Base {
	
	function isUserBanned($uid) {
		$uid = intval ( $uid );
		if ($uid < 1)
			return $this->buildResponse ( PERMISSIONS_INVALID_PARAMS );
		$userService = L::loadClass ( 'UserService', 'user' );
		$userInfo = $userService->get ( $uid );
		if (! S::isArray ( $userInfo ))
			return $this->buildResponse ( PERMISSIONS_USER_NOT_EXISTS );
		$groupId = $userInfo ['groupid'] == - 1 ? $userInfo ['memberid'] : $userInfo ['groupid'];
		$_G = array ();
		if (file_exists ( D_P . "data/groupdb/group_$groupId.php" )) {
			require S::escapePath ( D_P . "data/groupdb/group_$groupId.php" );
		} else {
			require (D_P . 'data/groupdb/group_1.php');
		}
		$code = 0;
		if ($groupId == 6 || getstatus ( $userInfo ['userstatus'], PW_USERSTATUS_BANUSER ) || ! $_G ['allowpost'])
			$code = 500;
		return $this->buildResponse ( $code );
	}
	
	function readForum($uid, $fid) {
		list ( $uid, $fid ) = array (intval ( $uid ), intval ( $fid ) );
		if ($uid < 1 || $fid < 1)
			return $this->buildResponse ( PERMISSIONS_INVALID_PARAMS );
		$userInfo = $GLOBALS ['db']->get_one ( "SELECT m.uid,m.username,m.password,m.safecv,m.email,m.bday,m.oicq,m.groupid,m.memberid,m.groups,m.icon,m.regdate,m.honor,m.timedf, m.style,m.datefm,m.t_num,m.p_num,m.yz,m.newpm,m.userstatus,m.shortcut,m.medals,md.lastmsg,md.postnum,md.rvrc,md.money,md.credit,md.currency,md.lastvisit,md.thisvisit,md.onlinetime,md.lastpost,md.todaypost,md.monthpost,md.onlineip,md.uploadtime,md.uploadnum,md.starttime,md.pwdctime,md.monoltime,md.digests,md.f_num,md.creditpop,md.jobnum,md.lastgrab,md.follows,md.fans,md.newfans,md.newreferto,md.newcomment,md.postcheck FROM pw_members m LEFT JOIN pw_memberdata md ON m.uid=md.uid WHERE m.uid=" . S::sqlEscape ( $uid ) . " AND m.groupid<>'0' AND md.uid IS NOT NULL" );
		if (! S::isArray ( $userInfo ))
			return $this->buildResponse ( PERMISSIONS_USER_NOT_EXISTS );
		$groupId = $userInfo ['groupid'] == - 1 ? $userInfo ['memberid'] : $userInfo ['groupid'];
		L::loadClass ( 'forum', 'forum', false );
		$pwforum = new PwForum ( $fid );
		$code = ! $pwforum->allowvisit ( $userInfo, $groupId ) ? 500 : 0;
		return $this->buildResponse ( $code );
	}
}