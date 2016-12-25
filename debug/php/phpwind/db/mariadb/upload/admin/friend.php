<?php
!function_exists('adminmsg') && exit('Forbidden');
$basename .= '&admintype='.$admintype;
//* include_once pwCache::getPath(D_P.'data/bbscache/inv_config.php');
pwCache::getData(D_P.'data/bbscache/inv_config.php');
require_once(R_P.'require/credit.php');
$nav =  $action ? array($action=>'class = current') : 'class=current';
if (empty($_POST['step'])) {
	if($admintype == 'invite'){
		if(empty($action)){
			ifcheck($inv_open,'open');
			ifcheck($inv_onlinesell,'onlinesell');
			$usergroup	= "";
			$num		= 0;
			foreach ($ltitle as $key => $value) {
				if ($key != 1 && $key != 2) {
					$checked = '';
					if (strpos($inv_groups,','.$key.',') !== false) {
						$checked = 'checked';
					}
					$num++;
					$htm_tr = $num%4 == 0 ?  '' : '';
					$usergroup .=" <li><input type='checkbox' name='groups[]' value='$key' $checked>$value</li>$htm_tr";
				}
			}
		}elseif($action == 'manager'){
			S::gp(array('page','type','username','receiver')); //搜索增加receiver@modify panjl@2010-11-2
			$sql =  '';
			$timediff = (int)($timestamp - $inv_days * 86400);
			$sel = array($type => 'selected');
			empty($type) && $type = '0';
			if ($type == '1') {
				$sql .= "AND i.ifused='0' AND i.createtime>=".$timediff;
			} elseif ($type == '2') {
				$sql .= "AND i.ifused='0' AND i.createtime<".$timediff;
			} elseif ($type == '3') {
				$sql .= "AND i.ifused='1'";
			}
			if($username){
				$sql .= ' AND m.username = '.S::sqlEscape($username);
			}
			if($receiver){ //搜索增加receiver@modify panjl@2010-11-2
				$sql .= ' AND i.receiver = '.S::sqlEscape($receiver);
			}
			$db_showperpage = 20;
			(!is_numeric($page) || $page<1) && $page = 1;
			$limit = S::sqlLimit(($page-1)*$db_showperpage,$db_showperpage);
			$rt    = $db->get_one("SELECT COUNT(*) AS sum FROM pw_invitecode i LEFT JOIN pw_members m USING(uid)  WHERE 1=1 $sql");
			$pages = numofpage($rt['sum'],$page,ceil($rt['sum']/$db_showperpage),"$basename&action=manager&type=$type&username=$username&");
			$sql .= ' ORDER BY i.createtime desc ';
			$query = $db->query("SELECT i.*,m.username FROM pw_invitecode i LEFT JOIN pw_members m USING(uid) WHERE 1=1 $sql $limit");
			$invdb = array();
			$i = 1;
			while ($rt = $db->fetch_array($query)) {
				$rt['used'] = '';
				if ($rt['ifused'] =='0' && $rt['createtime'] < $timediff){
					$rt['used'] = "<span class='gray'>已过期</span>";
				} elseif ($rt['ifused'] == '0' && $rt['createtime'] >= $timediff){
					$rt['used'] = "<span class='s3'  >未使用</span>";
				} elseif ($rt['ifused'] == '1'){
					$rt['used'] = "<span class='s2'  >已注册</span>";
				}
				$rt['num']=($page-1)*$db_showperpage+$i++;
				$rt['overtime'] = get_date(($rt['createtime'] + (int) $inv_days * 86400), 'Y-m-d H:i:s');
				$rt['type'] = $rt['type'] == 2 ? '<span class=\'s3\'  >批量生成</span>' : '<span class=\'s3\'  >用户购买</span>';
				$rt['createtime']=get_date($rt['createtime'],'Y-m-d H:i:s');
				$invdb[]=$rt;
			}
		}
	}elseif($admintype == 'propagateset'){
		if(empty($action)){
			ifcheck($inv_linkopen,'linkopen');
			ifcheck($inv_linktype,'linktype');
		}elseif($action == 'statics'){
			S::gp(array('page'));
			$db_showperpage = 20;
			(!is_numeric($page) || $page<1) && $page = 1;
			$limit = S::sqlLimit(($page-1)*$db_showperpage,$db_showperpage);
			$rt    = $db->get_one("SELECT count(distinct uid) as sum FROM pw_inviterecord ");
			$pages = numofpage($rt['sum'],$page,ceil($rt['sum']/$db_showperpage),"$basename&action=statics&");
			$query = $db->query("SELECT username,reward,unit, create_time,typeid,uid FROM pw_inviterecord a GROUP BY uid ORDER BY create_time DESC $limit");	 
			$invdb = $uids = array();
			while ($rt = $db->fetch_array($query)) {	
				$rt['create_time'] = get_date($rt['create_time'],'Y-m-d H:i:s');
				$rt['type'] = $rt['typeid'] ? '注册' : '访问';
				$rt['reward'] = $rt['reward'].$credit->cType[$rt['unit']];
				$uids[] = $rt['uid'];
				$invdb[$rt[uid]]=$rt;
			}
			// php4支持
			if (S::isArray($uids)) {
				$query = $db->query("SELECT uid,typeid,COUNT(*) AS visitnum FROM pw_inviterecord WHERE uid IN (" . S::sqlImplode($uids) . ") AND typeid = 0 GROUP BY uid");
				while ($rt = $db->fetch_array($query)) {
						$invdb[$rt[uid]]['visitnum'] = $rt['visitnum'];
				}
				$query = $db->query("SELECT uid,typeid,COUNT(*) AS registernum FROM pw_inviterecord WHERE uid IN (" . S::sqlImplode($uids) . ") AND typeid = 1 GROUP BY uid");
				while ($rt = $db->fetch_array($query)) {
						$invdb[$rt[uid]]['registernum'] = $rt['registernum'];
				}
			}
		}
	}
	include PrintEot('friend');exit;
}else{
	if($admintype == 'invite'){
		if($_POST['step'] == '2'){
			if ($action == 'generator') {
				S::gp(array('number'));
				if (!preg_match('/^\d+$/', $number)) adminmsg('生成数量必须为正整数',"$basename&action=generator");
				$number = (int) $number;
				!$number && adminmsg('生成的数量不能为空',"$basename&action=generator");
				$number > 100 && adminmsg('每次生成数量最多为100个',"$basename&action=generator");
				$invcode = $adminInfo = array();
				$userService = L::loadClass('UserService', 'user');
				$adminInfo = $userService->getByUserName($admin_name);
				for ($i = 0; $i < $number; $i++) {
					$invcode[] = array(randstr(16), $adminInfo['uid'], $timestamp, 2);
				}
				$db->update('INSERT INTO pw_invitecode (invcode, uid, createtime, type) VALUES ' . S::sqlMulti($invcode));
				adminmsg('操作完成', "$basename&action=generator");
			}
			S::gp(array('config','groups'),'P');
			if(!is_numeric($config['open'])) $config['open']=1;
			if(!is_numeric($config['days'])) $config['days']=10;
			if(!is_numeric($config['limitdays'])) $config['limitdays']=0;
			if(!is_numeric($config['costs'])) $config['costs']=1;
			if($config['onlinesell'] && $config['price'] == 0) adminmsg('支付金额不能为0！');
			if(is_array($groups)){
				$config['groups'] = ','.implode(',',$groups).',';
			} else {
				$config['groups'] = '';
			}
			foreach($config as $key=>$value){
				$db->pw_update(
					"SELECT hk_name FROM pw_hack WHERE hk_name=".S::sqlEscape("inv_$key"),
					"UPDATE pw_hack SET hk_value=".S::sqlEscape($value)."WHERE hk_name=".S::sqlEscape("inv_$key"),
					"INSERT INTO pw_hack SET hk_name=".S::sqlEscape("inv_$key").",hk_value=".S::sqlEscape($value)
				);
			}
			updatecache_inv();	
		}elseif($_POST['step'] == "3"){
			S::gp(array('selid'),'P');
			if (!$selid = checkselid($selid)) {
				adminmsg('operate_error');
			}
			$selid && $db->update("DELETE FROM pw_invitecode WHERE id IN ($selid)");		
		}
	}elseif($admintype == 'propagateset'){
		
		if($_POST['step'] == '2'){		
			S::gp(array('config'),'P');
			if(!is_numeric($config['linkopen'])) $config['linkopen']=0;
			if(!is_numeric($config['linktype'])) $config['linktype']=0;
			if(!is_numeric($config['linkscore'])) $config['linkscore']=0;			
			
			foreach($config as $key=>$value){
				$db->pw_update(
					"SELECT hk_name FROM pw_hack WHERE hk_name=".S::sqlEscape("inv_$key"),
					"UPDATE pw_hack SET hk_value=".S::sqlEscape($value)."WHERE hk_name=".S::sqlEscape("inv_$key"),
					"INSERT INTO pw_hack SET hk_name=".S::sqlEscape("inv_$key").",hk_value=".S::sqlEscape($value)
				);
			}
			updatecache_inv();	
		}
	}
	adminmsg('operate_success');	
}



?>