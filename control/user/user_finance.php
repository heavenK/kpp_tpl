<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
if ($op == 'report') {
	$title =$_lang['order_rights_submit'];
	$to_uid=intval($to_uid);
	$obj_id = intval($obj_id);
	$report_reason = keke_report_class::get_report_product_reason();
	if ($sbt_edit) {
		keke_order_class::set_report ( $obj_id, $to_uid, $to_username, $type, $file_url, $tar_content );
	} else {
		$order_info = keke_order_class::get_order_info ( $obj_id ); 
		if ($type == '1') { 
			$to_uid = $order_info ['order_uid'];
			$to_username = $order_info ['order_username'];
		} else {
			$to_uid = $order_info ['seller_uid'];
			$to_username = $order_info ['seller_username'];
		}
		$type = "1"; 
		require keke_tpl_class::template ( "report" );
	}
	die ();
}
// add by heavenK
if(!$op) $op = 'index';

$task_count = db_factory::get_one ( sprintf ( " select count(task_id) count from %switkey_task", TABLEPRE ), 1, 600 ); 
$task_in = db_factory::get_one ( sprintf ( " select sum(fina_cash) cash from %switkey_finance where fina_action='task_bid' and fina_type='in' ", TABLEPRE ), 1, 600 ); 
$register = db_factory::get_one ( sprintf ( " select count(uid) count from %switkey_member ", TABLEPRE ), 1, 600 ); 

$task_count =  intval ( $task_count ['count'] );
$task_in = number_format ( $task_in ['cash'], 2, ".", "," );
$register =  intval ( $register ['count'] );
// end
$ops = array ('detail', 'recharge', 'withdraw', 'order' ,'prom');
in_array ( $op, $ops ) or $op = "detail";
$sub_nav = array(
	array ("detail" => array ($_lang['accounts_detail'], "chart-line" ),
		"prom" => array ($_lang['prom_make_money'], "emotion-smile" ) ),
	array (
 		"recharge" => array ($_lang['account_recharge'], "cur-yen" ),
 		"withdraw" => array ($_lang['account_withdraw'], "clipboard-copy" ))
	);
$pay_arr = kekezu::get_table_data ( "k,v", "witkey_pay_config", '', '', '', '', 'k' ); 
$payment_list = kekezu::get_payment_config (); 
require 'user_' . $view . '_' . $op . '.php';
