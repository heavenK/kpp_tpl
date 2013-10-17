<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$record_obj = new Keke_witkey_auth_record_class();
$sys_config = $kekezu->_sys_config;

$shows = array("list","add");
$ac_url = "index.php?do=$do&view=$view&op=$op&opp=$opp";
in_array($show,$shows) or $show="list";

if($show== 'list'){
		if($ac=='del'){
			$res=db_factory::execute(sprintf(" delete from %switkey_shop_ads where ad_id='%d'",TABLEPRE,$ad_id));
			$res and kekezu::echojson( $_lang['delete_success'],"1") or kekezu::echojson( $_lang['delete_fail'],"0");
			die();
		}else{
			//error_reporting(E_ALL);
			$where = "where shop_id=".$shop_info['shop_id'];

			
			$count = db_factory::get_count ( sprintf ( "select count(ad_id) count from %switkey_shop_ads ".$where, TABLEPRE ) );
			
			$url = "index.php?do=forum&view=list&type_id=$type_id&page_size=$page_size&page=$page&type=$type";
			$page_size  and $page_size = intval ( $page_size ) or $page_size = 20;
			$page and $page = intval ( $page ) or $page = 1;
			$pages = $kekezu->_page_obj->getPages ( $count, $page_size, $page, $url );
			
			$where .= " order by ad_id desc".$pages['where'];
			$ads_arr = db_factory::query ( sprintf ( "select * from %switkey_shop_ads ".$where, TABLEPRE ) );
		}	
}else{
	
	if (isset($formhash)&&kekezu::submitcheck($formhash)){
	
		if($_FILES['pic']['name']){
			$pic = keke_file_class::upload_file('pic');
		}else if($hdn_case_pic){
			$pic = $hdn_case_pic;
		}
		
		
		$insertsqlarr = array ('shop_id' => $shop_info['shop_id'],'title' => $title, 'start_time' => strtotime($start_time), 'end_time' => strtotime($end_time), 'pic' => $pic, 'url' => $url );
		if ($ad_id) {
			$result = db_factory::updatetable ( "keke_witkey_shop_ads", $insertsqlarr, array ("ad_id" => $ad_id ) );
		} else {
			$result = db_factory::inserttable ( 'keke_witkey_shop_ads', $insertsqlarr );
		}
		
		if($result)	$msg= '保存成功';
		
		kekezu::show_msg ( "保存成功", "index.php?do=$do&view=$view&op=$op&opp=$opp", '2', $msg, 'alert_right' ) ;
	
	}else{
		
		if($ad_id)	$ads_info = db_factory::get_one(sprintf(" select * from %switkey_shop_ads where ad_id = '%d'",TABLEPRE, $ad_id));
		
	}
}
require keke_tpl_class::template ( "user/user_".$view."_".$op."_".$opp );