<?php	get_header();
	$css = get_vicuna_css('index');
?>
	<link rel="stylesheet" type="text/css" href="<?php echo $css; ?>" />
	<title><?php bloginfo('name'); ?></title>
</head>
<body class="mainIndex <?php vicuna_layout('index'); ?>">
<div id="header">
	<p class="siteName"><a href="<?php bloginfo('home'); ?>"><?php bloginfo('name'); ?></a></p>
<!---	<?php vicuna_description(); ?> --->
<?php vicuna_global_navigation() ?>
</div>


<?php	if ( !is_home() ) : ?>
	<link rel="start" href="<?php bloginfo('home'); ?>" title="<?php bloginfo('name'); ?> Home" />
<?php	endif; ?>
<?php	if ( $description = get_bloginfo('description') ) : ?>
	<meta name="description" content="<?php bloginfo('description'); ?>" />
<?php	endif; ?>
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />


<div id="content">
	<p class="description"><?php vicuna_description(); ?>

<?php	get_sidebar(); ?>

<?php	get_footer(); ?>
