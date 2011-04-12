<?php ks_header(); ?>
<!--start paging-->
<h2 id="tags"><?php _e('Tags List', 'ktai_style'); ?></h2>
<?php if (function_exists('wp_tag_cloud')) { ?>
<div><?php ks_tag_cloud(); ?></div>
<?php }
ks_footer(); ?>