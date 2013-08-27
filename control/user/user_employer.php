<?php	defined ( 'IN_KEKE' ) or exit('Access Denied');
$ops = array ('pub', 'task', 'shop','credit','index');
if($task_open==0){
	unset($ops[1]);
}
if($shop_open==0){
	unset($ops[2]);
}
// add by heavenK
if(!$op) $op = 'index';

$task_count = db_factory::get_one ( sprintf ( " select count(task_id) count from %switkey_task", TABLEPRE ), 1, 600 ); 
$task_in = db_factory::get_one ( sprintf ( " select sum(fina_cash) cash from %switkey_finance where fina_action='task_bid' and fina_type='in' ", TABLEPRE ), 1, 600 ); 
$register = db_factory::get_one ( sprintf ( " select count(uid) count from %switkey_member ", TABLEPRE ), 1, 600 ); 

$task_count =  intval ( $task_count ['count'] );
$task_in = number_format ( $task_in ['cash'], 2, ".", "," );
$register =  intval ( $register ['count'] );
// end
$ops = array_merge($ops);
in_array($op,$ops) or $op =$ops[1];
$sub_nav = array(
	array ("pub" => array ($_lang['pub_task'], "doc-new" )),
	array ("task" => array ($_lang['my_task'], "doc-lines-stright" ), 
	 		"shop" => array ($_lang['goods_trans'], "box" )),
	array ("credit" => array ($_lang['credit_grade'], "cert" ))
		);
if($task_open==0){
	unset($sub_nav[0],$sub_nav[1]['task']);
}
if($shop_open==0){
	unset($sub_nav[1]['shop']);
}
$model_list=kekezu::get_table_data ( '*', 'witkey_model', " model_type = '$op' and model_status=1", 'model_id asc ', '', '', 'model_id', 3600 );
$model_fds = array_keys($model_list);
$model_id or $model_id = intval($model_fds['0']);
$third_nav = array ();

// modify by heavenK
$task_count1 = kekezu::get_table_data ( "model_id,count(task_id) as count", "witkey_task", " uid = '$uid' ", '', 'model_id', '', 'model_id' );
$model_list1 = kekezu::get_table_data ( '*', 'witkey_model', " model_type = 'task' and model_status=1", 'model_id asc ', '', '', 'model_id', 3600 );
foreach ( $model_list1 as $v ) {
	$third_nav [] = array ("1" => $v ['model_id'], "2" => $v ['model_name'], "3" => intval ( $task_count1 [$v ['model_id']] ['count'] ) );
}
$third_nav = ( array ) $third_nav;
// end

switch ($op){
	case "pub":
		 header("Location:index.php?do=release");
		break;
	case "index":
		require 'user_'.$view.'_'.$op.'.php';	
		break;
	case "task":
		require 'user_'.$view.'_'.$op.'.php';	
		break;
	case "credit":
		require 'user_credit.php';
		break;
	case "shop": 
		$role = 2;
		require 'user_finance_order.php';
		break;
}
