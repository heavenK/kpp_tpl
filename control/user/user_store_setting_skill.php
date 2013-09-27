<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$record_obj = new Keke_witkey_auth_record_class();
$sys_config = $kekezu->_sys_config;
if (isset($formhash)&&kekezu::submitcheck($formhash)){
	
	
	$skill = implode(',',$skills);

	
	$sql = sprintf ( "update %switkey_space set skill_ids = '%s' where uid = '%d'", TABLEPRE, $skill, $uid );
	$res = db_factory::execute ( $sql );
	
	if($res)	$msg= '修改成功';
	
	kekezu::show_msg ( $_lang['system prompt'], "index.php?do=space&member_id=$uid", '2', $msg, 'alert_right' ) ;

}

// 行业列表
$final_task = kekezu::get_classify_indus();

$skill_count = substr_count($user_info['skill_ids'],',');
if($skill_count < 1) $skill_count = 0;
else $skill_count++;


$skill_list = explode(',',$user_info['skill_ids']);

$skill_ids = str_replace(',',' ',$user_info['skill_ids']).' ';
require keke_tpl_class::template ( "user/user_".$view."_".$op."_".$opp );