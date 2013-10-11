<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$record_obj = new Keke_witkey_auth_record_class();
$sys_config = $kekezu->_sys_config;
if (isset($formhash)&&kekezu::submitcheck($formhash)){

	if($_FILES['pic']['name']){
		$pic = keke_file_class::upload_file('pic');
	}else if($hdn_case_pic){
		$pic = $hdn_case_pic;
	}
	
	
	$insertsqlarr = array ('shop_id' => $shop_info['shop_id'],'title' => $title, 'start_time' => strtotime($start_time), 'end_time' => strtotime($end_time), 'pic' => $pic, 'url' => $url );
	if ($ad_id) {
		$result = db_factory::updatetable ( "keke_witkey_shop_ads", $insertsqlarr, array ("ad_id" => $ad_id ) );
	} else {
		$result = db_factory::inserttable ( 'keke_witkey_shop_ads', $insertsqlarr );
	}
	
	if($result)	$msg= '添加成功';
	
	kekezu::show_msg ( $_lang['system prompt'], "index.php?do=$do&view=$view&op=$op&opp=$opp", '2', $msg, 'alert_right' ) ;

}else{
	$ads_info = db_factory::get_one(sprintf(" select * from %switkey_shop_ads where shop_id = '%d'",TABLEPRE, $shop_info['shop_id']));
}

require keke_tpl_class::template ( "user/user_".$view."_".$op."_".$opp );