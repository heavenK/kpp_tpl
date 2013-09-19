<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

if(!$tid)	kekezu::show_msg("主题不存在！","index.php?do=forum&view=list&type_id=$type_id&page_size=$page_size&page=$page&type=$type",3,'','error');


$thread_right_arr = db_factory::query ( "select * from ".TABLEPRE."forum_thread where flag like '%r%' and status=2 and isShow=1 limit 0,10");

$thread_info = db_factory::get_one ( sprintf ( "select * from %sforum_thread where tid=".$tid, TABLEPRE ) );

if(!$thread_info)	kekezu::show_msg("主题不存在！","index.php?do=forum&view=list&type_id=$type_id&page_size=$page_size&page=$page&type=$type",3,'','error');
if($thread_info['status'] != 2 && $uid != $thread_info['uid']) kekezu::show_msg("主题正在审核中！","index.php?do=forum&view=list&type_id=$type_id&page_size=$page_size&page=$page&type=$type",3,'','error');

$post_list = db_factory::query ( sprintf ( "select * from %sforum_post ".$where ." order by floor desc", TABLEPRE ) );

db_factory::execute(" update ".TABLEPRE."forum_thread set views=views+1 where tid=".$tid);

$where = "where floor>1 and tid=".$tid ;


$where_gf = $where." and flag LIKE '%g%'";
$post_ganfang_list = db_factory::query ("select * from ".TABLEPRE."forum_post ".$where_gf );


$where_jh = $where." and flag LIKE '%h%'";
$post_jinghua_list = db_factory::query ( "select * from ".TABLEPRE."forum_post ".$where_jh  );


$count = db_factory::get_count ( sprintf ( "select count(tid) count from %sforum_post ".$where, TABLEPRE ) );
$url = "index.php?do=forum&view=list&type_id=$type_id&page_size=$page_size&page=$page&type=$type";
$page_size  and $page_size = intval ( $page_size ) or $page_size = 10;
$page and $page = intval ( $page ) or $page = 1;
$pages = $kekezu->_page_obj->getPages ( $count, $page_size, $page, $url );

$where .= "  order by floor asc".$pages['where'];
$post_list = db_factory::query ( sprintf ( "select * from %sforum_post ".$where , TABLEPRE ) );


require keke_tpl_class::template ( "forum/" . $view );
