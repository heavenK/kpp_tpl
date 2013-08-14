<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title='广场-我的关注'.'- '.$_K['html_title'];
kekezu::check_login();
$url = "index.php?do=$do&view=$view";
$follow_obj = keke_table_class::get_instance('witkey_free_follow');
$where = " 1 = 1 ";
$limit = 10;
$curpage or $curpage = 1;
if(!$type||$type == 1){
	$sql  = sprintf("select f.*,s.uid focus_uid,s.username focus_username,s.seller_level  from %switkey_free_follow as f left join %switkey_space as s on f.fuid = s.uid where ",TABLEPRE,TABLEPRE);
	$where .= " and f.uid = ".$uid;
}elseif ($type == 2){
	$sql  = sprintf("select f.*,s.uid focus_uid,s.username focus_username,s.seller_level from %switkey_free_follow as f left join %switkey_space as s on f.uid = s.uid where ",TABLEPRE,TABLEPRE);
	$where .=" and f.fuid = ".$uid;
}elseif ($type == 3){
	$sql  = sprintf("select a.*,b.*,s.uid focus_uid,s.username focus_username,s.seller_level from %switkey_free_follow as a
			left join  %switkey_free_follow as b
			on a.uid = b.fuid
			left join %switkey_space as s 
			on a.fuid = s.uid where ",TABLEPRE,TABLEPRE,TABLEPRE);
	$where .=" and a.fuid =  b.uid and a.uid =".$uid;
}else{
	kekezu::show_msg('操作提示',$url,3,'参数错误');
}
$order .= " order by f.on_time desc "; 
$count = db_factory::execute($sql.$where);
$pages = $kekezu->_page_obj->getPages($count, $limit, $curpage, $url);
$follow_list = db_factory::query($sql.$where.$pages['where']);
if(isset($action)){
	switch ($action) {
		case 'cancel':
		case 'remove':
		case 'remove_follow':
			$res = $follow_obj->del('follow_id',$ac_id );
			$res and kekezu::echojson($ac_id,1,'');
		break;
		case 'remove_focus':
			$res = db_factory::execute(sprintf("delete from %switkey_free_follow where uid = '%d' and fuid = '%d'",TABLEPRE,$uid,$ac_id));
			$res and kekezu::echojson($ac_id,1,array('res'=>'remove_focus'));
			break;
		case 'add':
			$fd['uid'] = $uid;
			$fd['fuid'] = intval($ac_id);
			$fd['on_time'] = time();
			$res = $follow_obj->save($fd);
			$res and kekezu::echojson($ac_id,1,'');
		break;
		case 'add_focus':
			$fd['uid'] = $uid;
			$fd['fuid'] = intval($ac_id);
			$fd['on_time'] = time();
			$res = $follow_obj->save($fd);
			$res and kekezu::echojson($ac_id,1,array('res'=>'add_focus'));
	}
}
require keke_tpl_class::template ( "tpl/" . $_K ['template'] . "/" . $do . "/" .$do."_". $view );