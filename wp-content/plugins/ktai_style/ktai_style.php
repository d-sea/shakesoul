<?php
/*
Plugin Name: Ktai Style
Plugin URI: http://wppluginsj.sourceforge.jp/ktai_style/
Description: Provides lightweight pages and admin interfaces for mobile phones.
Version: 1.44.1
Author: IKEDA Yuriko
Author URI: http://www.yuriko.net/cat/wordpress/
*/
define ('KS_VERSION', '1.44');

/*  Copyright (c) 2007-2008 yuriko

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if (defined('WP_INSTALLING') && WP_INSTALLING) {
	return;
}
define ('KS_ADMIN_DIR', 'admin');
define ('KS_BUILT_IN_THEMES_DIR', 'themes');
define ('KS_USER_THEMES_DIR', 'ktai-themes');
define ('KS_COOKIE_PCVIEW', 'ks_pc_view');

global $Ktai_Style;
$Ktai_Style = new Ktai_Style;

/* ==================================================
 * @param	none
 * @return	string  $is_ktai
 */
if (! function_exists('is_ktai')) :
function is_ktai($attribute = NULL) {
	global $Ktai_Style;
	switch ($attribute) {
	case 'type':
		return isset($Ktai_Style->ktai) ? $Ktai_Style->ktai->get('type') : false;
	case 'flat_rate':
		return isset($Ktai_Style->ktai) ? $Ktai_Style->ktai->get('flat_rate') : false;
	default:
		return $Ktai_Style->is_ktai();
	}
}
endif;

/* ==================================================
 * @param	string  $name
 * @return	mix     $value
 */
function ks_option($name) {
	return Ktai_Style::get_option($name);
}

/* ==================================================
 *   Ktai_Style class
   ================================================== */

class Ktai_Style {
	private $plugin_dir;
	private $plugin_url;
	private $theme;
	private $theme_root;
	private $theme_root_uri;
	private $template_dir;
	public  $ktai;

/* ==================================================
 * @param	none
 * @return	object  $this
 */
public function __construct() {
	$this->set_plugin_dir();
	$this->load_textdomain('ktai_style', 'lang');
	require dirname(__FILE__) . '/operators/services.php';
	$this->ktai = Ktai_Services::factory();
	$admin_dir = dirname(__FILE__) . '/' . KS_ADMIN_DIR;
	if (isset($this->ktai) && $this->ktai->get('operator') == 'Unknown' && isset($_GET['pcview'])) {
		setcookie(KS_COOKIE_PCVIEW, ($_GET['pcview'] == 'true'), 0, COOKIEPATH, COOKIE_DOMAIN);
		setcookie(KS_COOKIE_PCVIEW, ($_GET['pcview'] == 'true'), 0, SITECOOKIEPATH, COOKIE_DOMAIN);
		$location = remove_query_arg('pcview', $_SERVER['REQUEST_URI']);
		$this->redirect($location);
		exit;
	}
	global $allowedposttags, $allowedtags;
	if ($allowedposttags) {
		$allowedposttags['img']['localsrc'] = array();
		$allowedposttags['img']['alt'] = array();
	}
	if ($allowedtags) {
		$allowedtags['img']['localsrc'] = array();
		$allowedtags['img']['alt'] = array();
	}
	if ($this->is_ktai()) {
		require dirname(__FILE__) . '/patches.php';
		$this->init_mobile($admin_dir);
		do_action('init_mobile/ktai_style.php');
	} else {
		$this->init_pc($admin_dir);
		do_action('init_pc/ktai_style.php');
	}
}

/* ==================================================
 * @param	none
 * @return	none
 */
private function set_plugin_dir() {
	$this->plugin_dir = basename(dirname(__FILE__));
	if (defined('WP_PLUGIN_URL')) {
		$url = WP_PLUGIN_URL . '/';
	} else {
		$url = get_bloginfo('wpurl') . '/' . (defined('PLUGINDIR') ? PLUGINDIR . '/': 'wp-content/plugins/');
	}
	$this->plugin_url = $url . $this->plugin_dir . '/';
}

/* ==================================================
 * @param	string   $domain
 * @param	string   $subdir
 * @return	none
 */
private function load_textdomain($domain, $subdir = '') {
	if ($this->check_wp_version('2.6', '>=') && defined('WP_PLUGIN_DIR')) {
		load_plugin_textdomain($domain, false, $this->get('plugin_dir') . ($subdir ? '/' . $subdir : ''));
	} else {
		$plugin_path = defined('PLUGINDIR') ? PLUGINDIR . '/': 'wp-content/plugins/';
		load_plugin_textdomain($domain, $plugin_path . $this->get('plugin_dir') . ($subdir ? '/' . $subdir : ''));
	}
}

/* ==================================================
 * @param	string   $version
 * @param	string   $operator
 * @return	boolean  $result
 */
public function check_wp_version($version, $operator = '>=') {
	$wp_vers = get_bloginfo('version');
	if (! is_numeric($wp_vers)) {
		$wp_vers = preg_replace('/[^.0-9]/', '', $wp_vers);
	}
	return version_compare($wp_vers, $version, $operator);
}

/* ==================================================
 * @param	string  $key
 * @return	boolean $charset
 */
public function get($key) {
	switch ($key) {
	case 'plugin_dir':
		return $this->plugin_dir;
	case 'plugin_url':
		return $this->plugin_url;
	case 'theme':
		return $this->theme;
	case 'theme_root':
		return $this->theme_root;
	case 'theme_root_uri':
		return $this->theme_root_uri;
	case 'template_dir':
		return $this->template_dir;
	default:
		if (! $this->ktai) {
			return Ktai_Services::get($key);
		}
		return $this->ktai->get($key);
	}
}

/* ==================================================
 * @param	string  $name
 * @return	mix     $value
 */
public function get_option($name, $return_default = false) {
	if (! $return_default) {
		$value = get_option($name);
		if (preg_match('/^ks_theme/', $name)) {
			$value = preg_replace('|^wp-content/|', '', $value);
		}
		if (false !== $value) {
			return $value;
		}
	}
	// default values 
	switch ($name) {
	case 'ks_theme':
		return 'default';
	case 'ks_date_color':
		return '#00aa33';
	case 'ks_author_color':
		return '#808080';
	case 'ks_comment_type_color':
		return '#808080';
	case 'ks_external_link_color':
		return '#660099';
	case 'ks_year_format':
		return 'Y-m-d';
	case 'ks_month_date_format':
		return 'n/j';
	case 'ks_time_format':
		return 'H:i';
	case 'ks_theme_mova':
	case 'ks_theme_foma':
	case 'ks_theme_ezweb':
	case 'ks_theme_sb_pdc':
	case 'ks_theme_sb_3g':
	case 'ks_theme_willcom':
	case 'ks_theme_emobile':
	default:
		return NULL;
	}
}

/* ==================================================
 * @param	none
 * @return	boolean $is_ktai
 */
public function is_ktai() {
	if ($this->ktai && ! isset($_COOKIE[KS_COOKIE_PCVIEW])) {
		return $this->ktai->get('operator');
	} 
	return false;
}

/* ==================================================
 * @param	none
 * @return	none
 * based on wp_redirect() at pluggable.php of WP 2.3.1
 */
public function redirect($location) {
	global $is_IIS;
	if ( $is_IIS ) {
		header("Refresh: 0;url=$location");
	} else {
		if ( php_sapi_name() != 'cgi-fcgi' ) {
			status_header(302);
		}
		header("Location: $location");
	}
}

/* ==================================================
 * @param	none
 * @return	none
 */
public function shutout_login() {
	$this->check_ktai_login(true);
}

/* ==================================================
 * @param	boolean $exit
 * @return	none
 */
public function check_ktai_login($exit = false) {
	$wpurl = @parse_url(get_bloginfo('wpurl'));
	if (preg_match('!^' . preg_quote($wpurl['path'], '!') . '/wp-login($|\?|\.php)!', $_SERVER['REQUEST_URI'])) {
		if (! $exit) {
			wp_redirect($this->get('plugin_url') . 'login.php');
		}
		exit();
	}
}

/* ==================================================
 * @param	string  $admin_dir
 * @return	none
 */
private function init_pc($admin_dir) {
	if (defined('WP_USE_THEMES')) {
		add_action('wp_head', array($this, 'show_mobile_url'));
//		add_action('atom_head', array($this, 'show_mobile_url_atom_head'));
//		add_action('atom_entry', array($this, 'show_mobile_url_atom_entry'));
		add_action('rss2_ns', array($this, 'show_mobile_url_rss2_ns'));
		add_action('rss2_head', array($this, 'show_mobile_url_rss2_head'));
		add_action('rss2_item', array($this, 'show_mobile_url_rss2_item'));
		if (isset($_COOKIE[KS_COOKIE_PCVIEW])) {
			add_action('wp_head', array($this, 'switch_ktai_view_css'));
			add_action('wp_footer', array($this, 'switch_ktai_view'));
		}
	} else {
		require dirname(__FILE__) . '/prefpane.php';
		$KS_Prefs = new Ktai_Style_PrefPane;
		add_action('admin_init', array($KS_Prefs, 'add_comment_meta'));
		add_action('admin_menu', array($KS_Prefs, 'add_page'));
		if (preg_match('!/wp-admin/!', $_SERVER['REQUEST_URI'])
		&& file_exists($admin_dir) && $this->check_wp_version('2.2', '>=')) {
			require $admin_dir . '/install.php';
			$plugin = $this->get('plugin_dir') . '/' . basename(__FILE__);
			add_action('activate_' . $plugin, array('Ktai_Style_Install', 'install'));
			add_action('deactivate_' . $plugin, array('Ktai_Style_Install', 'uninstall'));
		}
	}
	add_filter('the_content',  array('Ktai_Services', 'convert_pict'));
	add_filter('get_comment_text',  array('Ktai_Services', 'convert_pict'));
}

/* ==================================================
 * @param	string  $admin_dir
 * @return	none
 */
private function init_mobile($admin_dir) {
	$this->set_theme();
	add_filter('stylesheet', array($this, 'get_stylesheet'), 90);
	add_filter('option_stylesheet', array($this, 'get_stylesheet'), 90);
	add_filter('template', array($this, 'get_template'), 90);
	add_filter('option_template', array($this, 'get_template'), 90);
	add_filter('theme_root', array($this, 'get_theme_root'), 90);
	add_filter('theme_root_uri', array($this, 'get_theme_root_uri'), 90, 2);
	add_action('template_redirect', array($this, 'output'), 11);
	add_filter('clean_url', array($this, 'clean_url_filter'), 90, 3);
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'locale_stylesheet');
	remove_action('wp_head', 'wp_print_scripts');
	remove_action('wp_head', 'wp_generator');
	if ($this->get_option('ks_require_term_id')) {
		add_filter('pre_comment_user_agent', array($this->ktai, 'add_term_id'));
	}
	if (file_exists($admin_dir)) {
		if ($this->check_wp_version('2.2', '>=')) {
			require $admin_dir . '/pluggable-override.php';
			require $admin_dir . '/class.php';
			add_action('init', array($this, 'check_ktai_login'));
		} else {
			/* don't load admin feature */
		}
	} elseif (! defined('KS_KEEP_ADMIN_ACESS')) {
		// kill access to WP's admin feature
		function auth_redirect() {
			exit();
		}
		add_action('init', array($this, 'shutout_login'));
	}

	if (isset($_GET['ks'])) {
		$_GET['s'] = mb_convert_encoding($_GET['ks'], get_bloginfo('charset'), $this->detect_encoding(isset($_GET['Submit']) ? $_GET['Submit'] : ''));
	}
	if (isset($_POST['urlquery']) && isset($_POST['post_password'])) {
		parse_str($_POST['urlquery'], $query);
		foreach($query as $k => $v) {
			$_GET[$k] = $v;
		}
	}
}

/* ==================================================
 * @param	none
 * @return	none
 */
private function set_theme() {
	$theme = $this->ktai->get('theme');
	if (empty($theme) || preg_match('|[^-_.+/a-zA-Z0-9]|', $theme) || strpos($theme, '../') !== false) {
		$theme = $this->get_option('ks_theme');
	}
	if (strpos($theme, '/') !== false) {
		$path_item = explode('/', $theme);
		$theme = array_pop($path_item);
		if (! $theme) {
			$theme = array_pop($path_item);
		}
		$content_dir = defined('WP_CONTENT_DIR') ? WP_CONTENT_DIR . '/': ABSPATH . 'wp-content/';
		$this->theme_root = $content_dir . implode('/', $path_item);
		$content_url = defined('WP_CONTENT_URL') ? WP_CONTENT_URL . '/': get_option('siteurl') . '/wp-content/';
		$this->theme_root_uri = $content_url . implode('/', $path_item);
	} else {
		$this->theme_root = dirname(__FILE__) . '/' . KS_BUILT_IN_THEMES_DIR;
		$this->theme_root_uri = $this->get('plugin_url') . KS_BUILT_IN_THEMES_DIR;
	}
	$this->template_dir = $this->theme_root . "/$theme/";
	if (empty($theme) || preg_match('|[^-_.+a-zA-Z0-9]|', $theme) || strpos($theme, '../') !== false
	|| ! file_exists($this->template_dir) 
	|| ! file_exists($this->template_dir . 'index.php') 
	|| ! file_exists($this->template_dir . 'style.css')) {
		$theme = 'default';
		$this->template_dir = dirname(__FILE__) . '/' . KS_BUILT_IN_THEMES_DIR . "/$theme/";
	}
	$this->theme = $theme;
}

/* ==================================================
 * @param	striig  $stylesheet
 * @return	string  $stylesheet
 */
public function get_stylesheet($stylesheet) {
	return $this->get('theme');
}

/* ==================================================
 * @param	striig  $template
 * @return	string  $template
 */
public function get_template($template) {
	return $this->get('theme');
}

/* ==================================================
 * @param	striig  $path
 * @return	string  $path
 */
public function get_theme_root($path) {
	if ($this->theme_root) {
		return $this->theme_root;
	} else {
		return dirname(__FILE__) . '/' . KS_BUILT_IN_THEMES_DIR;
	}
}

/* ==================================================
 * @param	striig  $uri
 * @return	string  $uri
 */
public function get_theme_root_uri($uri, $siteurl) {
	$full_uri = $this->theme_root_uri ? $this->theme_root_uri : ($this->get('plugin_url') . KS_BUILT_IN_THEMES_DIR);
	return $this->strip_host($full_uri);
}
 
/* ==================================================
 * @param	none
 * @return	none
 */
public function output() {
	if ((function_exists('is_robots') && is_robots()) || is_feed() || is_trackback()) {
		return;
	}
	$this->set_image_inline();

	require dirname(__FILE__) . '/tags.php';
	if (is_404()) {
		// redirect to dashboard or login screen if accessed to non-existing URLs
		if (class_exists('Ktai_Style_Admin')) {
			if (preg_match('!^' . ks_plugin_url(false) . KS_ADMIN_DIR . '/!',  $_SERVER['REQUEST_URI'])) {
				$sid = Ktai_Style_Admin::get_sid();
				wp_redirect(ks_plugin_url(false) . ($sid ? KS_ADMIN_DIR . '/?' . KS_SESSION_NAME . '=' . attribute_escape($sid) : 'login.php'));
				exit();
			}
		} elseif (preg_match('!wp-admin/!',  $_SERVER['REQUEST_URI'])) {
			exit(); // shut out access to non-existing admin screen
		}
	}
	if (! $template = $this->load_template()) {
		$this->ks_die(__('Can\'t display pages. Bacause mobile phone templates are collapsed.', 'ktai_style'));
	}

	add_filter('wp_list_categories', array($this, 'filter_tags'), 90);
	add_filter('wp_list_pages', array($this, 'filter_tags'), 90);
	add_filter('raw_content/ktai_style.php', array($this->ktai, 'shrink_pre_encode'), 9);
	add_filter('encoding_converted/ktai_style.php', array($this->ktai, 'shrink_pre_split'), 5);
	add_filter('encoding_converted/ktai_style.php', array($this->ktai, 'replace_smiley'), 7);
	add_filter('encoding_converted/ktai_style.php', array($this->ktai, 'convert_pict'), 9);
	add_filter('split_page/ktai_style.php', array($this->ktai, 'shrink_post_split'), 15);
	$buffer = $this->ktai->get('preamble');
	$buffer .= ($buffer ? "\n" : '');
	ob_start();
	include $template;
	$buffer .= ob_get_contents();
	ob_end_clean();
	$buffer = apply_filters('raw_content/ktai_style.php', $buffer);
	$buffer = mb_convert_encoding($buffer, $this->ktai->get('charset'), get_bloginfo('charset'));
	$buffer = apply_filters('encoding_converted/ktai_style.php', $buffer);
	$buffer = apply_filters('split_page/ktai_style.php', $buffer, $this->get_page_num());
	$mime_type    = $this->ktai->get('mime_type');
	$iana_charset = $this->ktai->get('iana_charset');
	if (ks_is_front() || ks_is_menu('comments')) {
		nocache_headers();
	}
	header ("Content-Type: $mime_type; charset=$iana_charset");
	echo $buffer;
	exit;
}

/* ==================================================
 * @param	string  $url
 * @return	string  $url
 */
public function strip_host($url = '/') {
	return preg_replace('!^https?://[^/]*/?!', '/', $url);
}

/* ==================================================
 * @param	string  $url
 * @param	string  $original_url
 * @param	string  $context
 * @return	string  $url
 */
function clean_url_filter($url, $original_url, $context) {
	if ('display' == $context) {
		$url = str_replace('&#038;', '&amp;', $url);
	}
	return $url;
}

/* ==================================================
 * @param	string  $input
 * @return	string  $charset
 */
public function detect_encoding($input) {
	if (empty($input)) {
		$charset = 'auto';
	} else {
		$charset = mb_detect_encoding($input, array('ASCII', 'JIS', 'UTF-8', 'SJIS', 'EUC-JP'));
		if (! $charset || $charset == 'ASCII') {
			$charset = 'auto';
		}
	}
	return $charset;
}

/* ==================================================
 * @param	string  $charset1
 * @param	string  $charset2
 * @return	boolean $is_same
 */
public function compare_encoding($charset1, $charset2) {
	$normalize = array(
		'shift_jis'     => 'sjis',
		'sjis-win'      => 'sjis',
		'eucjp-win'     => 'euc-jp',
		'iso-2022-jp'   => 'jis',
		'iso-2022-jp-1' => 'jis',
		'iso-2022-jp-2' => 'jis',
	);
	$charset1 = strtr(strtolower($charset1), $normalize);
	$charset2 = strtr(strtolower($charset2), $normalize);
	return (strcmp($charset1, $charset2) === 0);
}

/* ==================================================
 * @param	none
 * @return	string  $template
 * based on wp-includes/template-loader.php of WP 2.2.3
 */
private function load_template() {
	if ( is_404() && $template = $this->query_template('404')) {
		return $template;
	} elseif (isset($_GET['menu'])) {
		if ($template = $this->menu_template($_GET['menu'])) {
			return $template;
		}
	} elseif (is_search() && $template = $this->query_template('search')) {
		return $template;
	} elseif (is_home() && $template = $this->get_home_template()) {
		return $template;
	} elseif (is_attachment() && $template = $this->get_attachment_template()) {
		return $template;
	} elseif (is_single() && $template = $this->query_template('single')) {
		if (is_attachment()) {
			add_filter('the_content', 'prepend_attachment');
		}
		return $template;
	} elseif (is_page() && $template = $this->get_page_template()) {
		if (is_attachment()) {
			add_filter('the_content', 'prepend_attachment');
		}
		return $template;
	} elseif (is_category() && $template = $this->get_category_template()) {
		return $template;
	} elseif (function_exists('is_tag') && is_tag() && $template = $this->get_tag_template()) {
		return $template;
	} elseif (is_author() && $template = $this->query_template('author')) {
		return $template;
	} elseif (is_date() && $template = $this->query_template('date')) {
		return $template;
	} elseif (is_archive() && $template = $this->query_template('archive')) {
		return $template;
	} elseif (is_paged() && $template = $this->query_template('paged')) {
		return $template;
	} elseif (file_exists($this->get('template_dir') . 'index.php')) {
		if (is_attachment()) {
			add_filter('the_content', 'prepend_attachment');
		}
		return $this->get('template_dir') . 'index.php';
	}
	return NULL;
}

/* ==================================================
 * @param	string  $type
 * @return	string  $template
 * based on get_query_template() at wp-includes/theme.php of WP 2.2.3
 */
public function query_template($type) {
	$template = '';
	if (file_exists($this->get('template_dir') . "{$type}.php")) {
		$template = $this->get('template_dir') . "{$type}.php";
	}
	return apply_filters("{$type}_template", $template);
}

/* ==================================================
 * @param	string  $type
 * @return	string  $template
 * based on get_query_template() at wp-includes/theme.php of WP 2.2.3
 */
private function menu_template($type) {
	if (! preg_match('/^[_a-z0-9]+$/', $type)) {
		return NULL;
	}
	$template = '';
	if (file_exists($this->get('template_dir') . "menu_{$type}.php")) {
		$template = $this->get('template_dir') . "menu_{$type}.php";
	} else {
		$default_menu['comments'] = true;
		$default_menu['months']   = true;
		$default_menu['cats']     = true;
		$default_menu['tags']     = true;
		$default_menu['pages']    = true;
		$default_menu['links']    = true;
		if (! $default_menu[$type]) {
			return NULL;
		} elseif (file_exists(dirname(__FILE__) . '/' . KS_BUILT_IN_THEMES_DIR . "/default/menu_{$type}.php")) {
			$template = dirname(__FILE__) . '/' . KS_BUILT_IN_THEMES_DIR . "/default/menu_{$type}.php";
		}
	}
	return apply_filters("menu_{$type}_template", $template);
}

/* ==================================================
 * @param	none
 * @return	string  $template
 * based on get_category_template() at wp-includes/theme.php of WP 2.2.3
 */
private function get_category_template() {
	$template = '';
	if (file_exists($this->get('template_dir') . 'category-' . get_query_var('cat') . '.php')) {
		$template = $this->get('template_dir') . 'category-' . get_query_var('cat') . '.php';
	} elseif (file_exists($this->get('template_dir') . 'category.php')) {
		$template = $this->get('template_dir') . 'category.php';
	}
	return apply_filters('category_template', $template);
}

/* ==================================================
 * @param	none
 * @return	string  $template
 * based on get_tag_template() at wp-includes/theme.php of WP 2.3.1
 */
private function get_tag_template() {
	$template = '';
	if (file_exists($this->get('template_dir') . 'tag-' . get_query_var('tag') . '.php')) {
		$template = $this->get('template_dir') . 'tag-' . get_query_var('tag') . '.php';
	} elseif (file_exists($this->get('template_dir') . 'tag.php')) {
		$template = $this->get('template_dir') . 'tag.php';
	}
	return apply_filters('tag_template', $template);
}

/* ==================================================
 * @param	none
 * @return	string
 * based on get_home_template() at wp-includes/theme.php of WP 2.2.3
 */
private function get_home_template() {
	$template = '';
	if (file_exists($this->get('template_dir') . 'home.php')) {
		$template = $this->get('template_dir') . 'home.php';
	} elseif (file_exists($this->get('template_dir') . 'index.php')) {
		$template = $this->get('template_dir') . 'index.php';
	}
	return apply_filters('home_template', $template);
}

/* ==================================================
 * @param	none
 * @return	string
 * based on get_page_template() at wp-includes/theme.php of WP 2.3.3
 */
private function get_page_template() {
	global $wp_query;

	$id = (int) $wp_query->post->ID;
	$template = get_post_meta($id, '_wp_page_template', true);

	if ('default' == $template) {
		$template = '';
	}
	if (! empty($template) && file_exists($this->get('template_dir') . $template)) {
		$template = $this->get('template_dir') . $template;
	} elseif (file_exists($this->get('template_dir') . 'page.php')) {
		$template = $this->get('template_dir') . 'page.php';
	} else {
		$template = '';
	}
	return apply_filters('page_template', $template);
}

/* ==================================================
 * @param	none
 * @return	string  $template
 * based on get_attachment_template() at wp-includes/theme.php of WP 2.2.3
 */
private function get_attachment_template() {
	global $posts;
	$type = explode('/', $posts[0]->post_mime_type);
	if ($template = $this->query_template($type[0]) )
		return $template;
	elseif ($template = $this->query_template($type[1]) )
		return $template;
	elseif ($template = $this->query_template("$type[0]_$type[1]") )
		return $template;
	else
		return $this->query_template('attachment');
}

/* ==================================================
 * @param	none
 * @return	none
 */
public function get_header() {
	do_action('get_header');
	if (file_exists($this->get('template_dir') . 'header.php')) {
		load_template($this->get('template_dir') . 'header.php');
	}
	return;
}

/* ==================================================
 * @param	none
 * @return	none
 */
public function get_footer() {
	do_action('get_footer');
	if (file_exists($this->get('template_dir') . 'footer.php')) {
		load_template($this->get('template_dir') . 'footer.php');
	}
	return;
}

/* ==================================================
 * @param	none
 * @return	int    $page_num
 */
private function get_page_num() {
	$page_num = 0;
	if (isset($_GET['kp']) && is_numeric($_GET['kp'])) {
		$page_num = intval($_GET['kp']);
	} elseif (isset($_POST['kp']) && is_numeric($_POST['kp'])) {
		$page_num = intval($_POST['kp']);
	}
	return $page_num;
}

/* ==================================================
 * @param	string  $html
 * @return	string  $html
 */
public function filter_tags($html) {
	$html = Ktai_HTML_Filter::kses($html, $this->get('allowedtags'));
	return $html;
}

/* ==================================================
 * @param   int      $post_id
 * @return	string   $url
 */
static function get_self_url() {
	if (! preg_match('|^(https?://[^/]*)|', get_bloginfo('url'), $host)) {
		$scheme = (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://';
		$host[1] = $scheme . $_SERVER['SERVER_NAME'];
	}
	return $host[1] . $_SERVER['REQUEST_URI'];
}

/* ==================================================
 * @param   none
 * @return	none
 */
public function show_mobile_url() {
	$url = self::get_self_url();
?>
<link rel="alternate" media="handheld" type="text/html" href="<?php echo htmlspecialchars($url, ENT_QUOTES); ?>" />
<?php 
}

/* ==================================================
 * @param   none
 * @return	none
 */
public function show_mobile_url_atom_head() {
	$url = preg_replace('!(\?feed=atom|feed/atom/?)$!', '', self::get_self_url());
?>
<link rel="alternate" x:media="handheld" type="text/html" href="<?php echo htmlspecialchars($url, ENT_QUOTES); ?>" />
<?php 
}

/* ==================================================
 * @param   none
 * @return	none
 */
public function show_mobile_url_atom_entry() {
	$url = get_permalink();
?>
		<link rel="alternate" x:media="handheld" type="text/html" href="<?php echo htmlspecialchars($url, ENT_QUOTES); ?>" />
<?php 
}

/* ==================================================
 * @param   none
 * @return	none
 */
public function show_mobile_url_rss2_ns() {
?>
	xmlns:xhtml="http://www.w3.org/1999/xhtml"
<?php 
}

/* ==================================================
 * @param   none
 * @return	none
 */
public function show_mobile_url_rss2_head() {
	$url = preg_replace('!(\?feed=rss2|feed/rss2/?)$!', '', self::get_self_url());
?>
		<xhtml:link rel="alternate" media="handheld" type="text/html" href="<?php echo htmlspecialchars($url, ENT_QUOTES); ?>" />
<?php 
}

/* ==================================================
 * @param   none
 * @return	none
 */
public function show_mobile_url_rss2_item() {
	$url = get_permalink();
?>
	<xhtml:link rel="alternate" media="handheld" type="text/html" href="<?php echo htmlspecialchars($url, ENT_QUOTES); ?>" />
<?php 
}

/* ==================================================
 * @param   none
 * @return	none
 */
public function switch_ktai_view_css() {
	$style = <<< E__O__T
#switch-mobile {color:white; background:gray; text-align:center; clear:both;}
#switch-mobile a, #switch-mobile a:link, #switch-mobile a:visited {color:white;}
E__O__T;
	$style = apply_filters('switch_ktai_view_css/ktai_style.php', $style);
	if ($style) {
		echo '<style type="text/css">' . $style . '</style>';
	}
}

/* ==================================================
 * @param   none
 * @return	none
 */
public function switch_ktai_view() {
	$here = $_SERVER['REQUEST_URI'];
	$menu = '<div id="switch-mobile"><a href="' . 
	attribute_escape($here . (strpos($here, '?') === false ? '?' : '&') . 'pcview=false') . 
	'">' . __('To Mobile view', 'ktai_style') . '</a></div>';
	echo apply_filters('switch_ktai_view/ktai_style.php', $menu, $here);
}

/* ==================================================
 * @param	none
 * @return	array  $themes
 */
public function installed_themes() {
	$theme_data = get_theme_data(dirname(__FILE__) . '/' . KS_BUILT_IN_THEMES_DIR . '/default/style.css');
	if (isset($theme_data['Name'])) {
		$themes['default'] = $theme_data['Name'] . ' (' . $theme_data['Version'] . ')';
	} else {
		$theme['default'] = 'Default';
	}
	foreach (glob(dirname(__FILE__) . '/' . KS_BUILT_IN_THEMES_DIR . '/*', GLOB_ONLYDIR) as $d) {
		if (! file_exists($d . '/index.php') || ! file_exists($d . '/style.css')) {
			continue;
		}
		if (preg_match('!/([-_.+a-zA-Z0-9]+)/?$!', $d, $filename) && $filename[1] != 'default') {
			$theme_data = get_theme_data($d . '/style.css');
			$themes[$filename[1]] = $theme_data['Name'] . ' (' . $theme_data['Version'] . ')';
		}
	}
	$content_dir = defined('WP_CONTENT_DIR') ? WP_CONTENT_DIR . '/' : ABSPATH . 'wp-content/';
	if (! file_exists($content_dir . KS_USER_THEMES_DIR)) {
		return $themes;
	}
	foreach (glob($content_dir . KS_USER_THEMES_DIR . '/*', GLOB_ONLYDIR) as $d) {
		if (! file_exists($d . '/index.php') || ! file_exists($d . '/style.css')) {
			continue;
		}
		if (preg_match('!/([-_.+a-zA-Z0-9]+)/?$!', $d, $filename) && ! in_array($filename[1], $themes)) {
			$theme_data = get_theme_data($d . '/style.css');
			$themes[KS_USER_THEMES_DIR . '/' . $filename[1]] = $theme_data['Name'] . ' (' . $theme_data['Version'] . ')';
		}
	}
	return $themes;
}

/* ==================================================
 * @param	none
 * @return	none
 */
private function set_image_inline() {
	$this->ktai->set('image_inline_default', apply_filters('image_inline_setting/ktai_style.php', $this->ktai->get('flat_rate') && ! $this->get_option('ks_images_to_link')));
	if (isset($_GET['img'])) {
		switch($_GET['img']) {
		case 'inline':
			$this->ktai->set('image_inline', $this->ktai->get('flat_rate'));
			break;
		case 'link':
			$this->ktai->set('image_inline', false);
			break;
		}
	} else {
		$this->ktai->set('image_inline', $this->ktai->get('image_inline_default'));
	}
}

/* ==================================================
 * @param	string  $message
 * @param	string  $title
 * @param	boolean $show_back_link
 * @param	boolean $encode_converted
 * @return	none
 * based on wp_die() at wp-includes/functions() of WP 2.2.3
 */
public function ks_die($message, $title = '', $show_back_link = true, $encode_converted = false) {
	global $Ktai_Style, $KS_Admin;

	if ( function_exists( 'is_wp_error' ) && is_wp_error( $message ) ) {
		if ( empty($title) ) {
			$error_data = $message->get_error_data();
			if ( is_array($error_data) && isset($error_data['title']) )
				$title = $error_data['title'];
		}
		$errors = $message->get_error_messages();
		switch ( count($errors) ) :
		case 0 :
			$message = '';
			break;
		case 1 :
			$message = "<p>{$errors[0]}</p>";
			break;
		default :
			$message = "<ul><li>" . join( "</li><li>", $errors ) . "</li></ul>";
			break;
		endswitch;
	} elseif (is_string($message) && strpos($message, '<p>') === false) {
		$message = "<p>$message</p>";
	}
	if ($show_back_link && $KS_Admin && $referer = $KS_Admin->get_referer()) {
		$message .= sprintf(__('Back to <a href="%s">the previous page</a>.', 'ktai_style'), attribute_escape($referer));
	}

	switch ($Ktai_Style->is_ktai()) {
	case 'DoCoMo':
		$logo_ext = 'gif';
		$head_style = '';
		break;
	case 'KDDI':
		$logo_ext = 'png';
		$head_style = '<style>p {margin-bottom:1em;}</style>';
		break;
	default:
		$logo_ext = 'png';
		$head_style = '';
		break;
	}
	$mime_type = 'text/html';
	if (! did_action('admin_head') ) :
		if ($Ktai_Style->ktai) {
			$charset      = $Ktai_Style->ktai->get('charset');
			$iana_charset = $Ktai_Style->ktai->get('iana_charset');
			$Ktai_Style->ktai->set('mime_type', $mime_type);
		} else {
			$charset      = Ktai_Services::get('charset');
			$iana_charset = Ktai_Services::get('iana_charset');
		}
		header ("Content-Type: $mime_type; charset=$iana_charset");

		if (empty($title)) {
			$title = __('WordPress | Error', 'ktai_style');
		}
		if (! $encode_converted) {
			$title = mb_convert_encoding($title, $charset, get_bloginfo('charset'));
			$message = mb_convert_encoding($message, $charset, get_bloginfo('charset'));
		}
		echo '<?xml version="1.0" encoding="' . $iana_charset .'" ?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.0//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="<?php echo $mime_type; ?>; charset=<?php echo $iana_charset; ?>" />
<title><?php echo wp_specialchars($title); ?></title>
<?php echo $head_style; ?>
</head>
<body>
<?php endif;
$logo_url = $Ktai_Style->strip_host($Ktai_Style->get('plugin_url')) . 'wplogo.' . $logo_ext ;
echo apply_filters('ks_die_logo/ktai_style.php', '<div><h1 id="logo"><img alt="WordPress" src="' . $logo_url . '" /></h1></div>', $logo_url, $logo_ext);
echo $message; ?>
</body>
</html>
<?php
	exit();
}

// ===== End of class ====================
}
?>