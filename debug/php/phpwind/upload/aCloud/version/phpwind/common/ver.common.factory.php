<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_VERSION_PATH . '/common/ver.common.base.php';
class ACloud_Ver_Common_Factory {
	
	var $service = array ();
	
	function getInstance() {
		static $instance = null;
		if (! is_null ( $instance ))
			return $instance;
		$instance = new ACloud_Ver_Common_Factory ();
		return $instance;
	}
	
	function getVersionCommonPermissions() {
		if (! isset ( $this->service ['VersionCommonPermissions'] ) || ! $this->service ['VersionCommonPermissions']) {
			require_once ACLOUD_VERSION_PATH . '/common/ver.common.permissions.php';
			$this->service ['VersionCommonPermissions'] = new ACloud_Ver_Common_Permissions ();
		}
		return $this->service ['VersionCommonPermissions'];
	}
	
	function getVersionCommonSearch() {
		if (! isset ( $this->service ['VersionCommonSearch'] ) || ! $this->service ['VersionCommonSearch']) {
			require_once ACLOUD_VERSION_PATH . '/common/ver.common.search.php';
			$this->service ['VersionCommonSearch'] = new ACloud_Ver_Common_Search ();
		}
		return $this->service ['VersionCommonSearch'];
	}
	
	function getVersionCommonSite() {
		if (! isset ( $this->service ['VersionCommonSite'] ) || ! $this->service ['VersionCommonSite']) {
			require_once ACLOUD_VERSION_PATH . '/common/ver.common.site.php';
			$this->service ['VersionCommonSite'] = new ACloud_Ver_Common_Site ();
		}
		return $this->service ['VersionCommonSite'];
	}
	
	function getVersionCommonUser() {
		if (! isset ( $this->service ['VersionCommonUser'] ) || ! $this->service ['VersionCommonUser']) {
			require_once ACLOUD_VERSION_PATH . '/common/ver.common.user.php';
			$this->service ['VersionCommonUser'] = new ACloud_Ver_Common_User ();
		}
		return $this->service ['VersionCommonUser'];
	}
	
	function getVersionCommonThread() {
		if (! isset ( $this->service ['VersionCommonThread'] ) || ! $this->service ['VersionCommonThread']) {
			require_once ACLOUD_VERSION_PATH . '/common/ver.common.thread.php';
			$this->service ['VersionCommonThread'] = new ACloud_Ver_Common_Thread ();
		}
		return $this->service ['VersionCommonThread'];
	}
	
	function getVersionCommonPost() {
		if (! isset ( $this->service ['VersionCommonPost'] ) || ! $this->service ['VersionCommonPost']) {
			require_once ACLOUD_VERSION_PATH . '/common/ver.common.post.php';
			$this->service ['VersionCommonPost'] = new ACloud_Ver_Common_Post ();
		}
		return $this->service ['VersionCommonPost'];
	}
	
	function getVersionCommonAttach() {
		if (! isset ( $this->service ['VersionCommonAttach'] ) || ! $this->service ['VersionCommonAttach']) {
			require_once ACLOUD_VERSION_PATH . '/common/ver.common.attach.php';
			$this->service ['VersionCommonAttach'] = new ACloud_Ver_Common_Attach ();
		}
		return $this->service ['VersionCommonAttach'];
	}
	
	function getVersionCommonForum() {
		if (! isset ( $this->service ['VersionCommonForum'] ) || ! $this->service ['VersionCommonForum']) {
			require_once ACLOUD_VERSION_PATH . '/common/ver.common.forum.php';
			$this->service ['VersionCommonForum'] = new ACloud_Ver_Common_Forum ();
		}
		return $this->service ['VersionCommonForum'];
	}
	
	function getVersionCommonDiary() {
		if (! isset ( $this->service ['VersionCommonDiary'] ) || ! $this->service ['VersionCommonDiary']) {
			require_once ACLOUD_VERSION_PATH . '/common/ver.common.diary.php';
			$this->service ['VersionCommonDiary'] = new ACloud_Ver_Common_Diary ();
		}
		return $this->service ['VersionCommonDiary'];
	}
	
	function getVersionCommonColony() {
		if (! isset ( $this->service ['VersionCommonColony'] ) || ! $this->service ['VersionCommonColony']) {
			require_once ACLOUD_VERSION_PATH . '/common/ver.common.colony.php';
			$this->service ['VersionCommonColony'] = new ACloud_Ver_Common_Colony ();
		}
		return $this->service ['VersionCommonColony'];
	}
	
	function getVersionCommonUtility() {
		if (! isset ( $this->service ['VersionCommonUtility'] ) || ! $this->service ['VersionCommonUtility']) {
			require_once ACLOUD_VERSION_PATH . '/common/ver.common.utility.php';
			$this->service ['VersionCommonUtility'] = new ACloud_Ver_Common_Utility ();
		}
		return $this->service ['VersionCommonUtility'];
	}
}