<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$credit_level = unserialize($member_info['seller_level']);
$indus_arr = $kekezu->_indus_arr;
if($shop_open){
	 $service_obj = new Keke_witkey_service_class();
	 $service_obj->setWhere("uid = ".intval($member_id)." order by on_time desc limit 0,6 ");
	 $service_arr = $service_obj->query_keke_witkey_service();
}



$sql = "select * from " . TABLEPRE . "witkey_shop_case where  shop_id = " . $shop_info ['shop_id'] . " order by on_time desc ";
$shop_arr = db_factory::query ( $sql );

require keke_tpl_class::template(SKIN_PATH."/space/{$type}_{$view}");
