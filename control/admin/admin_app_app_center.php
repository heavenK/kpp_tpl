<?php	defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 159 );
$plug_close = db_factory::query(sprintf("select * from %switkey_plug where status=0",TABLEPRE));
$plug_open = kekezu::get_table_data('*','witkey_plug','status=1','','','','submenu_id');
$path = S_ROOT.'/keke_client/keke/config.php';
include $path;
$data = file_get_contents($path);
$resource_arr = db_factory::query(sprintf("select * from %switkey_resource where 1=1 ",TABLEPRE));
$plug_obj = new Keke_witkey_plug_class();
$resource_new_arr = array();
if(is_array($plug_open)){
	foreach($resource_arr as $k=>$v){
			$resource_new_arr[$v[submenu_id]][] = $v;
	}
}
if(isset($ac)&&$plug_id){
	$plug_info = db_factory::get_one(sprintf("select * from %switkey_plug where plug_id=%d",TABLEPRE,$plug_id));
	switch($ac){
		case "open":
			$plug_id = intval($plug_id);
			$plug_obj->setWhere("plug_id=".$plug_id);
			$plug_obj->setStatus(1);
			$plug_obj->setOn_time(time());
			$res = $plug_obj->edit_keke_witkey_plug();
			if($res){
				db_factory::execute(sprintf("update %switkey_resource_submenu set status=1 where submenu_id=%d",TABLEPRE,$plug_info['submenu_id']));
				if($plug_info['plug_code']=='square'||$plug_info['plug_code']=='prom'){
					db_factory::execute(sprintf("update %switkey_nav set ishide=0 where nav_style='%s'",TABLEPRE,$plug_info['plug_code']));
				}
				if($plug_info['plug_code']=='prom'){
					db_factory::execute(sprintf("update %switkey_basic_config set v=1 where k='%s'",TABLEPRE,'prom_open'));
				}
				if($plug_info['plug_code']=='keke'){
					$res = preg_replace(array(
							"/application\'] = (\d)*(\s)*/",
					), array(
							"application'] = 1",
					), $data);
					file_put_contents($path, $res);
				}
				//$kekezu->_cache_obj->del ( 'menu_resource_cache' );
				kekezu::admin_show_msg ( '启用成功', "index.php?do=$do&view=$view", 3, '', 'success' );
			}else{
			kekezu::admin_show_msg ( '启用失败', "index.php?do=$do&view=$view", 3, '', 'warning' );
			}
			break;
		case "close":
			$plug_id = intval($plug_id);
			$plug_obj->setWhere("plug_id=".$plug_id);
			$plug_obj->setStatus(0);
			$plug_obj->setOn_time(time());
			$res = $plug_obj->edit_keke_witkey_plug();
			if($res){
				//更新submenu表
				db_factory::execute(sprintf("update %switkey_resource_submenu set status=0 where submenu_id=%d",TABLEPRE,$plug_info['submenu_id']));
				if($plug_info['plug_code']=='square'||$plug_info['plug_code']=='prom'){
					db_factory::execute(sprintf("update %switkey_nav set ishide=1 where nav_style='%s'",TABLEPRE,$plug_info['plug_code']));
				}
				if($plug_info['plug_code']=='prom'){
					db_factory::execute(sprintf("update %switkey_basic_config set v=0 where k='%s'",TABLEPRE,'prom_open'));
				}
				if($plug_info['plug_code']=='keke'){
					$res = preg_replace(array(
							"/application\'] = (\d)*(\s)*/",
					), array(
							"application'] = 0",
					), $data);
					file_put_contents($path, $res);
				}
				kekezu::admin_show_msg ( '关闭成功', "index.php?do=$do&view=$view", 3, '', 'success' );
			}else{
				kekezu::admin_show_msg ( '关闭失败', "index.php?do=$do&view=$view", 3, '', 'warning' );
			}
			break;
	}
}
require $template_obj->template ( 'control/admin/tpl/admin_app_app_center' );
