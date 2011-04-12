<?php
/* ==================================================
 *   Ktai Admin Options of Default Category
 *   based on wp-admin/options-writing.php of WP 2.3
   ================================================== */

require dirname(__FILE__) . '/admin.php';
$title = __('Options');
$parent_file = 'options-cat.php';
include dirname(__FILE__) . '/admin-header.php';
if (isset($_GET['updated'])) { ?>
<p><font color="blue"><?php _e('Options saved.') ?></font></p>
<?php } ?>
<h2><?php _e('Default Post Category:', 'ktai_style'); ?></h2>
<form method="post" action="options.php">
<?php $KS_Admin->sid_field(); wp_nonce_field('update-options'); ?>
<div><?php echo $KS_Admin->dropdown_categories('default_category', get_option('default_category')); ?>
</div>
<div><input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="default_category" />
<input type="submit" name="Submit" value="<?php _e('Set Category', 'ktai_style'); ?>" />
</div></form>
<?php include dirname(__FILE__) . '/admin-footer.php'; ?>