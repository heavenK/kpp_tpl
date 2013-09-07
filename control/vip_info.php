<?php	defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
/**
 * 用户空间的入口
 * 判断当前用户是个人还是企业
 * @copyright keke-tech
 * @author shang
 * @version v 2.0
 * 2010-6-13早上11:25:00
 */

$language = $kekezu->_lang;
keke_lang_class::package_init ( $do );
$shop_info = db_factory::get_one(sprintf("select * from %switkey_shop where uid = '%d'",TABLEPRE,$uid));

$level_pic = unserialize($user_info['seller_level']);

$views = array ('index', 'info', 'question');
in_array ( $view, $views ) or $view = "index";

$top_s_4 = db_factory::query ( sprintf ( "select a.username,a.uid,a.indus_id,a.indus_pid,a.isvip,a.seller_good_num,a.seller_total_num,b.shop_name from %switkey_shop b "
		." left join %switkey_space a on a.uid=b.uid  where a.isvip>0 and a.recommend=1 and IFNULL(b.is_close,0)=0 and shop_status=1 order by a.uid desc limit 0,6", TABLEPRE,TABLEPRE ), 1, 600 );


$task_count = db_factory::get_one ( sprintf ( " select count(task_id) count from %switkey_task", TABLEPRE ), 1, 600 ); 
$task_in = db_factory::get_one ( sprintf ( " select sum(fina_cash) cash from %switkey_finance where fina_action='task_bid' and fina_type='in' ", TABLEPRE ), 1, 600 ); 
$register = db_factory::get_one ( sprintf ( " select count(uid) count from %switkey_member ", TABLEPRE ), 1, 600 ); 

$task_count =  intval ( $task_count ['count'] );
$task_in = number_format ( $task_in ['cash'], 2, ".", "," );
$register =  intval ( $register ['count'] );

require S_ROOT . "control/vip/v_{$view}.php";