<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.factory.php';
class ACloud_Api_Customized_Thread {
	
	function getByTid($tid) {
		$versionThread = $this->getVersionCustomizedThread ();
		return $versionThread->getByTid ( $tid );
	}
	
	function getByUid($uid, $offset, $limit) {
		$versionThread = $this->getVersionCustomizedThread ();
		return $versionThread->getByUid ( $uid, $offset, $limit );
	}
	
	function getLatestThread($fids, $offset, $limit) {
		$versionThread = $this->getVersionCustomizedThread ();
		return $versionThread->getLatestThread ( $fids, $offset, $limit );
	}
	
	function getLatestThreadByFavoritesForum($uid, $offset, $limit) {
		$versionThread = $this->getVersionCustomizedThread ();
		return $versionThread->getLatestThreadByFavoritesForum ( $uid, $offset, $limit );
	}
	
	function getLatestThreadByFollowUser($uid, $offset, $limit) {
		$versionThread = $this->getVersionCustomizedThread ();
		return $versionThread->getLatestThreadByFollowUser ( $uid, $offset, $limit );
	}
	
	function getLatestImgThread($fids, $offset, $limit) {
		$versionThread = $this->getVersionCustomizedThread ();
		return $versionThread->getLatestImgThread ( $fids, $offset, $limit );
	}
	
	function getThreadImgs($tid) {
		$versionThread = $this->getVersionCustomizedThread ();
		return $versionThread->getThreadImgs ( $tid );
	}
	
	function getToppedThreadByFid($fid, $offset, $limit) {
		$versionThread = $this->getVersionCustomizedThread ();
		return $versionThread->getToppedThreadByFid ( $fid, $offset, $limit );
	}
	
	function getThreadByFid($fid, $offset, $limit) {
		$versionThread = $this->getVersionCustomizedThread ();
		return $versionThread->getThreadByFid ( $fid, $offset, $limit );
	}
	
	function getAtThreadByUid($uid, $offset, $limit) {
		$versionThread = $this->getVersionCustomizedThread ();
		return $versionThread->getAtThreadByUid ( $uid, $offset, $limit );
	}
	
	function getThreadByTopic($topic, $offset, $limit) {
		$versionThread = $this->getVersionCustomizedThread ();
		return $versionThread->getThreadByTopic ( $topic, $offset, $limit );
	}
	
	function postMobileThread($uid, $fid, $subject, $content, $mobileType) {
		$versionThread = $this->getVersionCustomizedThread ();
		return $versionThread->postMobileThread ( $uid, $fid, $subject, $content, $mobileType );
	}
	
	function postThread($uid, $fid, $subject, $content) {
		$customizedThread = $this->getVersionCustomizedThread ();
		return $customizedThread->postThread ( $uid, $fid, $subject, $content );
	}
	
	function getVersionCustomizedThread() {
		$customizedFactory = ACloud_Ver_Customized_Factory::getInstance ();
		return $customizedFactory->getVersionCustomizedThread ();
	}
}