<?php
/*
Plugin Name: WP Multibyte Patch
Plugin URI: http://eastcoder.com/code/wp-multibyte-patch/
Description: WP Multibyte Patch は本家版、日本語版 WordPress のマルチバイト文字の取り扱いに関する不具合の累積的修正と強化を行うプラグインです。 <a href="http://eastcoder.com/code/wp-multibyte-patch/">&raquo; 詳しい説明を読む</a>
Author: tenpura
Version: 1.1
Author URI: http://eastcoder.com/
*/

/*
    Copyright (C) 2008 tenpura (Email: 210pura at gmail dot com)
           This program is licensed under the GNU GPL.
*/

class multibyte_patch {

	var $conf = array(
	                'excerpt_length' => 55,
	                'excerpt_mblength' => 110,
	                'comment_excerpt_length' => 20,
	                'comment_excerpt_mblength' => 40,
	                'patch_wp_mail' => true,
	                'patch_incoming_trackback' => true,
	                'patch_incoming_pingback' => true,
	                'patch_wp_trim_excerpt' => true,
	                'patch_get_comment_excerpt' => true,
	                'patch_process_search_terms' => true,
	                'patch_admin_custom_css' => true
	            );

	var $blog_encoding;
	var $has_mbfunctions;

	function guess_encoding($string, $encoding = '') {
		$blog_encoding = $this->blog_encoding;

		if(!$encoding && seems_utf8($string))
			return 'UTF-8';
		elseif(!$encoding)
			return $blog_encoding;
		else
			return $encoding;
	}

	function convenc($string, $to_encoding, $from_encoding = '') {
		$blog_encoding = $this->blog_encoding;

		if('' == $from_encoding)
			$from_encoding = $blog_encoding;

		if(strtoupper($to_encoding) == strtoupper($from_encoding))
			return $string;
		else
			return mb_convert_encoding($string, $to_encoding, $from_encoding);
	}

	function incoming_trackback($commentdata) {
		global $wpdb;

		if('trackback' != $commentdata['comment_type'])
			return $commentdata;

		if(false === $this->conf['patch_incoming_trackback'])
			return $commentdata;

		$title = stripslashes($_POST['title']);
		$excerpt = stripslashes($_POST['excerpt']);
		$blog_name = stripslashes($_POST['blog_name']);
		$blog_encoding = $this->blog_encoding;
		$from_encoding = (preg_match("/^.*charset=([a-zA-Z0-9\-_]+).*$/i", $_SERVER['CONTENT_TYPE'], $matched)) ? $matched[1] : '';
		$from_encoding = $this->guess_encoding($excerpt . $title . $blog_name, $from_encoding);

		$title = $this->convenc($title, $blog_encoding, $from_encoding);
		$blog_name = $this->convenc($blog_name, $blog_encoding, $from_encoding);
		$excerpt = $this->convenc($excerpt, $blog_encoding, $from_encoding);

		$title = strip_tags($title);
		$excerpt = strip_tags($excerpt);

		$title = (strlen($title) > 250) ? mb_strcut($title, 0, 250, $blog_encoding) . '...' : $title;
		$excerpt = (strlen($excerpt) > 255) ? mb_strcut($excerpt, 0, 252, $blog_encoding) . '...' : $excerpt;

		$title = wp_specialchars($title);

		$commentdata['comment_author'] = $wpdb->escape($blog_name);
		$commentdata['comment_content'] = $wpdb->escape("<strong>$title</strong>\n\n$excerpt");

		return $commentdata;
	}

	function pre_remote_source($linea, $pagelinkedto) {
		$this->pingback_ping_linea = $linea;
		$this->pingback_ping_pagelinkedto = $pagelinkedto;
		return $linea;
	}

	function incoming_pingback($commentdata) {
		if('pingback' != $commentdata['comment_type'])
			return $commentdata;

		if(false === $this->conf['patch_incoming_pingback'])
			return $commentdata;

		$pagelinkedto = $this->pingback_ping_pagelinkedto;
		$linea = $this->pingback_ping_linea;

		$linea = preg_replace("/" . preg_quote('<!DOC', '/') . "/i", '<DOC', $linea);
		$linea = preg_replace("/\s+/", ' ', $linea);
		$linea = preg_replace("/ <(h1|h2|h3|h4|h5|h6|p|th|td|li|dt|dd|pre|caption|input|textarea|button|body)[^>]*>/i", "\n\n", $linea);

		preg_match("/<meta[^<>]+charset=([a-zA-Z0-9\-_]+)[^<>]*>/i", $linea, $charset);
		$from_encoding = $this->guess_encoding(strip_tags($linea), $charset[1]);
		$blog_encoding = $this->blog_encoding;

		$linea = strip_tags($linea, '<a>');
		$linea = $this->convenc($linea, $blog_encoding, $from_encoding);
		$p = explode("\n\n", $linea);

		foreach ($p as $para) {
			if(strpos($para, $pagelinkedto) !== false && preg_match("/^([^<>]*)(\<a[^<>]+[\"']" . preg_quote($pagelinkedto, '/') . "[\"'][^<>]*\>)([^<>]+)(\<\/a\>)(.*)$/i", $para, $context))
				break;
		}

		if(!$context)
			return $commentdata;

		$context[1] = strip_tags($context[1]);
		$context[5] = strip_tags($context[5]);
		$len_max = 250;
		$len_c3 = strlen($context[3]);

		if($len_c3 > $len_max) {
			$excerpt = mb_strcut($context[3], 0, 250, $blog_encoding);
		} else {
			$len_c1 = strlen($context[1]);
			$len_c5 = strlen($context[5]);
			$len_left = $len_max - $len_c3;
			$len_left_even = ceil($len_left / 2);

			if($len_left_even > $len_c1) {
				$context[5] = mb_strcut($context[5], 0, $len_left - $len_c1, $blog_encoding);
			}
			elseif($len_left_even > $len_c5) {
				$context[1] .= "\t\t\t\t\t\t";
				$context[1] = mb_strcut($context[1], $len_c1 - ($len_left - $len_c5), $len_c1 + 6, $blog_encoding);
				$context[1] = preg_replace("/\t*$/", '', $context[1]);
			}
			else {
				$context[1] .= "\t\t\t\t\t\t";
				$context[1] = mb_strcut($context[1], $len_c1 - $len_left_even, $len_c1 + 6, $blog_encoding);
				$context[1] = preg_replace("/\t*$/", '', $context[1]);
				$context[5] = mb_strcut($context[5], 0, $len_left_even, $blog_encoding);
			}

			$excerpt = $context[1] . $context[3] . $context[5];
		}

		$commentdata['comment_content'] = '[...] ' . wp_specialchars($excerpt) . ' [...]';
		$commentdata['comment_content'] = addslashes($commentdata['comment_content']);
		$commentdata['comment_author'] = stripslashes($commentdata['comment_author']);
		$commentdata['comment_author'] = $this->convenc($commentdata['comment_author'], $blog_encoding, $from_encoding);
		$commentdata['comment_author'] = addslashes($commentdata['comment_author']);

		return $commentdata;
	}

	function preprocess_comment($commentdata) {
		if($commentdata['comment_type'] == 'trackback')
			return $this->incoming_trackback($commentdata);
		elseif($commentdata['comment_type'] == 'pingback')
			return $this->incoming_pingback($commentdata);
		else
			return $commentdata;
	}

	function is_almost_ascii($string, $encoding) {
		return (75 < round(@(mb_strlen($string, $encoding) / strlen($string)) * 100)) ? true : false;
	}

	function wp_trim_excerpt($text) {
		global $post;
		$blog_encoding = $this->blog_encoding;

		if('' == $text) {
			$text = get_the_content('');

			$text = strip_shortcodes( $text );

			$text = apply_filters('the_content', $text);
			$text = str_replace(']]>', ']]&gt;', $text);
			$text = strip_tags($text);

			if($this->is_almost_ascii($text, $blog_encoding)) {
				$words = explode(' ', $text, $this->conf['excerpt_length'] + 1);

				if(count($words) > $this->conf['excerpt_length']) {
					array_pop($words);
					array_push($words, '[...]');
					$text = implode(' ', $words);
				}
			}
			elseif(mb_strlen($text, $blog_encoding) > $this->conf['excerpt_mblength']) {
				$text = mb_substr($text, 0, $this->conf['excerpt_mblength'], $blog_encoding) . ' [...]';
			}
		}

		return $text;
	}

	function get_comment_excerpt() {
		global $comment;
		$blog_encoding = $this->blog_encoding;

		$comment_text = strip_tags($comment->comment_content);

		if($this->is_almost_ascii($comment_text, $blog_encoding)) {
			$words = explode(' ', $comment_text, $this->conf['comment_excerpt_length'] + 1);

			if(count($words) > $this->conf['comment_excerpt_length']) {
				array_pop($words);
				array_push($words, '[...]');
				$comment_text = implode(' ', $words);
			}
		}
		elseif(mb_strlen($comment_text, $blog_encoding) > $this->conf['comment_excerpt_mblength']) {
			$comment_text = mb_substr($comment_text, 0, $this->conf['comment_excerpt_mblength'], $blog_encoding) . ' [...]';
		}

		return $comment_text;
	}

	function deactivation_conditionals() {
		global $wp_version;
		return (version_compare($wp_version, '2.5', '<') || !$this->has_mbfunctions) ? true : false;
	}

	function deactivate_self() {
		$plugin = plugin_basename(__FILE__);
		$current = get_option('active_plugins');
		$key = array_search($plugin, $current);

		if(false !== $key && null !== $key) {
			array_splice($current, $key, 1);
			update_option('active_plugins', $current);
		}
	}

	function filters() {
		// remove filter
		if(false !== $this->conf['patch_wp_trim_excerpt'])
			remove_filter('get_the_excerpt', 'wp_trim_excerpt');

		// add filter
		add_filter('preprocess_comment', array(&$this, 'preprocess_comment'), 99);

		if(false !== $this->conf['patch_incoming_pingback'])
			add_filter('pre_remote_source', array(&$this, 'pre_remote_source'), 10, 2);

		if(false !== $this->conf['patch_wp_trim_excerpt'])
			add_filter('get_the_excerpt', array(&$this, 'wp_trim_excerpt'));

		if(false !== $this->conf['patch_get_comment_excerpt'])
			add_filter('get_comment_excerpt', array(&$this, 'get_comment_excerpt'));

		// add action
		if(method_exists($this, 'process_search_terms') && false !== $this->conf['patch_process_search_terms'])
			add_action('sanitize_comment_cookies', array(&$this, 'process_search_terms'));

		if(method_exists($this, 'wp_mail') && false !== $this->conf['patch_wp_mail'])
			add_action('phpmailer_init', array(&$this, 'wp_mail'));

		if(method_exists($this, 'admin_custom_css') && false !== $this->conf['patch_admin_custom_css'])
			add_action('admin_head' , array(&$this, 'admin_custom_css'), 99);
	}

	function mbfunctions_exist() {
		return (
		           function_exists('mb_convert_encoding') &&
		           function_exists('mb_convert_kana') &&
		           function_exists('mb_detect_encoding') &&
		           function_exists('mb_strcut') &&
		           function_exists('mb_strlen') &&
		           function_exists('mb_substr')
		       ) ? true : false;
	}

	function multibyte_patch() {
		global $wpmp_conf;
		$this->conf = array_merge($this->conf, $wpmp_conf);

		$this->blog_encoding = get_option('blog_charset');
		$this->has_mbfunctions = $this->mbfunctions_exist();

		if($this->deactivation_conditionals()) {
			$this->deactivate_self();
			return;
		}

		$this->filters();
	}
}

if(defined('PLUGINDIR')) {
	$wpmp_conf = array();
	$wpmp_conf['base_dir'] = dirname(__FILE__);

	if(file_exists($wpmp_conf['base_dir'] . '/wpmp-config.php'))
		require_once($wpmp_conf['base_dir'] . '/wpmp-config.php');

	if(file_exists($wpmp_conf['base_dir'] . '/ext/' . get_locale() . '/class.php')) {
		if(file_exists($wpmp_conf['base_dir'] . '/ext/' . get_locale() . '/config.php'))
			require_once($wpmp_conf['base_dir'] . '/ext/' . get_locale() . '/config.php');

		require_once($wpmp_conf['base_dir'] . '/ext/' . get_locale() . '/class.php');
		$wpmp = new multibyte_patch_ext();
	}
	elseif(file_exists($wpmp_conf['base_dir'] . '/ext/default/class.php')) {
		if(file_exists($wpmp_conf['base_dir'] . '/ext/default/config.php'))
			require_once($wpmp_conf['base_dir'] . '/ext/default/config.php');

		require_once($wpmp_conf['base_dir'] . '/ext/default/class.php');
		$wpmp = new multibyte_patch_ext();
	}
	else
		$wpmp = new multibyte_patch();
}

?>