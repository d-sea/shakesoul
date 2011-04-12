<?php ks_force_text_html(); ?>
<html><head>
<meta http-equiv="Content-Type" content="<?php ks_mimetype(); ?>; charset=<?php ks_charset(); ?>" />
<title><?php ks_title(); ?></title>
<?php if (is_ktai() == 'KDDI') { ?><style>p {margin:0.75em 0;}</style><?php }
/* ks_wp_head(); */ ?>
</head><body link="#0066cc" vlink="#0066cc">
<?php if (is_search()) { ?>
	<h1><?php printf(__('Search results of %s', 'ktai_style'), (function_exists('get_search_query') ? get_search_query() : attribute_escape($_GET['s']))); ?></h1>
<?php } elseif (is_category()) { ?>
	<h1><?php printf(__('Archive for the %s category', 'ktai_style'), single_cat_title('', FALSE)); ?></h1>
<?php } elseif (function_exists('is_tag') && is_tag()) { ?>
	<h1><?php printf(__('Posts Tagged as %s', 'ktai_style'), single_tag_title('', FALSE)); ?></h1>
<?php } elseif (is_day()) { ?>
	<h1><?php printf(__('Archive for %s', 'ktai_style'), get_the_time(__('F jS, Y', 'ktai_style'))); ?></h1>
<?php } elseif (is_month()) { ?>
	<h1><?php printf(__('Archive for %s', 'ktai_style'), get_the_time(__('F, Y', 'ktai_style'))); ?></h1>
<?php } elseif (is_year()) { ?>
	<h1><?php printf(__('Archive for year %s', 'ktai_style'), get_the_time(__('Y', 'ktai_style'))); ?></h1>
<?php } elseif (is_author()) { ?>
	<h1><?php printf(__('Archive written by %s', 'ktai_style'), get_the_author()); ?></h1>
<?php } elseif (! is_single()) { ?>
	<?php /* <h1 align="center"><img src="<?php ks_theme_url(); ?>logo.png" alt="<?php bloginfo('name'); ?>" /></h1> */ ?>
	<h1><?php bloginfo('name'); ?></h1>
<?php } ?>
<div align="right"><img localsrc="30" alt="<?php _e('v', 'ktai_style'); ?>" /><a href="#tail"><?php _e('Menu', 'ktai_style'); ?></a></div>
<?php if (! is_single()) { ?><hr color="red" /><?php } 
global $ol_max; $ol_max = 4;
?>