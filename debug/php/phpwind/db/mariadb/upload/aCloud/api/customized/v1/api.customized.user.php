<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.factory.php';
class ACloud_Api_Customized_User {
	
	function getByUid($uid) {
		$customizedUser = $this->getVersionCustomizedUser ();
		return $customizedUser->getByUid ( $uid );
	}
	
	function getByName($username) {
		$customizedUser = $this->getVersionCustomizedUser ();
		return $customizedUser->getByName ( $username );
	}
	
	function updateIcon($uid) {
		$customizedUser = $this->getVersionCustomizedUser ();
		return $customizedUser->updateIcon ( $uid );
	}
	
	function getFavoritesForumByUid($uid) {
		$customizedUser = $this->getVersionCustomizedUser ();
		return $customizedUser->getFavoritesForumByUid ( $uid );
	}
	
	function addFavoritesForumByUid($uid, $fid) {
		$customizedUser = $this->getVersionCustomizedUser ();
		return $customizedUser->addFavoritesForumByUid ( $uid, $fid );
	}
	
	function deleteFavoritesForumByUid($uid, $fid) {
		$customizedUser = $this->getVersionCustomizedUser ();
		return $customizedUser->deleteFavoritesForumByUid ( $uid, $fid );
	}
	
	function userLogin($username, $password) {
		$customizedUser = $this->getVersionCustomizedUser ();
		return $customizedUser->userLogin ( $username, $password );
	}
	
	function userRegister($username, $password, $email) {
		$customizedUser = $this->getVersionCustomizedUser ();
		return $customizedUser->userRegister ( $username, $password, $email );
	}
	
	function updateEmail($uid, $email) {
		$customizedUser = $this->getVersionCustomizedUser ();
		return $customizedUser->updateEmail ( $uid, $email );
	}
	
	function getVersionCustomizedUser() {
		$customizedFactory = ACloud_Ver_Customized_Factory::getInstance ();
		return $customizedFactory->getVersionCustomizedUser ();
	}
}