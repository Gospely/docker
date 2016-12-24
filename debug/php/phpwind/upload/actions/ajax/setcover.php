<?php
!defined('P_W') && exit('Forbidden');

S::gp(array('aid'), 'GP', 2);

empty($aid) && Showmsg('job_attach_error');
$attachService = L::loadClass('attachs','forum');
$attachInfo = $attachService->getByAid($aid);

if (!S::isArray($attachInfo) || $attachInfo['type'] != 'img' || !$attachInfo['tid']) Showmsg('job_attach_error');

$isGM = S::inArray($windid, $manager);
!$isGM && $groupid == 3 && $isGM = 1;
$adminCheck = ($attachInfo['uid'] == $winduid || $isGM) ? 1 : 0;

if (!$adminCheck) {
	Showmsg('没有权限设置封面！');
}

$tucoolService = L::loadClass('tucool','forum');

if ($tucoolService->setCover($attachInfo['tid'],$attachInfo['attachurl'],$attachInfo['ifthumb'])){
	echo "success";
	ajax_footer();
}
Showmsg('undefined_action');