<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title='广场-我的需求'.'- '.$_K['html_title'];
kekezu::check_login();
$table_obj = keke_table_class::get_instance('witkey_weibo');
$url = "index.php?do=square&view=demand&page=$page&pagesize=$pagesize";
$where = ' 1=1 ';
$page or $page = 1;
$pagesize or $pagesize = 10;
if(isset($type)&&in_array($type, array('task','free_task'))){
	$where .= " and obj_type = '{$type}' and obj_uid = ".$uid;
}else{
	$where.=" and  obj_uid = ".$uid." and obj_type in('task','free_task')";
}
$where .=" order by on_time desc";
$data = $table_obj->get_grid($where ,$url, $page, $pagesize,'','','');
$task_list = $data['data'];
$pages = $data['page'];
require keke_tpl_class::template ( "tpl/" . $_K ['template'] . "/" . $do . "/" .$do."_". $view );