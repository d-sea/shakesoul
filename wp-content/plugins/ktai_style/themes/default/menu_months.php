<?php ks_header(); ?>
<!--start paging-->
<h2 id="months"><?php _e('Archives by Months', 'ktai_style'); ?></h2>
<ul>
<?php ks_get_archives('type=monthly&show_post_count=1'); ?>
</ul>
<?php ks_footer(); ?>