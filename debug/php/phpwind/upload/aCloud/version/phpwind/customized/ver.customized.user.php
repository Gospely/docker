<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

define ( 'USER_INVALID_PARAMS', 201 );
define ( 'USER_INVALID_USERNAME', 202 );
define ( 'USER_UPDATE_ERROR', 203 );
define ( 'USER_DELETE_ERROR', 204 );
define ( 'USER_NOT_EXISTS', 205 );
define ( 'USER_PWD_ERROR', 206 );
define ( 'USER_REGISTER_CLOSE', 207 );
define ( 'USER_REGISTER_SAME_USERNAME_PASSWORD', 208 );
define ( 'USER_REGISTER_FAIL', 209 );
define ( 'FORUM_FAVOR_MAX', 210 );
define ( 'FORUM_FAVOR_ALREADY', 211 );

class ACloud_Ver_Customized_User extends ACloud_Ver_Customized_Base {
	
	function getByUid($uid) {
		$uid = intval ( $uid );
		if ($uid < 1)
			return $this->buildResponse ( USER_INVALID_PARAMS );
		$userService = L::loadClass ( 'UserService', 'user' );
		$userInfo = $userService->get ( $uid, true, true, true );
		if (empty ( $userInfo ))
			return $this->buildResponse ( USER_NOT_EXISTS );
		$userInfo = $this->buildUserInfo ( $userInfo );
		$userInfo ['weibo'] = $this->getWeiboInfo ( $uid, $userInfo ['fans'], $userInfo ['follows'] );
		$userInfo = $this->filterField ( $userInfo );
		return $this->buildResponse ( 0, $userInfo );
	}
	
	function getByName($username) {
		$username = trim ( $username );
		if (empty ( $username ))
			return array ();
		$userService = L::loadClass ( 'UserService', 'user' );
		$userInfo = $userService->getByUserName ( $username, true, true, true );
		if (empty ( $userInfo ))
			return $this->buildResponse ( USER_NOT_EXISTS );
		$userInfo = $this->buildUserInfo ( $userInfo );
		$userInfo ['weibo'] = $this->getWeiboInfo ( $userInfo ['uid'], $userInfo ['fans'], $userInfo ['follows'] );
		$userInfo = $this->filterField ( $userInfo );
		return $this->buildResponse ( 0, $userInfo );
	}
	
	function updateIcon($uid) {
		global $atc_attachment_name, $db_ifftp;
		$uid = intval ( $uid );
		if ($uid < 1 || ! S::isArray ( $_FILES ))
			return $this->buildResponse ( USER_INVALID_PARAMS );
		ACloud_Sys_Core_Common::setGlobal ( 'customized_current_uid', $uid );
		$user = $this->getCurrentUser ();
		if (! $user->isLogin ())
			return $this->buildResponse ( USER_NOT_LOGIN );
		$ext = strtolower ( substr ( strrchr ( $_FILES ['icon'] ['name'], '.' ), 1 ) );
		L::loadClass ( 'faceupload', 'upload', false );
		$face = new FaceUpload ( $user->uid );
		$icondb = PwUpload::upload ( $face );
		require_once (R_P . 'require/showimg.php');
		$udir = str_pad ( substr ( $user->uid, - 2 ), 2, '0', STR_PAD_LEFT );
		if (! in_array ( strtolower ( $ext ), array ('gif', 'jpg', 'jpeg', 'png', 'bmp' ) ))
			return $this->buildResponse ( USER_UPLOAD_CONTENT_ERROR );
		$filename = "{$user->uid}.$ext";
		$sourceFilename = "{$user->uid}_tmp.$ext";
		$sourceDir = "upload/$udir/";
		$middleDir = "upload/middle/$udir/";
		$smallDir = "upload/small/$udir/";
		$img_w = $img_h = 0;
		$sourceFile = PwUpload::savePath ( $db_ifftp, $sourceFilename, $sourceDir );
		$middleFile = PwUpload::savePath ( $db_ifftp, $filename, $middleDir );
		PwUpload::createFolder ( dirname ( $middleFile ) );
		PwUpload::movefile ( $sourceFile, $middleFile );
		require_once R_P . 'require/imgfunc.php';
		if (! $img_size = GetImgSize ( $middleFile )) {
			P_unlink ( $middleFile );
			return $this->buildResponse ( USER_UPLOAD_CONTENT_ERROR );
		}
		
		list ( $img_w, $img_h ) = getimagesize ( $middleFile );
		$smallFile = PwUpload::savePath ( $db_ifftp, $filename, $smallDir );
		$s_ifthumb = 0;
		PwUpload::createFolder ( dirname ( $smallFile ) );
		if ($ext == 'gif') {
			L::loadClass ( 'gifdecoder', 'utility', false );
			L::loadClass ( 'gif', 'utility', false );
			$gifDecoder = new GIFDecoder ( $data );
			$frames = $gifDecoder->GIFGetFrames ();
			if (! empty ( $frames )) {
				foreach ( $frames as $key => $value ) {
					$frames [$key] = makeAvatarGifThumb ( $value, $img_w, $img_h, 48, 48 );
				}
				$anime = new GIFEncoder ( $frames, $gifDecoder->GIFGetDelays (), $gifDecoder->GIFGetLoop (), $gifDecoder->GIFGetDisposal (), $gifDecoder->GIFGetTransparentR (), $gifDecoder->GIFGetTransparentG (), $gifDecoder->GIFGetTransparentB (), 'bin' );
				$newGifData = $anime->getAnimation ();
				PwUpload::createFolder ( dirname ( $smallFile ) );
				writeover ( $smallFile, $newGifData );
				$s_ifthumb = 1;
			}
		} elseif (MakeThumb ( $middleFile, $smallFile, 48, 48 )) {
			$s_ifthumb = 1;
		}
		
		if ($db_ifftp) {
			PwUpload::movetoftp ( $middleFile, $middleDir . $filename );
			$s_ifthumb && PwUpload::movetoftp ( $smallFile, $smallDir . $filename );
		}
		$user_a = explode ( '|', $user->icon );
		$user_a [2] = $img_w;
		$user_a [3] = $img_h;
		$usericon = setIcon ( "$udir/{$user->uid}.$ext", 3, $user_a );
		
		$userService = L::loadClass ( 'UserService', 'user' ); /* @var $userService PW_UserService */
		$userService->update ( $user->uid, array ('icon' => $usericon ) );
		list ( $iconurl ) = showfacedesign ( $usericon, 1, 's' );
		return $this->buildResponse ( 0, array ('icon' => $iconurl ) );
	}
	
	function getFavoritesForumByUid($uid) {
		$uid = intval ( $uid );
		if ($uid < 1)
			return $this->buildResponse ( USER_INVALID_PARAMS );
		$userService = L::loadClass ( 'UserService', 'user' );
		$userInfo = $userService->get ( $uid );
		if (! $userInfo)
			return $this->buildResponse ( USER_NOT_EXISTS );
		if (! $userInfo ['shortcut'])
			return $this->buildResponse ( 0, array () );
		$favoriteFids = array_filter ( array_unique ( explode ( ',', $userInfo ['shortcut'] ) ) );
		if (! S::isArray ( $favoriteFids ))
			return $this->buildResponse ( 0, array () );
		$result = array ();
		$query = $GLOBALS ['db']->query ( "SELECT f.fid, f.name, fdata.tpost FROM pw_forums f LEFT JOIN pw_forumdata fdata ON f.fid = fdata.fid WHERE f.fid IN (" . S::sqlImplode ( $favoriteFids ) . ")" );
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$result [] = array ('fid' => intval ( $rt ['fid'] ), 'forumname' => strip_tags ( $rt ['name'] ), 'todaypost' => $rt ['tpost'] );
		}
		return $this->buildResponse ( 0, array ('forums' => $result, 'count' => count ( $result ) ) );
	}
	
	function addFavoritesForumByUid($uid, $fid) {
		list ( $uid, $fid ) = array (intval ( $uid ), intval ( $fid ) );
		if ($uid < 1 || $fid < 1)
			return $this->buildResponse ( USER_INVALID_PARAMS );
		ACloud_Sys_Core_Common::setGlobal ( 'customized_current_uid', intval ( $uid ) );
		$user = $this->getCurrentUser ();
		if (! $user->isLogin ())
			return $this->buildResponse ( USER_NOT_LOGIN );
		$myshortcut = explode ( ',', $user->info ['shortcut'] );
		foreach ( $myshortcut as $key => $value ) {
			if (! $value || ! is_numeric ( $value )) {
				unset ( $myshortcut [$key] );
			}
		}
		$myshortcut = array_unique ( $myshortcut );
		if (count ( $myshortcut ) >= 6)
			return $this->buildResponse ( FORUM_FAVOR_MAX );
		if (in_array ( $fid, $myshortcut ))
			return $this->buildResponse ( FORUM_FAVOR_ALREADY );
		$myshortcut [] = $fid;
		$count = count ( $myshortcut );
		$shortcut = ',' . implode ( ',', $myshortcut ) . ',';
		$shortcut .= "\t" . $user->info ['appshortcut'];
		$userService = L::loadClass ( 'UserService', 'user' );
		$userService->update ( $user->uid, array ('shortcut' => $shortcut ) );
		return $this->buildResponse ( 0, array ('count' => $count ) );
	}
	
	function deleteFavoritesForumByUid($uid, $fid) {
		list ( $uid, $fid ) = array (intval ( $uid ), intval ( $fid ) );
		if ($uid < 1 || $fid < 1)
			return $this->buildResponse ( USER_INVALID_PARAMS );
		ACloud_Sys_Core_Common::setGlobal ( 'customized_current_uid', intval ( $uid ) );
		$user = $this->getCurrentUser ();
		if (! $user->isLogin ())
			return $this->buildResponse ( USER_NOT_LOGIN );
		if (! trim ( $user->info ['shortcut'], ',' ))
			return $this->buildResponse ( 0, array ('count' => 0 ) );
		$shortcut = explode ( ',', $user->info ['shortcut'] );
		$shortcut = array_diff ( $shortcut, array ($fid, '' ) );
		$count = count ( $shortcut );
		$shortcut = ',' . implode ( ',', $shortcut ) . ",\t" . $user->info ['appshortcut'];
		$userService = L::loadClass ( 'UserService', 'user' );
		$userService->update ( $user->uid, array ('shortcut' => $shortcut ) );
		return $this->buildResponse ( 0, array ('count' => $count ) );
	}
	
	function userLogin($username, $password) {
		list ( $username, $password ) = array (trim ( $username ), trim ( $password ) );
		if (empty ( $username ) || empty ( $password ))
			return $this->buildResponse ( USER_INVALID_PARAMS );
		$userService = L::loadClass ( 'UserService', 'user' );
		$userInfo = $userService->getByUserName ( $username, true, true );
		if (empty ( $userInfo ))
			return $this->buildResponse ( USER_NOT_EXISTS );
		return $this->buildResponse ( 0, array ('uid' => ($userInfo ['password'] == $password ? $userInfo ['uid'] : 0) ) );
	}
	
	function userRegister($username, $password, $email) {
		list ( $username, $password, $email, $timestamp ) = array (trim ( $username ), trim ( $password ), trim ( $email ), time () );
		if (empty ( $username ) || empty ( $password ) || empty ( $email ))
			return $this->buildResponse ( USER_INVALID_PARAMS );
		$rgConfig = L::reg ();
		if ($rgConfig ['rg_allowregister'] == 0 || ($rgConfig ['rg_registertype'] == 1 && date ( 'j', $timestamp ) != $rgConfig ['rg_regmon']) || ($rgConfig ['rg_registertype'] == 2 && date ( 'w', $timestamp ) != $rgConfig ['rg_regweek']))
			return $this->buildResponse ( USER_REGISTER_CLOSE );
		if (L::reg ( 'rg_npdifferf' ) && $username == $password)
			return $this->buildResponse ( USER_REGISTER_SAME_USERNAME_PASSWORD );
		$register = L::loadClass ( 'Register', 'user' ); /* @var $register PW_Register */
		$register->setStatus ( 11 );
		$register->setName ( $username );
		$register->setPwd ( $password, $password );
		$register->setEmail ( $email );
		$register->execute ();
		list ( $uid, $rgyz, $safecv ) = $register->getRegUser ();
		if ($uid < 1)
			return $this->buildResponse ( USER_REGISTER_FAIL );
		if ($rgConfig ['rg_regsendmsg']) {
			$rgConfig ['rg_welcomemsg'] = str_replace ( '$rg_name', $username, $rgConfig ['rg_welcomemsg'] );
			M::sendNotice ( array ($uid ), array ('title' => "Welcome To[{$GLOBALS[db_bbsname]}]!", 'content' => $rgConfig ['rg_welcomemsg'] ) );
		}
		return $this->buildResponse ( 0, array ('uid' => $uid ) );
	}
	
	function updateEmail($uid, $email) {
		$uid = intval ( $uid );
		if ($uid < 1)
			return $this->buildResponse ( USER_INVALID_PARAMS );
		ACloud_Sys_Core_Common::setGlobal ( 'customized_current_uid', intval ( $uid ) );
		$user = $this->getCurrentUser ();
		if (! $user->isLogin ())
			return $this->buildResponse ( USER_NOT_LOGIN );
		$email = trim ( $email ) ? trim ( $email ) : '';
		$query = $GLOBALS ['db']->query ( "UPDATE pw_members SET email=" . S::sqlEscape ( $email ) . " WHERE uid = " . S::sqlEscape ( $uid ) );
		return $this->buildResponse ( 0, array ('uid' => $uid ) );
	}
	
	function buildUserInfo($userInfo) {
		$customizedCommonService = $this->getCustomizedCommonService ();
		$uid = $userInfo ['uid'];
		$userInfo ['icon'] = $customizedCommonService->getUserIcon ( $userInfo ['icon'] );
		$userInfo ['ltitle'] = $this->getUserLevel ( $userInfo );
		$userInfo ['isfollowed'] = $this->getIsFollowed ( $uid );
		$subjectNum = $this->getSubjectNum ( $uid );
		$userInfo ['replycount'] = $userInfo ['postnum'] - $subjectNum;
		$userInfo ['favorcount'] = $this->getFavorCount ( $uid, 'postfavor' );
		return $userInfo;
	}
	
	function getWeiboInfo($uid, $fans, $follows) {
		$weiboService = $this->getWeiboService ();
		$userWeiboInfo ['followedweibo'] = $weiboService->getUserAttentionWeibosNotMeCount ( $uid );
		$userWeiboInfo ['userweibo'] = $weiboService->getUserWeibosCount ( $uid );
		$userWeiboInfo ['referweibo'] = $weiboService->getRefersToMeCount ( $uid );
		$userWeiboInfo ['fans'] = $fans;
		$userWeiboInfo ['follows'] = $follows;
		return $userWeiboInfo;
	}
	
	function filterField($data) {
		$filedMap = array ('uid' => $data ['uid'], 'username' => $data ['username'], 'gender' => $data ['gender'], 'icon' => $data ['icon'], 'birthday' => $data ['bday'], 'honor' => $data ['honor'], 'postnum' => $data ['postnum'], 'ltitle' => $data ['ltitle'], 'isfollowed' => $data ['isfollowed'], 'replycount' => $data ['replycount'], 'favorcount' => $data ['favorcount'] );
		if ($data ['weibo'])
			$filedMap ['weibo'] = $data ['weibo'];
		return $filedMap;
	}
	
	function getUserLevel($userInfo) {
		include_once D_P . 'data/bbscache/level.php';
		$groupid = $userInfo ['groupid'] == '-1' ? $userInfo ['memberid'] : $userInfo ['groupid'];
		$_ltitle = $ltitle ? $ltitle [$groupid] : '';
		return $_ltitle;
	}
	
	function getIsFollowed($uid) {
		$user = $this->getCurrentUser ();
		$isfollowed = 0;
		if ($user->uid && $user->uid !== $uid) {
			$attentionService = $this->getAttentionService ();
			$isfollowed = $attentionService->isFollow ( $user->uid, $uid );
		}
		return $isfollowed;
	}
	
	function getSubjectNum($uid) {
		$count = $GLOBALS ['db']->get_value ( "SELECT COUNT(*) FROM pw_threads WHERE authorid=" . S::sqlEscape ( $uid ) );
		return $count;
	}
	
	function getFavorCount($uid, $type) {
		list ( $windVersion ) = explode ( ',', WIND_VERSION );
		if ($windVersion == '8.0') {
			$tids = $GLOBALS ['db']->get_value ( "SELECT tids FROM pw_favors WHERE uid=" . S::sqlEscape ( $uid ) );
			$tiddb = $this->getFavor ( $tids );
			$count = count ( $tiddb );
		} else {
			$collectionObj = L::loadClass ( 'Collection', 'collection' );
			$count = $collectionObj->countByUidAndType ( $uid, $type );
		}
		return $count;
	}
	
	function getFavor($tids) {
		$tids = explode ( '|', $tids );
		$tiddb = array ();
		foreach ( $tids as $key => $t ) {
			if (! $t)
				continue;
			$v = explode ( ',', $t );
			foreach ( $v as $k => $v1 ) {
				$tiddb [] = $v1;
			}
		}
		return $tiddb;
	}
	
	function getAttentionService() {
		return L::loadClass ( 'Attention', 'friend' );
	}
	
	function getWeiboService() {
		return L::loadClass ( 'weibo', 'sns' );
	}
}