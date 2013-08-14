<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$res = kekezu::get_table_data ( "art_cat_id", 'witkey_article_category', "art_index like '%{294}%'", '', '', '', 'art_cat_id', 3600 );
$model_list = $kekezu->_model_list;
$model_ids = implode(',',array_keys($model_list));
if ($res) {
	$cat_ids = array_keys ( $res );
	$cat_ids = implode ( ',', $cat_ids );
	$new_help = db_factory::query ( sprintf ( "select art_id,art_title,art_cat_id from %switkey_article where art_cat_id in (%s) order by views desc limit 0,3", TABLEPRE, $cat_ids ), 1, 3600 );
}
$basic_url .= '?do=' . $do . '&view=' . $view;
$auth_item = keke_auth_base_class::get_auth_item ();
$auth_temp = array_keys ( $auth_item );
$user_info ['user_type'] == 2 and $un_code = 'realname' or $un_code = "enterprise";
$t = implode ( ",", $auth_temp );
$auth_info = db_factory::query ( " select a.auth_code,a.auth_status,b.auth_title,b.auth_small_ico,b.auth_small_n_ico from " . TABLEPRE . "witkey_auth_record a left join " . TABLEPRE . "witkey_auth_item b on a.auth_code=b.auth_code where a.uid ='$uid' and FIND_IN_SET(a.auth_code,'$t')", 1, - 1 );
$auth_info = kekezu::get_arr_by_key ( $auth_info, "auth_code" );
$payitem_list = keke_payitem_class::get_payitem_config ( null, null, null, 'item_id' ); 
$task_status = sreward_task_class::get_task_status ();
$task_cove = kekezu::get_table_data ( '*', "witkey_task_cash_cove", '', "start_cove", '', '', 'cash_rule_id', 3600 );
$pub_task = db_factory::query ( sprintf ( "select * from %switkey_task where uid = '%d' and task_status in (0,2,3,4,5,6) and model_id in ($model_ids) order by start_time desc limit 0,6", TABLEPRE, $uid ) );
$pub_task = get_pub_list ( $pub_task );
$pub_service = db_factory::query ( sprintf ( "
		SELECT a.order_id,a.order_uid,a.order_username,a.order_status,a.seller_uid,a.seller_username,a.order_time,c.* from %switkey_order a
		left join %switkey_order_detail b on a.order_id =b.order_id and a.order_status in ('wait','ok','send','accept')
		left join %switkey_service c on b.obj_id = c.service_id where a.seller_uid='%d' and a.model_id in(6,7) and b.obj_type='service' group by c.service_id order by a.order_time desc", TABLEPRE, TABLEPRE, TABLEPRE, $uid ) );
$sql = "select a.* from %switkey_task a left join %switkey_task_bid b on a.task_id = b.task_id left join %switkey_task_work c on a.task_id = c.task_id where task_status in (2,3,4,5,6) and a.model_id in ($model_ids) and (b.uid=%d or c.uid=%d) group by a.task_id order by start_time desc limit 0,6";
$join_task = db_factory::query ( sprintf ( $sql, TABLEPRE, TABLEPRE, TABLEPRE, $uid, $uid ) );
$join_task = get_pub_list ( $join_task );
$buy_service = db_factory::query ( sprintf ( "
		SELECT a.order_id,a.order_uid,a.order_username,a.order_status,a.seller_uid,a.seller_username,a.order_time,c.* from %switkey_order a
		left join %switkey_order_detail b on a.order_id =b.order_id and a.order_status in ('wait','ok','send','accept')
		left join %switkey_service c on b.obj_id = c.service_id where a.order_uid='%d' and a.model_id in(6,7) and b.obj_type='service' group by c.service_id order by a.order_time desc", TABLEPRE, TABLEPRE, TABLEPRE, $uid ) );
$buy_service = get_pub_list ( $buy_service );
function get_pub_list($list) {
	foreach ( $list as $k => $v ) {
		$pay_time = unserialize ( $v ['payitem_time'] );
		$list [$k] = $v;
		$list [$k] ['top'] = $pay_time ['top'];
		$list [$k] ['urgent'] = $pay_time ['urgent'];
	}
	return $list;
}
if (isset ( $ac ) &&$ac=='pay'&& $model_id&&$task_id) {
	$model_info = $kekezu->_model_list [$model_id];
	if ($model_info ['model_type'] == "task") {
		$class_name = $model_info ['model_code'] . "_task_class";
		$order_info = db_factory::get_one ( sprintf ( "select order_id from %switkey_order_detail where obj_id=%d", TABLEPRE, $task_id ) );
		$task_info = db_factory::get_one ( sprintf ( "select * from %switkey_task where task_id='%d'", TABLEPRE, $task_id ) );
		$obj = new $class_name ( $task_info );
		$res = $obj->dispose_order ( $order_info ['order_id'], 'pay' );
		kekezu::show_msg ( $res ['title'], $res ['url'], 1, $res ['content'], $res ['type'] );
	}
} elseif ($download) {
	$title = $_lang ['works_file_upload'];
	$view = "file";
	$ajax = "goods_filedown";
	require "control/ajax/ajax_file.php";
	die ();
}
function get_mark_info($order_id, $obj_id, $order_uid, $seller_uid) {
	global $uid, $role;
	if ($role == 1) { 
		$mark_type = 1;
		$auid = $order_uid;
	} else { 
		$mark_type = 2;
		$auid = $seller_uid;
	}
	$mark_info = db_factory::get_one ( sprintf ( "select * from %switkey_mark where obj_id=%d and origin_id=%d and mark_type=%d and uid=$auid and by_uid=$uid", TABLEPRE, $order_id, $obj_id, $mark_type ) );
	return $mark_info;
}
$art_notice_arr = db_factory::query ( sprintf ( "select * from %switkey_article a left join %switkey_article_category b on a.art_cat_id=b.art_cat_id where b.cat_type='article' order by a.pub_time desc limit 0,4", TABLEPRE, TABLEPRE ) );
if (isset ( $ajax )) {
	switch ($ajax) {
		case 'bid_notice' :
			$dynamic_arr = kekezu::get_feed ( "feedtype='work_accept'", "feed_time desc", 4 ); 
			require keke_tpl_class::template ( "ajax/index" );
			die ();
			break;
		case 'withdraw' :
			$withdraw_arr = db_factory::query ( sprintf ( "select * from %switkey_withdraw where withdraw_status=2 order by process_time desc limit 0,4", TABLEPRE ) );
			require keke_tpl_class::template ( "ajax/index" );
			die ();
			break;
	}
}
function master_opera($m_id, $t_id, $url, $task_cash) {
	global $kekezu;
	$button = array ();
	$model_code = $kekezu->_model_list [$m_id] ['model_code'];
	$c = $model_code . '_task_class';
	if ($model_code && method_exists ( $c, 'master_opera' )) {
		$button = call_user_func_array ( array ($c, 'master_opera' ), array ($m_id, $t_id, $url, $task_cash ) );
	} else { 
		$button = call_user_func_array ( array ('sreward_task_class', 'master_opera' ), array ($m_id, $t_id, $url, $task_cash ) );
	}
	if ($button ['del']) {
		unset ( $button ['del'] );
	}
	unset ( $button ['onkey'] );
	return $button;
}
function wiki_opera($m_id, $t_id, $w_id, $url) {
	global $kekezu;
	$button = array ();
	$model_code = $kekezu->_model_list [$m_id] ['model_code'];
	$c = $model_code . '_task_class';
	if ($model_code && method_exists ( $c, 'wiki_opera' )) {
		$button = call_user_func_array ( array ($c, 'wiki_opera' ), array ($m_id, $t_id, $w_id, $url ) );
	} else { 
		$button = call_user_func_array ( array ('sreward_task_class', 'wiki_opera' ), array ($m_id, $t_id, $w_id, $url ) );
	}
	if ($button ['share']) {
		unset ( $button ['share'] );
	}
	return $button;
}
$end_time_arr = keke_glob_class::get_taskstatus_desc ();
require keke_tpl_class::template ( "user/user_" . $view );