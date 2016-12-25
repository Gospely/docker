<?php
!function_exists('readover') && exit('Forbidden');

/****

@name:提前道具
@type:帖子类
@effect:可以把自己发表的帖子提前到帖子所在版块的第一页

****/

if($tooldb['type']!=1){
	Showmsg('tooluse_type_error');  // 判断道具类型是否设置错误
}
if($tpcdb['authorid'] != $winduid){
	Showmsg('tool_authorlimit');
}
//$db->update("UPDATE pw_threads SET lastpost=".S::sqlEscape($timestamp).",toolinfo=".S::sqlEscape($tooldb['name'],false)."WHERE tid=".S::sqlEscape($tid));
pwQuery::update('pw_threads', 'tid=:tid', array($tid), array('lastpost'=>$timestamp, 'toolinfo'=>$tooldb['name']));
# memcache refresh
$fid = $db->get_value("SELECT fid FROM pw_threads WHERE tid=".S::sqlEscape($tid));
// $threadList = L::loadClass("threadlist", 'forum');
// $threadList->updateThreadIdsByForumId($fid,$tid);
// $threads = L::loadClass('Threads', 'forum');
// $threads->delThreads($tid);

Perf::gatherInfo('changeThreadWithForumIds', array('fid'=>$fid));

require_once (R_P . 'require/updateforum.php');
delfcache($fid, $db_fcachenum);

$db->update("UPDATE pw_usertool SET nums=nums-1 WHERE uid=".S::sqlEscape($winduid)."AND toolid=".S::sqlEscape($toolid));
$logdata=array(
	'type'		=>	'use',
	'nums'		=>	'',
	'money'		=>	'',
	'descrip'	=>	'tool_7_descrip',
	'uid'		=>	$winduid,
	'username'	=>	$windid,
	'ip'		=>	$onlineip,
	'time'		=>	$timestamp,
	'toolname'	=>	$tooldb['name'],
	'subject'	=>	substrs($tpcdb['subject'],15),
	'tid'		=>	$tid,
);
writetoollog($logdata);
Showmsg('toolmsg_success');
?>