<?php	defined ( 'IN_KEKE' ) or exit('Access Denied');
$shop_info=db_factory::get_one(sprintf(" select * from %switkey_shop where uid='%d' ",TABLEPRE,$uid));
$opps = array('basic','case','skill','link','ads');
in_array($opp,$opps) or $opp ="basic";
$ac_url = "index.php?do=$do&view=$view&op=$op&opp=$opp";
if($shop_info){
	$third_nav = array (
				"basic" => array ('店铺设置',$_lang['space_set'] )
		);
		 $third_nav["case"] =array ('案例管理',$_lang['case_manage'] );
}else{
	$ac='open';
	$third_nav=array("basic" => array ($_lang['space_set'],$_lang['space_set']));	
	if(intval($user_info['user_type'])==2){
		$space_fds = array('user_type','summary','address','email','indus_id','indus_pid');
		$where  = null_sql($space_fds);
		$where .=' and uid='.$uid;
		$res = intval(db_factory::get_count(sprintf("select count(*) from %switkey_space where %s",TABLEPRE,$where)));
		$enter_fds = array('company','legal','licen_num');
		$e_where = null_sql($enter_fds);
		$e_res = intval(db_factory::get_count(sprintf("select count(*) from %switkey_auth_enterprise where %s and uid='%d'",TABLEPRE,$e_where,$uid)));
		if(!$res||!$e_res){
			$access = 1;			
			$url = 'index.php?do=user&view=setting&op=basic&from=space';
		}else{
			$access=2;
		}
	}else{
		$fds = array('user_type','sex','birthday','truename','indus_id','indus_pid');
		$where = null_sql($fds);
		$where .= ' and uid='.$uid;
		$res = db_factory::get_count(sprintf("select count(*) from %switkey_space where %s",TABLEPRE,$where));
		$res = intval($res);
		if(!$res){
			$access=1;	
			$url = 'index.php?do=user&view=store&op=setting';
		}else{
			$access=2;
		}
	}	
	if($access==1||($access==2&&$ac!='open')){		
		$opp='notice';
	}
}
require 'user_'.$view."_".$op.'_'.$opp.'.php';
function null_sql($fds){
	if(is_array($fds)){
		foreach($fds as $v){
			$str[] = sprintf("IFNULL(RTRIM(%s),' ')!=' '",$v);
		}
		return implode(' and ', $str);
	}else{
		return  sprintf("IFNULL(RTRIM(%s),' ')!=' '",$fds);
	}
}
