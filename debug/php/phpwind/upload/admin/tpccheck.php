<?php
!function_exists('adminmsg') && exit('Forbidden');
$basename="$admin_file?adminjob=tpccheck";
//* include_once pwCache::getPath(D_P.'data/bbscache/forumcache.php');
pwCache::getData(D_P.'data/bbscache/forumcache.php');
include_once(R_P.'require/forum.php');

if ($admin_gid == 5) {
	list($allowfid,$forumcache) = GetAllowForum($admin_name);
	$sql = $allowfid ? "fid IN($allowfid)" : '0';
} else {
	//* include pwCache::getPath(D_P.'data/bbscache/forumcache.php');
	pwCache::getData(D_P.'data/bbscache/forumcache.php');
	list($hidefid,$hideforum) = GetHiddenForum();
	if ($admin_gid == 3) {
		$forumcache .= $hideforum;
		$sql = '1';
	} else {
		$sql = $hidefid ? "fid NOT IN($hidefid)" : '1';
	}
}
$action = S::getGP('action');


if (!$action) {
	if (!$_POST['step']) {

		S::gp(array('fid','username','uid','page'));
		if (is_numeric($fid)) {
			$sql .= " AND fid=" . S::sqlEscape($fid);
		} elseif ($sql == '1') {
			$fids = array();
			foreach ($forum as $key => $value) {
				$fids[] = $key;
			}
			$fids && $sql .= " AND fid IN(" . S::sqlImplode($fids) . ")";
		}
		$sql .= " AND ifcheck='0'";
		if ($username) {
			$sql .= " AND author like " . S::sqlEscape("%$username%");
			$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
			$userdb = $userService->getByUserName($username);
			$uid=$userdb['uid'];
		}
	//	is_numeric($uid) && $sql .= "AND authorid=" . S::sqlEscape($uid);

		(!is_numeric($page) || $page < 1) && $page = 1;
		$limit = S::sqlLimit(($page-1)*$db_perpage,$db_perpage);

		$rt    = $db->get_one("SELECT COUNT(*) AS sum FROM pw_threads WHERE $sql");
		$pages = numofpage($rt['sum'],$page,ceil($rt['sum']/$db_perpage),"$basename&fid=$fid&uid=$uid&");

		/**Begin modify by liaohu 2010-06-21*/
		$checkdb = array();

		$query = $db->query("SELECT tid,fid,subject,author,authorid,postdate FROM pw_threads WHERE $sql ORDER BY postdate DESC $limit");
		while ($rt = $db->fetch_array($query)) {
			$rt['content_table'] = GetTtable($rt['tid']);

			if ($rt['subject']) {
				$rt['subject'] = substrs($rt['subject'],35);
			} else {
				$rt['subject'] = substrs($rt['content'],35);
			}

			$rt['name']     = $forum[$rt['fid']]['name'];
			$rt['postdate'] = get_date($rt['postdate']);

			$checkdb[] = $rt;
		}

		foreach($checkdb as $key=>$v){
			$query = $db->query("SELECT content FROM ".$v['content_table']." where tid = ".$v['tid']);
			$rt = $db->fetch_array($query);
			$checkdb[$key]['content'] = $rt['content'];
		}
		/**End modify by liaohu 2010-06-21*/
		$forumcache = preg_replace("/\<option value=\"$fid\"\>(.+?)\<\/option\>(\\r?\\n)/is","<option value=\"".$fid."\" selected>\\1</option>\\2",$forumcache);
		include PrintEot('tpccheck');exit;

	} elseif ($_POST['step'] == 2) {

	    /**Begin modify by liaohu 2010-06-21*/
		S::gp(array('pass','dels','here','selid'),'P');

		if(0 == count($pass) && 0 == count($dels) && 0 == count($here)){
			adminmsg("operate_error");
		}

		$pass = array_values($pass);

		if (is_array($pass)) {
			$fids  = $cydb = $arrtmsgs = array();
			$query = $db->query("SELECT tid,fid,tpcstatus FROM pw_threads WHERE $sql AND tid IN(".S::sqlImplode($pass).")");
			while ($rt = $db->fetch_array($query)) {
				$tablename = GetTtable($rt['tid']);
				$arrtmsgs[$tablename][] = $rt['tid'];
				$fids[$rt['fid']] ++;
				if ($rt['tpcstatus'] && getstatus($rt['tpcstatus'], 1)) {
					$cydb[] = $rt['tid'];
				}
			}
			foreach ($fids as $key => $value) {
				$rt = $db->get_one("SELECT tid,author,postdate,subject FROM pw_threads WHERE fid=" . S::sqlEscape($key) . " ORDER BY lastpost DESC LIMIT 1");
				$lastpost = $rt['subject']."\t".$rt['author']."\t".$rt['postdate']."\t"."read.php?tid=$rt[tid]&page=e#a";
				/**
				$db->update("UPDATE pw_forumdata"
					. " SET topic=topic+" . S::sqlEscape($value)
						. ',article=article+' . S::sqlEscape($value)
						. ',tpost=tpost+' . S::sqlEscape($value)
						. ',lastpost=' . S::sqlEscape($lastpost)
					. ' WHERE fid=' . S::sqlEscape($key));
				**/
				$db->update(pwQuery::buildClause("UPDATE :pw_table SET topic=topic+:topic,article=article+:article,tpost=tpost+:tpost,lastpost=:lastpost WHERE fid=:fid", array('pw_forumdata', $value, $value, $value, $lastpost, $key)));		
			}
	
			if ($pass) {
				//$db->update("UPDATE pw_threads SET ifcheck='1' WHERE $sql AND tid IN(".S::sqlImplode($pass).")");
				pwQuery::update('pw_threads', "$sql AND tid IN (:tid)", array($pass), array('ifcheck'=>1));
				foreach ($arrtmsgs as $tmsgs => $tids) {
					if ($tids) {
						//* $db->update("UPDATE $tmsgs SET ifwordsfb='$db_wordsfb' WHERE tid IN(".S::sqlImplode($tids).")");
						pwQuery::update($tmsgs, 'tid IN (:tid)', array($tids), array('ifwordsfb'=>$db_wordsfb));
					}
				}
				/**
				$threadIds = explode("','",trim($pass,"'"));
				if($threadIds){
					$threads = L::loadClass('Threads', 'forum');
					$threads->delThreads($threadIds);
				}**/
			}
			if ($cydb) {
				$query = $db->query("SELECT COUNT(*) AS tnum,cyid FROM pw_argument WHERE tid IN(" . S::sqlImplode($cydb) . ") GROUP BY cyid");
				while ($rt = $db->fetch_array($query)) {
					//* $db->update("UPDATE pw_colonys SET tnum=tnum+" . S::sqlEscape($rt['tnum']) . ' WHERE id=' . S::sqlEscape($rt['cyid']));
					$db->update(pwQuery::buildClause("UPDATE :pw_table SET tnum=tnum+:tnum WHERE id=:id", array('pw_colonys', $rt['tnum'], $rt['cyid'])));
				}
			}
			/*
			foreach($fids as $fid){
				$threadList = L::loadClass("threadlist", 'forum');
				$threadList->refreshThreadIdsByForumId($fid);
			} */
			Perf::gatherInfo('changeThreadWithForumIds', array('fid'=>array_keys($fids)));		
			
		}
		if(is_array($dels)){

			$delarticle = L::loadClass('DelArticle', 'forum');
			if (!$sqlby = $delarticle->sqlFormatByIds($dels)) {
				$basename = "javascript:history.go(-1);";
				adminmsg('operate_error');
			}
			$readdb = $delarticle->getTopicDb("$sql AND tid $sqlby");

			$delarticle->delTopic($readdb);
		}
		adminmsg('operate_success');
		/**End modify by liaohu 2010-06-21*/
	}
}  elseif ($action == 'moveThread') {
	define('AJAX',1);
	S::gp(array('step','tid','fid'),'GP','2');
	if (empty($step)) {
		$fid = intval($fid);
		$basename="$admin_file?adminjob=tpccheck&action=moveThread";
		pwCache::getData(D_P . 'data/bbscache/cache_post.php');
		pwCache::getData(D_P . 'data/bbscache/forum_typecache.php');
		$re = $db->query("SELECT fid,t_type FROM pw_forums ");
		$forumArr = array();
		while($row = $db->fetch_array($re)) {
			$forumArr[$row['fid']] = $row;
		}
		if ($topic_type_cache) {
			foreach ($topic_type_cache as $key => $value) {
				foreach ($value as $k => $v) {
					
					$v['name'] = strip_tags($v['name']);
					if ($v['upid'] == 0) {
						if($forumArr[$key]['t_type'] == 0) continue;
						$t_typedb[$key][$k] = $v['name'];
						$t_typedb[$key][0]  = $forumArr[$key]['t_type']; //zhuli
					} else {	
						$t_subtypedb[$key][$v['upid']][$k] = $v['name'];
					}
				}
			}
		}
		if (S::isArray($t_typedb)) {
			$t_typedb = pwJsonEncode($t_typedb);
		}
		if (S::isArray($t_subtypedb)) {
			$t_subtypedb = pwJsonEncode($t_subtypedb);
		}
		$reason_sel = '';
		$reason_a	= explode("\n",$db_adminreason);
		foreach ($reason_a as $k=>$v) {
			if ($v = trim($v)) {
				$reason_sel .= "<option value=\"$v\">$v</option>";
			} else {
				$reason_sel .= "<option value=\"\">-------</option>";
			}
		}
		include PrintEot('tpccheck');ajax_footer();
	} elseif ($step == 2) {
		S::gp(array('to_id', 'to_threadcate', 'to_subtype','tidarray'));
		if ($forum[$to_id]['type'] == 'category') {
			Showmsg('mawhole_error');
		}
		pwCache::getData(D_P . 'data/forums/fid_'.$to_id.'.php');
		if($foruminfo['t_type'] == 2 && !$to_threadcate) Showmsg('请选择主题分类后发布');
		$mids = $ttable_a = $ptable_a = array();
		if (is_array($tidarray)) {
			foreach ($tidarray as $key => $value) {
				if (is_numeric($value)) {
					$mids[] = $value;
					$ttable_a[GetTtable($value)][] = $value;
				}
			}
		}
		!$mids && Showmsg('mawhole_nodata');

		$pw_attachs = L::loadDB('attachs', 'forum');
		$pw_attachs->updateByTid($mids, array('fid' => $to_id));

		//* $threads = L::loadClass('Threads', 'forum');
		//* $threads->delThreads($mids);
		Perf::gatherInfo('changeThreadWithThreadIds', array('tid'=>$mids));

		//$mids = S::sqlImplode($mids);
		$updatetop = $todaypost = $topic_all = $replies_all = 0;

		$cy_tids = array();
		$query = $db->query("SELECT tid,fid as tfid,author,postdate,subject,replies,topped,ptable,ifcheck,tpcstatus,modelid,special,specialsort FROM pw_threads WHERE tid IN(" . S::sqlImplode($mids) . ")");
		
		$mgdate = get_date($timestamp, 'Y-m-d'); //语言文件中用
		//tucool
		$tucoolService = L::loadClass('Tucool','forum');
		while ($rt = $db->fetch_array($query)) {
			S::slashes($rt);
			@extract($rt);
			$ptable_a[$ptable] = 1;
			$postdate > $tdtime && $todaypost += ($replies + 1);
			$ifcheck && $topic_all++;
			$replies_all += $replies;
			if ($rt['tpcstatus'] && getstatus($rt['tpcstatus'], 1)) {
				$cy_tids[$rt['tid']] = $rt['tid'];
			}
			// 静态模版更新
			if ($foruminfo['allowhtm'] == 1) {
				$date = date('ym', $postdate);
				$htmurldel = R_P . $db_readdir . '/' . $fid . '/' . $date . '/' . $tid . '.html';
				P_unlink($htmurldel);
			}
			$toname = strip_tags($forum[$to_id]['name']);
			$logdb[] = array('type' => 'move', 'username1' => $author, 'username2' => $windid, 'field1' => $fid,
				'field2' => $tid, 'field3' => '', 'descrip' => 'move_descrip', 'timestamp' => $timestamp,
				'ip' => $onlineip, 'tid' => $tid, 'subject' => substrs($subject, 28), 'tofid' => $to_id,
				'toforum' => $toname, 'forum' => $forum[$fid]['name'], 'reason' => stripslashes($atc_content));

			//分类信息处理
			if ($modelid > 0) {
				$tablename = GetTopcitable($modelid);
				$db->update("UPDATE $tablename SET fid=" . S::sqlEscape($to_id) . " WHERE tid=" . S::sqlEscape($tid));
			}
			//分类信息处理

			//团购处理
			if ($special > 20) {
				$pcid = $special - 20;
				$pcid = (int) $pcid;
				$tablename = GetPcatetable($pcid);
				$db->update("UPDATE $tablename SET fid=" . S::sqlEscape($to_id) . " WHERE tid=" . S::sqlEscape($tid));
			}
			//团购处理

			//置顶帖处理
			if ($specialsort) {
				$updatetop = 1;
				if ($specialsort == PW_THREADSPECIALSORT_KMD) {//孔明灯
					/*
					if (!$toForuminfo) {
						$toForuminfo = L::forum($to_id);
						$toForuminfo = $toForuminfo['forumset'];
					}
					$kmdService = L::loadClass('kmdservice', 'forum');
					if (!$toForuminfo['ifkmd']){//目标版块未开启孔明灯
						$kmdService->initThreadInfoByTid($tid);
					} else {
						$leftKmd = $kmdService->getLeftKmdNumsByFid($to_id);
						$kmdInfo = $kmdService->getKmdInfoByTid($tid);
						//开启,孔明灯数量未达上限，则更新
						if ($leftKmd && is_array($kmdInfo)) {
							$kmdService->updateKmdInfo(array('fid'=>$to_id),$kmdInfo['kid']);
						} else {
							$kmdService->initThreadInfoByTid($tid);
						}
					}
					*/
					$kmdService = L::loadClass('kmdservice', 'forum');
					$kmdService->initThreadInfoByTid($tid);
				} else {//置顶帖
					$_topped = $db->get_one("SELECT * FROM pw_poststopped WHERE fid=" . S::sqlEscape($fid) . " AND tid=" . S::sqlEscape($tid) . " AND pid='0'");
					if ($_topped) {
						$db->update("UPDATE pw_poststopped SET uptime = " . S::sqlEscape($to_id) . " WHERE tid= " . S::sqlEscape($tid) . " AND pid = '0' AND fid = " . S::sqlEscape($fid));
						$db->update("REPLACE INTO pw_poststopped (fid,tid,pid,floor,uptime,overtime) values
							(" . S::sqlEscape($to_id) . "," . S::sqlEscape($tid) . ",'0'," . S::sqlEscape($_topped['floor']) . "," . S::sqlEscape($to_id) . "," . S::sqlEscape($_topped['overtime']) . ") ");
					}
				}
			}
			//置顶帖处理
		}

		$remindinfo = strip_tags(getLangInfo('other', 'mawhole_move'));
		$to_threadcate = $to_subtype ? $to_subtype : $to_threadcate;

		//$db->update("UPDATE pw_threads SET fid=" . S::sqlEscape($to_id) . ",type=" . S::sqlEscape($to_threadcate) . " WHERE tid IN($mids)");
		pwQuery::update('pw_threads', 'tid IN (:tid)', array($mids), array('fid'=>$to_id, 'type'=>$to_threadcate));

		foreach ($ttable_a as $pw_tmsgs => $val) {
			//* $val = S::sqlImplode($val);
			//* $db->update("UPDATE $pw_tmsgs SET remindinfo=" . S::sqlEscape($remindinfo) . " WHERE tid IN($val)");
			pwQuery::update($pw_tmsgs, 'tid IN (:tid)', array($val), array('remindinfo'=>$remindinfo));
		}
		foreach ($ptable_a as $key => $val) {
			$pw_posts = GetPtable($key);
			//$db->update("UPDATE $pw_posts SET fid=" . S::sqlEscape($to_id) . " WHERE tid IN(" . S::sqlImplode($mids) . ")");
			pwQuery::update($pw_posts, 'tid IN(:tid)', array($mids), array('fid' => $to_id));
		}
		
		//tucool
		foreach ($mids as $tid){
			$tucoolService->updateTucoolImageNum($tid);
		}

		Perf::gatherInfo('changeThreadWithForumIds', array('fid'=>array($fid, $to_id)));

		if (!empty($cy_tids)) {
			$db->update("DELETE FROM pw_argument WHERE tid IN(" . S::sqlImplode($cy_tids) . ')');
			pwQuery::update('pw_threads', 'tid IN (:tid)', array($cy_tids), array('tpcstatus'=>0));
		}

		if ($updatetop) {
			updatetop();
		}

		//* P_unlink(D_P . 'data/bbscache/c_cache.php');
		pwCache::deleteData(D_P . 'data/bbscache/c_cache.php');
		adminmsg('operate_success');
	}
} else {
	$basename="$admin_file?adminjob=tpccheck&action=postcheck";
	if (!$_POST['step']) {
		S::gp(array('fid','username','uid','page','ptable'));
		/**Begin modify by liaohu 2010-06-21*/
		if ($username) {
			$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
			$userdb = $userService->getByUserName($username);
			$uid=$userdb['uid'];
		}
		//is_numeric($uid) && $sql .= " AND authorid=" . S::sqlEscape($uid);
		$sql = str_replace('fid', 't.fid', $sql);
		$sql .= " ORDER BY postdate DESC";
		if ($db_plist && count($db_plist)>1) {
			!isset($ptable) && $ptable = $db_ptable;
			foreach ($db_plist as $key => $val) {
				$name = $val ? $val : ($key != 0 ? getLangInfo('other','posttable').$key : getLangInfo('other','posttable'));
				$p_table .= "<option value=\"$key\">".$name."</option>";
			}	
			$p_table  = str_replace("<option value=\"$ptable\">","<option value=\"$ptable\" selected=\"selected\">",$p_table);
			$pw_posts = GetPtable($ptable);
		} else {
			$pw_posts = 'pw_posts';
		}
		(!is_numeric($page) || $page < 1) && $page = 1;
		$limit = S::sqlLimit(($page-1)*$db_perpage,$db_perpage);
		$rt    = $db->get_one("SELECT COUNT(*) AS sum FROM $pw_posts WHERE ifcheck='0' ".(is_numeric($fid) ? " AND fid=".S::sqlEscape($fid) : " ") . ($username  ?  " AND author like " . S::sqlEscape("%$username%") : " " ) . " ORDER BY postdate DESC");
		$pages = numofpage($rt['sum'],$page,ceil($rt['sum']/$db_perpage),"$basename&fid=$fid&uid=$uid&");
		//(is_numeric($uid)  ? " AND t.authorid=" . S::sqlEscape($uid) : " ") .
		$postdb=array();
		$query = $db->query("SELECT p.pid,p.tid,p.fid,p.subject,p.author,p.authorid,p.ifcheck,p.postdate,p.content,t.subject as tsubject FROM $pw_posts AS p LEFT JOIN pw_threads AS t ON p.tid = t.tid WHERE p.ifcheck='0' ".(is_numeric($fid) ? " AND t.fid=".S::sqlEscape($fid) : " ") .($username  ? " AND p.author like " . S::sqlEscape("%$username%") : " ") . " AND $sql $limit");
		/**Begin modify by liaohu 2010-06-21*/
		while ($rt = $db->fetch_array($query)) {
			if ($rt['subject']) {
				$rt['subject'] = substrs($rt['subject'],35);
			} else {
				$rt['subject'] = substrs($rt['content'],35);
			}
			$rt['name']     = $forum[$rt['fid']]['name'];
			$rt['postdate'] = get_date($rt['postdate']);
			$postdb[]       = $rt;
		}

		$forumcache = preg_replace("/\<option value=\"$fid\"\>(.+?)\<\/option\>(\\r?\\n)/is","<option value=\"".$fid."\" selected>\\1</option>\\2",$forumcache);
		include PrintEot('postcheck');exit;

	} elseif ($_POST['step'] == 2) {
		/**Begin modify by liaohu 2010-06-21*/
		S::gp(array('pass','dels','here','ptable'),'P');

		if(0 == count($pass) && 0 == count($dels) && 0 == count($here)){
			adminmsg("operate_error");
		}

		$pass = array_values($pass);
		$pw_posts = GetPtable($ptable);
		//if ($type == 'pass') {
		if(is_array($pass)){
			$fids  = $tids = array();
			$query = $db->query("SELECT fid,tid FROM $pw_posts WHERE $sql AND pid IN(".S::sqlImplode($pass).")");
			while ($rt = $db->fetch_array($query)) {
				$tids[$rt['tid']] ++;
				$fids[$rt['fid']] ++;
			}
			foreach ($tids as $key => $value) {
				$rt = $db->get_one("SELECT postdate,author FROM $pw_posts WHERE tid=" . S::sqlEscape($key) . " ORDER BY postdate DESC LIMIT 1");

				//$db->update("UPDATE pw_threads SET replies=replies+".S::sqlEscape($value) . ",lastpost=" . S::sqlEscape($rt['postdate'],false) . ",lastposter =" . S::sqlEscape($rt['author'],false) . "WHERE tid=" . S::sqlEscape($key));
				$db->update(pwQuery::buildClause('UPDATE :pw_table SET replies = replies + :replies, lastpost = :lastpost, lastposter = :lastposter WHERE tid = :tid', array('pw_threads', $value, $rt['postdate'], $rt['author'], $key)));
				# memcache refresh
				M::sendNotice(
					array($rt['author']),
					array(
						'title' => getLangInfo('writemsg','post_pass_title'),
						'content' => getLangInfo('writemsg','post_pass_content',array(
							'tid' => $key
						)),
					)
				);
				/*
				$threadList = L::loadClass("threadlist", 'forum');
				$threadList->updateThreadIdsByForumId($fid,$key);

				$thread = L::loadClass("Threads", 'forum');
				$thread->clearThreadByThreadId($key); */
				
				Perf::gatherInfo('changeThreadWithForumIds', array('fid'=>$fid));				
			}
			foreach ($fids as $key => $value) {
				/**
				$db->update("UPDATE pw_forumdata SET article=article+".S::sqlEscape($value).",tpost=tpost+".S::sqlEscape($value,false)."WHERE fid=".S::sqlEscape($key));
				**/
				$db->update(pwQuery::buildClause("UPDATE :pw_table SET article=article+:article,tpost=tpost+:tpost WHERE fid=:fid", array('pw_forumdata', $value, $value, $key)));
			}

			$db->update("UPDATE $pw_posts SET ifcheck='1',ifwordsfb='$db_wordsfb' WHERE $sql AND pid IN(".S::sqlImplode($pass).")");
			Perf::gatherInfo('changePosts', array('_tablename'=>$pw_posts, 'pid'=>$pass));
			
			/**
			$threadIds = explode(",",$pass);
			if($threadIds){
				$threads = L::loadClass('Threads', 'forum');
				$threads->delThreads($threadIds);
			}**/
		} else if(is_array($dels)){
			require_once(R_P.'require/credit.php');
			$creditOpKey = "Deleterp";
			$forumInfos = array();
			$_tids = $_pids = $deluids = array();
			$query = $db->query("SELECT fid,tid,pid,aid,author,authorid FROM $pw_posts WHERE $sql AND pid IN(".S::sqlImplode($dels).")");
			while ($rt = $db->fetch_array($query)) {
				//积分操作
				if (!isset($forumInfos[$rt['fid']])) $forumInfos[$rt['fid']] = L::forum($rt['fid']);
				$foruminfo = $forumInfos[$rt['fid']];
				$creditset = $credit->creditset($foruminfo['creditset'],$db_creditset);
				$credit->addLog("topic_$creditOpKey", $creditset[$creditOpKey], array(
					'uid' => $rt['authorid'],
					'username' => $rt['author'],
					'ip' => $onlineip,
					'fname' => strip_tags($foruminfo['name']),
					'operator' => $windid,
				));
				$credit->sets($rt['authorid'],$creditset[$creditOpKey],false);

				$deluids[$rt['authorid']] = isset($deluids[$rt['authorid']]) ? $deluids[$rt['authorid']] + 1 : 1;
				if ($rt['aid']) {
					$_tids[$rt['tid']] = $rt['tid'];
					$_tids[$rt['pid']] = $rt['pid'];
				}
			}
			$credit->runsql();

			if ($_tids && $_pids) {
				$pw_attachs = L::loadDB('attachs', 'forum');
				$attachdb = $pw_attachs->getByTid($_tids,$_pids);
				require_once(R_P.'require/updateforum.php');
				delete_att($attachdb);
				pwFtpClose($ftp);
			}
			$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
			foreach ($deluids as $uid => $value) {
				$userService->updateByIncrement($uid, array(), array('postnum'=>-$value));
			}
			$db->update("DELETE FROM $pw_posts WHERE $sql AND pid IN(".S::sqlImplode($dels).")");
			Perf::gatherInfo('deletePosts', array('_tablename'=>$pw_posts, 'pid'=>$dels));
		}
		adminmsg('operate_success');
		/**End modify by liaohu 2010-06-21*/
	}
}
?>