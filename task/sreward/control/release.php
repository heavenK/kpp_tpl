<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$std_cache_name = 'task_cache_'.$pub_mode.'_'.$model_id.'_'.$t_id.'_' . substr ( md5 ( $uid ), 0, 6 );
$release_obj = sreward_release_class::get_instance ($model_id,$pub_mode);
$task_config = $release_obj->_task_config; 
$payitem_arr = keke_payitem_class::get_payitem_info('employer','sreward'); 
$payitem_standard = keke_payitem_class::payitem_standard (); 
$release_obj->get_task_obj ( $std_cache_name ); 
$release_obj->pub_mode_init($std_cache_name,$init_info);
$release_info = $release_obj->_std_obj->_release_info; 
$min =time()+ 24*3600*$task_config['min_day'];
$min = date("Y-m-d",$min); 
$ajax =='check_priv' and $release_obj->check_pub_priv('','json');



$release_t_obj = tender_release_class::get_instance ( 4 ,$pub_mode);
$cove_arr = $release_t_obj->_cash_cove;


switch ($r_step) { 
	case "step1" :
		switch ($ajax) {
			case "getmaxday" : 
				$release_obj->get_max_day ( $task_cash );
				break;
			case "show_indus" : 
				$release_obj->get_task_indus ( $indus_pid,$ajax );
				break;
		}
		if (kekezu::submitcheck($formhash)) {
			$release_info and $_POST = array_merge ( $release_info, $_POST );
			$_POST['txt_task_cash'] = keke_curren_class::convert($_POST['txt_task_cash'],0,true);
			$release_obj->save_task_obj ( $_POST, $std_cache_name ); 
			header ( "location:index.php?do=release&pub_mode=$pub_mode&t_id=$t_id&model_id={$model_id}&r_step=step2" );
			die ();
		} else{
			$default_max_day = $release_obj->_default_max_day; 
		}
		break;
	case "step2" :	
		switch ($ajax) {
			case "show_indus" : 
				$release_obj->get_task_indus ( $indus_pid,$ajax );
				break;
		}	
		if (kekezu::submitcheck($formhash)) {
			$release_info and $_POST = array_merge ( $release_info, $_POST);
 			$_POST['txt_title'] = kekezu::escape($txt_title);
 			$_POST['tar_content'] = $tar_content;
			$release_obj->save_task_obj ($_POST, $std_cache_name ); 
			header ( "location:index.php?do=release&pub_mode=$pub_mode&t_id=$t_id&model_id={$model_id}&r_step=step3" );
			die ();
		} else {
			$release_obj->check_access ( $r_step, $model_id, $release_info ); 
			$kf_info	 = $release_obj->_kf_info; 
			$indus_p_arr = $release_obj->get_bind_indus(); 
			$indus_arr   = $release_obj->get_task_indus($release_info ['indus_pid']); 
			$ext_types   = kekezu::get_ext_type (); 
			$ext_show = kekezu::get_ext_type_show();
 		}
		break;
	case "step3" : 
		$limit_max =ceil(( strtotime($release_info['txt_task_day']) - time())/3600/24);  
		switch ($ajax) {
			case "save_payitem" : 
				$r = $release_obj->save_pay_item ( $item_id, $item_code, $item_name, $item_cash, $std_cache_name ,$item_num,$item_type);
				break;
			case "rm_payitem" :	
				$release_obj->remove_pay_item ( $item_id, $std_cache_name );
				break;
			case "show_indus" : 
				$release_obj->get_task_indus ( $indus_pid,$ajax );
				break;
		}
		if (kekezu::submitcheck($formhash)) {

			$last_time = $_SESSION[$uid.'_release'];
			if((time()- $last_time) < 2)	{
				kekezu::show_msg ( "您点的太快了，休息一下！", "index.php?do=user&view=employer&op=task", 3,"您点的太快了，休息一下！" , 'warning' );
			}
			$_SESSION[$uid.'_release'] = time();
			$release_info and $_POST = array_merge ( $release_info, $_POST );
			$release_obj->save_task_obj ( $_POST, $std_cache_name ); 
			$task_id = $release_obj->pub_task (); 
			$release_obj->update_task_info ( $task_id, $std_cache_name ); 
		} else {
			
			 $release_obj->check_access ( $r_step, $model_id, $release_info ); 
			 $item_list = $payitem_arr; 
			$standard = keke_payitem_class::payitem_standard ();
			$total_cash = $release_obj->get_total_cash ( $release_info ['txt_task_cash'] ); 
			$item_info = $release_obj->get_pay_item (); 
			$item_detail = $release_obj->get_pay_item_info();
			
		}
		break;
	case "step4" :
		$release_obj->check_access ( $r_step, $model_id, $release_info,$task_id ); 
		break;
}


if($to_uid){
	$member_id = intval ( $to_uid );
	$member_info = kekezu::get_user_info ( $member_id );
	$shop_info = db_factory::get_one(sprintf("select * from %switkey_shop where uid = '%d'",TABLEPRE,$member_id));
	$credit_level = unserialize($member_info['seller_level']);
	
	
	$auth_item = keke_auth_base_class::get_auth_item ();
	$auth_temp = array_keys ( $auth_item );
	$user_info ['user_type'] == 2 and $un_code = 'realname' or $un_code = "enterprise";
	$t = implode ( ",", $auth_temp );
	$auth_info = db_factory::query ( " select a.auth_code,a.auth_status,b.auth_title,b.auth_small_ico,b.auth_small_n_ico from " . TABLEPRE . "witkey_auth_record a left join " . TABLEPRE . "witkey_auth_item b on a.auth_code=b.auth_code where a.uid ='$member_id' and FIND_IN_SET(a.auth_code,'$t')", 1, - 1 );
	$auth_info = kekezu::get_arr_by_key ( $auth_info, "auth_code" );
	
	// 收支
	$sql = ' select a.fina_cash,a.fina_type from '.TABLEPRE.'witkey_finance a left join '
					.TABLEPRE.'witkey_task b on a.obj_id=b.task_id left join '.TABLEPRE
					.'witkey_service c on a.obj_id=c.service_id ';
	$where =" where a.uid=".intval($uid)." and a.fina_type='in' and a.fina_action not in ('withdraw','offline_recharge','offline_charge','online_charge','online_recharge','withdraw_fail')";
	$fina_arr = db_factory::query($sql.$where);
	$shouru = 0;
	$sum = 0;
	foreach($fina_arr as $val){
		$shouru += $val['fina_cash'];
		$sum ++;
	}
	//end 
}




if($to_uid)	require keke_tpl_class::template ( 'release_zj' );
else require keke_tpl_class::template ( 'release' );
		