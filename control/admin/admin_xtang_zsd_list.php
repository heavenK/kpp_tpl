<?php
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
$url = 'index.php?do=xtang&view=zsd_list';



$w = " 1=1 ";
$page = max($page,1);
$limit = max($limit,20);
$ord = max($ord,1);

$title and $w .= " and title like '%" . $title . "%'";
$type_id and $w .= " and type_id = " . $type_id ;
$w.=' order by pub_time desc ';
$count =  max(db_factory::get_count(' select count(sid) from '.TABLEPRE.'xtang_article where '.$w),0);
$pages = $kekezu->_page_obj->getPages($count, $limit, $page,$url.'&ord='.$ord.'&limit='.$limit);

$art_arr = db_factory::query ( sprintf ( "select * from %sxtang_article where %s ", TABLEPRE, $w.$pages['where'] ) );


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
