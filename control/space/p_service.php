<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$credit_level = unserialize($member_info['seller_level']);
$indus_arr = $kekezu->_indus_arr;
if($shop_open){
	 $service_obj = new Keke_witkey_service_class();
	 $service_obj->setWhere("uid = ".intval($member_id)." order by on_time desc limit 0,6 ");
	 $service_arr = $service_obj->query_keke_witkey_service();
}



$service_obj = new Keke_witkey_service_class ();
$w = " service_status='2' and uid = " . intval ( $member_id ) ." and model_id = 7 ";
$page = max($page,1);
$limit = max($limit,8);
$title&&$title!==$_lang['to_search_product_name'] and $w .= " and title like '%" . $title . "%'";
$ord = max($ord,1);
$ord==1 and $w.=' order by price desc ' or $w.=' order by price asc ';
$count =  max(db_factory::get_count(' select count(service_id) from '.TABLEPRE.'witkey_service where '.$w),0);
$pages = $kekezu->_page_obj->getPages($count, $limit, $page,$p_url.'&view='.$view.'&ord='.$ord.'&limit='.$limit);
$service_obj->setWhere ( $w.$pages['where']);
$service_arr = $service_obj->query_keke_witkey_service ();



require keke_tpl_class::template(SKIN_PATH."/space/{$type}_{$view}");
