<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.factory.php';
class ACloud_Api_Customized_Message {
	
	function countUnreadMessage($uid) {
		$customizedMessage = $this->getVersionCustomizedMessage ();
		return $customizedMessage->countUnreadMessage ( $uid );
	}
	
	function getMessageByUid($uid, $offset, $limit) {
		$customizedMessage = $this->getVersionCustomizedMessage ();
		return $customizedMessage->getMessageByUid ( $uid, $offset, $limit );
	}
	
	function sendMessage($fromUid, $toUid, $title, $content) {
		$customizedMessage = $this->getVersionCustomizedMessage ();
		return $customizedMessage->sendMessage ( $fromUid, $toUid, $title, $content );
	}
	
	function replyMessage($messageid, $relationid, $uid, $content) {
		$customizedMessage = $this->getVersionCustomizedMessage ();
		return $customizedMessage->replyMessage ( $messageid, $relationid, $uid, $content );
	}
	
	function getMessageAndReply($messageid, $relationid, $uid, $offset, $limit) {
		$customizedMessage = $this->getVersionCustomizedMessage ();
		return $customizedMessage->getMessageAndReply ( $messageid, $relationid, $uid, $offset, $limit );
	}
	
	function getReplyThreadMessage($uid, $offset, $limit) {
		$customizedMessage = $this->getVersionCustomizedMessage ();
		return $customizedMessage->getReplyThreadMessage ( $uid, $offset, $limit );
	}
	
	function getVersionCustomizedMessage() {
		$customizedFactory = ACloud_Ver_Customized_Factory::getInstance ();
		return $customizedFactory->getVersionCustomizedMessage ();
	}

}