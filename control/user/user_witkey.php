<?php
defined ( 'IN_KEKE' ) or exit('Access Denied');
$ops = array ('pub', 'task', 'shop','g_pub','credit','index','ensure','fav');
if($task_open==0){
	unset($ops[1]);
}
if($shop_open==0){
	unset($ops[0],$ops[2],$ops[3]);
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
	array ("pub" => array ($_lang['pub_goods'], "doc-new" )),
	array ("task" => array ($_lang['my_work'], "doc-lines-stright" ),
		  "g_pub"=>array ($_lang['g_pub'],"notepad"),
 		   "shop" => array ($_lang['sell_goods'], "box" )),
	array ("credit" => array ($_lang['credit_grade'], "cert" ))
);
if($task_open==0){
	unset($sub_nav[1]['task']);
}
if($shop_open==0){
	unset($sub_nav[0],$sub_nav[1]['shop'],$sub_nav[1]['g_pub']);
}
$op=='task' and $model_type='task' or $model_type='shop';

// add by heavenK
if($op == 'index') $model_type = 'task';
if($op == 'ensure') $model_type = 'task';
if($op == 'fav') $model_type = 'task';
//	end

$model_list=kekezu::get_table_data ( '*', 'witkey_model', " model_type = '$model_type' and model_status=1", 'model_id asc ', '', '', 'model_id', 3600 );
$model_fds = array_keys($model_list);
$model_id or $model_id = intval($model_fds['0']);


$user_join = keke_task_config::get_user_join_task ();
$sql = sprintf ( " select count(f_id) count from %switkey_favorite where uid = '$uid' and keep_type = 'task' ", TABLEPRE );
$task_gz_count = db_factory::get_one ($sql, 1, 600 ); 


switch ($op){
	case "pub":
		 header("Location:index.php?do=shop_release");
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
	case "g_pub":
		require 'user_g_pub.php';
		break;
	case "shop":
		$role = 1;
		require 'user_finance_order.php';
		break;
	case "ensure":
		require 'user_'.$view.'_'.$op.'.php';	
		break;
	case "fav":
		require 'user_'.$op.'.php';	
		break;
}