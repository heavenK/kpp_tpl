<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$first_nav = kekezu::get_table_data ( "art_cat_id,cat_name,cat_type", "witkey_article_category", "art_cat_pid='0' and cat_type='help'", " listorder asc", "", "", "art_cat_id", 3600 );
$fpid_arr = array_keys ( $first_nav );
$fpid or $fpid = $fpid_arr ['0'];
$type = 'help';
if ($fpid) {
	$second_nav = db_factory::query ( sprintf ( " select art_cat_id,cat_name from %switkey_article_category where art_cat_pid='%d' order by listorder asc", TABLEPRE, $fpid ), 1, 3600 );
	$second_nav = kekezu::get_arr_by_key ( $second_nav, "art_cat_id" );
	$spid_arr = array_keys ( $second_nav );
	$spid or $spid = $spid_arr ['0']; 
	$third_list = db_factory::query ( sprintf ( " select art_cat_pid from %switkey_article_category where INSTR(art_index,%d) and art_cat_pid!='%d' order by listorder asc", TABLEPRE, $fpid, $fpid ), 1, 3600 );
	$third_list = kekezu::get_arr_by_key ( $third_list, "art_cat_pid" );
	$third_nav = db_factory::query ( sprintf ( " select art_cat_id,cat_name from %switkey_article_category where art_cat_pid='%d' order by listorder asc", TABLEPRE, $spid ), 1, 3600 );
	$third_nav = kekezu::get_arr_by_key ( $third_nav, "art_cat_id" );
	$tpid_arr = array_keys ( $third_nav );
	$tpid or $tpid = $tpid_arr ['0'];
	$page_obj = $kekezu->_page_obj;
	intval ( $page ) or $page = 1;
	intval ( $page_size ) or $page_size = 10;
	$url = "index.php?do=help&fpid=$fpid&spid=$spid&tpid=$tpid";
	$tpid and $hpid = $tpid or $hpid = $spid;
	$sql = sprintf ( " select art_id,art_title,content,views from %switkey_article where art_cat_id='%d' order by listorder asc ", TABLEPRE, $hpid );
	$count = intval ( db_factory::execute ( $sql ) );
	$pages = $page_obj->getPages ( $count, $page_size, $page, $url );
	$help_list = db_factory::query ( $sql . $pages ['where'], 1, 3600 );
	$page_title = $page_keyword = $second_nav [$spid] ['cat_name'];
	$page_description = '';
	foreach ( $help_list as $v ) {
		$page_description .= $v ['art_title'] . ",";
	}
}
$page_title or $page_title = $_K ['html_title'];
$page_keyword or $page_keyword = $kekezu->_sys_config ['seo_keyword'];
$page_description or $page_description = $kekezu->_sys_config ['seo_desc'];
require keke_tpl_class::template ( $do );
