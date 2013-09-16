<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$credit_level = unserialize($member_info['seller_level']);
$indus_arr = $kekezu->_indus_arr;
if($shop_open){
	 $service_obj = new Keke_witkey_service_class();
	 $service_obj->setWhere("uid = ".intval($member_id)." order by on_time desc limit 0,6 ");
	 $service_arr = $service_obj->query_keke_witkey_service();
}



$status_arr   = service_shop_class::get_order_status();
intval ( $page ) and $p ['page'] = intval ( $page ) or $p ['page']='1';
intval ( $page_size ) and $p ['page_size'] = intval ( $page_size ) or $p['page_size']='10';
$p['url'] = $basic_url."&view=sale&page_size=".$p ['page_size']."&page=".$p ['page'];
$p ['anchor']	  = '#pageTop';
$w=array();
$w['a.order_status']="complete";


$order = " a.order_time desc";
$where = " select a.order_status,a.order_name,a.order_uid,a.order_username,a.seller_uid,a.seller_username,a.order_amount,a.order_time from " . TABLEPRE . "witkey_order a left join " . TABLEPRE . "witkey_order_detail b on a.order_id=b.order_id where
1=1 and b.obj_type = 'service' and (a.order_uid = ".$member_info['uid'] . " or a.seller_uid = ".$member_info['uid'].")";
$ext_condit and $where .= " and " . $ext_condit;
$arr = keke_table_class::format_condit_data ( $where, $order, $w, $p );
$sale_info = db_factory::query ( $arr ['where'] );
$sale_arr ['sale_info'] = $sale_info;
$sale_arr ['pages'] = $arr ['pages'];
$sale_list   = $sale_arr['sale_info'];
$pages      = $sale_arr['pages'];

$i = 0;
$j = 0;
$m = 0;
$order_list = array();
$seller_list = array();

foreach($sale_list as $key => $val){
	$i++;
	if($val['order_uid'] == $member_id) {
		$order_list[$key] = $val;
		$m++;
	}
	if($val['seller_uid'] == $member_id) {
		$seller_list[$key] = $val;
		$j++;
	}
}

require keke_tpl_class::template(SKIN_PATH."/space/{$type}_{$view}");
