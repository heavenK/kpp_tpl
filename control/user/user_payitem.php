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
require 'user_' . $op . '.php';