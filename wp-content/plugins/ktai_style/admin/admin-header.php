<?php 
/* ==================================================
 *   Ktai Admin Output Header
   ================================================== */

if (! defined('ABSPATH')) {
	exit;
}
global $mime_type, $iana_charset, $user_identity;
ob_start(); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="<?php echo wp_specialchars($mime_type); ?>; charset=<?php echo wp_specialchars($iana_charset); ?>" />
<title><?php echo get_bloginfo('name') . '&gt;' . wp_specialchars(strip_tags($title)); ?></title>
</head><body>
<div><?php bloginfo('name'); ?></div>
<div align="right"><?php printf(__('Howdy, %s.', 'ktai_style'), $user_identity) ?><a href="#tail"><img localsrc="30" alt="<?php _e('v', 'ktai_style'); ?>" /></a></div>
<hr color="#4f96c8" />
<h1><a name="head"><?php echo wp_specialchars($title); ?></a></h1>
<!--start paging-->