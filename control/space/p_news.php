<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$credit_level = unserialize($member_info['seller_level']);
$indus_arr = $kekezu->_indus_arr;
if($shop_open){
	 $service_obj = new Keke_witkey_service_class();
	 $service_obj->setWhere("uid = ".intval($member_id)." order by on_time desc limit 0,6 ");
	 $service_arr = $service_obj->query_keke_witkey_service();
}



$url = "index.php?do=$do&view=$view&member_id=$member_id&art_title=$art_title&page_size=$page_size&page=$page&type=$type";
$art_obj = new Keke_witkey_article_class ();
$table_obj = new keke_table_class ( "witkey_article" );
		
$where = ' 1 = 1 ';

$where .= " and cat_type = 'article' ";

$page_size  and $page_size = intval ( $page_size ) or $page_size = 100;
$page and $page = intval ( $page ) or $page = 1;

strval ( $art_title ) and $where .= " and art_title like '%$art_title%'";
if($start_time || $end_time){
	if($start_time) $where .= " and pub_time > ".strtotime($start_time);
	if($end_time) $where .= " and pub_time < ".strtotime($end_time);
}


$where .= " and art_cat_id = 368";

$where .= " and uid = ".$member_id;
$ord[0]&&$ord[1] and $where .=' order by '.$ord[0].' '.$ord[1] or $where.=" order by art_id desc ";
$r = $table_obj->get_grid ( $where, $url, $page, $page_size,null,1,'ajax_dom');
$art_arr = $r [data];
$pages = $r [pages];
  

require keke_tpl_class::template(SKIN_PATH."/space/{$type}_{$view}");
