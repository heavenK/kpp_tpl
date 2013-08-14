<?php
defined('IN_KEKE') or exit('Access Denied');
$nav_active_index = 'task';
$basic_url = 'index.php?do=task&task_id='.$task_id;
$task_obj = preward_task_class::get_instance($task_info);
$task_info= $task_obj->_task_info;
$model_id = $task_info ['model_id'];
$payitem_str = $task_obj->get_payitem_str();
if(!$uid&&strstr(' '.$payitem_str,'seohide')){
	kekezu::show_msg("拒绝访问",$back_url,3,"该任务在您登录后才可以访问");
}
$time_obj = new preward_time_class();
$time_obj->validtaskstatus();
$cover_cash = kekezu::get_cash_cove('',true);
$trust_mode=$task_obj->_trust_mode;
$process_can = $task_obj->process_can();
$process_desc = $task_obj->process_desc();
$status_arr = $task_obj->get_task_status();
$task_status = $task_obj->_task_status;
$delay_rule = $task_obj->_delay_rule;
$task_config = $task_obj->_task_config;
$delay_total = sizeof($delay_rule);
$delay_count=intval($task_info['is_delay']);
$task_obj->plus_view_num();
$stage_desc = $task_obj->get_task_stage_desc (); 
$wiki_cash = floatval($task_info['single_cash'])*(100-floatval($task_info['profit_rate']))/100 ; 
$time_desc = $task_obj->get_task_timedesc (); 
$related_task = $task_obj->get_task_related ();
$if_can_hand = intval($task_obj->check_work_if_standard('hand'));
$max_work_num = $task_obj->get_work_count('max');
$browing_history = $task_obj->browing_history($task_id,$task_info['task_cash']."元",$task_info['task_title']);
$show_payitem = $task_obj->show_payitem();
$sub_task_user_level =$g_info = $task_obj->_g_userinfo;
$bid_count = $task_obj->get_work_count ( "work_status=6" );
if($task_config['task_rate'] > 0){
	$cash = $task_info['task_cash'] * ( 1 - $task_config['task_rate']/100 ) ;
}else{
	$cash = $task_info['task_cash'];
}
switch ($op){
	case 'message':
		$title=$_lang['send_msg'];
		if($sbt_edit){
			$task_obj->send_message($title, $tar_content, $to_uid, $to_username,'','json');			
		}else{
			require keke_tpl_class::template('message');			
		}
		die();
		break;
	case 'reqedit':
       if($task_info['ext_desc']){
		$title = $_lang['edit_supply_demand'];
		}else{
		$title =$_lang['supply_demand'];
		}
		if ($sbt_edit) {
			$task_obj->set_task_reqedit ( $tar_content, '', 'json' );
		} else
			$ext_desc = $task_info ['ext_desc'];
		require keke_tpl_class::template ( 'task/task_reqedit' );
		die ();
		break;
	case "taskdelay" : 
		$title = $_lang['task_delay'];
		if($sbt_edit){
			$task_obj->set_task_delay($delay_day, $delay_cash,'','json');
		}else{
			$min_cash = intval($task_config['min_delay_cash']);
			$max_day  = intval($task_config['max_delay']);
			$this_min_cash = intval($delay_rule[$delay_count]['defer_rate']*$task_info['task_cash']/100);
			$min_cash>$this_min_cash and $real_min = $min_cash or $real_min = $this_min_cash;
			$credit_allow =  intval($kekezu->_sys_config ['credit_is_allow']);
			require keke_tpl_class::template("task/task_delay");
		}		
		die();
		break;
	case "work_hand":
		$title=$_lang['hand_work'];
		if($sbt_edit){			
			$task_obj->work_hand($tar_content, $file_ids,$workhide);
		}else{			
			$workhide_exists = keke_payitem_class::payitem_exists($uid,'workhide','work');
			keke_payitem_class::payitem_cost ('payitem_cost', $uid, 'task', 'spend', $task_id, $task_id );
			$have_workhide = $task_obj->check_work_hide();
			require keke_tpl_class::template ( 'task/reward_work' );
		}		
		die();
		break;
	case "report":
		$transname = keke_report_class::get_transrights_name($type);
		$title = $transname.$_lang['submit'];
		if($obj == 'task'){
			$report_reason = keke_report_class::get_report_task_reason();
		}elseif ($obj == 'work'){
			$report_reason = keke_report_class::get_report_work_reason();
		}
		if($sbt_edit){
			$task_obj->set_report ( $obj, $obj_id, $to_uid,$to_username, $type, $file_url, $tar_content,$slt_reason);
		}
			require keke_tpl_class::template("report");
		die();
		break;	
	case "work_choose":
		$work_status = $task_obj->get_work_status();
		$title = '选择'.$work_status[$to_status];
		if($sbt_edit){
			$task_obj->work_choose ( $work_id, $to_status);
		}else{
			$work_info = $task_obj->get_task_work($work_id,'');
			require keke_tpl_class::template ( 'task/work_choose' );
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
}
switch ($view){
	case "work":
		$search_condit = $task_obj->get_search_condit();
		$work_status = $task_obj->get_work_status();
		intval ( $page ) and $p ['page'] = intval ( $page ) or $p ['page']='1';
		intval ( $page_size ) and $p ['page_size'] = intval ( $page_size ) or $p['page_size']=10;
		$p['url'] = $basic_url."&view=work&page_size=".$p ['page_size']."&page=".$p ['page'];
		if($st){
			$p['url'] .="&st=".$st;
		}
		if($ut){
			$p['url'] .="&ut=".$ut;
		}
		$p ['anchor'] = '#work_list';
		$w['work_id'] = $work_id;
		$w['work_status'] = $st;
		$w['user_type']   = $ut;
		$work_arr = $task_obj->get_work_info ($w, " a.work_id desc ", $p ); 
		$pages = $work_arr ['pages'];
		$work_info = $work_arr ['work_info'];	
		$mark      = $work_arr['mark'];
		$has_new  = $task_obj->has_new_comment($p ['page'],$p ['page_size']);			
		break;
	case "comment":
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
	case "base":
	default:
		$task_file = $task_obj->get_task_file();
		$kekezu->init_prom();
		$can_prom = $kekezu->_prom_obj->is_meet_requirement ( "bid_task", $task_id );
		if($task_info['task_status']==8){
			$list_work = db_factory::query(' select DISTINCT(uid) uid,username from '.TABLEPRE.'witkey_task_work where task_id='.intval($task_id).' and work_status in (4,6)');
		}
		if($task_info['task_status']==2&&$task_info['uid']==$uid){
			$item_list= keke_payitem_class::get_payitem_config ( 'employer', null, null, 'item_id' );
		}
		break;
}
if($union_hand){
	require keke_tpl_class::template ( "task_info");
}else{
	require keke_tpl_class::template ( "task/" . $model_info ['model_code'] . "/tpl/" . $_K ['template'] . "/task_info" );
}
