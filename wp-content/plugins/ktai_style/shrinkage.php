<?php
/* ==================================================
 *   Ktai Content Shrinkage
   ================================================== */

global $KS_Shrinkage;
$KS_Shrinkage = new Ktai_Content_Shrinkage;

add_filter('attribute_escape', array($KS_Shrinkage, 'attribute_escape_filter'), 90, 2);
add_filter('the_title', array($KS_Shrinkage, 'shrink_title'), 90);
add_filter('the_content', array($KS_Shrinkage, 'shrink_content'), 90);
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt',  array($KS_Shrinkage, 'trim_excerpt'), 9);
add_filter('get_comment_text', array($KS_Shrinkage, 'shrink_content'), 90);
add_filter('post_link', array($KS_Shrinkage->parent, 'strip_host'), 90);
add_filter('page_link', array($KS_Shrinkage->parent, 'strip_host'), 90);
add_filter('attachment_link', array($KS_Shrinkage->parent, 'strip_host'), 90);
add_filter('year_link', array($KS_Shrinkage->parent, 'strip_host'), 90);
add_filter('month_link', array($KS_Shrinkage->parent, 'strip_host'), 90);
add_filter('day_link', array($KS_Shrinkage->parent, 'strip_host'), 90);
add_filter('category_link', array($KS_Shrinkage->parent, 'strip_host'), 90);
add_filter('list_cats', array($KS_Shrinkage->parent, 'strip_host'), 90);
add_filter('tag_link', array($KS_Shrinkage->parent, 'strip_host'), 90);
add_filter('redirect_canonical', array($KS_Shrinkage, 'complete_url'), 10, 2);
add_filter('wp_generate_tag_cloud', array($KS_Shrinkage, 'shrink_tag_cloud'), 90, 3);
add_filter('split_page/ktai_style.php', array($KS_Shrinkage, 'add_url_inline_image'), 7);
add_filter('split_page/ktai_style.php', array($KS_Shrinkage, 'split_page'), 9, 2);
add_filter('split_page/ktai_style.php', array($KS_Shrinkage, 'trim_images'), 20);

define ('KS_THUMBNAIL_FILENAME', '.ktai');
define ('KS_THUMBNAIL_MAX_SIZE', 96);
define ('KS_SIZE_EXCEED_COLOR', '#808080');
define ('KS_KTAI_SITE_CLASS', 'ktai');
define ('KS_PCONLY_SITE_CLASS', 'pconly');
define ('KS_REDIR_URL', 'redir.php?url=');

//define ('KS_SHRINKAGE_DEBUG', true);

class Ktai_Content_Shrinkage {
	public $parent;
	private $num_image;
	private $url;
	private $url_pat;
	private $wpurl;
	private $wpurl_pat;
	private $wpcontent_pat;
	private $basepath;
	private $short_links;
	private $internal_pat;
	private $short_int_pat;
	private $host_pat;
	private $wp_plugin_url;
	private $scheme_pat;
	private $leave_pat;
	private $mobile_pat;
	private $none_mobile_pat;
	static public $mobile_same_url = array(
		// Use same URL for PC and mobile
		'http://[-\w]+\.blog\d+\.fc2\.com/', 
		'http://(jugem|yaplog)\.jp/[-\w]+/', 
		'http://[-\w]+\.seesaa\.net/', 
		'http://blog\.goo\.ne\.jp/[-\w]+/', 
		'http://blogs\.dion\.ne\.jp/[-\w]+/',
		'http://[-\w]+\.blog\.so-net\.ne\.jp/',
		'http://[-\w]+\.paslog\.jp/',
		'http://[-\w]+\.vox\.com/',
		'http://hb\.afl\.rakuten\.co\.jp/hgc/',
		'http://[-\w]+\.ap\.teacup\.com/',
		// Redirect mobile URL from PC
		'http://d\.hatena\.ne\.jp/[-\w]+/',
		'http://blog\.livedoor\.jp/[-\w]+/',
		'http://[-\w]+.(cocolog|air|moe|tea|txt|way)-nifty\.com/',
		'http://[-\w]+\.at\.webry\.info/',
		'http://[-\w]+\.spaces\.live\.com/',
		'http://plaza\.rakuten\.co\.jp/[-\w]+(/|$)',
//		'http://mixi\.jp/',
	);
	static public $none_mobile_url = array(
		'http://(www|support|app)\.cocolog-nifty\.com/',
	);

// ==================================================
public function __construct() {
	global $Ktai_Style, $wpmu_version;
	$this->parent = $Ktai_Style;
	$this->num_image = 0;
	$this->url       = $this->parent->strip_host(get_bloginfo('url') . '/');
	$this->url_pat   = '!^(' . preg_quote(get_bloginfo('url'), '!') . '/|' . preg_quote($this->url, '!') . ')!';
	$this->wpurl     = $this->parent->strip_host(get_bloginfo('wpurl') . '/');
	if (! isset($wpmu_version)) {
		$this->wpurl_pat = '!^(' . preg_quote(get_bloginfo('wpurl'), '!') . '/|' . preg_quote($this->wpurl, '!') . ')!';
		$this->wpcontent_pat = preg_replace('/\)!$/', ')wp-content/!', $this->wpurl_pat);
		$this->basepath = ABSPATH;
		$this->internal_pat[0] = '!^' . preg_quote(get_bloginfo('wpurl'), '!') . '/?!';
	} else {
		$this->wpurl .= 'files/';
		$this->wpurl_pat = '!^(' . preg_quote(get_bloginfo('wpurl'), '!') . '/files/|' . preg_quote($this->wpurl, '!') . ')!';
		$this->wpcontent_pat = $this->wpurl_pat;
		$this->basepath = constant('ABSPATH') . constant('UPLOADS');
		$this->internal_pat[0] = '!^' . preg_quote(get_bloginfo('wpurl'), '!') . '/files/?!';
	}
	$this->short_links[0]  = $this->wpurl;
	if (strcmp($this->wpurl, $this->url) !== 0) {
		$this->internal_pat[] = '!^' . preg_quote(get_bloginfo('url'), '!') . '/?!';
		$this->short_links[]  = $this->url;
	}
	$this->short_int_pat = '!^(' . implode('|',  array_map('preg_quote', $this->short_links)) . ')!';
	if (preg_match('!^(https?://[^/]*)/?!', get_bloginfo('wpurl'), $host)) {
		$this->host_pat = '!^(/|' . preg_quote($host[1], '!') . '/)!';
	} else {
		$this->host_pat = '!^/!';
	}
	$this->wp_plugin_url = preg_replace($this->wpurl_pat, '', $this->parent->get('plugin_url'));
	
	$leave_schemes = apply_filters('leave_scheme/ktai_style.php', array('tel:', 'tel-av:', 'vtel:', 'mailto:', 'device:', 'location:'));
	$leave_sites = preg_split('/\\s+/', ks_option('ks_treat_as_internal'), -1, PREG_SPLIT_NO_EMPTY);
	$this->scheme_pat = '!^(#|' . implode('|', $leave_schemes) . ')!';
	$this->leave_pat = '!^(#|' . implode('|', array_merge($leave_schemes, array_map('preg_quote', $leave_sites))) . ')!';
	$this->mobile_pat = '!^(' . implode('|', apply_filters('mobile_same_url/ktai_style.php', self::$mobile_same_url)) . ')!';
	$this->none_mobile_pat = '!^(' . implode('|', apply_filters('none_mobile_url/ktai_style.php', self::$none_mobile_url)) . ')!';
	return;
}

// ==================================================
public function get($key) {
	return isset($this->$key) ? $this->$key : NULL;
}

// ==================================================
public function added_image() {
	return ++$this->num_image;
}

// ==================================================
public function has_inline_images() {
	return $this->num_image;
}

/* ==================================================
 * @param	string  $safe_text
 * @param	string  $text
 * @return	string  $safe_text
 */
public function attribute_escape_filter($safe_text, $text) {
	return str_replace('&#038;', '&amp;', $safe_text);
}

/* ==================================================
 * @param	string  $url
 * @return	string  $url
 */
public function complete_url($redirect_url, $requested_url) {
	if (preg_match('!^://!', $redirect_url)) {
		$request = @parse_url($requested_url);
		$redirect_url = $request['scheme'] . $redirect_url;
	} elseif (preg_match('!^/!', $redirect_url) && preg_match('!^(https?://[^/]*)!', $requested_url, $request)) {
		$redirect_url = $request[1] . $redirect_url;
	}
	return $redirect_url;
}

/* ==================================================
 * @param	string  $title
 * @return	string  $title
 */
public function shrink_title($title) {
	$phrase[0]     = str_replace('%s', '', __('Protected: %s'));
	$phrase_pat[0] = '/^' . preg_quote($phrase[0]) . '/';
	$icon[0]       = '<img localsrc="279" alt="' . $phrase[0] . '" />';
	$phrase[1]     = str_replace('%s', '', __('Private: %s'));
	$phrase_pat[1] = '/^' . preg_quote($phrase[1]) . '/';
	$icon[1]       = '<img localsrc="501" alt="' . $phrase[1] . '" />';
	return preg_replace($phrase_pat, $icon, $title);
}

/* ==================================================
 * @param	string  $content
 * @return	string  $content
 */
public function convert_links($content) {
	for ($offset = 0, $replace = 'X' ; 
	     preg_match('!<a ([^>]*?)>(.*?)</a>!', $content, $l, PREG_OFFSET_CAPTURE, $offset) ; 
	     $offset += strlen($replace))
	{
		$orig      = $l[0][0];
		$offset    = $l[0][1];
		$attr      = $l[1][0];
		$label     = $l[2][0];
		$link_html = $label; // default is stripping links
		$replace   = $orig;
		if (! preg_match('/href=([\'"])([^\\\\]*?(\\\\.[^\\1\\\\]*?)*)\\1/', $attr, $h)) {
			continue;
		}
		$href = $h[2];
		preg_match('/class=([\'"])([^\\\\]*?(\\\\.[^\\1\\\\]*?)*)\\1/', $attr, $class);
		if (preg_match('!<img [^>]*?src=([\'"])([^\\\\]*?(\\\\.[^\\1\\\\]*?)*)\\1[^>]*? ?/?>!', $label, $image)) {
			$src = $image[2];
			if (preg_match('!' . preg_quote($this->wp_plugin_url, '!') . '!', $src)) { // skip plug-in's icon
				continue; // leave links
			} elseif (preg_match($this->wpurl_pat, $href) && preg_match($this->wpurl_pat, $src)) { // both internal link
				$path = preg_replace($this->wpurl_pat, $this->basepath, $href);
				if (! is_dir($path) && $imagesize = @filesize($path)) { // a thumbnail linked to original image
					$thumbnail = str_replace('<img ', '<img has_orig="true" ', $label); // inform existance of original to images_to_link()
					$link_html = $thumbnail . '<img src="' . $href . '" alt="' . sprintf(__('Original(%dKB)', 'ktai_style'), intval($imagesize / 1024)) . '" filesize="' . $imagesize . '"/>'; // pass filesize to images_to_link()
				} else { // internal link to other than images
					$link_html = $label . '(<a href="' . $href . '">' . __('Link Target', 'ktai_style') . '</a>)';
				}
			} elseif (preg_match($this->host_pat, $href) && preg_match($this->host_pat, $src) && $_SERVER['DOCUMENT_ROOT']) { // both in-host link
				$path = preg_replace($this->host_pat, $_SERVER['DOCUMENT_ROOT'] . '/', $href);
				if (! is_dir($path) && $imagesize = @filesize($path)) { // a thumbnail linked to original image
					$thumbnail = str_replace('<img ', '<img has_orig="true" ', $label); // inform existance of original to images_to_link()
					$link_html = $thumbnail . '<img src="' . $href . '" alt="' . sprintf(__('Original(%dKB)', 'ktai_style'), intval($imagesize / 1024)) . '" filesize="' . $imagesize . '"/>'; // pass filesize to images_to_link()
				} else { // internal link to other than images
					$link_html = $label . '(<a href="' . $href . '">' . __('Link Target', 'ktai_style') . '</a>)';
				}
			} else { // external link
				$link_html = $this->rewrite_link($href, __('Link Target', 'ktai_style'), $class);
				if (is_null($link_html)) {
					$link_html = '<a href="' . $href . '">' . __('Link Target', 'ktai_style') . '</a>';
				}
				$link_html = "{$label}($link_html)";
			}
		} else {
			$link_html = $this->rewrite_link($href, $label, $class);
		}
		$replace = apply_filters('convert_links/ktai_style.php', $link_html, $orig, $href, $label);
		if (! is_null($replace)) {
			$content = substr_replace($content, $replace, $offset, strlen($orig)); // convert links
		} else {
			$offset += strlen($orig);
		}
	}
	return $content;
}

/* ==================================================
 * @param   string  $href
 * @param	string  $label
 * @param	string  $class
 * @return	string  $link_html
 */
public function rewrite_link($href, $label, $class) {
	$clipped = preg_replace($this->internal_pat, $this->short_links, $href, 1, $is_internal);
	$leave_pat = ks_is_loggedin() ? $this->scheme_pat : $this->leave_pat;
	if ($is_internal) {
		$link_html = '<a href="' . $clipped . '">' . $label . '</a>';
	} elseif (preg_match($this->short_int_pat, $href) 
	       || preg_match($leave_pat, $href)) {
		$link_html = NULL; // leave links
	} else {
		$icon = '<img localsrc="70" alt="' . __('[external]', 'ktai_style') . '" />';
		if (preg_match('/(^| )' . preg_quote(KS_PCONLY_SITE_CLASS) . '( |$)/', $class[2]) || $this->none_mobile_sites($href)) {
			$pconly_html = '&' . KS_PCONLY_SITE_CLASS . '=true';
		} else {
			$pconly_html = '';
		}
		$redir_link = '<a href="' . ks_plugin_url(false) . KS_REDIR_URL . rawurlencode($href) . $pconly_html . '">';
		$direct_link = '<a href="' . attribute_escape($href) . '">';
		$colored_label = '<font color="' . ks_option('ks_external_link_color') . '">' . $label . '</font></a>';
		if (ks_is_loggedin()) {
			$link_html = $icon . $redir_link . $colored_label;
		} elseif (preg_match('/(^| )' . preg_quote(KS_KTAI_SITE_CLASS) . '( |$)/', $class[2]) || $this->has_mobile_sites($href)) {
			$link_html = $direct_link . $label . '</a>';
		} elseif ($this->parent->get('use_redir')) {
			$link_html = $icon . $redir_link . $colored_label;
		} else {
			$link_html = $direct_link . $colored_label;
		}
		$link_html = apply_filters('external_link/ktai_style.php', $link_html, $href, $label);
	}
	return $link_html;
}

// ==================================================
private function has_mobile_sites($url) {
	if (preg_match($this->none_mobile_pat, $url)) {
		return false;
	}
	return preg_match($this->mobile_pat, $url);
}

// ==================================================
private function none_mobile_sites($url) {
	return preg_match($this->none_mobile_pat, $url);
}

/* ==================================================
 * @param	string  $content
 * @return	string  $content
 */
public function convert_images($content) {
	for ($offset = 0, $replace = 'X'; 
	     preg_match('!<img ([^>]*?)src=([\'"])([^\\\\]*?(\\\\.[^\\2\\\\]*?)*)\\2([^>]*?) ?/?>!', $content, $img, PREG_OFFSET_CAPTURE, $offset) ; 
	     $offset += strlen($replace))
	{
		$orig    = $img[0][0];
		$offset  = $img[0][1];
		$q       = $img[2][0];
		$src     = $img[3][0];
		$attr    = $img[1][0] . $img[5][0];
		$replace = $orig;
		if (preg_match('/local$/', $img[1][0])) { // ezweb pict chars
			continue;
		}
		list($replace, $has_image) = $this->image_to_link($orig, $src, $q, $attr);
		$replace = apply_filters('image_to_link/ktai_style.php', $replace, $orig, $src);
		if (! is_null($replace)) {
			$content = substr_replace($content, $replace, $offset, strlen($orig));
			if ($has_image) {
				$this->added_image();
			}
		} else {
			$offset += strlen($orig);
		}
	}
	return $content;
}

/* ==================================================
 * @param	string  $html
 * @param	string  $src
 * @param	string  $q
 * @param	string  $attr
 * @return	string  $replace
 * @return	boolean $has_image
 */
private function image_to_link($html, $src, $q, $attr) {
	$replace = $html;
	$has_image = false;
	$has_class = preg_match('/class=([\'"])([^\\\\]*?(\\\\.[^\\1\\\\]*?)*)\\1/', $attr, $class);
	if (preg_match('/alt=([\'"])([^\\\\]*?(\\\\.[^\\1\\\\]*?)*)\\1/', $attr, $a)) {
		$alt = stripslashes($a[2]);
		if (empty($alt) && (! $has_class || ! preg_match('/(^| )(wp-image-\d+)( |$)/', $class[2]))) {
			$replace = ''; // hide images if the alt string is empty. 
			return array($replace, $has_image);
		}
	} else {
		$alt = NULL;
	}
	if (empty($alt)) {
		if (preg_match('/title=([\'"])([^\\\\]*?(\\\\.[^\\1\\\\]*?)*)\\1/', $attr, $title)) {
			$alt = stripslashes($title[2]);
		} else {
			$url = parse_url($src);
			$alt = basename($url['path']);
		}
	}
	if (preg_match('/(width|height)=(([\'"])1\\3|1\b)/', $attr)) {
		$replace = ''; // hide 1 pixel width/height images.
	} elseif ($has_class && preg_match('/(^| )(wp-smiley|' . preg_quote(KS_KTAI_SITE_CLASS) . ')( |$)/', $class[2])) {
		$short_src = preg_replace($this->host_pat, '/', $src);
		$replace = "<img src=$q$short_src$q alt=" . '"' . addslashes($alt) . '" class="' . $class[2] . '" />';
	} elseif (preg_match('!' . preg_quote($this->wp_plugin_url, '!') . '!', $src)) { // plug-in's icon or what
		if ($this->parent->get('show_plugin_icon')) {
			$has_image = true;
			$replace = ks_is_image_inline() ? preg_replace('!(src=[\'"])https?://[^/]*!', '$1', $html) : "[$alt]";
		} else {
			$replace = "[$alt]";
		}
	} elseif (preg_match('/filesize="(\d*)"/', $attr, $filesize)) { // original image for thumbnail passed by convert_links()
		$cache_size = $this->parent->get('cache_size');
		if ($filesize[1] && $cache_size > 0 && $filesize[1] > $cache_size) {
			$replace = '<font color="' . KS_SIZE_EXCEED_COLOR . '">' . $alt . '</font>]';
		} else {
			$replace = '<a href=' . "$q$src$q" . '>' . $alt . '</a>]';
		}

	} else { // normal image or thumbnail image
		if (preg_match($this->wpurl_pat, $src)) {
			list($thumbpath, $thumburl, $img_path) = $this->check_thumbnail($src, $this->wpurl_pat, $this->basepath, $this->wpurl);
		} elseif (preg_match($this->host_pat, $src) && $_SERVER['DOCUMENT_ROOT']) {
			list($thumbpath, $thumburl, $img_path) = $this->check_thumbnail($src, $this->host_pat, $_SERVER['DOCUMENT_ROOT'] . '/', '/');
		} else {
			list($thumbpath, $thumburl, $img_path) = array(NULL, NULL, NULL);
		}
		if (defined('KS_SHRINKAGE_DEBUG') && is_ks_error($thumbpath)) {
			$replace = '[[' . $thumbpath->getMessage() . ']]';
		} elseif (ks_is_image_inline() && $thumburl) {
			$replace = '<img src="' . $thumburl . '" alt="' . $alt . '" />';
			$replace .= preg_match('/has_orig="true"/', $attr) ? '[' : '';
			$has_image = true;
		} else {
			$replace = '[<img localsrc="94" alt="' . __('IMAGE:', 'ktai_style') . '" />';
			if (! $img_path) { // link to a image of external sites
				$replace .= '<a href=' . "$q$src$q" . '>' . $alt . '</a>';
			} elseif (! is_dir($img_path) && $size = @filesize($img_path) && $size <= $this->parent->get('cache_size')) { // link to the image
				$replace .= '<a href=' . "$q$src$q" . '>' . $alt . '</a>';
				$has_image = ! is_ks_error($thumbpath);
			} elseif ($thumbpath && $thumburl) { // link to a thumbnail
				$replace .= '<a href=' . "$q$thumburl$q" . '>' . $alt . '</a>';
				$has_image = ! is_ks_error($thumbpath);
			} else { // no link to images
				$replace .= '<font color="' . KS_SIZE_EXCEED_COLOR . '">' . $alt . '</font>';
			}
			$replace .= preg_match('/has_orig="true"/', $attr) ? ' | ' : ']';
		}
	}
	return array($replace, $has_image);
}

/* ==================================================
 * @param	string  $img_src
 * @param	string  $url_pat
 * @param	string  $path_prefix
 * @param	string  $short_url
 * @return	string  $thumbpath
 * @return	string  $thumburl
 * @return	string  $img_path
 */
private function check_thumbnail($img_src, $url_pat, $path_prefix, $short_url) {
	$img_path = preg_replace($url_pat, $path_prefix, $img_src);
	preg_match('!^(cropped-)?(.*?)(\.thumbnail|-\d+x\d+)?(\.[^.]+)?$!', basename($img_path), $file);
	$orig = dirname($img_path) . '/' . $file[2] . @$file[4];
	if (isset($file[3]) && file_exists($orig)) {		
		$target = $orig; // Use the original image to make a smaller thumbnail.
		$result = $this->create_alter_image($orig, false);
		if (defined('KS_SHRINKAGE_DEBUG') && is_ks_error($result)) {
			return array($result, NULL, $img_path);
		}
	} else {
		$target = $img_path;
	}

	$thumbpath = dirname($img_path) . '/' . $file[2] . KS_THUMBNAIL_FILENAME . $file[4];
	if (! file_exists($thumbpath)) {
		$thumbpath = $this->create_thumbnail($target, $thumbpath);
		if (is_ks_error($thumbpath)) {
			return array($thumbpath, NULL, $img_path);
		}
	}
	$thumburl = preg_replace('!^' . preg_quote($path_prefix, '!') . '!', $short_url, $thumbpath);
	return array($thumbpath, $thumburl, $img_path);
}

/* ==================================================
 * @param	string  $img_path
 * @param	string  $thumbpath
 * @return	mix     $result
 */
private function create_thumbnail($img_path, $thumbpath) {
	$size = $this->create_alter_image($img_path, true);
	if (is_ks_error($size)) {
		return $size;
	}
	try {
		$width  = $size[0];
		$height = $size[1];
		$type   = $size[2];
		$image  = $size['image'];
		if ($width <= KS_THUMBNAIL_MAX_SIZE && $height <= KS_THUMBNAIL_MAX_SIZE) { // No need to make thumbnail
			return $img_path;
		}
		if ($width > $height) {
			$image_ratio = $width / KS_THUMBNAIL_MAX_SIZE;
			$new_width  = KS_THUMBNAIL_MAX_SIZE;
			$new_height = $height / $image_ratio;
		} else {
			$image_ratio = $height / KS_THUMBNAIL_MAX_SIZE;
			$new_height = KS_THUMBNAIL_MAX_SIZE;
			$new_width = $width / $image_ratio;
		}
		$thumbnail = @imagecreatetruecolor($new_width, $new_height);
		if (! $thumbnail) {
			throw new KS_Error('Cannot initialize for thumbnail');
		}
		if (function_exists('imageantialias')) {
			imageantialias($thumbnail, true);
		}
		if (! imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height)) {
			throw new KS_Error('Resample failed');
		}
	
		// move the thumbnail to it's final destination
		$other_path = NULL;
		$result_other = NULL;
		switch ($type) {
		case IMAGETYPE_GIF:
			$result = @imagegif($thumbnail, $thumbpath);
			if ($result) {
				$other_path = preg_replace('|\.gif$|i', '.png', $thumbpath);
				$result_other = @imagepng($thumbnail, $other_path);
			}
			break;
		case IMAGETYPE_PNG:
			$result = @imagepng($thumbnail, $thumbpath);
			if ($result) {
				$other_path = preg_replace('|\.png$|i', '.gif', $thumbpath);
				$result_other = @imagegif($thumbnail, $other_path);
			}
			break;
		case IMAGETYPE_JPEG:
		default:
			$result = @imagejpeg($thumbnail, $thumbpath);
		break;
		}
		imagedestroy($thumbnail);
		if (! $result || ! file_exists($thumbpath)) {
			throw new KS_Error('Thumbnail file not written');
		}
		chmod($thumbpath, 0646);
		if ($other_path && $result_other) {
			chmod($other_path, 0646);
		}
		return $thumbpath;
	
	} catch (KS_Error $e) {
		return $e;
	}
}

/* ==================================================
 * @param	string   $img_path
 * @param	boolean  $return_image
 * @return	resource $image
 */
private function create_alter_image($img_path, $return_image = true) {
	try {
		if (! function_exists('imagecreatetruecolor')) {
			throw new KS_Error('GD not available');
		}
		if (empty($img_path)) {
			throw new KS_Error('No file name');
		}
		if (! file_exists($img_path)) {
			throw new KS_Error('No such a file:' . $img_path);
		}		
		$size = getimagesize($img_path);
		if (! $size) {
			throw new KS_Error('Cannot access to image:' . $img_path);
		}
		$width  = $size[0];
		$height = $size[1];
		$type   = $size[2];
		if ($width <= 0 || $height <= 0) {
			throw new KS_Error('Zero size image');
		}
		$other_path = NULL;
		$result = NULL;
		switch ($type) {
		case IMAGETYPE_GIF:
			$image = imagecreatefromgif($img_path);
			$other_path = preg_replace('|\.gif$|i', '.png', $img_path);
			if ($image && ! file_exists($other_path)) {
				$result = @imagepng($image, $other_path);
			}
			break;
		case IMAGETYPE_PNG:
			$image = imagecreatefrompng($img_path);
			$other_path = preg_replace('|\.png$|i', '.gif', $img_path);
			if ($image && ! file_exists($other_path)) {
				$result = @imagegif($image, $other_path);
			}
			break;
		case IMAGETYPE_JPEG:
			if ($return_image) {
				$image = imagecreatefromjpeg($img_path);
			}
			break;
		default:
			throw new KS_Error(sprintf('Can\'t handle image type "%1$s" of file: %2$s', $size['mime'], $img_path));
			break;
		}
		if ($return_image && ! $image) {
			throw new KS_Error('Invalid image file: ' . $img_path);
		}
		if ($other_path && $result) {
			chmod($other_path, 0646);
		}
		if ($result === false) {
			throw new KS_Error('Cannot write PNG/GIF image: ' . $other_path);
		}
		if ($return_image) {
			$size['image'] = $image;
			return $size;
		} else {
			return $result;
		}

	} catch (KS_Error $e) {
		return $e;
	}
}

/* ==================================================
 * @param	string  $buffer
 * @return	string  $buffer
 */
public function strip_styles_scripts($buffer) {
	$buffer = preg_replace('!<(script|style)[^>]*>.*?</\\1>!s', '', $buffer);
	$buffer = preg_replace('|<!--.*?-->|', '', $buffer);
	return $buffer;
}

/* ==================================================
 * @param	string  $content
 * @return	string  $content
 */
public function shrink_content($content) {
	$content = preg_replace('!<del[^>]*>.*?</del>!s', '', $content);
	$content = $this->strip_styles_scripts($content);
	$content = $this->parent->filter_tags($content);
	$content = $this->convert_links($content);
	$content = $this->convert_images($content);
	return $content;
}

/* ==================================================
 * @param	string  $text
 * @param	string  $text
 * Based on wp_trim_excerpt at formatting.php of WP 2.5
 */
function trim_excerpt($text) { // Fakes an excerpt if needed
	if ( '' == $text ) {
		$text = get_the_content('');
		$text = $this->parent->filter_tags($text);
		$text = preg_replace('!<del[^>]*>.*?</del>!s', '', $text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text);
		$excerpt_length = 300;
		if (strlen($text) > $excerpt_length) {
			$text = mb_strcut($text, 0, $excerpt_length) . '[...]';
		}
	}
	return $text;
}

/* ==================================================
 * @param	int     $num
 * @param	boolean $rest
 * @param	string  $post_password
 * @return	string  $output
 * @return	int     $page_num
 */
private function get_split_page_navi($num, $rest, $post_password) {
	$link = htmlspecialchars(remove_query_arg('kp', $_SERVER['REQUEST_URI']), ENT_QUOTES);
	$link .= (strpos($link, '?') === false) ? '?kp=': '&amp;kp=';
	$del_accesskey = '';
	if ($num == 2) {
		$prev = _internal_link(preg_replace('/(\?|&(amp;)?)kp=/', '', $link), '*', '', __('*.Prev', 'ktai_style'), $post_password) . ' | ';
		$del_accesskey .= '*';
	} elseif ($num >= 3) {
		$prev = _internal_link($link . intval($num -1), '*', '', __('*.Prev', 'ktai_style'), $post_password) . ' | ';
		$del_accesskey .= '*';
	} else {
		$prev = '';
	}
	if ($rest) {
		$next = ' | ' . _internal_link($link . intval($num +1), '#', '', __('#.Next', 'ktai_style'), $post_password);
		$del_accesskey .= '#';
	} else {
		$next = '';
	}
	$output = mb_convert_encoding(sprintf(apply_filters('split_page_navi/ktai_style.php', __('<div align="center">Splitting the page for mobile: %spage %d%s</div>', 'ktai_style'), $prev, $num, $next), $prev, $num, $next), $this->parent->get('charset'), get_bloginfo('charset')); 
	return array($output, $del_accesskey);
}

/* ==================================================
 * @param	string  $balanced
 * @param	string  $start_tags
 * @return	string  $start_tags
 */
private function detect_nesting_list($balanced, $start_tags) {
	preg_match_all('|</?[ou]l([^>]*)>|', $balanced, $lists, PREG_OFFSET_CAPTURE);
	$open[0] = '<ol>';
	do {
		$close = array_pop($lists[0]);
	} while (strpos($close, '</ol>') !== false);
	$max_ol_level = preg_match_all('/<ol>/', $start_tags, $ol);
	for ($ol_level = 0 ; $ol_level < $max_ol_level ; $ol_level++) {
		$inside[$ol_level][0] = array('start' => strlen($close[0]), 'end' => $close[1]);
	}
	$level = 0;
	$ol_level = 0;
	$below_level[$ol_level] = $level +1;
	$entered[$ol_level] = 1; // to make sure
	foreach (array_reverse($lists[0]) as $l) {
		if (strpos($l[0], '</') !== false) {
			$level++;
			if ($level == $below_level[$ol_level]) {
				$inside[$ol_level][0]['start'] = $l[1] + strlen($l[0]);
			}
			if (strpos($l[0], '</ol>') !== false && $ol_level < $max_ol_level -1 && ! isset($entered[$ol_level +1])) {
				$ol_level++;
				$below_level[$ol_level] = $level +1;
				$inside[$ol_level][0]['end'] = $l[1];
				$entered[$ol_level] = 1;
			}
		} elseif ($level <= 0) {
			$open[0] = $l[0];
			$inside[$ol_level][0]['start'] = $l[1] + strlen($l[0]);
			break;
		} else {
			if (strpos($l[0], '<ol') !== false && $level < $below_level[$ol_level] && @$entered[$ol_level] == 1) {
				$open[$ol_level] = $l[0];
				$inside[$ol_level][0]['start'] = $l[1] + strlen($l[0]);
				$entered[$ol_level] = 2;
				$ol_level--;
			}
			if ($level == $below_level[$ol_level]) {
				array_unshift($inside[$ol_level], array('end' => $l[1]));
			}
			$level--;
		}
	}
	for ($ol_level = 0 ; $ol_level < $max_ol_level ; $ol_level++) {
		if (preg_match('/start=[\'"](\d+)[\'"]/', $open[$ol_level], $start)) {
			$start_num = intval(@$start[1]);
		} else {
			$start_num = 1;
		}
		$inside_html = '';
		foreach ($inside[$ol_level] as $offset) {
			$inside_html .= substr($balanced, $offset['start'], $offset['end'] - $offset['start']);
		}
		$start_num += preg_match_all('/<li>/', $inside_html, $items);
		$ol_pos = strpos($start_tags, '<ol>'); // should be matched
		if (strpos($start_tags, '<li>', $ol_pos) == $ol_pos + 4) {
			$start_num -= 1; // use same number for splited item
		}
		if ($start_num > 1) {
			$start_html = ' start="' . $start_num . '"';
		} else {
			$start_html = ' start="1"';
		}
		preg_match('/ type=[\'"][^\'"]+[\'"]/', $open[$ol_level], $type);
		$start_tags = preg_replace('/<ol>/', '<ol' . $start_html . @$type[0] . '>', $start_tags, 1);
	}
	return str_replace(' start="1"', '', $start_tags);
}

/* ==================================================
 * @param	string  $buffer
 * @param	int     $page_num
 * @return	string  $paged_content
 */
public function split_page($buffer, $page_num) {
	if ($page_num > KS_MAX_PAGE_NUM) {
		$page_num = KS_MAX_PAGE_NUM;
	} elseif ($page_num < 1) {
		$page_num = 1;
	}

	if (strpos($buffer, KS_START_PAGING) === false) {
		$buffer = preg_replace('/(<body[^>]*>)/', "\$1\n" . KS_START_PAGING, $buffer);
	}
	if (strpos($buffer, KS_END_PAGING) === false) {
		if (preg_match('!<hr [^/>]*/>\s*<a name="tail"!', $buffer)) {
			$buffer = preg_replace('!(<hr [^/>]*/>\s*<a name="tail")!', KS_END_PAGING . "\n\$1", $buffer);
		} else {
			$buffer = preg_replace('!(</body>)!', KS_END_PAGING . "\n\$1", $buffer);
		}
	}

	list($header, $buffer) = preg_split('/' . preg_quote(KS_START_PAGING, '/') . '\n*/', $buffer, 2);
	list($buffer, $footer) = preg_split('/\n*' . preg_quote(KS_END_PAGING, '/') . '/', $buffer, 2);
	$buffer = preg_replace('/>[ \t]*\n+[ \t]*</', '><', $buffer);
	if (preg_match('/<input type="hidden" name="post_password" value="(.*?)"/', $buffer, $match)) {
		$post_password = $match[1];
	} else {
		$post_password = '';
	}
	list($navi, $del_accesskey) = $this->get_split_page_navi(101,true, $post_password);
	$page_size = $this->parent->get('page_size') - strlen("$header$navi<hr /><hr />$navi$footer") - 32; // 32-byte is space for adding tags by force_balance_tags()
	if ($page_size < 256) { // too small
		$header = preg_replace('/(<body[^>]*>)/', '$1' . KS_START_PAGING, $header);
		list($header, $move2body) = explode(KS_START_PAGING, $header, 2);
		$buffer = $move2body . $buffer . $footer;
		$footer = '';
		$page_size = $this->parent->get('page_size') - strlen($header . $navi . '<hr /><hr />' . $navi . $footer) - 32;
	}

	$start_tags = '';
	$terminator = '<!--KS_TERMINATOR_' . md5(uniqid()) . '-->';
	$marker = 0;
	$buffer_length = strlen($buffer);
	for ($count = 0 ; $count < $page_num ; $count++) {
		$fragment = _cut_html($buffer, $page_size, $marker, $this->parent->get('charset'));
		$fragment = preg_replace('/\x1b\$[GEFOPQ]?$/', '', $fragment);
		if (preg_match('/(\x1b\$[GEFOPQ])[!-z]+$/', $fragment, $pict_sequence)) {
			$complemention = "\x0f"; // complete softbank pictgram shift-in
		} else {
			$complemention = '';
		}
		$quoted = str_replace(array("<\x0f", ">\x0f"), array("&lt;\x0f", "&gt;\x0f"), $start_tags . $fragment . $terminator); // protect softbank pictograms
		$balanced = force_balance_tags($quoted);
		preg_match("/$terminator(.*)\$/", $balanced, $added_html);
		$complemented = preg_replace('/\x1b\$[GEFOPQ]?\x1b/', '', $start_tags . $fragment . $complemention . @$added_html[1]);
		if (preg_match_all('!</([^<>]*)>!', @$added_html[1], $added_tags)) {
			$start_tags = '<' . implode('><', array_reverse($added_tags[1])) . '>'; // store complemented tags to next fragment
			if (strpos($start_tags, '<ol>') !== false) {
				$start_tags = $this->detect_nesting_list($balanced, $start_tags);
			}
			$start_tags .= (isset($pict_sequence[1]) ? $pict_sequence[1] : '');
		} else {
			$start_tags = (isset($pict_sequence[1]) ? $pict_sequence[1] : '');
		}
		$marker += strlen($fragment);
		if ($marker >= $buffer_length) {
			$count++;
			break;
		}
	}

	if (strlen($fragment) < $buffer_length && isset($added_html[1])) {
		list($navi, $del_accesskey) = $this->get_split_page_navi($count, ($marker +1 < $buffer_length), $post_password);
		if ($del_accesskey) { // delete redundant access keys
			$complemented = preg_replace('/(<(a|label) [^>]*?) accesskey="[' . $del_accesskey . ']">/', '$1>', $complemented);
		}
		return "$header$navi<hr />$complemented<hr />$navi$footer";
	} else {
		return "$header$buffer$footer";
	}
}

/* ==================================================
 * @param	string  $content
 * @return	string  $content
 */
public function trim_images($content) {
	if ($this->parent->get('cache_size') > 0) {
		$total_size = strlen($content);
		for ($offset = 0, $replace = 'X'; 
		     preg_match('!<img ([^>]*?)src=([\'"])([^\\\\>]*?(\\\\.[^\\\\>]*?)*)\\2([^>]*?) ?/?>!', $content, $i, PREG_OFFSET_CAPTURE, $offset); //"syntax highlighting fix
		     $offset += strlen($replace)) {
			$offset  = $i[0][1];
			$replace = $i[0][0];
			$src     = $i[3][0];
			$attr    = $i[1][0] . $i[5][0];
			if (preg_match($this->wpurl_pat, $src)) {
				$imagesize = @filesize(preg_replace($this->wpurl_pat, $this->basepath, $src));
				if ($imagesize) {
					$total_size += $imagesize;
				}
				if ($total_size > $this->parent->get('cache_size')) {
					if (preg_match('/alt=([\'"])([^\\\\>]*?(\\\\.[^\\\\>]*?)*)\\1/', $attr, $a)) { //"syntax highlighting fix
						$replace = $a[2];
					} else {
						$replace = basename($src);
					}
					$content = substr_replace($content, $replace, $offset, strlen($i[0][0]));
				}
			}
		}
	}
	return $content;
}

/* ==================================================
 * @param	string  $content
 * @param	array   $tags
 * @param	array   $args
 * @return	string  $content
 */
public function shrink_tag_cloud($content, $tags, $args) {
	for ($offset = 0, $replace = 'X' ; 
	     preg_match('!<a href=([\'"])([^\\\\]*?(\\\\.[^\\1\\\\]*?)*)\\1([^>]*?)>(.*?)</a>!', $content, $l, PREG_OFFSET_CAPTURE, $offset); 
	     $offset += strlen($replace))
	{
		$orig    = $l[0][0];
		$offset  = $l[0][1];
		$q       = $l[1][0];
		$href    = $l[2][0];
		$attr    = $l[4][0];
		$label   = $l[5][0];
		$replace = $orig;
		preg_match('/ style=([\'"])[^\\\\]*?(\\\\.[^\\1\\\\]*?)*\\1/', $attr, $style);
		$replace = '<a href=' . $q . $href . $q . $style[0] . '>' . $label . '</a>';
		$content = substr_replace($content, $replace, $offset, strlen($orig));
	}
	return $content;
}

/* ==================================================
 * @param	string  $buffer
 * @return	string  $buffer
 */
public function add_url_inline_image($buffer) {
	if ($this->parent->ktai->get('image_inline_default') == $this->parent->ktai->get('image_inline')) {
		return $buffer;
	}
	$value = ks_is_image_inline() ? 'inline' : 'link';
	for ($offset = 0, $replace = 'X' ; 
	     preg_match('!<a ([^>]*?)href=([\'"])([^\\\\]*?(\\\\.[^\\2\\\\]*?)*)\\2([^>]*?)>!', $buffer, $l, PREG_OFFSET_CAPTURE, $offset) ; 
	     $offset += strlen($replace))
	{
		$orig    = $l[0][0];
		$offset  = $l[0][1];
		$q       = $l[2][0];
		$href    = $l[3][0];
		$href    = _quoted_remove_query_arg('img', $href);
		$attr1   = $l[1][0];
		$attr2   = $l[5][0];
		$replace = $orig;
		if (! preg_match($this->url_pat, $href) || preg_match($this->wpcontent_pat, $href) || preg_match('/id="inline"/', $attr1 . $attr2)) {
			continue;
		}
		$href .= (strpos($href, '?') === false ? '?' : '&amp;' ) . "img=$value";
		$replace = "<a {$attr1}href=$q$href$q$attr2>"; 
		$buffer = substr_replace($buffer, $replace, $offset, strlen($orig)); // convert links		
	}
	return $buffer;
}

// ===== End of class ====================
}

/* ==================================================
 *   KS_Error class
   ================================================== */

if (! class_exists('KS_Error')) :

function is_ks_error($thing) {
	return (is_object($thing) && is_a($thing, 'KS_Error'));
}

class KS_Error extends Exception {

public function setCode($code) {
	$this->code = $code;
}

// ===== End of class ====================
}
endif;

?>
