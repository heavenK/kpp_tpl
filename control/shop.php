<?php  defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
/**
 * this not free,powered by keke-tech
 * @author hr
 * @charset:GBK  last-modify 2012-1-7-下午3:29:50
 * @version V2.0
 */
$nav_active_index = 'shop';
$page_title=$_lang['weike_shop'].'- '.$_K['html_title'];
$rs_indus  = kekezu::get_classify_indus('shop','total');
 $clean_industry_arr = array();
 kekezu::get_tree($rs_indus, $clean_industry_arr, '');
 
 !$status && $status = 'latest' ;
 
 $fields = 'service_id,pic,ad_pic,leave_num,title,content,price,sale_num';
 $table = 'witkey_service';
 $where = 'service_status=2';
 switch ($status){
 	case 'latest' ://最新
 		$order = 'on_time desc';
 		break;
 	case 'highprice' ://最高金额
 		$order = 'price desc';
 		break;
 	case 'hot' ://默认最热
 		$order = 'views desc';
 }
 $services_list = $kekezu -> get_table_data($fields, $table, $where, $order, '', '0,16', 'service_id');
 $top2 = array_splice($services_list,0,2);

 //统计_交易中选稿中
$sql = " select count(order_id) from %switkey_order where model_id in(6,7) and order_status in ('ok','accept','send') ";
$count_record = db_factory::get_count ( sprintf($sql,TABLEPRE));
require $kekezu->_tpl_obj->template ('shop');
 