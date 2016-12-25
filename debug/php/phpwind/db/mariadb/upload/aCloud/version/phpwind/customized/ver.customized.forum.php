<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

define ( 'FORUM_INVALID_PARAMS', 401 );
define ( 'FORUM_FAVOR_MAX', 402 );
define ( 'FORUM_FAVOR_ALREADY', 403 );
define ( 'FORUM_NOT_EXISTS', 404 );

class ACloud_Ver_Customized_Forum extends ACloud_Ver_Customized_Base {
	
	function getAllForum() {
		$user = $this->getCurrentUser ( array ('visit', 'post' ) );
		$user->initRight ();
		$query = $GLOBALS ['db']->query ( "SELECT f.fid,f.name,f.fup,f.type,f.ifsub,f.childid,f.allowvisit,fdata.tpost FROM pw_forums f LEFT JOIN pw_forumdata fdata ON f.fid = fdata.fid  WHERE f.ifcms!=2 AND f.cms!='1' ORDER BY f.vieworder,f.fid" );
		$cates = $forums = $subForums = $secondSubForums = $filerFids = array ();
		$count = 0;
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$rt ['name'] = strip_tags ( $rt ['name'] );
			if ($rt ['type'] == 'category') {
				$cates [$rt ['fid']] = array ('fid' => $rt ['fid'], 'forumname' => strip_tags ( $rt ['name'] ), 'childid' => $rt ['childid'], 'type' => $rt ['type'], 'todaypost' => '' );
			} elseif ($rt ['type'] == 'forum') {
				if (! $user->allowcheck ( $rt ['allowvisit'], $rt ['fid'], 'visit' )) {
					$filerFids [] = $rt ['fid'];
					continue;
				}
				$forums [$rt ['fup']] [$rt ['fid']] = array ('fid' => $rt ['fid'], 'forumname' => strip_tags ( $rt ['name'] ), 'childid' => $rt ['childid'], 'type' => $rt ['type'], 'todaypost' => $rt ['tpost'] );
			} elseif ($rt ['type'] == 'sub') {
				if (S::inArray ( $rt ['fup'], $filerFids ))
					continue;
				$subForums [$rt ['fup']] [$rt ['fid']] = array ('fid' => $rt ['fid'], 'forumname' => strip_tags ( $rt ['name'] ), 'childid' => $rt ['childid'], 'type' => $rt ['type'], 'todaypost' => '' );
			} elseif ($rt ['type'] == 'sub2') {
				if (S::inArray ( $rt ['fup'], $filerFids ))
					continue;
				$secondSubForums [$rt ['fup']] [$rt ['fid']] = array ('fid' => $rt ['fid'], 'forumname' => strip_tags ( $rt ['name'] ), 'childid' => $rt ['childid'], 'type' => $rt ['type'], 'todaypost' => '' );
			}
			$count ++;
		}
		$result = array ();
		foreach ( $cates as $cateId => $cateInfo ) {
			$cateInfo ['child'] = (isset ( $forums [$cateId] ) && $forums [$cateId]) ? $this->organizeForums ( $forums [$cateId], $subForums, $secondSubForums ) : array ();
			$result [] = $cateInfo;
		}
		return $this->buildResponse ( 0, array ('count' => $count, 'forums' => $result ) );
	}
	
	function getForumByFid($fid) {
		$fid = intval ( $fid );
		if ($fid < 1)
			return $this->buildResponse ( FORUM_INVALID_PARAMS );
		$data = array ();
		$result = $GLOBALS ['db']->get_one ( "SELECT f.fid,f.name as forumname,fdata.tpost as todaypost FROM pw_forums f LEFT JOIN pw_forumdata fdata ON f.fid = fdata.fid  WHERE f.fid= " . pwEscape ( $fid ) . " ORDER BY f.vieworder" );
		$result ['forumname'] = strip_tags ( $result ['forumname'] );
		return $this->buildResponse ( 0, array ('forum' => $result ) );
	}
	
	function getChildForumByFid($fid) {
		$fid = intval ( $fid );
		if ($fid < 1)
			return $this->buildResponse ( FORUM_INVALID_PARAMS );
		$subForums = $secondFids = $secondSubForums = array ();
		list ( $subForums, $secondFids ) = $this->getSubForumsByFids ( array ($fid ) );
		if (! S::isArray ( $subForums ))
			return $this->buildResponse ( 0, array () );
		if (S::isArray ( $secondFids ))
			list ( $secondSubForums ) = $this->getSubForumsByFids ( $secondFids );
		$result = array ();
		foreach ( $subForums [$fid] as $value ) {
			$value ['child'] = (isset ( $secondSubForums [$value ['fid']] ) && $secondSubForums [$value ['fid']]) ? $secondSubForums [$value ['fid']] : array ();
			$result [] = $value;
		}
		return $this->buildResponse ( 0, array ('count' => count ( $subForums ) + count ( $secondSubForums ), 'forums' => $result ) );
	}
	
	function getSubForumsByFids($fids) {
		if (! S::isArray ( $fids ))
			return array ();
		$user = $this->getCurrentUser ( array ('visit', 'post' ) );
		$user->initRight ();
		$result = $sub = array ();
		$query = $GLOBALS ['db']->query ( "SELECT f.fid,f.name,f.fup,f.type,f.ifsub,f.childid,f.allowvisit,fdata.tpost FROM pw_forums f LEFT JOIN pw_forumdata fdata ON f.fid = fdata.fid WHERE f.fup IN (" . S::sqlImplode ( $fids ) . ") ORDER BY f.vieworder" );
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			if (! $user->allowcheck ( $rt ['allowvisit'], $rt ['fid'], 'visit' ))
				continue;
			$rt ['name'] = strip_tags ( $rt ['name'] );
			$result [$rt ['fup']] [$rt ['fid']] = array ('fid' => $rt ['fid'], 'forumname' => $rt ['name'], 'childid' => $rt ['childid'], 'type' => $rt ['type'], 'todaypost' => $rt ['tpost'] );
			$rt ['childid'] && $sub [] = $rt ['fid'];
		}
		return array ($result, $sub );
	}
	
	function organizeForums($forums, $subForums, $secondSubForums) {
		$result = array ();
		foreach ( $forums as $fid => $forum ) {
			$forum ['child'] = (isset ( $subForums [$fid] ) && $subForums [$fid]) ? $this->organizeSubForums ( $subForums [$fid], $secondSubForums ) : array ();
			$result [] = $forum;
		}
		return $result;
	}
	
	function organizeSubForums($subForums, $secondSubForums) {
		$result = array ();
		foreach ( $subForums as $fid => $subForum ) {
			$subForum ['child'] = (isset ( $secondSubForums [$fid] ) && $secondSubForums [$fid]) ? $secondSubForums [$fid] : array ();
			$result [] = $subForum;
		}
		return $result;
	}
}