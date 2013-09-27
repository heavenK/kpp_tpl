<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$nav_active_index = 'task';
$basic_url = "index.php?do=task&task_id=$task_id"; 
$task_obj = tender_task_class::get_instance ( $task_info );
$cover_id = $task_obj->_task_info ['task_cash_coverage'];
$cover_cash = kekezu::get_cash_cove ( '', true );
$task_config = $task_obj->_task_config;
$g_uid = $task_obj->_guid;
$g_uid and $g_info = kekezu::get_user_info ( $g_uid );
$payitem_str = $task_obj->get_payitem_str();
if(!$uid&&strstr(' '.$payitem_str,'seohide')){
	kekezu::show_msg("拒绝访问",$back_url,3,"该任务在您登录后才可以访问");
}
$model_id = $task_obj->_model_id;
$task_status = $task_obj->_task_status;
$tender_time_obj = new tender_time_class();
$tender_time_obj->validtaskstatus();
$indus_arr = $kekezu->_indus_c_arr; 
$indus_p_arr = $kekezu->_indus_p_arr; 
$is_bided = intval($task_obj->check_bid());
$trust_mode = $task_obj->_trust_mode; 
$status_arr = $task_obj->_task_status_arr; 
$time_desc = $task_obj->get_task_timedesc (); 
$sub_task_user_level =$g_info = $task_obj->_g_userinfo;
$uid != $g_uid && task_status == 1 and kekezu::show_msg ( $_lang ['friendly_notice'], 'index.php?do=index', 2, '任务审核中' );
$stage_desc = $task_obj->get_task_stage_desc (); 
$related_task = $task_obj->get_task_related (); 
$delay_rule = $task_obj->_delay_rule; 
$delay_total = sizeof ( $delay_rule ); 
$delay_count = intval ( $task_info ['is_delay'] ); 
$process_can = $task_obj->process_can (); 
$process_desc = $task_obj->process_desc (); 
$cash_arr = $task_obj->_cash_arr;
$task_cash_cove = $task_obj->get_task_coverage ();
$browing_history = $task_obj->browing_history ( $task_id, $task_cash_cove, $task_info ['task_title'] );
$show_payitem = $task_obj->show_payitem ();
$task_obj->plus_view_num (); 
$g_info = $task_obj->_g_userinfo;
switch ($op) {
	case "pub_agreement" : 
		$bid_info = $task_obj->get_bid_info ();		
		$task_obj->set_task_status(5);
		$res = $task_obj->set_agreement_status ( $bid_info ['bid_id'], 1 );
		$res and kekezu::keke_show_msg ( "index.php?do=task&task_id=$task_id", $_lang ['operate_success'], 'alert_right' ) or kekezu::keke_show_msg ( "index.php?do=task&task_id=$task_id", $_lang ['operate_fail'], 'alert_error' );
		break;
	case "work_over" : 
		$bid_info = $task_obj->get_bid_info ();
		$task_obj->set_task_status ( 8 );
		if ($task_info['task_union'] == 2) {
			$u = new keke_union_class ( $task_id);
			$u->task_close ( array ('r_task_id' => $u->_r_task_id, 'indetify' =>1,'bid_uid'=>$bid_info['uid'] ) );
		}
		$res = $task_obj->set_agreement_status ( $bid_info ['bid_id'], 2 );
		$res and kekezu::keke_show_msg ( "index.php?do=task&task_id=$task_id", $_lang ['operate_success'], 'alert_right' ) or kekezu::keke_show_msg ( "index.php?do=task&task_id=$task_id", $_lang ['operate_fail'], 'alert_error' );
		die ();
		break;
	case "reqedit" : 
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
		$title = $_lang ['task_delay'];
		if ($sbt_edit) {
			$task_obj->set_task_delay ( $delay_day, $delay_cash, '', 'json' );
		} else
			$min_cash = intval ( $task_config ['min_delay_cash'] ); 
		$max_day = intval ( $task_config ['max_delay'] ); 
		$this_min_cash = intval ( $delay_rule [$delay_count] ['defer_rate'] * $task_info ['task_cash'] / 100 ); 
		$min_cash > $this_min_cash and $real_min = $min_cash or $real_min = $this_min_cash; 
		$credit_allow = intval ( $kekezu->_sys_config ['credit_is_allow'] ); 
		require keke_tpl_class::template ( "task/task_delay" );
		die ();
		break;
	case "work_hand" : 
		$title = '任务投标';
		if ($sbt_edit) {
			if (CHARSET == 'gbk') {
				$work_frm ['area'] = kekezu::utftogbk ( $province . "," . $city . ',' . $area );
				$work_frm ['tar_content'] = kekezu::utftogbk ( $work_frm ['tar_content'] );
			}else{
				$work_frm ['area'] = $province . "," . $city . ',' . $area ;
			}
			if($user_info['credit'] < $hand_credit)	kekezu::show_msg ( "您的豆币不足，无法提交稿件！", "index.php?do=task&task_id=" . $task_info[task_id], 3, "提交失败", "alert_error" );
			$task_obj->tender_work_hand ( $work_frm , $hand_credit);
		} else {
			$loca = explode ( ",", $user_info ['residency'] );
			$workhide_exists = keke_payitem_class::payitem_exists ( $uid, 'workhide', 'work' ); 
			require keke_tpl_class::template ( 'task/tender_work' );
		}
		die ();
		break;
	case "work_choose" : 
		$work_status = $task_obj->get_work_status();
		$title = '选择'.$work_status[$to_status];
		if($sbt_edit){
			
			$hand_credit = $basic_config['tender_credit']*$basic_config['selected_credit'];
			$get_credit = $basic_config['tender_sel_credit'];
			
			$task_obj->work_choose ( $work_id, $to_status, false, $hand_credit, $get_credit);
		}else{
			$work_info = $task_obj->get_task_work($work_id,'');
			require keke_tpl_class::template ( 'task/work_choose' );
		}
		die();
		break;
	case "start_vote" : 
		$task_obj->start_vote ( '', 'json' );
		break;
	case "work_vote" : 
		$task_obj->set_task_vote ( $work_id, '', 'json' );
		break;
	case "report" : 
		$transname = keke_report_class::get_transrights_name ( $type );
		$title = $transname . $_lang ['submit'];
		if($obj == 'task'){
			$report_reason = keke_report_class::get_report_task_reason();
		}elseif ($obj == 'work'){
			$report_reason = keke_report_class::get_report_work_reason();
		}
		if($sbt_edit){
			$task_obj->set_report ( $obj, $obj_id, $to_uid,$to_username, $type, $file_url, $tar_content,$slt_reason);
		} else {
			require keke_tpl_class::template ( "report" );
		}
		die ();
		break;
	case "mark" : 
		$title = $_lang ['each_mark'];
		$model_code = $task_obj->_mode_code;
		require S_ROOT . 'control/mark.php';
		die ();
		break;
	case "work_del" : 
		$task_obj->del_work ( $work_id, '', 'json' );
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






$search_condit = $task_obj->get_search_condit ();
$date_prv = date ( "Y-m-d", time () ); 
$work_status = $task_obj->get_work_status (); 
$sql = sprintf ( "select a.*,b.* from %switkey_task_bid as a left join %switkey_space as b on a.uid=b.uid where a.task_id=%d", TABLEPRE, TABLEPRE, $task_id );
$st = $st ? $st : "all";
$sql .= " and 1=1";
$ut == "my" and $sql .= " and a.uid=" . intval ( $uid );
$url = "index.php?do=task&task_id=$task_id&view=work&ut=$ut&page_size=$page_size&$page=$page";
$count = db_factory::execute ( $sql );
$page = $page ? $page : 1;
$page_size = intval ( $page_size ) ? intval ( $page_size ) : 5;
$sql .= " order by (CASE WHEN  a.bid_status!=0 THEN a.bid_status ELSE 0 END) asc ,a.bid_time desc";
$page_obj->setAjax ( 1 );
$page_obj->setAjaxDom ( "gj_summery" );
$pages = $page_obj->getPages ( $count, $page_size, $page, $url );
$sql = $sql . $pages ['where'];
$bid_info = db_factory::query ( $sql );
$bid_info1 = kekezu::get_arr_by_key ( $bid_info, 'bid_id' );
$bid_ids = implode ( ',', array_keys ( $bid_info1 ) );
$bid_ids && $uid == $task_info ['uid'] and db_factory::execute ( 'update ' . TABLEPRE . 'witkey_task_bid set is_view=1 where bid_id in (' . $bid_ids . ') and is_view=0' );
$has_new = $task_obj->has_new_comment ( $page, $page_size );






$comment_obj = keke_comment_class::get_instance ( 'task' );
$url = $basic_url . "&view=comment";
intval ( $page ) or $page = 1;
$comment_arr = $comment_obj->get_comment_list ( $task_id, $url, $page );
$comment_data = $comment_arr ['data'];
$comment_page = $comment_arr ['pages'];
$reply_arr = $comment_obj->get_reply_info ( $task_id );

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


switch ($view) {
	case "work" :
		$search_condit = $task_obj->get_search_condit ();
		$date_prv = date ( "Y-m-d", time () ); 
		$work_status = $task_obj->get_work_status (); 
		$sql = sprintf ( "select a.*,b.* from %switkey_task_bid as a left join %switkey_space as b on a.uid=b.uid where a.task_id=%d", TABLEPRE, TABLEPRE, $task_id );
		$st = $st ? $st : "all";
		switch ($st) {
			case "all" :
				$sql .= " and 1=1";
				break;
			case 4 :
				$sql .= " and a.bid_status=4";
				break;
			case 7 :
				$sql .= " and a.bid_status=7";
				break;
		}
		$ut == "my" and $sql .= " and a.uid=" . intval ( $uid );
		$url = "index.php?do=task&task_id=$task_id&view=work&ut=$ut&page_size=$page_size&$page=$page";
		$count = db_factory::execute ( $sql );
		$page = $page ? $page : 1;
		$page_size = intval ( $page_size ) ? intval ( $page_size ) : 5;
		$sql .= " order by (CASE WHEN  a.bid_status!=0 THEN a.bid_status ELSE 0 END) asc ,a.bid_time desc";
		$page_obj->setAjax ( 1 );
		$page_obj->setAjaxDom ( "gj_summery" );
		$pages = $page_obj->getPages ( $count, $page_size, $page, $url );
		$sql = $sql . $pages ['where'];
		$bid_info = db_factory::query ( $sql );
		$bid_info1 = kekezu::get_arr_by_key ( $bid_info, 'bid_id' );
		$bid_ids = implode ( ',', array_keys ( $bid_info1 ) );
		$bid_ids && $uid == $task_info ['uid'] and db_factory::execute ( 'update ' . TABLEPRE . 'witkey_task_bid set is_view=1 where bid_id in (' . $bid_ids . ') and is_view=0' );
		$has_new = $task_obj->has_new_comment ( $page, $page_size );
		break;
	case "comment" :
		$comment_obj = keke_comment_class::get_instance ( 'task' );
		$url = $basic_url . "&view=comment";
		intval ( $page ) or $page = 1;
		$comment_arr = $comment_obj->get_comment_list ( $task_id, $url, $page );
		$comment_data = $comment_arr ['data'];
		$comment_page = $comment_arr ['pages'];
		$reply_arr = $comment_obj->get_reply_info ( $task_id );
		switch ($op) {
			case "reply" : 
				$comment_arr = array ("obj_id" => $task_id, "origin_id" => $task_id, "obj_type" => "task", "p_id" => $pid, "uid" => $uid, "username" => $username, "content" => $content, "on_time" => time () );
				$res = $comment_obj->save_comment ( $comment_arr, $task_id, 1 );
				if (!in_array($res,array(-1,-2,-3))) {
					$v1 = $comment_obj->get_comment_info ( $res );
					$tmp = 'replay_comment';
					require keke_tpl_class::template ( "task/task_comment_reply" );
				} else {
					echo $res;
				}
				die ();
				break;
			case "add" : 
				$comment_arr = array ("obj_id" => $task_id, "origin_id" => $task_id, "obj_type" => "task", "uid" => $uid, "username" => $username, "content" => $content, "on_time" => time () );
				$res = $comment_obj->save_comment ( $comment_arr, $task_id );
				if (!in_array($res,array(-1,-2,-3))) {
					$v = $comment_obj->get_comment_info ( $res );
					$tmp = 'pub_comment';
					require keke_tpl_class::template ( "task/task_comment_reply" );
				} else {
					echo $res;
				}
				die ();
				break;
			case "del" :
				$comment_info = $comment_obj->get_comment_info ( $comment_id );
				if ($uid == ADMIN_UID || $user_info ['group_id'] == 7) {
					$res = $comment_obj->del_comment ( $comment_id, $task_id, $comment_info ['p_id'] );
				} else {
					kekezu::keke_show_msg ( "", $_lang ['not_priv'], "error", "json" );
				}
				$res and kekezu::keke_show_msg ( "", $_lang ['delete_success'], "", "json" ) or kekezu::keke_show_msg ( "", $_lang ['system_is_busy'], "error", "json" );
				break;
		}
		break;
	case "base" :
	default :
		$task_file = $task_obj->get_task_file (); 
		if ($task_info ['task_status'] == 8) {
			$bid_info5 = db_factory::get_one ( ' select uid,username from ' . TABLEPRE . 'witkey_task_bid where task_id=' . $task_id . ' and bid_status =4' );
			$w_info = kekezu::get_user_info ( $bid_info5 ['uid'] );
		}
		if ($task_info ['task_status'] == 2 && $task_info ['uid'] == $uid) {
			$item_list = keke_payitem_class::get_payitem_config ( 'employer', null, null, 'item_id' );
		}
}
if($union_hand){
	require keke_tpl_class::template ( "task_info");
}else{
	require keke_tpl_class::template ( "task/" . $model_info ['model_code'] . "/tpl/" . $_K ['template'] . "/task_info" );
}
