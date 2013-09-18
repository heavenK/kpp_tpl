<?php defined ( 'ADMIN_KEKE' )or exit ( 'Access Denied' );
$cat_arr = db_factory::query ( sprintf ( "select * from %sxtang_type where pid>0 order by list_order asc", TABLEPRE ) );


if($sid){
	$zsd_info = db_factory::get_one ( sprintf ( "select * from %sxtang_article where sid=".$sid." order by pub_time desc", TABLEPRE ) );
	
	$question_arr = db_factory::query ( sprintf ( "select * from %sxtang_question where sid=%d or sid is NULL order by qid desc", TABLEPRE, $sid ) );
	
	if($sbt_edit){
		$ids =  implode(',',$question);
		db_factory::execute(" update ".TABLEPRE."xtang_question set sid=NULL where sid = ".$sid);
		$res = db_factory::execute(" update ".TABLEPRE."xtang_question set sid=".$sid." where qid in (".$ids.")");
	}
}



$url = 'index.php?do=xtang&view=zsd_list';
if($res)	kekezu::admin_show_msg($_lang['operate_success'],$url,3,'','success');

require  $template_obj->template('control/admin/tpl/admin_'. $do .'_'. $view);