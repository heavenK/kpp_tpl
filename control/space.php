<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
/**
 * 用户空间的入口
 * 判断当前用户是个人还是企业
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-6-13早上11:25:00
 */

$member_id = intval ( $member_id );
$language = $kekezu->_lang;
keke_lang_class::package_init ( $do );
$member_info = kekezu::get_user_info ( $member_id );
//店铺信息
$shop_info = db_factory::get_one(sprintf("select * from %switkey_shop where uid = '%d'",TABLEPRE,$member_id));

//seo的相关
$shop_info['seo_title'] and $page_title = $shop_info['seo_title'];
if($shop_info['seo_keyword']){
	$page_keyword = $shop_info['seo_keyword'];
}else{
	if($member_info['indus_id']&&$member_info['indus_pid']){
		$page_keyword = $shop_info['shop_name'].','.$indus_arr[$member_info['indus_id']]['indus_name'].','.$indus_p_arr[$member_info['indus_pid']]['indus_name'];
	}else{
		$page_keyword = $shop_info['shop_name'];
	}
}
if($shop_info['seo_desc']){
	$page_description =$shop_info['seo_desc'];
}else{
	if($shop_info['shop_desc']){
		$page_description = kekezu::cutstr(strip_tags($shop_info['shop_desc']),200);
	}else{
		$page_description = kekezu::cutstr(strip_tags($shop_info['shop_slogans']),200);
	}

}

/* $banner_column = 'banner'; //对应的数据库字段,首页幻灯和其他页面分别存在不同字段,所以这个是变化的_企业空间
	$banner_column = 'homebanner'; //首页对应的幻灯片字段
	$e_route_arr = array_slice ( $e_route_arr, 0, 5 ); //默认 */
//店铺幻灯
if ($shop_info ['homebanner']) {
	$banner_arr = unserialize ( $shop_info ['homebanner'] );
} else {
	$banner_arr = array (
			'sy' => 'tpl/default/img/enterprise/banner_img.jpg',
			'gsjs' => 'tpl/default/img/enterprise/banner_img.jpg',
			'qycy' => 'tpl/default/img/enterprise/qycy_banner.jpg',
			'xgrw' => 'tpl/default/img/enterprise/rw_banner.jpg',
			'spzs' => 'tpl/default/img/enterprise/sp_banner.jpg',
			'cgal' => 'tpl/default/img/enterprise/suc_banner.jpg',
			'gstj' => 'tpl/default/img/enterprise/gstj_banner.jpg' );
}
if ($shop_info ['shop_backstyle']) { //空间背景图片的显示
	$shop_backstyle = unserialize ( $shop_info ['shop_backstyle'] );
}
if($shop_backstyle){
	//关联数组implode是
	$shop_backstyle_value = implode ( ' ', array_values ( $shop_backstyle ) );
}
$bgimg = "resource/img/system/img_pw.jpg";
$shop_background = file_exists($shop_info['shop_background'])?$shop_info['shop_background']:$bgimg;
/**
 * 设置空间风格。背景图
 */
if ($uid == $shop_info ['uid'] && $ac == 'custom') {
	switch ($t) {
		case 'bground' : //背景
			$bg_repeat = array ('no-repeat' => $_lang ['not_repeat'], 'repeat-x' => $_lang ['x_repeat'], 'repeat-y' => $_lang ['y_repeat'], 'repeat' => $_lang ['default'] ); //跟空间的背景有关验证数组
			$bg_scroll = array ('scroll' => $_lang ['scroll'], 'fixed' => $_lang ['fixed'] ); //跟空间的背景有关验证数组
			$bg_position = array ('left' => $_lang ['upper_left_corner'], 'center' => $_lang ['center'], 'right' => $_lang ['upper_right_corner'] ); //跟空间的背景有关验证数组
			//修改背景位置
			if ($sbt == 1) {
				/*begin 空间背景图片的定位*/
				$bgstyle = array ();
				array_key_exists ( strval ( $repeat ), $bg_repeat ) && $bgstyle ['repeat'] = strval ( $repeat );
				array_key_exists ( strval ( $scroll ), $bg_scroll ) && $bgstyle ['scroll'] = strval ( $scroll );
				array_key_exists ( strval ( $position ), $bg_position ) && $bgstyle ['position'] = strval ( $position ) . ' top';
				$bgstyle && $shop_backstyle = serialize ( $bgstyle );
				/*end 空间背景图片的定位*/
				db_factory::execute(sprintf(" update %switkey_shop set shop_backstyle='%s' where shop_id='%d'",TABLEPRE,$shop_backstyle,$shop_info['shop_id']));
			}

			if ($ajax) {
				$fieldss = array ('logo', 'shop_background', 'banner' );
				if (! $fields || ! in_array ( $fields, $fieldss )) {
					kekezu::echojson ( $_lang ['fail_set'], "0" );
				}
				$fid = db_factory::get_count(sprintf(" select file_id from %switkey_file where save_name='%s'",TABLEPRE,$shop_info['shop_background']));


				kekezu::del_att_file($fid);
				$fields && isset ( $filePath ) and $res = db_factory::execute ( sprintf ( " update %switkey_shop set %s='%s' where shop_id='%d'", TABLEPRE, $fields, $filePath, $shop_info ['shop_id'] ) );
				$res and kekezu::echojson ( $_lang ['successfully_set'],1) or kekezu::echojson ( $_lang ['fail_set'],0 );
			}
			if ($rever && $rever == 'change') {
				$sql = sprintf ( "update %switkey_shop set shop_background=null where shop_id=%d", TABLEPRE, $shop_info ['shop_id'] );
				$result = db_factory::execute ( $sql );
				if($result){
					$fid = db_factory::get_count(sprintf(" select file_id from %switkey_file where save_name='%s'",TABLEPRE,$shop_info['shop_background']));
					kekezu::del_att_file($fid);
					kekezu::echojson ( $_lang ['successfully_set'], "1" );die();
				}
				kekezu::echojson ( $_lang ['fail_set'], "0" );die();
			}
			break;
	}
	//require keke_tpl_class::template ( 'space/space_custom' );
	die ();
}

$type = 'p';
//$view = 'store';
if(!$view) $view = 'index';

$ip = kekezu::get_ip ();

if ($_COOKIE ['ip'] != 1) {
	db_factory::execute ( sprintf ( " update %switkey_shop set views=views+1 where uid=%d", TABLEPRE, $member_id ) );
	setcookie ( "ip", 1, time () + 3600 * 24, COOKIE_PATH, COOKIE_DOMAIN,NULL,TRUE );
}
keke_lang_class::package_init ( "space" );
keke_lang_class::loadlang ( "{$type}_{$view}" );

$footer_load = false;
//空间地址
$p_url = kekezu::build_space_url($member_id);

//统计
$task_count = db_factory::get_one ( sprintf ( " select count(task_id) count from %switkey_task", TABLEPRE ), 1, 600 ); 
$task_in = db_factory::get_one ( sprintf ( " select sum(fina_cash) cash from %switkey_finance where fina_action='task_bid' and fina_type='in' ", TABLEPRE ), 1, 600 ); 
$register = db_factory::get_one ( sprintf ( " select count(uid) count from %switkey_member ", TABLEPRE ), 1, 600 ); 

$task_count =  intval ( $task_count ['count'] );
$task_in = number_format ( $task_in ['cash'], 2, ".", "," );
$register =  intval ( $register ['count'] );
require S_ROOT . "control/space/{$type}_{$view}.php";