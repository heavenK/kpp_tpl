<?php	defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 11 );
if($check_uid){
	CHARSET=='gbk' and $check_uid = kekezu::utftogbk($check_uid);
	$info = get_info($check_uid);
	if($info){
		$info['balance'] = floatval($info['balance']);
		$info['credit'] = floatval($info['credit']);
	}
	$info and kekezu::echojson('',1,$info) or kekezu::echojson($_lang['none_exists_uid_or_username'],0);
	die();
}
$config = $kekezu->_sys_config;
if($is_submit){
	$url = "index.php?do=$do&view=$view";
	$user or kekezu::admin_show_msg($_lang['username_uid_can_not_null'],$url,3,'','warning');
	$info = get_info($user);
	$cash = floatval($cash);$credit = floatval($credit);
	if($cash<-$info['balance']){
		kekezu::admin_show_msg($_lang['user_deduct_limit'].$info['balance'].$_lang['yuan'],$url,3,'','warning');
	}elseif($credit<-$info['credit']){
		kekezu::admin_show_msg($_lang['user_deduct_limit'].$info['balance'].CREDIT_NAME,$url,3,'','warning');
	}
	($cash==0&&$credit==0) and kekezu::admin_show_msg($_lang['cash_can_not_null'],$url,3,'','warning');
	$cash_type or $cash = -$cash;
	$credit_type or $credit=-$credit;
	$res = keke_finance_class::cash_in($info['uid'], floatval($cash),floatval($credit),'admin_charge','','admin_charge');
	$charge_reason = kekezu::filter_input($charge_reason);
	$sql2 = "update " . TABLEPRE . "witkey_finance set  fina_mem='{$charge_reason}' where fina_id = last_insert_id()";
	db_factory::execute ( $sql2 );
	if($res){
		if($cash_type == 1){
			if($credit_type == 1){
				$v_arr = array ('用户名'=>$info[username],'金额动作'=>'充值','代金券动作'=>'充值','金额'=>$cash ,'代金券'=>$credit);
				keke_shop_class::notify_user ( $info[uid], $info[username], "admin_charge", '后台手动充值通知', $v_arr );
				kekezu::admin_show_msg('手动充值现金和代金券成功',$url,3,'','success');
			}elseif ($credit_type == 0){
				$v_arr = array ('用户名'=>$info[username],'金额动作'=>'充值','代金券动作'=>'扣除','金额'=>$cash ,'代金券'=>$credit);
				keke_shop_class::notify_user ( $info[uid], $info[username], "admin_charge", '后台手动充值通知', $v_arr );
				kekezu::admin_show_msg('手动充值现金和代金券扣除成功',$url,3,'','success');
			}
		}else{
			$action = '扣除';
			if($credit_type == 1){
				$v_arr = array ('用户名'=>$info[username],'金额动作'=>'扣除','代金券动作'=>'充值','金额'=>$cash ,'代金券'=>$credit);
				keke_shop_class::notify_user ( $info[uid], $info[username], "admin_charge", '后台手动充值通知', $v_arr );
				kekezu::admin_show_msg('手动扣除现金和充值代金券成功',$url,3,'','success');
			}elseif ($credit_type == 0){
				$v_arr = array ('用户名'=>$info[username],'金额动作'=>'扣除','代金券动作'=>'扣除','金额'=>$cash ,'代金券'=>$credit);
				keke_shop_class::notify_user ( $info[uid], $info[username], "admin_charge", '后台手动充值通知', $v_arr );
				kekezu::admin_show_msg('手动扣除现金和代金券成功',$url,3,'','success');
			}
		}
	}else{
		kekezu::admin_show_msg('手动充值或扣除失败',"index.php?do=$do&view=$view",3,'','warning');
	}
}
function get_info($uid){
	$sql = " select balance,credit,uid,username from %switkey_space where ";
	is_numeric($uid) and $sql=sprintf($sql." uid=%d or username=%d",TABLEPRE,$uid,$uid) or $sql=sprintf($sql." username='%s'",TABLEPRE,$uid);
	$info = db_factory::get_one($sql);
	return $info; 
}
require keke_tpl_class::template ( 'control/admin/tpl/admin_user_charge' );