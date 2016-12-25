<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.factory.php';
class ACloud_Api_Customized_Post {
	
	function getPost($tid, $sort, $offset, $limit) {
		$versionPost = $this->getVersionCustomizedPost ();
		return $versionPost->getPost ( $tid, $sort, $offset, $limit );
	}
	
	function getPostByUid($uid, $offset, $limit) {
		$versionPost = $this->getVersionCustomizedPost ();
		return $versionPost->getPostByUid ( $uid, $offset, $limit );
	}
	
	function getPostByTidAndUid($tid, $uid, $offset, $limit) {
		$versionPost = $this->getVersionCustomizedPost ();
		return $versionPost->getPostByTidAndUid ( $tid, $uid, $offset, $limit );
	}
	
	function sendMobilePost($tid, $uid, $title, $content, $mobileType) {
		$versionPost = $this->getVersionCustomizedPost ();
		return $versionPost->sendMobilePost ( $tid, $uid, $title, $content, $mobileType );
	}
	
	function sendPost($tid, $uid, $title, $content) {
		$versionPost = $this->getVersionCustomizedPost ();
		return $versionPost->sendPost ( $tid, $uid, $title, $content );
	}
	
	function getVersionCustomizedPost() {
		$customizedFactory = ACloud_Ver_Customized_Factory::getInstance ();
		return $customizedFactory->getVersionCustomizedPost ();
	}
}