<html><head>
<meta http-equiv="Content-Type" content="<?php ks_mimetype(); ?>; charset=<?php ks_charset(); ?>" />
<title><?php ks_title(); ?></title>
<?php if (is_ktai() == 'KDDI') { ?>
	<style>p {margin:0.75em 0;}</style>
<?php }
/* ks_wp_head(); */ ?>
</head>
<body>
<?php /* <body bgcolor="" text="" link="" vlink=""> */ ?>
<?php if (! is_single()) { ?>
	<h1><?php echo get_bloginfo('name'); ?></h1><hr />
<?php }
ks_pict_number(0, true); _e('v', 'ktai_style'); ?><a href="#tail" accesskey="0"><?php _e('Menu', 'ktai_style'); ?></a><hr />