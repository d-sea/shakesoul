<?php
/* ==================================================
 *   Redirect to external sites
   ================================================== */

define ('KS_REDIR_URL_PARAM', 'url');
define ('KS_PCONLY_SITE_PARAM', 'pconly');

$wpload_error = 'Could not open the redirect page because custom WP_PLUGIN_DIR is set.';
require dirname(__FILE__) . '/wp-load.php';
nocache_headers();

if (! isset($Ktai_Style) || ! $Ktai_Style->is_ktai()) {
	wp_redirect(get_bloginfo('url'));
} else {
	$Redir = new Ktai_Style_Redir;
}
exit();

/* ==================================================
 *   Ktai_Style_Redir class
   ================================================== */

class Ktai_Style_Redir {
	private $url = NULL;

// ==================================================
public function __construct() {
	if (isset($_GET[KS_REDIR_URL_PARAM]) && ! empty($_GET[KS_REDIR_URL_PARAM])) {
		$this->url = clean_url($_GET[KS_REDIR_URL_PARAM]);
		if (! isset($_GET[KS_PCONLY_SITE_PARAM]) || $_GET[KS_PCONLY_SITE_PARAM] != 'true') {
			$mobile_url = $this->discover_mobile($this->url);
			if ($mobile_url) {
				wp_redirect($mobile_url);
				exit;
			}
		}
		$this->output();
	} else {
		wp_redirect(get_bloginfo('url'));
	}
}

/* ==================================================
 * @param	none
 * @return	string   $mobile_url
 * based on discover_pingback_server_uri() at comment.php of WP 2.5.1
 */
private function discover_mobile($url) {
	global $wp_version;

	$byte_count = 0;
	$timeout_bytes = 32768;
	$headers = '';
	extract(parse_url($url), EXTR_SKIP);
	if ( !isset($host) ) {
		// Not an URL. This should never happen.
		return false;
	}
	$path  = ( isset($path) )  ? $path        : '/';
	$path .= ( isset($query) ) ? '?' . $query : '';
	if (isset($scheme) && $scheme == 'https') {
		$host_sock = 'ssl://' . $host;
		$host_req  = $host;
		if (isset($port)) {
			$host_req .= ':' . $port;
		} else {
			$port = 443;
		}
	} else {
		$host_sock = $host;
		$host_req  = $host;
		if (isset($port)) {
			$host_req .= ':' . $port;
		} else {
			$port  = 80;
		}
	}

	// Try to connect to the server at $host
	$fp = @fsockopen($host_sock, $port, $errno, $errstr, 2.0);
	if ( !$fp ) // Couldn't open a connection to $host
		return false;

	// Send the GET request
	$request[] = 'GET ' . $path . ' HTTP/1.1';
	$request[] = 'Host: ' . $host_req;
	$request[] = 'User-Agent: WordPress/' . $wp_version . '; ' . get_bloginfo('url');
	// ob_end_flush();
	fputs($fp, implode("\r\n", preg_replace('/[\r\n]/', '', $request)) . "\r\n\r\n");

	// Check the content type
	while (! feof($fp)) {
		$line = fgets($fp, 512);
		if (trim($line) == '')
			break;
		$headers .= trim($line)."\n";
		if ( strpos(strtolower($headers), 'content-type: ') ) {
			preg_match('#content-type: (.+)#is', $headers, $matches);
			$content_type = trim($matches[1]);
		}
	}

	if (preg_match('#(image|audio|video|model)/#is', $content_type)) {
	 // Not an (x)html, sgml, or xml page, no use going further
		fclose($fp);
		return false;
	}

	$is_chunked = preg_match('/^Transfer-Encoding: chunked$/im', $headers);
	$contents = '';
	$offset = 0;
	while (! feof($fp)) {
		$line = $this->fgets($fp, $line);
		if (is_null($line)) {
			break;
		} elseif (empty($line)) {
			continue;
		}
		$contents .= trim($line);
		if (preg_match_all('!<link([^>]*?)media=([\'"])handheld\\2([^>]*)/?>!i', $contents, $links, PREG_SET_ORDER | PREG_OFFSET_CAPTURE, $offset)) {
			foreach ($links as $l) {
				$attr = $l[1][0] . $l[3][0];
				if (! preg_match('/rel=([\'"])alternate\\1/i', $attr) || ! preg_match('/href=([\'"])(.*?)\\1/i', $attr, $href)) {
					continue;
				}
				if (! preg_match('!^(https?:/)?/!', $href[2])) { // relarive URL
					$href[2] = $url . $href[2];
				}
				if (function_exists('sanitize_url')) {
					$mobile_url = sanitize_url($href[2]);
				} else {
					$mobile_url = $href[2];
				}
				if ($mobile_url) {
					fclose($fp);
					return $mobile_url;
				}
			}
			$offset = $links[count($links) -1][0][1] + strlen($links[count($links) -1][0][0]);
		}
		if (preg_match('!(</head>|<body[ >])!i', $contents)) {
			break;
		}
		$byte_count += strlen($line);
		if ( $byte_count > $timeout_bytes ) {
			// It's no use going further, there probably isn't any pingback
			// server to find in this file. (Prevents loading large files.)
			break;
		}
	}
	// We didn't find anything.
	fclose($fp);
	return false;
}

/* ==================================================
 * @param	resource $fp
 * @param	boolean  $is_chunked
 * @return	string   $line
 */
private function fgets($fp, $is_chunked) {
	$line = fgets($fp, 4096);
	if (! trim($line)) {
		return '';
	} elseif ($is_chunked && preg_match('/^[0-9a-fA-F]+$/', trim($line))) {
		$length = hexdec($line);
		if ($length <= 0) {
			return NULL;
		}
		for ($chunk = '' ; strlen($chunk) < $length ; $chunk .= $bytes) {
			if (feof($fp)) {
				break;
			}
			$bytes = fread($fp, $length - strlen($chunk));
			if (empty($bytes)) {
				break;
			}
		}
		$line = $chunk;
	}
	return $line;
}

// ==================================================
private function output() {
	global $Ktai_Style;
	$charset = $Ktai_Style->ktai->get('charset');
	$title = __('Confirm connecting to external sites', 'ktai_style');
	$full_url = $this->url;
	if (preg_match('|^/|', $this->url)) {
		$full_url = preg_replace('|^(https?://[^/]*)/.*$|', '$1', get_bloginfo('url')) . $this->url;
	}
	$html = '<p>' . __('You are about to visit a website for PC:', 'ktai_style') . "<br />\n"
	      . '<a href="' . attribute_escape($this->url) . '">' . attribute_escape($full_url) . "</a>";
	if ($Ktai_Style->is_ktai() == 'KDDI' && $Ktai_Style->get('type') == 'WAP2.0') {
		$html .= '<br />'. sprintf(__('(<a %s>View the site by PC Site Viewer.</a>)', 'ktai_style'), ' href="device:pcsiteviewer?url=' . attribute_escape($full_url) . '"');
	} elseif ($Ktai_Style->is_ktai() == 'DoCoMo' && $Ktai_Style->get('type') == 'FOMA') {
		$html .= '<br />'. sprintf(__('(<a %s>View the site by Full Browser.</a>)', 'ktai_style'), 'href="' . attribute_escape($this->url) . '" ifb="' . attribute_escape($full_url) . '"');
	}
	$html .= "</p>\n<p>" . __('If you are sure, follow above link. If not, go to the previous page with browser\'s back button.', 'ktai_style') . '</p>';
	$html .='<form action=""><div>' . __('To copy the URL, use below text field:', 'ktai_style') . '<br /><input type="text" name="url" size="80" maxlength="255" value="' . attribute_escape($full_url) . '" /></div></form>';
	nocache_headers();
	$Ktai_Style->ks_die(apply_filters('redir/ktai_style.php', $html, $full_url), $title, false);
}

// ===== End of class ====================
}
?>