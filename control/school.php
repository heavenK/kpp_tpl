<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
// add by heavenk
$task_count = db_factory::get_one ( sprintf ( " select count(task_id) count from %switkey_task", TABLEPRE ), 1, 600 ); 
$task_in = db_factory::get_one ( sprintf ( " select sum(fina_cash) cash from %switkey_finance where fina_action='task_bid' and fina_type='in' ", TABLEPRE ), 1, 600 ); 
$register = db_factory::get_one ( sprintf ( " select count(uid) count from %switkey_member ", TABLEPRE ), 1, 600 ); 

$task_count =  intval ( $task_count ['count'] );
$task_in = number_format ( $task_in ['cash'], 2, ".", "," );
$register =  intval ( $register ['count'] );
// end

$cat_arr = db_factory::query ( sprintf ( "select * from %sxtang_type where pid=0 order by list_order asc", TABLEPRE ) );
$art_top_arr = db_factory::query ( sprintf ( "select * from %sxtang_article where isTop=1 order by pub_time desc limit 0,10", TABLEPRE ) );


$res = db_factory::query ( sprintf ( "select id,type_name from %sxtang_type", TABLEPRE ) );
$type_arr = array();
foreach($res as $key => $val){
	$type_arr[$val['id']] = $val['type_name'];
}

$views = array ('index', 'art', 'question','art_show');
in_array ( $view, $views ) or $view = "index";

if($view == 'art'){
	
	$where = "where type_id=".$type_id;
	
	
	
	$count = db_factory::get_count ( sprintf ( "select count(sid) count from %sxtang_article ".$where, TABLEPRE ) );
	
	$url = "index.php?do=school&view=art&type_id=$type_id&page_size=$page_size&page=$page&type=$type";
	$page_size  and $page_size = intval ( $page_size ) or $page_size = 30;
	$page and $page = intval ( $page ) or $page = 1;
	$pages = $kekezu->_page_obj->getPages ( $count, $page_size, $page, $url );
	
	$where .= " order by pub_time desc".$pages['where'];
	$art_arr = db_factory::query ( sprintf ( "select * from %sxtang_article ".$where, TABLEPRE ) );
	
	require $kekezu->_tpl_obj->template ( $do."_".$view );
}
if($view == 'art_show'){
	
	$art_info = db_factory::get_one ( sprintf ( " select * from %sxtang_article where sid=".$sid, TABLEPRE )); 
	
	if(!$type_id) $type_id = $art_info['type_id'];
	if(!$pid) {
		$pid_info = db_factory::get_one ( sprintf ( " select * from %sxtang_type where id=".$type_id, TABLEPRE )); 
		$pid = $pid_info['pid'];
	}
	
	$sqlplus = "update %sxtang_article set views = views+1 where sid = %d";
	db_factory::execute(sprintf($sqlplus,TABLEPRE,$sid));
	
	
	require $kekezu->_tpl_obj->template ( $do."_".$view );
}

if($view == 'question'){
	
	if(!$uid) kekezu::show_msg("您还没有登录，无法答题！",'index.php?do=login',3,'','fail');
	
	$art_info = db_factory::get_one ( sprintf ( " select * from %sxtang_article where sid=".$sid, TABLEPRE )); 
	
	$where = "where sid=".$sid;
	
	$question_arr = db_factory::query ( sprintf ( "select * from %sxtang_question ".$where." order by list_order asc", TABLEPRE ) );
	
	if($sbt){
		
		$result = 1;
		foreach($question_arr as $key => $val){
			if($val['q_answer'] != $question[$val['qid']]) {
				$result = 0;
				break;
			}
		}
		if($result)	{
			
			$sidUid = db_factory::get_one ( sprintf ( " select * from %sxtang_artuid where sid=".$sid." and uid=".$uid, TABLEPRE )); 
			if(!$sidUid){
				keke_finance_class::cash_in($uid, floatval(0),intval($basic_config['answer_credit']),'answer_credit','','answer_credit');
				$flags = 1;
				$sqlplus = "insert into %sxtang_artuid values(%d,%d)";
				db_factory::execute(sprintf($sqlplus,TABLEPRE,$sid,$uid));
			}

			
			
			require $kekezu->_tpl_obj->template ( $do."_success" );
		}
		else require $kekezu->_tpl_obj->template ( $do."_fail" );
		
	}
	else	require $kekezu->_tpl_obj->template ( $do."_".$view );
}

if($view == 'index'){

	
	if(!$pid) $where = 'where pid >0';
	else $where = "where pid=".$pid;
	
	
	$count = db_factory::get_count ( sprintf ( "select count(id) count from %sxtang_type ".$where." order by id asc, list_order asc", TABLEPRE ) );
	
	$url = "index.php?do=school&pid=$pid&page_size=$page_size&page=$page&type=$type";
	$page_size  and $page_size = intval ( $page_size ) or $page_size = 30;
	$page and $page = intval ( $page ) or $page = 1;
	$pages = $kekezu->_page_obj->getPages ( $count, $page_size, $page, $url );
	
	
	$where .= " GROUP BY t.id ORDER BY sums desc ".$pages['where'];
	
	$sql = "SELECT t.*,SUM(a.views) sums FROM %sxtang_article a LEFT JOIN %sxtang_type t on a.type_id=t.id ".$where;
	
	
	$cat_sub_arr = db_factory::query ( sprintf ( $sql, TABLEPRE, TABLEPRE ) );
	
	require $kekezu->_tpl_obj->template ( $do );
}
