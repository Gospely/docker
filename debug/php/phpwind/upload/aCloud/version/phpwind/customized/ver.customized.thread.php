<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

define ( 'THREAD_INVALID_PARAMS', 301 );
define ( 'THREAD_USER_NOT_RIGHT', 302 );
define ( 'THREAD_FORUM_NOT_EXIST', 303 );
define ( 'THREAD_USER_NOT_EXIST', 304 );
define ( 'THREAD_ID_NOT_ILLEGAL', 305 );
define ( 'THREAD_EDIT_TIME_LIMIT', 306 );
define ( 'THREAD_USER_NOT_HTML_RIGHT', 307 );
define ( 'THREAD_SYSTEM_ERROR', 500 );
define ( 'THREAD_FAVOR_MAX', 309 );
define ( 'THREAD_FAVOR_ALREADY', 310 );
define ( 'THREAD_NOT_EXIST', 312 );
define ( 'THREAD_LOCKED', 500 );
define ( 'POST_GP_LIMIT', 314 );
define ( 'THREAD_ALLOW_READ', 315 );

class ACloud_Ver_Customized_Thread extends ACloud_Ver_Customized_Base {
	
	function getByTid($tid) {
		global $attachpath, $db_windpost, $foruminfo, $fid, $forumset, $pwforum, $db_hits_store;
		$tid = intval ( $tid );
		if ($tid <= 0)
			return $this->buildResponse ( THREAD_INVALID_PARAMS );
		$GLOBALS ['tid'] = $tid;
		$threadData = $this->_getThread ( $tid, true );
		if (empty ( $threadData ))
			return $this->buildResponse ( 0 );
		$fid = $threadData ['fid'];
		$user = $this->getCurrentUser ();
		$user->initRight ();
		$this->getCustomizedCommonService ()->getReadRight ( $user );
		$GLOBALS ['tpc_buy'] = $threadData ['buy'];
		$GLOBALS ['tpc_author'] = $threadData ['author'];
		L::loadClass ( 'forum', 'forum', false );
		$pwforum = new PwForum ( $fid );
		$foruminfo = $pwforum->foruminfo;
		$forumset = $pwforum->forumset;
		list ( $windVersion ) = explode ( ',', WIND_VERSION );
		if ($windVersion == '8.0') {
			$threadData = $this->_isMyFavoredForEarly ( $tid, $threadData );
		} else {
			$threadData = $this->_isMyFavoredForAfter ( $tid, $threadData );
		}
		$udb = $this->_getUDb ( $threadData );
		$bandb = $pwforum->forumBan ( $udb );
		isset ( $bandb [$threadData ['uid']] ) && $threadData ['groupid'] = 6;
		$_attachList = array ();
		if ($threadData ['aid']) {
			$query = $GLOBALS ['db']->query ( 'SELECT * FROM pw_attachs WHERE tid=' . pwEscape ( $tid ) . ' AND pid=0' );
			while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
				$_attachList [] = $rt;
			}
		}
		$imgsInContent = $this->getCustomizedCommonService ()->parseImgInContent ( $threadData );
		$threadData ['content'] = $this->getCustomizedCommonService ()->parsePostContent ( $threadData );
		$threadData ['content'] = $this->getCustomizedCommonService ()->clearHtmlTag ( $threadData ['content'], '<br>' );
		$threadData ['content'] = $this->getCustomizedCommonService ()->parseEmotionInContent ( $threadData ['content'] );
		$threadData ['attachlist'] = $this->getCustomizedCommonService ()->getAttachWithThumblist ( $_attachList );
		$threadData ['attachlist'] = array_merge ( $imgsInContent, $threadData ['attachlist'] );
		$this->getCustomizedCommonService ()->clearAttachSign ( $_attachList, &$threadData ['content'] );
		
		$threadData ['icon'] = $this->getCustomizedCommonService ()->getUserIcon ( $threadData ['icon'] );
		
		if ($db_hits_store == 0) {
			$GLOBALS ['db']->update ( 'UPDATE pw_threads SET hits=hits+1 WHERE tid=' . pwEscape ( $tid ) );
		} elseif ($db_hits_store == 1) {
			$GLOBALS ['db']->update ( 'UPDATE pw_hits_threads SET hits=hits+1 WHERE tid=' . pwEscape ( $tid ) );
		} elseif ($db_hits_store == 2) {
			if (class_exists ( "pwCache" ) && method_exists ( "pwCache", "writeover" )) {
				pwCache::writeover ( D_P . 'data/bbscache/hits.txt', $tid . "\t", 'ab' );
			} else {
				writeover ( D_P . 'data/bbscache/hits.txt', $tid . "\t", 'ab' );
			}
		}
		return $this->buildResponse ( 0, $threadData );
	}
	
	function getByUid($uid, $offset = 0, $limit = 20) {
		list ( $uid, $offset, $limit ) = array (intval ( $uid ), intval ( $offset ), intval ( $limit ) );
		if ($uid <= 0)
			return $this->buildResponse ( THREAD_INVALID_PARAMS );
		$userService = L::loadClass ( 'UserService', 'user' );
		$userInfo = $userService->get ( $uid );
		if (! $userInfo)
			return $this->buildResponse ( THREAD_USER_NOT_EXIST );
		$userIcon = $this->getCustomizedCommonService ()->getUserIcon ( $userInfo ['icon'] );
		$_result ['threads'] = array ();
		$count = $GLOBALS ['db']->get_value ( "SELECT count(*) as count FROM pw_threads WHERE authorid= " . pwEscape ( $uid ) . " AND fid != 0" );
		$_result ['count'] = $count;
		if ($_result ['count']) {
			$sqlLimit = $offset > 0 ? ' AND t.tid < ' . intval ( $offset ) : '';
			$sql = "SELECT t.*,f.name FROM pw_threads t LEFT JOIN pw_forums f ON t.fid=f.fid WHERE t.authorid=" . pwEscape ( $uid ) . " AND t.fid != 0 $sqlLimit ORDER BY t.postdate DESC LIMIT " . intval ( $limit );
			$query = $GLOBALS ['db']->query ( $sql );
			while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
				$_result ['threads'] [] = array ('tid' => $rt ['tid'], 'fid' => $rt ['fid'], 'forumname' => strip_tags ( $rt ['name'] ), 'icon' => $userIcon, 'author' => $rt ['author'], 'authorid' => $rt ['authorid'], 'subject' => $rt ['subject'], 'postdate' => $rt ['postdate'] );
			}
		}
		return $this->buildResponse ( 0, $_result );
	}
	
	function getLatestThread($fids, $offset, $limit) {
		list ( $fids, $offset, $limit ) = array (($fids ? explode ( ',', $fids ) : array ()), intval ( $offset ), intval ( $limit ) );
		$sqlLimit = $offset > 0 ? ' AND t.tid < ' . intval ( $offset ) : '';
		$fidLimit = S::isArray ( $fids ) ? ' AND t.fid IN (' . S::sqlImplode ( $fids ) . ') ' : '';
		$result = array ();
		$query = $GLOBALS ['db']->query ( "SELECT t.tid,t.fid,t.subject,t.author,t.authorid,t.postdate,f.name as forumname FROM pw_threads t LEFT JOIN pw_forums f ON t.fid=f.fid WHERE t.ifcheck=1 $sqlLimit $fidLimit ORDER BY t.tid DESC LIMIT " . intval ( $limit ) );
		$uids = array ();
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$rt ['forumname'] = strip_tags ( $rt ['forumname'] );
			$rt ['icon'] = '';
			$uids [$rt ['tid']] = $rt ['authorid'];
			$result [$rt ['tid']] = $rt;
		}
		$result = $this->getCustomizedCommonService ()->buildMultiUserIcons ( $uids, $result );
		return $this->buildResponse ( 0, $result );
	}
	
	function getLatestThreadByFavoritesForum($uid, $offset, $limit) {
		list ( $uid, $offset, $limit ) = array (intval ( $uid ), intval ( $offset ), intval ( $limit ) );
		if ($uid <= 0)
			return $this->buildResponse ( THREAD_INVALID_PARAMS );
		$userService = L::loadClass ( 'UserService', 'user' );
		$userInfo = $userService->get ( $uid );
		if (! $userInfo)
			return $this->buildResponse ( THREAD_USER_NOT_EXIST );
		if (! $userInfo ['shortcut'])
			return $this->buildResponse ( 0, array () );
		$favoriteFids = $this->_getFidsByShortCut ( $userInfo ['shortcut'] );
		if (! S::isArray ( $favoriteFids ))
			return $this->buildResponse ( 0, array () );
		return $this->getLatestThreadsByFids ( $favoriteFids, $offset, $limit );
	}
	
	function getLatestThreadByFollowUser($uid, $offset, $limit) {
		list ( $uid, $offset, $limit ) = array (intval ( $uid ), intval ( $offset ), intval ( $limit ) );
		if ($uid <= 0)
			return $this->buildResponse ( THREAD_INVALID_PARAMS );
		$attentionService = L::loadClass ( 'Attention', 'friend' );
		$uids = $attentionService->getUidsInFollowList ( $uid, 1, 100 );
		if (! S::isArray ( $uids ))
			$this->buildResponse ( 0, array () );
		return $this->getLatestThreadByUids ( $uids, $offset, $limit );
	}
	
	function getLatestImgThread($fids, $offset, $limit) {
		list ( $fids, $offset, $limit ) = array (($fids ? explode ( ',', $fids ) : array ()), intval ( $offset ), intval ( $limit ) );
		global $attachpath;
		$tmpData = $result ['threads'] = $tmpThreadInfos = array ();
		$sqlLimit = $offset > 0 ? ' AND tid < ' . intval ( $offset ) : '';
		$fidLimit = S::isArray ( $fids ) ? ' AND fid IN (' . S::sqlImplode ( $fids ) . ') ' : '';
		$result ['count'] = intval ( $GLOBALS ['db']->get_value ( "SELECT COUNT(distinct tid) as count FROM pw_attachs WHERE `pid`=0 AND `did`=0 AND `type`='img' $fidLimit" ) );
		if ($result ['count'] > 0) {
			$query = $GLOBALS ['db']->query ( "SELECT tid,attachurl,ifthumb,count(attachurl) as count FROM pw_attachs WHERE `pid`=0 AND `did`=0 AND `type`='img' $sqlLimit $fidLimit GROUP BY tid ORDER BY uploadtime DESC LIMIT " . intval ( $limit ) );
			while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
				$rt ['tid'] > 0 && $tmpData [$rt ['tid']] ['picurl'] = current ( $this->getCustomizedCommonService ()->getAttachWithThumblist ( array ($rt ) ) );
				$rt ['tid'] > 0 && $tmpData [$rt ['tid']] ['piccount'] = $rt ['count'];
			}
		}
		if (count ( $tmpData ) > 0) {
			$tmpThreadInfos = $this->getThreadsByTids ( array_filter ( array_unique ( array_keys ( $tmpData ) ) ) );
			foreach ( $tmpData as $tid => $value ) {
				$tmpResult = (isset ( $tmpThreadInfos [$tid] ) ? $tmpThreadInfos [$tid] : array ());
				if (isset ( $tmpResult ['content'] )) {
					$tmpResult ['content'] = preg_replace ( '|\[attachment=\d+\]|i', '', $tmpResult ['content'] );
					$tmpResult ['content'] = strip_tags ( stripWindCode ( $tmpResult ['content'] ) );
				}
				$tmpData [$tid] = array_merge ( $value, $tmpResult );
			}
		}
		$result ['threads'] = $tmpData;
		return $this->buildResponse ( 0, $result );
	}
	
	function getThreadImgs($tid) {
		global $attachpath;
		$tid = intval ( $tid );
		if ($tid < 1)
			return $this->buildResponse ( THREAD_INVALID_PARAMS );
		$result ['img'] = array ();
		$query = $GLOBALS ['db']->query ( "SELECT attachurl FROM pw_attachs WHERE `tid`=" . S::sqlEscape ( $tid ) . " AND `pid`=0 AND `did`=0 AND `type`='img'" );
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			list ( $attachurl, $attachThumbUrl ) = $this->getCustomizedCommonService ()->getThumbAttach ( $rt ['attachurl'], false );
			$result ['img'] [] = array ('url' => $attachurl );
		}
		$result ['count'] = count ( $result ['img'] );
		return $this->buildResponse ( 0, $result );
	}
	
	function getToppedThreadByFid($fid, $offset, $limit) {
		list ( $fid, $offset, $limit ) = array (intval ( $fid ), intval ( $offset ), intval ( $limit ) );
		if ($fid <= 0)
			return $this->buildResponse ( THREAD_INVALID_PARAMS );
		$tmpResult = $GLOBALS ['db']->get_one ( "SELECT fd.topthreads,f.name as forumname FROM pw_forumdata fd LEFT JOIN pw_forums f USING(fid) WHERE fd.fid=" . S::sqlEscape ( $fid ) );
		if (! $tmpResult ['topthreads'])
			$this->buildResponse ( 0, array () );
		$tids = array_filter ( array_unique ( explode ( ',', $tmpResult ['topthreads'] ) ) );
		$result ['threads'] = $uids = array ();
		$result ['count'] = count ( $tids );
		$tmpTids = $offset ? array_slice ( $tids, array_search ( $offset, $tids ) + 1, $limit ) : array_slice ( $tids, 0, $limit );
		if (S::isArray ( $tmpTids )) {
			$query = $GLOBALS ['db']->query ( "SELECT tid,fid,author,authorid,subject,postdate,lastpost,hits,replies FROM pw_threads  WHERE `tid` IN (" . S::sqlImplode ( $tmpTids ) . ") AND fid!=0 AND ifcheck=1 ORDER BY lastpost DESC" );
			while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
				$rt ['forumname'] = strip_tags ( $tmpResult ['forumname'] );
				$rt ['icon'] = '';
				$uids [$rt ['tid']] = $rt ['authorid'];
				$result ['threads'] [$rt ['tid']] = $rt;
			}
			$result ['threads'] = $this->getCustomizedCommonService ()->buildMultiUserIcons ( $uids, $result ['threads'] );
		}
		return $this->buildResponse ( 0, $result );
	}
	
	function getThreadByFid($fid, $offset, $limit) {
		list ( $fid, $offset, $limit ) = array (intval ( $fid ), intval ( $offset ), intval ( $limit ) );
		if ($fid <= 0)
			return $this->buildResponse ( THREAD_INVALID_PARAMS );
		$count = $GLOBALS ['db']->get_value ( "SELECT COUNT(*) as count FROM pw_threads WHERE fid=" . S::sqlEscape ( $fid ) . " AND ifcheck=1 AND topped=0" );
		if ($count < 1)
			return $this->buildResponse ( 0, array () );
		$sqlLimit = $offset > 0 ? ' AND t.lastpost < ' . intval ( $offset ) : '';
		$query = $GLOBALS ['db']->query ( "SELECT t.tid,t.fid,t.author,t.authorid,t.subject,t.postdate,t.lastpost,f.name as forumname,t.hits,t.replies FROM pw_threads t LEFT JOIN pw_forums f USING(fid) WHERE t.fid=" . S::sqlEscape ( $fid ) . " $sqlLimit AND t.ifcheck=1 AND t.topped=0 ORDER BY t.lastpost DESC LIMIT " . intval ( $limit ) );
		$result = array ();
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$rt ['forumname'] = strip_tags ( $rt ['forumname'] );
			$rt ['icon'] = '';
			$uids [$rt ['tid']] = $rt ['authorid'];
			$result [$rt ['tid']] = $rt;
		}
		$result = $this->getCustomizedCommonService ()->buildMultiUserIcons ( $uids, $result );
		return $this->buildResponse ( 0, array ('count' => $count, 'threads' => $result ) );
	}
	
	function getAtThreadByUid($uid, $offset, $limit) {
		list ( $uid, $offset, $limit ) = array (intval ( $uid ), intval ( $offset ), intval ( $limit ) );
		if ($uid <= 0)
			return $this->buildResponse ( THREAD_INVALID_PARAMS );
		$result ['threads'] = $uids = $tids = array ();
		$result ['count'] = intval ( $GLOBALS ['db']->get_value ( "SELECT count(*) as count FROM pw_threads_at WHERE pid=0 AND uid=" . S::sqlEscape ( $uid ) ) );
		if ($result ['count'] > 0) {
			$query = $GLOBALS ['db']->query ( "SELECT tid FROM pw_threads_at WHERE pid=0 AND uid=" . S::sqlEscape ( $uid ) );
			while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
				$tids [] = $rt ['tid'];
			}
			if (S::isArray ( $tids )) {
				$sqlLimit = $offset > 0 ? ' AND t.tid < ' . intval ( $offset ) : '';
				$query = $GLOBALS ['db']->query ( "SELECT t.tid,t.fid,t.subject,t.author,t.authorid,t.postdate,f.name as forumname FROM pw_threads t LEFT JOIN pw_forums f ON t.fid=f.fid WHERE t.ifcheck=1 AND t.tid IN (" . S::sqlImplode ( $tids ) . " ) $sqlLimit ORDER BY t.postdate DESC LIMIT " . intval ( $limit ) );
				while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
					$rt ['forumname'] = strip_tags ( $rt ['forumname'] );
					$rt ['icon'] = '';
					$uids [$rt ['tid']] = $rt ['authorid'];
					$result ['threads'] [$rt ['tid']] = $rt;
				}
				$result ['threads'] = $this->getCustomizedCommonService ()->buildMultiUserIcons ( $uids, $result ['threads'] );
			}
		}
		return $this->buildResponse ( 0, $result );
	}
	
	function getThreadByTopic($topic, $offset, $limit) {
		$charset = ACloud_Sys_Core_Common::getGlobal ( 'g_charset' );
		list ( $topic, $offset, $limit ) = array (trim ( $topic ), intval ( $offset ), intval ( $limit ) );
		if (! $topic)
			return $this->buildResponse ( THREAD_INVALID_PARAMS );
		$result ['threads'] = $uids = $fids = array ();
		$result ['count'] = intval ( $GLOBALS ['db']->get_value ( "SELECT count(*) as count FROM pw_threads WHERE ifcheck =1 AND subject LIKE " . S::sqlEscape ( "%$topic%" ) ) );
		if ($result ['count'] > 0) {
			$sqlLimit = $offset > 0 ? ' AND t.tid < ' . intval ( $offset ) : '';
			$query = $GLOBALS ['db']->query ( "SELECT t.tid,t.fid,t.subject,t.author,t.authorid,t.postdate FROM pw_threads t WHERE t.ifcheck=1 AND subject LIKE " . S::sqlEscape ( "%$topic%" ) . " $sqlLimit ORDER BY t.postdate DESC LIMIT " . intval ( $limit ) );
			while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
				$rt ['icon'] = '';
				$fids [$rt ['tid']] = $rt ['fid'];
				$uids [$rt ['tid']] = $rt ['authorid'];
				$result ['threads'] [$rt ['tid']] = $rt;
			}
			$result ['threads'] = $this->getCustomizedCommonService ()->buildMultiUserIcons ( $uids, $result ['threads'] );
			$result ['threads'] = $this->buildMultiForumNames ( $fids, $result ['threads'] );
		}
		return $this->buildResponse ( 0, $result );
	}
	
	function postMobileThread($uid, $fid, $subject, $content, $mobileType) {
		list ( $code, $data ) = $this->postThread ( $uid, $fid, $subject, $content );
		if ($code)
			return $this->buildResponse ( $code, $data );
		$GLOBALS ['db']->query ( sprintf ( "UPDATE pw_threads SET frommob = %s WHERE tid = %s", intval ( $mobileType ), S::sqlEscape ( $data ['tid'] ) ) );
		return $this->buildResponse ( $code, $data );
	}
	
	function postThread($uid, $fid, $subject, $content) {
		global $winddb, $winduid, $windid, $groupid, $_G, $timestamp, $pwforum, $pwpost, $uploadcredit, $uploadmoney, $db_uploadfiletype, $_time;
		$timestamp = time ();
		$_time = array ('hours' => get_date ( $timestamp, 'G' ), 'day' => get_date ( $timestamp, 'j' ), 'week' => get_date ( $timestamp, 'w' ) );
		list ( $uid, $fid, $subject, $content ) = array (intval ( $uid ), intval ( $fid ), trim ( $subject ), trim ( $content ) );
		if ($uid < 1 || $fid < 1 || ! $subject || ! $content)
			return $this->buildResponse ( THREAD_INVALID_PARAMS );
		ACloud_Sys_Core_Common::setGlobal ( 'customized_current_uid', $uid );
		$user = $this->getCurrentUser ();
		if (! $user->isLogin ())
			return $this->buildResponse ( USER_NOT_LOGIN );
		if ($user->groupid == 6 || getstatus ( $user->info ['userstatus'], PW_USERSTATUS_BANUSER ))
			return $this->buildResponse ( THREAD_USER_NOT_RIGHT );
		$user->initRight ();
		$winduid = $user->uid;
		$groupid = $user->groupid;
		$windid = $user->username;
		$winddb = $user->info;
		$_G = $user->_G;
		if ($_G ['postlimit'] && $winddb ['todaypost'] >= $_G ['postlimit'])
			return $this->buildResponse ( POST_GP_LIMIT );
		L::loadClass ( 'forum', 'forum', false );
		$pwforum = new PwForum ( $fid );
		if (! $pwforum->isForum ())
			return $this->buildResponse ( THREAD_FORUM_NOT_EXIST );
		list ( $uploadcredit, $uploadmoney, , ) = explode ( "\t", $pwforum->forumset ['uploadset'] );
		L::loadClass ( 'post', 'forum', false );
		require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.bbscode.php';
		$pwpost = new PwPost ( $pwforum );
		$pwpost->errMode = true;
		$pwpost->forumcheck ();
		$pwpost->postcheck ();
		L::loadClass ( 'topicpost', 'forum', false );
		$topicpost = new topicPost ( $pwpost );
		$topicpost->check ();
		$postdata = new topicPostData ( $pwpost );
		$postdata->setWtype ( '', '', $pwforum->foruminfo ['t_type'], $pwforum->foruminfo ['topictype'] );
		$postdata->setTitle ( $subject );
		$postdata->setContent ( $content );
		$postdata->setConvert ( 1, 1 );
		$postdata->setTags ( '' );
		$postdata->setDigest ( '' );
		$postdata->setTopped ( '' );
		$postdata->setIfsign ( 1, 0 );
		if ($pwpost->errMsg && $msg = reset ( $pwpost->errMsg )) {
			return $this->buildResponse ( THREAD_SYSTEM_ERROR, $msg );
		}
		require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.attupload.php';
		if (PwUpload::getUploadNum ()) {
			$_G ['uploadtype'] && $db_uploadfiletype = $_G ['uploadtype'];
			$db_uploadfiletype = ! empty ( $db_uploadfiletype ) ? (is_array ( $db_uploadfiletype ) ? $db_uploadfiletype : unserialize ( $db_uploadfiletype )) : array ();
			$postdata->att = new AttUpload ( $user->uid );
			$return = $postdata->att->check ();
			if ($return) {
				$msginfo = getLangInfo ( 'msg', $return );
				return $this->buildResponse ( THREAD_USER_NOT_RIGHT );
			}
			list ( $windVersion ) = explode ( ',', WIND_VERSION );
			if ($windVersion && $windVersion < '8.5') {
				PwUpload::upload ( $postdata->att );
				$postdata->att->transfer ();
			}
		}
		$topicpost->execute ( $postdata );
		$tid = $topicpost->getNewId ();
		return $this->buildResponse ( 0, array ('tid' => $tid ) );
	}
	
	function buildMultiForumNames($fids, $data) {
		$result = array ();
		if (! S::isArray ( $fids ))
			return $result;
		$query = $GLOBALS ['db']->query ( "SELECT fid, name as forumname FROM pw_forums WHERE fid IN ( " . S::sqlImplode ( $fids ) . ")" );
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$result [$rt ['fid']] = $rt ['forumname'];
		}
		foreach ( $data as $key => &$value ) {
			$value ['forumname'] = isset ( $result [$value ['fid']] ) ? strip_tags ( $result [$value ['fid']] ) : '';
		}
		return $data;
	}
	
	function getThreadsByTids($tids) {
		if (! S::isArray ( $tids ))
			return array ();
		$result = array ();
		foreach ( $tids as $tid ) {
			$tmpResult = $this->_getThread ( $tid, true );
			$result [$tid] = array ('tid' => $tmpResult ['tid'], 'icon' => $this->getCustomizedCommonService ()->getUserIcon ( $tmpResult ['icon'] ), 'author' => $tmpResult ['author'], 'authorid' => $tmpResult ['authorid'], 'subject' => $tmpResult ['subject'], 'postdate' => $tmpResult ['postdate'], 'hits' => $tmpResult ['hits'], 'replies' => $tmpResult ['replies'] );
			$content = $this->getCustomizedCommonService ()->parsePostContent ( $tmpResult );
			$result [$tid] ['content'] = $this->getCustomizedCommonService ()->parseEmotionInContent ( $content );
		}
		return $result;
	}
	
	function getLatestThreadsByFids($fids, $offset, $limit) {
		list ( $offset, $limit ) = array (intval ( $offset ), intval ( $limit ) );
		if (! S::isArray ( $fids ))
			return $this->buildResponse ( 0, array () );
		$sqlLimit = $offset > 0 ? ' AND t.tid < ' . intval ( $offset ) : '';
		$result ['threads'] = $uids = array ();
		$result ['count'] = intval ( $GLOBALS ['db']->get_value ( "SELECT count(*) as count FROM pw_threads WHERE ifcheck=1 AND fid IN (" . S::sqlImplode ( $fids ) . " )" ) );
		if ($result ['count'] > 0) {
			$query = $GLOBALS ['db']->query ( "SELECT t.tid,t.fid,t.subject,t.author,t.authorid,t.postdate,f.name as forumname FROM pw_threads t LEFT JOIN pw_forums f ON t.fid=f.fid WHERE t.ifcheck=1 AND t.fid IN (" . S::sqlImplode ( $fids ) . " ) $sqlLimit ORDER BY t.postdate DESC LIMIT " . intval ( $limit ) );
			while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
				$rt ['forumname'] = strip_tags ( $rt ['forumname'] );
				$rt ['icon'] = '';
				$uids [$rt ['tid']] = $rt ['authorid'];
				$result ['threads'] [$rt ['tid']] = $rt;
			}
			$result ['threads'] = $this->getCustomizedCommonService ()->buildMultiUserIcons ( $uids, $result ['threads'] );
		}
		return $this->buildResponse ( 0, $result );
	}
	
	function getLatestThreadByUids($uids, $offset, $limit) {
		list ( $offset, $limit ) = array (intval ( $offset ), intval ( $limit ) );
		if (! S::isArray ( $uids ))
			return $this->buildResponse ( 0, array () );
		$sqlLimit = $offset > 0 ? ' AND t.tid < ' . intval ( $offset ) : '';
		$result ['threads'] = $tmpUids = array ();
		$result ['count'] = intval ( $GLOBALS ['db']->get_value ( "SELECT count(*) as count FROM pw_threads WHERE ifcheck=1 AND authorid IN (" . S::sqlImplode ( $uids ) . " )" ) );
		if ($result ['count'] > 0) {
			$query = $GLOBALS ['db']->query ( "SELECT t.tid,t.fid,t.subject,t.author,t.authorid,t.postdate,f.name as forumname FROM pw_threads t LEFT JOIN pw_forums f ON t.fid=f.fid WHERE t.ifcheck=1 AND t.authorid IN (" . S::sqlImplode ( $uids ) . " ) $sqlLimit ORDER BY t.postdate DESC LIMIT " . intval ( $limit ) );
			while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
				$rt ['forumname'] = strip_tags ( $rt ['forumname'] );
				$rt ['icon'] = '';
				$tmpUids [$rt ['tid']] = $rt ['authorid'];
				$result ['threads'] [$rt ['tid']] = $rt;
			}
			$result ['threads'] = $this->getCustomizedCommonService ()->buildMultiUserIcons ( $tmpUids, $result ['threads'] );
		}
		return $this->buildResponse ( 0, $result );
	}
	
	function _getThread($tid, $isDetailed = false) {
		if ($isDetailed) {
			$pw_tmsgs = GetTtable ( $tid );
			return $GLOBALS ['db']->get_one ( "SELECT t.*,m.uid,m.icon,m.groupid,m.userstatus,tm.* FROM pw_threads t LEFT JOIN pw_members m ON t.authorid=m.uid LEFT JOIN $pw_tmsgs tm ON t.tid=tm.tid WHERE t.tid=" . S::sqlEscape ( $tid ) . " AND t.ifcheck = 1 AND t.fid != 0" );
		} else {
			return $GLOBALS ['db']->get_one ( "SELECT * FROM pw_threads WHERE tid=" . S::sqlEscape ( $tid ) . " AND ifcheck = 1 AND fid != 0" );
		}
	}
	
	function _getFidsByShortCut($fields) {
		$fields = trim ( $fields );
		if ($fields == '')
			return array ();
		$tmpArr = explode ( ',', $fields );
		$fids = array ();
		foreach ( ( array ) $tmpArr as $v ) {
			(intval ( $v ) > 0) && $fids [] = intval ( $v );
		}
		return $fids;
	}
	
	function _isMyFavoredForEarly($tid, $threadData) {
		$user = $this->getCurrentUser ();
		if ($user->isLogin ()) {
			$favored = $GLOBALS ['db']->get_value ( "SELECT tids FROM pw_favors WHERE uid=" . pwEscape ( $user->uid ) );
			$threadData ['isFavored'] = strpos ( ',' . $favored . ',', ',' . $tid . ',' ) === false ? 0 : 1;
		}
		return $threadData;
	}
	
	function _isMyFavoredForAfter($tid, $threadData) {
		$user = $this->getCurrentUser ();
		if ($user->isLogin ()) {
			$collectionService = L::loadClass ( 'Collection', 'collection' );
			$favor = $collectionService->getByTypeAndTypeid ( $user->uid, 'postfavor', $tid );
			$threadData ['isFavored'] = $favor ? 1 : 0;
		}
		return $threadData;
	}
	
	function _getUDb($data) {
		return $data ['uid'] ? array ('groupid' => $data ['groupid'], 'userstatus' => $data ['userstatus'], 'uid' => $data ['uid'] ) : array ();
	}
}