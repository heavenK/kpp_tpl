<?php
class Keke_witkey_space_class{
	public $_db;
	public $_tablename;
	public $_dbop;
	public $_uid;
	public $_username;
	public $_password;
	public $_sec_code;
	public $_email;
	public $_user_pic;
	public $_group_id;
	public $_isvip;
	public $_status;
	public $_user_type;
	public $_sex;
	public $_marry;
	public $_hometown;
	public $_residency;
	public $_address;
	public $_birthday;
	public $_truename;
	public $_idcard;
	public $_idpic;
	public $_qq;
	public $_msn;
	public $_fax;
	public $_phone;
	public $_mobile;
	public $_indus_id;
	public $_indus_pid;
	public $_skill_ids;
	public $_summary;
	public $_experience;
	public $_reg_time;
	public $_reg_ip;
	public $_domain;
	public $_credit;
	public $_balance;
	public $_balance_status;
	public $_pub_num;
	public $_take_num;
	public $_nominate_num;
	public $_accepted_num;
	public $_vip_start_time;
	public $_vip_end_time;
	public $_pay_zfb;
	public $_pay_cft;
	public $_pay_bank;
	public $_score;
	public $_buyer_credit;
	public $_buyer_good_num;
	public $_buyer_level;
	public $_buyer_total_num;
	public $_seller_credit;
	public $_seller_good_num;
	public $_seller_level;
	public $_seller_total_num;
	public $_studio_id;
	public $_last_login_time;
	public $_client_status;
	public $_recommend;
	public $_union_user;
	public $_union_assoc;
	public $_union_rid;
	
	public $_xueli;
	public $_shengao;
	public $_tizhong;
	public $_xingzuo;
	public $_xuexing;
	public $_yuyan;
	public $_aihao;
	public $_zhiye;
	public $_gerencaiyi;
	public $_biyeyuanxiao;
	public $_xiuxianfangshi;
	public $_ziwojieshao;
	public $_pid;
	public $_look_credit;
	public $_zl_flag;

	
	
	
	
	public $_cache_config = array ('is_cache' => 0, 'time' => 0 );
	public $_replace=0;
	public $_where;
	function  keke_witkey_space_class(){
		$this->_db = new db_factory ( );
		$this->_dbop = $this->_db->create(DBTYPE);
		$this->_tablename = TABLEPRE."witkey_space";
	}
	 
	public function getUid(){
		return $this->_uid ;
	}
	public function getUsername(){
		return $this->_username ;
	}
	public function getPassword(){
		return $this->_password ;
	}
	public function getSec_code(){
		return $this->_sec_code ;
	}
	public function getEmail(){
		return $this->_email ;
	}
	public function getUser_pic(){
		return $this->_user_pic ;
	}
	public function getGroup_id(){
		return $this->_group_id ;
	}
	public function getIsvip(){
		return $this->_isvip ;
	}
	public function getStatus(){
		return $this->_status ;
	}
	public function getUser_type(){
		return $this->_user_type ;
	}
	public function getSex(){
		return $this->_sex ;
	}
	public function getMarry(){
		return $this->_marry ;
	}
	public function getHometown(){
		return $this->_hometown ;
	}
	public function getResidency(){
		return $this->_residency ;
	}
	public function getAddress(){
		return $this->_address ;
	}
	public function getBirthday(){
		return $this->_birthday ;
	}
	public function getTruename(){
		return $this->_truename ;
	}
	public function getIdcard(){
		return $this->_idcard ;
	}
	public function getIdpic(){
		return $this->_idpic ;
	}
	public function getQq(){
		return $this->_qq ;
	}
	public function getMsn(){
		return $this->_msn ;
	}
	public function getFax(){
		return $this->_fax ;
	}
	public function getPhone(){
		return $this->_phone ;
	}
	public function getMobile(){
		return $this->_mobile ;
	}
	public function getIndus_id(){
		return $this->_indus_id ;
	}
	public function getIndus_pid(){
		return $this->_indus_pid ;
	}
	public function getSkill_ids(){
		return $this->_skill_ids ;
	}
	public function getSummary(){
		return $this->_summary ;
	}
	public function getExperience(){
		return $this->_experience ;
	}
	public function getReg_time(){
		return $this->_reg_time ;
	}
	public function getReg_ip(){
		return $this->_reg_ip ;
	}
	public function getDomain(){
		return $this->_domain ;
	}
	public function getCredit(){
		return $this->_credit ;
	}
	public function getBalance(){
		return $this->_balance ;
	}
	public function getBalance_status(){
		return $this->_balance_status ;
	}
	public function getPub_num(){
		return $this->_pub_num ;
	}
	public function getTake_num(){
		return $this->_take_num ;
	}
	public function getNominate_num(){
		return $this->_nominate_num ;
	}
	public function getAccepted_num(){
		return $this->_accepted_num ;
	}
	public function getVip_start_time(){
		return $this->_vip_start_time ;
	}
	public function getVip_end_time(){
		return $this->_vip_end_time ;
	}
	public function getPay_zfb(){
		return $this->_pay_zfb ;
	}
	public function getPay_cft(){
		return $this->_pay_cft ;
	}
	public function getPay_bank(){
		return $this->_pay_bank ;
	}
	public function getScore(){
		return $this->_score ;
	}
	public function getBuyer_credit(){
		return $this->_buyer_credit ;
	}
	public function getBuyer_good_num(){
		return $this->_buyer_good_num ;
	}
	public function getBuyer_level(){
		return $this->_buyer_level ;
	}
	public function getBuyer_total_num(){
		return $this->_buyer_total_num ;
	}
	public function getSeller_credit(){
		return $this->_seller_credit ;
	}
	public function getSeller_good_num(){
		return $this->_seller_good_num ;
	}
	public function getSeller_level(){
		return $this->_seller_level ;
	}
	public function getSeller_total_num(){
		return $this->_seller_total_num ;
	}
	public function getStudio_id(){
		return $this->_studio_id ;
	}
	public function getLast_login_time(){
		return $this->_last_login_time ;
	}
	public function getClient_status(){
		return $this->_client_status ;
	}
	public function getRecommend(){
		return $this->_recommend ;
	}
	public function getUnion_user(){
		return $this->_union_user ;
	}
	public function getUnion_assoc(){
		return $this->_union_assoc ;
	}
	public function getUnion_rid(){
		return $this->_union_rid ;
	}
	public function getWhere(){
		return $this->_where ;
	}

	public function getXueli(){
		return $this->_xueli ;
	}
	public function getShengao(){
		return $this->_shengao ;
	}
	public function getTizhong(){
		return $this->_tizhong ;
	}
	public function getXingzuo(){
		return $this->_xingzuo ;
	}
	public function getXuexing(){
		return $this->_xuexing ;
	}
	public function getYuyan(){
		return $this->_yuyan ;
	}
	public function getAihao(){
		return $this->_aihao ;
	}
	public function getZhiye(){
		return $this->_zhiye ;
	}
	public function getGerencaiyi(){
		return $this->_gerencaiyi ;
	}
	public function getBiyeyuanxiao(){
		return $this->_biyeyuanxiao ;
	}
	public function getXiuxianfangshi(){
		return $this->_xiuxianfangshi ;
	}
	public function getZiwojieshao(){
		return $this->_ziwojieshao ;
	}
	public function getPid(){
		return $this->_pid ;
	}
	public function getLook_credit(){
		return $this->_look_credit ;
	}
	public function getZl_flag(){
		return $this->_zl_flag ;
	}


	public function getCache_config() {
		return $this->_cache_config;
	}

	public function setUid($value){
		$this->_uid = $value;
	}
	public function setUsername($value){
		$this->_username = $value;
	}
	public function setPassword($value){
		$this->_password = $value;
	}
	public function setSec_code($value){
		$this->_sec_code = $value;
	}
	public function setEmail($value){
		$this->_email = $value;
	}
	public function setUser_pic($value){
		$this->_user_pic = $value;
	}
	public function setGroup_id($value){
		$this->_group_id = $value;
	}
	public function setIsvip($value){
		$this->_isvip = $value;
	}
	public function setStatus($value){
		$this->_status = $value;
	}
	public function setUser_type($value){
		$this->_user_type = $value;
	}
	public function setSex($value){
		$this->_sex = $value;
	}
	public function setMarry($value){
		$this->_marry = $value;
	}
	public function setHometown($value){
		$this->_hometown = $value;
	}
	public function setResidency($value){
		$this->_residency = $value;
	}
	public function setAddress($value){
		$this->_address = $value;
	}
	public function setBirthday($value){
		$this->_birthday = $value;
	}
	public function setTruename($value){
		$this->_truename = $value;
	}
	public function setIdcard($value){
		$this->_idcard = $value;
	}
	public function setIdpic($value){
		$this->_idpic = $value;
	}
	public function setQq($value){
		$this->_qq = $value;
	}
	public function setMsn($value){
		$this->_msn = $value;
	}
	public function setFax($value){
		$this->_fax = $value;
	}
	public function setPhone($value){
		$this->_phone = $value;
	}
	public function setMobile($value){
		$this->_mobile = $value;
	}
	public function setIndus_id($value){
		$this->_indus_id = $value;
	}
	public function setIndus_pid($value){
		$this->_indus_pid = $value;
	}
	public function setSkill_ids($value){
		$this->_skill_ids = $value;
	}
	public function setSummary($value){
		$this->_summary = $value;
	}
	public function setExperience($value){
		$this->_experience = $value;
	}
	public function setReg_time($value){
		$this->_reg_time = $value;
	}
	public function setReg_ip($value){
		$this->_reg_ip = $value;
	}
	public function setDomain($value){
		$this->_domain = $value;
	}
	public function setCredit($value){
		$this->_credit = $value;
	}
	public function setBalance($value){
		$this->_balance = $value;
	}
	public function setBalance_status($value){
		$this->_balance_status = $value;
	}
	public function setPub_num($value){
		$this->_pub_num = $value;
	}
	public function setTake_num($value){
		$this->_take_num = $value;
	}
	public function setNominate_num($value){
		$this->_nominate_num = $value;
	}
	public function setAccepted_num($value){
		$this->_accepted_num = $value;
	}
	public function setVip_start_time($value){
		$this->_vip_start_time = $value;
	}
	public function setVip_end_time($value){
		$this->_vip_end_time = $value;
	}
	public function setPay_zfb($value){
		$this->_pay_zfb = $value;
	}
	public function setPay_cft($value){
		$this->_pay_cft = $value;
	}
	public function setPay_bank($value){
		$this->_pay_bank = $value;
	}
	public function setScore($value){
		$this->_score = $value;
	}
	public function setBuyer_credit($value){
		$this->_buyer_credit = $value;
	}
	public function setBuyer_good_num($value){
		$this->_buyer_good_num = $value;
	}
	public function setBuyer_level($value){
		$this->_buyer_level = $value;
	}
	public function setBuyer_total_num($value){
		$this->_buyer_total_num = $value;
	}
	public function setSeller_credit($value){
		$this->_seller_credit = $value;
	}
	public function setSeller_good_num($value){
		$this->_seller_good_num = $value;
	}
	public function setSeller_level($value){
		$this->_seller_level = $value;
	}
	public function setSeller_total_num($value){
		$this->_seller_total_num = $value;
	}
	public function setStudio_id($value){
		$this->_studio_id = $value;
	}
	public function setLast_login_time($value){
		$this->_last_login_time = $value;
	}
	public function setClient_status($value){
		$this->_client_status =$value;
	}
	public function setRecommend($value){
		$this->_recommend =$value;
	}
	public function setUnion_user($value){
		$this->_union_user=$value;
	}
	public function setUnion_assoc($value){
		$this->_union_assoc=$value;
	}
	public function setUnion_rid($value){
		$this->_union_rid=$value;
	}
	public function setWhere($value){
		$this->_where = $value;
	}
	
	
	
	public function setXueli($value){
		$this->_xueli = $value;
	}
	public function setShengao($value){
		$this->_shengao = $value;
	}
	public function setTizhong($value){
		$this->_tizhong = $value;
	}
	public function setXingzuo($value){
		$this->_xingzuo = $value;
	}
	public function setXuexing($value){
		$this->_xuexing = $value;
	}
	public function setYuyan($value){
		$this->_yuyan = $value;
	}
	public function setAihao($value){
		$this->_aihao = $value;
	}
	public function setZhiye($value){
		$this->_zhiye = $value;
	}
	public function setGerencaiyi($value){
		$this->_gerencaiyi = $value;
	}
	public function setBiyeyuanxiao($value){
		$this->_biyeyuanxiao = $value;
	}
	public function setXiuxianfangshi($value){
		$this->_xiuxianfangshi = $value;
	}
	public function setZiwojieshao($value){
		$this->_ziwojieshao = $value;
	}
	public function setPid($value){
		$this->_pid = $value;
	}
	public function setLook_credit($value){
		$this->_look_credit = $value;
	}
	public function setZl_flag($value){
		$this->_zl_flag = $value;
	}
	
	
	
	
	public function setCache_config($_cache_config) {
		$this->_cache_config = $_cache_config;
	}

	 
	public  function __set($property_name, $value) {

		$this->$property_name = $value;

	}

	public function __get($property_name) {

		if (isset ( $this->$property_name )) {

			return $this->$property_name;

		} else {

			return null;

		}

	}

	 
	/**
	 * insert into  keke_witkey_space  ,or add new record
	 * @return int last_insert_id
	 */
	function create_keke_witkey_space(){
		$data =  array();

		if(!is_null($this->_uid)){
			$data['uid']=$this->_uid;
		}
		if(!is_null($this->_username)){
			$data['username']=$this->_username;
		}
		if(!is_null($this->_password)){
			$data['password']=$this->_password;
		}
		if(!is_null($this->_sec_code)){
			$data['sec_code']=$this->_sec_code;
		}
		if(!is_null($this->_email)){
			$data['email']=$this->_email;
		}
		if(!is_null($this->_user_pic)){
			$data['user_pic']=$this->_user_pic;
		}
		if(!is_null($this->_group_id)){
			$data['group_id']=$this->_group_id;
		}
		if(!is_null($this->_isvip)){
			$data['isvip']=$this->_isvip;
		}
		if(!is_null($this->_status)){
			$data['status']=$this->_status;
		}
		if(!is_null($this->_user_type)){
			$data['user_type']=$this->_user_type;
		}
		if(!is_null($this->_sex)){
			$data['sex']=$this->_sex;
		}
		if(!is_null($this->_marry)){
			$data['marry']=$this->_marry;
		}
		if(!is_null($this->_hometown)){
			$data['hometown']=$this->_hometown;
		}
		if(!is_null($this->_residency)){
			$data['residency']=$this->_residency;
		}
		if(!is_null($this->_address)){
			$data['address']=$this->_address;
		}
		if(!is_null($this->_birthday)){
			$data['birthday']=$this->_birthday;
		}
		if(!is_null($this->_truename)){
			$data['truename']=$this->_truename;
		}
		if(!is_null($this->_idcard)){
			$data['idcard']=$this->_idcard;
		}
		if(!is_null($this->_idpic)){
			$data['idpic']=$this->_idpic;
		}
		if(!is_null($this->_qq)){
			$data['qq']=$this->_qq;
		}
		if(!is_null($this->_msn)){
			$data['msn']=$this->_msn;
		}
		if(!is_null($this->_fax)){
			$data['fax']=$this->_fax;
		}
		if(!is_null($this->_phone)){
			$data['phone']=$this->_phone;
		}
		if(!is_null($this->_mobile)){
			$data['mobile']=$this->_mobile;
		}
		if(!is_null($this->_indus_id)){
			$data['indus_id']=$this->_indus_id;
		}
		if(!is_null($this->_indus_pid)){
			$data['indus_pid']=$this->_indus_pid;
		}
		if(!is_null($this->_skill_ids)){
			$data['skill_ids']=$this->_skill_ids;
		}
		if(!is_null($this->_summary)){
			$data['summary']=$this->_summary;
		}
		if(!is_null($this->_experience)){
			$data['experience']=$this->_experience;
		}
		if(!is_null($this->_reg_time)){
			$data['reg_time']=$this->_reg_time;
		}
		if(!is_null($this->_reg_ip)){
			$data['reg_ip']=$this->_reg_ip;
		}
		if(!is_null($this->_domain)){
			$data['domain']=$this->_domain;
		}
		if(!is_null($this->_credit)){
			$data['credit']=$this->_credit;
		}
		if(!is_null($this->_balance)){
			$data['balance']=$this->_balance;
		}
		if(!is_null($this->_balance_status)){
			$data['balance_status']=$this->_balance_status;
		}
		if(!is_null($this->_pub_num)){
			$data['pub_num']=$this->_pub_num;
		}
		if(!is_null($this->_take_num)){
			$data['take_num']=$this->_take_num;
		}
		if(!is_null($this->_nominate_num)){
			$data['nominate_num']=$this->_nominate_num;
		}
		if(!is_null($this->_accepted_num)){
			$data['accepted_num']=$this->_accepted_num;
		}
		if(!is_null($this->_vip_start_time)){
			$data['vip_start_time']=$this->_vip_start_time;
		}
		if(!is_null($this->_vip_end_time)){
			$data['vip_end_time']=$this->_vip_end_time;
		}
		if(!is_null($this->_pay_zfb)){
			$data['pay_zfb']=$this->_pay_zfb;
		}
		if(!is_null($this->_pay_cft)){
			$data['pay_cft']=$this->_pay_cft;
		}
		if(!is_null($this->_pay_bank)){
			$data['pay_bank']=$this->_pay_bank;
		}
		if(!is_null($this->_score)){
			$data['score']=$this->_score;
		}
		if(!is_null($this->_buyer_credit)){
			$data['buyer_credit']=$this->_buyer_credit;
		}
		if(!is_null($this->_buyer_good_num)){
			$data['buyer_good_num']=$this->_buyer_good_num;
		}
		if(!is_null($this->_buyer_level)){
			$data['buyer_level']=$this->_buyer_level;
		}
		if(!is_null($this->_buyer_total_num)){
			$data['buyer_total_num']=$this->_buyer_total_num;
		}
		if(!is_null($this->_seller_credit)){
			$data['seller_credit']=$this->_seller_credit;
		}
		if(!is_null($this->_seller_good_num)){
			$data['seller_good_num']=$this->_seller_good_num;
		}
		if(!is_null($this->_seller_level)){
			$data['seller_level']=$this->_seller_level;
		}
		if(!is_null($this->_seller_total_num)){
			$data['seller_total_num']=$this->_seller_total_num;
		}
		if(!is_null($this->_studio_id)){
			$data['studio_id']=$this->_studio_id;
		}
		if(!is_null($this->_last_login_time)){
			$data['last_login_time']=$this->_last_login_time;
		}
		if(!is_null($this->_client_status)){
			$data['client_status']=$this->_client_status;
		}
		if(!is_null($this->_recommend)){
			$data['recommend']=$this->_recommend;
		}
		if(!is_null($this->_union_user)){
			$data['union_user']=$this->_union_user;
		}
		if(!is_null($this->_union_assoc)){
			$data['union_assoc']=$this->_union_assoc;
		}
		if(!is_null($this->_union_rid)){
			$data['_union_rid']=$this->_union_rid;
		}
		
		
		if(!is_null($this->_xueli)){
			$data['_xueli']=$this->_xueli;
		}
		if(!is_null($this->_shengao)){
			$data['_shengao']=$this->_shengao;
		}
		if(!is_null($this->_tizhong)){
			$data['_tizhong']=$this->_tizhong;
		}
		if(!is_null($this->_xingzuo)){
			$data['_xingzuo']=$this->_xingzuo;
		}
		if(!is_null($this->_xuexing)){
			$data['_xuexing']=$this->_xuexing;
		}if(!is_null($this->_yuyan)){
			$data['_yuyan']=$this->_yuyan;
		}
		if(!is_null($this->_aihao)){
			$data['_aihao']=$this->_aihao;
		}
		if(!is_null($this->_zhiye)){
			$data['_zhiye']=$this->_zhiye;
		}
		if(!is_null($this->_gerencaiyi)){
			$data['_gerencaiyi']=$this->_gerencaiyi;
		}
		if(!is_null($this->_biyeyuanxiao)){
			$data['_biyeyuanxiao']=$this->_biyeyuanxiao;
		}
		if(!is_null($this->_xiuxianfangshi)){
			$data['_xiuxianfangshi']=$this->_xiuxianfangshi;
		}
		if(!is_null($this->_ziwojieshao)){
			$data['_ziwojieshao']=$this->_ziwojieshao;
		}
		if(!is_null($this->_pid)){
			$data['_pid']=$this->_pid;
		}
		if(!is_null($this->_look_credit)){
			$data['_look_credit']=$this->_look_credit;
		}
		if(!is_null($this->_zl_flag)){
			$data['_zl_flag']=$this->_zl_flag;
		}

		return $this->_uid = $this->_db->inserttable($this->_tablename,$data,1,$this->_replace);
	}

	/**
	 * edit table keke_witkey_space
	 * @return int affected_rows
	 */
	function edit_keke_witkey_space(){
		$data =  array();

		if(!is_null($this->_uid)){
			$data['uid']=$this->_uid;
		}
		if(!is_null($this->_username)){
			$data['username']=$this->_username;
		}
		if(!is_null($this->_password)){
			$data['password']=$this->_password;
		}
		if(!is_null($this->_sec_code)){
			$data['sec_code']=$this->_sec_code;
		}
		if(!is_null($this->_email)){
			$data['email']=$this->_email;
		}
		if(!is_null($this->_user_pic)){
			$data['user_pic']=$this->_user_pic;
		}
		if(!is_null($this->_group_id)){
			$data['group_id']=$this->_group_id;
		}
		if(!is_null($this->_isvip)){
			$data['isvip']=$this->_isvip;
		}
		if(!is_null($this->_status)){
			$data['status']=$this->_status;
		}
		if(!is_null($this->_user_type)){
			$data['user_type']=$this->_user_type;
		}
		if(!is_null($this->_sex)){
			$data['sex']=$this->_sex;
		}
		if(!is_null($this->_marry)){
			$data['marry']=$this->_marry;
		}
		if(!is_null($this->_hometown)){
			$data['hometown']=$this->_hometown;
		}
		if(!is_null($this->_residency)){
			$data['residency']=$this->_residency;
		}
		if(!is_null($this->_address)){
			$data['address']=$this->_address;
		}
		if(!is_null($this->_birthday)){
			$data['birthday']=$this->_birthday;
		}
		if(!is_null($this->_truename)){
			$data['truename']=$this->_truename;
		}
		if(!is_null($this->_idcard)){
			$data['idcard']=$this->_idcard;
		}
		if(!is_null($this->_idpic)){
			$data['idpic']=$this->_idpic;
		}
		if(!is_null($this->_qq)){
			$data['qq']=$this->_qq;
		}
		if(!is_null($this->_msn)){
			$data['msn']=$this->_msn;
		}
		if(!is_null($this->_fax)){
			$data['fax']=$this->_fax;
		}
		if(!is_null($this->_phone)){
			$data['phone']=$this->_phone;
		}
		if(!is_null($this->_mobile)){
			$data['mobile']=$this->_mobile;
		}
		if(!is_null($this->_indus_id)){
			$data['indus_id']=$this->_indus_id;
		}
		if(!is_null($this->_indus_pid)){
			$data['indus_pid']=$this->_indus_pid;
		}
		if(!is_null($this->_skill_ids)){
			$data['skill_ids']=$this->_skill_ids;
		}
		if(!is_null($this->_summary)){
			$data['summary']=$this->_summary;
		}
		if(!is_null($this->_experience)){
			$data['experience']=$this->_experience;
		}
		if(!is_null($this->_reg_time)){
			$data['reg_time']=$this->_reg_time;
		}
		if(!is_null($this->_reg_ip)){
			$data['reg_ip']=$this->_reg_ip;
		}
		if(!is_null($this->_domain)){
			$data['domain']=$this->_domain;
		}
		if(!is_null($this->_credit)){
			$data['credit']=$this->_credit;
		}
		if(!is_null($this->_balance)){
			$data['balance']=$this->_balance;
		}
		if(!is_null($this->_balance_status)){
			$data['balance_status']=$this->_balance_status;
		}
		if(!is_null($this->_pub_num)){
			$data['pub_num']=$this->_pub_num;
		}
		if(!is_null($this->_take_num)){
			$data['take_num']=$this->_take_num;
		}
		if(!is_null($this->_nominate_num)){
			$data['nominate_num']=$this->_nominate_num;
		}
		if(!is_null($this->_accepted_num)){
			$data['accepted_num']=$this->_accepted_num;
		}
		if(!is_null($this->_vip_start_time)){
			$data['vip_start_time']=$this->_vip_start_time;
		}
		if(!is_null($this->_vip_end_time)){
			$data['vip_end_time']=$this->_vip_end_time;
		}
		if(!is_null($this->_pay_zfb)){
			$data['pay_zfb']=$this->_pay_zfb;
		}
		if(!is_null($this->_pay_cft)){
			$data['pay_cft']=$this->_pay_cft;
		}
		if(!is_null($this->_pay_bank)){
			$data['pay_bank']=$this->_pay_bank;
		}
		if(!is_null($this->_score)){
			$data['score']=$this->_score;
		}
		if(!is_null($this->_buyer_credit)){
			$data['buyer_credit']=$this->_buyer_credit;
		}
		if(!is_null($this->_buyer_good_num)){
			$data['buyer_good_num']=$this->_buyer_good_num;
		}
		if(!is_null($this->_buyer_level)){
			$data['buyer_level']=$this->_buyer_level;
		}
		if(!is_null($this->_buyer_total_num)){
			$data['buyer_total_num']=$this->_buyer_total_num;
		}
		if(!is_null($this->_seller_credit)){
			$data['seller_credit']=$this->_seller_credit;
		}
		if(!is_null($this->_seller_good_num)){
			$data['seller_good_num']=$this->_seller_good_num;
		}
		if(!is_null($this->_seller_level)){
			$data['seller_level']=$this->_seller_level;
		}
		if(!is_null($this->_seller_total_num)){
			$data['seller_total_num']=$this->_seller_total_num;
		}
		if(!is_null($this->_studio_id)){
			$data['studio_id']=$this->_studio_id;
		}
		if(!is_null($this->_last_login_time)){
			$data['last_login_time']=$this->_last_login_time;
		}
		if(!is_null($this->_client_status)){
			$data['client_status']=$this->_client_status;
		}
		if(!is_null($this->_recommend)){
			$data['recommend']=$this->_recommend;
		}
		if(!is_null($this->_union_user)){
			$data['union_user']=$this->_union_user;
		}
		if(!is_null($this->_union_assoc)){
			$data['union_assoc']=$this->_union_assoc;
		}
		if(!is_null($this->_union_rid)){
			$data['_union_rid']=$this->_union_rid;
		}
		
		
		if(!is_null($this->_xueli)){
			$data['xueli']=$this->_xueli;
		}
		if(!is_null($this->_shengao)){
			$data['shengao']=$this->_shengao;
		}
		if(!is_null($this->_tizhong)){
			$data['tizhong']=$this->_tizhong;
		}
		if(!is_null($this->_xingzuo)){
			$data['xingzuo']=$this->_xingzuo;
		}
		if(!is_null($this->_xuexing)){
			$data['xuexing']=$this->_xuexing;
		}if(!is_null($this->_yuyan)){
			$data['yuyan']=$this->_yuyan;
		}
		if(!is_null($this->_aihao)){
			$data['aihao']=$this->_aihao;
		}
		if(!is_null($this->_zhiye)){
			$data['zhiye']=$this->_zhiye;
		}
		if(!is_null($this->_gerencaiyi)){
			$data['gerencaiyi']=$this->_gerencaiyi;
		}
		if(!is_null($this->_biyeyuanxiao)){
			$data['biyeyuanxiao']=$this->_biyeyuanxiao;
		}
		if(!is_null($this->_xiuxianfangshi)){
			$data['xiuxianfangshi']=$this->_xiuxianfangshi;
		}
		if(!is_null($this->_ziwojieshao)){
			$data['ziwojieshao']=$this->_ziwojieshao;
		}
		if(!is_null($this->_pid)){
			$data['pid']=$this->_pid;
		}
		if(!is_null($this->_look_credit)){
			$data['look_credit']=$this->_look_credit;
		}
		if(!is_null($this->_zl_flag)){
			$data['zl_flag']=$this->_zl_flag;
		}

		if($this->_where){
			return $this->_db->updatetable($this->_tablename,$data,$this->getWhere());
		}
		else{
			$where = array('uid' => $this->_uid);
			return $this->_db->updatetable($this->_tablename,$data,$where);
		}
	}

	/**
	 * query table: keke_witkey_space,if isset where return where record,else return all record
	 * @return array
	 */
	function query_keke_witkey_space($is_cache=0, $cache_time=0){
		if($this->_where){
			$sql = "select * from $this->_tablename where ".$this->_where;
		}
		else{
			$sql = "select * from $this->_tablename";
		}
		if ($is_cache) {
			$this->_cache_config ['is_cache'] = $is_cache;
		}
		if ($cache_time) {
			$this->_cache_config ['time'] = $cache_time;
		}
		if ($this->_cache_config ['is_cache']) {
			if (CACHE_TYPE) {
				$keke_cache = new keke_cache_class ( CACHE_TYPE );
				$id = $this->_tablename . ($this->_where?"_" .substr(md5 ( $this->_where ),0,6):'');
				$data = $keke_cache->get ( $id );
				if ($data) {
					return $data;
				} else {
					$res = $this->_dbop->query ( $sql );
					$keke_cache->set ( $id, $res,$this->_cache_config['time'] );
					$this->_where = "";
					return $res;
				}
			}
		}else{
			$this->_where = "";
			return  $this->_dbop->query ( $sql );
		}
	}

	/**
	 * query count keke_witkey_space records,if iset where query by where
	 * @return int count records
	 */
	function count_keke_witkey_space(){
		if($this->_where){
			$sql = "select count(*) as count from $this->_tablename where ".$this->_where;
		}
		else{
			$sql = "select count(*) as count from $this->_tablename";
		}
		$this->_where = "";
		return $this->_dbop->getCount($sql);
	}

	/**
	 * delete table keke_witkey_space, if isset where delete by where
	 * @return int deleted affected_rows
	 */
	function del_keke_witkey_space(){
		if($this->_where){
			$sql = "delete from $this->_tablename where ".$this->_where;
		}
		else{
			$sql = "delete from $this->_tablename where uid = $this->_uid ";
		}
		$this->_where = "";
		return $this->_dbop->execute($sql);
	}


	 
	 
}
?>