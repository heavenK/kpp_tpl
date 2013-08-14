<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title='广场-我的收藏'.'- '.$_K['html_title'];
kekezu::check_login();
$favor_obj = keke_table_class::get_instance('witkey_free_favor');
$url = "index.php?do=square&view=favor&pagesize=$pagesize&page=$page&type=$type";
$where = " 1 = 1  ";
$pagesize = 10;
$page or $page = 1 ;
$sql = "SELECT * FROM `%switkey_free_favor` 
 		 where  ";
if(isset($type)&&$type == 'task'){
	$where.= " and uid = ".$uid." and obj_type = 'free_task'";
}elseif (isset($type)&&$type == 'service'){
	$where.= " and uid = ".$uid." and obj_type = 'free_service'";
}else{
	$where.= " and uid = ".$uid;
}
$where .=" order by on_time desc";
$sql = sprintf($sql,TABLEPRE,TABLEPRE);
$count = db_factory::execute($sql.$where);
$pages = $kekezu->_page_obj->getPages($count, $pagesize, $page, $url);
$favor_arr = db_factory::query($sql.$where.$pages['where']);
$op_arr = array(
			'pub' => '发布',
			'buy' => '购买'
		);
$obj_arr = array(
		'free_service' => '出售',
		'service' => '出售',
		'free_task' => '需求',
		'task' => '需求',
);
if(isset($action)&&$action=="del_favor"){
	$res =  $favor_obj->del('favor_id', $del_favor_id);
	db_factory::execute(sprintf("update %switkey_weibo set focus_num = focus_num -1  where obj_id = '%d' and obj_type = '%s'",TABLEPRE,$obj_id,$obj_type));
	$res and kekezu::echojson($del_favor_id,1,'') or kekezu::echojson('',0,'');
}
require keke_tpl_class::template ( "tpl/" . $_K ['template'] . "/" . $do . "/" .$do."_". $view );