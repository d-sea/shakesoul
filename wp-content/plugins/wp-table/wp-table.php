<?php

/*
Plugin Name: wp-Table
Plugin URI: http://alexrabe.boelinger.com/?page_id=3
Description: This plugin is a simple table manager. I didn't find anything in the web which creates the same result, for my purpose.
Author: Alex Rabe
Version: 1.11
Author URI: http://alexrabe.boelinger.com/

Copyright 2006  Alex Rabe (email : alex.rabe@lycos.de)

THX to the plugin's from Thomas Boley (myGallery) and GaMerZ (WP-Polls),
which gives me a lot of education.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/  

// Load language
load_plugin_textdomain('wpTable','wp-content/plugins/wp-table');

$wpdb->golftable					= $table_prefix . 'golftable';
$wpdb->golfresult					= $table_prefix . 'golfresult';


// Insert table menu
function add_option_menu() {
	if (function_exists('add_submenu_page')) {
		add_submenu_page( 'edit.php' , __('Tables','wpTable'),  __('Tables','wpTable'), 9            , 'wp-table/wp-table-admin.php');
//		add_submenu_page(  parent    , page_title    , menu_title     , access_level , file, [function]);
	}
}

// ### Serach for [TABLE=X] in Content
function golftable ($content) {
global $wpdb;
 
$search = "/\[TABLE=([A-Za-z0-9\-\_]+)(|\([0-9\,]+\))\]/";   //search for 'table' entry

preg_match_all($search, $content, $matches);

if (is_array($matches[1])) {
	foreach ($matches[1] as $content_id) {
		$search = "/\[TABLE=".$content_id."\]/";
		
		$dbresult = $wpdb->get_results("SELECT * FROM $wpdb->golftable WHERE table_aid = '$content_id'");
		if ($dbresult) {
			$replace = replacetable($dbresult[0]->table_aid);
			$content = preg_replace ($search, $replace, $content);
		}
	}
}

return $content;
}

// ### Lookup for table content
function replacetable($table_id) {
global $wpdb;

	// get table data
	$act_tableset = $wpdb->get_results("SELECT * FROM $wpdb->golftable WHERE table_aid = $table_id ");
	$act_tablename = $act_tableset[0]->table_name;
	$act_description = $act_tableset[0]->description;
	$act_headcol = $act_tableset[0]->head_color;
	$act_alt_col = $act_tableset[0]->alt_color;
	$act_altnativ =$act_tableset[0]->alternative;
	$act_sh_name = $act_tableset[0]->show_name;
	$act_sh_desc = $act_tableset[0]->show_desc;
	$act_head_b = $act_tableset[0]->head_bold;
	$act_bordercol = $act_tableset[0]->border_color;
	$act_table_align = $act_tableset[0]->table_align;
	if ($act_bordercol == "") {	$act_bordercol =""; }	
	else { $act_bordercol = ' bordercolor="'.$act_bordercol.'"'; }
	$act_tablewidth = $act_tableset[0]->table_width;	
	if ($act_tablewidth == 0) {	$act_tablewidth =""; }	
	else { $act_tablewidth = ' width="'.$act_tablewidth.'"'; }
	$act_bordersize = $act_tableset[0]->border_size;
	if ($act_bordersize == 0) {	$act_bordersize =""; }	
	else { $act_bordersize = ' border="'.$act_bordersize.'"'; }
	$act_cellspace = $act_tableset[0]->cellspacing;
	if ($act_cellspace == 0) {	$act_cellspace =""; }	
	else { $act_cellspace = ' cellspacing="'.$act_cellspace.'"'; }	
	$act_cellpad = $act_tableset[0]->cellpadding;
	if ($act_cellpad == 0) {	$act_cellpad =""; }	
	else { $act_cellpad = ' cellpadding="'.$act_cellpad.'"'; }
	
	if ($act_sh_name) { $tbl_header = "\n".'<p><h2>'.$act_tablename.'</h2></p>'; }
	if ($act_sh_desc) { $tbl_header = $tbl_header."\n".'<p>'.$act_description.'</p>'; }
	$tbl_header = $tbl_header."\n".'<table'.$act_tablewidth.$act_bordersize.$act_cellspace.$act_cellpad.' align="'.$act_table_align.'"'.$act_bordercol.'>'."\n";

	$rowcount=array();
	$max_col = maxcolumn($table_id);
	$act_width =tablewidth($table_id);
	$act_align =tablealign($table_id);

	$rowcount = $wpdb->get_results("SELECT row_id FROM $wpdb->golfresult WHERE table_id = '$table_id' GROUP BY row_id ORDER BY row_id ASC ");	
	$count_row = 0;
	if (is_array($rowcount)) {
	 	$count_col = 0;
		foreach ($rowcount as $rowcount){
		 	if ($rowcount->row_id > 0 ) {
		 	$row_content= "\t".'<tr>'."\n\t";
			$getrow = $wpdb->get_results("SELECT value FROM $wpdb->golfresult WHERE table_id = '$table_id' AND row_id ='$rowcount->row_id' ORDER BY result_aid ASC ");		 	
			if ( $count_row == 0 ){ $bgcolor = 'bgcolor="'.$act_headcol.'"';}
			else if (($count_row%2 == 0) AND ($count_row != 0) AND ($act_altnativ)) {
				$bgcolor = 'bgcolor="'.$act_alt_col.'"';
			}  else {
				$bgcolor = ''; 
			}
			$i = 0;
			foreach ($getrow as $getrow){			
				$count_col++;
				if(!empty($getrow->value)){
					if (($act_head_b) AND ($count_row == 0)) {
					 	$row_content=$row_content."\t".'<td width="'.$act_width[$i].'" '.$bgcolor.'><div '.$act_align[$i++].'><strong>'.stripslashes($getrow->value).'</strong></div></td>'."\n\t";
				 	} else {
					 	$row_content=$row_content."\t".'<td width="'.$act_width[$i].'" '.$bgcolor.'><div '.$act_align[$i++].'>'.stripslashes($getrow->value).'</div></td>'."\n\t";
					}
				} else { $row_content=$row_content."\t".'<td width="'.$act_width[$i++].'" '.$bgcolor.'>&nbsp;</td>'."\n\t";	}
			}
			if ($count_col < $max_col) { 	// fill up with spaces with below max column
			 	for ( ; $count_col < $max_col ; $count_col++){
					$row_content=$row_content."\t".'<td width="'.$act_width[$i++].'" '.$bgcolor.'>&nbsp;</td>'."\n\t";					
				}
			}
			$count_row++;
			$tbl_content=$tbl_content.$row_content.'</tr>'."\n"; // finish row
			}
		}
		$tbl_content= $tbl_header.$tbl_content.'</table>'."\n"; // finish table
	}
	return $tbl_content;
}

// ### Calculate the max number of column for this table
function maxcolumn($table_id){
global $wpdb;

$max_col = 0 ;

	$rowcount = $wpdb->get_results("SELECT row_id FROM $wpdb->golfresult WHERE table_id = '$table_id' GROUP BY row_id ");	
	if (is_array($rowcount)) {
		foreach ($rowcount as $rowcount){	
			$getrow = $wpdb->get_results("SELECT row_id FROM $wpdb->golfresult WHERE table_id = '$table_id' AND row_id ='$rowcount->row_id' ");		 	
			$col_num = count($getrow);
			if ($col_num > $max_col) {
				$max_col = $col_num; 
			}
		}
	}	
return 	$max_col;
}

// ### Get the width for each column in a array
function tablewidth($table_id){
global $wpdb;
 
	$widthcount = $wpdb->get_results("SELECT * FROM $wpdb->golfresult WHERE table_id = '$table_id' AND row_id ='0' ORDER BY result_aid ASC ");	
	if (is_array($widthcount)) {
	 	$i = 0;
		foreach ($widthcount as $widthcount){	
		 	$col_width[$i++] = $widthcount->value;
		}
	}
return 	$col_width;
}

// ### Get the align for each column in a array
function tablealign($table_id){
global $wpdb;
 
	$aligncount = $wpdb->get_results("SELECT * FROM $wpdb->golfresult WHERE table_id = '$table_id' AND row_id ='-1' ORDER BY result_aid ASC ");	
	if (is_array($aligncount)) {
	 	$i = 0;
		foreach ($aligncount as $aligncount){	
		 	$col_align[$i++] = convertalign($aligncount->value);
		}
	}
return 	$col_align;
}

// ### Convert the align shortcut into a html code
function convertalign($table_value){

if ($table_value == "C"){ $html_value = 'align="center"'; }
if ($table_value == "R"){ $html_value = 'align="right"';  } 
if ($table_value == "L"){ $html_value = 'align="left"';   }  

return 	$html_value;
}

// init wpTable in wp-database if plugin is activated
function checkdatabase() {

	require_once(ABSPATH . 'wp-content/plugins/wp-table/wp-table-install.php');
	wptable_install();
	
}

// Plugin activation
add_action('activate_wp-table/wp-table.php', 'checkdatabase');

// Action calls for all functions 
add_filter('the_content', 'golftable');

// Insert the mt_add_pages() sink into the plugin hook list for 'admin_menu'
add_action('admin_menu', 'add_option_menu');

?>