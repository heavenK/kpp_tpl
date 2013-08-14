<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
keke_lang_class::package_init("shop");
keke_lang_class::loadlang("info");
$nav_active_index = "shop"; 
$sid=intval($_GET['sid']); 
if ($sid) { 
	$service_info = keke_shop_class::get_service_info ( $sid );
	if($service_info['model_id']==6){
	   $model_code='goods';
	}elseif($service_info['model_id']==7){
	   $model_code='service';
	}
	if($service_info['seo_title']){
		$page_title = $service_info['seo_title'];
	}else{
		$page_title = $service_info['title'].','.$indus_arr[$service_info['indus_id']]['indus_name'].','.$indus_p_arr[$service_info['indus_pid']]['indus_name'].','.$_lang['task_hall'].'-'.$_K['html_title'];
	}
	if($service_info['seo_keyword']){
		$page_keyword = $service_info['seo_keyword'];
	}else{
		$page_keyword = $service_info['title'].','.$indus_arr[$service_info['indus_id']]['indus_name'].','.$indus_p_arr[$service_info['indus_pid']]['indus_name'];
	}
	if($service_info['seo_desc']){
		$page_description =  $service_info['seo_desc'];
	}else{
		$page_description =  kekezu::cutstr(strip_tags($service_info['content']),200);
	}
	$cover_list = keke_shop_class::output_pics($service_info['pic'],'');
	$num        = sizeof($cover_list);
	$mc = keke_shop_class::get_mark_count_ext($model_code,$sid);
	$mc['all'] = intval($mc[1]['c']+$mc[2]['c']);
	$mc['seller'] = intval($mc[1]['c']);
	$mc['buyer'] = intval($mc[2]['c']);
	if ($service_info ['point']) {
		$point = explode (',', $service_info['point'] );
		$px = $point ['0'];
		$py = $point ['1'];
	} 
	if ($uid != $service_info ['uid']&&$service_info ['service_status']!=2&&$service_info ['service_status']!=5) {
		$uid == ADMIN_UID or kekezu::show_msg ( $_lang['operate_notice'], "index.php?do=shop_list", '1', $_lang['goods_not_exist'], 'error' );
	}
   if($view=='service_op'&&$t&&$user_info['group_id']){
		switch ($t){			
			case 1:
				$res=goods_shop_class::set_service_status($sid, 4);
				break;
		}
	 	$res and kekezu::show_msg($_lang['operate_notice'],"index.php?do=service&sid=$sid",'1',$_lang['operate_success'],'alert_right') or kekezu::show_msg($_lang['operate_notice'],"index.php?do=service&sid=$sid",'1',$_lang['operate_fail'],'alert_error'); 
	}
if($view=='tools'){
	$payitem_arr = keke_payitem_class::get_payitem_info ( 'employer', $model_list [$service_info ['model_id']] ['model_code'] );
$exist_payitem_arr = keke_payitem_class::payitem_exists ( $uid, false, '', $payitem_arr ); 
$payitem_arr_desc = unserialize ( $service_info ['payitem_time'] );
$payitem_standard = keke_payitem_class::payitem_standard (); 
if($sid&&$point&&$city){
	$pay_item = $service_info ['pay_item'];
	if($pay_item){
		$pay_item = $pay_item.','.$item_id;
	}else{
		$pay_item = $item_id;
	}
	$res = db_factory::execute ( sprintf ( "update %switkey_service set point='%s',pay_item='%s',city='%s' where service_id=%d", TABLEPRE, $point,$pay_item,$city, $sid ) );
	$res.=$cost_res = keke_payitem_class::payitem_cost ( 'map', 1, 'service', 'spend', $sid, $sid );
	$res and kekezu::echojson('',1,'');
}
if ($formhash) {
	is_array($payitem_num) or $payitem_num=array();
	if (! array_filter ( $payitem_num )) {
		kekezu::show_msg ( $_lang['friendly_notice'], 'index.php?do=service&sid='.$sid.'&view=tools', 1, $_lang['no_choose_any_tools'] );
	}
	$keys_arr = array_keys ( $payitem_arr_desc );
	$pay_item = $service_info ['pay_item'];
	foreach ( array_filter ( $payitem_num ) as $k => $v ) {
		if (intval ( $v ) > 0 && ! stristr ( $pay_item, "$k" )) {
			$pay_item = $pay_item . ",$k";
		}
		if (in_array ( $payitem_arr [$k] ['item_code'], $keys_arr )) { 
			$payitem_arr_desc [$payitem_arr [$k] ['item_code']] > time () and $payitem_arr_desc [$payitem_arr [$k] ['item_code']] = 3600 * 24 * $v + $payitem_arr_desc [$payitem_arr [$k] ['item_code']] or $payitem_arr_desc [$payitem_arr [$k] ['item_code']] = time () + 3600 * 24 * $v;
		} else { 
			db_factory::execute ( sprintf ( "update %switkey_service set point='%s',city='%s' where service_id=%d", TABLEPRE, $_POST ['point'], $province, $sid ) ); 
		}
		$cost_res = keke_payitem_class::payitem_cost ( $payitem_arr [$k] ['item_code'], $v, 'service', 'spend', $sid, $sid ); 
	}
	$pay_item = ltrim ( $pay_item, "," );
	if (strlen ( $pay_item )) {
		db_factory::execute ( sprintf ( "update %switkey_service set pay_item='%s' where service_id=%d", TABLEPRE, $pay_item, $sid ) ); 
	}
	$res = keke_payitem_class::set_payitem_time ( $payitem_arr_desc, $sid, 'service' ); 
	$res || $cost_res and kekezu::show_msg ( $_lang['friendly_notice'], "index.php?do=service&sid=$sid&view=tools", '1', '操作成功', 'alert_right' );
}
if($opp == 'tips'){
	$p_in = db_factory::get_one(sprintf("select * from %switkey_payitem where item_code = '%s'",TABLEPRE,$obj));
	require keke_tpl_class::template ( "use_tool" );
}	
}
	$indus_p_arr = kekezu::get_table_data ( '*', "witkey_industry", "indus_type=1 and indus_pid = 0 ", "listorder asc ", '', '', 'indus_id', NULL );
	$indus_arr   = kekezu::get_table_data ( '*', 'witkey_industry', '', 'listorder', '', '', 'indus_id', NULL );
	$model_id    = $service_info ['model_id'];
	$model_info  = $model_list [$model_id];
	$model_code  = $model_info['model_code'];
	$owner_info  = kekezu::get_user_info($service_info['uid']);
	$user_level  = unserialize($owner_info['seller_level']);
	$auth_info  = keke_auth_fac_class::get_submit_auth_record($owner_info['uid'],1);
	$more_list = keke_shop_class::get_more_service($service_info['uid'], $sid);
	$related_list = keke_shop_class::get_related_service($model_id, $sid, $service_info['indus_id']);
	$hot_list = keke_shop_class::get_hot_service($model_id, $sid, $service_info['indus_pid']);
	$task_list = keke_shop_class::get_task_info($service_info['indus_id']);
	keke_lang_class::package_init("shop");
	keke_lang_class::loadlang($model_info ['model_dir']);
	keke_lang_class::loadlang("service_info");
} else {
	kekezu::show_msg ( $_lang['operate_notice'], "index.php?do=index", '1', $_lang['param_error'], 'error' );
}
$model_info and ( require S_ROOT . "/shop/" . $model_info ['model_dir'] . "/control/service_info.php") or kekezu::show_msg ( $_lang['error'], "index.php?do=index", 3, $_lang['goods_not_exist'], 'error' );
