<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

define ( 'MESSAGE_INVALID_PARAMS', 601 );
define ( 'MESSAGE_UID_ERROR', 602 );
define ( 'MESSAGE_SEND_FAIL', 603 );
define ( 'MESSAGE_GROUP_FORBIDDEN', 604 );
define ( 'MESSAGE_NUM_REACHED', 605 );
class ACloud_Ver_Customized_Message extends ACloud_Ver_Customized_Base {
	
	function countUnreadMessage($uid) {
		$uid = intval ( $uid );
		if ($uid < 1)
			return $this->buildResponse ( MESSAGE_INVALID_PARAMS );
		$messageService = $this->getMessageService ();
		$count = $messageService->countMessagesNotRead ( $uid );
		return $this->buildResponse ( 0, array ('count' => intval ( $count ) ) );
	}
	
	function getMessageByUid($uid, $offset, $limit) {
		list ( $uid, $offset, $limit ) = array (intval ( $uid ), intval ( $offset ), intval ( $limit ) );
		if ($uid < 1)
			return $this->buildResponse ( MESSAGE_INVALID_PARAMS );
		$count = $GLOBALS ['db']->get_value ( "SELECT COUNT(*) as count FROM pw_ms_relations WHERE uid = " . S::sqlEscape ( $uid ) . " AND categoryid=1 AND isown=0" );
		if ($count < 1)
			return $this->buildResponse ( 0, array () );
		$result = array ();
		$sqlLimit = $offset ? ' AND rid < ' . S::sqlEscape ( $offset ) : '';
		$query = $GLOBALS ['db']->query ( "SELECT * FROM pw_ms_relations WHERE uid = " . S::sqlEscape ( $uid ) . " AND categoryid=1 AND isown=0 $sqlLimit ORDER BY rid DESC LIMIT " . intval ( $limit ) );
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$result [] = $rt;
		}
		$result = $this->build ( $result );
		$messages = array ();
		foreach ( $result as $value ) {
			list ( $value ['icon'] ) = showfacedesign ( $value ['icon'], 1, 's' );
			$messages [] = $this->messageMapper ( $value );
		}
		return $this->buildResponse ( 0, array ('count' => $count, 'messages' => $messages ) );
	}
	
	function getReplyThreadMessage($uid, $offset, $limit) {
		list ( $uid, $offset, $limit ) = array (intval ( $uid ), intval ( $offset ), intval ( $limit ) );
		if ($uid < 1)
			return $this->buildResponse ( MESSAGE_INVALID_PARAMS );
		$count = $GLOBALS ['db']->get_value ( "SELECT COUNT(*) as count FROM pw_ms_relations WHERE uid = " . S::sqlEscape ( $uid ) . " AND categoryid=1 AND typeid=105 AND isown=0" );
		if ($count < 1)
			return $this->buildResponse ( 0, array () );
		$sqlLimit = $offset ? ' AND rid < ' . S::sqlEscape ( $offset ) : '';
		$query = $GLOBALS ['db']->query ( "SELECT * FROM pw_ms_relations WHERE uid = " . S::sqlEscape ( $uid ) . " AND categoryid=1 AND typeid=105 AND isown=0 $sqlLimit ORDER BY rid DESC LIMIT " . intval ( $limit ) );
		while ( $rt = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$result [] = $rt;
		}
		$result = $this->build ( $result );
		$messages = array ();
		foreach ( $result as $value ) {
			list ( $value ['icon'] ) = showfacedesign ( $value ['icon'], 1, 's' );
			$messages [] = $this->messageMapper ( $value );
		}
		return $this->buildResponse ( 0, array ('count' => $count, 'messages' => $messages ) );
	}
	
	function sendMessage($fromUid, $toUid, $title, $content) {
		list ( $fromUid, $toUid, $title, $content ) = array (intval ( $fromUid ), intval ( $toUid ), trim ( $title ), trim ( $content ) );
		if ($fromUid < 1 || $toUid < 1 || ! $title || ! $content)
			return $this->buildResponse ( MESSAGE_INVALID_PARAMS );
		ACloud_Sys_Core_Common::setGlobal ( 'customized_current_uid', intval ( $fromUid ) );
		$user = $this->getCurrentUser ();
		if (! $user->isLogin ())
			return $this->buildResponse ( USER_NOT_LOGIN );
		$user->initRight ();
		$GLOBALS ['winduid'] = $user->uid;
		$GLOBALS ['windid'] = $user->username;
		$GLOBALS ['groupid'] = $user->groupid;
		$GLOBALS ['winddb'] = $user->info;
		$GLOBALS ['_G'] = $user->_G;
		$userService = L::loadClass ( 'UserService', 'user' );
		$receiveUserInfo = $userService->get ( $toUid );
		$messageInfo = array ('create_uid' => $fromUid, 'create_username' => $user->username, 'title' => $title, 'content' => $content );
		$messageService = $this->getMessageService ();
		if (! $user->_G ['allowmessege'])
			return $this->buildResponse ( MESSAGE_GROUP_FORBIDDEN, '你所在的用户组不能发送消息' );
		if (! $messageService->checkUserMessageLevle ( 'sms', 1 ))
			return $this->buildResponse ( MESSAGE_NUM_REACHED, '你已超过每日发送消息数或你的消息总数已满' );
		$mid = $messageService->sendMessage ( $fromUid, array ($receiveUserInfo ['username'] ), $messageInfo );
		return $this->buildResponse ( 0, array ('messageid' => $mid ) );
	}
	
	function replyMessage($messageId, $relationId, $uid, $content) {
		list ( $messageId, $relationId, $uid, $content ) = array (intval ( $messageId ), intval ( $relationId ), intval ( $uid ), trim ( $content ) );
		if ($messageId < 1 || $relationId < 1 || $uid < 1 || ! $content)
			return $this->buildResponse ( MESSAGE_INVALID_PARAMS );
		ACloud_Sys_Core_Common::setGlobal ( 'customized_current_uid', intval ( $uid ) );
		$user = $this->getCurrentUser ();
		if (! $user->isLogin ())
			return $this->buildResponse ( USER_NOT_LOGIN );
		$user->initRight ();
		$GLOBALS ['winduid'] = $user->uid;
		$GLOBALS ['windid'] = $user->username;
		$GLOBALS ['groupid'] = $user->groupid;
		$GLOBALS ['winddb'] = $user->info;
		$GLOBALS ['_G'] = $user->_G;
		$messageService = $this->getMessageService ();
		$message = $messageService->getMessage ( $messageId );
		$messageInfo = array ('create_uid' => $user->uid, 'create_username' => $user->username, 'title' => $message ['title'], 'content' => $content );
		$replyId = $messageService->sendReply ( $user->uid, $relationId, $messageId, $messageInfo );
		return $this->buildResponse ( 0, array ('replyid' => $replyId ) );
	}
	
	function getMessageAndReply($messageId, $relationId, $uid, $offset, $limit) {
		list ( $messageId, $relationId, $uid, $offset, $limit ) = array (intval ( $messageId ), intval ( $relationId ), intval ( $uid ), intval ( $offset ), intval ( $limit ) );
		if ($messageId < 1 || $relationId < 1 || $uid < 1)
			return $this->buildResponse ( MESSAGE_INVALID_PARAMS );
		$messageService = $this->getMessageService ();
		$messageService->readMessages ( $uid, $messageId );
		$relation = $GLOBALS ['db']->get_one ( "SELECT * FROM pw_ms_relations WHERE rid = " . S::sqlEscape ( $relationId ) . " LIMIT 1" );
		$message = $messageService->getMessage ( $messageId );
		if ($relation ['relation'] == 2) {
			$expand = (isset ( $message ['expand'] )) ? unserialize ( $message ['expand'] ) : array ();
			$message = $messageService->getMessage ( $expand ['parentid'] );
			$messageId = $message ['mid'];
		}
		$count = $GLOBALS ['db']->get_value ( "SELECT COUNT(*) as count FROM pw_ms_replies WHERE parentid = " . S::sqlEscape ( $messageId ) );
		if ($count < 1)
			return $this->buildResponse ( 0, array () );
		$result = $messages = array ();
		$sqlLimit = $offset > 0 ? ' AND id > ' . S::sqlEscape ( $offset ) : '';
		$query = $GLOBALS ['db']->query ( "SELECT * FROM pw_ms_replies WHERE parentid = " . S::sqlEscape ( $messageId ) . $sqlLimit . " ORDER BY created_time ASC LIMIT " . intval ( $limit ) );
		while ( $rs = $GLOBALS ['db']->fetch_array ( $query ) ) {
			$result [] = $rs;
		}
		$result = $this->buildUsersLists ( $result );
		foreach ( $result as $value ) {
			$value ['tid'] = $this->findTidFromContent ( $value );
			$value ['content'] = $this->clearThreadInfoInContent ( $value ['content'] );
			$messages [] = array ('title' => $value ['title'], 'tid' => $value ['tid'], 'uid' => $value ['uid'], 'messageid' => $value ['parentid'], 'username' => $value ['username'], 'icon' => str_replace ( '/middle/', '/small/', $value ['face'] ), 'postdate' => $value ['created_time'], 'content' => $value ['content'], 'status' => ($value ['status'] ? 0 : 1) );
		}
		return $this->buildResponse ( 0, array ('count' => $count, 'messages' => $messages ) );
	}
	
	function messageMapper($data) {
		
		$return = array ('messageid' => $data ['mid'], 'uid' => $data ['create_uid'], 'username' => $data ['create_username'], 'icon' => $data ['icon'], 'postdate' => $data ['created_time'] );
		isset ( $data ['title'] ) && $return ['title'] = $data ['title'];
		isset ( $data ['rid'] ) && $return ['relationid'] = $data ['rid'];
		isset ( $data ['status'] ) && $return ['status'] = $data ['status'] ? 0 : 1;
		$return ['tid'] = $this->findTidFromContent ( $data );
		$data ['content'] = $this->mShowFace ( $data ['content'] );
		$return ['content'] = $this->clearThreadInfoInContent ( $data ['content'] );
		return $return;
	}
	
	function findTidFromContent($data) {
		if (strpos ( $data ['content'], '查看主题' ) === false)
			return 0;
		preg_match ( '|<a href="http.*read.php\?tid=(\d+)"[^>]*>查看主题</a>|i', $data ['content'], $match );
		return $match [1];
	}
	
	function clearThreadInfoInContent($content) {
		$content = preg_replace ( '/\<a href="http:(.*)job\.php\?action\=topost&tid\=(\d*)&pid\=(\d*)"[^>]*>查看回复<\/a>/i', '', $content );
		$content = preg_replace ( '/\<a href="http:(.*)read\.php\?tid\=(\d*)"[^>]*>查看主题<\/a>/i', '', $content );
		return $content;
	}
	
	function mShowFace($content) {
		$content = preg_replace ( "/<img src\=\"[^>]+\/post\/smile\/([^>]+)\" \/>/eis", "UnMShowFace('\\1')", $content );
		$content = mShowface ( $content );
		return $content;
	}
	
	function build($relations, $category = false) {
		if (! $relations)
			return false;
		$messageIds = $tmpRelations = array ();
		foreach ( $relations as $r ) {
			($r ['mid']) ? $messageIds [] = $r ['mid'] : 0;
			$tmpRelations [$r ['rid']] = $r;
		}
		if (! $messageIds)
			return false;
		$messagesDao = L::loadDB ( 'ms_messages', 'message' );
		if (! ($messages = $messagesDao->getMessagesByMessageIds ( $messageIds ))) {
			return false;
		}
		$tmpMessages = $result = array ();
		foreach ( $messages as $m ) {
			$tmpMessages [$m ['mid']] = $m;
		}
		foreach ( $tmpRelations as $rid => $r ) {
			(isset ( $tmpMessages [$r ['mid']] )) ? $result [$rid] = $r + $tmpMessages [$r ['mid']] : 0;
		}
		return $this->buildUsersLists ( $result );
	}
	
	function buildUsersLists($arrays) {
		global $tpc_author;
		if (! $arrays)
			return false;
		$userIds = array ();
		foreach ( $arrays as $v ) {
			(0 < $v ['create_uid']) ? $userIds [] = $v ['create_uid'] : 0;
		}
		$tmp = $this->retrieveUsers ( $userIds );
		require_once (R_P . 'require/bbscode.php');
		$groupInfos = $tmpArrays = array ();
		foreach ( $arrays as $rid => $a ) {
			$a ['content'] = $this->stringReplace ( convert ( $a ['content'], $GLOBALS ['db_windpost'] ) );
			$a ['extra'] = ($a ['extra']) ? unserialize ( $a ['extra'] ) : '';
			$tmpArrays [$rid] = isset ( $tmp [$a ['create_uid']] ) ? $tmp [$a ['create_uid']] + $a : $a;
		
		}
		return $tmpArrays;
	}
	
	function retrieveUsers($userIds) {
		if (! $userIds)
			return array ();
		array_unique ( $userIds );
		$userService = L::loadClass ( 'UserService', 'user' );
		$members = $userService->getByUserIds ( $userIds );
		$tmp = array ();
		require_once (R_P . 'require/showimg.php');
		foreach ( $members as $member ) {
			list ( $member ['face'] ) = showfacedesign ( $member ['icon'], 1, 'm' );
			$tmp [$member ['uid']] = $member;
		}
		return $tmp;
	}
	
	function stringReplace($value) {
		return nl2br ( $value );
	}
	
	function getMessageService() {
		return L::loadClass ( "message", 'message' );
	}
}