<?php	defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
$table_obj = new Keke_witkey_free_service_class();
$url = "index.php?do=app&view=service_list";
if(isset($formhash)&&kekezu::submitcheck($formhash)){
$table_obj->setWhere('service_id ='.$service_id );
$table_obj->setUsername(kekezu::escape($txt_name));
$table_obj->setService_title($txt_title);
$table_obj->setService_desc($txt_desc);
$table_obj->setOn_time(time());
$table_obj->setService_file($f_path);
if(!$pic){
	$table_obj->setService_pic(keke_file_class::upload_file('id_pic'));
		}
		$res = $table_obj->edit_keke_witkey_free_service();
	 	$res and kekezu::admin_show_msg('操作提示',$url,3,'提交成功！','success') or kekezu::admin_show_msg('操作提示',$url,3,'提交失败！','warning');
}
if($op == 'edit'){
	$service_edit = db_factory::get_one(sprintf("select * from %switkey_free_service where service_id = '%d'",TABLEPRE,intval($pro_id)));
	if($service_edit[service_file]){
		$service_file_arr = db_factory::query(sprintf(" select * from %switkey_file where file_id in ('%s') ",TABLEPRE,$service_edit[service_file]));
	}
	if($service_edit[service_pic]){
		$service_pic_arr = db_factory::query(sprintf(" select * from %switkey_file where file_id in ('%s') ",TABLEPRE,$service_edit[service_pic]));
	}
}
require $template_obj->template('control/admin/tpl/admin_'.$do."_".$view);