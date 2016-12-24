<?php
define('SCR','read');
require_once('global.php');
L::loadClass('forum', 'forum', false);
require_once(R_P.'require/bbscode.php');
//* include_once pwCache::getPath(D_P.'data/bbscache/cache_read.php',true);
pwCache::getData(D_P.'data/bbscache/cache_read.php');
S::gp(array('tid'));

if (Perf::checkMemcache()) {
	$_cacheService = Perf::getCacheService();
	$_thread = $_cacheService->get('thread_tid_' . $tid);
	$_thread && $_tmsg = $_cacheService->get('thread_tmsg_tid_' . $tid);
	$read = ($_thread && $_tmsg) ? array_merge($_thread, $_tmsg) : false;
	if (!$read) {
		$_cacheService = Perf::gatherCache('pw_threads');
		$read = ($page>1) ? $_cacheService->getThreadByThreadId($tid) : $_cacheService->getThreadAndTmsgByThreadId($tid);	
	}
} else {
	$read = $db->get_one("SELECT t.* ,tm.* FROM pw_threads t LEFT JOIN ".S::sqlMetadata(GetTtable($tid))." tm ON t.tid=tm.tid WHERE t.tid=" . S::sqlEscape($tid));
}
!$read && Showmsg('illegal_tid');
$postdate = get_date($read['postdate'],'Y-m-d');
list($fid,$ptable,$ifcheck,$openIndex,$topped_count,$subject,$authorid,$author) = array($read['fid'],$read['ptable'],$read['ifcheck'],getstatus($read['tpcstatus'], 2),$read['topreplays'],$read['subject'],$read['authorid'],$read['author']);
$pw_posts = GetPtable($ptable);

$pwforum = new PwForum($fid);
if (!$pwforum->isForum()) {
	Showmsg('data_error');
}
$foruminfo =& $pwforum->foruminfo;
$forumset =& $pwforum->forumset;

if (!S::inArray($windid, $manager)) {
	$pwforum->forumcheck($winddb, $groupid);
}

if (!$foruminfo['allowvisit'] && $_G['allowread']==0 && $_COOKIE) {
	Showmsg('read_group_right');
}

/**************************************/

//帖子浏览及管理权限
$isGM = $isBM = $admincheck = $managecheck = $pwPostHide = $pwSellHide = $pwEncodeHide = 0;
$pwSystem = array();
if ($groupid != 'guest') {
	$isGM = S::inArray($windid,$manager);
	$isBM = $pwforum->isBM($windid);
	$admincheck = ($isGM || $isBM) ? 1 : 0;
	if (!$isGM) {#非创始人权限获取
		$pwSystem = pwRights($isBM);
		if ($pwSystem && ($pwSystem['tpccheck'] || $pwSystem['digestadmin'] || $pwSystem['lockadmin'] || $pwSystem['pushadmin'] || $pwSystem['coloradmin'] || $pwSystem['downadmin'] || $pwSystem['delatc'] || $pwSystem['moveatc'] || $pwSystem['copyatc'] || $pwSystem['topped'] || $pwSystem['unite'] || $pwSystem['pingcp'] || $pwSystem['areapush'] || $pwSystem['split'])) {
			$managecheck = 1;
		}
		$pwPostHide = $pwSystem['posthide'];
		$pwSellHide = $pwSystem['sellhide'];
		$pwEncodeHide = $pwSystem['encodehide'];
	} else {
		$managecheck = $pwPostHide = $pwSellHide = $pwEncodeHide = 1;
	}
}

//版块查看权限
if ($foruminfo['allowread'] && !$admincheck && !allowcheck($foruminfo['allowread'],$groupid,$winddb['groups'])) {
	Showmsg('forum_read_right');
}
if (!$admincheck) {
	$pwforum->creditcheck($winddb, $groupid);#积分限制浏览
	$pwforum->sellcheck($winduid);#出售版块
}
if ($read['ifcheck'] == 0 && !$isGM && $windid != $read['author'] && !$pwSystem['viewcheck']) {
	Showmsg('read_check');
}
if ($read['locked']%3==2 && !$isGM && !$pwSystem['viewclose']) {
	Showmsg('read_locked');
}
unset($S_sql,$J_sql,$foruminfo['forumset']);

//来自群组的帖子
if ($colony && (!$colony['ifopen'] && !$admincheck && (!$colony['ifcyer'] || $colony['ifadmin'] == -1))) {
	Showmsg('该群组话题内容仅对成员开放!');
}
//是否图酷、是否允许浏览
$isTucool = $forumset['iftucool'] && getstatus($read['tpcstatus'], 5);
$ptable = $read['ptable'];
$ifhide = ($read['ifhide'] && !ifpost($tid)) ? 1 : 0;
$isAllowViewPic = $admincheck || ($read['authorid'] == $winduid) || (!$ifhide && ($winduid || !$forumset['viewpic']));
(!$isTucool || !$isAllowViewPic) && ObHeader("read.php?tid=$tid&ds=1");

//禁言、屏蔽
$userService = L::loadClass('UserService', 'user');
$userInfo = $userService->get($read['authorid'],true,false,false);
$ifshieldThread = (($read['ifshield'] || ($userInfo['groupid'] == 6 && $db_shield)) && !$isGM)? 0 : 1;
!$ifshieldThread && ObHeader("read.php?tid=$tid&ds=1");

$attachsService = L::loadClass('Attachs', 'forum');
$tucoolAttachs = $attachsService->getByTidAndUid($tid,$read['authorid']);
!$tucoolAttachs && ObHeader("read.php?tid=$tid&ds=1");

if ($read['aid']) {
	$attachShow = new attachShow(($isGM || $pwSystem['delattach'] || $read['authorid'] == $winduid), $forumset['uploadset'], $forumset['viewpic']);
	$attachShow->setData($tucoolAttachs);
	$tucoolAttachs = buildTucoolAttachs($tucoolAttachs);
	$contentAids = $attachShow->findPicAids($read['content']);
	$read['content'] = convert($read['content'], $db_windpost);
	if (strrpos($read['content'],'attachment') !== false) {
		$haveAids = $attachShow->findPicAids($read['content']);
	}
}
$tmpKeyArray = array_diff(array_keys($tucoolAttachs),(array)$contentAids);
$tmpArray = array();
foreach((array)$tmpKeyArray as $v){
	$tmpArray[$v] = $tucoolAttachs[$v];
}
$contentAttachs = array();
if (S::isArray($haveAids)) {
	foreach($tucoolAttachs as $k => $v) {
		if (S::inArray($k,$haveAids)) $contentAttachs[$k] = $tucoolAttachs[$k];
	}
}
$tucoolAttachs = array_merge($contentAttachs,(array)$tmpArray);
!$tucoolAttachs && refreshto("read.php?tid=$tid&ds=1",'您暂无权限查看此帖的图片!');
// 编辑图片信息权限
$editAttachRight = ($admincheck || $read['authorid'] == $winduid) ? 1 : 0;

// 回复数
if ($openIndex) {#高楼帖子索引
	$replyCount = 1 + $db->get_value("SELECT max(floor) FROM pw_postsfloor WHERE tid =". S::sqlEscape($tid));
} else {
	$replyCount = $read['replies']+1;
}

//帖子浏览记录
$readlog = str_replace(",$tid,",',',GetCookie('readlog'));
$readlog.= ($readlog ? '' : ',').$tid.',';
$readlogCount = substr_count($readlog,',');
$readlogCount>11 && $readlog = preg_replace("/[\d]+\,/i",'',$readlog,$readlogCount-11);
Cookie('readlog',$readlog);

$readdb = $_uids = array();
$read['pid'] = $pids[] = 'tpc';
$readdb[] = $read;
$_uids[$read['authorid']] = 'UID_'.$read['authorid'];#用户
//帖内置顶相关处理
if ($topped_count) {
	$topped_page_num = $db_readperpage;
	$start_limit = (int)($page-1)*$db_readperpage - 1;
	if ($start_limit < 0) {
		$topped_page_num += $start_limit;
		$start_limit = 0;
	}
	$topped_count - $start_limit < $db_readperpage && $topped_page_num = $topped_count - $start_limit;
	$topped_page_num = $topped_page_num < 0 ? 0 : $topped_page_num;
	if ($topped_count > $start_limit) {
		$limit = S::sqlLimit($start_limit,$topped_page_num);
		$query = $db->query("SELECT t.floor, p.* FROM pw_poststopped t
			LEFT JOIN $pw_posts p ON t.pid = p.pid $tablaadd
			WHERE t.tid = ".S::sqlEscape($tid)." AND t.fid = '0' AND t.pid != '0' AND p.ifcheck = '1' ORDER BY t.uptime desc $limit");
		while ($rd = $db->fetch_array($query)) {
			$_uids[$rd['authorid']] = 'UID_'.$rd['authorid'];
			$rd['aid'] && $_pids[$rd['pid']] = $rd['pid'];
			$rd['istop'] = "topped";
			$_page = ceil(($rd['floor'] + 1 + $topped_count)/$db_readperpage);
			$rd['jumpurl'] = "read.php?tid=$tid&page=$_page#".$rd['pid'];
			//$rd['remindinfo'] = '';
			$readdb[] = $rd;
			$pids[] = $rd['pid'];
		}
	}
}
list($replies,$hits) = array(intval($read['replies']),intval($read['hits']));

//更新帖子点击
if ($db_hits_store == 0){
	pwQuery::update('pw_threads', 'tid=:tid', array($tid), null, array(PW_EXPR=>array('hits=hits+1')));	
}elseif ($db_hits_store == 1){
	$db->update('UPDATE pw_hits_threads SET hits=hits+1 WHERE tid='.S::sqlEscape($tid)); 
}elseif ($db_hits_store == 2){
	pwCache::writeover(D_P.'data/bbscache/hits.txt',$tid."\t", 'ab');
} 

//帖子回复信息
if ($read['replies'] > 0 && $topped_page_num < $db_readperpage) {
	if ($openIndex) {#高楼索引处理
		$readnum = $db_readperpage-1;
		$start_limit = 0;
		$start_limit = $start_limit-$topped_count <= 0 ? 0 : $start_limit-$topped_count;
		$end = $start_limit + $readnum - $topped_page_num;
		$sql_floor = " AND f.floor > " . $start_limit ." AND f.floor <= ".$end." ";
		$query = $db->query("SELECT f.pid FROM pw_postsfloor f WHERE f.tid = ". S::sqlEscape($tid) ." $sql_floor ORDER BY f.floor DESC");
		while ($rt = $db->fetch_array($query)) {
			$postIds[] = $rt['pid'];
		}
		if (getstatus($read['tpcstatus'], 7)) {
			$robSql = " AND floor > " . S::sqlEscape($start_limit) ." AND floor <= " . S::sqlEscape($end);
			if ($award) {
				$postIds = array();
				$robSql = '';
				$start_limit = (int)($page-1)*$db_readperpage-1;
				if ($start_limit < 0) {
					$start_limit = 0 ;
				}
				$end_limit = ($start_limit == 0) ? $db_readperpage-1 : $db_readperpage;
				$limit = S::sqlLimit($start_limit,$end_limit);
			}
			$robFloors = array();
			$query = $db->query("SELECT pid,floor FROM pw_robbuildfloor WHERE tid = ". S::sqlEscape($tid) ." $robSql ORDER BY floor DESC $limit");
			while ($rt = $db->fetch_array($query)) {
				$robFloors[$rt[pid]] = $rt['floor'];
				$award && $postIds[] = $rt['pid'];
			}
		}
		if ($postIds) {
			$postIds && $sql_postId = " AND t.pid IN ( ". S::sqlImplode($postIds,false) ." ) ";
			$query = $db->query("SELECT t.* FROM $pw_posts t $tablaadd WHERE t.tid=".S::sqlEscape($tid)." $sql_postId");
			$currentPostsId = array();
			while ($read = $db->fetch_array($query)) {
				if ($read['ifcheck']!='1') {
					$read['subject'] = '';
					$read['content'] = getLangInfo('bbscode','post_unchecked');
				}
				$_uids[$read['authorid']] = 'UID_'.$read['authorid'];
				$read['aid'] && $_pids[$read['pid']] = $read['pid'];
				($robFloors && isset($robFloors[$read['pid']]))&& $read['robfloor'] = $robFloors[$read['pid']];
				$read['istop'] = strpos($read['remindinfo'],getLangInfo('bbscode','read_topped_tag')) !== false ? 'top' : '';
				$currentPostsId[] = $read['pid'];
				$currentPosts[$read['pid']] = $read;
			}
			foreach ($postIds as $key => $value) {
				if (in_array($value,$currentPostsId)) {
					$readdb[] = $currentPosts[$value];
				}else{
					$readdb[] = array('postdate'=>'N','content'=>getLangInfo('bbscode','post_deleted'));
				}
			}
		}
	} else {#正常分页
		$readnum = $db_readperpage-1;
		$start_limit = 0;
		if  (perf::checkMemcache() && !$fieldadd && !$tablaadd && !$sqladd && $orderby=='DESC' && !$rewardtype) {
			$_cacheService = Perf::gatherCache('pw_posts');
			$tmpReaddb = $_cacheService->getFirstPostsByTid($pw_posts,$tid,$start_limit,($db_readperpage-$topped_page_num));
			foreach ($tmpReaddb as $key=>$value) {
				$robPostIds[] = $value['pid'];
				$_uids[$value['authorid']] = 'UID_'.$read['authorid'];
				$value['aid'] && $_pids[$value['pid']] = $value['pid'];
				$value['istop'] = strpos($value['remindinfo'],getLangInfo('bbscode','read_topped_tag')) !== false ? 'top' : '';
				$tmpReaddb[$key] = $value;
			}
			$readdb = array_merge((array)$readdb,(array)$tmpReaddb);
		} else {
			$limit = S::sqlLimit($start_limit,($db_readperpage-$topped_page_num));
			$query = $db->query("SELECT t.* FROM $pw_posts t $tablaadd WHERE t.tid=".S::sqlEscape($tid)." AND t.ifcheck='1' ORDER BY t.postdate DESC $limit");
			while ($read = $db->fetch_array($query)) {
				$_uids[$read['authorid']] = 'UID_'.$read['authorid'];
				$read['aid'] && $_pids[$read['pid']] = $read['pid'];
				$read['istop'] = strpos($read['remindinfo'],getLangInfo('bbscode','read_topped_tag')) !== false ? 'top' : '';
				$readdb[] = $read;
			}
		}
	}
	$db->free_result($query);
	$pageinverse && $readdb = array_reverse($readdb);
}

//读取用户信息
if ($_uids) {
	$_userIds = array_keys($_uids);
	if (perf::checkMemcache()){
		$_cacheService = Perf::gatherCache('pw_members');
		$pwMembers = $tableinfo ? $_cacheService->getAllByUserIds($_userIds, true, true, true) : $_cacheService->getAllByUserIds($_userIds, true, true);
		$showCustom && $customdb = $_cacheService->getMemberCreditByUserIds($_userIds);
		$db_showcolony && $colonydb = $_cacheService->getCmemberAndColonyByUserIds($_userIds);	
		//为了兼容原来版本中的查询字段取别名 'icon as micon'
		if (S::isArray($pwMembers)){
			foreach ($pwMembers as $k=>$v){
				$pwMembers[$k]['micon'] = $pwMembers[$k]['icon'];
				unset($pwMembers[$k]['icon']);
			}
		}
	} else {
		$_dbCacheService = Perf::gatherCache('pw_membersdbcache');
		list($pwMembers, $customdb, $colonydb) = $_dbCacheService->getUserDBCacheByUserIds($_userIds, $showCustom, $db_showcolony, $showfield);
	}
}
//用户禁言及词语过滤
$bandb = S::isArray($pwMembers) ? $pwforum->forumBan($pwMembers) : null;
$start_limit = ($page == 1 || $start_limit < 0)? 0 : $start_limit + 1;

//帖子详细内容
$ping_logs = array();
$pageinverse && $start_limit += $readnum - 1;

require_once(R_P.'require/showimg.php');
if(S::isArray($readdb)){
	foreach ($readdb as $key => $read) {
		$read = array_merge((array)$read,(array)$pwMembers[$read['authorid']]);
		isset($bandb[$read['authorid']]) && $read['groupid'] = 6;
		if (($read['ifshield'] || $read['groupid'] == 6 && $db_shield) && !$admincheck) {
			unset($readdb[$key]);
			continue;
		}
		if ($read['istop'] == 'topped') {
			$readdb[$key] = viewread($read,'');
		} else {
			if( $pageinverse) {
				$readdb[$key] = viewread($read,$start_limit--);
			} else {
				$readdb[$key] = viewread($read,$start_limit++);
			}
		}
	}
}
unset($_cache,$sign,$ltitle,$lpic,$lneed,$_G['right'],$_MEDALDB,$fieldadd,$tablaadd,$read,$order,$readnum,$pwMembers);

require_once PrintEot('slide');
pwOutPut();

function viewread($read) {
	global $winduid,$isGM,$pwSystem,$_G,$db_windpost,$tpc_buy,$tpc_pid,$tpc_tag,$tpc_author,$tid;
	$tpc_buy = $read['buy'];
	$tpc_pid = $read['pid'];
	$tpc_tag = NULL;
	$tpc_author = '';
	if ($read['anonymous']) {
		$anonymous = (!$isGM && $winduid != $read['authorid'] && !$pwSystem['anonyhide']);
	} else {
		$anonymous = false;
	}
	if (!$anonymous) {
		$tpc_author = $read['author'];
	}
	list($read['face']) = showfacedesign($read['micon'], true, 'm');
	$read['content'] = preg_replace("/\[quote\](.*)\[\/quote\]/is","",$read['content']);
	$read['ifsign']<2 && $read['content'] = str_replace("\n", "<br />", $read['content']);
	$read['leaveword'] && $read['content'] .= leaveword($read['leaveword'],$read['pid']);
	
	$read['content'] = preg_replace('/(\[s:[^]]+\])+/', '[表情]', $read['content']); //face
	$read['content'] = strip_tags(convert($read['content'],'1'));
	if ($read['ifwordsfb'] != $GLOBALS['db_wordsfb']) {
		$read['content'] = wordsConvert($read['content'], array(
			'id'	=> ($tpc_pid == 'tpc') ? $tid : $tpc_pid,
			'type'	=> ($tpc_pid == 'tpc') ? 'topic' : 'posts',
			'code'	=> $read['ifwordsfb']
		));
	}
	$read['content'] = parseReplyContent($read['content']);
	return $read;
}

function parseReplyContent($old_content) {
	global $imgpath;
	$old_content = preg_replace("/\[hide=(.+?)\](.+?)\[\/hide\]/is",getLangInfo('post','hide_post'),$old_content);
	$old_content = preg_replace("/\[post\](.+?)\[\/post\]/is",getLangInfo('post','post_post'),$old_content);
	$old_content = preg_replace("/\[sell=(.+?)\](.+?)\[\/sell\]/is",getLangInfo('post','sell_post'),$old_content);
	$bit_content = explode("\n",$old_content);
	
	if (count($bit_content) > 5) {
		$old_content = "$bit_content[0]\n$bit_content[1]\n$bit_content[2]\n$bit_content[3]\n$bit_content[4]\n.......";
	}
	if (strpos($old_content,$db_bbsurl) !== false) {
		$old_content = str_replace('p_w_picpath',$db_picpath,$old_content);
		$old_content = str_replace('p_w_upload',$db_attachname,$old_content);
	}
	$old_content = preg_replace("/\<(.+?)\>/is","",$old_content);
	$old_content = preg_replace('/\[(\/)?(b|u|i|list|sub|color|font|hr|size|align|sup|strike|code|paragraph)[^\]]*]/i', '', $old_content);
	$quote_content = substrs($old_content, 260);

	if (strpos($quote_content,'p_w_upload') !== false) {
		$quote_content = str_replace('p_w_upload',$db_attachname,$quote_content);
	}
	return preg_replace('/(\[attachment=\d+\])+/', "[图片]", $quote_content); //face	
}

function buildTucoolAttachs($tucoolAttachs) {
	global $isGM;
	if (!S::isArray($tucoolAttachs)) return array();
	$attachs = array();
	$countNum = count((array) $tucoolAttachs);
	$i = 1;
	foreach ((array)$tucoolAttachs as $v) {
		$v['dfadmin'] = $GLOBALS['attachShow']->isAdmin;
		if ($v['type'] != 'img' || ($v['needrvrc'] > 0 && $GLOBALS['attachShow']->viewHiddenAtt($v) !== true)) continue;
		$v['position'] = '['.$i . '/' . $countNum.']';
		$v['json'] = pwJsonEncode($v);
		$attachs[$v[aid]] = $v;
		$i++;
	}
	return $attachs;
}