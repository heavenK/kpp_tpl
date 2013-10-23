<?php
keke_lang_class::load_lang_class('keke_auth_email_class');
class keke_auth_email_class extends keke_auth_base_class{
public static function get_instance($auth_code='email') {
		static $obj = null;
		if ($obj == null) {
			$obj = new keke_auth_email_class($auth_code);
		}
		return $obj;
	}
	public function __construct($auth_code='email') {
		parent::__construct($auth_code);
		$this->_primary_key     ='email_a_id';
		$this->_tab_obj         =keke_table_class::get_instance($this->_auth_table_name);
	}
	public static function get_auth_step($auth_step=null,$auth_info=array()){
		$step='step1';
		if($auth_step){
			$step=$auth_step;
		}elseif($auth_info){
			if($auth_info['auth_status']==1 || $auth_info['auth_status']==2){
				$step="step3";
			}else{
				$step="step2";
			}
		}
		return $step;
	}
	public function add_auth($email,$file_name = ''){
		global $_K,$user_info,$_lang;
		$data['email']=$email;
		$data=$this->format_auth_apply($data);
		$data ['email'] or kekezu::show_msg ( $this->auth_lang().$_lang['apply_submit_fail'],$_SERVER['HTTP_REFERER'], 3, $this->auth_lang().$_lang['apply_fail_and_info_fail'], 'warning' );
		$data['auth_time'] = time();
		$auth_info=$this->get_user_auth_info($user_info[uid]);
		if($auth_info){
			$success=$this->_tab_obj->save($data,array($this->_primary_key=>$auth_info[$this->_primary_key]));
			$success and $success=$auth_info[$this->_primary_key];
				$this->set_auth_record_status($user_info['uid'], '0');
		}else{
			$success=$this->_tab_obj->save($data);
		}
		if ($success) {
			if($this->send_mail($success,$data)){
				$data['start_time']==$data['end_time'] and $end_time=$data['end_time'] or $end_time=0;
				db_factory::execute(" update ".TABLEPRE."witkey_space set email = '$data[email]' where uid = '$data[uid]'");
				db_factory::execute(" update ".TABLEPRE."witkey_member set email = '$data[email]' where uid = '$data[uid]'");
				return $this->add_auth_record($data['uid'], $data['username'], $this->_auth_code,$end_time);
			}
		}
	}
	public function audit_auth($active_code,$email_a_id){
		global $_K, $kekezu,$_lang;
		$user_info=$kekezu->_userinfo;
		if(md5($user_info['uid'].$user_info['username'].$user_info['email'])==$active_code){
			$auth_info=$this->get_auth_info($email_a_id);
			$auth_info or kekezu::show_msg($_lang['operate_notice'],'index.php?do=user&view=payitem&op=auth&auth_code=email&ver=1','3',$this->auth_lang().$_lang['apply_not_exist_nopass'],'warning');
			$this->set_auth_status($auth_info[0][$this->_primary_key],'1');
			$this->set_auth_record_status($auth_info[0]['uid'], '1');
			$kekezu->init_prom();
			$kekezu->_prom_obj->dispose_prom_event($this->_auth_name,$user_info['uid'],$user_info['uid']);
			$feed_arr = array(	
			 		"feed_username"=>array("content"=>$user_info[username],"url"=>"index.php?do=space&member_id=$user_info[uid]"),
					"action"=>array("content"=>$_lang['have_passed'],"url"=>""),
					"event"=>array("content"=>$this->auth_lang(),"url"=>"")
			 	);
			kekezu::save_feed($feed_arr, $user_info['uid'],$user_info['username'],'email_auth' ); 
			$v_arr = array($_lang['auth_code']=>$this->auth_lang(),$_lang['auth_url']=>$_K ['siteurl'] . '/index.php?do=user&view=payitem&op=auth&auth_code=email&ver=1#userCenter');
			keke_msg_class::notify_user($user_info['uid'], $user_info['username'], 'auth_success',$this->auth_lang().$_lang['success'],$v_arr );
			kekezu::show_msg($_lang['news_notice'],'index.php?do=user&view=payitem&op=auth&auth_code=email&ver=1#userCenter',3,$this->auth_lang().$_lang['success'],'success');
			}
	}
	public function send_mail($email_a_id,$data){
		global $_K,$_lang;
		$md5_code = md5($data['uid'].$data['username'].$data['email']);
		$title = $_K['html_title'].'--'.$_lang['email_auth'];
		$body = '<font color="red">亲爱的 '.$data['uid'].'：</font><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;感谢你注册豆8网（www.dou8wang.com），成为豆8网的一员！<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;在这里，你可以轻松赚取赏金，还能在豆8网找到有共同兴趣的豆友！相聚“豆豆江湖”，一起分享你的经历和经验！当然啦！你也可以在“豆豆学堂”了解各种稀奇古怪的知识。在“师徒关系”中向豆豆红人取经，提高自己任务的成功率！<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;豆8网让你随时随地“赚”起来……千千万万大威客、小威客集结这里！ 打造这个属于我们的威客时代！<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;请点击下面的链接完成注册：<a href="'
			.$_K[siteurl].'/index.php?do=user&view=payitem&op=auth&auth_code=email&email_a_id='.$email_a_id.'&ac=check_email&auth_step=step3&ver=1&active_code='.$md5_code.'">'
			.$_K[siteurl].'/index.php?do=user&view=payitem&op=auth&auth_code=email&email_a_id='
			.$email_a_id.'&ac=check_email&auth_step=step3&ver=1&active_code='.$md5_code.'</a>,如果你的邮箱不支持链接点击，请将链接地址拷贝到你的浏览器地址栏中。'.$_K[siteurl].'/index.php?do=user&view=payitem&op=auth&auth_code=email&email_a_id='.$email_a_id.'&ac=check_email&auth_step=step3&ver=1&active_code='.$md5_code.'<br><br>&nbsp;&nbsp;&nbsp;为确保我们的信息不被当做垃圾邮件处理，请把豆8网 dou88@dou88.com.cn 添加为您的联系人！(这是一封自动产生的email，请勿回复。）<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;感谢对豆8网的支持，再次希望你在豆8网体验愉快；如有任何疑问请联系下方客服<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;豆8网';
		return kekezu::send_mail($data['email'],$title,$body);
	}
}
?>