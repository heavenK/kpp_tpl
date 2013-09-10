<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$basic_config = $kekezu->_sys_config;
$cls = $model_list [$model_id] ['model_code'] . "_task_class";
$status_arr = call_user_func ( array ($cls, "get_task_status" ) );


$page_obj = $kekezu->_page_obj;
intval ( $page_size ) or $page_size = '12';
intval ( $page ) or $page = '1';
$url = $origin_url . "&op=$op&page_size=$page_size&status=$status&page=$page";

($status === '0') and $where .= " and task_status='$status'" or ($status and $where .= " and task_status = '$status' ");
$task_id && $task_id != $_lang ['input_task_id'] and $where .= " and task_id = '$task_id' ";
$task_title && $task_title != $_lang ['input_task_name'] and $where .= " and INSTR(task_title,'$task_title')>0 ";
$ord and $where .= " order by $ord " or $where .= " order by task_id desc ";


$sql = "select t.* from ".TABLEPRE."witkey_favorite f left join ".TABLEPRE."witkey_task t on f.obj_id=t.task_id where f.uid = '$uid' ";

$count_sql = "select count(task_id) from ".TABLEPRE."witkey_favorite f left join ".TABLEPRE."witkey_task t on f.obj_id=t.task_id where f.uid = '$uid' ";


$count = db_factory::get_count ( $count_sql . $where );

$pages = $page_obj->getPages ( $count, $page_size, $page, $url, '#userCenter' );
$task_info = db_factory::query ( $sql . $where . $pages ['where'] );




require keke_tpl_class::template ( "user/" . $do . "_" . $op );
