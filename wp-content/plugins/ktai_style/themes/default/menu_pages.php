<?php ks_header(); ?>
<!--start paging-->
<h2 id="pages"><?php _e('Pages', 'ktai_style'); ?></h2>
<ul>
<?php wp_list_pages('title_li=&sort_column=menu_order,ID'); ?>
</ul>
<?php ks_footer(); ?>