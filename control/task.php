<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$rs_indus  = kekezu::get_classify_indus('task','total');
$nav_active_index = "task";
$page_title = $_lang ['task_index'] . '-' . $_K ['html_title'];

// add by heavenk
$task_count = db_factory::get_one ( sprintf ( " select count(task_id) count from %switkey_task", TABLEPRE ), 1, 600 ); 
$task_in = db_factory::get_one ( sprintf ( " select sum(fina_cash) cash from %switkey_finance where fina_action='task_bid' and fina_type='in' ", TABLEPRE ), 1, 600 ); 
$register = db_factory::get_one ( sprintf ( " select count(uid) count from %switkey_member ", TABLEPRE ), 1, 600 ); 

$task_count =  intval ( $task_count ['count'] );
$task_in = number_format ( $task_in ['cash'], 2, ".", "," );
$register =  intval ( $register ['count'] );
// end


if (isset ( $task_id )) {
	$task_ext_obj = new Keke_witkey_task_ext_class ();
	$task_ext_obj->setWhere ( 'a.task_id=' . intval ( $task_id ) );
	$task_info = $task_ext_obj->query_keke_witkey_task ();
	$task_info = kekezu::k_stripslashes ( $task_info ['0'] );
	$prom_rule = keke_prom_class::get_prom_rule ( "bid_task" );
	$check_right = db_factory::get_one(sprintf("select report_id from %switkey_report where origin_id = '%d'",TABLEPRE,$task_id));
	$union_hand = keke_union_class::hand_link ($task_info);
	if ($task_info ['task_union'] == 2 && $task_info ['r_task_id'] && intval ( $u ) == 1) {
		$union_obj = new keke_union_class ( $task_id );
		$union_obj->view_task ();
	}
	$task_info ['uid'] != $uid && $uid != ADMIN_UID && $task_info ['task_status'] == 1 and kekezu::show_msg ( $_lang ['friendly_notice'], 'index.php?do=task_list', 2, $_lang ['task_auditing'] );
	$task_info ['uid'] != $uid && $uid != ADMIN_UID && $task_info ['task_status'] == 0 and kekezu::show_msg ( $_lang ['friendly_notice'], 'index.php?do=task_list', 2, $_lang ['task_not_pay'] );
	if ($view == 'misc' && in_array ( $t, array (1, 2, 3, 4 ) ) && $user_info ['group_id']) {
		switch ($t) {
			case 1 : 
				$res = keke_task_config::task_audit_pass ( array ($task_id ) );
				break;
			case 2 : 
				$res = keke_task_config::task_audit_nopass ( $task_id );
				break;
			case 3 : 
				$res = db_factory::execute ( 'update ' . TABLEPRE . 'witkey_task set is_top=1 where task_id=' . $task_id );
				break;
			case 4 : 
				$res = keke_task_config::task_freeze ( $task_id );
				break;
		}
		$res and kekezu::show_msg ( $_lang ['operate_notice'], "index.php?do=task&task_id=$task_id", '1', $_lang ['operate_success'], 'alert_right' ) or kekezu::show_msg ( $_lang ['operate_notice'], "index.php?do=task&task_id=$task_id", '1', $_lang ['operate_fail'], 'alert_error' );
	}
	if ($view == 'tools') {
		$payitem_arr = keke_payitem_class::get_payitem_info ( 'employer', $model_list [$task_info ['model_id']] ['model_code'] ); 
		$exist_payitem_arr = keke_payitem_class::payitem_exists ( $uid, false, '', $payitem_arr ); 
		$payitem_arr_desc = unserialize ( $task_info ['payitem_time'] ); 
		$payitem_standard = keke_payitem_class::payitem_standard (); 
		$payitem_arr_desc['urgent'];
		foreach ( $payitem_arr_desc as $k => $v ) {
			if ($v > time ()) {
				$sy_time_str = $v - time ();
				$sy_time_desc [$k] = kekezu::time2Units ( $sy_time_str );
			} else {
				$sy_time_desc [$k] = '0' . $_lang ['day'];
			}
		}
		if($task_id&&$point&&$city){
			$pay_item = $task_info ['pay_item'];
			if($pay_item){
				$pay_item = $pay_item.','.$item_id;
			}else{
				$pay_item = $item_id;
			}
			$res = db_factory::execute ( sprintf ( "update %switkey_task set point='%s',pay_item='%s',city='%s' where task_id=%d", TABLEPRE, $point,$pay_item,$city, $task_id ) );
			$res.=$cost_res = keke_payitem_class::payitem_cost ( 'map', 1, 'task', 'spend', $task_id, $task_id );
			$res and kekezu::echojson('',1,'');
		}
		if (isset ( $formhash ) && kekezu::submitcheck ( $formhash )) {
			$payitem_num = ( array ) $payitem_num;
			if (! array_filter ( $payitem_num )) {
				kekezu::show_msg ( $_lang ['friendly_notice'], 'index.php?do=task&task_id=' . $task_id . '&view=tools', 3, $_lang ['no_choose_any_tools'] );
			}
			$keys_arr = array_keys ( $payitem_arr_desc );
			$pay_item = $task_info ['pay_item'];
			foreach ( array_filter ( $payitem_num ) as $k => $v ) {
				if (intval ( $v ) > 0 && ! stristr ( $pay_item, "$k" )) {
					$pay_item = $pay_item . ",$k";
				}
				if (in_array ( $payitem_arr [$k] ['item_code'], $keys_arr )) {
					$payitem_arr_desc [$payitem_arr [$k] ['item_code']] > time () and $payitem_arr_desc [$payitem_arr [$k] ['item_code']] = 3600 * 24 * $v + $payitem_arr_desc [$payitem_arr [$k] ['item_code']] or $payitem_arr_desc [$payitem_arr [$k] ['item_code']] = time () + 3600 * 24 * $v;
				} else {
					db_factory::execute ( sprintf ( "update %switkey_task set point='%s',city='%s' where task_id=%d", TABLEPRE, $_POST ['point'], $province, $task_id ) );
				}
				$cost_res = keke_payitem_class::payitem_cost ( $payitem_arr [$k] ['item_code'], $v, 'task', 'spend', $task_id, $task_id );
			}
			$pay_item = ltrim ( $pay_item, "," );
			if (strlen ( $pay_item )) {
				db_factory::execute ( sprintf ( "update %switkey_task set pay_item='%s' where task_id=%d", TABLEPRE, $pay_item, $task_id ) ); 
			}
			$res = keke_payitem_class::set_payitem_time ( $payitem_arr_desc, $task_id, 'task' );
			$res || $cost_res and kekezu::show_msg ( $_lang ['operate_notice'], "index.php?do=task&task_id=$task_id&view=tools", '1', $_lang ['operate_success'], 'alert_right' );
		}
		if($opp == 'tips'){
			$p_in = db_factory::get_one(sprintf("select * from %switkey_payitem where item_code = '%s'",TABLEPRE,$obj));
			require keke_tpl_class::template ( "use_tool" );
		}
	}
	if (! $task_info) {
		kekezu::show_msg ( $_lang ['operate_notice'], "index.php?do=index", '1', $_lang ['task_not_exsit_has_delete'], 'error' );
	}
	if ($task_info ['point']) {
		$point = explode ( ',', $task_info ['point'] );
		$px = $point ['0'];
		$py = $point ['1'];
	}
	$model_info = $model_list [$task_info ['model_id']];
	$model_code = $model_info ['model_code'];
	keke_lang_class::package_init ( "task" );
	keke_lang_class::loadlang ( $model_info ['model_dir'] );
	keke_lang_class::loadlang ( "task_info" );
	if($task_info['seo_title']){
		$page_title = $task_info['seo_title'];
	}else{
		$page_title = $task_info['task_title'].','.$indus_arr[$task_info['indus_id']]['indus_name'].','.$indus_p_arr[$task_info['indus_pid']]['indus_name'].','.$_lang['task_hall'].'-'.$_K['html_title'];
	}
	if($task_info['seo_keyword']){
		$page_keyword = $task_info['seo_keyword'];
	}else{
		$page_keyword = $task_info['task_title'].','.$indus_arr[$task_info['indus_id']]['indus_name'].','.$indus_p_arr[$task_info['indus_pid']]['indus_name'];
	}
	if($task_info['seo_desc']){
		$page_description = $task_info['seo_desc'];
	}else{
		$page_description = kekezu::cutstr(strip_tags($task_info['task_desc']),200);
	}
	$model_info and (require S_ROOT . "/task/" . $model_info ['model_dir'] . "/control/task_info.php") or kekezu::show_msg ( $_lang ['error'], "index.php?do=index", 3, $_lang ['task_model_not_exist'], 'error' );
} else {
	$model_open = get_model ();
	$clean_industry_arr = array ();
	kekezu::get_tree ( $rs_indus, $clean_industry_arr, '' );
	$count_advance_task_sql = 'select count(*) count from ' . TABLEPRE . 'witkey_task where task_status in (2,3) and model_id  in ('.$model_open.')';
	$advance_task = db_factory::get_count ( $count_advance_task_sql, 0, null, 180 );
	$model_list = $kekezu->_model_list; 
	$task_cash_cove = kekezu::get_cash_cove ( '', true );
	$task_obj = new Keke_witkey_task_class ();
	$page_obj = $kekezu->_page_obj;
	isset ( $page ) or $page = 1;
	isset ( $page_size ) or $page_size = 12;
	isset ( $t ) or $t = 'new';
	$url = "index.php?do=task&t=$t";
	$sql = " task_union=0 and model_id in ($model_open) ";
	switch ($t) {
		case "new" :
			$sql .= sprintf ( " and task_status=2 order by start_time desc " );
			break;
		case "h" :
			$sql .= sprintf ( " and  sub_time<'%s' and task_status='2' order by start_time desc ", time () + 24 * 3600 );
			break;
		case "t" :
			$sql .= " and task_status in (2,3) order by task_cash desc ";
			break;
		case "u" :
			$sql = " 1=1 and task_status in (2,3) and `r_task_id` > '0' and task_union=2 order by start_time desc ";
			break;
	}
	$task_obj->setWhere ( $sql );
	$count = intval ( $task_obj->count_keke_witkey_task () );
	$pages = $page_obj->getPages ( $count, $page_size, $page, $url );
	$task_obj->setWhere ( $sql . $pages ['where'] );
	$task_list = $task_obj->query_keke_witkey_task ( 1, 300 );
	function is_tender($model_code) {
		if (in_array ( $model_code, array ("dtender", "tender" ) )) {
			return 1;
		} else {
			return 0;
		}
	}
	require keke_tpl_class::template ( "task" );
}
function get_model() {
	global $model_list;
	foreach ( $model_list as $v ) {
		$v ['model_status'] == 1 and $model_arr [] = $v ['model_id'];
	}
	$res = implode ( ',', $model_arr );
	return $res;
}
