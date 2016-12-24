<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_Sys_Router {
	
	function run() {
		list ( $method ) = ACloud_Sys_Core_S::gp ( array ('method' ) );
		$method = strtolower ( str_replace ( '.', '_', $method ) );
		$controlService = ACloud_Sys_Core_Common::loadSystemClass ( 'control', 'verify.service' );
		$control = in_array ( $method, array ('open_initkey', 'open_checkkey', 'apply_verify' ) ) ? true : $controlService->doubleControl ( $_POST );
		$output = ($control && $method && method_exists ( $this, $method )) ? $this->$method () : $this->sys_error ();
		print_r ( $output );
		exit ();
	}
	
	function sys_error() {
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'sys error' );
	}
	
	function config_addApp() {
		list ( $app_id, $app_name, $app_token ) = ACloud_Sys_Core_S::gp ( array ('app_id', 'app_name', 'app_token' ) );
		$initService = ACloud_Sys_Core_Common::loadSystemClass ( 'apps', 'config.service' );
		$fields = array ('app_id' => $app_id, 'app_name' => ACloud_Sys_Core_Common::convertFromUTF8 ( $app_name ), 'app_token' => $app_token );
		if (! ($app = $initService->addApp ( $fields )))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_addApp fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, 'config_addApp success' );
	}
	
	function config_updateApp() {
		list ( $app_id, $app_name, $app_token ) = ACloud_Sys_Core_S::gp ( array ('app_id', 'app_name', 'app_token' ) );
		$initService = ACloud_Sys_Core_Common::loadSystemClass ( 'apps', 'config.service' );
		$initService->updateApp ( array ('app_name' => ACloud_Sys_Core_Common::convertFromUTF8 ( $app_name ), 'app_token' => $app_token ), $app_id );
		$app = $initService->getApp ( $app_id );
		if (! $app || $app ['app_token'] != $app_token)
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_updateApp fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, 'config_updateApp success' );
	}
	
	function config_deleteApp() {
		list ( $app_id ) = ACloud_Sys_Core_S::gp ( array ('app_id' ) );
		$initService = ACloud_Sys_Core_Common::loadSystemClass ( 'apps', 'config.service' );
		$initService->deleteApp ( $app_id );
		$app = $initService->getApp ( $app_id );
		if ($app)
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_deleteApp fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, 'config_deleteApp success' );
	}
	
	function config_getApp() {
		list ( $app_id ) = ACloud_Sys_Core_S::gp ( array ('app_id' ) );
		$initService = ACloud_Sys_Core_Common::loadSystemClass ( 'apps', 'config.service' );
		$app = $initService->getApp ( $app_id );
		if (! $app)
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_getApp fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $app );
	}
	
	function config_getApps() {
		$initService = ACloud_Sys_Core_Common::loadSystemClass ( 'apps', 'config.service' );
		$apps = $initService->getApps ();
		if (! $apps)
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_getApps fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $apps );
	}
	
	function config_addApi() {
		list ( $name, $template, $argument, $argumentType, $fields, $status, $category, $createdTime, $modifiedTime ) = ACloud_Sys_Core_S::gp ( array ('name', 'template', 'argument', 'argumentType', 'fields', 'status', 'category', 'createdTime', 'modifiedTime' ) );
		$configApiService = ACloud_Sys_Core_Common::loadSystemClass ( 'apis', 'config.service' );
		$fields = array ('name' => $name, 'template' => $template, 'argument' => $argument, 'argument_type' => $argumentType, 'fields' => $fields, 'status' => $status, 'category' => $category, 'created_time' => $createdTime, 'modified_time' => $modifiedTime );
		if (! ($result = $configApiService->addApi ( $fields )))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_addApi fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, 'config_addApi success' );
	}
	
	function config_updateApi() {
		list ( $name, $template, $argument, $argumentType, $fields, $status, $category, $createdTime, $modifiedTime ) = ACloud_Sys_Core_S::gp ( array ('name', 'template', 'argument', 'argumentType', 'fields', 'status', 'category', 'createdTime', 'modifiedTime' ) );
		$configApiService = ACloud_Sys_Core_Common::loadSystemClass ( 'apis', 'config.service' );
		$fields = array ('template' => $template, 'argument' => $argument, 'argument_type' => $argumentType, 'fields' => $fields, 'status' => $status, 'category' => $category, 'created_time' => $createdTime, 'modified_time' => $modifiedTime );
		if (! ($result = $configApiService->updateApiConfigByApiName ( $name, $fields )))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_updateApi fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, 'config_updateApi success' );
	}
	
	function config_deleteApi() {
		list ( $name ) = ACloud_Sys_Core_S::gp ( array ('name' ) );
		$configApiService = ACloud_Sys_Core_Common::loadSystemClass ( 'apis', 'config.service' );
		if (! ($result = $configApiService->deleteApiConfigByApiName ( $name )))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_deleteApi fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, 'config_deleteApi success' );
	}
	
	function config_getApi() {
		list ( $name ) = ACloud_Sys_Core_S::gp ( array ('name' ) );
		$configApiService = ACloud_Sys_Core_Common::loadSystemClass ( 'apis', 'config.service' );
		$apis = $configApiService->getApiConfigByApiName ( $name );
		if (! $apis)
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_getApi fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $apis );
	}
	
	function config_getApis() {
		$configApiService = ACloud_Sys_Core_Common::loadSystemClass ( 'apis', 'config.service' );
		$apis = $configApiService->getApis ();
		if (! $apis)
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_getApis fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $apis );
	}
	
	function config_addTableSetting() {
		list ( $name, $status, $category, $primaryKey, $createdTime, $modifiedTime ) = ACloud_Sys_Core_S::gp ( array ('name', 'status', 'category', 'primaryKey', 'createdTime', 'modifiedTime' ) );
		$tableSettingsService = ACloud_Sys_Core_Common::loadSystemClass ( 'table.settings', 'config.service' );
		$fields = array ('name' => $name, 'status' => $status, 'category' => $category, 'primary_key' => $primaryKey, 'created_time' => $createdTime, 'modified_time' => $modifiedTime );
		if (! ($result = $tableSettingsService->addTableSetting ( $fields )))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_addTableSetting fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, 'config_addTableSetting success' );
	}
	
	function config_updateTableSetting() {
		list ( $name, $status, $category, $primaryKey, $createdTime, $modifiedTime ) = ACloud_Sys_Core_S::gp ( array ('name', 'status', 'category', 'primaryKey', 'createdTime', 'modifiedTime' ) );
		$tableSettingsService = ACloud_Sys_Core_Common::loadSystemClass ( 'table.settings', 'config.service' );
		$fields = array ('status' => $status, 'category' => $category, 'primary_key' => $primaryKey, 'created_time' => $createdTime, 'modified_time' => $modifiedTime );
		if (! ($result = $tableSettingsService->updateTableSettingByTableName ( $name, $fields )))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_updateTableSetting fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, 'config_updateTableSetting success' );
	}
	
	function config_deleteTableSetting() {
		list ( $name ) = ACloud_Sys_Core_S::gp ( array ('name' ) );
		$tableSettingsService = ACloud_Sys_Core_Common::loadSystemClass ( 'table.settings', 'config.service' );
		if (! ($result = $tableSettingsService->deleteTableSettingByTableName ( $name )))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_deleteTableSetting fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, 'config_deleteTableSetting success' );
	}
	
	function config_getTableSetting() {
		list ( $name ) = ACloud_Sys_Core_S::gp ( array ('name' ) );
		$tableSettingsService = ACloud_Sys_Core_Common::loadSystemClass ( 'table.settings', 'config.service' );
		$tableSetting = $tableSettingsService->getSettingByTableName ( $name );
		if (! $tableSetting)
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_getTableSetting fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $tableSetting );
	}
	
	function config_getTableSettings() {
		$tableSettingsService = ACloud_Sys_Core_Common::loadSystemClass ( 'table.settings', 'config.service' );
		$tableSettings = $tableSettingsService->getTableSettings ();
		if (! $tableSettings)
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_getTableSettings fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $tableSettings );
	}
	
	function config_setExtra() {
		list ( $ekey, $evalue, $etype ) = ACloud_Sys_Core_S::gp ( array ('ekey', 'evalue', 'etype' ) );
		$extrasService = ACloud_Sys_Core_Common::loadSystemClass ( 'extras', 'config.service' );
		if (! ($extra = $extrasService->setExtra ( $ekey, $evalue, $etype )))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_setExtra fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $extra );
	}
	
	function config_getExtra() {
		list ( $ekey ) = ACloud_Sys_Core_S::gp ( array ('ekey' ) );
		$extrasService = ACloud_Sys_Core_Common::loadSystemClass ( 'extras', 'config.service' );
		if (! ($evalue = $extrasService->getExtra ( $ekey )))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_getExtra fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $evalue );
	}
	
	function config_getExtras() {
		$extrasService = ACloud_Sys_Core_Common::loadSystemClass ( 'extras', 'config.service' );
		if (! ($extras = $extrasService->getExtras ()))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_getExtras fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $extras );
	}
	
	function config_addAppConfig() {
		list ( $app_id, $app_key, $app_value, $app_type ) = ACloud_Sys_Core_S::gp ( array ('app_id', 'app_key', 'app_value', 'app_type' ) );
		$appConfigService = ACloud_Sys_Core_Common::loadSystemClass ( 'app.configs', 'config.service' );
		if (! ($config = $appConfigService->addAppConfig ( array ('app_id' => $app_id, 'app_key' => $app_key, 'app_value' => $app_value, 'app_type' => $app_type ) )))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_addAppConfig fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $config );
	}
	
	function config_getAppConfig() {
		list ( $app_id, $app_key ) = ACloud_Sys_Core_S::gp ( array ('app_id', 'app_key' ) );
		$appConfigService = ACloud_Sys_Core_Common::loadSystemClass ( 'app.configs', 'config.service' );
		if (! ($config = $appConfigService->getAppConfig ( $app_id, $app_key )))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_addAppConfig fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $config );
	}
	
	function config_getAppConfigsByAppId() {
		list ( $app_id ) = ACloud_Sys_Core_S::gp ( array ('app_id' ) );
		$appConfigService = ACloud_Sys_Core_Common::loadSystemClass ( 'app.configs', 'config.service' );
		if (! ($configs = $appConfigService->getAppConfigsByAppId ( $app_id )))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_getAppConfigsByAppId fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $configs );
	}
	
	function config_updateAppConfig() {
		list ( $app_id, $app_key, $app_value, $app_type ) = ACloud_Sys_Core_S::gp ( array ('app_id', 'app_key', 'app_value', 'app_type' ) );
		$appConfigService = ACloud_Sys_Core_Common::loadSystemClass ( 'app.configs', 'config.service' );
		if (! ($config = $appConfigService->updateAppConfig ( $app_id, $app_key, $app_value, $app_type )))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_updateAppConfig fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $config );
	}
	
	function config_deleteAppConfig() {
		list ( $app_id, $app_key ) = ACloud_Sys_Core_S::gp ( array ('app_id', 'app_key' ) );
		$appConfigService = ACloud_Sys_Core_Common::loadSystemClass ( 'app.configs', 'config.service' );
		if (! ($config = $appConfigService->deleteAppConfig ( $app_id, $app_key )))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_deleteAppConfig fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $config );
	}
	
	function config_deleteAppConfigByAppId() {
		list ( $app_id ) = ACloud_Sys_Core_S::gp ( array ('app_id' ) );
		$appConfigService = ACloud_Sys_Core_Common::loadSystemClass ( 'app.configs', 'config.service' );
		if (! ($config = $appConfigService->deleteAppConfigByAppId ( $app_id )))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_deleteAppConfigByAppId fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $config );
	}
	
	function config_getAppConfigs() {
		$appConfigService = ACloud_Sys_Core_Common::loadSystemClass ( 'app.configs', 'config.service' );
		if (! ($configs = $appConfigService->getAppConfigs ()))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'config_getAppConfigs fail' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $configs );
	}
	
	function apply_verify() {
		$controlService = ACloud_Sys_Core_Common::loadSystemClass ( 'control', 'verify.service' );
		if (! $controlService->ipControl ())
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'open_initKey fail' );
		
		$applyService = ACloud_Sys_Core_Common::loadSystemClass ( 'apply', 'open.service' );
		$bool = $applyService->verifying ( $_POST );
		if (! $bool)
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'apply_verify fail ' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, 'apply_verify success ' );
	}
	
	function open_initKey() {
		$controlService = ACloud_Sys_Core_Common::loadSystemClass ( 'control', 'verify.service' );
		if (! $controlService->identifyControl ( array_merge ( $_GET, $_POST ) ))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'open_initKey fail' );
		
		$initService = ACloud_Sys_Core_Common::loadSystemClass ( 'init', 'open.service' );
		list ( $bool, $message ) = $initService->initKey ( array_merge ( $_GET, $_POST ) );
		if (! $bool)
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, $message );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, 'open_initKey success ' );
	}
	
	function open_checkKey() {
		$controlService = ACloud_Sys_Core_Common::loadSystemClass ( 'control', 'verify.service' );
		if (! $controlService->identifyControl ( array_merge ( $_GET, $_POST ) ))
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, 'open_initKey fail' );
		
		$initService = ACloud_Sys_Core_Common::loadSystemClass ( 'init', 'open.service' );
		list ( $bool, $message ) = $initService->checkKey ( array_merge ( $_GET, $_POST ) );
		if (! $bool)
			return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_FAIL, $message );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, 'open_checkKey success ' );
	}
	
	function env_checkFunctions() {
		$openService = ACloud_Sys_Core_Common::loadSystemClass ( 'env', 'open.service' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $openService->checkFunctions () );
	}
	
	function env_getNetWorkSpeed() {
		$openService = ACloud_Sys_Core_Common::loadSystemClass ( 'env', 'open.service' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $openService->getNetWorkSpeed () );
	}
	
	function env_getServerInfo() {
		$openService = ACloud_Sys_Core_Common::loadSystemClass ( 'env', 'open.service' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $openService->getServerInfo () );
	}
	
	function env_getFilesInfo() {
		$openService = ACloud_Sys_Core_Common::loadSystemClass ( 'env', 'open.service' );
		return ACloud_Sys_Core_Common::simpleResponse ( ACLOUD_HTTP_OK, $openService->getFilesInfo () );
	}
	
	function dataflow_crawlTable() {
		list ( $tableName, $page, $perpage ) = ACloud_Sys_Core_S::gp ( array ('tablename', 'page', 'perpage' ) );
		$dataFlowService = ACloud_Sys_Core_Common::loadSystemClass ( 'crawler', 'dataflow.service' );
		return $dataFlowService->crawlTable ( $tableName, $page, $perpage );
	}
	
	function dataflow_crawlTableMaxId() {
		list ( $tableName ) = ACloud_Sys_Core_S::gp ( array ('tablename' ) );
		$dataFlowService = ACloud_Sys_Core_Common::loadSystemClass ( 'crawler', 'dataflow.service' );
		return $dataFlowService->crawlTableMaxId ( $tableName );
	}
	
	function dataflow_crawlPostMaxId() {
		$dataFlowService = ACloud_Sys_Core_Common::loadSystemClass ( 'crawler', 'dataflow.service' );
		return $dataFlowService->crawlPostMaxId ();
	}
	
	function dataflow_crawlTableByIdRange() {
		list ( $tableName, $startId, $endId ) = ACloud_Sys_Core_S::gp ( array ('tablename', 'startid', 'endid' ) );
		$dataFlowService = ACloud_Sys_Core_Common::loadSystemClass ( 'crawler', 'dataflow.service' );
		return $dataFlowService->crawlTableByIdRange ( $startId, $endId, $tableName );
	}
	
	function dataflow_crawlThreadDelta() {
		list ( $startTime, $endTime, $page, $perpage ) = ACloud_Sys_Core_S::gp ( array ('starttime', 'endtime', 'page', 'perpage' ) );
		$dataFlowService = ACloud_Sys_Core_Common::loadSystemClass ( 'crawler', 'dataflow.service' );
		return $dataFlowService->crawlThreadDelta ( $startTime, $endTime, $page, $perpage );
	}
	
	function dataflow_crawlThreadDeltaCount() {
		list ( $startTime, $endTime ) = ACloud_Sys_Core_S::gp ( array ('starttime', 'endtime' ) );
		$dataFlowService = ACloud_Sys_Core_Common::loadSystemClass ( 'crawler', 'dataflow.service' );
		return $dataFlowService->crawlThreadDeltaCount ( $startTime, $endTime );
	}
	
	function dataflow_crawlSqlLog() {
		list ( $startTime, $endTime, $page, $perpage ) = ACloud_Sys_Core_S::gp ( array ('starttime', 'endtime', 'page', 'perpage' ) );
		$dataFlowService = ACloud_Sys_Core_Common::loadSystemClass ( 'crawler', 'dataflow.service' );
		return $dataFlowService->crawlSqlLog ( $startTime, $endTime, $page, $perpage );
	}
	
	function dataflow_crawlSqlLogCount() {
		list ( $startTime, $endTime ) = ACloud_Sys_Core_S::gp ( array ('starttime', 'endtime' ) );
		$dataFlowService = ACloud_Sys_Core_Common::loadSystemClass ( 'crawler', 'dataflow.service' );
		return $dataFlowService->crawlSqlLogCount ( $startTime, $endTime );
	}
	
	function dataflow_deleteSqlLog() {
		list ( $startTime, $endTime ) = ACloud_Sys_Core_S::gp ( array ('starttime', 'endtime' ) );
		$dataFlowService = ACloud_Sys_Core_Common::loadSystemClass ( 'crawler', 'dataflow.service' );
		return $dataFlowService->deleteSqlLog ( $startTime, $endTime );
	}
	
	function dataflow_crawlDeletedId() {
		list ( $type, $startId, $endId ) = ACloud_Sys_Core_S::gp ( array ('type', 'startid', 'endid' ) );
		$dataFlowService = ACloud_Sys_Core_Common::loadSystemClass ( 'crawler', 'dataflow.service' );
		return $dataFlowService->crawlDeletedId ( $type, $startId, $endId );
	}
	
	function dataflow_crawlThreadRange() {
		list ( $startId, $endId ) = ACloud_Sys_Core_S::gp ( array ('startid', 'endid' ) );
		$dataFlowService = ACloud_Sys_Core_Common::loadSystemClass ( 'crawler', 'dataflow.service' );
		return $dataFlowService->crawlThreadRange ( $startId, $endId );
	}
	
	function dataflow_crawlMemberRange() {
		list ( $startId, $endId ) = ACloud_Sys_Core_S::gp ( array ('startid', 'endid' ) );
		$dataFlowService = ACloud_Sys_Core_Common::loadSystemClass ( 'crawler', 'dataflow.service' );
		return $dataFlowService->crawlMemberRange ( $startId, $endId );
	}
	
	function dataflow_crawlPostRange() {
		list ( $startId, $endId ) = ACloud_Sys_Core_S::gp ( array ('startid', 'endid' ) );
		$dataFlowService = ACloud_Sys_Core_Common::loadSystemClass ( 'crawler', 'dataflow.service' );
		return $dataFlowService->crawlPostRange ( $startId, $endId );
	}
	
	function dataflow_crawlAttachRange() {
		list ( $startId, $endId ) = ACloud_Sys_Core_S::gp ( array ('startid', 'endid' ) );
		$dataFlowService = ACloud_Sys_Core_Common::loadSystemClass ( 'crawler', 'dataflow.service' );
		return $dataFlowService->crawlAttachRange ( $startId, $endId );
	}
	
	function dataflow_crawlForumRange() {
		list ( $startId, $endId ) = ACloud_Sys_Core_S::gp ( array ('startid', 'endid' ) );
		$dataFlowService = ACloud_Sys_Core_Common::loadSystemClass ( 'crawler', 'dataflow.service' );
		return $dataFlowService->crawlForumRange ( $startId, $endId );
	}
	
	function dataflow_crawlDiaryRange() {
		list ( $startId, $endId ) = ACloud_Sys_Core_S::gp ( array ('startid', 'endid' ) );
		$dataFlowService = ACloud_Sys_Core_Common::loadSystemClass ( 'crawler', 'dataflow.service' );
		return $dataFlowService->crawlDiaryRange ( $startId, $endId );
	}
	
	function dataflow_crawlColonyRange() {
		list ( $startId, $endId ) = ACloud_Sys_Core_S::gp ( array ('startid', 'endid' ) );
		$dataFlowService = ACloud_Sys_Core_Common::loadSystemClass ( 'crawler', 'dataflow.service' );
		return $dataFlowService->crawlColonyRange ( $startId, $endId );
	}
}