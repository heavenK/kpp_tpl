<?php
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
$url = 'index.php?do=xtang&view=zsd_list';
$art_arr = db_factory::query ( sprintf ( "select * from %sxtang_article order by pub_time desc", TABLEPRE ) );


if($ac == 'del' && $sid){
	$res = db_factory::execute(" delete from ".TABLEPRE."xtang_article where sid=".$sid);
	kekezu::admin_show_msg($_lang['operate_success'],$url,3,'','success');
}

$res = db_factory::query ( sprintf ( "select id,type_name from %sxtang_type where pid>0", TABLEPRE ) );
$type_arr = array();
foreach($res as $key => $val){
	$type_arr[$val['id']] = $val['type_name'];
}

require  $template_obj->template('control/admin/tpl/admin_'. $do .'_'. $view);
