<?php defined ( 'ADMIN_KEKE' )or exit ( 'Access Denied' );

$cat_arr = db_factory::query ( sprintf ( "select * from %sxtang_type where pid>0 order by list_order asc", TABLEPRE ) );

if($sid){
	$zsd_info = db_factory::get_one ( sprintf ( "select * from %sxtang_article where sid=".$sid." order by pub_time desc", TABLEPRE ) );
	
	if($sbt_edit){
		if(!isset($isShow)) $isShow = 0;
		if(!isset($isTop)) $isTop = 0;
		
		$res = db_factory::execute(" update ".TABLEPRE."xtang_article set type_id=".$type_id.", pic='".$fields['art_pic']."', title='".$title."', keyword='".$keyword."', description='".$fields['description']."', content='".$fields['content']."', isShow=".$isShow.", isTop=".$isTop."  where sid = ".intval($sid));
	}
}else{
	if($sbt_edit){
		if(!isset($isShow)) $isShow = 0;
		if(!isset($isTop)) $isTop = 0;
		//var_dump(" insert into ".TABLEPRE."xtang_type value('','".$type_name."','".$fields['art_pic']."',".$isShow.",".$list_order.",".$pid.")");
		$res = db_factory::execute(" insert into ".TABLEPRE."xtang_article value('',".$type_id.",'".$title."','".$fields['art_pic']."','".$keyword."','".$fields['description']."','".$fields['content']."','".$admin_info['uid']."','".$admin_info['username']."',".time().",".$isTop.",0,".$isShow.")");
	}
	
}

$url = 'index.php?do=xtang&view=zsd_list';
if($res)	kekezu::admin_show_msg($_lang['operate_success'],$url,3,'','success');

require  $template_obj->template('control/admin/tpl/admin_'. $do .'_'. $view);