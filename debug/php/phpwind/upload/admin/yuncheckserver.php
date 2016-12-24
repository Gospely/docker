<?php
require_once(R_P.'lib/cloudwind/cloudwind.class.php');
$_service = CloudWind::getPlatformCheckServerService ();
if ($_service->checkCloudWind () < 9) {
	ObHeader ( $admin_file . '?adminjob=yunbasic' );
}
CLOUDWIND_SECURITY_SERVICE::gp ( array ('step' ) );
if ($step == 'reset') {
	$bool = CloudWind::resetCloudwind();
	$message = $bool ? '重置云服务成功' : '重置云服务失败';
	Showmsg($message);
}
list ( $bbsname, $bbsurl, $bbsversion, $cloudversion ) = $_service->getSiteInfo ();
list ( $fsockopen, $parse_url, $isgethostbyname, $gethostbyname ) = $_service->getFunctionsInfo ();
list ( $searchHost, $searchIP, $searchPort, $searchPing ) = $_service->getSearchHostInfo ();
list ( $defendHost, $defendIp, $defendPort, $defendPing ) = $_service->getDefendHostInfo ();
$description = $_service->getBaseDescription ();
$showReset = true;
include PrintEot ( 'yuncheckserver' );