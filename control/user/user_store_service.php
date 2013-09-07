<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
if ($model_id && !$opp) {
	
	$indus = db_factory::query( sprintf ( "select indus_id,indus_name from %switkey_industry ", TABLEPRE ) );
	
	$count_arr = kekezu::get_table_data ( "model_id,count(service_id) count", "witkey_service", " model_id IN(6,7) and uid='$uid'", "", "model_id", "", "model_id", 3600 );
	$third_nav = array ();
	foreach ( $model_list as $v ) {
		$third_nav [] = array ("1" => $v ['model_id'], "2" => $s . $v ['model_name'], "3" => $count_arr [$v ['model_id']] ['count'] );
	}
	intval ( $page_size ) or $page_size = '10';
	intval ( $page ) or $page = '1';
	$url = $origin_url . "&op=$op&model_id=$model_id&page_size=$page_size&status=$status&page=$page";
	$model_id = intval ( $model_id );
	$model_code = $model_list [$model_id] ['model_code'];
     $status_arr = array("1"=>"待审核","2"=>"出售中","3"=>"已下架");
	if (isset ( $ac )) {
		$ser_id = intval ( $ser_id );
		if ($ser_id) {
			switch ($ac) {
				case "del" :
					$res = db_factory::execute ( sprintf ( " delete from %switkey_service where service_id='%d'", TABLEPRE, $ser_id ) );
				    if($res){
				    	$res1 = db_factory::execute(sprintf("update %switkey_shop set on_sale = on_sale-1 where uid=$uid",TABLEPRE));
				        $res1 and kekezu::show_msg ( $_lang ['operate_notice'], $url, 1, $_lang ['g_delete_success'], 'alert_right' ) or kekezu::show_msg ( $_lang ['operate_notice'], $url, 1, $_lang ['g_delete_fail'], 'alert_error' );
				    }
					break;
				case "edit" :
					$url = "$origin_url&op=$op&model_id=$model_id&ac=edit&ser_id=$ser_id";
					require S_ROOT . '/shop/' . $model_code . '/control/' . $model_code . '_edit.php';
					break;
				case "check" :
					$res = db_factory::get_count ( sprintf ( " select a.order_id from %switkey_order a left join 
					%switkey_order_detail b on a.order_id=b.order_id where a.model_id='%d' and b.obj_id='%d'
					 and b.obj_type='service' and a.order_status not in ('close','arb_confirm')", TABLEPRE, TABLEPRE, $model_id, $ser_id ) );
					$res and kekezu::echojson ( '', 1 ) or kekezu::echojson ( '', 0 );
					die ();
					break;
				case "down_shelf" :
					$res = db_factory::execute ( sprintf ( " update %switkey_service set service_status=3 where service_id='%d'", TABLEPRE, $ser_id ) );
					if($res){
						$res1 = db_factory::execute(sprintf("update %switkey_shop set on_sale = on_sale-1 where uid=$uid",TABLEPRE));
						$res1 and kekezu::show_msg ( $_lang ['operate_notice'], $url, 1, '商品下架成功', 'alert_right' ) or kekezu::show_msg ( $_lang ['operate_notice'], $url, 1, '商品下架失败', 'alert_error' );
					}
					break;
			}
		} else {
			kekezu::show_msg ( $_lang ['operate_notice'], $url, 3, $_lang ['please_choose_delete_sid'], "alert_error" );
		}
	}
	$ord_arr = array (" service_id desc " => $_lang ['service_id_desc'], " service_id asc " => $_lang ['service_id_asc'], " on_time desc " => $_lang ['pub_time_desc'], " on_time asc " => $_lang ['pub_time_asc'] );
	$page_obj = $kekezu->_page_obj; 
	$s_obj = new Keke_witkey_service_class ();
	$where = " model_id = '$model_id' and uid= '$uid'";
	
	$indus_id and $where .= " and indus_id = '$indus_id'";
	if($start_time || $end_time){
		if($start_time) $where .= " and on_time > ".strtotime($start_time);
		if($end_time) $where .= " and on_time < ".strtotime($end_time);
	}
	
	($service_status === '0') and $where .= " and service_status='$service_status'" or ($service_status and $where .= " and service_status = '$service_status' ");
	$service_id && $service_id != $_lang ['please_input_service_id'] and $where .= " and service_id = '$service_id' ";
	$ord and $where .= " order by $ord " or $where .= " order by service_id desc ";
	$s_obj->setWhere ( $where );
	$count = intval ( $s_obj->count_keke_witkey_service () );
	$pages = $page_obj->getPages ( $count, $page_size, $page, $url, '#userCenter' );
	$s_obj->setWhere ( $where . $pages ['where'] );
	$s_info = $s_obj->query_keke_witkey_service ();
	
	require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );
}elseif($opp == 'add'){
	kekezu::check_login();
	$is_shop_open = db_factory::get_one(sprintf("select shop_id from %switkey_shop where shop_status=1 and uid=%d",TABLEPRE,$uid));
	$is_shop_open or kekezu::show_msg ( $_lang['operate_notice'], "index.php?do=user&view=setting&op=space", '2','您必须开通店铺后才能发布商品或服务', 'error' );
	keke_lang_class::package_init("shop");
	keke_lang_class::loadlang("release");
	$page_title= $_lang['pub_witkey_service'] . $_K ['html_title'];
	$model_list = kekezu::get_table_data ( '*', 'witkey_model', " model_type = 'shop' and model_status='1'", 'listorder', '', '', 'model_id', 3600 );
	$model_img_desc = array("6"=>$_lang['shang'],"7"=>$_lang['fu']);
	if(!$model_id){
		$model_ids = array_keys($model_list);
		$model_id = $model_ids['0'];
	}
	$model_id and $model_info = $model_list[$model_id] or kekezu::keke_show_msg("index.php",$_lang['now_no_goods_model'],"error");
	$stage_nav=array("1"=>array("step1",$_lang['choose_goods_type'],$_lang['choose_model_to_confirm_trans']),
					"2"=>array("step2",$_lang['input_goods_description'],$_lang['from_description_to_confirm_detail']),
					"3"=>array("step3",$_lang['pub_success'],$_lang['complete_pub']));
	$r_step or $r_step='step1';
	$basic_url = $_K['siteurl']."/index.php?do=user&view=store&op=service&opp=add&model_id=".$model_id."&r_step=".$r_step;
	
	$std_cache_name = 'service_cache_'.$model_id.'_' . substr ( md5 ( $uid ), 0, 6 );
	$release_obj = service_release_class::get_instance ( $model_id );
	$payitem_arr = keke_payitem_class::get_payitem_info('employer','service'); 
	$payitem_standard = keke_payitem_class::payitem_standard (); 
	$release_obj->get_service_obj ( $std_cache_name ); 
	$release_info = $release_obj->_std_obj->_release_info; 
	$service_config = $release_obj->_service_config; 
	$ext = '*.jpg;*.jpeg;*.gif;*.png;*.bmp';
	$min =time()+24*3600;
	$min = date("Y-m-d",$min);
	switch ($r_step) { 
		case "step1" :
			switch ($ajax) {
				case "show_indus" : 
					$release_obj->get_service_bind_indus ( $indus_pid,$ajax );
					break;
			}
			if (kekezu::submitcheck($formhash)) {
				$release_info and $_POST = array_merge ( $release_info, $_POST,$_FILES);
				$_POST['txt_title']  = kekezu::escape($txt_title);
				$_POST['tar_content'] =  $tar_content ;
				$_POST['txt_price'] = keke_curren_class::convert($_POST['txt_price'],0,true);
				$release_obj->save_service_obj ( $_POST, $std_cache_name ); 
			} else {
				$release_obj->check_access ( $r_step, $model_id, $release_info ); 
				$kf_info	 = $release_obj->_kf_info; 
				$indus_p_arr = $release_obj->get_bind_indus(); 
				$indus_arr   = $release_obj->get_service_indus($release_info ['indus_pid']); 
				$price_unit  = $release_obj->get_price_unit();
				$service_unit= $release_obj->get_service_unit();
			}
			if (kekezu::submitcheck($formhash)) {
				$release_info and $_POST = array_merge ( $release_info, $_POST );
				$release_obj->save_service_obj ( $_POST, $std_cache_name ); 
				$service_id = $release_obj->pub_service();
				$release_obj->update_service_info($service_id, $std_cache_name);
				header ( "location:index.php?do=user&view=store&op=service&model_id=7" );
				die ();
			} else {
				$release_obj->check_access ( $r_step, $model_id, $release_info ); 
				$item_list = keke_payitem_class::get_payitem_info ('employer',$model_info[model_code] );
				$standard = keke_payitem_class::payitem_standard ();
				$item_info = $release_obj->get_pay_item (); 
				$item_detail = $release_obj->get_pay_item_info();
				$total_cash = $release_obj->get_payitem_cash ( 0); 
			}
			break;
		case "step3" :
			$service_info = $release_obj->check_access ( $r_step, $model_id, $release_info,$service_id ); 
			break;
	}
	require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op ."_" . $opp);
}else{
	require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );	
}
