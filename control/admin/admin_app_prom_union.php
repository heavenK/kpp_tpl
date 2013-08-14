<?php	defined ( 'ADMIN_KEKE' ) or exit ( 'Access Denied' );
kekezu::admin_check_role ( 160 );
require $template_obj->template ( 'control/admin/tpl/admin_app_prom_union' );
