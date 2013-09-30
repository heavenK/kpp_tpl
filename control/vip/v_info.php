<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );


if($sbt){

	if($user_info['balance'] < $price)	kekezu::show_msg ( "您的余额不足，请充值后再开通！", "index.php?do=user&view=finance&op=recharge", 3,"您的余额不足，请充值后再开通！" , 'warning' );
	else $res = keke_finance_class::cash_out ($uid, $price, 'open_vip'); 
	if($res)	{
		if($op == 'zx') $level = 1;
		else $level = 2;
		db_factory::execute(" update ".TABLEPRE."witkey_space set vip_status = ".$level." where uid = '$uid'");
		
		kekezu::show_msg ( "提交成功，请等待管理员审核通过！", "index.php?do=user", 3,"提交成功，请等待管理员审核通过！" , 'success' );
	}

/*	if($op == 'zx'){
		$sqlplus = "update %switkey_space set isvip = 1 ,vip_start_time=".time()." where uid = %d";
		$res = db_factory::execute(sprintf($sqlplus,TABLEPRE,$uid));
		if($res)	kekezu::show_msg("升级成功，您已经是尊享VIP！",'index.php',3,'','success');
	}
	if($op == 'kc'){
		$sqlplus = "update %switkey_space set isvip = 2 ,vip_start_time=".time()." where uid = %d";
		$res = db_factory::execute(sprintf($sqlplus,TABLEPRE,$uid));
		if($res)	kekezu::show_msg("升级成功，您已经是开创VIP！",'index.php',3,'','success');
	}*/
	
}

require keke_tpl_class::template ( "vip/v_" . $view );
