<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

define ( 'THREAD_INVALID_PARAMS', 301 );
define ( 'THREAD_USER_NOT_RIGHT', 302 );
define ( 'THREAD_FORUM_NOT_EXIST', 303 );
define ( 'THREAD_USER_NOT_EXIST', 304 );
define ( 'THREAD_ID_NOT_ILLEGAL', 305 );
define ( 'THREAD_EDIT_TIME_LIMIT', 306 );
define ( 'THREAD_USER_NOT_HTML_RIGHT', 307 );
define ( 'THREAD_SYSTEM_ERROR', 308 );
define ( 'THREAD_FAVOR_MAX', 309 );
define ( 'THREAD_FAVOR_ALREADY', 310 );
define ( 'THREAD_NOT_EXIST', 312 );
define ( 'THREAD_LOCKED', 500 );
define ( 'POST_GP_LIMIT', 314 );
define ( 'THREAD_ALLOW_READ', 315 );

class ACloud_Ver_Customized_Post extends ACloud_Ver_Customized_Base {
	
	function getPost($tid, $sort, $offset, $limit) {
		global $db_postmin, $db_postmax, $foruminfo, $fid, $pwforum;
		list ( $tid, $offset, $limit ) = array (intval ( $tid ), intval ( $offset ), intval ( $limit ) );
		if ($tid < 1)
			return $this->buildResponse ( THREAD_INVALID_PARAMS );
		$threadInfo = $GLOBALS ['db']->get_one ( "SELECT * FROM pw_threads WHERE tid=" . S::sqlEscape ( $tid ) . " AND ifcheck = 1 AND fid != 0" );
		if (! S::isArray ( $threadInfo ))
			return $this->buildResponse ( THREAD_NOT_EXIST );
		$user = $this->getCurrentUser ();
		$user->init ( array ('reply' ) );
		$user->initRight ();
		$this->getCustomizedCommonService ()->getReadRight ( $user );
		L::loadClass ( 'forum', 'forum', false );
		$pwforum = new PwForum ( $threadInfo ['fid'] );
		if (! $pwforum->isForum ( true ))
			return $this->buildResponse ( THREAD_FORUM_NOT_EXIST );
		$fid = $pwforum->fid;
		$foruminfo = $pwforum->foruminfo;
		$pwforum->forumcheck ( $user->info, $user->groupid );
		$pwforum->creditcheck ( $user->info, $user->groupid );
		if (! $user->allowcheck ( $pwforum->foruminfo ['allowread'] ) && ! $pwforum->isBM ( $user->username ))
			return $this->buildResponse ( THREAD_ALLOW_READ );
		if ($threadInfo ['ifcheck'] == 0 && $user->username != $threadInfo ['author'] && ! $user->getSystemRight ( 'viewcheck' ))
			return $this->buildResponse ( THREAD_READ_CHECK );
		if ($threadInfo ['locked'] % 3 == 2 && ! $user->getSystemRight ( 'viewclose' ))
			return $this->buildResponse ( THREAD_READ_LOCKED );
		$return = $_pids = array ();
		$pw_posts = $this->getCustomizedCommonService ()->getPtable ( $tid );
		$return ['count'] = $GLOBALS ['db']->get_value ( "SELECT count(*) FROM $pw_posts WHERE tid=" . S::sqlEscape ( $tid ) . " AND ifcheck='1'" );
		$comments = $this->_getreplys ( $tid, $offset, $limit, $sort );
		foreach ( $comments as $key => $value ) {
			$value ['aid'] && $_pids [$value ['pid']] = $value ['pid'];
		}
		$_attachList = array ();
		if ($_pids) {
			$query = $GLOBALS ['db']->query ( 'SELECT * FROM pw_attachs WHERE tid=' . pwEscape ( $tid ) . " AND pid IN (" . pwImplode ( $_pids ) . ")" );
			while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
				$_attachList [$rt ['pid']] [] = $rt;
			}
		}
		$return ['posts'] = array ();
		foreach ( $comments as $key => $value ) {
			$value ['content'] = $this->getCustomizedCommonService ()->clearHtmlTag ( $value ['content'], '<br>' );
			$value ['content'] = $this->getCustomizedCommonService ()->parseEmotionInContent ( $value ['content'] );
			$value ['attachlist'] = array_merge ( $value ['attachlist'], $this->getCustomizedCommonService ()->getAttachWithThumblist ( $_attachList [$value ['pid']] ) );
			$this->getCustomizedCommonService ()->clearAttachSign ( $_attachList [$value ['pid']], &$value ['content'] );
			$return ['posts'] [] = $value;
		}
		return $this->buildResponse ( 0, $return );
	}
	
	function getPostByUid($uid, $offset, $limit) {
		list ( $uid, $offset, $limit ) = array (intval ( $uid ), intval ( $offset ), intval ( $limit ) );
		if ($uid < 1)
			return $this->buildResponse ( THREAD_INVALID_PARAMS );
		$userService = L::loadClass ( 'UserService', 'user' );
		$userInfo = $userService->get ( $uid );
		if (! S::isArray ( $userInfo ))
			return $this->buildResponse ( THREAD_USER_NOT_EXIST );
		! isset ( $ptable ) && $ptable = $GLOBALS ['db_ptable'];
		$pw_posts = GetPtable ( $ptable );
		$_comments = $_result = $aids = $tids = array ();
		$count = $GLOBALS ['db']->get_value ( "SELECT COUNT(*) AS count FROM $pw_posts p WHERE p.authorid=" . S::sqlEscape ( $uid ) );
		$_result ['count'] = $count;
		$sqlLimit = $offset > 0 ? " AND p.pid < " . intval ( $offset ) : '';
		$sql = "SELECT p.pid,p.tid,p.postdate,p.author,p.authorid,p.subject,p.content,p.aid FROM " . $pw_posts . " p WHERE p.authorid=" . pwEscape ( $uid ) . $sqlLimit . ' ORDER BY p.postdate DESC LIMIT ' . $limit;
		$query = $GLOBALS ['db']->query ( $sql );
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$tids [] = $rt ['tid'];
			$rt ['aid'] && $aids [$rt ['pid']] = $rt ['pid'];
			$_comments [$rt ['pid']] = array ('pid' => $rt ['pid'], 'tid' => $rt ['tid'], 'author' => $rt ['author'], 'authorid' => $rt ['authorid'], 'subject' => $rt ['subject'], 'postdate' => $rt ['postdate'] );
			$_comments [$rt ['pid']] ['icon'] = $this->getCustomizedCommonService ()->getUserIcon ( $userInfo ['icon'] );
			$_comments [$rt ['pid']] ['attachlist'] = $this->getCustomizedCommonService ()->parseImgInContent ( $rt );
			$_comments [$rt ['pid']] ['content'] = $this->getCustomizedCommonService ()->parsePostContent ( $rt );
		}
		$_attachList = array ();
		if ($aids) {
			$query = $GLOBALS ['db']->query ( "SELECT * FROM pw_attachs WHERE pid IN (" . pwImplode ( $aids ) . ")" );
			while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
				$_attachList [$rt ['pid']] [] = $rt;
			}
		}
		$_threadsList = $this->getThreadsByTids ( $tids );
		$_result ['posts'] = array ();
		foreach ( $_comments as $key => $value ) {
			isset ( $_threadsList [$value ['tid']] ) && $value ['threadsubject'] = $_threadsList [$value ['tid']] ['subject'];
			$value ['content'] = $this->getCustomizedCommonService ()->clearHtmlTag ( $value ['content'], '<br>' );
			$value ['content'] = $this->getCustomizedCommonService ()->parseEmotionInContent ( $value ['content'] );
			$value ['attachlist'] = array_merge ( $value ['attachlist'], $this->getCustomizedCommonService ()->getAttachWithThumblist ( $_attachList [$value ['pid']] ) );
			$this->getCustomizedCommonService ()->clearAttachSign ( $_attachList [$value ['pid']], &$value ['content'] );
			$_result ['posts'] [] = $value;
		}
		return $this->buildResponse ( 0, $_result );
	}
	
	function getPostByTidAndUid($tid, $uid, $offset, $limit) {
		list ( $tid, $uid, $offset, $limit ) = array (intval ( $tid ), intval ( $uid ), intval ( $offset ), intval ( $limit ) );
		if ($uid < 1 || $tid < 1)
			return $this->buildResponse ( THREAD_INVALID_PARAMS );
		$userService = L::loadClass ( 'UserService', 'user' );
		$userInfo = $userService->get ( $uid );
		if (! S::isArray ( $userInfo ))
			return $this->buildResponse ( THREAD_USER_NOT_EXIST );
		$postTable = $this->getCustomizedCommonService ()->getPtable ( $tid );
		$_comments = $_result = $aids = array ();
		$count = $GLOBALS ['db']->get_value ( "SELECT COUNT(*) AS count FROM $postTable p WHERE p.tid=" . S::sqlEscape ( $tid ) . " AND p.authorid=" . S::sqlEscape ( $uid ) );
		$_result ['count'] = $count;
		if ($_result ['count'] > 0) {
			$sql = "SELECT p.pid,p.tid,p.postdate,p.author,p.authorid,p.subject,p.content,p.aid FROM " . $postTable . " p WHERE p.tid=" . S::sqlEscape ( $tid ) . " AND p.authorid=" . pwEscape ( $uid ) . ' ORDER BY p.postdate DESC ' . S::sqlLimit ( $offset, $limit );
			$query = $GLOBALS ['db']->query ( $sql );
			while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
				$rt ['aid'] && $aids [$rt ['pid']] = $rt ['pid'];
				$_comments [$rt ['pid']] = array ('pid' => $rt ['pid'], 'tid' => $rt ['tid'], 'author' => $rt ['author'], 'authorid' => $rt ['authorid'], 'subject' => $rt ['subject'], 'postdate' => $rt ['postdate'] );
				$_comments [$rt ['pid']] ['icon'] = $this->getCustomizedCommonService ()->getUserIcon ( $userInfo ['icon'] );
				$_comments [$rt ['pid']] ['attachlist'] = $this->getCustomizedCommonService ()->parseImgInContent ( $rt );
				$_comments [$rt ['pid']] ['content'] = $this->getCustomizedCommonService ()->parsePostContent ( $rt );
			}
		}
		$_attachList = array ();
		if ($aids) {
			$query = $GLOBALS ['db']->query ( "SELECT * FROM pw_attachs WHERE pid IN (" . pwImplode ( $aids ) . ")" );
			while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
				$_attachList [$rt ['pid']] [] = $rt;
			}
		}
		$_result ['posts'] = array ();
		foreach ( $_comments as $key => $value ) {
			$value ['content'] = $this->getCustomizedCommonService ()->clearHtmlTag ( $value ['content'], '<br>' );
			$value ['content'] = $this->getCustomizedCommonService ()->parseEmotionInContent ( $value ['content'] );
			$value ['attachlist'] = array_merge ( $value ['attachlist'], $this->getCustomizedCommonService ()->getAttachWithThumblist ( $_attachList [$value ['pid']] ) );
			$this->getCustomizedCommonService ()->clearAttachSign ( $_attachList [$value ['pid']], &$value ['content'] );
			$_result ['posts'] [] = $value;
		}
		return $this->buildResponse ( 0, $_result );
	}
	
	function sendMobilePost($tid, $uid, $title, $content, $mobileType) {
		list ( $code, $data ) = $this->sendPost ( $tid, $uid, $title, $content );
		if ($code)
			return $this->buildResponse ( $code, $data );
		$postTable = GetPtable ( 'N', $tid );
		$GLOBALS ['db']->query ( sprintf ( "UPDATE %s SET frommob = %s WHERE pid = %s", S::sqlMetadata ( $postTable ), intval ( $mobileType ), S::sqlEscape ( $data ['pid'] ) ) );
		return $this->buildResponse ( $code, $data );
	}
	
	function sendPost($tid, $uid, $title, $content) {
		global $winddb, $winduid, $windid, $groupid, $fid, $timestamp, $pwforum, $pwpost, $_G, $db_uploadfiletype, $uploadcredit, $uploadmoney, $manager, $isBM, $_time;
		$timestamp = time ();
		$_time = array ('hours' => get_date ( $timestamp, 'G' ), 'day' => get_date ( $timestamp, 'j' ), 'week' => get_date ( $timestamp, 'w' ) );
		list ( $uid, $tid, $title, $content ) = array (intval ( $uid ), intval ( $tid ), trim ( $title ), trim ( $content ) );
		if ($uid < 1 || $tid < 1 || ! $content)
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
		$tpcarray = $GLOBALS ['db']->get_one ( "SELECT t.tid,t.fid,t.locked,t.ifcheck,t.author,t.authorid,t.postdate,t.lastpost,t.ifmail,t.special,t.subject,t.type,t.ifshield,t.anonymous,t.ptable,t.replies,t.tpcstatus FROM pw_threads t WHERE t.tid=" . pwEscape ( $tid ) );
		L::loadClass ( 'forum', 'forum', false );
		$pwforum = new PwForum ( $tpcarray ['fid'] );
		if (! $pwforum->isForum ())
			return $this->buildResponse ( THREAD_FORUM_NOT_EXIST );
		$fid = $tpcarray ['fid'];
		$isBM = $pwforum->isBM ( $windid );
		$isGM = S::inArray ( $windid, $manager );
		if (! $isGM && $tpcarray ['locked'] % 3 != 0 && ! pwRights ( $isBM, 'replylock' ))
			return $this->buildResponse ( THREAD_LOCKED );
		L::loadClass ( 'post', 'forum', false );
		require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.bbscode.php';
		$pwpost = new PwPost ( $pwforum );
		$pwpost->errMode = true;
		$pwpost->forumcheck ();
		$pwpost->postcheck ();
		L::loadClass ( 'replypost', 'forum', false );
		$replypost = new replyPost ( $pwpost );
		$replypost->setTpc ( $tpcarray );
		$replypost->check ();
		$pw_posts = GetPtable ( $replypost->tpcArr ['ptable'] );
		$postdata = new replyPostData ( $pwpost );
		$postdata->setTitle ( $title );
		$postdata->setContent ( $content );
		$postdata->conentCheck ();
		if ($pwpost->errMsg && $msg = reset ( $pwpost->errMsg ))
			return $this->buildResponse ( THREAD_SYSTEM_ERROR, $msg );
		require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.attupload.php';
		if (PwUpload::getUploadNum ()) {
			$_G ['uploadtype'] && $db_uploadfiletype = $_G ['uploadtype'];
			$db_uploadfiletype = ! empty ( $db_uploadfiletype ) ? (is_array ( $db_uploadfiletype ) ? $db_uploadfiletype : unserialize ( $db_uploadfiletype )) : array ();
			$postdata->att = new AttUpload ( $user->uid );
			$return = $postdata->att->check ();
			if ($return) {
				$msginfo = getLangInfo ( 'msg', $return );
				return $this->errMessage ( THREAD_USER_NOT_RIGHT, $msginfo );
			}
			list ( $windVersion ) = explode ( ',', WIND_VERSION );
			if ($windVersion && $windVersion < '8.5') {
				$postdata->att->transfer ();
				PwUpload::upload ( $postdata->att );
			}
		}
		$replypost->execute ( $postdata );
		$pid = $replypost->getNewId ();
		return $this->buildResponse ( 0, array ('pid' => $pid ) );
	}
	
	function _getreplys($tid, $offset, $limit, $order = 'asc') {
		global $db_windpost, $pwforum;
		require_once R_P . 'require/showimg.php';
		$pw_posts = $this->getCustomizedCommonService ()->getPtable ( $tid );
		$array = array ();
		$order = strtolower ( $order );
		! in_array ( $order, array ('asc', 'desc' ) ) && $order = 'asc';
		$direct = $order == 'asc' ? '>' : '<';
		$sqlLimit = $offset > 0 ? " AND p.pid $direct " . intval ( $offset ) : '';
		$query = $GLOBALS ['db']->query ( "SELECT p.*,m.uid,m.icon,m.groupid,m.userstatus FROM $pw_posts p LEFT JOIN pw_members m ON p.authorid=m.uid WHERE p.tid=" . S::sqlEscape ( $tid ) . " AND p.ifcheck='1' $sqlLimit ORDER BY p.postdate $order LIMIT " . intval ( $limit ) );
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			list ( $_icon ) = showfacedesign ( $rt ['icon'], '1', 's' );
			$udb [$rt ['uid']] = array ('uid' => $rt ['uid'], 'userstatus' => $rt ['userstatus'], 'groupid' => $rt ['groupid'] );
			$array [$rt ['pid']] = array ('pid' => $rt ['pid'], 'aid' => $rt ['aid'], 'tid' => $rt ['tid'], 'author' => $rt ['author'], 'authorid' => $rt ['authorid'], 'icon' => $_icon, 'postdate' => $rt ['postdate'], 'subject' => $rt ['subject'], 'content' => $rt ['content'] );
		}
		$bandb = $pwforum->forumBan ( $udb );
		foreach ( $array as $key => $value ) {
			$GLOBALS ['tpc_buy'] = $value ['buy'];
			$GLOBALS ['tpc_author'] = $value ['author'];
			$GLOBALS ['tpc_pid'] = $value ['pid'];
			isset ( $bandb [$value ['authorid']] ) && $value ['groupid'] = 6;
			$attachlist = $this->getCustomizedCommonService ()->parseImgInContent ( $value );
			$content = $this->getCustomizedCommonService ()->parsePostContent ( $value );
			$content = $this->getCustomizedCommonService ()->clearHtmlTag ( $content, '<br>' );
			$array [$key] ['content'] = $content;
			$array [$key] ['attachlist'] = $attachlist;
		}
		return $array;
	}
	
	function getThreadsByTids($tids) {
		if (! S::isArray ( $tids ))
			return array ();
		$result = array ();
		foreach ( $tids as $tid ) {
			$tmpResult = $this->_getThread ( $tid, true );
			$result [$tid] = array ('subject' => $tmpResult ['subject'] );
		}
		return $result;
	}
	
	function _getThread($tid, $isDetailed = false) {
		if ($isDetailed) {
			$pw_tmsgs = GetTtable ( $tid );
			return $GLOBALS ['db']->get_one ( "SELECT t.*,m.uid,m.icon,m.groupid,m.userstatus,tm.* FROM pw_threads t LEFT JOIN pw_members m ON t.authorid=m.uid LEFT JOIN $pw_tmsgs tm ON t.tid=tm.tid WHERE t.tid=" . S::sqlEscape ( $tid ) . " AND t.ifcheck = 1 AND t.fid != 0" );
		} else {
			return $GLOBALS ['db']->get_one ( "SELECT * FROM pw_threads WHERE tid=" . S::sqlEscape ( $tid ) . " AND ifcheck = 1 AND fid != 0" );
		}
	}
	
	function _cleanThreadinfo($threadinfo) {
		$unset = array ('titlefont', 'toolinfo', 'toolfield', 'ifupload', 'activeid', 'ifmail', 'ifmark', 'ifreward', 'ifshield', 'ifsale', 'anonymous', 'dig', 'fight', 'ptable', 'ifmagic', 'ifhide', 'inspect', 'digests', 'xigests', 'xig', 'tpcstatus', 'modelid', 'shares', 'topreplays', 'aid', 'userip', 'ifsign', 'buy', 'ipfrom', 'alterinfo', 'remindinfo', 'tags', 'ifconvert', 'ifwordsfb', 'form', 'c_from', 'magic', 'overprint' );
		foreach ( $unset as $key => $value ) {
			unset ( $threadinfo [$value] );
		}
		return $threadinfo;
	}
}