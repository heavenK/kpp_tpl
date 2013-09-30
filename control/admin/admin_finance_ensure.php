<?php
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );


$url = "index.php?do=finance&view=ensure&page_size=$page_size&page=$page&type=$type";


if($ac == 'pass' || $ac == 'nopass'){
	if($ac == 'pass') $ensure = $bz_status;
	else $ensure = 0;
	
	if($t_uid)	$res = db_factory::execute(" update ".TABLEPRE."witkey_space set ensure=".$ensure.", bz_status=0 where uid=".$t_uid);

	
	if($res)	kekezu::admin_show_msg($_lang['operate_success'],$url,3,'','success');
	else	kekezu::admin_show_msg("操作失败！",$url,3,'','warnning');
}


	
	$where = "where bz_status > 0 ";
	
	$count = db_factory::get_count ( sprintf ( "select count(uid) count from %switkey_space ".$where, TABLEPRE ) );
	
	$page_size  and $page_size = intval ( $page_size ) or $page_size = 20;
	$page and $page = intval ( $page ) or $page = 1;
	$pages = $kekezu->_page_obj->getPages ( $count, $page_size, $page, $url );
	
	$where .= " order by uid desc".$pages['where'];
	$user_arr = db_factory::query ( sprintf ( "select * from %switkey_space ".$where, TABLEPRE ) );


require  $template_obj->template('control/admin/tpl/admin_'. $do .'_'. $view);
