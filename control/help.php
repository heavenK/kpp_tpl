<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
if(!isset($op)) $op = 'index';


$art_cat_arr = kekezu::get_table_data('*',"witkey_article_category","cat_type='help'"," art_cat_id desc",'','','art_cat_id',null);
$where = ' 1 = 1 ';	
$where.=" and cat_type='help' ";
$cat_arr = kekezu::get_table_data ( "*", "witkey_article_category", $where, "", "", "", "", 0 );
sort ( $cat_arr );
$t_arr = array ();
kekezu::get_tree ( $cat_arr, $t_arr, 'cat', NULL, 'art_cat_id', 'art_cat_pid', 'cat_name' );
$cat_show_arr = $t_arr;
unset ( $t_arr );
unset($cat_show_arr[0]); 

$tree_list = array();
foreach($cat_show_arr as $key => $val){
	if($val['art_cat_pid'] == 100) $tree_list[$val['art_cat_id']] = $val;
	else $tree_list[$val['art_cat_pid']]['subtree'][$key] = $val;
}


// add by heavenk
$task_count = db_factory::get_one ( sprintf ( " select count(task_id) count from %switkey_task", TABLEPRE ), 1, 600 ); 
$task_in = db_factory::get_one ( sprintf ( " select sum(fina_cash) cash from %switkey_finance where fina_action='task_bid' and fina_type='in' ", TABLEPRE ), 1, 600 ); 
$register = db_factory::get_one ( sprintf ( " select count(uid) count from %switkey_member ", TABLEPRE ), 1, 600 ); 

$task_count =  intval ( $task_count ['count'] );
$task_in = number_format ( $task_in ['cash'], 2, ".", "," );
$register =  intval ( $register ['count'] );
// end


if($op == 'index'){
	$sql = "select * from " . TABLEPRE . "witkey_article where art_cat_id=369 order by pub_time desc limit 0,7";
	$art_list_arr = db_factory::query ( $sql );
	
	$sql = "select * from " . TABLEPRE . "witkey_article where cat_type='help' order by pub_time desc limit 0,9";
	$art_list_arr_new = db_factory::query ( $sql );
	require keke_tpl_class::template ( $do."_".$op );
}elseif($op == 'info' || $op == 'list'){
	
	if($art_id){
		$art_info = db_factory::get_one ( sprintf ( " select * from %switkey_article where art_id=%d", TABLEPRE, $art_id )); 
	}
	if($art_cat_id){
		$sql = "select * from " . TABLEPRE . "witkey_article where art_cat_id=".$art_cat_id." order by pub_time desc";
		$art_list = db_factory::query ( $sql );
	}
	if($sbt){
		$sql = "select * from " . TABLEPRE . "witkey_article where art_title LIKE '%".$key_word."%'".$art_cat_id." order by pub_time desc";
		$art_list = db_factory::query ( $sql );
	}
	
	$sql = "select * from " . TABLEPRE . "witkey_article where art_cat_id=369 order by pub_time desc limit 0,5";
	$art_list_arr = db_factory::query ( $sql );
	
	$sql = "select * from " . TABLEPRE . "witkey_article where cat_type='help' order by pub_time desc limit 0,5";
	$art_list_arr_new = db_factory::query ( $sql );
	require keke_tpl_class::template ( $do."_info" );
}


