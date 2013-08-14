<?php

defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 167 );
$url = "index.php?do=app&view=$view&page=$page&page_size=$page_size&ord[0]={$ord['0']}&ord[1]={$ord['1']}";
if ($space [id]) {
	$url .= "&space[id]=" . $space [id];
}
if ($space [content]) {
	$url .= "&space[content]=" . $space [content];
}
$table_obj = keke_table_class::get_instance ( 'witkey_free_denounce' );
$where = '1 = 1';
$page and $page or $page = 1; // 当前页码
$page_size or $page_size = 10; // 每页显示数
// 搜索条件
$space [id] and $where .= " and denounce_id = " . intval ( $space [id] );
$space [content] and $where .= " and content like '%" . $space [content] . "%' ";
// 排序
$ord [0] && $ord [1] and $where .= " order by {$ord['0']} {$ord['1']}" or $where .= " order by denounce_id desc";
$type_arr = array (
		'task' => '免费需求',
		'service' => '免费服务',
		'leave' => '留言' 
);

$data = $table_obj->get_grid ( $where, $url, $page, $page_size, '', 'ajax', 'ajax_dom' );
$denounce_list = $data ['data'];
$pages = $data ['pages'];
$count = $data ['count'];

// 删除
if ($op == 'del') {
	if ($from_type != 'leave') {
		// 删除文件
		$sql_1 = "SELECT * FROM " . TABLEPRE . "witkey_file where obj_id = " . $obj_id;
		$file_arr = db_factory::query ( $sql_1 );
		if ($file_arr) {
			foreach ( $file_arr as $ke => $v ) {
				if (file_exists ( S_ROOT . $v ['save_name'] )) {
					unlink ( S_ROOT . $v ['save_name'] );
				}
			}
		}
		// 删除文件所在记录
		$sql_2 = "DELETE From " . TABLEPRE . "witkey_file where obj_id = " . $obj_id;
		db_factory::execute ( $sql_2 );
	}
	// 删除obj_id对象的记录
	if ($from_type == "task") {
		db_factory::execute ( "DELETE From " . TABLEPRE . "witkey_free_task where task_id =" . $obj_id ); // 删除免费任务
		db_factory::execute ( "DELETE From " . TABLEPRE . "witkey_weibo where obj_type = 'free_task' and obj_id =" . $obj_id ); // 删除免费任务产生的微博
	} elseif ($from_type == "service") {
		db_factory::execute ( "DELETE From " . TABLEPRE . "witkey_free_service where service_id=" . $obj_id ); // 删除免费商品/服务
		db_factory::execute ( "DELETE From " . TABLEPRE . "witkey_weibo where  obj_type = 'free_service'  and obj_id =" . $obj_id ); // //删除免费商品/服务产生的微博
	} elseif ($from_type == "leave") {
		db_factory::execute ( "DELETE From " . TABLEPRE . "witkey_free_message where comment_id=" . $obj_id ); // 删除留言
	}
	$res = db_factory::execute ( "update " . TABLEPRE . "witkey_free_denounce set status = 1 where denounce_id =" . $denounce_id ); // 更新举报状态
	$res and kekezu::admin_show_msg ( '操作提示', $url, 3, '删除成功！', 'success' ) or kekezu::admin_show_msg ( '操作提示', $url, 3, '删除失败！', 'success' );
}

require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );