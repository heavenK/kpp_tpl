<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page = intval ( $page );
$page or $page = 1;
$page_size = intval ( $page_size );
$year = intval($year);
$page_size or $page_size = 5;
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
require keke_tpl_class::template ( "tpl/" . $_K ['template'] . "/" . $do . "/" . $view );
