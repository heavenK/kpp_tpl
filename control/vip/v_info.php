<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );


if($sbt){

	if($op == 'zx'){
		$sqlplus = "update %switkey_space set isvip = 1 ,vip_start_time=".time()." where uid = %d";
		$res = db_factory::execute(sprintf($sqlplus,TABLEPRE,$uid));
		if($res)	kekezu::show_msg("升级成功，您已经是尊享VIP！",'index.php',3,'','success');
	}
	if($op == 'kc'){
		$sqlplus = "update %switkey_space set isvip = 2 ,vip_start_time=".time()." where uid = %d";
		$res = db_factory::execute(sprintf($sqlplus,TABLEPRE,$uid));
		if($res)	kekezu::show_msg("升级成功，您已经是开创VIP！",'index.php',3,'','success');
	}
	
}

require keke_tpl_class::template ( "vip/v_" . $view );
