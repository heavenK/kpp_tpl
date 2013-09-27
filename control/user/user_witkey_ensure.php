<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$model_id = '';
$model_list=kekezu::get_table_data ( '*', 'witkey_model', " model_type = 'task' and model_status=1", 'model_id asc ', '', '', 'model_id', 3600 );
$user_join = keke_task_config::get_user_join_task (); 

if($submit){
	$res = db_factory::execute(" update ".TABLEPRE."witkey_space set ensure = 1 where uid = '$uid'");
	echo 1;
	exit;
}
function get_pay_config($paymentname = "", $pay_type = 'online'){
	$where = " 1=1 ";
	$paymentname and $where  .= " and payment='$paymentname' ";
	$pay_type and  $where .= " and type = '$pay_type' ";
	$list=  kekezu::get_table_data ( '*', "witkey_pay_api", $where, "pay_id asc", '', '', '', null );
	$tmp = array();
	foreach ($list as $k=>$v){
	if($v['config']){
		$config = unserialize( $v['config'] );
		if(is_array($config)){
			$v = array_merge($v,$config);
		}
	}
	$tmp[$v ['payment']] = $v;
	}
	return $tmp;
}
function get_ten_bank_type(){
	static $bank = array(
			"1001"=>"17",   
			"1002"=>"10",
			"1003"=>"2",
			"1004"=>"9",
			"1005"=>"1",
			"1006"=>"4",
			"1008"=>"8",
			"1009"=>"27",
			"1010"=>"18",
			"1020"=>"5",
			"1021"=>"7",
			"1022"=>"3",
			"1024"=>"20",
			"1025"=>"22",
			"1027"=>"6",
			"1032"=>"11",
			"1033"=>"14",
			"1052"=>"19",
			"8001"=>"logo",
			);
	return $bank;
}


if($action == 'choose'){
	$pay_arr = kekezu::get_table_data ( "k,v", "witkey_pay_config", '', '', '', '', 'k' ); 
	$offline_pay_list = get_pay_config('','offline');
	$payment_list = get_pay_config();
	$ten_bank_type_arr = get_ten_bank_type(); 
	
	require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op . "_choose" );
	exit;
}
require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );
