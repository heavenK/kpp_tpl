<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title = '广场-我的留言' . '- ' . $_K ['html_title'];
kekezu::check_login ();
$url = "index.php?do=$do&view=$view&page=$page&pagesize=$pagesize&type=$type";
$message_obj = keke_table_class::get_instance ( 'witkey_free_message' );
$where = " where  1 = 1 ";
$pagesize or $pagesize = 10;
$page or $page = 1;
$sql = sprintf ( "SELECT 
		m.comment_id,m.uid m_uid,m.username m_username,m.content,m.on_time m_on_time,m.obj_id m_obj_id,m.obj_type m_obj_type,m.status,
		t.task_id,t.uid t_uid,t.username t_username,t.task_title,
		s.service_id,s.uid s_uid,s.username s_username,s.service_title
	FROM `%switkey_free_message` m
	LEFT JOIN %switkey_free_task t on m.obj_id = t.task_id
	LEFT JOIN %switkey_free_service s on m.obj_id = s.service_id", TABLEPRE, TABLEPRE, TABLEPRE );
if ($type == 'receive') { 
	$where .= " and (t.uid = " . $uid . " or s.uid = " . $uid . ") and m.status!=0 ";
} elseif ($type == 'send') { 
	$where .= " and  m.uid = " . $uid;
} else {
	$where .= " and (t.uid = " . $uid . " or m.uid = " . $uid . " or s.uid = " . $uid . ") and m.status!=0";
}
$where .= " and m.p_id <= 0 order by m_on_time desc ";
$count = db_factory::execute ( $sql . $where );
$pages = $kekezu->_page_obj->getPages ( $count, $pagesize, $page, $url );
$message_list = db_factory::query ( $sql . $where . $pages ['where'] );
if (isset ( $ac ) && $ac == 'del') {
	$res = $message_obj->del ( 'comment_id', $message_id );
	$res and kekezu::echojson ( '删除成功', 1, array (
			'res' => $message_id 
	) ) or kekezu::echojson ( '删除失败', 0, '' );
}
require keke_tpl_class::template ( "tpl/" . $_K ['template'] . "/" . $do . "/" . $do . "_" . $view );