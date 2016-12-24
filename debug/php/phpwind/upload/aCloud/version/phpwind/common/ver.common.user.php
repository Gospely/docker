<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

define ( 'USER_INVALID_PARAMS', 801 );

class ACloud_Ver_Common_User extends ACloud_Ver_Common_Base {
	
	function getPrimaryKeyAndTable() {
		return array ('pw_members', 'uid' );
	}
	
	function banUser($uid) {
		$uid = intval ( $uid );
		if ($uid < 1)
			return $this->buildResponse ( USER_INVALID_PARAMS );
		$timestamp = time ();
		$GLOBALS ['db_cvtime'] != 0 && $timestamp += $GLOBALS ['db_cvtime'] * 60;
		$userService = L::loadClass ( 'UserService', 'user' ); /* @var $userService PW_UserService */
		$userService->update ( $uid, array ('groupid' => 6 ) );
		$userService->setUserStatus ( $uid, PW_USERSTATUS_BANUSER, true );
		$banArray = array ('uid' => $uid, 'fid' => 0, 'type' => 2, 'startdate' => $timestamp, 'days' => 0, 'admin' => '', 'reason' => '' );
		$GLOBALS ['db']->update ( "REPLACE INTO `pw_banuser` SET " . S::sqlSingle ( $banArray ), false );
		return $this->buildResponse ( 0 );
	}
	
	function getIconsByUids($uids) {
		$uids = $uids ? explode ( ',', $uids ) : array ();
		$uids = array_filter ( array_unique ( $uids ) );
		if (! S::isArray ( $uids ))
			return $this->buildResponse ( USER_INVALID_PARAMS );
		$userService = L::loadClass ( 'UserService', 'user' );
		$userInfos = $userService->getByUserIds ( $uids );
		if (! S::isArray ( $userInfos ))
			return $this->buildResponse ( 0 );
		list ( $result, $utilityService ) = array (array (), $this->getCommonUtilityService () );
		foreach ( $userInfos as $userInfo ) {
			$tmp = array ();
			$tmp ['uid'] = $userInfo ['uid'];
			$tmp ['icon'] = $utilityService->getUserIcon ( $userInfo ['icon'] );
			$result [] = $tmp;
		}
		return $this->buildResponse ( 0, $result );
	}
	
	function getUsersByRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		if ($startId < 0 || $startId > $endId || $endId < 1)
			return array ();
		$query = $GLOBALS ['db']->query ( "SELECT m.*,mi.*,md.* FROM pw_members m LEFT JOIN pw_memberinfo mi USING(uid) LEFT JOIN pw_memberdata md USING(uid) WHERE m.uid >= " . S::sqlEscape ( $startId ) . " AND m.uid <= " . S::sqlEscape ( $endId ) );
		$result = $members = array ();
		$utilityService = $this->getCommonUtilityService ();
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$result [] = $rt;
		}
		if (! S::isArray ( $result ))
			return array ();
		foreach ( $result as $member ) {
			$member ['memberurl'] = $GLOBALS ['db_bbsurl'] . '/u.php?uid=' . $member ['uid'];
			$member ['icon'] = $utilityService->getUserIcon ( $member ['icon'] );
			$members [$member ['uid']] = $member;
		}
		return $this->filterMemberFields ( $members );
	}
	
	function filterMemberFields($members) {
		if (! S::isArray ( $members ))
			return array ();
		$result = array ();
		foreach ( $members as $value ) {
			unset ( $value ['password'], $value ['safecv'] );
			$result [] = $value;
		}
		return $result;
	}
}