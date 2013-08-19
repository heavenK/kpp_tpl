<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$nav_active_index = "index";
$now_cur = strtoupper ( $kekezu->_currency );
$_currencies = keke_curren_class::get_curr_list();
$cur_info = $_currencies[$now_cur];
$cash_coverage = kekezu::get_cash_cove ( '', true );
if ($task_open) {
	$final_task = kekezu::get_classify_indus();
	$indus_recomm_task = db_factory::query(sprintf("select task_id,task_title,task_cash,task_cash_coverage,indus_pid,task_status,start_time from %switkey_task where task_status=2 order by start_time desc limit 0,10",TABLEPRE));
	$indus_recomm_task_1 = db_factory::query(sprintf("select task_id,task_title,task_cash,task_cash_coverage,indus_pid,task_status,start_time from %switkey_task where task_status=2 and model_id=1 order by start_time desc limit 0,10",TABLEPRE));
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
// 最新威客
	$new_member = db_factory::query(sprintf("select *  from %switkey_member order by uid desc limit 0,10",TABLEPRE));

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
$bulletin_arr = db_factory::query(sprintf("select art_id,art_title,listorder,is_recommend,pub_time from %switkey_article where cat_type='bulletin' order by is_recommend desc, listorder asc, pub_time desc limit 0,4",TABLEPRE));
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