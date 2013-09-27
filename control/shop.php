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
 
 $fields = 'service_id,pic,ad_pic,leave_num,title,content,price,sale_num,uid,views,mark_num,on_time,indus_pid,indus_id';
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

// add by heavenk
$task_count = db_factory::get_one ( sprintf ( " select count(task_id) count from %switkey_task", TABLEPRE ), 1, 600 ); 
$task_in = db_factory::get_one ( sprintf ( " select sum(fina_cash) cash from %switkey_finance where fina_action='task_bid' and fina_type='in' ", TABLEPRE ), 1, 600 ); 
$register = db_factory::get_one ( sprintf ( " select count(uid) count from %switkey_member ", TABLEPRE ), 1, 600 ); 

$task_count =  intval ( $task_count ['count'] );
$task_in = number_format ( $task_in ['cash'], 2, ".", "," );
$register =  intval ( $register ['count'] );
// end

$where .= " and indus_pid=";
$order = 'on_time desc';
// 分类服务
$indus_all_arr = $kekezu->_indus_arr;
$services_list_kf = $kekezu -> get_table_data($fields, $table, $where."2", $order, '', '0,5', 'service_id');
$services_list_sj = $kekezu -> get_table_data($fields, $table, $where."441", $order, '', '0,5', 'service_id');
$services_list_ch = $kekezu -> get_table_data($fields, $table, $where."3", $order, '', '0,5', 'service_id');
$services_list_zx = $kekezu -> get_table_data($fields, $table, $where."335", $order, '', '0,5', 'service_id');
$services_list_fw = $kekezu -> get_table_data($fields, $table, $where."192", $order, '', '0,5', 'service_id');


$sql = " select * from %switkey_order where model_id=7 and order_status ='complete' limit 0,8";
$success_res = db_factory::query ( sprintf($sql,TABLEPRE));


// 最新VIP威客
$new_member_vip = db_factory::query(sprintf("select *  from %switkey_member m left join %switkey_space s on m.uid=s.uid where s.isvip>0 order by m.uid desc limit 0,5",TABLEPRE,TABLEPRE));
// end

 //统计_交易中选稿中
$sql = " select count(order_id) from %switkey_order where model_id in(6,7) and order_status in ('ok','accept','send') ";
$count_record = db_factory::get_count ( sprintf($sql,TABLEPRE));
require $kekezu->_tpl_obj->template ('shop');
 