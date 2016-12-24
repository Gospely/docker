<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Ver_Core_Reset {
	
	function execute() {
		$this->resetWind ();
	}
	
	function resetWind() {
		$fields = array ('db_yunsearch_search', 'db_yunsearch_hook', 'db_yun_hash', 'db_yunsearch_domain', 'db_yunsearch_isopen', 'db_yunsearch_unique', 'db_yundefend_shield', 'db_yundefend_shielduser', 'db_yundefend_shieldpost', 'db_yun_model', 'db_yun_expand' );
		foreach ( $fields as $field ) {
			$GLOBALS ['db']->query ( "DELETE FROM `pw_config` WHERE db_name = '{$field}'" );
		}
		$tables = array ('pw_log_aggregate', 'pw_log_attachs', 'pw_log_colonys', 'pw_log_diary', 'pw_log_forums', 'pw_log_members', 'pw_log_postdefend', 'pw_log_posts', 'pw_log_postverify', 'pw_log_setting', 'pw_log_threads', 'pw_log_userdefend', 'pw_log_weibos', 'pw_yun_setting' );
		foreach ( $tables as $table ) {
			$result = $GLOBALS ['db']->get_one ( "SHOW TABLES LIKE '{$table}'" );
			if ($result) {
				$GLOBALS ['db']->query ( "TRUNCATE TABLE `{$table}`" );
			}
		}
		P_unlink ( D_P . 'data/bbscache/cloudwind_logsettings.php' );
		P_unlink ( D_P . 'data/bbscache/cloudwind_settings.php' );
		P_unlink ( D_P . 'data/bbscache/cloudwind_postinfo.php' );
		require_once (R_P . 'admin/cache.php');
		updatecache_c ();
		return true;
	}
}