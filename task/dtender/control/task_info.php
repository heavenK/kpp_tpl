<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$nav_active_index = 'task';
$basic_url = "index.php?do=task&task_id=$task_id"; 
$task_obj = dtender_task_class::get_instance ( $task_info );
$task_info= $task_obj->_task_info;
$cover_id = $task_obj->_task_info['task_cash_coverage'];
$cover_cash = kekezu::get_cash_cove('',true);
$payitem_str = $task_obj->get_payitem_str();
if(!$uid&&strstr(' '.$payitem_str,'seohide')){
	kekezu::show_msg("拒绝访问",$back_url,3,"该任务在您登录后才可以访问");
}
$dtender_time_obj = new dtender_time_class();
$dtender_time_obj->validtaskstatus();
$sub_task_user_level =$g_info = $task_obj->_g_userinfo;
$task_config =$task_obj->_task_config;
$model_id = $task_info ['model_id'];
$task_status = $task_obj->_task_status;
$cash_cove = kekezu::get_cash_cove('dtender');
$task_info['task_covery'] = $cash_cove[$task_info['task_cash_coverage']]['cove_desc'];
$status_arr = $task_obj->_task_status_arr; 
$time_desc = $task_obj->get_task_timedesc (); 
$stage_desc = $task_obj->get_task_stage_desc (); 
$g_uid = $task_obj->_guid;
$g_info = $task_obj->_g_userinfo;
$related_task = $task_obj->get_task_related ();
$process_can = $task_obj->process_can (); 
$process_desc = $task_obj->process_desc (); 
$plan_status = $task_obj->get_plan_status(); 
$task_obj->plus_view_num();
$is_bided = intval($task_obj->check_bid());
$browing_history = $task_obj->browing_history($task_id,$task_info['task_covery'],$task_info['task_title']);
$show_payitem = $task_obj->show_payitem();
$loca = explode(",",$user_info['residency']); 
$is_report = db_factory::get_one(sprintf("select * from %switkey_report where origin_id = '%d'",TABLEPRE,$task_id));
switch ($op) {
	case "reqedit" : 
        if($task_info['ext_desc']){
		$title = $_lang['edit_supply_demand'];
		}else{
		$title =$_lang['supply_demand'];
		}
		if ($sbt_edit) {
			$task_obj->set_task_reqedit ( $tar_content, '', 'json' );
		} else{
			$ext_desc = $task_info ['ext_desc'];
			require keke_tpl_class::template ( 'task/task_reqedit' );
			die ();
		}
		break;
	case "work_hand" : 
		$title = '任务投标';
		if($sbt_edit){
			$province and $area = $province;
			$city and $area = $area.','.$city;	
			$area = kekezu::utftogbk($area);
			$task_obj->bid_hand($quote, $cycle, $area, $tar_content, $plan_amount, $start_time, $end_time, $plan_title,$bid_hide);
		}else{
			$workhide_exists = keke_payitem_class::payitem_exists($uid,'workhide','work');
			require keke_tpl_class::template ( "task/" . $model_info ['model_code'] . "/tpl/" . $_K ['template'] . "/dtender_work" );
			die();
		}
		break;
	case "work_edit":
		$title = kekezu::lang("edit_work");
		if($sbt_edit){
			$province and $area = $province;
			$city and $area = $area.','.$city.','.$area;
			$area = kekezu::utftogbk($area);
			$task_obj->bid_edit($bid_id, $quote, $cycle, $area, $tar_content, $plan_amount, $start_time, $end_time, $plan_title);
		}else{
			$bid_info = $task_obj->get_single_bid($bid_id);
			$base_info = $bid_info['bid_info'];			
			$plan_info = $bid_info['plan_info'];
			$loca = explode(",",$base_info['area']);
			require keke_tpl_class::template ( "task/" . $model_info ['model_code'] . "/tpl/" . $_K ['template'] . "/dtender_work" );
			die();
		}		
		break;
	case "work_choose" : 
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
	case "hosted_amount":
		$title=kekezu::lang("trust_reward");
		if($sbt_edit){
			$task_obj->hosted_amount();
		}else{
			$quote = $task_obj->_bid_info['quote'];
			$service_cash = $task_info['real_cash'];
			$sy_cash = floatval($quote) - floatval($service_cash);
			$newurl = $basic_url.'&op=hosted_amount';
			require keke_tpl_class::template("task/".$model_info['model_code']."/tpl/".$_K['template']."/task_hosted");
		}
		break;
	case "plan_complete":
		$task_obj->plan_complete($plan_id, $plan_step);
		break;
	case "plan_confirm":
		$task_obj->plan_confirm($plan_id, $plan_step);
		break;
	case "report" : 
		$transname = keke_report_class::get_transrights_name($type);
		$title=$transname.$_lang['submit'];
		if($type == 2){
			if($obj == 'task'){
				$report_reason = keke_report_class::get_report_task_reason();
			}elseif ($obj == 'work'){
				$report_reason = keke_report_class::get_report_work_reason();
			}
		}else{
			if($to_uid != $uid){
				$report_reason = keke_report_class::get_report_witkey_reason();
			}else{
				$report_reason = keke_report_class::get_report_employee_reason();
			}
		}
		if($sbt_edit){
			$task_obj->set_report ( $obj, $obj_id, $to_uid,$to_username, $type, $file_url, $tar_content,$slt_reason);
		}else{
			require keke_tpl_class::template("report");
			die();
		}
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
		$title = kekezu::lang("send_msg");
		if ($sbt_edit) {
			$task_obj->send_message($title,$tar_content,$to_uid, $to_username,'','json');
		} else{
			require keke_tpl_class::template ( 'message' );
			die ();
		}
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
		$w['bid_id'] = $bid_id;
		$w['bid_status'] = $st;
		$w['user_type']   = $ut;
		$work_arr = $task_obj->get_work_info ($w, " bid_id desc ", $p ); 
		$pages = $work_arr ['pages'];
		$work_info = $work_arr ['work_info'];
		$plan_info = $work_arr['plan_info'];
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
		$mark_obj = new Keke_witkey_mark_class();
		$mark_obj->setWhere("origin_id = $task_id");
		$count = $mark_obj->count_keke_witkey_mark();
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
        if($task_info['task_status']==8){
			$bid_info = db_factory::get_one(' select uid,username from '.TABLEPRE.'witkey_task_bid where task_id='.intval($task_id).' and bid_status =4');
			$w_info = kekezu::get_user_info($bid_info['uid']);
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
