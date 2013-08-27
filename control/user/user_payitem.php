<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$ops = array (
		'auth',
		'toolbox',
		'promotion',
		'payitem_task' 
);
if ($task_open == 0 && $shop_open == 0) {
	unset ( $ops [1], $ops [3] );
}
if (! $auth_item_list) {
	unset ( $ops [0] );
}
$ops = array_merge ( $ops );
in_array ( $op, $ops ) or $op = $ops [0];
$sub_nav = array (
		array (
				"auth" => array (
						kekezu::lang ( "auth" ),
						"document" 
				) 
		),
		array (
				"toolbox" => array (
						kekezu::lang ( "toolbox" ),
						"icon16 box" 
				) 
		) 
);
if ($task_open == 0 && $shop_open == 0) {
	unset ( $sub_nav [0] ['toolbox'] );
}
if (! $auth_item_list) {
	unset ( $sub_nav [0] ['auth'] );
}
$auth_item_all = keke_auth_base_class::get_auth_item ( null, null, 1 ,'');;
if ($user_info ['user_type'] != 2) { 
	unset ( $auth_item_list ['enterprise'] );
}

$task_count = db_factory::get_one ( sprintf ( " select count(task_id) count from %switkey_task", TABLEPRE ), 1, 600 ); 
$task_in = db_factory::get_one ( sprintf ( " select sum(fina_cash) cash from %switkey_finance where fina_action='task_bid' and fina_type='in' ", TABLEPRE ), 1, 600 ); 
$register = db_factory::get_one ( sprintf ( " select count(uid) count from %switkey_member ", TABLEPRE ), 1, 600 ); 

$task_count =  intval ( $task_count ['count'] );
$task_in = number_format ( $task_in ['cash'], 2, ".", "," );
$register =  intval ( $register ['count'] );

require 'user_' . $op . '.php';