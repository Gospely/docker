<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

define ( 'Friend_INVALID_PARAMS', 101 );
define ( 'Friend_NOT_EXISTS', 102 );
define ( 'Friend_ALREADY_FOLLOWED', 103 );
define ( 'Friend_FOLLOWED_BLACKLIST', 104 );
define ( 'Friend_NOT_FOLLOWED', 105 );

class ACloud_Ver_Customized_Friend extends ACloud_Ver_Customized_Base {
	
	function getAllFriend($uid, $offset, $limit) {
		list ( $uid, $offset, $limit ) = array (intval ( $uid ), intval ( $offset ), intval ( $limit ) );
		if ($uid < 1)
			return $this->buildResponse ( Friend_INVALID_PARAMS );
		$uids = array ();
		$count = $GLOBALS ['db']->get_value ( "SELECT COUNT(*) as count FROM pw_friends WHERE status='0' AND uid=" . S::sqlEscape ( $uid ) );
		if ($count < 1)
			return $this->buildResponse ( 0, array () );
		$sqlLimit = $offset > 0 ? ' AND friendid > ' . S::sqlLimit ( $offset ) : '';
		$query = $GLOBALS ['db']->query ( "SELECT friendid FROM pw_friends WHERE status='0' AND uid=" . S::sqlEscape ( $uid ) . $sqlLimit . " ORDER BY friendid ASC LIMIT " . intval ( $limit ) );
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$uids [] = $rt ['friendid'];
		}
		if (! S::isArray ( $uids ))
			return $this->buildResponse ( 0, array () );
		$userService = L::loadClass ( 'UserService', 'user' );
		$userInfos = $userService->getByUserIds ( $uids );
		require_once (R_P . 'require/showimg.php');
		$result = array ();
		foreach ( $userInfos as $userInfo ) {
			$tmpIcon = showfacedesign ( $userInfo ['icon'], 1, 's' );
			$result [] = array ('uid' => $userInfo ['uid'], 'icon' => $tmpIcon [0], 'username' => $userInfo ['username'] );
		}
		return $this->buildResponse ( 0, array ('friends' => $result, 'count' => $count ) );
	}
	
	function searchAllFriend($uid, $keyword, $offset, $limit) {
		list ( $uid, $keyword, $offset, $limit ) = array (intval ( $uid ), trim ( $keyword ), intval ( $offset ), intval ( $limit ) );
		if ($uid < 1 || ! $keyword)
			$this->buildResponse ( Friend_INVALID_PARAMS );
		$keyword = ACloud_Sys_Core_Common::convert ( $keyword, ACloud_Sys_Core_Common::getGlobal ( 'g_charset' ), 'UTF-8' );
		$uids = array ();
		$query = $GLOBALS ['db']->query ( "SELECT friendid FROM pw_friends WHERE status='0' AND uid=" . S::sqlEscape ( $uid ) );
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$uids [] = $rt ['friendid'];
		}
		if (! S::isArray ( $uids ))
			return $this->buildResponse ( 0, array () );
		list ( $result, $uids ) = array (array (), array_slice ( $uids, 0, 150 ) );
		$count = $GLOBALS ['db']->get_value ( "SELECT COUNT(*) as count FROM pw_members WHERE username LIKE " . S::sqlEscape ( "%$keyword%" ) . " AND uid IN (" . S::sqlImplode ( $uids ) . ")" );
		if ($count < 1)
			return $this->buildResponse ( 0, array () );
		$sqlLimit = $offset > 0 ? ' AND uid > ' . S::sqlEscape ( $offset ) : '';
		$query = $GLOBALS ['db']->query ( "SELECT uid,icon,username FROM pw_members WHERE username LIKE " . S::sqlEscape ( "%$keyword%" ) . " AND uid IN (" . S::sqlImplode ( $uids ) . ") $sqlLimit ORDER BY uid ASC LIMIT " . intval ( $limit ) );
		require_once (R_P . 'require/showimg.php');
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$tmpIcon = showfacedesign ( $rt ['icon'], 1, 's' );
			$result [] = array ('uid' => $rt ['uid'], 'username' => $rt ['username'], 'icon' => $tmpIcon [0] );
		}
		return $this->buildResponse ( 0, array ('count' => $count, 'friends' => $result ) );
	}
	
	function getFollowByUid($uid, $offset, $limit) {
		list ( $uid, $offset, $limit ) = array (intval ( $uid ), intval ( $offset ), intval ( $limit ) );
		if ($uid < 1)
			return $this->buildResponse ( Friend_INVALID_PARAMS );
		$attentionService = L::loadClass ( 'Attention', 'friend' );
		$count = $attentionService->countFollows ( $uid );
		if ($count < 1)
			return $this->buildResponse ( 0, array () );
		$sqlLimit = $offset > 0 ? ' AND friendid < ' . S::sqlEscape ( $offset ) : '';
		$sql = "SELECT m.uid,m.username,m.icon as face,m.honor,m.groupid,m.memberid,m.gender,md.thisvisit,md.lastvisit,md.fans" . " FROM pw_attention f " . " LEFT JOIN pw_members m ON f.friendid = m.uid" . " LEFT JOIN pw_memberdata md ON f.friendid = md.uid" . " WHERE f.uid = " . S::sqlEscape ( $uid ) . " $sqlLimit ORDER BY f.friendid DESC LIMIT " . intval ( $limit );
		$query = $GLOBALS ['db']->query ( $sql );
		require_once (R_P . 'require/showimg.php');
		$result = array ();
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$tmpIcon = showfacedesign ( $rt ['face'], 1, 's' );
			$result [$rt ['uid']] = array ('uid' => $rt ['uid'], 'username' => $rt ['username'], 'face' => $tmpIcon [0] );
		}
		$currentUid = ACloud_Sys_Core_Common::getGlobal ( 'customized_current_uid', 0 );
		$relationIds = $this->getUids ( $attentionService->getFollowList ( $currentUid ), 'friendid' );
		$result = $this->buildFriendList ( $uid, $result, $relationIds );
		return $this->buildResponse ( 0, array ('friends' => $result, 'count' => $count ) );
	}
	
	function addFollowByUid($uid, $touid) {
		list ( $uid, $touid ) = array (intval ( $uid ), intval ( $touid ) );
		if ($uid < 1 || $touid < 1)
			return $this->buildResponse ( Friend_INVALID_PARAMS );
		$attentionService = L::loadClass ( 'Attention', 'friend' );
		if ($attentionService->isFollow ( $uid, $touid ))
			return $this->buildResponse ( Friend_ALREADY_FOLLOWED );
		if ($attentionService->isInBlackList ( $touid, $uid ))
			return $this->buildResponse ( Friend_FOLLOWED_BLACKLIST );
		$attentionService->addFollow ( $uid, $touid );
		$count = $attentionService->countFollows ( $uid );
		return $this->buildResponse ( 0, array ('follows' => $count ) );
	}
	
	function deleteFollowByUid($uid, $touid) {
		list ( $uid, $touid ) = array (intval ( $uid ), intval ( $touid ) );
		if ($uid < 1 || $touid < 1)
			return $this->buildResponse ( Friend_INVALID_PARAMS );
		$attentionService = L::loadClass ( 'Attention', 'friend' );
		if (! $attentionService->isFollow ( $uid, $touid ))
			return $this->buildResponse ( Friend_NOT_FOLLOWED );
		$attentionService->delFollow ( $uid, $touid );
		$count = $attentionService->countFollows ( $uid );
		return $this->buildResponse ( 0, array ('follows' => $count ) );
	}
	
	function getFanByUid($uid, $offset, $limit) {
		list ( $uid, $offset, $limit ) = array (intval ( $uid ), intval ( $offset ), intval ( $limit ) );
		if ($uid < 1)
			$this->buildResponse ( Friend_INVALID_PARAMS );
		$attentionService = L::loadClass ( 'Attention', 'friend' );
		$count = $attentionService->countFans ( $uid );
		if ($count < 1)
			return $this->buildResponse ( 0, array () );
		$sqlLimit = $offset > 0 ? ' AND f.uid < ' . S::sqlEscape ( $offset ) : '';
		$sql = "SELECT m.uid,m.username,m.icon as face,m.honor,m.groupid,m.memberid,m.gender,md.thisvisit,md.lastvisit,md.fans FROM pw_attention f LEFT JOIN pw_members m ON f.uid = m.uid" . " LEFT JOIN pw_memberdata md ON f.uid = md.uid" . " WHERE f.friendid=" . S::sqlEscape ( $uid ) . " $sqlLimit ORDER BY f.uid DESC LIMIT " . intval ( $limit );
		$query = $GLOBALS ['db']->query ( $sql );
		require_once (R_P . 'require/showimg.php');
		$result = array ();
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$tmpIcon = showfacedesign ( $rt ['face'], 1, 's' );
			$result [$rt ['uid']] = array ('uid' => $rt ['uid'], 'username' => $rt ['username'], 'face' => $tmpIcon [0] );
		}
		$currentUid = ACloud_Sys_Core_Common::getGlobal ( 'customized_current_uid', 0 );
		$followIds = $this->getUids ( $attentionService->getFollowList ( $currentUid ), 'friendid' );
		$result = $this->buildFriendList ( $uid, $result, $followIds );
		return $this->buildResponse ( 0, array ('friends' => $result, 'count' => $count ) );
	}
	
	function buildFriendList($uid, $data, $filterIds) {
		$attentionService = L::loadClass ( 'Attention', 'friend' );
		$ids = $this->getUids ( $data );
		$result = array ();
		foreach ( $ids as $id ) {
			$result [] = array ('uid' => $id, 'username' => $data [$id] ['username'], 'icon' => $data [$id] ['face'], 'isfollowed' => (S::inArray ( $id, $filterIds ) ? 1 : 0) );
		}
		return $result;
	}
	
	function getUids($data, $key = 'uid') {
		$result = array ();
		if (! S::isArray ( $data ))
			return $result;
		foreach ( $data as $value ) {
			$result [] = $value [$key];
		}
		return $result;
	}
}