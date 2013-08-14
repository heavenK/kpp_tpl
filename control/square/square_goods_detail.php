<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$service_id and $service_info = db_factory::get_one(sprintf("select * from %switkey_free_service where service_id=%d",TABLEPRE,intval($service_id)));
if(!$service_info){
	kekezu::show_msg ( $_lang ['friendly_notice'], 'index.php?do=square', 2, '数据不存在','alert_error' );
}else{
	$service_info = db_factory::get_one(sprintf("select * from %switkey_free_service where service_id=%d",TABLEPRE,intval($service_id)));
	$page_title=$service_info['service_title'].'广场-服务&商品详细'.'- '.$_K['html_title'];
	$page_keyword = '广场-服务&商品详细'.'- '.$_K['html_title'];
	$page_description = kekezu::cutstr($service_info['task_desc'],200);
	$service_info['service_file'] and $service_file = db_factory::query(sprintf("select * from %switkey_file where file_id in (%s)",TABLEPRE,$service_info['service_file']));
	$service_info['service_pic'] and $service_pic = db_factory::query(sprintf("select * from %switkey_file where file_id in (%s)",TABLEPRE,$service_info['service_pic']));
	$wiki_info = kekezu::get_user_info($service_info['uid']);
	$uid and $is_favor = db_factory::get_one(sprintf("select favor_id  from %switkey_free_favor where obj_id = '%d' and obj_type ='free_service' and uid= %d;",TABLEPRE,$service_id,$uid));
	$sina_app_id = $kekezu->_sys_config['sina_app_id'];
	$sohu_app_id = $kekezu->_sys_config['sohu_app_id'];
	$share_title = $task_info['task_title'];
	strtolower(CHARSET)=='gbk' and $utitle = urlencode(kekezu::gbktoutf($share_title)) or $utitle = urlencode($share_title);
	$seller_aid = keke_user_mark_class::get_user_aid ( $wiki_info['uid'], '2', null, '1' );
	$level        = unserialize($wiki_info['seller_level']);
	$good_rate  = get_witkey_good_rate($wiki_info);
	$comment_obj = keke_comment_class::get_instance('free_service');
	$url = "index.php?do=square&view=goods_detail&service_id=$service_id";
	intval($page) or $page = 1;
	$comment_arr = $comment_obj->get_free_comment_list($service_id, $url, $page);
	$comment_data = $comment_arr['data'];
	$comment_page = $comment_arr['pages'];
	$comment_count = count($comment_data);
	$reply_arr = $comment_obj->get_free_reply_info($service_id);
	switch ($op){
		case "reply": 
			$comment_arr = array("obj_id"=>$service_id,"origin_id"=>$service_id,"obj_type"=>"free_service","p_id"=>$pid,
			"uid"=>$uid, "username"=>$username,"content"=>$content,"on_time"=>time(),"status"=>1);
			$res = $comment_obj->save_free_comment($comment_arr,$service_id,1);
			if(!in_array($res,array(-1,-2,-3))){
				$v1 =  $comment_obj->get_free_comment_info($res);
				$tmp ='replay_free_comment';
				require keke_tpl_class::template ( "task/task_comment_reply" );
			}else{
				echo $res;
			}
			die();
			break;
		case "add": 
			$comment_arr = array("obj_id"=>$service_id,"origin_id"=>$task_id,"obj_type"=>"free_service","p_id"=>0,
			"uid"=>$uid, "username"=>$username,"content"=>$content,"on_time"=>time(),"status"=>1);
			$res = $comment_obj->save_free_comment($comment_arr,$service_id);
			if(!in_array($res,array(-1,-2,-3))){
				$v = $comment_obj->get_free_comment_info($res);
				db_factory::execute(sprintf("update %switkey_free_service set leave_num = ifnull(leave_num,0)+1 where service_id=%d",TABLEPRE,$service_id));
				kekezu::update_weibo_data($service_id,'free_service','free_service',$service_info['service_title'],$service_info[uid],$service_info[username],intval($service_info[leave_num])+1,intval($service_info[focus_num]),intval($service_info[work_num]),'leave',0,$uid,$username);
				$tmp ='pub_free_comment';
				require keke_tpl_class::template ( "task/task_comment_reply" );
			}else{
				echo $res;
			}
			die();
			break;
		case "comment_del":
			$comment_info = $comment_obj->get_free_comment_info($comment_id);
			if( $uid == $comment_info[uid]){
				$res = $comment_obj->del_free_comment($comment_id,$service_id,$comment_info['p_id']);
			}
			$res and kekezu::keke_show_msg("", $_lang['delete_success'],"","json") or kekezu::keke_show_msg("",$_lang['system_is_busy'],"error","json");
			break;
	}
}
function get_witkey_good_rate($user_info){
	$st = $user_info['seller_total_num'];
	return $st?(number_format($user_info['seller_good_num']/$st,2)*100).'%':'0%';
}
require keke_tpl_class::template ( "tpl/" . $_K ['template'] . "/" . $do . "/" .$do."_". $view );