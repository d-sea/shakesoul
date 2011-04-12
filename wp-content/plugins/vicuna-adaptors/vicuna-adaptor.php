<?php
/*
Plugin Name: Vicuna Adaptor
Plugin URI: http://ma38su.sourceforge.jp/wp/vicuna/ja/adaptor/
Description: Plugins and Scripts Adaptor of  for wp.Vicuna
Author: ma38su
Version: 0.11
Author URI: http://ma38su.sourceforge.jp/
*/

$options = get_option('vicuna_adaptor');
require_once(dirname(__FILE__).'/includes/admin_ui.php');
require_once(dirname(__FILE__).'/includes/hatena.php');

if ($options['fontsize-switcher']) {
	function add_fontsize_switcher() {
?>
	<script type="text/javascript" src="<?php bloginfo("wpurl"); ?>/wp-content/plugins/vicuna-adaptors/js/fontsize-switcher.js"></script>
<?php
	}
	add_action('wp_footer', add_fontsize_switcher);
	function fontsize_switcher_css() {
?>
@import url(<?php bloginfo("wpurl"); ?>/wp-content/plugins/vicuna-adaptors/css/fontsizeSwitcher.css);
<?php
	}
	add_action('vicuna_plugin_css', fontsize_switcher_css);
}

if ($options['delicious']) {
	function add_delicious_button() {
?>
	<li class="icon delicious"><a href="http://del.icio.us/post" onclick="window.open('http://del.icio.us/post?v=4&amp;noui&amp;jump=close&amp;url='+encodeURIComponent(location.href)+'&amp;title='+encodeURIComponent(document.title),'delicious', 'toolbar=no,width=700,height=400');return false;"><img src="<?php bloginfo("wpurl"); ?>/wp-content/plugins/vicuna-adaptors/images/mark/icon_delicious.gif" alt="save this page del.icio.us" /></a></li>
<?php
	}
	add_action('entry_info', add_delicious_button);
	add_action('single_entry_info', add_delicious_button);
	add_action('page_entry_info', add_delicious_button);
}

function icon_css() {
?>
@import url(<?php bloginfo("wpurl"); ?>/wp-content/plugins/vicuna-adaptors/css/icon.css);
<?php
}
add_action('vicuna_plugin_css', icon_css);


add_action('init', plugins_opt);
function plugins_opt() {
	if(function_exists('wp_pagenavi')) {
		remove_action('entries_footer', vicuna_paging_link);
		add_action('entries_footer', wp_pagenavi);
	}
}
?>
