<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$credit_level = unserialize($member_info['seller_level']);
$indus_arr = $kekezu->_indus_arr;
if($shop_open){
	 $service_obj = new Keke_witkey_service_class();
	 $service_obj->setWhere("uid = ".intval($member_id)." order by on_time desc limit 0,6 ");
	 $service_arr = $service_obj->query_keke_witkey_service();
}
$auth_arr = get_auth($member_info);
function get_auth($user_info){
	$auth_item = keke_auth_base_class::get_auth_item ();
	$auth_temp = array_keys ( $auth_item );
	$user_info ['user_type'] == 2 and $un_code = 'realname' or $un_code = "enterprise";
	$t = implode ( ",", $auth_temp );
	$auth_info = db_factory::query ( " select a.auth_code,a.auth_status,b.auth_title,b.auth_small_ico,b.auth_small_n_ico from " . TABLEPRE . "witkey_auth_record a left join " . TABLEPRE . "witkey_auth_item b on a.auth_code=b.auth_code where a.uid ='".$user_info['uid']."' and FIND_IN_SET(a.auth_code,'$t')", 1, -1 );
	$auth_info = kekezu::get_arr_by_key ( $auth_info, "auth_code" );
	return array('item'=>$auth_item,'info'=>$auth_info,'code'=>$un_code);
}
$good_rate  = get_witkey_good_rate($member_info);
function get_witkey_good_rate($user_info){
	$st = $user_info['seller_total_num'];
	return $st?(number_format($user_info['seller_good_num']/$st,2)*100).'%':'0%'; 
}
if($task_open){
	$task_obj = keke_table_class::get_instance('witkey_task');
	$w = sprintf(' uid = %d',$member_id);
	$page or $page = 1;
	$limit=10;
	$task = $task_obj->get_grid($w,$p_url, $page,$limit,' order by start_time desc',1,'task_list');
	$count = $task_obj->_count;
	$task_list = $task['data'];
	$pages     = $task['pages'];
	$cash_cove = kekezu::get_cash_cove('',true);
}


// 收支
$sql = ' select a.fina_cash,a.fina_type from '.TABLEPRE.'witkey_finance a left join '
				.TABLEPRE.'witkey_task b on a.obj_id=b.task_id left join '.TABLEPRE
				.'witkey_service c on a.obj_id=c.service_id ';
$where =" where a.uid=".intval($uid)." and a.fina_type='in' and a.fina_action not in ('withdraw','offline_recharge','offline_charge','online_charge','online_recharge','withdraw_fail')";
$fina_arr = db_factory::query($sql.$where);
$shouru = 0;
$sum = 0;
foreach($fina_arr as $val){
	$shouru += $val['fina_cash'];
	$sum ++;
}
//end 


// 成功案例
$sql_c = "select a.*,a.indus_id in_id,b.* from " . TABLEPRE . "witkey_shop_case as a
		left join " . TABLEPRE . "witkey_service as b
				on a.service_id = b.service_id
					where  a.shop_id = " . intval($shop_info ['shop_id']) . " order by b.service_id desc limit 0,4";
$shop_arr = db_factory::query ( $sql_c );


$sql = "select * from " . TABLEPRE . "witkey_task order by task_id desc limit 0,10";
$task_list_arr = db_factory::query ( $sql );

$sql = "select * from " . TABLEPRE . "witkey_article where art_cat_id=5 order by pub_time desc limit 0,10";
$art_list_arr = db_factory::query ( $sql );

$sql = "select * from " . TABLEPRE . "witkey_article where art_cat_id=366 order by pub_time desc limit 0,10";
$art_list_arr_366 = db_factory::query ( $sql );

require keke_tpl_class::template(SKIN_PATH."/space/{$type}_{$view}");
