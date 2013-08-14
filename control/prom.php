<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$views = array (
		'index',
		'task',
		'good' 
);
in_array ( $view, $views ) and $view or $view = 'index';
$nav_active_index = "prom";
if (! $plug_arr [prom] [status]) {
	kekezu::show_msg ( $_lang ['operate_notice'], "index.php?do=index.php", '3', '此插件未开启', 'alert_error' );
}
$kekezu->init_prom ();
$shows = array (
		'reg',
		'pub_task',
		'bid_task',
		'service' 
);
$shows = array_merge ( $shows );
in_array ( $show, $shows ) or $show = $shows [0];
$reg_config = $kekezu->get_table_data ( '*', 'witkey_prom_rule', ' type="reg" ', '', '', '', 'prom_code', null );
if ($reg_config ['reg'] ['config']) {
	$reg_config ['reg'] ['config'] = unserialize ( $reg_config ['reg'] ['config'] );
	$prom_type = $reg_config ['reg'] ['config'] ['auth_step'];
}
switch (isset ( $_COOKIE ['user_prom_event'] )) {
	case 1 :
		$u and $url_data = $kekezu->_prom_obj->extract_prom_cookie ();
		if ($u == $url_data ['u']) {
			$kekezu->_prom_obj->prom_jump ( $url_data );
		} else {
			$kekezu->_prom_obj->create_prom_cookie ( $_SERVER ['QUERY_STRING'] );
		}
		break;
	case 0 :
		$u and $kekezu->_prom_obj->create_prom_cookie ( $_SERVER ['QUERY_STRING'] );
		break;
}
$global_config = $kekezu->get_table_data ( '*', 'witkey_basic_config', ' type="prom"', '', '', '', 'k' );
$prom_event_list = db_factory::query ( "select parent_uid,parent_username,event_desc,event_status,rake_cash,obj_id,action,event_time from " . TABLEPRE . "witkey_prom_event where event_status = '2' order by event_id desc limit 0,6", 1, 3600 );
function get_task_title($task_id) {
	return db_factory::get_one ( sprintf ( "select task_title from %switkey_task where task_id = '%d'", TABLEPRE, $task_id ) );
}
function get_goods_title($service_id) {
	return db_factory::get_one ( sprintf ( "select title from %switkey_service where service_id = '%d'", TABLEPRE, $service_id ) );
}
$model_list = kekezu::get_table_data ( "model_id,model_name", "witkey_model", "model_status='1'", "", "", "", "model_id", 3600 );
$indus_list = $kekezu->_indus_arr;
require S_ROOT . '/control/' . $do . '/' . $do . '_' . $view . '.php';
 