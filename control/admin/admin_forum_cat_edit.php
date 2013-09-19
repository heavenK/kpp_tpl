<?php defined ( 'ADMIN_KEKE' )or exit ( 'Access Denied' );

$cat_arr = db_factory::query ( sprintf ( "select * from %sforum_type where pid=0 order by list_order asc", TABLEPRE ) );

if($type_id){
	$type_info = db_factory::get_one ( sprintf ( "select * from %sforum_type where id=".$type_id." order by list_order asc", TABLEPRE ) );
	
	if($sbt_edit){
		$res = db_factory::execute(" update ".TABLEPRE."forum_type set type_name='".$type_name."', pic='".$fields['art_pic']."', isShow='".$isShow."', list_order='".$list_order."', pid='".$pid."'  where id = ".intval($type_id));
	}
}else{
	if($sbt_edit){
		if(!isset($isShow)) $isShow = 0;
		//var_dump(" insert into ".TABLEPRE."xtang_type value('','".$type_name."','".$fields['art_pic']."',".$isShow.",".$list_order.",".$pid.")");
		$res = db_factory::execute(" insert into ".TABLEPRE."forum_type value('','".$type_name."','".$fields['art_pic']."',".$isShow.",".$list_order.",".$pid.")");
	}
	
}

$url = 'index.php?do=forum&view=cat_list';
if($res)	kekezu::admin_show_msg($_lang['operate_success'],$url,3,'','success');

require  $template_obj->template('control/admin/tpl/admin_'. $do .'_'. $view);