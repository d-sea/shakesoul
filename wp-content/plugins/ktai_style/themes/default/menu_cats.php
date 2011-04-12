<?php ks_header(); ?>
<!--start paging-->
<h2 id="cats"><?php _e('Category List', 'ktai_style'); ?></h2>
<ul>
<?php 
if (function_exists('wp_list_categories')) {
	wp_list_categories('orderby=id&show_count=1&use_desc_for_title=0&title_li=');
} else {
	list_cats('orderby=id&show_count=1&use_desc_for_title=0&title_li=');
} ?>
</ul>
<?php ks_footer(); ?>