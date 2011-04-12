<?php
/**
	Plugin Name: Editor Extender
	Plugin URI: http://benjaminsterling.com/wordpress-editor-extender/
	Description: Extends the features of the rich text editor for Wordpress
	Author: Benjamin Sterling
	Version: 0.1
	Author URI: http://benjaminsterling.com

	Copyright 2008 by Benjamin Sterling

	
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

/**
 *	Load WP-Config File If This File Is Called Directly
 */
if (!function_exists('add_action')) {
	require_once('../../../wp-config.php');
} //  end : if (!function_exists('add_action'))

// grab the blogs url once
$blogsurl = get_bloginfo('wpurl') . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/plugins';
$serverpath = dirname(__FILE__) . '\plugins';

global $ee_plugins, $ee_plugins_lang, $ee_buttons_3;
$ee_plugins = array();
$ee_buttons_3 = array();
$ee_buttons_4 = array();
$ee_plugins_lang = array();


if(get_option('ee_advhr')){
	$ee_plugins = array_merge($ee_plugins, array('advhr'  => $blogsurl . '/advhr/editor_plugin.js'));
	array_push($ee_buttons_3, 'advhr');
	$ee_plugins_lang = array_merge($ee_plugins_lang, array('advhr'  => $serverpath . '/advhr/langs/lang.php'));
}

if(get_option('ee_advimage')){
	$ee_plugins = array_merge($ee_plugins, array('advimage' => $blogsurl . '/advimage/editor_plugin.js'));
	array_push($ee_buttons_3, 'advimage');
	$ee_plugins_lang = array_merge($ee_plugins_lang, array('advimage' => $blogsurl . '/advimage/langs/lang.php'));
}

if(get_option('ee_advlink')){
	$ee_plugins = array_merge($ee_plugins, array('advlink' => $blogsurl . '/advlink/editor_plugin.js'));
	array_push($ee_buttons_3, 'advlink');
	$ee_plugins_lang = array_merge($ee_plugins_lang, array('advlink' => $blogsurl . '/advlink/langs/lang.php'));
}

if(get_option('ee_contextmenu')){
	$ee_plugins = array_merge($ee_plugins, array('contextmenu' => $blogsurl . '/contextmenu/editor_plugin.js'));
	array_push($ee_buttons_3, 'contextmenu');
}

if(get_option('ee_emotions')){
	$ee_plugins = array_merge($ee_plugins, array('emotions' => $blogsurl . '/emotions/editor_plugin.js'));
	array_push($ee_buttons_3, 'emotions');
}

if(get_option('ee_fullpage')){
	$ee_plugins = array_merge($ee_plugins, array('fullpage' => $blogsurl . '/fullpage/editor_plugin.js'));
	array_push($ee_buttons_3, 'fullpage');
	$ee_plugins_lang = array_merge($ee_plugins_lang, array('fullpage' => $blogsurl . '/fullpage/langs/lang.php'));
}

if(get_option('ee_iespell')){
	$ee_plugins = array_merge($ee_plugins, array('iespell' => $blogsurl . '/iespell/editor_plugin.js'));
	array_push($ee_buttons_3, 'iespell');
}

if(get_option('ee_insertdatetime')){
	$ee_plugins = array_merge($ee_plugins, array('insertdatetime' => $blogsurl . '/insertdatetime/editor_plugin.js'));
	array_push($ee_buttons_3, 'insertdatetime');
	array_push($ee_buttons_3, 'insertdate');
	array_push($ee_buttons_3, 'inserttime');
}

if(get_option('ee_noneditable')){
	$ee_plugins = array_merge($ee_plugins, array('noneditable' => $blogsurl . '/noneditable/editor_plugin.js'));
	array_push($ee_buttons_3, 'noneditable');
}


if(get_option('ee_preview')){
	$ee_plugins = array_merge($ee_plugins, array('preview' => $blogsurl . '/preview/editor_plugin.js'));
	array_push($ee_buttons_3, 'preview');
}

if(get_option('ee_print')){
	$ee_plugins = array_merge($ee_plugins, array('print' => $blogsurl . '/print/editor_plugin.js'));
	array_push($ee_buttons_3, 'print');
}

if(get_option('ee_searchreplace')){
	$ee_plugins = array_merge($ee_plugins, array('searchreplace' => $blogsurl . '/searchreplace/editor_plugin.js'));
	array_push($ee_buttons_3, 'searchreplace,search,replace');
	$ee_plugins_lang = array_merge($ee_plugins_lang, array('searchreplace' => $blogsurl . '/searchreplace/langs/lang.php'));
}

if(get_option('ee_table')){
	$ee_plugins = array_merge($ee_plugins, array('table' => $blogsurl . '/table/editor_plugin.js'));
	array_push($ee_buttons_3, 'table');
	array_push($ee_buttons_3, 'tablecontrols');
	
	$ee_plugins_lang = array_merge($ee_plugins_lang, array('table' => $blogsurl . '/table/langs/lang.php'));
}

if(get_option('ee_visualchars')){
	$ee_plugins = array_merge($ee_plugins, array('visualchars' => $blogsurl . '/visualchars/editor_plugin.js'));
	array_push($ee_buttons_3, 'visualchars');
}

if(get_option('ee_layer')){
	$ee_plugins = array_merge($ee_plugins, array('layer' => $blogsurl . '/layer/editor_plugin.js'));
	array_push($ee_buttons_4, 'layer,insertlayer,moveforward,movebackward,absolute,|');
}

if(get_option('ee_nonbreaking')){
	$ee_plugins = array_merge($ee_plugins, array('nonbreaking' => $blogsurl . '/nonbreaking/editor_plugin.js'));
	array_push($ee_buttons_4, 'nonbreaking');
}

if(get_option('ee_style')){
	$ee_plugins = array_merge($ee_plugins, array('style' => $blogsurl . '/style/editor_plugin.js'));
	array_push($ee_buttons_4, 'style,styleprops,|');
	$ee_plugins_lang = array_merge($ee_plugins_lang, array('style' => $blogsurl . '/style/langs/lang.php'));
}

if(get_option('ee_xhtmlxtras')){
	$ee_plugins = array_merge($ee_plugins, array('xhtmlxtras' => $blogsurl . '/xhtmlxtras/editor_plugin.js'));
	array_push($ee_buttons_4, '|,xhtmlxtras,cite,abbr,acronym,del,ins,attribs|');
	$ee_plugins_lang = array_merge($ee_plugins_lang, array('xhtmlxtras' => $blogsurl . '/xhtmlxtras/langs/lang.php'));
}

function ee_mce_external_plugins($array = array()){
	global $ee_plugins;
	return array_merge($array, $ee_plugins);
}
add_filter('mce_external_plugins', 'ee_mce_external_plugins', 1, 1);


function ee_mce_external_languages($array = array()){
	global $ee_plugins_lang;
	return array_merge($array, $ee_plugins_lang);
}
add_filter('mce_external_languages', 'ee_mce_external_languages', 10, 1);


function ee_mce_buttons_3($array = array()){
	global $ee_buttons_3;
	return array_merge($array, $ee_buttons_3);
}
add_filter('mce_buttons_3', 'ee_mce_buttons_3', 1, 1);

function ee_mce_buttons_4($array = array()){
	global $ee_buttons_4;
	return array_merge($array, $ee_buttons_4);
}
add_filter('mce_buttons_4', 'ee_mce_buttons_4', 1, 1);

function ee_add_pages() {
    add_options_page('Editor Extender Options', 'Editor Extender', 8, __FILE__, 'ee_options_page');
}
add_action('admin_menu', 'ee_add_pages');

function ee_options_page() {
	include('editor-extender-form.php');
}
?>