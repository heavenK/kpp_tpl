<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
if (isset ($success)) {
	 require keke_tpl_class::template ( $do."_cg" );
	 die ();
}
if ($view == 'reg_vip') {
	
	if($sbt){
		$res = db_factory::get_one ( sprintf ( "select * from %switkey_space_ext where uid = %d and k='vip_uid'", TABLEPRE, $user_info ['uid'] ) );
		if($res)	db_factory::execute ( sprintf ( "update %switkey_space_ext set v=%d where uid = %d", TABLEPRE, $vip_uid, $user_info ['uid'] ) );
		else db_factory::execute ( sprintf ( "insert into %switkey_space_ext value(%d, 'vip_uid', %d, %d)", TABLEPRE, $user_info ['uid'], $vip_uid, time() ) );
		kekezu::show_msg ( "设置成功！", "index.php", '2', "设置成功", 'alert_right' ) ;
	}
	
	if($action == 'no_uid'){
		$res = db_factory::get_one ( sprintf ( "select * from %switkey_space_ext where uid = %d and k='vip_uid'", TABLEPRE, $user_info ['uid'] ) );
		if($res)	db_factory::execute ( sprintf ( "update %switkey_space_ext set v=%d where uid = %d", TABLEPRE, 0, $user_info ['uid'] ) );
		else db_factory::execute ( sprintf ( "insert into %switkey_space_ext value(%d, 'vip_uid', %d, %d)", TABLEPRE, $user_info ['uid'], -1, time() ) );
		kekezu::show_msg ( "设置成功！", "index.php", '2', "设置成功", 'alert_right' ) ;
	}
	 require keke_tpl_class::template ( $do."_vip" );
	 die ();
}
if (isset ( $check_uid ) && ! empty ( $check_uid )) {
	 $res =  keke_user_class::check_uid ( $check_uid );
	 echo  $res;
	 die ();
}
($uid && !isset($_SESSION['auid'])) and header ( "location:index.php" ); 
$page_title=$_lang['register'].'-'.$_K['html_title'];
$reg_obj = new keke_register_class();
$api_name = keke_glob_class::get_open_api();
$page_type = "网站快速注册";
if (isset ($success)) {
	 require keke_tpl_class::template ( $do."_cg" );
	 die ();
}
if (isset($formhash)&&kekezu::submitcheck($formhash)){ 
	$reg_uid = $reg_obj->user_register($txt_account, md5($pwd_password), $txt_email,$txt_code,1,$pwd_password,$int_uid);
	$user_info = keke_user_class::get_user_info($reg_uid); 
	
	$pic_id = rand(1,20);
	$id = keke_user_avatar_class::set_user_sys_pic($reg_uid, $pic_id);
	if($id){
    	$kekezu->_cache_obj->del ( "keke_witkey_member_ext" );
    }
	
	$reg_obj->register_login_jump($user_info);
}
if (isset ( $check_email ) && ! empty ( $check_email )) {
	$res = keke_user_class::check_email ( $check_email );  
	echo  $res;
	die ();
}
if (isset ( $check_username ) && ! empty ( $check_username )) {
	 $res =  keke_user_class::check_username ( $check_username );
	 echo  $res;
	 die ();
}
if ($ajax && isset ( $check_uid ) && ! empty ( $check_uid )) {
	 $res =  keke_user_class::check_uid ( $check_uid , $ajax);
	 echo  $res;
	 die ();
}

require keke_tpl_class::template ( $do );