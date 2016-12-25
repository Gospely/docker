<?php
! defined ( 'CLOUDWIND' ) && exit ( 'Forbidden' );
require_once CLOUDWIND . '/client/core/public/core.service.class.php';
class CloudWind_Platform_Reset extends CloudWind_Core_Service {
	
	function resetCloudWind() {
		$GLOBALS ['db']->query ( "DELETE FROM `pw_config` WHERE db_name = 'db_yunsearch_search'" );
		$GLOBALS ['db']->query ( "DELETE FROM `pw_config` WHERE db_name = 'db_yunsearch_hook'" );
		$GLOBALS ['db']->query ( "DELETE FROM `pw_config` WHERE db_name = 'db_yunsearch_search'" );
		$GLOBALS ['db']->query ( "DELETE FROM `pw_config` WHERE db_name = 'db_yunsearch_domain'" );
		$GLOBALS ['db']->query ( "DELETE FROM `pw_config` WHERE db_name = 'db_yunsearch_isopen'" );
		$GLOBALS ['db']->query ( "DELETE FROM `pw_config` WHERE db_name = 'db_yunsearch_unique'" );
		$GLOBALS ['db']->query ( "DELETE FROM `pw_config` WHERE db_name = 'db_yundefend_shield'" );
		$GLOBALS ['db']->query ( "DELETE FROM `pw_config` WHERE db_name = 'db_yundefend_shielduser'" );
		$GLOBALS ['db']->query ( "DELETE FROM `pw_config` WHERE db_name = 'db_yundefend_shieldpost'" );
		$GLOBALS ['db']->query ( "DELETE FROM `pw_config` WHERE db_name = 'db_yun_model'" );
		$GLOBALS ['db']->query ( "DELETE FROM `pw_config` WHERE db_name = 'db_yun_expand'" );
		$GLOBALS ['db']->query ( "TRUNCATE TABLE `pw_log_aggregate`" );
		$GLOBALS ['db']->query ( "TRUNCATE TABLE `pw_log_attachs`" );
		$GLOBALS ['db']->query ( "TRUNCATE TABLE `pw_log_colonys`" );
		$GLOBALS ['db']->query ( "TRUNCATE TABLE `pw_log_diary`" );
		$GLOBALS ['db']->query ( "TRUNCATE TABLE `pw_log_forums`" );
		$GLOBALS ['db']->query ( "TRUNCATE TABLE `pw_log_members`" );
		$GLOBALS ['db']->query ( "TRUNCATE TABLE `pw_log_postdefend`" );
		$GLOBALS ['db']->query ( "TRUNCATE TABLE `pw_log_posts`" );
		$GLOBALS ['db']->query ( "TRUNCATE TABLE `pw_log_postverify`" );
		$GLOBALS ['db']->query ( "TRUNCATE TABLE `pw_log_setting`" );
		$GLOBALS ['db']->query ( "TRUNCATE TABLE `pw_log_threads`" );
		$GLOBALS ['db']->query ( "TRUNCATE TABLE `pw_log_userdefend`" );
		$GLOBALS ['db']->query ( "TRUNCATE TABLE `pw_log_weibos`" );
		$GLOBALS ['db']->query ( "TRUNCATE TABLE `pw_yun_setting`" );
		
		P_unlink ( D_P . 'data/bbscache/cloudwind_logsettings.php' );
		P_unlink ( D_P . 'data/bbscache/cloudwind_settings.php' );
		P_unlink ( D_P . 'data/bbscache/cloudwind_postinfo.php' );
		
		require_once (R_P . 'admin/cache.php');
		updatecache_c ();
		
		return true;
	}
}