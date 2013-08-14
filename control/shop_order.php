<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
kekezu::check_login ();
$nav_active_index = 'shop';
keke_lang_class::package_init ( "shop" );
keke_lang_class::loadlang ( "info");
$sid and $sid = intval($_GET['sid']);
$item_config = keke_payitem_class::get_payitem_config ( null, null, null, 'item_id' );
$time_obj =new  service_time_class();
$time_obj->validtaskstatus();
if ($sid) {
	$service_info  = keke_shop_class::get_service_info($sid);
	$seller_info = kekezu::get_user_info($service_info['uid']);
	$model_info  = $model_list [$service_info['model_id']];
    $seller_goods_num = db_factory::get_count(sprintf("select count(service_id) from %switkey_service where model_id=6 and uid=%d and service_status=2",TABLEPRE,$service_info['uid']));
	$service_info or kekezu::show_msg ( $_lang['operate_notice'], "index.php?do=shop", '1', $_lang['goods_not_exist'], 'error' );
	$model_id = $service_info['model_id'];
    $shop_aid = keke_user_mark_class::get_user_aid ( $service_info['uid'], 2, null, 1 );
    if($order_id){
    	keke_shop_class::access_check($sid,$service_info['uid'],$service_info['model_id']);
    	$order_info = db_factory::get_one(sprintf("select * from %switkey_order where order_id=%d",TABLEPRE,$order_id));
    	if(!$order_info){
    		kekezu::show_msg ( $_lang['operate_notice'], "index.php?do=shop", '1', '订单不存在！', 'error' );
    	}
    	$buyer_info = kekezu::get_user_info($order_info['order_uid']);
    	$seller_real_path = keke_auth_fac_class::auth_check ( "realname", $seller_info['uid'] );
    	$buyer_real_path = keke_auth_fac_class::auth_check('realname',$buyer_info['uid']);
    	if ($mark) {
    		$title = $_lang ['both_mark'];
    		$obj_id = $obj_id;
    		$order_id = $order_id;
    		$model_code = $kekezu->_model_list [$service_info[model_id]] ['model_code'];
    		require S_ROOT . 'control/mark.php';
    		die ();
    	}
     if($show_kf){
     	$title = '客服信息';
        $kf_info = kekezu::get_rand_kf();
     	require keke_tpl_class::template ( 'shop/kf_info' );
     	die ();
     }
    }
    if($service_info['model_id']==6){
    	$file_info = db_factory::get_one(sprintf("select * from %switkey_file where save_name = '%s'",TABLEPRE,$service_info['file_path']));
    	$steps = goods_shop_class::get_order_step($order_info);
    }else{
    	$steps = service_shop_class::get_order_step($order_info);
    }
	switch ($steps) {
		case 'step1':
				switch ($op) {
					case "buy" :
						$owner_info = kekezu::get_user_info ( $service_info ['uid'] );
						$user_level = unserialize ( $owner_info ['seller_level'] );
						$auth_info = keke_auth_fac_class::get_submit_auth_record ( $owner_info ['uid'], 1 );
						break;
					case "confirm" :
						$order_info = keke_shop_class::check_has_buy($sid, $uid);
						if($order_info&&$order_info['order_status']=='wait'){
							kekezu::keke_show_msg ( "index.php?do=shop_order&sid=".$sid."&steps=step2&order_id=".$order_info['order_id'], '您已经购买了该作品', 'error' );
						}
						$message and $service_info['leave_message'] = kekezu::escape($message) or $service_info['leave_message'] = '0';
						keke_shop_class::create_service_order($service_info);
						break;
					case "seller_confirm":
						$obj = new service_shop_class();
						$res = $obj->dispose_order ( $order_id, 'wait' );
						break;
					case "seller_close":
						$obj = new service_shop_class();
						$res = $obj->dispose_order ( $order_id, 'close' );
						break;
				}
				require keke_tpl_class::template ( "shop/order_sub" );
			break;
		case 'step2':
			$buyer_info = kekezu::get_user_info($order_info['order_uid']);
			if(isset($op)&&$op=='confirm_pay'){
				if($model_id==6){
					$obj = new goods_shop_class();
				}else{
					$obj = new service_shop_class();
				}
				$res = $obj->dispose_order ( $order_id, 'ok' );
			}elseif(isset($op)&&$op=='close_order'){
				$obj = new service_shop_class();
				$res = $obj->dispose_order ( $order_id, 'close' );
			}
		  require keke_tpl_class::template ( "shop/order_sub2" );
		  break;
		case 'step3':
			switch ($op) {
				case "confirm_comp" :
					$obj = new service_shop_class();
					$res = $obj->dispose_order ( $order_id, 'confirm_complete' );
					break;
				case "confirm_ys" :
					$obj = new service_shop_class();
		            $res = $obj->dispose_order ( $order_id, 'complete' );
					break;
				case "confirm":
					$obj = new goods_shop_class();
		            $res = $obj->dispose_order ( $order_id, 'confirm' );
					break;
			}
		  require keke_tpl_class::template ( "shop/order_sub3" );
		  break;
		case 'step4':
			$mark_info = get_mark_info($order_id,$sid,$order_info['order_uid'],$order_info['seller_uid']);
		    require keke_tpl_class::template ( "shop/order_sub4" );
		  break;
		default:
			require keke_tpl_class::template ( "shop/order_sub" );
		 break;
	}
} else {
	kekezu::show_msg ( $_lang['operate_notice'], "index.php?do=shop_list", '1', $_lang['param_error'], 'error' );
}
function get_mark_info($order_id, $obj_id, $order_uid, $seller_uid) {
	global $uid, $role;
	if ($uid == $seller_uid) { 
		$mark_type = 1;
		$auid = $order_uid;
	} else { 
		$mark_type = 2;
		$auid = $seller_uid;
	}
	$mark_info = db_factory::get_one ( sprintf ( "select * from %switkey_mark where obj_id=%d and origin_id=%d and mark_type=%d and uid=$auid and by_uid=$uid", TABLEPRE, $order_id, $obj_id, $mark_type ) );
	return $mark_info;
}