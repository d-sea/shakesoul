<?php
/* ==================================================
 *   Ktai Admin Options of Mobile Theme
 *   based on wp-admin/options-writing.php of WP 2.3
   ================================================== */

require dirname(__FILE__) . '/admin.php';
$title = __('Options');
$parent_file = 'options-cat.php';
include dirname(__FILE__) . '/admin-header.php';
if (isset($_GET['updated'])) { ?>
<p><font color="blue"><?php _e('Options saved.') ?></font></p>
<?php }
$theme         = $Ktai_Style->get_option('ks_theme');
$theme_mova    = $Ktai_Style->get_option('ks_theme_mova');
$theme_foma    = $Ktai_Style->get_option('ks_theme_foma');
$theme_ezweb   = $Ktai_Style->get_option('ks_theme_ezweb');
$theme_sb_pdc  = $Ktai_Style->get_option('ks_theme_sb_pdc');
$theme_sb_3g   = $Ktai_Style->get_option('ks_theme_sb_3g');
$theme_willcom = $Ktai_Style->get_option('ks_theme_willcom');
$theme_emobile = $Ktai_Style->get_option('ks_theme_emobile');
?>
<h2><?php _e('Mobile Theme', 'ktai_style'); ?></h2>
<form method="post" action="options.php">
<dl>
<?php
$KS_Admin->sid_field(); wp_nonce_field('update-options');
$careers[0] = array('label' => 'theme', 
					'desc'  => __('Common theme', 'ktai_style'));
$careers[]  = array('label' => 'theme_mova', 
					'desc'  => __('For mova', 'ktai_style'));
$careers[]  = array('label' => 'theme_foma', 
					'desc'  => __('For FOMA', 'ktai_style'));
$careers[]  = array('label' => 'theme_ezweb', 
					'desc'  => __('For EZweb', 'ktai_style'));
$careers[]  = array('label' => 'theme_sb_pdc', 
					'desc'  => __('For SoftBank PDC', 'ktai_style'));
$careers[]  = array('label' => 'theme_sb_3g', 
					'desc'  => __('For SoftBank 3G', 'ktai_style'));
$careers[]  = array('label' => 'theme_willcom', 
					'desc'  => __('For WILLCOM', 'ktai_style'));
$careers[]  = array('label' => 'theme_emobile', 
					'desc'  => __('For EMobile', 'ktai_style'));
$opts = array();
	foreach ($careers as $c) {
		$opt = 'ks_' . $c['label'];
		$opts[] = $opt;
?>
<dt><?php echo $c['desc']; ?></dt><dd><select name="<?php echo $opt; ?>" id="<?php echo $opt; ?>">
<?php	if (strcmp($c['label'], 'theme') !== 0) {
			$current = empty($$c['label']) ? ' selected="selected"' : '';
			echo '<option value="0"' . $current . '>' . __('(Same as common theme)', 'ktai_style') . '</option>';
		}
		foreach($Ktai_Style->installed_themes() as $dir => $name) {
			$current = strcmp($dir, $$c['label']) === 0 ? ' selected="selected"' : '';
			echo '<option value="' . attribute_escape($dir) . '"' . $current . '>' . attribute_escape($name) . '</option>';
		} ?>
</select></dd>
<?php } ?>
</dl><div><input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="<?php echo implode(',', $opts); ?>" />
<input type="submit" name="Submit" value="<?php _e('Set Theme', 'ktai_style'); ?>" />
</div></form>
<?php include dirname(__FILE__) . '/admin-footer.php'; ?>