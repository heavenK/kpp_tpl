<?php
defined ( 'IN_KEKE' ) or exit ( 'Access Denied' );
$page_title = '新手任务' . '- ' . $_K ['html_title'];
$effect_mode_reg = keke_prom_class::get_prom_rule ( 'reg' );
$effect_mode_reg = keke_prom_class::get_prom_rule ( $effect_mode_reg ['auth_step'] );
$auth_code = explode('_', $effect_mode_reg['prom_code']);
$link_reg = $_K [siteurl] . "/index.php?do=prom&u=" . $user_info ['uid'] . "&p=reg";
$promtext or $promtext = $_K ['html_title'];
$link_code = "<a href=" . $link_reg . " target=_blank>" . $promtext . "</a>";
$link_pub = $_K [siteurl] . "/index.php?do=prom&u=" . $user_info ['uid'] . "&p=reg&p=pub_task&l=release";
$link_pub_code = "<a href=" . $link_pub . " target=_blank>" . $promtext . "</a>";
$effect_mode_pub_task = keke_prom_class::get_prom_rule('pub_task');
require keke_tpl_class::template ( "tpl/" . $_K ['template'] . "/" . $do . "/" . $do . "_" . $view );