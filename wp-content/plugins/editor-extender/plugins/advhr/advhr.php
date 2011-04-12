<?php
if (!function_exists('add_action')) {
	require_once('../../../../../wp-config.php');
} //  end : if (!function_exists('add_action'))

$path_arr = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
$plugin = $path_arr[count($path_arr)-1];
$plugindir = $path_arr[count($path_arr)-2];
$pluginwpdir = $path_arr[count($path_arr)-3];

$blogsurl = get_bloginfo('wpurl') . '/wp-content/plugins/' . $pluginwpdir .'/'.$plugindir.'/'.$plugin;
echo "tinymce.PluginManager.load('advhr', \"{$blogsurl}/editor_plugin.js\");";
?>