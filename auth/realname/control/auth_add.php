<?php
defined ( 'IN_KEKE' ) or exit('Access Denied');
$page_title= $_lang['realname_auth'];
$step_arr=array("step1"=>array( $_lang['step_one'], $_lang['fill_in_realname_auth_info']),
				"step2"=>array( $_lang['step_two'], $_lang['waiting_for_background_check']),
				"step3"=>array( $_lang['step_three'], $_lang['background_check_pass']));
$auth_step= keke_auth_realname_class::get_auth_step($auth_step,$auth_info);
$verify   = 0;
$ac_url = $origin_url . "&op=$op&auth_code=$auth_code&ver=".intval($ver);
switch ($auth_step){
	case "step1":
		break;
	case "step2":
		if(name_exists($fds['id_pic'])){
			kekezu::show_msg($_lang['operate_notice'],$ac_url,1,'this mobile exists!','alert_error');
		}
		isset($formhash)&&kekezu::submitcheck($formhash) and $auth_obj->add_auth($fds,'id_pic');
		break;
	case "step3":
		break;
	case "step4":
		break;
}
if($auth_info['auth_status']==1){
	$auth_tips =$_lang['congratulations_pass_realname_auth'];
	$auth_style = 'success';
}elseif($auth_info['auth_status']==2){
	$auth_tips =$_lang['regrettably_not_pass'];
	$auth_style = 'warning';
}else{
	$auth_tips =$_lang['realname_auth_exan_and_verify_realname_auth'];
	$auth_style = 'warning';
}
if($auth_info['auth_status']==1&&$auth_step=='step2'){
	$auth_step = 'step3';
}
function name_exists($id_card){
	global $uid;
	$sql = sprintf('select count(*) from  %switkey_auth_realname where id_card = \'%s\' and auth_status = 1 and uid != \'%d\'',TABLEPRE,$id_card,$uid);
	return (bool) db_factory::get_count($sql);
}
require keke_tpl_class::template ( 'auth/' . $auth_dir . '/tpl/' . $_K ['template'] . '/auth_add' );