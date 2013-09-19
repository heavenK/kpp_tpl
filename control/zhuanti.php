<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );

// add by heavenk
$task_count = db_factory::get_one ( sprintf ( " select count(task_id) count from %switkey_task", TABLEPRE ), 1, 600 ); 
$task_in = db_factory::get_one ( sprintf ( " select sum(fina_cash) cash from %switkey_finance where fina_action='task_bid' and fina_type='in' ", TABLEPRE ), 1, 600 ); 
$register = db_factory::get_one ( sprintf ( " select count(uid) count from %switkey_member ", TABLEPRE ), 1, 600 ); 

$task_count =  intval ( $task_count ['count'] );
$task_in = number_format ( $task_in ['cash'], 2, ".", "," );
$register =  intval ( $register ['count'] );
// end
$tmp_arr = get_art_cate();
$year_arr = get_art_by_year();
function get_art_cate() {
	$array = kekezu::get_table_data ( "*", "witkey_article_category", "cat_type='article' and art_cat_pid=1", "", "", "", "", null );
	$tmp_arr = array ();
	kekezu::get_tree ( $array, $tmp_arr, "", "", "art_cat_id", "art_cat_pid", "art_cat_name" );
	return $tmp_arr;
}
function get_art_by_year() {
	$sql2 = "select count(a.art_id) as c ,YEAR(FROM_UNIXTIME(a.pub_time)) as y from %switkey_article as a  left join %switkey_article_category as b  \n" . "on a.art_cat_id = b.art_cat_id where b.cat_type='article'\n" . "GROUP BY y";
	return  db_factory::query ( sprintf ( $sql2, TABLEPRE, TABLEPRE ), true, 5*60);
}
function get_art_list($page, $page_size, $url, $where,$static=0) {
	global $kekezu;
	$sql = "select a.* ,b.cat_name from " . TABLEPRE . "witkey_article a left join " . TABLEPRE . "witkey_article_category b on a.art_cat_id=b.art_cat_id where b.cat_type='article'  $where";
	$csql = "select count(a.art_id) as c  from " . TABLEPRE . "witkey_article a left join " . TABLEPRE . "witkey_article_category b on a.art_cat_id=b.art_cat_id where b.cat_type='article'  $where";
	$count = intval ( db_factory::get_count ( $csql,0,null, 10*60 ) );
	$kekezu->_page_obj->setStatic($static);
	$pages = $kekezu->_page_obj->getPages ( $count, $page_size, $page, $url );
	$art_arr = db_factory::query ( $sql . $pages ['where'], 5*60 );
	return array("date"=>$art_arr,"pages"=>$pages);
}
function get_cat_info ($tmp_arr,$art_cat_id) {
	$id = "artilce_list_cat_info";
	$cobj  = new keke_cache_class();
	$t_arr = $cobj->get($id);
	if(!$t_arr){
		$size = sizeof ( $tmp_arr );
		for($i = 0; $i < $size; $i ++) {
			$t_arr [$tmp_arr [$i] ['art_cat_id']] = $tmp_arr [$i];
		}
		$cobj->set($id, $t_arr,null);
	}
   return $t_arr;
}

if(!$view){
	$art_cat_id = 371;
	
	$page = intval ( $page );
	$page or $page = 1;
	$page_size = intval ( $page_size );
	$year = intval($year);
	$page_size or $page_size = 9;
	$year and $where .= sprintf(" and year(FROM_UNIXTIME(a.pub_time))='%d' ",$year) ;
	$art_cat_id and $where .= sprintf(" and a.art_cat_id='%d'",$art_cat_id );
	$where.= " and a.is_show!=2 ";
	$where .=" order by is_recommend desc,a.listorder asc,pub_time desc";
	$cat_info = get_cat_info ( $tmp_arr, $art_cat_id );
	$cat_info = $cat_info [$art_cat_id];
	$static and ($type=='archive' and $url = $_K['siteurl']."/html/article/archive/{$year}_" or $url = $_K['siteurl']."/html/article/list/{$art_cat_id}_") or  $url = "index.php?do=article&view=article_list&art_cat_id=$art_cat_id&page_size=$page_size&year=$year";
	$static and $pre_url = $_K['siteurl'].'/';
	$article_page_arr = get_art_list ( $page, $page_size, $url, $where,$static);
	$article_arr = $article_page_arr['date'];
	$pages = $article_page_arr['pages'];
	if($cat_info['seo_title']){
		$page_title=$cat_info['seo_title'];
	}else{
		$page_title=$cat_info['cat_name'].'- '.$_K['html_title'];
	}
	if($cat_info['seo_keyword']){
		$page_keyword = $cat_info['seo_keyword'];
	}else{
		$page_keyword = $cat_info['cat_name']. '-' .$kekezu->_sys_config['seo_keyword'];
	}
	if($cat_info['seo_desc']){
		$page_description = $cat_info['seo_desc'];
	}else{
		$page_description = $cat_info['cat_name']. '-' .$kekezu->_sys_config['seo_keyword'];
	}
	require keke_tpl_class::template ( $do);
}
if($view == 'show'){
	$art_id = intval($art_id)+0;
	$art_cat_id and $art_cat_id = intval($art_cat_id);
	$year and $year = intval($year);
	$art_id or kekezu::show_msg(kekezu::lang("operate_notice"),"index.php?do=article",2,kekezu::lang("test"),"warning");
	$static and $pre_url = $_K['siteurl'].'/';
	$sql  = "select a.* ,b.cat_name from %switkey_article as a
				  left join %switkey_article_category as b  on a.art_cat_id = b.art_cat_id where a.art_id='%d'";
	$art_info = db_factory::get_one(sprintf($sql,TABLEPRE,TABLEPRE,$art_id));
	$art_keyword_arr = db_factory::query("select * from ".TABLEPRE."witkey_article_keyword where keyword_status=1");
	if(is_array($art_keyword_arr)){
	foreach ($art_keyword_arr as $v){
	$art_info['content'] = str_replace($v['word'], "<a href='".$v['url']."' target='_blank'>".$v['word']."</a>", $art_info['content']);
	$show_count = substr_count($art_info['content'], $v['word']);
	if($show_count&&!$art_info['views']){
		db_factory::execute(sprintf("update %switkey_article_keyword set show_count = show_count +%d where keyword_id=%d",TABLEPRE,intval($show_count),$v['keyword_id']));
	}
	}
	}
	$where = " and 1=1";
	if($year){
		  $where .= " and year(from_unixtime(pub_time)) = '$year'";
	} else{
		$art_cat_id and $where .= " and  art_cat_id  = $art_cat_id";
	}
	$art_up_info = db_factory::get_one(sprintf("select  art_id ,art_cat_id,art_title  from %switkey_article  where art_id<'%d'  %s order by art_id desc limit 0,1",TABLEPRE,$art_id,$where));
	$art_down_info = db_factory::get_one(sprintf("select art_id ,art_cat_id,art_title  from %switkey_article  where art_id>'%d' %s order by art_id asc limit 0,1",TABLEPRE,$art_id,$where));
	if(!$_COOKIE["article_".$art_id]){
		$sqlplus = "update %switkey_article set views = views+1 where art_id = %d";
		db_factory::execute(sprintf($sqlplus,TABLEPRE,$art_id));
	}
	setcookie("article_".$art_id,"exist_".$art_id,time()+3600*24, COOKIE_PATH, COOKIE_DOMAIN,NULL,TRUE );
	$page_title=$art_info['art_title'].$art_info['seo_title'].'- '.$_K['html_title'];
	$page_keyword = $art_info['seo_keyword']. '-' .$kekezu->_sys_config['seo_keyword'];
	$page_description = $art_info['seo_desc']. '-' .$kekezu->_sys_config['seo_desc'];
	require keke_tpl_class::template ( $do."_".$view);
}
