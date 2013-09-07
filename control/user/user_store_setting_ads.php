<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$record_obj = new Keke_witkey_auth_record_class();
$sys_config = $kekezu->_sys_config;
if (isset($formhash)&&kekezu::submitcheck($formhash)){

	var_dump($_REQUEST);
	if($_FILES['pic']['name']){
		$pic = keke_file_class::upload_file('pic');
	}
	
	/*
	$sql = sprintf ( "update %switkey_shop set link = '%s' where uid = '%d'", TABLEPRE, $link, $uid );
	$res = db_factory::execute ( $sql );
	
	if($res)	$msg= '修改成功';
	
	kekezu::show_msg ( $_lang['system prompt'], "index.php?do=user&view=store&op=setting&opp=link", '2', $msg, 'alert_right' ) ;*/

}

require keke_tpl_class::template ( "user/user_".$view."_".$op."_".$opp );