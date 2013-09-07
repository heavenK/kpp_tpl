<?php
defined ( 'IN_KEKE' ) or exit('Access Denied');


if($ajax == 1){
	$sql = sprintf ( "update %switkey_space set pid = NULL where uid = '%d'", TABLEPRE, $to_uid );
	$res = db_factory::execute ( $sql );
	echo $res;
	exit;
}
if($ajax == 2){
	$to_user_info = keke_user_class::get_user_info($to_uid);
	keke_msg_class::send_private_message ("申请拜师","威客".$username."要拜您为师，是否<a href=\"aaaaaa\">同意</a>", $to_user_info[uid], $to_user_info[username],'','','ajax');
 
	exit;
}
if($ajax == 3){
	$sql = sprintf ( "update %switkey_space set pid = '%d' where uid = '%d'", TABLEPRE,$uid, $f_uid );
	$res = db_factory::execute ( $sql );
	echo $res;
	
	$sql = sprintf ( "delete from %switkey_msg where title = '申请拜师' and uid = '%d'", TABLEPRE,$f_uid);
	$res = db_factory::execute ( $sql );
	exit;
}
if($ajax == 4){
	$sql = sprintf ( "delete from %switkey_msg where title = '申请拜师' and uid = '%d'", TABLEPRE,$f_uid);
	$res = db_factory::execute ( $sql );
	echo $res;
	exit;
}
// add by heavenK

$task_count = db_factory::get_one ( sprintf ( " select count(task_id) count from %switkey_task", TABLEPRE ), 1, 600 ); 
$task_in = db_factory::get_one ( sprintf ( " select sum(fina_cash) cash from %switkey_finance where fina_action='task_bid' and fina_type='in' ", TABLEPRE ), 1, 600 ); 
$register = db_factory::get_one ( sprintf ( " select count(uid) count from %switkey_member ", TABLEPRE ), 1, 600 ); 

$task_count =  intval ( $task_count ['count'] );
$task_in = number_format ( $task_in ['cash'], 2, ".", "," );
$register =  intval ( $register ['count'] );
// end


$ops = array ('list','req');
if(!$op) $op = 'list';
$ops = array_merge($ops);
in_array($op,$ops) or $op =$ops[1];

$auth_item = keke_auth_base_class::get_auth_item ();
$auth_temp = array_keys ( $auth_item );
$user_info ['user_type'] == 2 and $un_code = 'realname' or $un_code = "enterprise";
$t = implode ( ",", $auth_temp );
$auth_info = db_factory::query ( " select a.auth_code,a.auth_status,b.auth_title,b.auth_small_ico,b.auth_small_n_ico from " . TABLEPRE . "witkey_auth_record a left join " . TABLEPRE . "witkey_auth_item b on a.auth_code=b.auth_code where a.uid ='$uid' and FIND_IN_SET(a.auth_code,'$t')", 1, - 1 );
$auth_info = kekezu::get_arr_by_key ( $auth_info, "auth_code" );


$sql = sprintf ( "select distinct(uid) from %switkey_msg where ", TABLEPRE);
$where = " title = '申请拜师' and to_uid = " . $uid . " and on_time >= " .strtotime("today");
$req_count = count(db_factory::query ( $sql . $where ));

if($op == 'list'){
	intval ( $page_size ) or $page_size = '6';
	intval ( $page ) or $page = '1';
	$url = $origin_url . "&op=$op&page_size=$page_size&page=$page";
	
	$page_obj = $kekezu->_page_obj;
	$sql = sprintf ( "select * from %switkey_space s left join %switkey_shop p on s.uid=p.uid where ", TABLEPRE, TABLEPRE );
	$where = " pid = " . $uid . " ";
	$count = db_factory::get_count ( sprintf ( "select count(uid) from %switkey_space where %s", TABLEPRE, $where ) );
	$page_obj->setStyle("Pagetype1");
	$pages = $page_obj->getPages ( $count, $page_size, $page, $url, '#userCenter' );
	$shitu_info = db_factory::query ( $sql . $where . $pages ['where'] );

}elseif($op == 'req'){
	$where = " pid = " . $uid . " ";
	$count = db_factory::get_count ( sprintf ( "select count(uid) from %switkey_space where %s", TABLEPRE, $where ) );
	
	
	
	$sql = sprintf ( "select distinct(uid) from %switkey_msg where ", TABLEPRE);
	$where = " title = '申请拜师' and to_uid = " . $uid . " ";

	$uid_list = db_factory::query ( $sql . $where );
	
	foreach($uid_list as $key => $val){
		if($key == 0) $uids = $val['uid'];
		else $uids .= ','.$val['uid'];
	}
	
	if($uids){
		
		intval ( $page_size ) or $page_size = '6';
		intval ( $page ) or $page = '1';
		$url = $origin_url . "&op=$op&page_size=$page_size&page=$page";
		
		$page_obj = $kekezu->_page_obj;
		$sql = sprintf ("select * from %switkey_space s left join %switkey_shop p on s.uid=p.uid where ", TABLEPRE, TABLEPRE );
		$where = " s.uid IN (" . $uids . ")";
		
		$page_obj->setStyle("Pagetype1");
		$pages = $page_obj->getPages ( $count, $page_size, $page, $url, '#userCenter' );
		$shitu_info = db_factory::query ( $sql . $where . $pages ['where'] );
		
	}
}

$user_leve=unserialize($user_info['seller_level']);

require keke_tpl_class::template( "user/" . $do . "_" . $view . "_" . $op );