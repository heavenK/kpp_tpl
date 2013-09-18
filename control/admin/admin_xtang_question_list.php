<?php
defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );

$url = 'index.php?do=xtang&view=question_list';
$question_arr = db_factory::query ( sprintf ( "select * from %sxtang_question order by qid desc", TABLEPRE ) );


if($ac == 'del' && $qid){
	$res = db_factory::execute(" delete from ".TABLEPRE."xtang_question where qid=".$qid);
	kekezu::admin_show_msg($_lang['operate_success'],$url,3,'','success');
}


require  $template_obj->template('control/admin/tpl/admin_'. $do .'_'. $view);
