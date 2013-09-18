<?php defined ( 'ADMIN_KEKE' )or exit ( 'Access Denied' );
if($qid){
	$question_info = db_factory::get_one ( sprintf ( "select * from %sxtang_question where qid=".$qid, TABLEPRE ) );
	
	if($sbt_edit){
		if(!isset($isShow)) $isShow = 0;
		if(!isset($isTop)) $isTop = 0;
		
		$res = db_factory::execute(" update ".TABLEPRE."xtang_question set q_title='".$q_title."', q_answer=".$q_answer.", list_order=".$list_order." where qid = ".intval($qid));
	}
}else{
	if($sbt_edit){
		//var_dump(" insert into ".TABLEPRE."xtang_type value('','".$type_name."','".$fields['art_pic']."',".$isShow.",".$list_order.",".$pid.")");
		$res = db_factory::execute(" insert into ".TABLEPRE."xtang_question(q_title,q_answer,list_order) value('".$q_title."',".$q_answer.",".$list_order.")");
	}
	
}

$url = 'index.php?do=xtang&view=question_list';
if($res)	kekezu::admin_show_msg($_lang['operate_success'],$url,3,'','success');

require  $template_obj->template('control/admin/tpl/admin_'. $do .'_'. $view);