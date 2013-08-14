<?php	defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role(165);
$url = "index.php?do=app&view=$view&page=$page&page_size=$page_size&ord[0]={$ord['0']}&ord[1]={$ord['1']}";
if($space[id]){
	$url.="&space[id]=".$space[id];
}
if($space[content]){
	$url.="&space[content]=".$space[content];
}
$table_obj = keke_table_class::get_instance('witkey_free_message');
$where = '1 = 1';
$page and $page or $page =1;
$page_size  or $page_size = 10;
$space[id] and $where.=" and comment_id = ".intval($space[id]);
$space[content] and $where.=" and content like '%".$space[content]."%' ";
$ord[0]&&$ord[1] and $where .= " order by {$ord['0']} {$ord['1']}" or $where .= " order by comment_id desc";
$data = $table_obj->get_grid($where,$url, $page,$page_size,'','ajax','ajax_dom');
$message_list = $data['data'];
$pages = $data['pages'];
$count = $data['count'];
if($op == 'del'){
	$res = $table_obj->del('comment_id', $pro_id);
	kekezu::admin_show_msg('操作提示',$url,3,'删除成功！','success');
}
if($sbt_action == $_lang['mulit_delete']){
	$rs = $table_obj->del('comment_id', $ckb);
	kekezu::admin_show_msg('操作提示',$url,3,'批量删除成功！','success');
}
require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );