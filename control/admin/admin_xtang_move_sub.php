<?php defined ( 'ADMIN_KEKE' )or exit ( 'Access Denied' );

$url = 'index.php?do=xtang&view=cat_list';

if(!$id)	kekezu::admin_show_msg("没有目标项！",$url,3,'','fail');	
if(!$sub_ids)	kekezu::admin_show_msg("没有被移动项！",$url,3,'','fail');

if($type == 'type'){
	$sql = " update ".TABLEPRE."xtang_type set pid=".$id." where id in (".$sub_ids.")";
}
elseif($type == 'zsd'){
	$sql = " update ".TABLEPRE."xtang_article set type_id=".$id." where sid in (".$sub_ids.")";
}else{
	kekezu::admin_show_msg("操作错误！",$url,3,'','fail');	
}



$res = db_factory::execute($sql);
	
	
	
if($res)	kekezu::admin_show_msg($_lang['operate_success'],$url,3,'','success');
else	kekezu::admin_show_msg("操作错误！",$url,3,'','fail');	