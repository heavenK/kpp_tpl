<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$nav_active_index = "index";
$now_cur = strtoupper ( $kekezu->_currency );
$_currencies = keke_curren_class::get_curr_list();
$cur_info = $_currencies[$now_cur];
$cash_coverage = kekezu::get_cash_cove ( '', true );
if ($task_open) {
	$final_task = kekezu::get_classify_indus();
	$indus_recomm_task = db_factory::query(sprintf("select task_id,task_title,task_cash,task_cash_coverage,indus_pid,task_status,start_time from %switkey_task where task_status=2 and model_id in (1,2,4) order by start_time desc limit 0,9",TABLEPRE));
	$indus_recomm_task_1 = db_factory::query(sprintf("select task_id,task_title,task_cash,task_cash_coverage,indus_pid,task_status,start_time from %switkey_task where task_status=2 and model_id=1 or model_id=2 order by start_time desc limit 0,9",TABLEPRE));
	$task_array = array();
	if($indus_recomm_task){
		foreach($indus_recomm_task as $k=>$v){
			$task_array[$v['indus_pid']][]=$v;
		}
	}
	$task_in = db_factory::get_one ( sprintf ( " select sum(fina_cash) cash from %switkey_finance where fina_action='task_bid' and fina_type='in' ", TABLEPRE ), 1, 600 ); 
	$task_in = number_format ( $task_in ['cash'], 2, ".", "," );
	$task_count = db_factory::get_one ( sprintf ( " select count(task_id) count from %switkey_task", TABLEPRE ), 1, 600 ); 
	$task_count =  intval ( $task_count ['count'] );
	$task_recomm_3 = db_factory::query ( sprintf ( " select task_id,uid,username,task_title,task_cash,model_id,view_num,focus_num,work_num,task_cash_coverage from %switkey_task where is_top='1' and task_status='2' order by start_time desc limit 0,3", TABLEPRE ), 1, 600 );
	$sql = " select task_id,task_title,task_cash,view_num,focus_num,work_num,task_cash_coverage,indus_pid
		 from %switkey_task  where  (task_status='2' or task_status ='3' or task_status ='4' or task_status ='5' or task_status ='6')
		  order by start_time desc limit 3,39";
	$recomm_task = db_factory::query ( sprintf ( $sql, TABLEPRE ), true, 3600 );
}
if ($shop_open) {
	$final_shop = kekezu::get_classify_indus('shop');
	$indus_recomm_service = db_factory::query(sprintf("select service_id,title,price,unite_price,indus_pid,service_status from %switkey_service where is_top=1 and service_status=2 order by on_time desc",TABLEPRE));
	$service_array = array();
	if($indus_recomm_service){
		foreach($indus_recomm_service as $k=>$v){
			$service_array[$v['indus_pid']][]=$v;
		}
	}
	$service_in = db_factory::get_one ( sprintf ( " select sum(fina_cash) cash from %switkey_finance where fina_action='sale_service' and fina_type='in'", TABLEPRE ), 1, 600 ); 
	$service_in =  number_format ( $service_in ['cash'], 2, ".", "," );
	$service_count = db_factory::get_one ( sprintf ( " select count(service_id) count from %switkey_service where service_status='2'", TABLEPRE ), 1, 600 ); 
	$service_count = intval ( $service_count ['count'] );
	$top_s_4 = db_factory::query ( sprintf ( "select a.username,a.uid,a.indus_id,a.indus_pid,a.seller_good_num,a.seller_total_num,b.shop_name from %switkey_shop b "
		." left join %switkey_space a on a.uid=b.uid  where a.recommend=1 and IFNULL(b.is_close,0)=0 and shop_status=1 order by a.uid desc limit 0,6", TABLEPRE,TABLEPRE ), 1, 600 );
	$range = range ( 0, 11);
	$recomm_service = db_factory::query ( sprintf ( "select service_id,price,unite_price,pic,ad_pic,title from %switkey_service where  service_status='2' order by on_time desc limit 0,12", TABLEPRE ), 1, 600 );
}


$indus_all_arr = $kekezu->_indus_arr;
// 设计

	$rencai_list = db_factory::query(sprintf("select *  from %switkey_space where indus_id is not NULL order by indus_id desc",TABLEPRE));
	$rencai_count = array();
	for($i=0 ; $i<=800; $i++){
		$rencai_count[$i] = 0;
	}
	foreach($rencai_list as $key => $val){
		$rencai_count[$val['indus_id']]++;
		
	}
	
	$rencai_list1 = db_factory::query(sprintf("select *  from %switkey_space where skill_ids is not NULL order by uid desc",TABLEPRE));
	$rencai_count1 = array();
	foreach($rencai_list1 as $key => $val){
		$skills = explode(',',$val['skill_ids']);
		foreach($skills as $key1 => $val1){
			$rencai_count1[$val1]++;
		}
	}
	
	
	$user_list_arr = db_factory::query(" select * from ".TABLEPRE."witkey_member_index where type in ('s1','s2','s3','s4','s5') order by id asc");
	$user1 = array();
	$user2 = array();
	$user3 = array();
	$user4 = array();
	$user5 = array();
	$i = 0;
	$j = 0;
	$k = 0;
	$m = 0;
	$n = 0;
	foreach($user_list_arr as $key => $val){
		if($val['type'] == 's1') {
			$user1[$i] = $val['uid'];
			$i++;
		}
		if($val['type'] == 's2') {
			$user2[$j] = $val['uid'];
			$j++;
		}
		if($val['type'] == 's3') {
			$user3[$k] = $val['uid'];
			$k++;
		}
		if($val['type'] == 's4') {
			$user4[$m] = $val['uid'];
			$m++;
		}
		if($val['type'] == 's5') {
			$user5[$n] = $val['uid'];
			$n++;
		}
	}
	
	//$sj_member = db_factory::query(sprintf("select *  from %switkey_member m left join %switkey_space s on m.uid=s.uid where s.indus_pid=441 order by m.uid desc limit 0,8",TABLEPRE,TABLEPRE));
	//$sj_member_2 = db_factory::query(sprintf("select *  from %switkey_member m left join %switkey_space s on m.uid=s.uid where s.indus_pid=441 order by m.uid desc limit 8,8",TABLEPRE,TABLEPRE));
// 开发
/*	$kf_member = db_factory::query(sprintf("select *  from %switkey_member m left join %switkey_space s on m.uid=s.uid where s.indus_pid=2 order by m.uid desc limit 0,8",TABLEPRE,TABLEPRE));
	$kf_member_2 = db_factory::query(sprintf("select *  from %switkey_member m left join %switkey_space s on m.uid=s.uid where s.indus_pid=2 order by m.uid desc limit 8,8",TABLEPRE,TABLEPRE));
// 策划
	$ch_member = db_factory::query(sprintf("select *  from %switkey_member m left join %switkey_space s on m.uid=s.uid where s.indus_pid=3 order by m.uid desc limit 0,8",TABLEPRE,TABLEPRE));
	$ch_member_2 = db_factory::query(sprintf("select *  from %switkey_member m left join %switkey_space s on m.uid=s.uid where s.indus_pid=3 order by m.uid desc limit 8,8",TABLEPRE,TABLEPRE));
// 装修
	$zx_member = db_factory::query(sprintf("select *  from %switkey_member m left join %switkey_space s on m.uid=s.uid where s.indus_pid=335 order by m.uid desc limit 0,8",TABLEPRE,TABLEPRE));
	$zx_member_2 = db_factory::query(sprintf("select *  from %switkey_member m left join %switkey_space s on m.uid=s.uid where s.indus_pid=335 order by m.uid desc limit 8,8",TABLEPRE,TABLEPRE));
// 服务
	$fw_member = db_factory::query(sprintf("select *  from %switkey_member m left join %switkey_space s on m.uid=s.uid where s.indus_pid=192 order by m.uid desc limit 0,8",TABLEPRE,TABLEPRE));
	$fw_member_2 = db_factory::query(sprintf("select *  from %switkey_member m left join %switkey_space s on m.uid=s.uid where s.indus_pid=192 order by m.uid desc limit 8,8",TABLEPRE,TABLEPRE));*/




// 最新威客
	$new_member = db_factory::query(sprintf("select *  from %switkey_member m left join %switkey_space s on m.uid=s.uid order by s.reg_time desc limit 0,10",TABLEPRE,TABLEPRE));
	foreach($new_member as $key => $val){
		$credit_level[$key] = unserialize($val['seller_level']);
	}
// end
// 最新VIP威客
	$new_member_vip = db_factory::query(sprintf("select *  from %switkey_member m left join %switkey_space s on m.uid=s.uid where s.isvip>0 order by s.vip_start_time desc limit 0,10",TABLEPRE,TABLEPRE));
	foreach($new_member_vip as $key => $val){
		$new_member_vip[$key]['seller_level'] = unserialize($val['seller_level']);
	}
// end

// 最新VIP威客
	$accept_member = db_factory::query(sprintf("select *  from %switkey_feed m left join %switkey_space s on m.uid=s.uid where m.feedtype='work_accept' group by m.uid order by m.feed_time desc limit 0,10",TABLEPRE,TABLEPRE));
	foreach($accept_member as $key => $val){
		$accept_member[$key]['seller_level'] = unserialize($val['seller_level']);
	}



// 最新创意任务
	$logo_task_end = db_factory::query(sprintf("select *  from %switkey_task where task_status=8 and indus_pid in (441,457,357,458,459,460) order by task_id desc limit 0,4",TABLEPRE));
	$logo_task = db_factory::query(sprintf("select *  from %switkey_task where task_status=2 and indus_pid in (441,457,357,458,459,460)  order by task_id desc limit 0,4",TABLEPRE));
// end
// 最新程序开发
	$web_task_end = db_factory::query(sprintf("select *  from %switkey_task where task_status=8 and indus_pid in (2,455,456,249) order by task_id desc limit 0,4",TABLEPRE));
	$web_task = db_factory::query(sprintf("select *  from %switkey_task where task_status=2 and indus_pid in (2,455,456,249)  order by task_id desc limit 0,4",TABLEPRE));
// end
// 最新营销推广
	$tg_task_end = db_factory::query(sprintf("select *  from %switkey_task where task_status=8 and indus_pid in (442,537,538) order by task_id desc limit 0,4",TABLEPRE));
	$tg_task = db_factory::query(sprintf("select *  from %switkey_task where task_status=2 and indus_pid in (442,537,538)  order by task_id desc limit 0,4",TABLEPRE));
// end
// 最新生活服务
	$life_task_end = db_factory::query(sprintf("select *  from %switkey_task where task_status=8 and indus_pid in (498,499,500) order by task_id desc limit 0,4",TABLEPRE));
	$life_task = db_factory::query(sprintf("select *  from %switkey_task where task_status=2 and indus_pid in (498,499,500)  order by task_id desc limit 0,4",TABLEPRE));
// end

// vip
	$vip_shop1 = db_factory::query("select *  from ".TABLEPRE."witkey_member_index i left join ".TABLEPRE."witkey_space s on i.uid=s.uid left join ".TABLEPRE."witkey_shop m on m.uid=s.uid where i.type='vip1' order by i.id asc limit 0,5");
	$vip_shop2 = db_factory::query("select *  from ".TABLEPRE."witkey_member_index i left join ".TABLEPRE."witkey_space s on i.uid=s.uid left join ".TABLEPRE."witkey_shop m on m.uid=s.uid where i.type='vip2' order by i.id asc limit 0,5");
	$vip_shop3 = db_factory::query("select *  from ".TABLEPRE."witkey_member_index i left join ".TABLEPRE."witkey_space s on i.uid=s.uid left join ".TABLEPRE."witkey_shop m on m.uid=s.uid where i.type='vip3' order by i.id asc limit 0,5");
	$vip_shop4 = db_factory::query("select *  from ".TABLEPRE."witkey_member_index i left join ".TABLEPRE."witkey_space s on i.uid=s.uid left join ".TABLEPRE."witkey_shop m on m.uid=s.uid where i.type='vip4' order by i.id asc limit 0,5");


/*	$vip_shop1 = db_factory::query(sprintf("select *  from %switkey_space s left join %switkey_shop m on m.uid=s.uid where s.recommend=1 order by s.uid desc limit 0,5",TABLEPRE,TABLEPRE));
	$vip_shop2 = db_factory::query(sprintf("select *  from %switkey_space s left join %switkey_shop m on m.uid=s.uid where s.recommend=1 order by s.uid desc limit 5,5",TABLEPRE,TABLEPRE));
	$vip_shop3 = db_factory::query(sprintf("select *  from %switkey_space s left join %switkey_shop m on m.uid=s.uid where s.recommend=1 order by s.uid desc limit 10,5",TABLEPRE,TABLEPRE));
	$vip_shop4 = db_factory::query(sprintf("select *  from %switkey_space s left join %switkey_shop m on m.uid=s.uid where s.recommend=1 order by s.uid desc limit 15,5",TABLEPRE,TABLEPRE));*/
/*	foreach($new_member as $key => $val){
		$new_member[$key]['seller_level'] = unserialize($val['seller_level']);
	}*/
// end

// baozhang
	$ensure = array('无',500,1000,2000);
// end





if(isset($op)&&$op == 'suggest'){
		$title='我有话要说';
		if($sbt_edit){
		 kekezu::check_login($url,'json');
		    if (CHARSET == 'gbk') {
				$pro_title = kekezu::escape(kekezu::utftogbk ( $txt_title ));
				$desc = kekezu::escape(kekezu::utftogbk ( $tar_content ));
		    }else{
		    	$desc = kekezu::escape($tar_content);
		    	$pro_title = kekezu::escape($txt_title);
		    }
		    $suggest_obj = new Keke_witkey_proposal_class();
		    $suggest_obj->setPro_title($pro_title);
		    $suggest_obj->setPro_type($slt_type);
		    $suggest_obj->setPro_desc($desc);
		    $suggest_obj->setPro_status(1);
		    $suggest_obj->setPro_time(time());
		    $suggest_obj->setUid($uid);
		    $suggest_obj->setUsername($username);
		    $suggest_id = $suggest_obj->create_keke_witkey_proposal();
		    if ($suggest_id) {
			 kekezu::keke_show_msg ( '', '提交成功', "", 'json' );
		    } else {
			 kekezu::keke_show_msg ( '', '提交失败', "error", 'json' );
		    }
		}else{
			require keke_tpl_class::template("suggest");
		}
			die();	
}
if(isset($op)&&$op == 'report_check'){
	$ee = keke_report_class::check_if_report($type, $obj, $obj_id, $_SESSION['uid'], $to_uid);
	if($ee){
		kekezu::echojson('',1);
	}else{
		kekezu::echojson('',0);
	}
}
$register = db_factory::get_one ( sprintf ( " select count(uid) count from %switkey_member ", TABLEPRE ), 1, 600 ); 
$register =  intval ( $register ['count'] );
$all_auth = db_factory::get_one ( sprintf ( " select count(record_id) count from %switkey_auth_record where auth_status='1'", TABLEPRE ), 1, 600 ); 
$all_auth =  intval ( $all_auth ['count'] );

$bulletin_arr = db_factory::query(sprintf("select art_id,art_title,listorder,is_recommend,pub_time from %switkey_article where art_cat_id=17 order by is_recommend desc, listorder asc, pub_time desc limit 0,10",TABLEPRE));
//$bulletin_arr = db_factory::query(sprintf("select art_id,art_title,listorder,is_recommend,pub_time from %switkey_article where art_cat_id=5 order by is_recommend desc, listorder asc, pub_time desc limit 0,9",TABLEPRE));
$shaishi_arr = db_factory::query(sprintf("select * from %sforum_thread where type_id=14 and isShow=1 and status=2 order by pub_time desc limit 0,9",TABLEPRE));

$feed_list = db_factory::query ( "select uid,username,title,feed_time from " . TABLEPRE . "witkey_feed order by feed_time desc limit 0,4", 1, 3600 );
$mode_list = $kekezu->_model_list;
$news_list = kekezu::get_table_data ( "art_id,art_title,listorder,is_recommend,art_pic,content,pub_time", "witkey_article", " cat_type='article' ", "is_recommend desc, listorder asc, pub_time desc", "", "9", "", 3600 );
if ($task_open&&$shop_open) {
	$case_list = kekezu::get_table_data ( "case_id,obj_id,obj_type,case_img,case_title,case_price", "witkey_case", "", "on_time desc", "", "6", "", 3600 );
}elseif($task_open&&!$shop_open){
	$case_list = kekezu::get_table_data ( "case_id,obj_id,obj_type,case_img,case_title,case_price", "witkey_case", " obj_type='task' ", "on_time desc", "", "6", "", 3600 );
}elseif(!$task_open&&$shop_open){
	$case_list = kekezu::get_table_data ( "case_id,obj_id,obj_type,case_img,case_title,case_price", "witkey_case", " obj_type='service' ", "on_time desc", "", "6", "", 3600 );
}
$talent_list = db_factory::query ( sprintf ( " select uid,username from %switkey_space where status!=2 order by reg_time desc limit 0,16", TABLEPRE ), 1, 600 );
$income_rank = db_factory::query ( sprintf ( " select sum(a.fina_cash) as cash,a.uid,a.username from %switkey_finance a left join %switkey_space b on a.uid=b.uid where a.fina_type='in' and ( a.fina_action in('task_bid','sale_service') or INSTR(a.fina_action,'prom_')>0) and b.status!=2 group by a.uid order by cash desc limit 0,5 ", TABLEPRE, TABLEPRE ), 1, 600 ); 
if (isset ( $ajax )) {
	switch ($ajax) {
		case "task" :
			$sql2 = " select task_id,task_title,task_cash,view_num,focus_num,work_num,task_cash_coverage
		 from %switkey_task  where  (task_status='2' or task_status ='3' ) 
		  order by start_time desc limit 0,42";
			$task_lastest = db_factory::query ( sprintf ( $sql2, TABLEPRE ), true, 3600 );
			require keke_tpl_class::template ( "ajax/index" );
			die ();
			break;
		case "shop" :
			$service_lastes = db_factory::query ( sprintf ( "select service_id,pic,ad_pic,title,unite_price,price from %switkey_service where   service_status='2' order by on_time desc limit 0,12", TABLEPRE ), 1, 600 );
			require keke_tpl_class::template ( "ajax/index" );
			die ();
			break;
		case 'indus_index' :
			require keke_tpl_class::template ( "ajax/ajax_indus" );
			die ();
			break;
		case 'bid_notice' :
			$dynamic_arr = kekezu::get_feed ( "feedtype='work_accept'", "feed_time desc", 4 ); 
		    require keke_tpl_class::template ( "ajax/index" );
		    die ();
		    break;
		case 'withdraw' :
			$withdraw_arr = db_factory::query(sprintf("select * from %switkey_withdraw where withdraw_status=2 order by process_time desc limit 0,4",TABLEPRE));
		    require keke_tpl_class::template ( "ajax/index" );
		    die ();
		    break;				
	}
}
$page_title = $_K ['html_title'];
$link_task = $kekezu->_model_list;
$link_news = kekezu::get_table_data ( "art_cat_id,cat_name", "witkey_article_category", "art_cat_pid=0 and cat_type='article'", " listorder asc", "", "5", "", 3600 );
$link_about = kekezu::get_table_data ( "art_id,art_title,listorder,is_recommend,art_pic,content,pub_time", "witkey_article", " cat_type='about' ", "is_recommend desc, listorder asc, pub_time desc", "", "10", "", 3600 );
$link_help = kekezu::get_table_data ( "art_cat_id,cat_name", "witkey_article_category", "art_cat_pid=0 and cat_type='help'", " listorder asc", "", "5", "", 3600 );
require keke_tpl_class::template ( $do );