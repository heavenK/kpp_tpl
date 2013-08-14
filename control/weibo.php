<?php
defined ( 'IN_KEKE' ) or exit('Access Denied');
$page_title=$_lang['weibo_market'];
$custom_logo = 1;
$nav_arr = array(
		0=>array('nav_id' =>  '1','nav_url' =>'index.php','nav_title' =>$_lang['home'] , 'nav_style' =>'index','listorder' => '1'),
		1=>array('nav_id' =>  '2','nav_url' =>'index.php?do=task_list&path=B8','nav_title' =>$_lang['blog_forward_task'] , 'nav_style' =>'task_list','listorder' => '1'),
		2=>array('nav_id' =>  '3','nav_url' =>'index.php?do=task_list&path=B9','nav_title' =>$_lang['blog_click_task'] , 'nav_style' =>'task_list','listorder' => '1'),
		3=>array('nav_id' =>  '4','nav_url' =>'index.php?do=task_list&path=B10','nav_title' =>$_lang['taobao_weibo'] , 'nav_style' =>'task_list','listorder' => '1'),
		);
$_K['theme'] = 'weibo';
$sql = sprintf("select a.obj_id,a.uid,a.username,a.title,a.feed_time,b.model_id from keke_witkey_feed a 
		left join keke_witkey_task b on a.obj_id = b.task_id where a.feedtype='work_accept' and b.model_id in(8,9,10)
		order by feed_id desc limit 10 ");
$feed_arr = db_factory::query($sql);
$footer_load=false;
$basic_config['web_logo'] ='resource/img/weibo/weibo_logo.png';
$sql = sprintf("select * from %switkey_task where model_id in(8,9,10) and task_status=2 and is_top=1 order by start_time desc limit 0,24",TABLEPRE);
$top_weibo_arr = db_factory::query($sql);
$sql = sprintf("select * from %switkey_task where model_id in(8,9,10) and task_status=2  order by start_time desc limit 0,10",TABLEPRE); 
$weibo_arr = db_factory::query($sql); 
$art_arr = db_factory::query(sprintf("select * from %switkey_article where art_cat_id = 384 order by pub_time desc limit 0,4",TABLEPRE));
require keke_tpl_class::template($do);
