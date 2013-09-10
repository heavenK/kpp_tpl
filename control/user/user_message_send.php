<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$url = "index.php?do=$do&view=$view&op=$op&msg_type=$msg_type"; 
keke_lang_class::loadlang ( "user_message_send" );
if (isset($formhash)&&kekezu::submitcheck($formhash)){
	!$uid and kekezu::show_msg ( '未登录，无法发送', $url, 1, '发送失败', 'alert_error' );
	$msg_obj = new Keke_witkey_msg_class ();
	$space_obj = new Keke_witkey_space_class ();
	$space_info = kekezu::get_user_info ( $txt_to_username, 1 );
	if (! $space_info) {
		kekezu::show_msg ( $_lang['fail_to_send_instation_messages'], $url, 1, $_lang['the_user_does_not_exist'], 'alert_error' );
	} elseif ($space_info ['uid'] == $uid) {
		kekezu::show_msg ( $_lang['fail_to_send_instation_messages'], $url, 1, $_lang['can_not_send_messages_to_yourself'], 'alert_error' );
	}
	intval ( $hdn_pid ) and $msg_obj->setMsg_pid ( $hdn_pid );
	$msg_obj->setUid ( $uid );
	$msg_obj->setUsername ( $username );
	$msg_obj->setTo_uid ( $space_info ['uid'] );
	$msg_obj->setTo_username ( $space_info ['username'] );
	if (! $txt_msg_title) {
		kekezu::show_msg ( $_lang['fail_to_send_instation_messages'], $url, 1, $_lang['input_the_title'], 'alert_error' );
	}
	$msg_obj->setTitle ( kekezu::str_filter ( kekezu::escape($txt_msg_title) ) );
	if (! $txt_msg_content) {
		kekezu::show_msg ( $_lang['fail_to_send_instation_messages'], $url, 1, $_lang['input_the_content'], 'alert_error' );
	}
	$msg_obj->setContent ( kekezu::str_filter (kekezu::escape($txt_msg_content) ) );
	$msg_obj->setOn_time ( time () );
	$res = $msg_obj->create_keke_witkey_msg();
	if ($res) {
		kekezu::show_msg ( $_lang['success_to_send_instation_messages'], $url, 1, $_lang['station_message_has_been_successfully_sent_to'] . $space_info ['username'],'alert_right');
	} else {
		kekezu::show_msg ( $_lang['fail_to_send_instation_messages'], $url, 1, $_lang['fail_to_send_instation_messages'], 'alert_error' );
	}
}

$task_count = db_factory::get_one ( sprintf ( " select count(task_id) count from %switkey_task", TABLEPRE ), 1, 600 ); 
$task_in = db_factory::get_one ( sprintf ( " select sum(fina_cash) cash from %switkey_finance where fina_action='task_bid' and fina_type='in' ", TABLEPRE ), 1, 600 ); 
$register = db_factory::get_one ( sprintf ( " select count(uid) count from %switkey_member ", TABLEPRE ), 1, 600 ); 

$task_count =  intval ( $task_count ['count'] );
$task_in = number_format ( $task_in ['cash'], 2, ".", "," );
$register =  intval ( $register ['count'] );
require keke_tpl_class::template ( "user/user_message_send" );
