<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloud_Ver_Customized_Common {
	
	function getReadRight($user) {
		global $isGM, $isBM, $admincheck, $pwPostHide, $pwSellHide, $pwEncodeHide, $manager, $groupid, $windid;
		$pwSystem = array ();
		if ($user->groupid != 'guest') {
			$isGM = S::inArray ( $windid, $manager );
			if (! $isGM) {
				$pwSystem = pwRights ();
				$pwPostHide = $pwSystem ['posthide'];
				$pwSellHide = $pwSystem ['sellhide'];
				$pwEncodeHide = $pwSystem ['encodehide'];
			} else {
				$pwPostHide = $pwSellHide = $pwEncodeHide = 1;
			}
		}
	}
	
	function getPtable($tid) {
		if ($GLOBALS ['db_plist'] && is_array ( $plistdb = $GLOBALS ['db_plist'] )) {
			$postTableId = $GLOBALS ['db']->get_value ( 'SELECT ptable FROM pw_threads WHERE tid=' . S::sqlEscape ( $tid, false ) );
			if (( int ) $postTableId > 0 && array_key_exists ( $postTableId, $plistdb )) {
				return 'pw_posts' . $postTableId;
			}
		}
		return 'pw_posts';
	}
	
	function parsePostContent($data) {
		list ( $content, $tpc_shield ) = $this->shieldContent ( $data ['content'], $data ['ifshield'], $data ['groupid'] );
		if ($tpc_shield)
			return $content;
		$content = stripcslashes ( $content );
		if (strpos ( $content, "[quote]" ) !== false && strpos ( $content, "[/quote]" ) !== false) {
			$content = preg_replace_callback ( '/\[quote\](.*?)\[\/quote\]/is', array ($this, 'parseQuote' ), $content );
		}
		
		$content = preg_replace ( array ("/\n/is", "/\r\n/is" ), "<br />", $content );
		require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.bbscode.php';
		$content = convert ( $content, $GLOBALS ['db_windpost'] );
		return $content;
	}
	
	function parseEmotionInContent($content) {
		return mShowface ( $content );
	}
	
	function parseImgInContent($data) {
		list ( $content, $tpc_shield ) = $this->shieldContent ( $data ['content'], $data ['ifshield'], $data ['groupid'] );
		if ($tpc_shield)
			return array ();
		list ( $content, $result ) = array (stripcslashes ( $content ), array () );
		if (strpos ( $content, "[img]" ) !== false && strpos ( $content, "[/img]" ) !== false) {
			preg_match_all ( '/\[img\](https?)?(' . $GLOBALS ['db_attachname'] . '\/)?([^\[]+?)\[\/img\]/is', $content, $matches, PREG_SET_ORDER );
			foreach ( $matches as $value ) {
				$result [] = $this->parseContentImg ( $value );
			}
		}
		return $result;
	}
	
	function shieldContent($content, $ifshield, $groupid) {
		global $db_shield;
		$tpc_shield = 0;
		if ($ifshield || $groupid == 6 && $db_shield) {
			if ($ifshield == 2) {
				$content = '<blockquote>该主题已被删除!<\blockquote>';
			} else {
				$content = $ifshield ? '<blockquote>该主题已被管理员屏蔽!<\blockquote>' : '<blockquote>用户被禁言,该主题自动屏蔽!<\blockquote>';
			}
			$tpc_shield = 1;
		}
		return array ($content, $tpc_shield );
	}
	
	function parseContentImg($merged) {
		if ($merged [1]) {
			return array ('attachurl' => $merged [1] . $merged [3], 'attachthumburl' => $merged [1] . $merged [3] );
		}
		list ( $attachurl, $attachThumbUrl ) = $this->getThumbAttach ( $merged [3] );
		list ( $db_thumbw, $db_thumbh ) = explode ( "\t", $GLOBALS ['db_athumbsize'] );
		return array ('attachurl' => $attachurl, 'attachthumburl' => $attachThumbUrl );
	}
	
	function parseImg($merged) {
		$return = '<img';
		if ($merged [1]) {
			$return .= " src='$merged[1]$merged[3]' thumb='' where='-1'";
		} else {
			list ( $attachurl, $attachThumbUrl ) = $this->getThumbAttach ( $merged [3] );
			list ( $db_thumbw, $db_thumbh ) = explode ( "\t", $GLOBALS ['db_athumbsize'] );
			$return .= " src='$attachurl' thumb='$attachThumbUrl'";
		}
		return $return . ' />';
	}
	
	function parseQuote($merged) {
		$return = '';
		if (is_array ( $merged ) && $merged [0])
			$return = preg_replace ( '/\[url=([^\[\s]+?)(\,(1)\/?)?\](.+?)\[\/url\]/is', '', $merged [0] );
		return $return;
	}
	
	function getThumbAttach($attachurl, $ifthumb = false) {
		global $attachpath, $attachdir, $db_ftpweb;
		$a_url = geturl ( $attachurl, 'show', $ifthumb );
		list ( $wind_version ) = explode ( ',', WIND_VERSION );
		if ($wind_version >= 8.0) {
			$thumburl = $this->getMiniUrl ( $attachurl, $ifthumb, $a_url [1] );
		} else {
			$thumburl = $a_url [0];
		}
		if ($a_url [1] == 'Local') {
			$sourceurl = $attachpath . '/' . $attachurl;
		} elseif ($a_url [1] == 'Ftp') {
			$sourceurl = $db_ftpweb . '/' . $attachurl;
		}
		return array ($sourceurl, $thumburl );
	}
	
	function getMiniUrl($path, $ifthumb, $where) {
		$dir = '';
		($ifthumb & 1) && $dir = 'thumb/';
		($ifthumb & 2) && $dir = 'thumb/mini/';
		if ($where == 'Local')
			return $GLOBALS ['attachpath'] . '/' . $dir . $path;
		if ($where == 'Ftp')
			return $GLOBALS ['db_ftpweb'] . '/' . $dir . $path;
		if (! is_array ( $GLOBALS ['attach_url'] ))
			return $GLOBALS ['attach_url'] . '/' . $dir . $path;
		return $GLOBALS ['attach_url'] [0] . '/' . $dir . $path;
	}
	
	function parseAttachList($attachList, &$content) {
		if (empty ( $attachList ))
			return '';
		$_src = $_thumb = '';
		list ( $db_thumbw, $db_thumbh ) = explode ( "\t", $GLOBALS ['db_athumbsize'] );
		foreach ( $attachList as $value ) {
			if ($value ['type'] != 'img') {
				$content = str_replace ( '[attachment=' . $value ['aid'] . ']', '', $content );
				continue;
			}
			list ( $attachurl, $attachThumbUrl ) = $this->getThumbAttach ( $value ['attachurl'], $value ['ifthumb'] );
			
			if (strpos ( $content, '[attachment=' . $value ['aid'] . ']' ) !== false) {
				$content = str_replace ( '[attachment=' . $value ['aid'] . ']', '<img src="' . $attachThumbUrl . '" thumb="' . $attachurl . '" />', $content );
				continue;
			}
			$attachurl && $_src .= $attachurl . ',';
			$attachThumbUrl && $_thumb .= $attachThumbUrl . ',';
		}
	}
	
	function clearAttachSign($attachList, &$content) {
		if (empty ( $attachList ))
			return '';
		$_src = $_thumb = '';
		foreach ( $attachList as $value ) {
			if ($value ['type'] != 'img') {
				$content = str_replace ( '[attachment=' . $value ['aid'] . ']', '', $content );
				continue;
			}
			if (strpos ( $content, '[attachment=' . $value ['aid'] . ']' ) !== false) {
				$content = str_replace ( '[attachment=' . $value ['aid'] . ']', '', $content );
				continue;
			}
		}
	}
	
	function clearHtmlTag($content, $except = '') {
		return strip_tags ( $content, $except );
	}
	
	function getAttachWithThumblist($fields) {
		if (! S::isArray ( $fields ))
			return array ();
		$data = array ();
		foreach ( $fields as $v ) {
			list ( $attachurl, $attachThumbUrl ) = $this->getThumbAttach ( $v ['attachurl'], $v ['ifthumb'] );
			$data [] = array ('attachurl' => $attachurl, 'attachthumburl' => $attachThumbUrl );
		}
		return $data;
	}
	
	function buildMultiUserIcons($uids, $result) {
		$tmpUids = array_filter ( array_unique ( $uids ) );
		if (! S::isArray ( $tmpUids ))
			return $result;
		$userInfo = array ();
		$userService = L::loadClass ( 'UserService', 'user' );
		$userInfos = $userService->getByUserIds ( $tmpUids );
		if (! S::isArray ( $userInfos ))
			return $result;
		foreach ( $uids as $key => $uid ) {
			$result [$key] ['icon'] = isset ( $userInfos [$uid] ) ? $this->getUserIcon ( $userInfos [$uid] ['icon'] ) : '';
		}
		return $result;
	}
	
	function getUserIcon($iconData) {
		require_once R_P . 'require/showimg.php';
		list ( $icon ) = showfacedesign ( $iconData, '1', 's' );
		return $icon;
	}
}