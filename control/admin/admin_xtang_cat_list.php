<?php
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
$url = 'index.php?do=xtang&view=cat_list';
$cat_arr_1 = db_factory::query ( sprintf ( "select * from %sxtang_type where pid=0 order by list_order asc", TABLEPRE ) );
$cat_arr_2 = db_factory::query ( sprintf ( "select * from %sxtang_type where pid>0 order by list_order asc", TABLEPRE ) );


$cat_show_arr = array();
foreach($cat_arr_1 as $key => $val){
	if($val['pid'] == 0) $cat_show_arr[$val['id']] = $val;
	else $cat_show_arr[$val['pid']]['sub'][$key] = $val;
}
foreach($cat_arr_2 as $key => $val){
	if($val['pid'] == 0) $cat_show_arr[$val['id']] = $val;
	else $cat_show_arr[$val['pid']]['sub'][$key] = $val;
}
if($ac == 'del' && $type_id){
	$res = db_factory::execute(" delete from ".TABLEPRE."xtang_type where id=".$type_id);
	kekezu::admin_show_msg($_lang['operate_success'],$url,3,'','success');
}

require  $template_obj->template('control/admin/tpl/admin_'. $do .'_'. $view);
