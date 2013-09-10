<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$model_id = '';
$model_list=kekezu::get_table_data ( '*', 'witkey_model', " model_type = 'task' and model_status=1", 'model_id asc ', '', '', 'model_id', 3600 );
$user_join = keke_task_config::get_user_join_task (); 

if($submit){
	$res = db_factory::execute(" update ".TABLEPRE."witkey_space set ensure = 1 where uid = '$uid'");
	echo 1;
	exit;
}
require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );
