<?php

/*
+----------------------------------------------------------------+
+	wp-table-admin V1.11
+	by Alex Rabe
+   required for wp-table
+----------------------------------------------------------------+
*/

global $wpdb;

### Variables Variables Variables
$base_name = plugin_basename('wp-table/wp-table-admin.php');
$base_page = 'admin.php?page='.$base_name;
$mode = trim($_GET['mode']);
$act_table = trim($_GET['id']);

// Main Page

// ### Start the button form processing 
if (isset($_POST['do'])){
			$mode ='edit'; // reset mode if not selected delrow
	switch(key($_POST['do'])) {
		case 0:	// SAVE and EXIT
			$mode =''; // go back to main page
			
		case 1: // UPDATE 
			// read the $_POST values
			$upd_name = addslashes(trim($_POST['tabelname']));
			$upd_desc = addslashes(trim($_POST['description']));
			if(!empty($upd_name)) {
				$result = $wpdb->query("UPDATE $wpdb->golftable SET table_name = '$upd_name', description='$upd_desc' WHERE table_aid = '$act_table' ");
				if ($result) { $text = '<font color="green">'.__('Update Successfully','wpTable').'</font>';	}
			}
			$rowcount = $wpdb->get_results("SELECT row_id FROM $wpdb->golfresult WHERE table_id = '$act_table' GROUP BY row_id ");	
			if (is_array($rowcount)) {
				foreach ($rowcount as $rowcount){
					if ($rowcount->row_id > 0) {
						$getrow = $wpdb->get_results("SELECT result_aid FROM $wpdb->golfresult WHERE table_id = '$act_table' AND row_id = '$rowcount->row_id' ");
						foreach ($getrow as $getrow){			
							$row_value = addslashes(trim($_POST['row_aid-'.$getrow->result_aid]));
							$result = $wpdb->query("UPDATE $wpdb->golfresult SET value = '$row_value' WHERE result_aid = $getrow->result_aid ");
							if ($result) {	$text = '<font color="green">'.__('Update Successfully','wpTable').'</font>';	}
						}
					}
				}
			}
			if(empty($text)) {	$text = '<font color="blue">'.__('No Update needed','wpTable').'</font>';	}
			break;

		case 2: // CANCEL
			$mode =''; // go back to main page
			break;

		case 3: // Add a row

			$max_col = maxcolumn($act_table);
			$newrow_id = $wpdb->get_var("SELECT MAX(row_id) FROM $wpdb->golfresult WHERE table_id = '$act_table' ") + 1;	
			for ($i=1; $i <= $max_col; $i++)	{
				$result = $wpdb->query(" INSERT INTO $wpdb->golfresult (table_id, row_id) VALUES ('$act_table', '$newrow_id')");
				if (!$result) {	$text = '<font color="red">'.__('Database error. Could not perfom action.','wpTable').'</font>';	}	
			}			
			if(empty($text)) {	$text = '<font color="green">'.__('New row successfully added','wpTable').'</font>';	}
			break;

		case 4: // Add a column
			$row_ids = $wpdb->get_results("SELECT row_id FROM $wpdb->golfresult WHERE table_id = '$act_table' GROUP BY row_id ");	
			if (is_array($row_ids)) {	
				foreach ($row_ids as $row_ids){
				 	if ($row_ids->row_id ==0) {
					$result = $wpdb->query(" INSERT INTO $wpdb->golfresult (table_id, row_id, value) VALUES ('$act_table', '$row_ids->row_id', '30')");					
					} else {
					$result = $wpdb->query(" INSERT INTO $wpdb->golfresult (table_id, row_id) VALUES ('$act_table', '$row_ids->row_id')");
					}
					if (!$result) {	$text = '<font color="red">'.__('Database error. Could not perfom action.','wpTable').'</font>';	}	
				}
				if(empty($text)) {	$text = '<font color="green">'.__('New column successfully added','wpTable').'</font>';	}
			}
			if(empty($text)) {	$text = '<font color="red">'.__('Database error. Could not perfom action.','wpTable').'</font>';	}
			break;

		case 5: // Delete last column
			$row_ids = $wpdb->get_results("SELECT row_id FROM $wpdb->golfresult WHERE table_id = '$act_table' GROUP BY row_id ");	
			if (is_array($row_ids)) {	
				foreach ($row_ids as $row_ids){
					$maxrow_id = $wpdb->get_var("SELECT MAX(result_aid) FROM $wpdb->golfresult WHERE table_id = '$act_table' AND row_id ='$row_ids->row_id'");	
					$result = $wpdb->query("DELETE FROM $wpdb->golfresult WHERE result_aid = '$maxrow_id' ");
					if (!$result) {	$text = '<font color="red">'.__('Database error. Could not perfom action.','wpTable').'</font>';	}	
				}
				if(empty($text)) {	$text = '<font color="green">'.__('Last column successfully deleted','wpTable').'</font>';	}
			}
			if(empty($text)) {	$text = '<font color="red">'.__('Database error. Could not perfom action.','wpTable').'</font>';	}
			break;
			
		case 6: // Update option
			$act_bordercol = addslashes(trim($_POST['act_bordercol']));
			$act_headcol = addslashes(trim($_POST['act_headcol']));
			$act_alt_col = addslashes(trim($_POST['act_alt_col']));
			$act_altnativ = addslashes(trim($_POST['act_altnativ']));
			$act_sh_name = addslashes(trim($_POST['act_sh_name']));
			$act_sh_desc = addslashes(trim($_POST['act_sh_desc']));
			$act_head_b = addslashes(trim($_POST['act_head_b']));
			$act_bordersize = addslashes(trim($_POST['act_bordersize']));
			$act_cellspace = addslashes(trim($_POST['act_cellspace']));
			$act_cellpad = addslashes(trim($_POST['act_cellpad']));
			$act_table_align = addslashes(trim($_POST['act_align']));

			$result = $wpdb->query("UPDATE $wpdb->golftable SET border_color = '$act_bordercol', head_color = '$act_headcol', alt_color = '$act_alt_col', alternative = '$act_altnativ', show_name='$act_sh_name', show_desc='$act_sh_desc', head_bold='$act_head_b' , border_size='$act_bordersize' , cellspacing='$act_cellspace' , cellpadding='$act_cellpad' , table_align='$act_table_align' WHERE table_aid = '$act_table' ");
			if ($result) { $text = '<font color="green">'.__('Update Successfully','wpTable').'</font>';	}
			else { $text = '<font color="blue">'.__('No Update needed','wpTable').'</font>'; }
			break;

		case 7: // Update width
			
			// read the $_POST width values
			$getrow = $wpdb->get_results("SELECT result_aid FROM $wpdb->golfresult WHERE table_id = '$act_table' AND row_id = '0' ");
			if (is_array($getrow)) {
				foreach ($getrow as $getrow){			
					$row_value = addslashes(trim($_POST['width_aid-'.$getrow->result_aid]));
					$result = $wpdb->query("UPDATE $wpdb->golfresult SET value = '$row_value' WHERE result_aid = $getrow->result_aid ");
					if ($result) {	$text = '<font color="green">'.__('Update Successfully','wpTable').'</font>';	}
				}
			}
			
			// read the $_POST align values
			$getrow = $wpdb->get_results("SELECT result_aid FROM $wpdb->golfresult WHERE table_id = '$act_table' AND row_id = '-1' ");
			if (is_array($getrow)) {
				foreach ($getrow as $getrow){			
					$row_value = strtoupper(addslashes(trim($_POST['align_aid-'.$getrow->result_aid])));
					$result = $wpdb->query("UPDATE $wpdb->golfresult SET value = '$row_value' WHERE result_aid = $getrow->result_aid ");
					if ($result) {	$text = '<font color="green">'.__('Update Successfully','wpTable').'</font>';	}
				}
			}
			
			if(empty($text)) {	$text = '<font color="blue">'.__('No Update needed','wpTable').'</font>';	}
			$mode ='width'; // show again width menu
			break;
			
		case 8: // Cancel width menu
			break;	
	}
}

### Determines Which Mode It Is

if ($mode == 'width'){
	// edit table width

	?>
	<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
	<!-- Edit Table -->
	<div class="wrap">
		<h2><?php _e('Table width control', 'wpTable') ?></h2>
		<p><?php _e('Here you can edit the column width of the selected table.', 'wpTable') ?><br />
		<form name="table_options" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post" id="table_options">
			<fieldset class="options">
					<table align="center" cellpadding="1" cellspacing="0">
					<tr>
						<?php 
							$max_col = maxcolumn($act_table);
							$i = "A";
	 						for ($a=0; $a < $max_col; $a++)	{
								 echo "\t\t\t\t\t\t<th>".$i."</th>\n";
								 $i++;
							}
						?>
					</tr>
					<tr>
						<?php 
							$getrow = $wpdb->get_results("SELECT result_aid, value FROM $wpdb->golfresult WHERE table_id = '$act_table' AND row_id = '0' ORDER BY result_aid ASC ");
							if (is_array($getrow)) {
							 	$i = 0;
								foreach ($getrow as $getrow) {
								 	$act_width[$i] = $getrow->value;
									echo "\n\t<td align=\"center\"><input type=\"text\" maxlength=\"200\" name=\"width_aid-".$getrow->result_aid."\" value=\"".$act_width[$i++]."\"size=\"3\"></td>";
								}
							}
						// v1.10 align control addded
						echo "\n\t</tr><tr>\n\t<td align=\"center\" colspan=\"".$max_col."\">".__('Align control : Enter <strong>L</strong>=Left <strong>C</strong>=Center <strong>R</strong>=Right', 'wpTable')."</td>\n\t</tr><tr>";
						$getrow = $wpdb->get_results("SELECT result_aid, value FROM $wpdb->golfresult WHERE table_id = '$act_table' AND row_id = '-1' ORDER BY result_aid ASC ");
							if (is_array($getrow)) {
							 	$i = 0;
								foreach ($getrow as $getrow) {
								 	$act_align[$i] = $getrow->value;
									echo "\n\t<td align=\"center\"><input type=\"text\" maxlength=\"1\" name=\"align_aid-".$getrow->result_aid."\" value=\"".$act_align[$i++]."\"size=\"1\"></td>";
								}
							}
						echo "\n";
						?>
					</tr>
				</table>
			</fieldset class="options">
			<table width="100%" border="0" >
			<tr><td align="center" colspan="1">
				<input type="submit" name="do[7]" value="<?php _e('Update','wpTable'); ?>" class="button">
				<input type="submit" name="do[8]" value="<?php _e('Cancel','wpTable'); ?>" class="button">
			</td></tr>
			</table>
			<br />
		</form>	
		</div>
		
<!-- Table preview-->
		<div class="wrap">
		<fieldset class="options">
		<h2><?php _e('Table preview', 'wpTable') ?></h2>	
		<p><?php _e('Please note this is not a WYSIWYG mode of the table, the CSS of your theme could show the table in a slightly different way.', 'wpTable');?></b>
		<br /><br />
		<table border="1" align="center" cellspacing="1">
		<?php
			$rowcount = array();
			$tbl_content = '';
			$tbl_header = '';
			$max_col = maxcolumn($act_table);

			$rowcount = $wpdb->get_results("SELECT row_id FROM $wpdb->golfresult WHERE table_id = '$act_table' GROUP BY row_id ");	
			$count_row = 0;
			if (is_array($rowcount)) {
			 	$count_col = 0;
				foreach ($rowcount as $rowcount){
				 	if ($rowcount->row_id > 0) {
					 	$row_content= "\t".'<tr>'."\n\t";
						$getrow = $wpdb->get_results("SELECT value FROM $wpdb->golfresult WHERE table_id = '$act_table' AND row_id ='$rowcount->row_id' ORDER BY result_aid ASC ");		 	
						$i = 0;
						foreach ($getrow as $getrow){			
							$count_col++;
							if(!empty($getrow->value)){
							 	$align_value = convertalign($act_align[$i]);
							 	$row_content=$row_content."\t".'<td width="'.$act_width[$i++].'" ><div '.$align_value.'>'.stripslashes($getrow->value).'</div></td>'."\n\t";
							} else { $row_content=$row_content."\t".'<td width="'.$act_width[$i++].'" >&nbsp;</td>'."\n\t";	}
						}
					if ($count_col < $max_col) { 	// fill up with spaces with below max column
					 	for ( ; $count_col < $max_col ; $count_col++){
							$row_content=$row_content."\t".'<td>&nbsp;</td>'."\n\t";					
						}
					}
					$count_row++;
					$tbl_content=$tbl_content.$row_content.'</tr>'."\n"; // finish row
				}
			}
			$tbl_content= $tbl_header.$tbl_content.'</table>'."\n"; // finish table
			}
			echo $tbl_content;
			// preview ende
			
			?>
			</fieldset>	
	</div>
<?php	
}

if ($mode == 'delrow'){
 	// delete the row
 	$row_id = $_GET['rowid'];

	$delete_row = $wpdb->query("DELETE FROM $wpdb->golfresult WHERE table_id = $act_table AND row_id = '$row_id'");
	
	if(!$delete_row) {
		$text .= '<font color="red">'.__('Error in deleting row for table','wpTable').' \''.stripslashes($act_table).'\'</font>';
	} 
	if(empty($text)) {
		$text = '<font color="green">'.__('Row','wpTable').' \''.stripslashes($act_table).'\' '.__('deleted successfully','wpTable').'</font>';
	}
	
	$mode = 'edit'; // go to edit output
}

if ($mode == 'edit'){
	// edit table

	$align_left = '';
	$align_center = '';
	$align_right = '';

	$act_tableset = $wpdb->get_results("SELECT * FROM $wpdb->golftable WHERE table_aid = $act_table ");
	$act_tablename = htmlspecialchars(stripslashes($act_tableset[0]->table_name));
	$act_description = htmlspecialchars(stripslashes($act_tableset[0]->description));
	$act_bordercol = stripslashes($act_tableset[0]->border_color);
	$act_headcol = stripslashes($act_tableset[0]->head_color);
	$act_alt_col = stripslashes($act_tableset[0]->alt_color);
	$act_bordersize = stripslashes($act_tableset[0]->border_size);
	$act_cellspace = stripslashes($act_tableset[0]->cellspacing);
	$act_cellpad = stripslashes($act_tableset[0]->cellpadding);
	$act_sh_desc = stripslashes($act_tableset[0]->show_desc);
	
	$act_table_align = $act_tableset[0]->table_align;
	
	if ($act_table_align == "left") $align_left = 'selected="selected"';
	if ($act_table_align == "center") $align_center = 'selected="selected"';
	if ($act_table_align == "right") $align_right = 'selected="selected"';
	
	if ($act_tableset[0]->alternative) $act_altnativ='checked="checked"';	
	if ($act_tableset[0]->show_name) $act_sh_name='checked="checked"';
	if ($act_tableset[0]->show_desc) $act_sh_desc='checked="checked"';
	if ($act_tableset[0]->head_bold) $act_head_b='checked="checked"';
	
	?>
	<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
	<!-- Edit Table -->
	<div class="wrap">
		<h2><?php _e('Table', 'wpTable') ?></h2>
		<p><?php _e('Here you can edit the selected table. It\'s possible to add or delete the last column.', 'wpTable') ?><br />
		<?php _e('If you want to show this table in your page, enter the tag :', 'wpTable') ?><strong> [TABLE=<?php echo $act_table; ?>]</strong></p>
		<form name="table_options" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post" id="table_options">
			<fieldset class="options">
				<legend><?php _e('Edit table', 'wpTable');?></legend>
				<br />
					<table align="center" width="100%"  cellpadding="3" cellspacing="3">
					<tr>
						<td><strong><?php _e('Title or Name :', 'wpTable') ?> </strong><br /> <?php echo "<textarea cols=\"50\" rows=\"3\" maxlength=\"200\" name=\"tabelname\"/>".$act_tablename."</textarea></td>"; ?> </td>
						<td><strong><?php _e('Description :', 'wpTable') ?> </strong> <br /><?php echo "<textarea cols=\"50\" rows=\"3\" maxlength=\"200\" name=\"description\"/>".$act_description."</textarea></td>"; ?> </td>
					</tr>
					</table>
					<br /><br />
					<table align="center" cellpadding="1" cellspacing="1">
					<tr>
						<th>&nbsp;</th>					
						<?php 
							$max_col = maxcolumn($act_table);
	 						$i = "A";
							for ($a=0; $a < $max_col; $a++)	{
								 echo "\t\t\t\t\t\t<th>".$i."</th>\n";
								 $i++;
							}
						?>
					</tr>
						<?php 

						$exist_width = tablewidth($act_table);  // check if width entries exist
						if (!is_array($exist_width)) {
							for ($a=0; $a < $max_col; $a++)	{
							 	$insert_width = $wpdb->query(" INSERT INTO $wpdb->golfresult (table_id, row_id, value) VALUES ('$act_table', '0', '30')");	
							}
						}
						$exist_width = tablealign($act_table);  // check if align entries exist
						if (!is_array($exist_width)) {
							for ($a=0; $a < $max_col; $a++)	{
							 	$insert_width = $wpdb->query(" INSERT INTO $wpdb->golfresult (table_id, row_id, value) VALUES ('$act_table', '-1', 'C')");	
							}
						}
						$rowcount = $wpdb->get_results("SELECT row_id FROM $wpdb->golfresult WHERE table_id = '$act_table' GROUP BY row_id ");	
						if (is_array($rowcount)) {
						 	$width = array();
						 	$a = 1; // Count row
							foreach ($rowcount as $rowcount){
								if ($rowcount->row_id > 0) {
								 	echo "\n\t<tr>\n<th>".$a++."</th>";
									$getrow = $wpdb->get_results("SELECT result_aid, value FROM $wpdb->golfresult WHERE table_id = '$act_table' AND row_id = '$rowcount->row_id' ORDER BY result_aid ASC ");
									if (is_array($getrow)) {
									$i = 0;
										foreach ($getrow as $getrow) {
										 	$fieldsize = intval( $width[$i++] /10 );
											if ($fieldsize < 1 ) { $fieldsize = 1; } 			
											$row_value = htmlspecialchars(stripslashes($getrow->value));
											$row_aid = $getrow->result_aid;
											echo "\n\t<td><input type=\"text\" maxlength=\"200\" name=\"row_aid-$row_aid\" value=\"$row_value\"size=\"".$fieldsize."\"></td>";
											}
										echo "\n\t<td><a href=\"$base_page&amp;mode=delrow&amp;id=$act_table&amp;rowid=$rowcount->row_id\" class=\"delete\" onclick=\"javascript:check=confirm( '".__("Delete this row ?",'wpTable')."');if(check==false) return false;\">".__('Delete row','wpTable')."</a></td>\n";
									}
								echo "\n</tr>\n";
								} else {
									$tbl_width = "\n\t<tr>\n<th><code>".__('Width', 'wpTable')."</code></th>";
									$getrow = $wpdb->get_results("SELECT result_aid, value FROM $wpdb->golfresult WHERE table_id = '$act_table' AND row_id = '$rowcount->row_id' ORDER BY result_aid ASC ");
									if (is_array($getrow)) {
									 	$i = 0;
										foreach ($getrow as $getrow) {			
											$width[$i] = $getrow->value;
											$tbl_width = $tbl_width."\n\t<td align=\"center\" ><code><".$width[$i++]."></code></td>";
											}
										$tbl_width = $tbl_width. "\n\t<td><a href=\"$base_page&amp;mode=width&amp;id=$act_table&amp;rowid=$rowcount->row_id\" class=\"edit\" >".__('Edit width','wpTable')."</a></td>\n";	
									}
								$tbl_width = $tbl_width. "\n</tr>\n";
								}
							}
						}
						echo $tbl_width;
					    ?>
					</table>
					<br />
			</fieldset>
			<table width="100%"  border="0" >
			<tr><td align="center" colspan="1">
				<input type="submit" name="do[3]" value="<?php _e('Add new row','wpTable'); ?>" class="button">
				<input type="submit" name="do[4]" value="<?php _e('Add new column','wpTable'); ?>" class="button">
				<input type="submit" name="do[5]" value="<?php _e('Delete last column','wpTable'); ?>" class="button" onclick="javascript:check=confirm('<?php _e('Delete last column ?','wpTable'); ?>');if(check==false) return false;">
			</td></tr>
			<tr><td align="center" colspan="1">
				<input type="submit" name="do[1]" value="<?php _e('Update','wpTable'); ?>" class="button">
				<input type="submit" name="do[0]" value="<?php _e('Save and go back','wpTable'); ?>" class="button">
				<input type="submit" name="do[2]" value="<?php _e('Cancel','wpTable'); ?>" class="button">
			</td></tr>
			</table>
			<br />
		</form>
	</div>
	<!-- Option -->
	<div class="wrap">
		<h2><?php _e('Option','wpTable'); ?></h2>
			<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>&amp;mode=option" method="post">
				<fieldset class="options"> 
				<table border="0" cellspacing="3" cellpadding="3">
					<tr>
						<td align="left"><?php _e('Border Color','wpTable') ?></td>
						<td><input type="text" size="7" maxlength="7" name="act_bordercol" value="<?php echo "$act_bordercol" ?>" /></td>
						<td align="left"><i><?php _e('Enter the HTML color code (i.e. #E58802 for the border line)','wpTable') ?></i></td>
					</tr>
					<tr>					
						<td align="left"><?php _e('Headline Color','wpTable') ?></td>
						<td><input type="text" size="7" maxlength="7" name="act_headcol" value="<?php echo "$act_headcol" ?>" /></td>
						<td align="left"><i><?php _e('Select a background color for the first row','wpTable') ?></i></td>
					</tr>
					<tr>					
						<td align="left"><?php _e('Alternative Color','wpTable') ?></td>
						<td><input type="text" size="7" maxlength="7" name="act_alt_col" value="<?php echo "$act_alt_col" ?>" /></td>
						<td align="left"><i><?php _e('Select the alternating color (i.e. #F4F4EC)','wpTable') ?></i></td>
					</tr>
					<tr>					
						<td align="left"><?php _e('Table alignment','wpTable') ?></td>
						<td><select size="1" name="act_align">
						<option <?php echo "$align_left" ?> value="left"><?php _e('Left','wpTable') ?></option>
						<option <?php echo "$align_center" ?> value="center"><?php _e('Center','wpTable') ?></option>
						<option <?php echo "$align_right" ?> value="right"><?php _e('Right','wpTable') ?></option>
						</select></td>
						<td align="left"><i><?php _e('Alignment of table to surrounding text','wpTable') ?></i></td>
					</tr>
					<tr>					
						<td align="left"><?php _e('Border Size','wpTable') ?></td>
						<td><input type="text" size="3" maxlength="3" name="act_bordersize" value="<?php echo "$act_bordersize" ?>" /></td>
						<td align="left"><i><?php _e('Size of border around the table (0 = no border)','wpTable') ?></i></td>
					</tr>
					<tr>					
						<td align="left"><?php _e('Cell spacing','wpTable') ?></td>
						<td><input type="text" size="3" maxlength="3" name="act_cellspace" value="<?php echo "$act_cellspace" ?>" /></td>
						<td align="left"><i><?php _e('Space between cells (Default = 1)','wpTable') ?></i></td>
					</tr>
					<tr>					
						<td align="left"><?php _e('Cell padding','wpTable') ?></td>
						<td><input type="text" size="3" maxlength="3" name="act_cellpad" value="<?php echo "$act_cellpad" ?>" /></td>
						<td align="left"><i><?php _e('Space between the edge of a cell and the contents (Default = 0)','wpTable') ?></i></td>
					</tr>
					<tr>
						<td align="left"><?php _e('Use alternating color','wpTable') ?></td>
						<td><input name="act_altnativ" type="checkbox" value="1"  <?php echo "$act_altnativ" ?> /></td> 
						<td align="left"><i><?php _e('This option will show every second row in the alternativ color','wpTable') ?></i></td>
					</tr>
					<tr>
						<td align="left"><?php _e('Show table name as title','wpTable') ?></td>
						<td><input name="act_sh_name" type="checkbox" value="1"  <?php echo "$act_sh_name" ?> /></td> 
						<td align="left"><i><?php _e('The table name will be shown as [h2] headline','wpTable') ?></i></td>
					</tr>
					<tr>
						<td align="left"><?php _e('Show description','wpTable') ?></td>
						<td><input name="act_sh_desc" type="checkbox" value="1"  <?php echo "$act_sh_desc" ?> /></td>
						<td align="left"><i><?php _e('The description will be shown','wpTable') ?></i></td> 
					</tr>
					<tr>
						<td align="left"><?php _e('Show first row in bold','wpTable') ?></td>
						<td><input name="act_head_b" type="checkbox"  value="1"  <?php echo "$act_head_b" ?> /></td> 
						<td align="left"><i><?php _e('The first row get the tag [strong]','wpTable') ?></i></td>
					</tr>
					</table>
						<div class="submit"><input type="submit" name="do[6]" value="<?php _e('Update','wpTable'); ?>" class="button" /></div>
					</fieldset>
			</form>
	</div>
	<?php	
}
		
if ($mode == 'delete'){	  
 	// Delete A Poll Answer

	$delete_entries = $wpdb->query("DELETE FROM $wpdb->golfresult WHERE table_id = $act_table");
	$delete_table =  $wpdb->query("DELETE FROM $wpdb->golftable WHERE table_aid = $act_table");
	
	if(!$delete_table) {
	 	$text = '<font color="red">'.__('Error in deleting table','wpTable').' \''.stripslashes($act_table).'\' </font>';
	}
	if(!$delete_entries) {
		$text .= '<br /><font color="red">'.__('Error in deleting entries for table','wpTable').' \''.stripslashes($act_table).'\'</font>';
	} 
	if(empty($text)) {
		$text = '<font color="green">'.__('Table','wpTable').' \''.stripslashes($act_table).'\' '.__('deleted successfully','wpTable').'</font>';
	}
	$mode = 'main'; // show main page
}
	
if ($mode == 'add'){		
 	// add table
	$new_table = addslashes(trim($_POST['table_name']));
	$column_num = $_POST['column_num'];


	if(!empty($new_table)) {
		$metaname = $wpdb->escape($new_table);
		$insert_table = $wpdb->query(" INSERT INTO $wpdb->golftable (table_name) VALUES ('$metaname')");
		if ($insert_table != 0) {
		 	$table_aid = $wpdb->get_var("SELECT table_aid FROM $wpdb->golftable WHERE table_name = '$new_table' ");
			$text = '<font color="green">'.__('Table ','wpTable').$table_aid.__(' added successfully','wpTable').'</font>';
		 	for ($a=1; $a <= $column_num; $a++)	
		 		{
					$insert_row = $insert_row + $wpdb->query(" INSERT INTO $wpdb->golfresult (table_id, row_id) VALUES ('$table_aid', 1)");	
				}
			if ($insert_row == $column_num) {
				$text = $text.'<br /><font color="green"> '.$column_num.__(' columns added successfully','wpTable').'</font>';	
			}
		}
		else { $text = '<font color="red">'.__('Error : Table cannot insert to database','wpTable').'</font>'; }
	}
	else { $text = '<font color="red">'.__('Error : You need to enter a table name','wpTable').'</font>'; }
	$mode = 'main'; // show main page
}

/*** MAIN ADMIN PAGE ***/	
if ((empty($mode)) or ($mode == 'main')) {

	$tables = $wpdb->get_results("SELECT * FROM $wpdb->golftable ORDER BY 'table_aid' ASC ");
	?>
	<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
		<!-- Manage Polls -->
		<div class="wrap">
		<h2><?php _e('Manage Table','wpTable'); ?></h2>
			<table width="100%"  border="0" cellspacing="3" cellpadding="3">
			<tr>
				<th scope="col"><?php _e('ID','wpTable'); ?></th>
				<th scope="col"><?php _e('Table Name','wpTable'); ?></th>				
				<th scope="col"><?php _e('Description','wpTable'); ?></th>
				<th scope="col" colspan="2"><?php _e('Action','wpTable'); ?></th>
			</tr>
			<?php
				if($tables) {
					$i = 0;
					foreach($tables as $table) {
					 	if($i%2 == 0) {
							echo "<tr class='alternate'>\n";
						}  else {
							echo "<tr>\n";
						}
						echo "<th scope=\"row\">$table->table_aid</th>\n";
						echo "<td>$table->table_name</td>\n";
						echo "<td>$table->description</td>\n";
						echo "<td><a href=\"$base_page&amp;mode=edit&amp;id=$table->table_aid\" class=\"edit\">".__('Edit','wpTable')."</a></td>\n";
						echo "<td><a href=\"$base_page&amp;mode=delete&amp;id=$table->table_aid\" class=\"delete\" onclick=\"javascript:check=confirm( '".__("The complete table and content will be erased. Delete?",'wpTable')."');if(check==false) return false;\">".__('Delete')."</a></td>\n";
						echo '</tr>';
						$i++;
					}
				} else {
					echo '<tr><td colspan="7" align="center"><b>'.__('No table found','wpTable').'</b></td></tr>';
				}
			?>
			</table>
		</div>
		<!-- Add A Poll -->
		<div class="wrap">
			<h2><?php _e('Add a new table','wpTable'); ?></h2>
			<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>&amp;mode=add" method="post">
				<table width="100%"  border="0" cellspacing="3" cellpadding="3">
					<tr>
						<th align="left"><?php _e('Table name','wpTable') ?></th>
						<td><input type="text" size="50" maxlength="200" name="table_name" /></td>
					</tr>
					<tr>
						<th align="left"><?php _e('No. of columns:','wpTable') ?></th>
							<td>
								<select size="1" name="column_num">
										<?php
										for($k=2; $k <= 20; $k++) {
											echo "<option value=\"$k\">$k</option>";
										}
										?>
								</select>
							</td>
						</tr>
					<tr>
						<td colspan="2" align="center"><input type="submit" name="addtable" value="<?php _e('Add table','wpTable'); ?>" class="button" /></td>
					</tr>
				</table>
			</form>
		</div>
	<?php
}
?>