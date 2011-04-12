<?php

/* ==================================================
 *   Ktai_Style_PrefPane class
   ================================================== */

class Ktai_Style_PrefPane {
	private $nonce = -1;

/* ==================================================
 * @param	none
 * @return	none
 */
public function add_page() {
	add_options_page('Ktai Style Configuration', __('Mobile Output', 'ktai_style'), 'manage_options', 'ktai_style_prefpane.php', array($this, 'option_page'));
	if ( !function_exists('wp_nonce_field') ) {
		$this->nonce = -1;
	} else {
		$this->nonce = 'ktai-style-config';
	}
}

/* ==================================================
 * @param	none
 * @return	none
 */
public function option_page() {
	global $user_identity, $Ktai_Style;

	if (isset($_POST['update_option'])) {
		check_admin_referer($this->nonce);
		$this->upate_options();
	}
	if (isset($_POST['delete_option'])) {
		check_admin_referer($this->nonce);
		$this->delete_options();
	}
	$images_to_link    = $this->set_checkbox($Ktai_Style->get_option('ks_images_to_link'));
	$allow_pictograms   = $this->set_checkbox($Ktai_Style->get_option('ks_allow_pictograms'));
	$separate_comments = $this->set_checkbox($Ktai_Style->get_option('ks_separate_comments'));
	$require_term_id   = $this->set_checkbox($Ktai_Style->get_option('ks_require_term_id'));
	$theme               = $Ktai_Style->get_option('ks_theme');
	$theme_mova          = $Ktai_Style->get_option('ks_theme_mova');
	$theme_foma          = $Ktai_Style->get_option('ks_theme_foma');
	$theme_ezweb         = $Ktai_Style->get_option('ks_theme_ezweb');
	$theme_sb_pdc        = $Ktai_Style->get_option('ks_theme_sb_pdc');
	$theme_sb_3g         = $Ktai_Style->get_option('ks_theme_sb_3g');
	$theme_willcom       = $Ktai_Style->get_option('ks_theme_willcom');
	$theme_emobile       = $Ktai_Style->get_option('ks_theme_emobile');
	$treat_as_internal   = $Ktai_Style->get_option('ks_treat_as_internal');
	$date_color          = $Ktai_Style->get_option('ks_date_color');
	$author_color        = $Ktai_Style->get_option('ks_author_color');
	$comment_type_color  = $Ktai_Style->get_option('ks_comment_type_color');
	$external_link_color = $Ktai_Style->get_option('ks_external_link_color');
	$year_format         = $Ktai_Style->get_option('ks_year_format');
	$month_date_format   = $Ktai_Style->get_option('ks_month_date_format');
	$time_format         = $Ktai_Style->get_option('ks_time_format');
?>
<div class="wrap">
<h2><?php _e('Ktai Style Options', 'ktai_style'); ?></h2>
<form method="post" action="">
<?php $this->make_nonce_field($this->nonce); ?>
<h3 id="theme"><?php _e('Theme for mobile', 'ktai_style'); ?></h3>
<table class="optiontable form-table"><tbody>
<?php
	$careers[0] = array('label' => 'theme', 
	                    'desc'  => __('Common theme', 'ktai_style'));
	$careers[]  = array('label' => 'theme_mova', 
	                    'desc'  => __('For mova (DoCoMo)', 'ktai_style'));
	$careers[]  = array('label' => 'theme_foma', 
	                    'desc'  => __('For FOMA (DoCoMo)', 'ktai_style'));
	$careers[]  = array('label' => 'theme_ezweb', 
	                    'desc'  => __('For EZweb (au)', 'ktai_style'));
	$careers[]  = array('label' => 'theme_sb_pdc', 
	                    'desc'  => __('For SoftBank PDC', 'ktai_style'));
	$careers[]  = array('label' => 'theme_sb_3g', 
	                    'desc'  => __('For SoftBank 3G', 'ktai_style'));
	$careers[]  = array('label' => 'theme_willcom', 
	                    'desc'  => __('For WILLCOM', 'ktai_style'));
	$careers[]  = array('label' => 'theme_emobile', 
	                    'desc'  => __('For EMobile Handset', 'ktai_style'));
	foreach ($careers as $index => $c) { ?>
<tr><th><label for="<?php echo $c['label']; ?>"><?php echo $c['desc']; ?></label></th>
<td><select name="<?php echo $c['label']; ?>" id="<?php echo $c['label']; ?>">
<?php	if (strcmp($c['label'], 'theme') !== 0) {
			$current = empty($$c['label']) ? ' selected="selected"' : '';
			echo '<option value="0"' . $current . '>' . __('(Same as common theme)', 'ktai_style') . '</option>';
		}
		foreach($Ktai_Style->installed_themes() as $dir => $name) {
			$current = (strcmp($dir, $$c['label']) === 0) ? ' selected="selected"' : '';
			echo '<option value="' . attribute_escape($dir) . '"' . $current . '>' . attribute_escape($name) . '</option>';
		} ?>
</select></td></tr>
<?php if ($index == 0) { ?>
</tbody></table>
<h4><?php _e('Theme for each career', 'ktai_style'); ?></h4>
<table class="optiontable form-table"><tbody>
<?php } } ?>
</tbody></table>
<p><?php _e('Note: Settings below may not be reflected with other than standard theme (default, compact).', 'ktai_style'); ?></p>
<h3 id="design"><?php _e('Behavior', 'ktai_style'); ?></h3>
<table class="optiontable form-table"><tbody>
<tr>
<th><label for="images_to_link"><?php _e('Image output for 3G phone, WILLCOM, smartphones, etc', 'ktai_style'); ?></label></th> 
<td>
  <label><input type="radio" name="images_to_link" id="images_to_link" value="1"<?php echo $images_to_link[0]; ?> /> <?php _e('Show smaller thumbnails.', 'ktai_style'); ?></label><br />
  <label><input type="radio" name="images_to_link" id="images_to_link" value="2"<?php echo $images_to_link[1]; ?> /> <?php _e('Convert to links as PDC.', 'ktai_style'); ?></label>
</td>
</tr><tr>
<th><label for="allow_pictograms"><?php _e('Typing pictograms at comment forms, post contents', 'ktai_style'); ?></label></th> 
<td>
  <label><input type="radio" name="allow_pictograms" id="allow_pictograms" value="1"<?php echo $allow_pictograms[0]; ?> /> <?php _e('Deny', 'ktai_style'); ?></label><br />
  <label><input type="radio" name="allow_pictograms" id="allow_pictograms" value="2"<?php echo $allow_pictograms[1]; ?> /> <?php _e('Allow', 'ktai_style'); ?></label>
</td>
</tr><tr>
<th><label for="separate_comments"><?php _e('Comments and Trackbacks/Pingbacks', 'ktai_style'); ?></label></th> 
<td>
  <label><input type="radio" name="separate_comments" id="separate_comments" value="1"<?php echo $separate_comments[0]; ?> /> <?php _e('Mix them at each posts and/or recent comments.', 'ktai_style'); ?></label><br />
  <label><input type="radio" name="separate_comments" id="separate_comments" value="2"<?php echo $separate_comments[1]; ?> /> <?php _e('Separate comments and trackbacks.', 'ktai_style'); ?></label>
</td>
</tr><tr>
<th><label for="treat_as_internal"><?php _e('Websites to link directly', 'ktai_style'); ?></label></th>
<td><textarea name="treat_as_internal" id="treat_as_internal" cols="60" rows="2"><?php echo $treat_as_internal; ?></textarea><br /><?php _e('(Not using the redirect page for these sites; space separated)', 'ktai_style'); ?></td>
</tr><tr>
<th><label for="require_term_id"><?php _e('Terminal ID of comment poster', 'ktai_style'); ?></label></th> 
<td>
  <label><input type="radio" name="require_term_id" id="require_term_id" value="1"<?php echo $require_term_id[0]; ?> /> <?php _e('Not required', 'ktai_style'); ?></label><br />
  <label><input type="radio" name="require_term_id" id="require_term_id" value="2"<?php echo $require_term_id[1]; ?> /> <?php _e('Required to send', 'ktai_style'); ?></label><br />
  <small><?php _e('Note: Terminal ID is sensitive private information because the same ID is sent to any mobile sites.<br />Collecting the IDs is a risk of keeping private information of others.<br />Please select "Not required" unless you can get much merit than that risk.', 'ktai_style'); ?></small>
</td>
</tr>
</tbody></table>
<script type="text/javascript">
function change_sample(field, target) {
	var color = field.value;
	if (color.match("^(#[0-9a-fA-F]{6}|[a-z]+)$")) {
		document.getElementById(target).style.color = color;
	}
}
</script>
<h3 id="colors"><?php _e('Text Color', 'ktai_style'); ?></h3>
<p><?php _e('Note: To revert values to default, just empty of the field.', 'ktai_style'); ?></p>
<table class="optiontable form-table"><thead>
<tr><th><?php _e('Target', 'ktai_style'); ?></th><td><?php _e('#rrggbb as hex format', 'ktai_style'); ?></td><td><?php _e('Color sample', 'ktai_style'); ?></td></tr>
</thead><tbody>
<tr>
<th><label for="date_color"><?php _e('Date/time for post titles', 'ktai_style'); ?></label></th>
<td><input type="text" value="<?php echo attribute_escape($date_color); ?>" name="date_color" id="date_color" onkeyup='change_sample(this, "date_color_sample")' /></td>
<td id="date_color_sample" style="color:<?php echo attribute_escape($date_color); ?>;"><?php echo date('Y-m-d H:i'); ?></td>
</tr><tr>
<th><label for="author_color"><?php _e('Author, Date with a post content', 'ktai_style'); ?></label></th>
<td><input type="text" value="<?php echo attribute_escape($author_color); ?>" name="author_color" id="author_color" onkeyup='change_sample(this, "author_color_sample")' /></td>
<td id="author_color_sample" style="color:<?php echo attribute_escape($author_color); ?>;"><?php echo wp_specialchars($user_identity); ?></td>
</tr><tr>
<th><label for="comment_type_color"><?php _e('Comment types', 'ktai_style'); ?></label></th>
<td><input type="text" value="<?php echo attribute_escape($comment_type_color); ?>" name="comment_type_color" id="comment_type_color" onkeyup='change_sample(this, "comment_type_color_sample")' /></td>
<td id="comment_type_color_sample" style="color:<?php echo attribute_escape($comment_type_color); ?>;"><?php echo __('Comment'), '/', __('Trackback'), '/', __('Pingback'); ?></td>
</tr><tr>
<th><label for="external_link_color"><?php _e('Link text for PC targeted external sites', 'ktai_style'); ?></label></th>
<td><input type="text" value="<?php echo attribute_escape($external_link_color); ?>" name="external_link_color" id="external_link_color"  onkeyup='change_sample(this, "external_link_color_sample")' /></td>
<td id="external_link_color_sample" style="color:<?php echo attribute_escape($external_link_color); ?>;">http://wppluginsj.sourceforge.jp/</td>
</tr>
</tbody></table>
<p><?php _e('Note: To configure background color/normal text color/hyperlink color/visited link color, edit &lt;body&gt; element at themes/*/header.php', 'ktai_style'); ?></p>
<h3 id="date_format"><?php _e('Date format of posts/comments', 'ktai_style'); ?></h3>
<table class="optiontable form-table"><tbody>
<tr>
<th><label for="year_format"><?php _e('In case of displaying year, month, date<br />(for last year and before)', 'ktai_style'); ?></label></th>
<td><input type="text" value="<?php echo $year_format ?>" name="year_format" id="year_format" /></td>
</tr><tr>
<th><label for="month_date_format"><?php _e('In case of displaying month, date only<br />(for this year)', 'ktai_style'); ?></label></th>
<td><input type="text" value="<?php echo $month_date_format ?>" name="month_date_format" id="month_date_format" /></td>
</tr><tr>
<th><label for="time_format"><?php _e('Time', 'ktai_style'); ?></label></th>
<td><input type="text" value="<?php echo $time_format ?>" name="time_format" id="time_format" /></td>
</tr>
</tbody></table>
<p><?php _e('Note: About date format, refer to <a href="http://codex.wordpress.org/Formatting_Date_and_Time">Codex</a> or <a href="http://www.php.net/date">date() function manual</a> of PHP.', 'ktai_style'); ?></p>
<div class="submit">
<input type="hidden" name="action" value="update" />
<input type="submit" name="update_option" value="<?php 
if ($Ktai_Style->check_wp_version('2.5', '>=')) {
	_e('Save Changes');
} elseif ($Ktai_Style->check_wp_version('2.1', '>=')) {
	_e('Update Options &raquo;');
} else {
	echo __('Update Options') . " &raquo;";
} ?>" />
</div>
<hr />
<h3 id="delete_options"><?php _e('Delete Options', 'ktai_style'); ?></h3>
<div class="submit">
<input type="submit" name="delete_option" value="<?php _e('Delete option values and revert them to default &raquo;', 'ktai_style'); ?>" onclick="return confirm('<?php _e('Do you really delete option values and revert them to default?', 'ktai_style'); ?>')" />
</div>
</form>
</div>
<?php
} 

/* ==================================================
 * @param	mix   $action
 * @return	none
 */
private function make_nonce_field($action = -1) {
	if ( !function_exists('wp_nonce_field') ) {
		return;
	} else {
		return wp_nonce_field($action);
	}
}

/* ==================================================
 * @param   boolean $flag
 * @return	none
 */
private function set_checkbox($flag) {
	if ($flag) {
		$check[0] = '';
		$check[1] = ' checked="checked"';
	} else {
		$check[0] = ' checked="checked"';
		$check[1] = '';
	}
	return $check;
}

/* ==================================================
 * @param	none
 * @return	none
 */
private function upate_options() {
	$theme_opts = array('theme', 'theme_mova', 'theme_foma', 'theme_ezweb', 'theme_sb_pdc', 'theme_sb_3g', 'theme_willcom');
	foreach ($theme_opts as $t) {
		if (empty($_POST[$t])) {
			delete_option('ks_' . $t);
		} elseif (! preg_match('|[^-_.+/a-zA-Z0-9]|', $_POST[$t])) {
			if (strpos($_POST[$t], '/') !== false) {
				$theme_dir = (defined('WP_CONTENT_DIR') ? WP_CONTENT_DIR . '/': ABSPATH . 'wp-content/') . $_POST[$t];
			} else {
				$theme_dir = dirname(__FILE__) . '/' . KS_BUILT_IN_THEMES_DIR . '/' . $_POST[$t];
			}
			if (file_exists($theme_dir . '/index.php') && file_exists($theme_dir . '/style.css')) {
				update_option('ks_' . $t, $_POST[$t]);
			}
		}
	}
	$this->update_boolean_option('images_to_link');
	$this->update_boolean_option('allow_pictograms');
	$this->update_boolean_option('separate_comments');
	$this->update_boolean_option('require_term_id');
	$this->update_hex_option('author_color');
	$this->update_hex_option('date_color');
	$this->update_hex_option('comment_type_color');
	$this->update_hex_option('external_link_color');

	if (! empty($_POST['treat_as_internal'])) {
		$sites = preg_split('/\\s+/', $_POST['treat_as_internal'], -1, PREG_SPLIT_NO_EMPTY);
		$sites = array_map('clean_url', $sites);
		$sites = preg_replace('#/$#', '', $sites);
		$sites_join = implode(' ', $sites);
		if (! preg_match('/^\\s*$/', $sites_join)) {
			update_option('ks_treat_as_internal', $sites_join);
		} else {
			delete_option('ks_treat_as_internal');
		}
	} else {
		delete_option('ks_treat_as_internal');
	}

	if (! empty($_POST['year_format'])) {
		if (preg_match('/[Yyo]/', $_POST['year_format']) && preg_match('/[mnMF]/', $_POST['year_format']) && preg_match('/[djz]/', $_POST['year_format'])) {
			update_option('ks_year_format', $_POST['year_format']);
		}
	} else {
		delete_option('ks_year_format');
	}

	if (! empty($_POST['month_date_format'])) {
		if (preg_match('/[mnMF]/', $_POST['month_date_format']) && preg_match('/[djz]/', $_POST['month_date_format'])) {
			update_option('ks_month_date_format', $_POST['month_date_format']);
		}
	} else {
		delete_option('ks_month_date_format');
	}

	if (! empty($_POST['time_format'])) {
		if (preg_match('/[BgGhH]/', $_POST['time_format'])) {
			update_option('ks_time_format', $_POST['time_format']);
		}
	} else {
		delete_option('ks_time_format');
	}
?>
<div class="updated fade"><p><strong><?php _e('Options saved.'); ?></strong></p></div>
<?php
	return;
}

/* ==================================================
 * @param	string  $key
 * @return	none
 */
private function update_boolean_option($key) {
	if (! empty($_POST[$key])) {
		if (is_numeric($_POST[$key])) {
			update_option('ks_' . $key, ($_POST[$key] == 2));
		}
	} else {
		delete_option('ks_' . $key);
	}
}

/* ==================================================
 * @param	string  $key
 * @return	none
 */
private function update_hex_option($key) {
	if (! empty($_POST[$key])) {
		if (preg_match('/^#[0-9a-fA-F]{6}$/', $_POST[$key])) {
			update_option('ks_' . $key, $_POST[$key]);
		}
	} else {
		delete_option('ks_' . $key);
	}
}

/* ==================================================
 * @param	none
 * @return	none
 */
private function delete_options() {
	$theme_opts = array('theme', 'theme_mova', 'theme_foma', 'theme_ezweb', 'theme_sb_pdc', 'theme_sb_3g', 'theme_willcom');
	foreach ($theme_opts as $t) {
		delete_option('ks_' . $t);
	}
	delete_option('ks_title_only'); // obsolete option
	delete_option('ks_external_link'); // obsolete option
	delete_option('ks_images_to_link');
	delete_option('ks_allow_pictograms');
	delete_option('ks_separate_comments');
	delete_option('ks_separate_recent_comments'); // obsolete option
	delete_option('ks_treat_as_internal');
	delete_option('ks_require_term_id');
	delete_option('ks_author_color');
	delete_option('ks_date_color');
	delete_option('ks_comment_type_color');
	delete_option('ks_external_link_color');
	delete_option('ks_year_format');
	delete_option('ks_month_date_format');
	delete_option('ks_time_format');
?>
<div class="updated fade"><p><strong><?php _e('Options Deleted.', 'ktai_style'); ?></strong></p></div>
<?php
	return;
}

/* ==================================================
 * @param	none
 * @return	none
 */
public function add_comment_meta() {
	 if (function_exists('add_meta_box')) {
		add_meta_box('term_sub_ID', __("Poster's terminal ID and subscriber ID", 'ktai_style'), array($this, 'show_author_id'), 'comment', 'normal');
	}
}

/* ==================================================
 * @param	object   $comment
 * @param	mix      $box
 * @return	none
 */
public function show_author_id($comment, $box) {
	$id = Ktai_Services::read_term_id($comment);
	$author = array();
	if (count($id)) {
		if ($id[0]) {
			$author[] = sprintf(__('Term ID: %s', 'ktai_style'), attribute_escape($id[0]));
		}
		if ($id[1]) {
			$author[] = sprintf(__('USIM ID: %s', 'ktai_style'), attribute_escape($id[1]));
		}
		if ($id[2]) {
			$author[] = sprintf(__('Sub ID: %s', 'ktai_style'), attribute_escape($id[2]));
		}
		if (count($author)) {
			echo implode('<br />', $author);
		} else {
			_e('N/A', 'ktai_style');
		}
	} else {
		_e('N/A', 'ktai_style');
	}
}

// ===== End of class ====================
}
?>