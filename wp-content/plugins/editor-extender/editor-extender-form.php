<form method="post" action="options.php">
	<div class="wrap">
		<h2>Editor Extender Options</h2>
		<?php wp_nonce_field('update-options'); ?>
		<table width="100%" cellpadding="0" cellspacing="5" border="0" class="widefat">
			<tr>
				<td>
					Advanced HR:
					<input type="checkbox" name="ee_advhr" <?php echo (get_option('ee_advhr')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
				<td>
					Advanced Image:
					<input type="checkbox" name="ee_advimage" <?php echo (get_option('ee_advimage')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
				<td>
					Advanced Link:
					<input type="checkbox" name="ee_advlink" <?php echo (get_option('ee_advlink')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
				<td>
					Context Menu:
					<input type="checkbox" name="ee_contextmenu" <?php echo (get_option('ee_contextmenu')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
				<td>
					Emotions:
					<input type="checkbox" name="ee_emotions" <?php echo (get_option('ee_emotions')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
				<td>
					IE Spell:
					<input type="checkbox" name="ee_iespell" <?php echo (get_option('ee_iespell')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
			</tr>
			<tr class="alternate">
				<td>
					Insert Date/Time:
					<input type="checkbox" name="ee_insertdatetime" <?php echo (get_option('ee_insertdatetime')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
				<td>
					Layer:
					<input type="checkbox" name="ee_layer" <?php echo (get_option('ee_layer')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
				<td>
					Non-breaking line space:
					<input type="checkbox" name="ee_nonbreaking" <?php echo (get_option('ee_nonbreaking')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
				<td>
					Non-Editable:
					<input type="checkbox" name="ee_noneditable" <?php echo (get_option('ee_noneditable')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
				<td>
					Pagebreak:
					<input type="checkbox" name="ee_pagebreak" <?php echo (get_option('ee_pagebreak')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
				<td>
					Preview:
					<input type="checkbox" name="ee_preview" <?php echo (get_option('ee_preview')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
			</tr>
			<tr class="">
				<td>
					Print:
					<input type="checkbox" name="ee_print" <?php echo (get_option('ee_print')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
				<td>
					Search and Replace:
					<input type="checkbox" name="ee_searchreplace" <?php echo (get_option('ee_searchreplace')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
				<td>
					Style:
					<input type="checkbox" name="ee_style" <?php echo (get_option('ee_style')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
				<td>
					Table:
					<input type="checkbox" name="ee_table" <?php echo (get_option('ee_table')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
				<td>
					Xhtml Extras:
					<input type="checkbox" name="ee_xhtmlxtras" <?php echo (get_option('ee_xhtmlxtras')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
				<td>
					Visualchars:
					<input type="checkbox" name="ee_visualchars" <?php echo (get_option('ee_visualchars')) ? 'checked="checked"' : ''; ?> value="1" />
				</td>
			</tr>
			<tr class="alternate">
				<td>
				</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>
		<input type="hidden" name="page_options" value="ee_advhr,ee_advimage,ee_advlink,ee_contextmenu,ee_emotions,ee_iespell,			ee_insertdatetime,ee_layer,ee_nonbreaking,ee_noneditable,ee_pagebreak,ee_preview,ee_print,ee_searchreplace,ee_style,ee_table,ee_pagebreak,ee_visualchars,ee_xhtmlxtras" />
		<input type="hidden" name="action" value="update" />
		<p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
		</p>
	</div>
</form>