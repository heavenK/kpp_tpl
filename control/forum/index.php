<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

$gonggao_arr = db_factory::query(sprintf("select art_id,art_title,listorder,is_recommend,pub_time from %switkey_article where art_cat_id=17 order by is_recommend desc, listorder asc, pub_time desc limit 0,10",TABLEPRE));

$sql = sprintf ( "select * from %switkey_space s left join %switkey_shop p on s.uid=p.uid where ", TABLEPRE, TABLEPRE );
$where = " s.isvip>0 order by s.isvip asc,s.uid desc limit 0,6";
$shop_info = db_factory::query ( $sql . $where );

$thread_top[12] = getThread(12,0,1,'h');
$thread_part1_arr[12] = getThread(12,0,5,'c');
$thread_part2_arr[12] = getThread_auto(12);

$thread_top[13] = getThread(13,0,1,'h');
$thread_part1_arr[13] = getThread(13,0,5,'c');
$thread_part2_arr[13] = getThread_auto(13);

$thread_top[14] = getThread(14,0,1,'h');
$thread_part1_arr[14] = getThread(14,0,5,'c');
$thread_part2_arr[14] = getThread_auto(14);

$thread_top[15] = getThread(15,0,1,'h');
$thread_part1_arr[15] = getThread(15,0,5,'c');
$thread_part2_arr[15] = getThread_auto(15);

$thread_top[16] = getThread(16,0,1,'h');
$thread_part1_arr[16] = getThread(16,0,5,'c');
$thread_part2_arr[16] = getThread_auto(16);

$thread_top[17] = getThread(17,0,1,'h');
$thread_part1_arr[17] = getThread(17,0,5,'c');
$thread_part2_arr[17] = getThread_auto(17);

$thread_top[18] = getThread(18,0,1,'h');
$thread_part1_arr[18] = getThread(18,0,5,'c');
$thread_part2_arr[18] = getThread_auto(18);

$thread_top[19] = getThread(19,0,1,'h');
$thread_part1_arr[19] = getThread(19,0,5,'c');
$thread_part2_arr[19] = getThread_auto(19);

$thread_top[20] = getThread(20,0,1,'h');
$thread_part1_arr[20] = getThread(20,0,5,'c');
$thread_part2_arr[20] = getThread_auto(20);


function getThread($type_id, $start_num=0, $end_num, $flag){
	return db_factory::query("select * from ".TABLEPRE."forum_thread where isShow=1 and flag like '%".$flag."%' and type_id=".$type_id." order by pub_time desc limit ".$start_num.",".$end_num);
}

function getThread_auto($type_id){
	return db_factory::query("select * from ".TABLEPRE."forum_thread where isShow=1 and type_id=".$type_id." order by views desc limit 0,13");
}

require keke_tpl_class::template ( "forum/" . $view );