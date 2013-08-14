<?php
keke_lang_class::load_lang_class('keke_auth_mobile_class');
class keke_auth_mobile_class extends keke_auth_base_class{
	public static function get_instance($auth_code='mobile') {
		static $obj = null;
		if ($obj == null) {
			$obj = new keke_auth_mobile_class($auth_code);
		}
		return $obj;
	}
	public function __construct($auth_code='mobile') {
		parent::__construct($auth_code);
		$this->_primary_key     ='mobile_a_id';
		$this->_tab_obj         =keke_table_class::get_instance($this->_auth_table_name);
	}
	public static function get_auth_step($auth_step=null,$auth_info=array()){
		$step='step1';
		if($auth_step){ 
			$step=$auth_step;
		}elseif($auth_info){
			$auth_info['auth_status'] and $step="step3" or $step="step2";
		}
		return $step;
	}
	public function add_auth($data,$file_name = '') {
		global $kekezu,$user_info,$_lang;
		$moblie_obj = new Keke_witkey_auth_mobile_class();
		$fdata=$this->format_auth_apply($data);
		if (!$fdata ['mobile']) {
			kekezu::show_msg ( $this->auth_lang().$_lang['apply_submit_fail'],$_SERVER['HTTP_REFERER'], 3, $this->auth_lang().$_lang['apply_submit_fail_for_info_little'], 'warning' );
		} 
		$valid_code = kekezu::randomkeys(4);
		$fdata['valid_code'] = $valid_code;
		$fdata[auth_time]=time();
		$msg_obj = new keke_msg_class(); 
		$content = $_lang['dear'].$user_info['username'].$_lang['hello'].$_lang['mobile_auth_code']." {$fdata['valid_code']}   ".$_lang['from']."[{$kekezu->_sys_config[website_url]}]";
		$msg_res = $msg_obj->send_phone_sms($fdata['mobile'],$content); 
		if($msg_res=="操作成功"){
			$fdata['cash'] > 0 and keke_finance_class::cash_out ($fdata['uid'],$fdata ['cash'],$this->_auth_name, $fdata ['cash'], $this->_auth_name, null );
			$auth_info = db_factory::get_one(sprintf("select * from %switkey_auth_mobile where uid='%d'",TABLEPRE,$user_info['uid']));
			if($auth_info){
				$moblie_obj->setWhere('uid='.$fdata['uid']);
				$moblie_obj->setValid_code($fdata['valid_code']);
				$moblie_obj->setAuth_status(0);
				$moblie_obj->edit_keke_witkey_auth_mobile();
			}else{
				$moblie_obj->setUid($fdata[uid]);
				$moblie_obj->setMobile($fdata[mobile]);
				$moblie_obj->setValid_code($fdata['valid_code']);
				$moblie_obj->setUsername($fdata[username]);
				$moblie_obj->setCash($fdata[cash]);
				$moblie_obj->setAuth_time($fdata[auth_time]);
				$moblie_obj->setAuth_status(0);
				$moblie_obj->create_keke_witkey_auth_mobile();
			}
			db_factory::execute(" update ".TABLEPRE."witkey_space set mobile='$fdata[mobile]' where uid = '$fdata[uid]' ");
			$fdata['start_time']==$fdata['end_time'] and $end_time=$fdata['end_time'] or $end_time=0;
			$res = $this->add_auth_record($fdata['uid'], $fdata['username'], $this->_auth_code,$end_time,0);
			parse_str($_SERVER['QUERY_STRING'],$arr);
			$arr[auth_step]="step2";
			unset($arr['inajax']);
			$url = 'index.php?'.http_build_query($arr);
			kekezu::show_msg($this->auth_lang().$_lang['msg'],$url,3,'手机验证申请提交成功，请注意查收来自手机的验证码','alert_right');
		}else{ 
			$url = 'index.php?do=user&view=payitem&op=auth&auth_code=mobile&ver=1&auth_step=step1';
			kekezu::show_msg($this->auth_lang().$_lang['msg'],$url,3,$this->auth_lang().$_lang['web_not_function']);
		}
	}
	public function valid_auth($data){
		global $kekezu,$user_info,$_lang;
		$uid = $user_info['uid'];
		$mobile_obj = new Keke_witkey_auth_mobile_class();
		$mobile_obj->setWhere('uid='.$user_info[uid]);
		$mobile_info = $mobile_obj->query_keke_witkey_auth_mobile();
		$mobile_info = $mobile_info[0];
		$valid_code = $mobile_info[valid_code];
		if($data['valid_code'] == $valid_code){
			$end_time = time();
			$this->set_auth_record_status($mobile_info['uid'], 1);
			$mobile_obj->setWhere('uid='.$user_info[uid]);
			$mobile_obj->setEnd_time($end_time);
			$mobile_obj->setAuth_status(1);
			$res2 = $mobile_obj->edit_keke_witkey_auth_mobile();
			parse_str($_SERVER['HTTP_REFERER'],$arr);
			$url = str_ireplace('step2', 'step3', $_SERVER['HTTP_REFERER']);
			if($res2){
				$kekezu->init_prom();
				$kekezu->_prom_obj->dispose_prom_event($this->_auth_name,$uid,$uid);
				kekezu::show_msg ( $this->auth_lang().$_lang['success'],$url, 1, $this->auth_lang().$_lang['auth_audit_success'],'alert_right' );
			}
		}else{
			kekezu::show_msg ( $this->auth_lang(),$_SERVER['HTTP_REFERER'], 1, $this->auth_lang().$_lang['code_error'],'alert_error' );
		}
	}
}