<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
if ($model_id) {
	$cove_arr = kekezu::get_table_data ( "*", "witkey_task_cash_cove", "", "", "", "", "cash_rule_id" );
	$third_nav = array ();
	foreach ( $model_list as $v ) {
		$third_nav [] = array ("1" => $v ['model_id'], "2" => $_lang ['go_in'] . $v ['model_name'] );
	}
	$third_nav = ( array ) $third_nav;
	$model_info = $model_list [$model_id]; 

	switch ($model_info ['model_code']) {
		case "sreward" :
		case "preward" :
		case "mreward" :
		case "wbzf" :
		case "wbdj" :
		case "match" :
		case "taobao" :
			$tab_name = "witkey_task_work";
			$time_fds = "work_time";
			$id_fds = "work_id";
			$satus_fds = "work_status";
			break;
		case "dtender" :
		case "tender" :
			$tab_name = "witkey_task_bid";
			$time_fds = "bid_time";
			$id_fds = "bid_id";
			$satus_fds = "bid_status";
			break;
	}
	$user_join = keke_task_config::get_user_join_task (); 
	$ord_arr = array (" a.$id_fds desc " => $_lang ['manuscript_id_desc'], " a.$id_fds asc " => $_lang ['manuscript_id_asc'], " a.$time_fds desc " => $_lang ['submit_time_desc'], " a.$time_fds asc " => $_lang ['submit_time_asc'] );
	$cln = $model_info ['model_code'] . "_task_class";
	$page_obj = $kekezu->_page_obj;
	$status_arr = call_user_func ( array ($cln, "get_task_status" ) );
	$work_arr = call_user_func ( array ($cln, "get_work_status" ) );
	$work_arr [0] = $_lang ['yet_deal_with'];
	$join_count = intval ( db_factory::get_count ( sprintf ( "select count(task_id) from %s%s
 	where YEARWEEK(FROM_UNIXTIME(%d)) = YEARWEEK('%s') and uid='%d' ", TABLEPRE, $tab_name, $time_fds, date ( 'Y-m-d H:i:s', time () ), $uid ) ) );
	$sql = " select a.$satus_fds,a.$time_fds,a.$id_fds,b.task_id,b.task_cash,b.task_title,b.model_id,b.task_cash_coverage,b.task_status from " . TABLEPRE . $tab_name . " a left join " . TABLEPRE . "witkey_task b on a .task_id=b.task_id where b.model_id = '$model_id' and a.uid='$uid'";
	$count_sql = "select a.$id_fds from " . TABLEPRE . $tab_name . " a left join " . TABLEPRE . "witkey_task b on a .task_id=b.task_id where b.model_id = '$model_id' and a.uid='$uid'";
	if($task_status>-1){
		($task_status === '0') and $where .= " and b.task_status='" . intval ( $task_status ) . "'" or ($task_status and $where .= " and b.task_status = '" . intval ( $task_status ) . "' ");
	}
	($$satus_fds === '0') and $where .= " and a.$satus_fds='" . intval ( $$satus_fds ) . "'" or ($$satus_fds and $where .= " and a.$satus_fds = '" . intval ( $$satus_fds ) . "' ");
	$$id_fds && $$id_fds != $_lang ['enter_manuscript_id'] and $where .= " and a.$id_fds = '" . intval ( $$id_fds ) . "' ";
	$ord and $where .= " order by $ord " or $where .= " order by a.$id_fds desc ";
	$page_size and $page_size = intval ( $page_size ) or $page_size = '10';
	$page and $page = intval ( $page ) or $page = '1';
	$url = "index.php?do=$do&view=$view&op=$op&model_id=$model_id&page_size=$page_size&task_status=$task_status&$satus_fds=" . $$satus_fds . "&page=$page";
	$count = intval ( db_factory::execute ( $count_sql . $where ) );
	$pages = $page_obj->getPages ( $count, $page_size, $page, $url );
	$task_info = db_factory::query ( $sql . $where . $pages ['where'] );
}
if (isset ( $ac )) {
	$work_id = intval ( $work_id );
	if ($work_id) {
		switch ($ac) {
			case "del" :
				$res = db_factory::execute ( sprintf ( " delete from %switkey_task_work where work_id='%d'", TABLEPRE, $work_id ) );
				db_factory::execute ( sprintf ( ' delete from %switkey_comment where obj_id=%d and obj_type="work"', TABLEPRE, $work_id ) );
				keke_file_class::del_obj_file ( $work_id, 'work', true );
				$res and kekezu::show_msg ( $_lang ['operate_notice'], $url, 3, $_lang ['manuscript_del_succ'], 'alert_right' ) or kekezu::show_msg ( $_lang ['operate_notice'], $url, 3, $_lang ['manuscript_del_fail'], 'alert_error' );
				break;
		}
	} else {
		kekezu::show_msg ( $_lang ['operate_notice'], $url, 3, $_lang ['please_choose_delete_task'], "alert_error" );
	}
}
function wiki_opera($m_id, $t_id, $w_id, $url) {
	global $kekezu;
	$button = array ();
	$model_code = $kekezu->_model_list [$m_id] ['model_code'];
	$c = $model_code . '_task_class';
	if ($model_code && method_exists ( $c, 'wiki_opera' )) {
		$button = call_user_func_array ( array ($c, 'wiki_opera' ), array ($m_id, $t_id, $w_id, $url ) );
	} else { 
		$button = call_user_func_array ( array ('sreward_task_class', 'wiki_opera' ), array ($m_id, $t_id, $w_id, $url ) );
	}
	return $button;
}


$auth_item = keke_auth_base_class::get_auth_item ();
$auth_temp = array_keys ( $auth_item );
$user_info ['user_type'] == 2 and $un_code = 'realname' or $un_code = "enterprise";
$t = implode ( ",", $auth_temp );
$auth_info = db_factory::query ( " select a.auth_code,a.auth_status,b.auth_title,b.auth_small_ico,b.auth_small_n_ico from " . TABLEPRE . "witkey_auth_record a left join " . TABLEPRE . "witkey_auth_item b on a.auth_code=b.auth_code where a.uid ='$uid' and FIND_IN_SET(a.auth_code,'$t')", 1, - 1 );
$auth_info = kekezu::get_arr_by_key ( $auth_info, "auth_code" );

$task_5 = 0;
$task_4 = 0;
$task_7 = 0;
foreach($task_info as $val){
	if($val['work_status'] == 5) $task_5++;
	if($val['work_status'] == 4) $task_4++;
	if($val['work_status'] == 7) $task_7++;
}


$follow_obj = keke_table_class::get_instance('witkey_free_follow');
$where = " 1 = 1 ";
$limit = 5;
$curpage or $curpage = 1;
if(!$type||$type == 1){
	$sql  = sprintf("select f.*,s.uid focus_uid,s.username focus_username,s.seller_level  from %switkey_free_follow as f left join %switkey_space as s on f.fuid = s.uid where ",TABLEPRE,TABLEPRE);
	$where .= " and f.uid = ".$uid;
}
$order .= " order by f.on_time desc "; 
$count = db_factory::execute($sql.$where);
$pages = $kekezu->_page_obj->getPages($count, $limit, $curpage, $url);
$follow_list = db_factory::query($sql.$where.$pages['where']);



$sql = "select * from " . TABLEPRE . "witkey_article where art_cat_id=203 order by pub_time desc limit 0,5";
$art_list_arr = db_factory::query ( $sql );

$sql = "select * from " . TABLEPRE . "witkey_article where art_cat_id=5 order by pub_time desc limit 0,5";
$art_list_arr_1 = db_factory::query ( $sql );


require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );
