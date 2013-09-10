<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$ops = array ('basic', 'picture','credit', 'safe','fina_account', 'account_bind','space','rights','report');
in_array ( $op, $ops ) or $op = "basic";
$sub_nav =array(
	 array (
			"basic" => array ( $_lang['basics'], "contact-card" ),
	 		"picture" => array ( $_lang['head_icon_setting'], "picture" ),
	 ),
	 array (
			"safe" => array ( $_lang['safe_set'], "shield" )
	 ),
	 array (
			"account_bind" => array ( $_lang['accoutn_bind'], "openid" ),
	 ),
	 );
	 
$task_count = db_factory::get_one ( sprintf ( " select count(task_id) count from %switkey_task", TABLEPRE ), 1, 600 ); 
$task_in = db_factory::get_one ( sprintf ( " select sum(fina_cash) cash from %switkey_finance where fina_action='task_bid' and fina_type='in' ", TABLEPRE ), 1, 600 ); 
$register = db_factory::get_one ( sprintf ( " select count(uid) count from %switkey_member ", TABLEPRE ), 1, 600 ); 

$task_count =  intval ( $task_count ['count'] );
$task_in = number_format ( $task_in ['cash'], 2, ".", "," );
$register =  intval ( $register ['count'] );

require 'user_' . $op . '.php';
