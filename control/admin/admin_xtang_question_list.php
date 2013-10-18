<?php
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );


if($ajax && $sid){
	$question_arr = db_factory::query ( sprintf ( "select * from %sxtang_question where sid=%d order by qid desc", TABLEPRE, $sid ) );
	require  $template_obj->template('control/admin/tpl/admin_'. $do .'_'. $view.'_ajax');
	die();
}


$url = 'index.php?do=xtang&view=question_list';

$w = " 1=1 ";
$page = max($page,1);
$limit = max($limit,20);
$ord = max($ord,1);
$w.=' order by qid desc ';
$count =  max(db_factory::get_count(' select count(qid) from '.TABLEPRE.'xtang_question where '.$w),0);
$pages = $kekezu->_page_obj->getPages($count, $limit, $page,$url.'&ord='.$ord.'&limit='.$limit);


$question_arr = db_factory::query ( sprintf ( "select * from %sxtang_question where %s", TABLEPRE, $w.$pages['where'] ) );


if($ac == 'del' && $qid){
	$res = db_factory::execute(" delete from ".TABLEPRE."xtang_question where qid=".$qid);
	kekezu::admin_show_msg($_lang['operate_success'],$url,3,'','success');
}


require  $template_obj->template('control/admin/tpl/admin_'. $do .'_'. $view);
