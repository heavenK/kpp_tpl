<?php defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );


/**信誉等级**/
$credit_level = unserialize($member_info['seller_level']);
//行业数组
$indus_arr = $kekezu->_indus_arr;
//认证数组
$auth_arr = get_auth($member_info);
//结束时间描述
$end_time_arr = keke_glob_class::get_taskstatus_desc ();
//公用url
$url = "index.php?do=space&view=store&t=$t&member_id=$member_id&page_size=$page_size";
//服务
$sql_s = "select * from " . TABLEPRE . "witkey_service where service_status='2' and uid = {$member_id} order by on_time asc";
$count_s = db_factory::execute ($sql_s);
//评价
$sql_p = "select mark_id from ".TABLEPRE."witkey_mark where (uid = {$member_id} or by_uid = {$member_id}) and mark_status > 0";
$count_p = db_factory::execute($sql_p);
//案例
$sql_c = "select a.*,a.indus_id in_id,b.* from " . TABLEPRE . "witkey_shop_case as a
		left join " . TABLEPRE . "witkey_service as b
				on a.service_id = b.service_id
					where  a.shop_id = " . intval($shop_info ['shop_id']) . " order by b.service_id desc ";
$count_c = db_factory::execute ( $sql_c );
//需求
$sql_x = "select * from ".TABLEPRE."witkey_task where task_status not in(0,1) and uid = {$member_id} order by start_time asc";
$count_x = db_factory::execute ( $sql_x );
//动态
$sql_d = "select uid,title from " . TABLEPRE . "witkey_feed where 1=1 and uid = {$member_id} order by feed_id desc ";
$count_d = db_factory::execute ( $sql_d );
//公用
$page_size = 10;
$page = $page ? $page : 1;
switch ($t){
	case 'p'://评价
		/*信用评级ajax请求 */
		$come or $come='gz';
		$kekezu->_page_obj->setAjax(1);
		$kekezu->_page_obj->setAjaxDom('ajax_dom');
		$kekezu->_page_obj->setAjaxCove(1);
		if($isajax){
			switch ($sx){
				case 'all': //当前用户
							//雇主身份 （1=>雇主，2=>威客，）
							//评价类型（1=>雇主，2=>威客，）//
							//评价状态：0为未评价,1好评，2中评，3差评

					if($come=='gz'||empty($come)){
						$result = keke_user_mark_class::get_user_mark($member_id,1);
					}else{
						$result = keke_user_mark_class::get_user_mark($member_id,2);
					}
					break;
				case 'good':
					//来自雇主的好评
					if($come=='gz'||empty($come)){
						$result = keke_user_mark_class::get_user_mark($member_id,1,1,1);
					}else{
						$result = keke_user_mark_class::get_user_mark($member_id,2,2,1);
					}
					break;
				case 'middle':
					if($come=='gz'||empty($come)){
						$result = keke_user_mark_class::get_user_mark($member_id,1,1,2);
					}else{
						$result = keke_user_mark_class::get_user_mark($member_id,2,2,2);
					}

					break;
				case 'bad':
					if($come=='gz'||empty($come)){
						$result = keke_user_mark_class::get_user_mark($member_id,1,1,3);
					}else{
						$result = keke_user_mark_class::get_user_mark($member_id,2,2,3);
					}
					break;

			}
			$url .="&isajax=true&sx=$sx";
			$pages = $page_obj->page_by_arr($result, $page_size, $page, $url);
			$result = $pages['data'];
			if($m_ajax){//多重ajax请求
				//require keke_tpl_class::template ( SKIN_PATH . "/space/p_statistic" );
				require keke_tpl_class::template('/space/p_store');
			}else {
				require keke_tpl_class::template ( SKIN_PATH . "/space/p_sx" );
			}

			die;
		}
		if($come=='gz'||empty($come)){
			$result = keke_user_mark_class::get_user_mark($member_id,1);
		}else{
			$result = keke_user_mark_class::get_user_mark($member_id,2);
		}
		$pages = $kekezu->_page_obj->page_by_arr($result, $page_size, $page, $url);
		$result = $pages['data'];
		break;
	case 'c'://案例
		// 成功案例
		$pages = $kekezu->_page_obj->getPages ( $count_c, $page_size, $page, $url );
		$shop_arr = db_factory::query ( $sql_c . $pages['where'] );
		break;
	case 'x'://需求
		$pages = $kekezu->_page_obj->getPages ( $count_x, $page_size, $page, $url );
		$task_arr = db_factory::query($sql_x.$pages['where']);
		break;
	case 'd'://动态
		$pages = $kekezu->_page_obj->getPages ( $count_d, $page_size, $page, $url );
		$feed_arr = db_factory::query($sql_d.$pages['where']);
		break;
	case 'm'://名片

		break;
	case 's'://服务
	default:
		$pages = $kekezu->_page_obj->getPages ( $count_s, $page_size, $page, $url );
		$service_arr = db_factory::query($sql_s.$pages['where']);
		break;
}
//认证信息
function get_auth($user_info){
	$auth_item = keke_auth_base_class::get_auth_item ();
	$auth_temp = array_keys ( $auth_item );
	$user_info ['user_type'] == 2 and $un_code = 'realname' or $un_code = "enterprise";
	$t = implode ( ",", $auth_temp );
	$auth_info = db_factory::query ( " select a.auth_code,a.auth_status,b.auth_title,b.auth_small_ico,b.auth_small_n_ico from " . TABLEPRE . "witkey_auth_record a left join " . TABLEPRE . "witkey_auth_item b on a.auth_code=b.auth_code where a.uid ='".$user_info['uid']."' and FIND_IN_SET(a.auth_code,'$t')", 1, -1 );
	$auth_info = kekezu::get_arr_by_key ( $auth_info, "auth_code" );
	return array('item'=>$auth_item,'info'=>$auth_info,'code'=>$un_code);
}
//
if(isset($banner_path)){
	$res = db_factory::execute(sprintf("update %switkey_shop set banner = '%s' where uid = '%d'",TABLEPRE,$banner_path,$uid));
	$res and kekezu::echojson('更换成功！',1,'') or kekezu::echojson('更换失败！','0','');
}
require keke_tpl_class::template('/space/p_store');
