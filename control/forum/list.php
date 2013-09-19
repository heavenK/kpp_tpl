<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$gonggao_arr = db_factory::query(sprintf("select art_id,art_title,listorder,is_recommend,pub_time from %switkey_article where art_cat_id=17 order by is_recommend desc, listorder asc, pub_time desc limit 0,10",TABLEPRE));

$art_arr = db_factory::query(sprintf("select art_id,art_title,listorder,is_recommend,pub_time from %switkey_article where art_cat_id=358 order by is_recommend desc, listorder asc, pub_time desc limit 0,15",TABLEPRE));


$thread_top_arr = db_factory::query ( "select * from ".TABLEPRE."forum_thread where flag like '%t%' and type_id=".$type_id." and isShow=1 limit 0,6");

if(!$type_id) $type_id = 12;

$where = "where isShow=1 and status=2 and type_id=".$type_id;


$count = db_factory::get_count ( sprintf ( "select count(tid) count from %sforum_thread ".$where, TABLEPRE ) );

$url = "index.php?do=forum&view=list&type_id=$type_id&page_size=$page_size&page=$page&type=$type";
$page_size  and $page_size = intval ( $page_size ) or $page_size = 20;
$page and $page = intval ( $page ) or $page = 1;
$pages = $kekezu->_page_obj->getPages ( $count, $page_size, $page, $url );

$where .= " order by pub_time desc".$pages['where'];
$thread_arr = db_factory::query ( sprintf ( "select * from %sforum_thread ".$where, TABLEPRE ) );

require keke_tpl_class::template ( "forum/" . $view );
