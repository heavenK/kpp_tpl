<?php	defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
$table_obj = new Keke_witkey_free_task_class();
$url = "index.php?do=app&view=task_list";
;
if(isset($formhash)&&kekezu::submitcheck($formhash)){
$table_obj->setWhere('task_id ='.$task_id );
$table_obj->setUsername(kekezu::escape($txt_name));
$table_obj->setUsername($txt_name);
$table_obj->setTask_title($txt_title);
$table_obj->setTask_desc($txt_desc);
$table_obj->setOn_time(time());
$table_obj->setTask_file($f_path);
if(!$pic){
	$table_obj->setTask_pic(keke_file_class::upload_file('id_pic'));
		}
		$res = $table_obj->edit_keke_witkey_free_task();
	 	$res and kekezu::admin_show_msg('操作提示',$url,3,'提交成功！','success') or kekezu::admin_show_msg('操作提示',$url,3,'提交失败！','warning');
}
if($op == 'edit'){
	$aaa_arr = db_factory::get_one(sprintf("select * from %switkey_free_task where task_id = '%d'",TABLEPRE,intval($pro_id)));
	if($aaa_arr[task_file]){
		$task_file_arr = db_factory::query(sprintf(" select * from %switkey_file where file_id in ('%s') ",TABLEPRE,$aaa_arr[task_file]));
	}
	if($aaa_arr[task_pic]){
		$task_pic_arr = db_factory::query(sprintf(" select * from %switkey_file where file_id in ('%s') ",TABLEPRE,$aaa_arr[task_pic]));
	}
}
require $template_obj->template('control/admin/tpl/admin_'.$do."_".$view);