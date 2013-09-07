<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$ops = array (
		'setting','case','service','member','news' );
in_array ( $op, $ops ) or $op = "setting";
$sub_nav = array (
		array (
				"setting" => array (
						'店铺管理',
						"browser" 
				)
		) 
)
;
// add by heavenK
if(!$op) $op = 'index';

$task_count = db_factory::get_one ( sprintf ( " select count(task_id) count from %switkey_task", TABLEPRE ), 1, 600 ); 
$task_in = db_factory::get_one ( sprintf ( " select sum(fina_cash) cash from %switkey_finance where fina_action='task_bid' and fina_type='in' ", TABLEPRE ), 1, 600 ); 
$register = db_factory::get_one ( sprintf ( " select count(uid) count from %switkey_member ", TABLEPRE ), 1, 600 ); 

$task_count =  intval ( $task_count ['count'] );
$task_in = number_format ( $task_in ['cash'], 2, ".", "," );
$register =  intval ( $register ['count'] );
// end
require 'user_'.$view."_".$op.".php";
