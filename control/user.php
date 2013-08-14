<?php	defined ( 'IN_KEKE' ) or exit('Access Denied');
keke_lang_class::package_init("user");
$do and keke_lang_class::loadlang($do);
$views = array('index','setting','finance','employer','witkey','message','collect','payitem','store');
if($task_open==0&&$shop_open==0){
	unset($views['employer'],$views['witkey']);
}
$views = array_merge($views);
!in_array($view,$views) && $view =$views[0];
($view || $op=='basic' )and keke_lang_class::loadlang("{$do}_{$view}");
$view == 'setting' and  keke_lang_class::loadlang("{$do}_{$op}");
$op  and keke_lang_class::loadlang("{$do}_{$view}_{$op}");
$_K['is_rewrite'] = 0 ;
$uid or header ( "location:index.php?do=login" ); 
$user_info=$kekezu->_userinfo;
$origin_url="index.php?do=$do&view=$view";
$page_title=$_lang['user_center'];
$user_type = intval($user_info['user_type']);
if($user_type==2){
	$nav_setting=array(
			"index"=>array($_lang['manage_tpl'],"meter"),
			"setting"=>array('企业设置',"cog"));
}else{
	$nav_setting=array(
			"index"=>array($_lang['manage_tpl'],"meter"),
			"setting"=>array('个人设置',"cog"));
}
$nav=array(
"store"=>array('店铺管理',"browser"),
"finance"=>array($_lang['finance_manage'],"chart-line2"),
"employer"=>array($_lang['employer_buyer'],"buyer"),
"witkey"=>array($_lang['witkey_seller'],"seller"),
"message"=>array($_lang['info_center'],"sound-high"),
"collect"=>array($_lang['my_collect'],"star-fav"),
"payitem"=>array($_lang['add_service'],"bookmark-2"));
if($task_open==0||$shop_open==0){
	if($task_open==0){
		$nav['employer'][0]=$_lang['buyer'];
		$nav['witkey'][0]=$_lang['seller'];
	}
	if($shop_open==0){
		$nav['employer'][0]=$_lang['employer'];
		$nav['witkey'][0]=$_lang['witkey'];
	}
	if($task_open==0&&$shop_open==0){
		unset($nav['employer'],$nav['witkey']);
	}
}
$nav = array_merge($nav_setting,$nav);
$user_type==1 and $w=" auth_code!='enterprise' " or ($user_type==2 and $w=" auth_code!='realname' "  or $w='');
 isset($w) and $auth_item_list = keke_auth_base_class::get_auth_item ( null, null, 1 ,$w);
 $footer_load = 1;
 $identy_auth_info = kekezu::get_table_data('auth_code,auth_status','witkey_auth_record',"uid=".$uid,'','','','auth_code');
function item_show($item_type) {
	global $task_open, $shop_open;
	$show = true;
	if ($task_open||$shop_open) {
		switch ($item_type) {
			case 'task' :
				$task_open or $show=false;
			case 'work' :
				break;
			case 'task_service' :
				$task_open|$shop_open or $show=false;
				break;
		}
	}else{
		$show=false;
	}
	return $show;
}
require 'user/user_'.$view.'.php';
