<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.base.php';
class ACloud_Ver_Customized_Factory {
	
	var $service = array ();
	
	function getInstance() {
		static $instance = null;
		if (! is_null ( $instance ))
			return $instance;
		$instance = new ACloud_Ver_Customized_Factory ();
		return $instance;
	}
	
	function getVersionCustomizedThread() {
		if (! isset ( $this->service ['VersionCustomizedThread'] ) || ! $this->service ['VersionCustomizedThread']) {
			require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.thread.php';
			$this->service ['VersionCustomizedThread'] = new ACloud_Ver_Customized_Thread ();
		}
		return $this->service ['VersionCustomizedThread'];
	}
	
	function getVersionCustomizedPost() {
		if (! isset ( $this->service ['VersionCustomizedPost'] ) || ! $this->service ['VersionCustomizedPost']) {
			require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.post.php';
			$this->service ['VersionCustomizedPost'] = new ACloud_Ver_Customized_Post ();
		}
		return $this->service ['VersionCustomizedPost'];
	}
	
	function getVersionCustomizedUser() {
		if (! isset ( $this->service ['VersionCustomizedUser'] ) || ! $this->service ['VersionCustomizedUser']) {
			require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.user.php';
			$this->service ['VersionCustomizedUser'] = new ACloud_Ver_Customized_User ();
		}
		return $this->service ['VersionCustomizedUser'];
	}
	
	function getVersionCustomizedForum() {
		if (! isset ( $this->service ['VersionCustomizedForum'] ) || ! $this->service ['VersionCustomizedForum']) {
			require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.forum.php';
			$this->service ['VersionCustomizedForum'] = new ACloud_Ver_Customized_Forum ();
		}
		return $this->service ['VersionCustomizedForum'];
	}
	
	function getVersionCustomizedMessage() {
		if (! isset ( $this->service ['VersionCustomizedMessage'] ) || ! $this->service ['VersionCustomizedMessage']) {
			require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.message.php';
			$this->service ['VersionCustomizedMessage'] = new ACloud_Ver_Customized_Message ();
		}
		return $this->service ['VersionCustomizedMessage'];
	}
	
	function getVersionCustomizedFriend() {
		if (! isset ( $this->service ['VersionCustomizedFriend'] ) || ! $this->service ['VersionCustomizedFriend']) {
			require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.friend.php';
			$this->service ['VersionCustomizedFriend'] = new ACloud_Ver_Customized_Friend ();
		}
		return $this->service ['VersionCustomizedFriend'];
	}
	
	function getVersionCustomizedCommon() {
		if (! isset ( $this->service ['VersionCustomizedCommon'] ) || ! $this->service ['VersionCustomizedCommon']) {
			require_once ACLOUD_VERSION_PATH . '/customized/ver.customized.common.php';
			$this->service ['VersionCustomizedCommon'] = new ACloud_Ver_Customized_Common ();
		}
		return $this->service ['VersionCustomizedCommon'];
	}

}