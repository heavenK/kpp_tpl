<?php
defined ( 'IN_KEKE' ) or exit('Access Denied');
if(isset($check_mobile)&&$check_mobile){
	$res = mobile_exists($check_mobile);
	echo $res?$_lang['mobile_exists']:1;
	die();
}
$page_title= $_lang['mobi_auth'];
$step_arr=array("1"=>array( $_lang['step_one'], "填写手机号码"),
				"2"=>array( $_lang['step_two'], '接受并填写验证码'),
				"3"=>array( $_lang['step_three'], "认证成功"));
$auth_step= keke_auth_mobile_class::get_auth_step($auth_step,$auth_info);
$ac_url = $origin_url . "&op=$op&auth_code=$auth_code&ver=".intval($ver);
switch ($auth_step){
	case "step1": 
		if($fds['mobile'] and mobile_exists($fds['mobile'])){
			kekezu::show_msg($_lang['operate_notice'],$ac_url,1,$_lang['mobile_exists'],'alert_error');
		}
		isset($formhash)&&kekezu::submitcheck($formhash) and $auth_obj->add_auth($fds);
		break;
	case "step2":
		isset($formhash)&&kekezu::submitcheck($formhash) and $auth_obj->valid_auth($fds);
		break;
	case "step3":
		break;
}
function mobile_exists($mobile){
	global $kekezu;
	$uid = $kekezu->_uid;
	$sql = sprintf('select count(*) from  %switkey_auth_mobile where mobile = \'%s\' and auth_status = 1 and uid != \'%d\' ',TABLEPRE,$mobile,$uid);
	return (bool) db_factory::get_count($sql);
}
if($auth_info['auth_status']==1&&$auth_step=='step2'){
	$auth_step = 'step3';
}
if($auth_info['auth_status']==1){
	$auth_tips =$_lang['congratulations_pass_mobile_auth'];
	$auth_style = 'success';
}elseif($auth_info['auth_status']==2){
	$auth_tips =$_lang['regrettably_not_pass_mobile_auth'];
	$auth_style = 'warning';
}
require keke_tpl_class::template ( 'auth/' . $auth_dir . '/tpl/' . $_K ['template'] . '/auth_add' );