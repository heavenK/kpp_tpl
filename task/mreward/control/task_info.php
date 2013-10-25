<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$nav_active_index = 'task';
$basic_url = "index.php?do=task&task_id=$task_id"; 
$task_obj = mreward_task_class::get_instance ( $task_info );
$task_info= $task_obj->_task_info;
$payitem_str = $task_obj->get_payitem_str();
if(!$uid&&strstr(' '.$payitem_str,'seohide')){
	kekezu::show_msg("拒绝访问",$back_url,3,"该任务在您登录后才可以访问");
}
if(strstr($payitem_str,'search')) banspider();
$cover_cash = kekezu::get_cash_cove('',true);
$task_config =$task_obj->_task_config;
$model_id = $task_info ['model_id'];
$task_status = $task_obj->_task_status;
$trust_mode=$task_obj->_trust_mode;
$sub_task_user_level =$g_info = $task_obj->_g_userinfo;
$indus_arr = $kekezu->_indus_c_arr; 
$indus_p_arr = $kekezu->_indus_p_arr; 
$status_arr = $task_obj->_task_status_arr; 
$time_desc = $task_obj->get_task_timedesc (); 
$stage_desc = $task_obj->get_task_stage_desc (); 
$related_task = $task_obj->get_task_related ();
$delay_rule = $task_obj->_delay_rule;
$delay_total = sizeof($delay_rule);
$delay_count=intval($task_info['is_delay']);
$process_can = $task_obj->process_can (); 
$process_desc = $task_obj->process_desc (); 
$task_obj->plus_view_num();
$work_prize = $task_obj->get_work_prize();
$task_prize_arr = $task_obj->get_task_prize();
$task_obj->task_tg_timeout();
$task_obj->task_xg_timeout();
$task_obj->task_gs_timeout();
$browing_history = $task_obj->browing_history($task_id,$task_info['task_cash']."元",$task_info['task_title']);
$show_payitem = $task_obj->show_payitem();
$prize_c = $task_obj->get_prize_date();
$wiki_priv = $task_obj->_priv; 
$g_info = $task_obj->_g_userinfo;
$prize_work_date = $task_obj->get_prize_work_count();



$date_prv = date("Y-m-d",time());
$work_status = $task_obj->get_work_status ();
intval ( $page ) and $p ['page'] = intval ( $page ) or $p ['page']='1';
intval ( $page_size ) and $p ['page_size'] = intval ( $page_size ) or $p['page_size']='10';
$p['url'] = $basic_url."&view=work&page_size=".$p ['page_size']."&page=".$p ['page'];
if($st){
	$p['url'] .="&st=".$st;
}
if($ut){
	$p['url'] .="&ut=".$ut;
}
$p ['anchor'] = '';
$w['work_id'] = $work_id;
$w['work_status'] = $st;
$w['user_type']   = $ut;
$work_arr = $task_obj->get_work_info ($w, " work_id desc ", $p ); 
$pages = $work_arr ['pages'];
$work_info = $work_arr ['work_info'];
$mark      = $work_arr['mark'];
$has_new  = $task_obj->has_new_comment($p ['page'],$p ['page_size']);

$comment_obj = keke_comment_class::get_instance('task'); 
$url = $basic_url."&view=comment";
intval($page) or $page = 1;
$comment_arr = $comment_obj->get_comment_list($task_id, $url, $page); 
$comment_data = $comment_arr['data'];
$comment_page = $comment_arr['pages'];
$reply_arr = $comment_obj->get_reply_info($task_id);
foreach($comment_data as $key => $val){
	if($key == 0) $ids = $val['uid'];
	else $ids .= ','.$val['uid'];
}

if($ids){
	$space_list = db_factory::query(' select * from '.TABLEPRE.'witkey_space where uid in ('.$ids.")");	
	
	foreach($space_list as $key=>$val){
		$member_info[$val['uid']] = $val;
	}
	
}


$auth_html_sql = sprintf('select a.`auth_code`,a.`auth_title`,a.`auth_small_ico`,'
		.' a.`auth_small_n_ico`,a.`auth_open`,a.`listorder`,b.`auth_status`,b.`uid` from %switkey_auth_item a '
		.' left join %switkey_auth_record b on a.`auth_code`=b.`auth_code`  order by a.`listorder` ',TABLEPRE,TABLEPRE);
$rs_rz = db_factory::query($auth_html_sql);
function rz_show($uid){
	global $rs_rz,$_lang;
	$img_list='';
	$first = $_lang['certification_status'].'：';
	foreach ( $rs_rz as $c ) {
		if(empty($c['uid'])||empty($c['auth_status'])||$c['uid']!=$uid||$c ['auth_open']==false)
		{
		}else{
			$str = '';
			$str .= '<img src="';
			$str .= $c['auth_status'] ? $c ['auth_small_ico'] : $c ['auth_small_n_ico'];
			$str .= '" align="absmiddle" title="' . $c ['auth_title'];
			$str .= $c['auth_status'] ? $_lang['has_pass'] : $_lang['not_pass'];
			$str .= '">&nbsp;';
			$img_list .= $str;
		}
	}
	if($img_list)
	 $img_list =$img_list;
	return $img_list;
}



// 允许报名
$allow_baomings = array();
$work_lists = db_factory::query("select * from ".TABLEPRE."witkey_task_work where task_id=".$task_id." and work_status>0 ");

$own_user = array();
foreach($work_lists as $key2 => $val2){
	$own_user[] = $val2['uid'];
}

foreach($work_info as $key => $val){
	
	$allow_baomings[$key] = $val['uid'];
	foreach($work_lists as $key1 => $val1){
		if($val['uid'] == $val1['uid']) {
			unset($allow_baomings[$key]);
			break;
		}
	}
}
//
//报名人员
$baoming_list = db_factory::query(' select uid from '.TABLEPRE.'witkey_task_baoming where task_id ='.$task_id);	
$baomings = array();
foreach($baoming_list as $key => $val){
	$baomings[$key] = $val['uid'];
}
//

$vote_times = time()-$task_info['vote_start'];
$days=round(($vote_times)/3600/24) ;

if($days > $basic_config['vote_time'] && $task_info['is_vote'] == 2){
	
	db_factory::execute ( sprintf ( " update %switkey_task set is_vote=1 where task_id ='%d'", TABLEPRE, $task_id ) );


	$works_list = db_factory::query ( sprintf ( "select * from %switkey_task_work where task_id=%d and vote_num > 0 order by vote_num desc limit 0,2", TABLEPRE, $task_id) );
	
	$works_list_count = db_factory::get_count( sprintf ( "select count(work_id) count from %switkey_task_work where task_id=%d and vote_num > 0 order by vote_num desc limit 0,2", TABLEPRE, $task_id) );
	foreach($works_list as $key => $val){
		keke_finance_class::cash_in($val['uid'], floatval(0),intval($basic_config['baoming_credit']),'vote_win','','vote_win');

		if($task_config['task_rate'] > 0){
			$win_cash = $task_info['task_cash'] * $task_config['task_rate']/100  ;
			$win_cash = floor($win_cash/$works_list_count);
			
			keke_finance_class::cash_in($val['uid'], intval($win_cash),intval(0),'vote_win','','vote_win');
		}
		
		db_factory::execute ( 'update ' . TABLEPRE . 'witkey_task_work set vote_position='.($key+1).' where work_id ='.$val['work_id'] );
	}
}

switch ($op) {
	case "reqedit" : 
        if($task_info['ext_desc']){
		$title = $_lang['edit_supply_demand'];
		}else{
		$title =$_lang['supply_demand'];
		}
		if ($sbt_edit) {
			$task_obj->set_task_reqedit ( $tar_content, '', 'json' );
		} else {
			$ext_desc = $task_info ['ext_desc'];
			require keke_tpl_class::template ( 'task/task_reqedit' );
		}
		die ();
		break;
	case "taskdelay" : 
		$title = $_lang['task_delay'];
		if($sbt_edit){
			$task_obj->set_task_delay($delay_day, $delay_cash,'','json');
		}else {
			$min_cash = intval($task_config['min_delay_cash']);
			$max_day  = intval($task_config['max_delay']);
			$this_min_cash = intval($delay_rule[$delay_count]['defer_rate']*$task_info['task_cash']/100);
			$min_cash>$this_min_cash and $real_min = $min_cash or $real_min = $this_min_cash;
			$credit_allow =  intval($kekezu->_sys_config ['credit_is_allow']);
			require keke_tpl_class::template("task/task_delay");
		}
		die();
		break;
	case "work_hand" : 
		$title = $_lang['hand_work'];
		if($sbt_edit){
			if($user_info['credit'] < $hand_credit)	kekezu::show_msg ( "您的豆币不足，无法提交稿件！", "index.php?do=task&task_id=" . $task_info[task_id], 3, "提交失败", "alert_error" );
			$task_obj->work_hand ( $tar_content, $file_ids,$workhide,$qq,$mobile,$hand_credit);
		}else {
			$workhide_exists = keke_payitem_class::payitem_exists($uid,'workhide','work');
			$have_workhide = $task_obj->check_work_hide();
			require keke_tpl_class::template ( 'task/reward_work' );
		}
		die();
		break;
	case "work_choose" : 
		$work_status = $task_obj->get_work_status();
		$title = '选择'.$work_status[$to_status];
		if($sbt_edit){
			$hand_credit = $basic_config['hand_credit']*$task_info['task_cash']*$basic_config['selected_credit'];
			$get_credit = $basic_config['selected_suc_credit']*$task_info['task_cash'];
			$task_obj->work_choose ( $work_id, $to_status, false, $hand_credit, $get_credit);
		}else{
			$work_info = $task_obj->get_task_work($work_id,'');
			require keke_tpl_class::template ( 'task/work_choose' );
		}
		die();
		break;
	case "start_vote" : 
		$task_obj->start_vote('','json');
		break;
	case "work_vote" : 
		$task_obj->set_task_vote($work_id,'','json');
		break;
	case "report" : 
		$transname = keke_report_class::get_transrights_name($type);
		if($obj == 'task'){
			$report_reason = keke_report_class::get_report_task_reason();
		}elseif ($obj == 'work'){
			$report_reason = keke_report_class::get_report_work_reason();
		}
		if($sbt_edit){
			$task_obj->set_report ( $obj, $obj_id, $to_uid,$to_username, $type, $file_url, $tar_content,$slt_reason);
		}else{
			require keke_tpl_class::template("report");
		}
			die();
		break;
	case "mark" :
		$title = $_lang['each_mark'];
		$model_code = $task_obj->_model_code;
		require S_ROOT.'control/mark.php';
		die();
		break;
	case "work_del":
		$task_obj->del_work($work_id,'','json');
		break;
	case "comment" : 
		switch ($obj_type) {
			case "task" :
				break;
			case "work" :
				$tar_content and $task_obj->set_work_comment ( $obj_type, $obj_id, $tar_content, $p_id, '', 'json' );
				break;
		}
		break;
	case "message" : 
		$title = $_lang['send_msg'];
		if ($sbt_edit) {
			$task_obj->send_message($title,$tar_content,$to_uid, $to_username,'','json');
		} else {
			require keke_tpl_class::template ( 'message' );
		}
			die ();
		break;
}
switch ($view) {
	case "work" :
		$search_condit = $task_obj->get_search_condit();
		$date_prv = date("Y-m-d",time());
		$work_status = $task_obj->get_work_status ();
		intval ( $page ) and $p ['page'] = intval ( $page ) or $p ['page']='1';
		intval ( $page_size ) and $p ['page_size'] = intval ( $page_size ) or $p['page_size']='10';
		$p['url'] = $basic_url."&view=work&page_size=".$p ['page_size']."&page=".$p ['page'];
		if($st){
			$p['url'] .="&st=".$st;
		}
		if($ut){
			$p['url'] .="&ut=".$ut;
		}
		$p ['anchor'] = '';
		$w['work_id'] = $work_id;
		$w['work_status'] = $st;
		$w['user_type']   = $ut;
		$work_arr = $task_obj->get_work_info ($w, " work_id desc ", $p ); 
		$pages = $work_arr ['pages'];
		$work_info = $work_arr ['work_info'];
		$mark      = $work_arr['mark'];
		$has_new  = $task_obj->has_new_comment($p ['page'],$p ['page_size']);
		break;
	case "comment" :
	$comment_obj = keke_comment_class::get_instance('task'); 
		$url = $basic_url."&view=comment";
		intval($page) or $page = 1;
		$comment_arr = $comment_obj->get_comment_list($task_id, $url, $page); 
		$comment_data = $comment_arr['data'];
		$comment_page = $comment_arr['pages'];
		$reply_arr = $comment_obj->get_reply_info($task_id);
	    switch ($op){
	    	case "reply": 
	    		$comment_arr = array("obj_id"=>$task_id,"origin_id"=>$task_id,"obj_type"=>"task","p_id"=>$pid,
	    		 "uid"=>$uid, "username"=>$username,"content"=>$content,"on_time"=>time()); 
	    		$res = $comment_obj->save_comment($comment_arr,$task_id,1); 
	    		if(!in_array($res,array(-1,-2,-3))){
	    			$v1 =  $comment_obj->get_comment_info($res);
	    			$tmp ='replay_comment';
	    			require keke_tpl_class::template ( "task/task_comment_reply" );
	    		}else{
	    			echo $res;
	    		}
	    		die();
	    		break;
	    	case "add": 
	    		$comment_arr = array("obj_id"=>$task_id,"origin_id"=>$task_id,"obj_type"=>"task",
	    		"uid"=>$uid, "username"=>$username,"content"=>$content,"on_time"=>time());
	    		$res = $comment_obj->save_comment($comment_arr,$task_id); 
	    		if(!in_array($res,array(-1,-2,-3))){
	    			$v = $comment_obj->get_comment_info($res);
	    			$tmp ='pub_comment';
	    			require keke_tpl_class::template ( "task/task_comment_reply" );
	    		}else{
	    			echo $res;
	    		}
	    		die();
	    		break;
	    	case "del": 
	    		$comment_info = $comment_obj->get_comment_info($comment_id);
	    		if( $uid ==ADMIN_UID||$user_info['group_id']==7){
	    			$res = $comment_obj->del_comment($comment_id,$task_id,$comment_info['p_id']);
	    		}else{
	    			kekezu::keke_show_msg("", $_lang['not_priv'],"error","json");
	    		}
	    		$res and kekezu::keke_show_msg("", $_lang['delete_success'],"","json") or kekezu::keke_show_msg("",$_lang['system_is_busy'],"error","json");
	    		break;	
	    } 
		break;
	case "mark":
		$mark_count = $task_obj->get_mark_count();
		intval ( $page ) and $p ['page'] = intval ( $page ) or $p ['page']='1';
		intval ( $page_size ) and $p ['page_size'] = intval ( $page_size ) or $p['page_size']='10';
		$p['url'] = $basic_url."&view=mark&page_size=".$p ['page_size']."&page=".$p ['page'];
		$p ['anchor'] = '';
		$w['model_code'] = $model_code;
		$w['origin_id']   = $task_id;
		$w['mark_status'] = $st;
		$w['mark_type'] = $ut;
		$mark_arr = keke_user_mark_class::get_mark_info($w,$p,' mark_id desc ',"mark_status>0");
		$mark_info = $mark_arr['mark_info'];
		$pages     = $mark_arr['pages'];
		break;
	case "base" :
	default :
		$task_file = $task_obj->get_task_file (); 
		$kekezu->init_prom();
		$can_prom = $kekezu->_prom_obj->is_meet_requirement ( "bid_task", $task_id );
		if($task_info['task_status']==8){
			$list_work = db_factory::query(' select uid,username from '.TABLEPRE.'witkey_task_work where task_id='.intval($task_id).' and work_status in (1,2,3)');
		}
		if($task_info['task_status']==2&&$task_info['uid']==$uid){
			$item_list= keke_payitem_class::get_payitem_config ( 'employer', null, null, 'item_id' );
		}
}
if($union_hand){
	require keke_tpl_class::template ( "task_info");
}else{
	require keke_tpl_class::template ( "task/" . $model_info ['model_code'] . "/tpl/" . $_K ['template'] . "/task_info" );
}
