<?php	defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role(166);
$url = "index.php?do=app&view=$view&page=$page&page_size=$page_size&ord[0]={$ord['0']}&ord[1]={$ord['1']}";
$model_info = kekezu::get_table_data ( '*', 'witkey_model', 'model_status=1', '', '', '', 'model_code' );
if($space[id]){
	$url.="&space[id]=".$space[id];
}
if($space[title]){
	$url.="&space[title]=".$space[title];
}
if($space[type]){
	$url.="&space[type]=".$space[type];
}
$table_obj = keke_table_class::get_instance('witkey_weibo');
$where = '1 = 1';
$page and $page or $page =1;//当前页码
$page_size  or $page_size = 10;//每页显示数
//  搜索条件
$space[id] and $where.=" and weibo_id = ".intval($space[id]);
$space[title] and $where.=" and obj_title like '%".$space[title]."%' ";
$space[type] and $where.=" and detail_type like '%".$space[type]."%' ";
//排序
$ord[0]&&$ord[1] and $where .= " order by {$ord['0']} {$ord['1']}" or $where .= " order by weibo_id desc";
$data = $table_obj->get_grid($where,$url, $page,$page_size,'','ajax','ajax_dom');
$weibo_list = $data['data'];
$pages = $data['pages']; 
$count = $data['count'];


//删除
if($op == 'del'){
	$res = $table_obj->del('weibo_id', $pro_id);
	kekezu::admin_show_msg('操作提示',$url,3,'删除成功！','success');
}
//批量删除
if($sbt_action == $_lang['mulit_delete']){
	$rs = $table_obj->del('weibo_id', $ckb);
	kekezu::admin_show_msg('操作提示',$url,3,'批量删除成功！','success');
}


require $template_obj->template ( 'control/admin/tpl/admin_' . $do . '_' . $view );