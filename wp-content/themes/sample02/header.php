<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php bloginfo('name'); ?><?php wp_title(': '); ?></title>

<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats please -->

<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" />
</head>
<body>

<div id="header">
<h1><a href="<?php bloginfo('url'); ?>">
<img src="<?php bloginfo('template_directory'); ?>/images/logo.gif" alt="<?php bloginfo('name'); ?>" />
</a></h1>

<ul>
<li class="page_item"><a href="<?php bloginfo('url'); ?>" title="トップページ">TOP</a></li>
<?php wp_list_pages('sort_column=menu_order & depth=1 & title_li='); ?>
</ul>
</div>