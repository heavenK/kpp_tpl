<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$credit_level = unserialize($member_info['seller_level']);
$indus_arr = $kekezu->_indus_arr;
if($shop_open){
	 $service_obj = new Keke_witkey_service_class();
	 $service_obj->setWhere("uid = ".intval($member_id)." order by on_time desc limit 0,6 ");
	 $service_arr = $service_obj->query_keke_witkey_service();
}

$sect_list = db_factory::query ( "select k,v1 from ".TABLEPRE."witkey_member_ext where type='sect' and uid=".$member_info[uid]);

$sect_info = array();
foreach($sect_list as $k=>$v){
	$sect_info[$v['k']] = $v['v1'];
}

if($uid != $member_info[uid] && $member_info['look_credit'] && !$_SESSION[$uid.'-'.$member_info[uid]]){
	if($user_info[credit] < $member_info['look_credit'])	kekezu::show_msg ( "您的豆币不足，无法查看！", "index.php?do=space&member_id=$member_info[uid]", '2', "您的豆币不足，无法查看！", 'alert_right' ) ;
	else	{
		keke_finance_class::cash_out ($uid, $member_info['look_credit'], 'look_credit', '', '' ,'' ,1); 
		keke_finance_class::cash_in($member_info[uid], floatval(0),intval( $member_info['look_credit']*$basic_config['look_get_credit']),'look_credit','','look_credit');
	}
	$_SESSION[$uid.'-'.$member_info[uid]] = 1;
}


require keke_tpl_class::template(SKIN_PATH."/space/{$type}_{$view}");
