<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

if(!$uid) kekezu::show_msg("您还没有登录，无法发话题！",'index.php?do=login',3,'','fail');

$thread_right_arr = db_factory::query ( "select * from ".TABLEPRE."forum_thread where flag like '%r%' and status=2 and isShow=1 limit 0,10");

if($tid && $type =='thread'){
	$thread_info = db_factory::get_one(" select * from ".TABLEPRE."forum_thread where tid=".$tid);
}
if($pid){
	$thread_info = db_factory::get_one(" select * from ".TABLEPRE."forum_post where pid=".$pid);
}

if($sbt_edit){
	
	$url = 'index.php?do=forum&view=post&type=thread&type_id='.$type_id;
	
	if($type == 'post') $url = 'index.php?do=forum&view=show&tid='.$tid;
	
	$url_success = 'index.php?do=forum&view=show&tid=';
	
	if(!isset($type_id) || !isset($type)) kekezu::show_msg("来路不正确！",$url,3,'','error');
	
	if(!$content)	kekezu::show_msg("内容不可以为空！",$url,3,'','error');
	
	$no_keywords = explode('|',$basic_config['no_keywords']);
	
	foreach($no_keywords as $val){
		
		$title = str_replace($val,'***',$title);
		$content = str_replace($val,'***',$content);
	}
	
	
	if($type == 'thread'){
		if(!$title) kekezu::show_msg("标题不可以为空！",$url,3,'','error');
		
		if($tid){
			$res = db_factory::execute(" update ".TABLEPRE."forum_thread set title='".$title."', content='".$content."' where tid=".$tid);
				
			if($res)	$res1 = db_factory::execute(" update ".TABLEPRE."forum_post set title='".$title."', content='".$content."' where tid=".$tid." and floor=1");
			else	kekezu::show_msg("修改失败！",$url,3,'','error');
			
			if($res1)	kekezu::show_msg("修改成功！",$url_success.$tid,3,'','success');
			else	kekezu::show_msg("修改失败！",$url,3,'','error');
			
		}else{
		
			$thread = db_factory::get_one(" select * from ".TABLEPRE."forum_thread order by tid desc limit 0,1");
			
			if($thread) $tid = $thread['tid']+1;
			else $tid = 1;
			
			$res = db_factory::execute(" insert into ".TABLEPRE."forum_thread (tid,type_id, title, content, uid, username, pub_time, views, reply, isShow, status) value(".$tid.",".$type_id.",'".$title."','".$content."',".$uid.",'".$username."',".time().",0,0,1,2)");
				
			if($res)	$res1 = db_factory::execute(" insert into ".TABLEPRE."forum_post (tid, type_id, title, content, uid, username, pub_time, status, floor) value(".$tid.",".$type_id.",'".$title."','".$content."',".$uid.",'".$username."',".time().",2, 1)");
			else	kekezu::show_msg("发布失败！",$url,3,'','error');
			
			if($res1)	kekezu::show_msg("发布成功！",$url_success.$tid,3,'','success');
			else	kekezu::show_msg("发布失败！",$url,3,'','error');
		}
	}
	if($type == 'post'){
		
		if($pid){
			$res = db_factory::execute(" update ".TABLEPRE."forum_post set content='".$content."' where pid=".$pid);
			if($res)kekezu::show_msg("修改成功！",$url_success.$tid,1,'','success');
			else kekezu::show_msg("修改失败！",$url,3,'','error');
		}else{
			
			$thread_first = db_factory::get_one(" select * from ".TABLEPRE."forum_post where tid=".$tid ." order by floor desc limit 0,1");
			
			if($thread_first)	$res1 = db_factory::execute(" insert into ".TABLEPRE."forum_post (tid, type_id, content, uid, username, pub_time, status, floor) value(".$tid.",".$type_id.",'".$content."',".$uid.",'".$username."',".time().",2, ".($thread_first['floor']+1).")");
			else	kekezu::show_msg("主题不存在！",$url,3,'','error');
			
			if($res1)	{
				db_factory::execute(" update ".TABLEPRE."forum_thread set reply=reply+1 where tid=".$tid);
				kekezu::show_msg("发布成功！",$url_success.$tid,1,'','success');
			}
			else	kekezu::show_msg("发布失败！",$url,3,'','error');
		}
	}
}

require keke_tpl_class::template ( "forum/" . $view );
