<?php	defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role(164);
$url = "index.php?do=app&view=$view&page=$page&page_size=$page_size&ord[0]={$ord['0']}&ord[1]={$ord['1']}";
if($space[id]){
	$url.="&space[id]=".$space[id];
}
if($space[title]){
	$url.="&space[title]=".$space[title];
}
if($space[desc]){
	$url.="&space[title]=".$space[desc];
}
$table_obj = keke_table_class::get_instance('witkey_free_service');
$weibo_obj = new Keke_witkey_weibo_class();
$where = '1 = 1';
$page and $page or $page =1;
$page_size  or $page_size = 10;
$space[id] and $where.=" and service_id = ".intval($space[id]);
$space[title] and $where.=" and service_title like '%".$space[title]."%' ";
$space[desc] and $where.=" and service_desc like '%".$space[desc]."%' ";
$ord[0]&&$ord[1] and $where .= " order by {$ord['0']} {$ord['1']}" or $where .= " order by service_id desc";
$data = $table_obj->get_grid($where,$url, $page,$page_size,'','ajax','ajax_dom');
$task_list = $data['data'];
$pages = $data['pages'];
$count = $data['count'];
if($op == 'del'){
	$res = $table_obj->del('service_id', $pro_id);
	$weibo_obj->setWhere("obj_id=".$pro_id." and obj_type='free_service'");
	$weibo_obj->del_keke_witkey_weibo();
	kekezu::admin_show_msg('操作提示',$url,3,'删除成功！','success');
}
if($sbt_action == $_lang['mulit_delete']){
	$rs = $table_obj->del('service_id', $ckb);
	$ids = implode ( ',', $ckb );
	$weibo_obj->setWhere("obj_id in ($ids) and obj_type='free_service'");
	$weibo_obj->del_keke_witkey_weibo();
	kekezu::admin_show_msg('操作提示',$url,3,'批量删除成功！','success');
}
require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );