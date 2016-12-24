<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once ACLOUD_PATH . '/system/core/sys.core.dao.php';
class ACloud_Sys_Config_Dao_CreateTable extends ACloud_Sys_Core_Dao {
	
	function initTables() {
		$result = $this->fetchOne ( "SHOW TABLES LIKE 'pw_acloud_extras'" );
		if ($result)
			return true;
		$this->createTables ();
		$this->createTableRows ();
		return true;
	}
	
	function createTables() {
		$sqls = $this->_getTables ();
		foreach ( $sqls as $tableName => $sql ) {
			$result = $this->fetchOne ( "SHOW TABLES LIKE '{$tableName}'" );
			if ($result)
				continue;
			$this->query ( $sql );
		}
		return true;
	}
	
	function checkTables() {
		$sqls = $this->_getTables ();
		$tmp = array ();
		foreach ( $sqls as $tableName => $sql ) {
			$result = $this->fetchOne ( "SHOW TABLES LIKE '{$tableName}'" );
			$tmp [$tableName] = ($result) ? 1 : 0;
		}
		return $tmp;
	}
	
	function createTableRows() {
		$rows = $this->_getTableRows ();
		foreach ( $rows as $tableName => $sqls ) {
			$result = $this->fetchOne ( "SHOW TABLES LIKE '{$tableName}'" );
			if ($result)
				foreach ( $sqls as $sql )
					$this->query ( $sql );
		}
		return true;
	}
	
	function _getTables() {
		$db = $this->getDB ();
		$version = ($db->server_info () >= '4.1') ? 'ENGINE=MyISAM' : 'TYPE=MyISAM';
		return array ('pw_acloud_keys' => "create table pw_acloud_keys(
					id int(10) unsigned not null auto_increment,
					key1 char(128) not null default '',
					key2 char(128) not null default '',
					key3 char(128) not null default '',
					key4 char(128) not null default '',
					key5 char(128) not null default '',
					key6 char(128) not null default '',
					created_time int(10) unsigned not null default '0',
					modified_time int(10) unsigned not null default '0',
					primary key (id)
				)$version", 'pw_acloud_apps' => "create table pw_acloud_apps(
					app_id char(22) not null default '',
					app_name varchar(60) not null default '',
					app_token char(128) not null default '',
					created_time int(10) not null default '0',
					modified_time int(10) not null default '0',
					primary key (app_id)
				)$version", 'pw_acloud_app_configs' => "create table pw_acloud_app_configs(
					app_id char(22) not null default '',
					app_key varchar(30) not null default '',
					app_value text,
					app_type tinyint(3) not null default '1',
					created_time int(10) not null default '0',
					modified_time int(10) not null default '0',
					unique key (app_id,app_key)
				)$version", 'pw_acloud_extras' => "create table  pw_acloud_extras(
					ekey varchar(100) not null default '',
					evalue text,
					etype tinyint(3) not null default '1',
					created_time int(10) unsigned not null default '0',
					modified_time int(10) unsigned not null default '0',
					primary key (ekey)
				)$version", 'pw_acloud_sql_log' => "create table pw_acloud_sql_log (
				   id int(10) unsigned not null AUTO_INCREMENT,
				   log text,
				   created_time int(10) unsigned not null default '0',
				   primary key (id)
				)$version", 'pw_acloud_apis' => "create table pw_acloud_apis (
				  id int(10) unsigned not null AUTO_INCREMENT,
				  name varchar(255) not null default '',
				  template text,
				  argument varchar(255) not null default '',
				  argument_type varchar(255) not null default '',
				  fields varchar(255) not null default '',
				  status tinyint(3) not null default '0',
				  category tinyint(3) not null default '0',
				  created_time int(10) not null default '0',
				  modified_time int(10) unsigned not null default '0',
				  primary key (id),
				  unique key idx_name (name)
				)$version", 'pw_acloud_table_settings' => "create table pw_acloud_table_settings (
				   name varchar(255) not null default '',
				   status tinyint(3) not null default '0',
				   category tinyint(3) not null default '0',
				   primary_key varchar(20) not null default '',
				   created_time int(10) unsigned not null default '0',
				   modified_time int(10) unsigned not null default '0',
				   primary key (name)
				)$version" );
	}
	
	function _getTableRows() {
		$sqls = "";
		$sqls ['pw_acloud_keys'] [] = "REPLACE INTO pw_acloud_keys (id,key1,key2,key3,key4,key5,key6,created_time,modified_time) VALUES (1,'','','','','','',1330586406,1330586406)";
		$sqls ['pw_acloud_keys'] [] = "REPLACE INTO pw_acloud_keys (id,key1,key2,key3,key4,key5,key6,created_time,modified_time) VALUES (2,'','','','','','',1330586406,1330586406)";
		$sqls ['pw_acloud_extras'] [] = "REPLACE INTO pw_acloud_extras  SET `ekey` = 'ac_isopen' , `evalue` = '0'";
		$sqls ['pw_acloud_extras'] [] = "REPLACE INTO pw_acloud_extras  SET `ekey` = 'ac_ipcontrol' , `evalue` = '1'";
		$sqls ['pw_acloud_extras'] [] = "REPLACE INTO pw_acloud_extras  SET `ekey` = 'ac_apply_step' , `evalue` = '0'";
		$sqls ['pw_acloud_extras'] [] = "REPLACE INTO pw_acloud_extras  SET `ekey` = 'ac_apply_siteurl' , `evalue` = ''";
		$sqls ['pw_acloud_extras'] [] = "REPLACE INTO pw_acloud_extras  SET `ekey` = 'ac_apply_lasttime' , `evalue` = '0'";
		$sqls ['pw_acloud_apis'] [] = "REPLACE INTO `pw_acloud_apis` (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('general.thread.get', 'SELECT {fields} FROM pw_threads WHERE tid = {tid}', 'tid', '', 'tid,fid,author,authorid,subject,postdate,lastpost,hits,replies', '1', '2', '1331123306', '1331123306'),('general.thread.gets', 'SELECT {fields} FROM pw_threads WHERE tid IN ({tids})', 'tids', 'tids|array', 'tid,fid,author,authorid,subject,postdate,lastpost,hits,replies', '1', '2', '1331123306', '1331123306'),('general.thread.getByFid', 'SELECT {fields} FROM pw_threads WHERE fid = {fid} ORDER BY postdate DESC LIMIT {offset},{limit}', 'fid,offset,limit', '', 'tid,fid,author,authorid,subject,postdate,lastpost,hits,replies', '1', '2', '1331123306', '1331123306'),('general.thread.countByFid', 'SELECT COUNT(*) AS total FROM pw_threads WHERE fid = {fid}', 'fid', '', '', '1', '2', '1331123306', '1331123306'),('general.thread.getByFids', 'SELECT {fields} FROM pw_threads WHERE fid IN ({fids}) LIMIT {offset},{limit}', 'fids,offset,limit', 'fids|array', 'tid,fid,author,authorid,subject,postdate,lastpost,hits,replies', '1', '2', '1331123306', '1331123306'),('general.thread.countByFids', 'SELECT COUNT(*) AS total FROM pw_threads WHERE fid IN ({fids})', 'fids', 'fids|array', '', '1', '2', '1331123306', '1331123306'),('general.thread.getByUid', 'SELECT {fields} FROM pw_threads WHERE authorid = {uid} ORDER BY postdate DESC LIMIT {offset},{limit}', 'uid,offset,limit', '', 'tid,fid,author,authorid,subject,postdate,lastpost,hits,replies', '1', '2', '1331123306', '1331123306'),('general.thread.countByUid', 'SELECT COUNT(*) AS total FROM pw_threads WHERE authorid = {uid}', 'uid', '', '', '1', '2', '1331123306', '1331123306'),('general.thread.getByUids', 'SELECT {fields} FROM pw_threads WHERE authorid IN ({uids}) LIMIT {offset},{limit}', 'uids,offset,limit', 'uids|array', 'tid,fid,author,authorid,subject,postdate,lastpost,hits,replies', '1', '2', '1331123306', '1331123306'),('general.thread.countByUids', 'SELECT COUNT(*) FROM pw_threads WHERE authorid IN ({uids})', 'uids', 'uids|array', '', '1', '2', '1331123306', '1331123306'),('general.thread.latest.gets', 'SELECT {fields} FROM pw_threads ORDER BY postdate DESC LIMIT {offset},{limit}', 'offset,limit', '', 'tid,fid,author,authorid,subject,postdate,lastpost,hits,replies', '1', '2', '1331123306', '1331123306'),('general.thread.at.gets', 'SELECT {fields} FROM pw_threads WHERE uid = {uid} LIMIT {offset},{limit}', 'uid,offset,limit', '', '', '1', '2', '1331123306', '1331123306'),('general.member.get', 'SELECT {fields} FROM pw_members WHERE uid = {uid}', 'uid', '', 'uid,username,email,groupid,memberid,groups,icon,gender,regdate,signature,introduce,oicq,aliww,icq,msn,yahoo,site,location,honor,bday,lastaddrst,yz,timedf,style,datefm,t_num,p_num,attach,hack,newpm,banpm,msggroups,medals,userstatus,shortcut', '1', '2', '1331123306', '1331123306'),('general.member.getByName', 'SELECT {fields} FROM pw_members WHERE username = {username}', 'username', 'username|string', 'uid,username,email,groupid,memberid,groups,icon,gender,regdate,signature,introduce,oicq,aliww,icq,msn,yahoo,site,location,honor,bday,lastaddrst,yz,timedf,style,datefm,t_num,p_num,attach,hack,newpm,banpm,msggroups,medals,userstatus,shortcut', '1', '2', '1331123306', '1331123306'),('general.member.gets', 'SELECT {fields} FROM pw_members WHERE uid IN {uids}', 'uids', 'uids|array', 'uid,username,email,groupid,memberid,groups,icon,gender,regdate,signature,introduce,oicq,aliww,icq,msn,yahoo,site,location,honor,bday,lastaddrst,yz,timedf,style,datefm,t_num,p_num,attach,hack,newpm,banpm,msggroups,medals,userstatus,shortcut', '1', '2', '1331123306', '1331123306'),('general.member.favorites.getByUid', 'SELECT shortcut FROM pw_members WHERE uid = {uid}', 'uid', '', '', '1', '2', '1331123306', '1331123306'),('general.memberinfo.get', 'SELECT {fields} FROM pw_memberinfo WHERE uid = {uid}', 'uid', '', '', '1', '2', '1331123306', '1331123306'),('general.memberinfo.gets', 'SELECT {fields} FROM pw_memberinfo WHERE uid IN ({uids})', 'uids', 'uids|array', '', '1', '2', '1331123306', '1331123306'),('general.memberdata.get', 'SELECT {fields} FROM pw_memberdata WHERE uid = {uid}', 'uid', '', '', '1', '2', '1331123306', '1331123306'),('general.memberdata.gets', 'SELECT {fields} FROM pw_memberdata WHERE uid IN ({uids})', 'uids', 'uids|array', '', '1', '2', '1331123306', '1331123306'),('general.friend.all.gets', 'SELECT {fields} FROM pw_friends WHERE uid = {uid} LIMIT {offset},{limit}', 'uid,offset,limit', '', '', '1', '2', '1331123306', '1331123306'),('general.friend.all.count', 'SELECT COUNT(*) AS total FROM pw_friends WHERE uid = {uid}', 'uid', '', '', '1', '2', '1331123306', '1331123306'),('general.friend.attention.follow.gets', 'SELECT {fields} FROM pw_attention WHERE uid = {uid} LIMIT {offset},{limit}', 'uid,offset,limit', '', '', '1', '2', '1331123306', '1331123306'),('general.friend.attention.follow.count', 'SELECT COUNT(*) AS total FROM pw_attention WHERE uid = {uid}', 'uid', '', '', '1', '2', '1331123306', '1331123306'),('general.friend.attention.fan.gets', 'SELECT {fields} FROM pw_attention WHERE friendid = {uid} LIMIT {offset},{limit}', 'uid,offset,limit', '', '', '1', '2', '1331123306', '1331123306'),('general.friend.attention.fan.count', 'SELECT COUNT(*) AS total FROM pw_attention WHERE friendid = {uid}', 'uid', '', '', '1', '2', '1331123306', '1331123306'),('general.ms.message.get', 'SELECT {fields} FROM pw_ms_messages WHERE mid = {id}', 'id', '', '', '1', '2', '1331123306', '1331123306'),('general.ms.message.gets', 'SELECT {fields} FROM pw_ms_messages WHERE mid IN ({ids})', 'ids', 'ids|array', '', '1', '2', '1331123306', '1331123306'),('general.ms.relations.getsByUid', 'SELECT {fields} FROM pw_ms_relations WHERE categoryid = {categoryid} AND typeid = {typeid} AND uid = {uid} LIMIT {offset},{limit}', 'uid,categoryid,typeid,offset,limit', '', '', '1', '2', '1331123306', '1331123306'),('general.ms.relations.countByUid', 'SELECT COUNT(*) AS total FROM pw_ms_relations WHERE categoryid = {categoryid} AND typeid = {typeid} AND uid = {uid}', 'uid,categoryid,typeid', '', '', '1', '2', '1331123306', '1331123306'),('general.ms.relations.countByUidAndStatus', 'SELECT COUNT(*) AS total FROM pw_ms_relations WHERE categoryid = {categoryid} AND typeid = {typeid} AND status = {status} AND uid = {uid}', 'uid,categoryid,typeid,status', '', '', '1', '2', '1331123306', '1331123306'),('general.ms.replies.get', 'SELECT {fields} FROM pw_ms_replies WHERE id = {id}', 'id', '', '', '1', '2', '1331123306', '1331123306'),('general.ms.replies.gets', 'SELECT {fields} FROM pw_ms_replies WHERE id IN ({ids})', 'ids', 'ids|array', '', '1', '2', '1331123306', '1331123306'),('general.ms.replies.getByUid', 'SELECT {fields} FROM pw_ms_replies WHERE create_uid = {uid} LIMIT {offset},{limit}', 'uid,offset,limit', '', '', '1', '2', '1331123306', '1331123306'),('general.ms.replies.countByUid', 'SELECT COUNT(*) AS total FROM pw_ms_replies WHERE create_uid = {uid}', 'uid', '', '', '1', '2', '1331123306', '1331123306')";
		$sqls ['pw_acloud_apis'] [] = "REPLACE INTO `pw_acloud_apis` (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('customized.thread.get', 'getByTid', 'tid', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.getByUid', 'getByUid', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.latest.gets', 'getLatestThread', 'fids,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.latest.favoritesForum.gets', 'getLatestThreadByFavoritesForum', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.latest.followUser.gets', 'getLatestThreadByFollowUser', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.img.latest.gets', 'getLatestImgThread', 'fids,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.img.get', 'getThreadImgs', 'tid', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.topped.getByFid', 'getToppedThreadByFid', 'fid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.getByFid', 'getThreadByFid', 'fid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.at.gets', 'getAtThreadByUid', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.getbyTopic', 'getThreadByTopic', 'topic,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.send', 'postThread', 'uid,fid,subject,content', '', '', '1', '1', '1331123657', '1331123657'),('customized.post.gets', 'getPost', 'tid,sort,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.post.getByUid', 'getPostByUid', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.post.getByTidAndUid', 'getPostByTidAndUid', 'tid,uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.post.send', 'sendPost', 'tid,uid,title,content', '', '', '1', '1', '1331123657', '1331123657'),('customized.user.get', 'getByUid', 'uid', '', '', '1', '1', '1331123657', '1331123657'),('customized.user.icon.update', 'updateIcon', 'uid', '', '', '1', '1', '1331123657', '1331123657'),('customized.user.favoritesForum.gets', 'getFavoritesForumByUid', 'uid', '', '', '1', '1', '1331123657', '1331123657'),('customized.user.favoritesforum.add', 'addFavoritesForumByUid', 'uid,fid', '', '', '1', '1', '1331123657', '1331123657'),('customized.user.favoritesforum.delete', 'deleteFavoritesForumByUid', 'uid,fid', '', '', '1', '1', '1331123657', '1331123657'),('customized.user.login', 'userLogin', 'username,password', '', '', '1', '1', '1331123657', '1331123657'),('customized.user.register', 'userRegister', 'username,password,email', '', '', '1', '1', '1331123657', '1331123657'),('customized.user.updateemail', 'updateEmail', 'uid,email', '', '', '1', '1', '1331123657', '1331123657'),('customized.forum.all.get', 'getAllForum', '', '', '', '1', '1', '1331123657', '1331123657'),('customized.forum.get', 'getForumByFid', 'fid', '', '', '1', '1', '1331123657', '1331123657'),('customized.forum.child.getByFid', 'getChildForumByFid', 'fid', '', '', '1', '1', '1331123657', '1331123657'),('customized.message.unread.count', 'countUnreadMessage', 'uid', '', '', '1', '1', '1331123657', '1331123657'),('customized.message.gets', 'getMessageByUid', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.message.send', 'sendMessage', 'fromuid,touid,title,content', '', '', '1', '1', '1331123657', '1331123657'),('customized.message.reply', 'replyMessage', 'messageid,relationid,uid,content', '', '', '1', '1', '1331123657', '1331123657'),('customized.message.get', 'getMessageAndReply', 'messageid,relationid,uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.message.postmythread.gets', 'getReplyThreadMessage', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.friend.all.gets', 'getAllFriend', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.friend.all.search', 'searchAllFriend', 'uid,keyword,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.friend.follow.gets', 'getFollowByUid', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.friend.follow.add', 'addFollowByUid', 'uid,uid2', '', '', '1', '1', '1331123657', '1331123657'),('customized.friend.follow.delete', 'deleteFollowByUid', 'uid,uid2', '', '', '1', '1', '1331123657', '1331123657'),('customized.friend.fan.gets', 'getFanByUid', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('common.permissions.user.isbanned', 'isUserBanned', 'uid', '', '', '1', '0', '1331123657', '1331123657'),('common.permissions.user.readforum', 'readForum', 'uid,fid', '', '', '1', '0', '1331123657', '1331123657'),('common.search.hotwords.gets', 'getHotwords', '', '', '', '1', '0', '1331123657', '1331123657')";
		$sqls ['pw_acloud_apis'] [] = "REPLACE INTO `pw_acloud_apis` (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('customized.user.getByName', 'getByName', 'username', '', '', '1', '1', '1331123657', '1331123657')";
		
		$sqls ['pw_acloud_apis'] [] = "REPLACE INTO `pw_acloud_apis` (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('common.site.partitions.get', 'getTablePartitions', 'type', '', '', '1', '0', '1331123657', '1331123657')";
		$sqls ['pw_acloud_apis'] [] = "REPLACE INTO `pw_acloud_apis` (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('common.user.ban', 'banUser', 'uid', '', '', '1', '0', '1331123657', '1331123657')";
		$sqls ['pw_acloud_apis'] [] = "REPLACE INTO `pw_acloud_apis` (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('common.thread.shield', 'shieldThread', 'tid,fid', '', '', '1', '0', '1331123657', '1331123657')";
		$sqls ['pw_acloud_apis'] [] = "REPLACE INTO `pw_acloud_apis` (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('common.post.shield', 'shieldPost', 'pid,tid', '', '', '1', '0', '1331123657', '1331123657')";
		$sqls ['pw_acloud_apis'] [] = "REPLACE INTO `pw_acloud_apis` (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('common.attach.img.gets', 'getImgAttaches', 'aids', '', '', '1', '0', '1331123657', '1331123657')";
		$sqls ['pw_acloud_apis'] [] = "REPLACE INTO `pw_acloud_apis` (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('common.user.getIcons', 'getIconsByUids', 'uids', '', '', '1', '0', '1331123657', '1331123657')";
		$sqls ['pw_acloud_apis'] [] = "REPLACE INTO `pw_acloud_apis` (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('common.site.field.check', 'checkTableField', 'table,field', '', '', '1', '0', '1331123657', '1331123657')";
		$sqls ['pw_acloud_apis'] [] = "REPLACE INTO `pw_acloud_apis` (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('customized.thread.mobile.send', 'postMobileThread', 'uid,fid,subject,content,mobiletype', '', '', '1', '1', '1331123657', '1331123657')";
		$sqls ['pw_acloud_apis'] [] = "REPLACE INTO `pw_acloud_apis` (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('customized.post.mobile.send', 'sendMobilePost', 'tid,uid,title,content,mobiletype', '', '', '1', '1', '1331123657', '1331123657')";
		
		$sqls ['pw_acloud_table_settings'] [] = "REPLACE INTO `pw_acloud_table_settings` (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_threads', '1', '1', 'tid', '1331123657', '1331123657')";
		$sqls ['pw_acloud_table_settings'] [] = "REPLACE INTO `pw_acloud_table_settings` (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_members', '1', '1', 'uid', '1331123657', '1331123657')";
		$sqls ['pw_acloud_table_settings'] [] = "REPLACE INTO `pw_acloud_table_settings` (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_forums', '1', '1', 'fid', '1331123657', '1331123657')";
		$sqls ['pw_acloud_table_settings'] [] = "REPLACE INTO `pw_acloud_table_settings` (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_diary', '1', '1', 'did', '1331123657', '1331123657')";
		$sqls ['pw_acloud_table_settings'] [] = "REPLACE INTO `pw_acloud_table_settings` (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_posts', '1', '1', 'pid', '1331123657', '1331123657')";
		$sqls ['pw_acloud_table_settings'] [] = "REPLACE INTO `pw_acloud_table_settings` (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_colonys', '1', '1', 'id', '1331123657', '1331123657')";
		$sqls ['pw_acloud_table_settings'] [] = "REPLACE INTO `pw_acloud_table_settings` (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_attachs', '1', '1', 'aid', '1331123657', '1331123657')";
		$sqls ['pw_acloud_table_settings'] [] = "REPLACE INTO `pw_acloud_table_settings` (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_bbsinfo', '1', '1', 'id', '1331123657', '1331123657')";
		return $sqls;
	}
}