<?php
define ( "IN_KEKE", TRUE );
include 'app_comm.php';
$task_open  = $kekezu->_task_open;
$shop_open  = $kekezu->_shop_open;
$dos = $kekezu->_route;
(!empty($do)&& in_array($do, $dos)) and $do or (!$_GET&&!$_POST and $do=$kekezu->_sys_config['set_index'] or $do='index');
isset($m)&&$m=="user" and  $do ="avatar";
isset($_GET['apu'])&&intval($_GET['apu']) and keke_union_class::pub_redirect($apu);
keke_lang_class::package_init("index");
keke_lang_class::loadlang($do);
$kekezu->init_lang();
$page_keyword = $kekezu->_sys_config['seo_keyword'];
$page_description = $kekezu->_sys_config ['seo_desc'];
$kf_phone = $kekezu->_sys_config['kf_phone'];
$uid = $kekezu->_uid;
if(!$uid){
	if($_COOKIE['username'] && $_COOKIE['password']){
		
		$login_obj = new keke_user_login_class();
		$user_info = $login_obj->user_login_auto($_COOKIE['username'], $_COOKIE['password']); 
		
		$login_obj->save_user_info_auto($user_info, $ckb_cookie,10); 
		
	}
}
$username = $kekezu->_username;
$messagecount = $kekezu->_messagecount;
$mess_new = $kekezu->_message_new;
$user_info = $kekezu->_userinfo;
$indus_p_arr = $kekezu->_indus_p_arr;
$indus_c_arr = $kekezu->_indus_c_arr;
$indus_arr   = $kekezu->_indus_arr;
$model_list  = $kekezu->_model_list;
$nav_arr = kekezu::nav_list($kekezu->_nav_list);
$lang_list = $kekezu->_lang_list;
$language      = $kekezu->_lang;
$currency      = $kekezu->_currency;
$curr_list     = $kekezu->_curr_list;
$api_open   = $kekezu->_api_open;
$weibo_list = $kekezu->_weibo_list;
$attent_api_open = $kekezu->_attent_api_open;
$attent_list = $kekezu->_weibo_attent;
$style_path = $kekezu->_style_path;
$plug_arr = $kekezu->_plug_arr;
$style_path=SKIN_PATH;
$f_c_list = keke_curren_class::get_curr_list('code,title');
$_currencies = keke_curren_class::get_curr_list();
$flink = kekezu::get_table_data("link_id,link_name,link_url","witkey_link",""," link_id asc","","","",3600);
$log_account=null;
if($user_info['isvip']>0 && $user_info['vip_end_time'] < time()){
	db_factory::execute(" update ".TABLEPRE."witkey_space set isvip=0,vip_status=0,vip_start_time=0,vip_end_time=0 where uid=".$uid);
	db_factory::execute(" delete from ".TABLEPRE."witkey_space_ext where k='vip_uid' and uid=".$uid);
}
if(isset($_COOKIE['log_account'])){
	$log_account = $_COOKIE['log_account'];
}
$wb_type = $_SESSION['wb_type'];
if ($wb_type && $_SESSION ['auth_' . $wb_type] ['last_key']) {
	$oa = new keke_oauth_login_class ( $wb_type );
	$oauth_user_info = $oa->get_login_user_info ();
}
$square_open = $plug_arr['square']['status'];
kekezu::redirect_second_domain();
include S_ROOT . 'control/' . $do . '.php';
