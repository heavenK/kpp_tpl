<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$credit_level = unserialize($member_info['seller_level']);
$indus_arr = $kekezu->_indus_arr;
if($shop_open){
	 $service_obj = new Keke_witkey_service_class();
	 $service_obj->setWhere("uid = ".intval($member_id)." and model_id = 7 and service_status='2' order by on_time desc limit 0,5");
	 $service_arr = $service_obj->query_keke_witkey_service();
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



// 成功案例
$sql_c = "select a.*,a.indus_id in_id,b.* from " . TABLEPRE . "witkey_shop_case as a
		left join " . TABLEPRE . "witkey_service as b
				on a.service_id = b.service_id
					where  a.shop_id = " . intval($shop_info ['shop_id']) . " order by b.service_id desc limit 0,5";
$shop_arr = db_factory::query ( $sql_c );

$ad_info = db_factory::query ( " select * from " . TABLEPRE . "witkey_shop_ads where shop_id=".$shop_info['shop_id']." order by ad_id desc limit 0,6");
//$ad_count = db_factory::get_count ( " select count(ad_id) count from " . TABLEPRE . "witkey_shop_ads where shop_id=".$shop_info['shop_id']);

$sql = "select * from " . TABLEPRE . "witkey_task order by task_id desc limit 0,10";
$task_list_arr = db_factory::query ( $sql );

$sql = "select * from " . TABLEPRE . "witkey_article where art_cat_id=5 order by pub_time desc limit 0,10";
$art_list_arr = db_factory::query ( $sql );

$sql = "select * from " . TABLEPRE . "witkey_article where art_cat_id=366 order by pub_time desc limit 0,10";
$art_list_arr_366 = db_factory::query ( $sql );

require keke_tpl_class::template(SKIN_PATH."/space/{$type}_{$view}");
