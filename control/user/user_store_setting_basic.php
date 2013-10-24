<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$record_obj = new Keke_witkey_auth_record_class();
$sys_config = $kekezu->_sys_config;
if (isset($formhash)&&kekezu::submitcheck($formhash)){
	$shop_obj = keke_table_class::get_instance ( "witkey_shop" );
	
	$conf ['uid'] = $uid;
	$conf ['username'] = kekezu::escape($username);
	$conf ['shop_name'] = kekezu::escape($shop_name);
	$conf ['shop_type'] = kekezu::escape($shop_type);
	$conf['seo_title'] = kekezu::escape($seo_title);
	$conf['seo_keyword'] = kekezu::escape($seo_keyword);
	$conf['seo_desc'] = kekezu::escape($seo_desc);
	
	intval($shop_info['shop_id']) or $conf['shop_background'] = $file_temp;
	$shop_slogans and $conf ['shop_slogans'] = kekezu::escape($shop_slogans);
 	$sql = sprintf("select shop_id, on_time from %switkey_shop where uid=%d ",TABLEPRE,$uid); 
	$shop_info = db_factory::query($sql);

	if($shop_info[0]['on_time'] == NULL){
		//keke_finance_class::cash_in($uid, floatval(0),intval($basic_config['shop_open_credit']),'admin_charge','','admin_charge');
		$conf['on_time'] = time();
		$msg_credit = "恭喜您已成功开通工作室，完善详细资料，豆8网将赠送您".$basic_config['shop_open_credit']."豆币！";
	}
	
	$pk['shop_id'] = $shop_info['0']['shop_id']; 
	$res = $shop_obj->save ($conf, $pk );
	if($shop_info){
		$msg = '修改成功！'.$msg_credit;
	}else{
		$msg = '提交成功!'.$msg_credit;
	}
	kekezu::show_msg ( $_lang['system prompt'], "index.php?do=space&member_id=$uid", '2', $msg, 'alert_right' ) ;
}
require keke_tpl_class::template ( "user/user_".$view."_".$op."_".$opp );