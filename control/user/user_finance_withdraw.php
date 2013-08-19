<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$step_list = array ("step1" => array ($_lang['step_one'], $_lang['input_withdraw_money'] ), "step2" => array ($_lang['step_two'], $_lang['choose_withdraw_account'] ), "step3" => array ($_lang['step_three'], $_lang['confirm_account_info'] ), "step4" => array ($_lang['step_four'], $_lang['waiting_for_background_review'] ), "step5" => array ($_lang['step_five'], $_lang['complete_withdraw'] ) );
$step or $step = 'step1';
$verify = kekezu::reset_secode_session ( $ver ? 0 : 1 ); 
$ac_url = $origin_url . "&op=$op&ver=" . intval ( $ver );
$pay_config = kekezu::get_table_data ( "*", "witkey_pay_config", '', '', "", '', 'k' );
$third_nav=array(
		"account"=>array('账户信息',$_lang['finance_record_stats']),
		"basic"=>array($_lang['finance_detail'],$_lang['finance_record_stats']),
		"charge"=>array($_lang['recharge_record'],$_lang['recharge_record_stats']),
		"withdraw"=>array($_lang['withdraw_record'],$_lang['withdraw_record_stats']));
switch ($step) {
	case "step1" :
		if ($reset) {
			$_SESSION ['withdraw_cash'] = '';
		}elseif ($choose_cash) {
			if ($withdraw_cash) {
				$withdraw_cash = keke_curren_class::convert($withdraw_cash,'',true);
				$user_info['balance']<$withdraw_cash&&kekezu::show_msg ( $_lang['have_no_enough_money'], $ac_url . "#userCenter", "3", "", "warning" );
				$withdraw_cash < $pay_arr ['withdraw_min'] ['v'] || $withdraw_cash > $pay_arr ['withdraw_max'] ['v'] and kekezu::show_msg ( $_lang['day_withdraw_money_is'] . "{$pay_arr['withdraw_min']['v']}-{$pay_arr['withdraw_max']['v']}," . $_lang['you_withdraw_money_error'], $ac_url . "#userCenter", "3", "", "warning" );
				$_SESSION ['withdraw_cash'] = $withdraw_cash;
				header ( "Location:" . $ac_url . "&step=step2&withdraw_cash=$withdraw_cash#userCenter" );
			} else {
				kekezu::show_msg ( $_lang['not_choose_recharge_money'], $ac_url . "#userCenter", "3", "", "warning" );
			}
		}
		break;
	case "step2" :
		$withdraw_cash != $_SESSION ['withdraw_cash'] and kekezu::show_msg ( $_lang['alert_return_rewrite'], $ac_url . "&step=step1&reset=1#userCenter", "3", "", "warning" );
		$bank_auth = keke_auth_fac_class::auth_check ( "bank", $uid );
		$bind_list = kekezu::get_table_data("*","witkey_auth_bank","uid='$uid' and auth_status!=2","","","","bank_id",null); 
		$bank_arr=keke_glob_class::get_bank();
		$offline_list = kekezu::get_payment_config('','offline',1);
	case "step3" :
		switch ($paymode) { 
			case "online" : 
				$pay_info = $payment_list [$pay_type]; 
				break;
			case "offline" : 
				$bank_info = db_factory::get_one(" select * from ".TABLEPRE."witkey_auth_bank  where uid ='$uid' and auth_status=1 and bank_name='$pay_type'");
				$user_bank_info = kekezu::get_table_data ( "*", "witkey_member_bank", 'uid='.$uid, '', "", '', '' );
				break;
		}
		break;
	case "step4" :
		if ($sbt_withdraw) {
			$withdraw_obj = new Keke_witkey_withdraw_class ();
			if (isset($formhash)&&kekezu::submitcheck($formhash)) {
				floatval ( $withdraw_cash ) > $user_info ['balance'] and kekezu::show_msg ( $_lang['submit_error'], $ac_url . "&step=step1&reset=1#userCenter", 2, $_lang['withdraw_money_too_big'], 'warning' );
				$withdraw_obj->setWithdraw_cash ( floatval ( $withdraw_cash ) );
				$withdraw_obj->setUid ( $uid );
				$withdraw_obj->setUsername ( $username );
				$withdraw_obj->setPay_username($pay_username);
				$withdraw_obj->setWithdraw_status ( 1 );
				$withdraw_obj->setApplic_time ( time () );
				$withdraw_obj->setPay_type ( $pay_type );
				$withdraw_obj->setPay_account ( $pay_account );
				$withdraw_id = $withdraw_obj->create_keke_witkey_withdraw ();
				if ($withdraw_id) {
					unset ( $_SESSION ['withdraw_cash'] );
					$pay_way = array_merge(keke_glob_class::get_bank(),keke_glob_class::get_online_pay());
					$data = array(':pay_way'=>$pay_way[$pay_type],':pay_account'=>$pay_account,':pay_name'=>$pay_username);
					keke_finance_class::init_mem('withdraw', $data);
					keke_finance_class::cash_out ( $uid, abs ( floatval ( $withdraw_cash ) ), 'withdraw', 0, 'withdraw', $withdraw_id );
					kekezu::show_msg ( $_lang['submit_success'], $ac_url . "&step=step4&withdraw_cash=$withdraw_cash#userCenter", 2, $_lang['withdraw_apply_success_wait_audit'],'alert_right' );
				} else {
					kekezu::show_msg ( $_lang['submit_fail'], $ac_url . "&step=step3&withdraw_cash=$withdraw_cash#userCenter", 2, $_lang['withdraw_apply_subit_fail'], 'alert_error' );
				}
			}
		}
		break;
}
require keke_tpl_class::template ( "user/" . $do . "_" . $view . "_" . $op );
