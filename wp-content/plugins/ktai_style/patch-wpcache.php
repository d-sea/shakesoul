<?php

/* ==================================================
 *   Patch for WP-Cache / WP Super Cache
   ================================================== */

/*
  Usage:

  1) Duplicate wp-cache-config-sample.php as wp-cache-config.php
     and insert below code into line 19 (WP-Cache) or line 46 
     (WP Super Cache) ; after setting $cache_rejected_user_agent.

if (file_exists(ABSPATH. 'wp-content/plugins/ktai_style/patch-wpcache.php')) {
        include ABSPATH. 'wp-content/plugins/ktai_style/patch-wpcache.php';
}

  2) Place the config file to wp-content/wp-cache-config.php.
  3) Enable WP-Cache or WP Super Cache plugin.
  4) For WP Super Cache, add below code before RewriteRule 
     (there's two places):

RewriteCond %{HTTP_USER_AGENT} !^(DoCoMo/|J-PHONE/|J-EMULATOR/|Vodafone/|MOT(EMULATOR)?-|SoftBank/|[VS]emulator/|KDDI-|UP\.Browser/|emobile/|Huawei/|Nokia)
RewriteCond %{HTTP_USER_AGENT} !(DDIPOCKET;|WILLCOM;|Opera\ Mini|Opera\ Mobi|PalmOS|Windows\ CE;|PDA;\ SL-|PlayStation\ Portable;|SONY/COM|Nitro)

*/

if (! defined('KS_COOKIE_PCVIEW')) :
define ('KS_COOKIE_PCVIEW', 'ks_pc_view');
endif;

if (! isset($_COOKIE[KS_COOKIE_PCVIEW])) {
	$ks_mobile_agents = array(
		'DoCoMo/', 'J-PHONE/', 'J-EMULATOR/', 'Vodafone/', 
		'MOT-', 'MOTEMULATOR-', 'SoftBank/', 'emulator/', 
		'DDIPOCKET;', 'WILLCOM;', 'KDDI-', 'UP.Browser/', 
		'emobile/', 'Huawei/', 'Nokia', 'Opera Mini', 'Opera Mobi', 
		'PalmOS', 'Windows CE;', 'PDA; SL-',
		'(PSP (PlayStation Portable);', 'SONY/COM', 'Nitro) Opera'
	);
	
	$ua = $_SERVER['HTTP_USER_AGENT'];
	foreach ($ks_mobile_agents as $a) {
		if (stripos($ua, $a) !== false) {
			$cache_enabled = false;
			$super_cache_enabled = false;
			break;
		}
	}
	
	$cache_rejected_user_agent = array_merge($cache_rejected_user_agent, $ks_mobile_agents);
}
?>