<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$record_obj = new Keke_witkey_auth_record_class();
$sys_config = $kekezu->_sys_config;
if (isset($formhash)&&kekezu::submitcheck($formhash)){
	
	
	$skill = implode(',',$skills);

	
	$sql = sprintf ( "update %switkey_space set skill_ids = '%s' where uid = '%d'", TABLEPRE, $skill, $uid );
	$res = db_factory::execute ( $sql );
	
	if($res)	$msg= '修改成功';
	
	
	$rs = db_factory::get_one(sprintf ( "select * from %switkey_space_ext where uid = '%d' and k='fill_skills'", TABLEPRE, $uid ));
	if(!$rs['v']){
		keke_finance_class::cash_in($uid, floatval(0),intval($basic_config['fill_skills']),'fill_skills','','fill_skills');
		$msg .= "，第一次完善技能，豆8网赠送您".$basic_config['fill_skills']."豆币！";
		
		if($rs)	db_factory::execute ( sprintf ( "update %switkey_space_ext set v=%d where uid = %d and k='fill_skills'", TABLEPRE, 1, $uid ) );
		else db_factory::execute ( sprintf ( "insert into %switkey_space_ext value(%d, 'fill_skills', %d, %d)", TABLEPRE, $uid, 1, time() ) );
		
		//$conf['on_time'] = time();
		//$msg_credit = "恭喜您已成功开通工作室，完善详细资料，豆8网将赠送您".$basic_config['shop_open_credit']."豆币！";
	}
	
	kekezu::show_msg ( $msg, "index.php?do=space&member_id=$uid", '2', $msg, 'alert_right' ) ;

}

// 行业列表
$final_task = kekezu::get_classify_indus();

$skill_count = substr_count($user_info['skill_ids'],',');
if($skill_count < 1) $skill_count = 0;
else $skill_count++;


$skill_list = explode(',',$user_info['skill_ids']);

$skill_ids = str_replace(',',' ',$user_info['skill_ids']).' ';
require keke_tpl_class::template ( "user/user_".$view."_".$op."_".$opp );