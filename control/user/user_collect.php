<?php	defined ( 'IN_KEKE' ) or exit('Access Denied');
$ops = array ('task','work','service','shop');
if($task_open==0){
	unset($ops[0],$ops[1]);
}
if($shop_open==0){
	unset($ops[2]);
}
$ops = array_merge($ops);
in_array($op,$ops) or $op =$ops[0];
$sub_nav=array(
			array("task"=>array( $_lang['collection_of_task'],"document"),
			   "work"=>array( $_lang['collection_of_work'],"doc-empty"),
			   "service"=>array( $_lang['collection_of_service'],"layers-1"),
				"shop"=>array( $_lang['collection_of_shop'],"shop_cart"))
			);
if($task_open==0){
	unset($sub_nav[0]['task'],$sub_nav[0]['work']);
}
if($shop_open==0){
	unset($sub_nav[0]['service']);
}
$model_name_arr = 	kekezu::get_table_data ( 'model_code,model_name', 'witkey_model', '', 'model_id asc ', '', '', 'model_code');
$title = kekezu::lang("collection_of_".$op);
$favor_obj=new Keke_witkey_favorite_class();
$favor_table_obj = new keke_table_class('witkey_favorite');
$page_obj=$kekezu->_page_obj;
if(isset($ac)&&$ac=='del'){    
			if($f_id){ 
				$res = $favor_table_obj->del("f_id",$f_id);
				$res and kekezu::show_msg($_lang['operate_tips'],"index.php?do=$do&view=$view&op=$op&page=$page",1,$_lang['del_success'],"alert_right");
			}else{
			   kekezu::show_msg($_lang['operate_tips'],"index.php?do=$do&view=$view&op=$op&page=$page",1,$_lang['select_null_for_delete'],"alert_error");
			}	  
}
$ord_arr=array("f_id desc "=> $_lang['collection_num_desc'],
		   "f_id asc "=> $_lang['collection_num_asc'],
		   "on_date desc"=> $_lang['collection_time_desc'],
		   "on_date asc "=> $_lang['collection_time_asc']);
$where=" uid = '$uid' ";
intval($page) or $page=1;
intval($page_size) or $page_size='10';
$url=$origin_url."&op=$op&obj_type=$obj_type&ord=$ord&page=$page&page_size=$page_size";
$op and $where.=" and keep_type= '$op'";
$f_id&&$f_id!=$_lang['please_enter_the_collection_num'] and $where.=" and f_id = '$f_id' ";
$obj_name&&$obj_name!=$_lang['please_enter_the_collection_name'] and $where.=" and INSTR(obj_name,'$obj_name')>0 ";
$obj_type and $where.=" and obj_type = '$obj_type' ";
$ord and $where.=" order by $ord " or $where.=" order by f_id desc ";
$favor_obj->setWhere($where);
$count=intval($favor_obj->count_keke_witkey_favorite());
$pages=$page_obj->getPages($count, $page_size, $page, $url,"#userCenter");
$favor_obj->setWhere($where.$pages['where']);
$favor_arr=$favor_obj->query_keke_witkey_favorite();



foreach($favor_arr as $key => $val){
	if($key == 0) $ids = $val['obj_id'];
	else $ids .= ','.$val['obj_id'];
	$favor_arr_new[$val['obj_id']] = $val; 
}

if($ids){
	$sql =  sprintf ( " select * from %switkey_task where task_id in (%s)", TABLEPRE, $ids );
	$task_arr = db_factory::query ($sql);
}

foreach($task_arr as $key => $val){
	$task_arr_new[$val['task_id']] = $val;
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

$model_list=kekezu::get_table_data ( '*', 'witkey_model', " model_type = 'task' and model_status=1", 'model_id asc ', '', '', 'model_id', 3600 );
$user_join = keke_task_config::get_user_join_task (); 

$sql = sprintf ( " select count(f_id) count from %switkey_favorite where uid = '$uid' and keep_type = 'task' ", TABLEPRE );
$task_gz_count = db_factory::get_one ($sql, 1, 600 ); 

require keke_tpl_class::template('user/user_'.$view);
