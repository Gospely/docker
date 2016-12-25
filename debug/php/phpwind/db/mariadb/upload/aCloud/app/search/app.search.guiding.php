<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloud_App_Search_Guiding {
	
	function execute() {
		list ( $a ) = ACloud_Sys_Core_S::gp ( array ('a' ) );
		$action = ($a) ? $a . "Action" : "searchAction";
		if ($action && method_exists ( $this, $action )) {
			$this->$action ();
		}
	}
	
	function searchAction() {
		require_once ACLOUD_PATH . '/app/search/app.search.define.php';
		$_Service = ACloud_Sys_Core_Common::loadSystemClass ( 'app.configs', 'config.service' );
		$appConfigs = ACloud_Sys_Core_Common::arrayCombination ( $_Service->getAppConfigsByAppId ( APP_SEARCH_APPID ), 'app_key', 'app_value' );
		if (! $appConfigs || ! isset ( $appConfigs ['search_isopen'] ) || ! $appConfigs ['search_isopen'])
			return true;
		list ( $keyword ) = ACloud_Sys_Core_S::gp ( array ('keyword' ) );
		if (! $keyword && (! isset ( $appConfigs ['search_iskeyword'] ) || ! $appConfigs ['search_iskeyword']))
			return true;
		if (isset ( $appConfigs ['search_domain'] ) && $appConfigs ['search_domain']) {
			header ( "Location:http://" . $appConfigs ['search_domain'] . "/?" . $this->getSearchData () );
			exit ();
		}
		if (isset ( $appConfigs ['search_unique'] ) && $appConfigs ['search_unique']) {
			print_r ( $this->getSearchPage ( '云搜索', 'http://' . APP_SEARCH_HOST . '/?' . $this->getSearchData ( array ('n' => $appConfigs ['search_unique'] ) ), ACloud_Sys_Core_Common::getGlobal ( 'g_charset', 'gbk' ) ) );
			exit ();
		}
		return true;
	}
	
	function getSearchData($params = array()) {
		list ( $keyword, $type, $fid, $username ) = ACloud_Sys_Core_S::gp ( array ("keyword", "type", "fid", "username" ) );
		$data = array ();
		$data ['k'] = $keyword;
		$data ['type'] = $type;
		$data ['fid'] = intval ( $fid );
		$data ['username'] = $username;
		$data ['charset'] = ACloud_Sys_Core_Common::getGlobal ( 'g_charset', 'gbk' );
		$data ['url'] = ACloud_Sys_Core_Common::getGlobal ( 'g_siteurl', $_SERVER ['SERVER_NAME'] );
		require_once ACLOUD_PATH . '/system/core/sys.core.http.php';
		return ACloud_Sys_Core_Http::httpBuildQuery ( array_merge ( $data, $params ) );
	}
	
	function getSearchPage($title, $url, $charset) {
		return <<<EOT
<!doctype html>
<html>
	<head>
		<meta charset="$charset">
		<title>$title</title>
		<style type="text/css">html,body{margin:0;padding:0;}</style>
	</head>
	<body>
		<iframe id="searchiframe" style="border:none;overflow:hidden;" width="100%" src="$url" frameborder="0" scrolling="no"></iframe>
	</body>
</html>
EOT;
	}
	
	function proxyAction() {
		print_r ( $this->getProxyPage ( ACloud_Sys_Core_Common::getGlobal ( 'g_charset', 'gbk' ) ) );
		exit ();
	}
	
	function getProxyPage($charset) {
		return <<<EOT
<!doctype html>
<html>
	<head>
		<meta charset="$charset">
	</head>
	<body>
	</body>
	<script type="text/javascript">
		(function(){
			var getObj=function(id,parent){
				return (parent?parent:document).getElementById(id);
		    }
			var currHash="";
			var pParentFrame =top.document;
			setInterval(function(){
				var locationUrlHash =location.hash;
				if(typeof locationUrlHash!="undefined"){
					if(locationUrlHash!=currHash){
						if(locationUrlHash.split("#")[1]){
							var size=locationUrlHash.split("#")[1];
							var w=size.split("|")[0];
							var h=size.split("|")[1];
							pParentFrame.getElementById("searchiframe").style.height=h+"px";
						}
						currHash=locationUrlHash;
					}
				}
			},100)
		})();
	</script>
</html>
EOT;
	}

}