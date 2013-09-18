<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$credit_level = unserialize($member_info['seller_level']);
$indus_arr = $kekezu->_indus_arr;
if($shop_open){
	 $service_obj = new Keke_witkey_service_class();
	 $service_obj->setWhere("uid = ".intval($member_id)." order by on_time desc limit 0,6 ");
	 $service_arr = $service_obj->query_keke_witkey_service();
}

$sql = sprintf ( "select distinct(uid) from %switkey_msg where ", TABLEPRE);
$where = " title = '申请拜师' and to_uid = " . $member_id . " and on_time >= " .strtotime("today");
$req_count = count(db_factory::query ( $sql . $where ));


	$sql = sprintf ( "select * from %switkey_space s left join %switkey_shop p on s.uid=p.uid where ", TABLEPRE, TABLEPRE );
	$where = " pid = " . $member_id . " ";
	$count = db_factory::get_count ( sprintf ( "select count(uid) from %switkey_space where %s", TABLEPRE, $where ) );
	$shitu_info = db_factory::query ( $sql . $where );


	$where = " pid = " . $member_id . " ";
	$count = db_factory::get_count ( sprintf ( "select count(uid) from %switkey_space where %s", TABLEPRE, $where ) );
	$sql = sprintf ( "select distinct(uid) from %switkey_msg where ", TABLEPRE);
	$where = " title = '申请拜师' and to_uid = " . $member_id . " ";
	$uid_list = db_factory::query ( $sql . $where );
	
	
	foreach($uid_list as $key => $val){
		if($key == 0) $uids = $val['uid'];
		else $uids .= ','.$val['uid'];
	}
	
	if($uids){

		$sql = sprintf ("select * from %switkey_space s left join %switkey_shop p on s.uid=p.uid where ", TABLEPRE, TABLEPRE );
		$where = " s.uid IN (" . $uids . ")";

		$shitu_info_1 = db_factory::query ( $sql . $where  );
		
	}


$user_leve=unserialize($user_info['seller_level']);

require keke_tpl_class::template(SKIN_PATH."/space/{$type}_{$view}");
