<?php
/* ==================================================
 *   Ktai Template Tags
   ================================================== */

require dirname(__FILE__) . '/shrinkage.php';

/* ==================================================
 * @param	string  $key
 * @param	string  $query
 * @return	string  $query
 */
function _quoted_remove_query_arg($key, $query) {
	$query = preg_replace(array('/&amp;/', '/&#038;/'), array('&', '&'), $query);
	$query = remove_query_arg($key, $query);
	$query = preg_replace('/&(?:$|([^#])(?![a-z1-4]{1,8};))/', '&amp;$1', $query);
	return $query;
}

/* ==================================================
 * @param	none
 * @return	boolean $password_is_ok
 */
function _confirmed_post_password() {
	global $post;
	if (empty($post->post_password)) {
		return true;
	}
	return (stripslashes($_POST['post_password']) == $post->post_password);
}

/* ==================================================
 * @param	string  $accesskey
 * @return	string  $output
 */
function ks_accesskey_html($accesskey) {
	if (strlen("$accesskey") == 1 && strpos('0123456789*#', "$accesskey") !== false) {
		$output = ' accesskey="' . $accesskey . '"';
	} else {
		$output = '';
	}
	return $output;
}

/* ==================================================
 * @param	string  $link
 * @param	string  $icon
 * @param	string  $label
 * @param	string  $post_password
 * @return	string  $output
 */
function _internal_link($link, $accesskey, $icon, $label, $post_password) {
	if ($post_password && _confirmed_post_password()) {
		$param = '';
		$url =  parse_url($link);
		$query = $url['query'];
		if ($query) {
			$param = '<input type="hidden" name="urlquery" value="' . htmlspecialchars($query, ENT_QUOTES) . '" />';
		}
		$output  = '<form method="post" action="' . attribute_escape($link) . '">' . $param . '<input type="hidden" name="post_password" value="' . htmlspecialchars($post_password, ENT_QUOTES) . '" />' . $icon . '<label' . ks_accesskey_html($accesskey) . '><input type="submit" name="submit" value="' . attribute_escape($label) . '" /></label></form>';
	} else {
		$output = $icon . '<a href="' . attribute_escape($link) . '"' . ks_accesskey_html($accesskey) . '>' . attribute_escape($label) . '</a>';
	}
	return $output;
}

/* ==================================================
 * @param	none
 * @return	srting  $type
 */
function ks_service_type() {
	global $Ktai_Style;
	return $Ktai_Style->is_ktai('type');
}

/* ==================================================
 * @param	none
 * @return	boolean $is_flat_rate
 */
function ks_is_flat_rate() {
	global $Ktai_Style;
	return $Ktai_Style->is_ktai('flat_rate');
}

/* ==================================================
 * @param	none
 * @return	boolean $in_network
 */
function ks_in_network() {
	global $Ktai_Style;
	return $Ktai_Style->ktai->in_network();
}

/* ==================================================
 * @param	string  $type
 * @return	boolean $is_home
 */
function ks_is_menu($type = NULL) {
	$is_menu = false;
	if (isset($type) && preg_match('/^[_a-z0-9]+$/', $type) && isset($_GET['menu'])) {
		$is_menu = ($type === $_GET['menu']);
	} else {
		$is_menu = isset($_GET['menu']);
	}
	return $is_menu;
}

/* ==================================================
 * @param	none
 * @return	boolean $is_front
 */
function ks_is_front() {
	global $paged;
	return (is_home() && ! ks_is_menu() && intval($paged) < 2);
}

/* ==================================================
 * @param	none
 * @return	boolean $is_comments_list
 */
function ks_is_comments_list() {
	if (isset($_GET['view']) && $_GET['view'] == 'co_list') {
		return true;
	} elseif (isset($_POST['view']) && $_POST['view'] == 'co_list') {
		return true;
	}
	return false;	
}

/* ==================================================
 * @param	none
 * @return	boolean $is_comment_post
 */
function ks_is_comment_post() {
	if (isset($_GET['view']) && $_GET['view'] == 'co_post') {
		return true;
	} elseif (isset($_POST['view']) && $_POST['view'] == 'co_post') {
		return true;
	}
	return false;	
}

/* ==================================================
 * @param	none
 * @return	boolean $is_comments
 */
function ks_is_comments() {
	return (ks_is_comments_list() || ks_is_comment_post());
}


/* ==================================================
 * @param	none
 * @return	boolean $is_comments
 */
function ks_is_image_inline() {
	global $Ktai_Style;
	return $Ktai_Style->ktai->get('image_inline');
}

/* ==================================================
 * @param	none
 * @return	int     $num_images
 */
function ks_added_image() {
	global $KS_Shrinkage;
	return $KS_Shrinkage->added_image();
}

/* ==================================================
 * @param	none
 * @return	int     $num_images
 */
function ks_has_inline_images() {
	global $KS_Shrinkage;
	return $KS_Shrinkage->has_inline_images();
}

/* ==================================================
 * @param	none
 * @return	none
 */
function ks_wp_head() {
	global $KS_Shrinkage;
	ob_start();
	do_action('wp_head');
	$buffer = ob_get_contents();
	ob_end_clean();
	echo $KS_Shrinkage->strip_styles_scripts($buffer);
	return;
}

/* ==================================================
 * @param	none
 * @return	none
 */
function ks_wp_footer() {
	global $KS_Shrinkage;
	ob_start();
	do_action('wp_footer');
	$buffer = ob_get_contents();
	ob_end_clean();
	echo $KS_Shrinkage->strip_styles_scripts($buffer);
	return;
}

/* ==================================================
 * @param	none
 * @return	none
 */
function ks_header() {
	global $Ktai_Style;
	$Ktai_Style->get_header();
	return;
}

/* ==================================================
 * @param	none
 * @return	none
 */
function ks_footer() {
	global $Ktai_Style;
	$Ktai_Style->get_footer();
	return;
}

/* ==================================================
 * @param	boolean $echo
 * @return	string  $url
 */
function ks_blogurl($echo = true) {
	global $KS_Shrinkage;
	$url = $KS_Shrinkage->get('url');
	if ($echo) {
		echo $url;
	}
	return $url;
}

/* ==================================================
 * @param	boolean $echo
 * @return	string  $url
 */
function ks_siteurl($echo = true) {
	global $KS_Shrinkage;
	$url = $KS_Shrinkage->get('wpurl');
	if ($echo) {
		echo $url;
	}
	return $url;
}

/* ==================================================
 * @param	boolean $echo
 * @return	string  $url
 */
function ks_plugin_url($echo = true) {
	global $Ktai_Style;
	$url = $Ktai_Style->strip_host($Ktai_Style->get('plugin_url'));
	if ($echo) {
		echo $url;
	}
	return $url;
}

/* ==================================================
 * @param	boolean $echo
 * @return	string  $url
 */
function ks_theme_url($echo = true) {
	$url = get_template_directory_uri() . '/';
	if ($echo) {
		echo $url;
	}
	return $url;
}

/* ==================================================
 * @param	boolean $echo
 * @return	string  $url
 */
function ks_admin_url($echo = true) {
	global $Ktai_Style;
	$url = '';
	if (class_exists('Ktai_Style_Admin')) {
		$url = ks_plugin_url(false) . KS_ADMIN_DIR . '/';
		if ($echo) {
			echo $url;
		}
	}
	return $url;
}

/* ==================================================
 * @param	string  $before
 * @param	string  $after
 * @param	boolean $echo
 * @return	string  $output
 */
function ks_login_link($before = ' | ', $after = '', $echo = true) {
	$output = '';
	if (class_exists('Ktai_Style_Admin')) {
		global $Ktai_Style;
		$output = $before . '<a href="' . ks_plugin_url(false) . 'login.php">' . __('Login') . '</a>' . $after;
		if ($echo) {
			echo $output;
		}
	}
	return $output;
}

/* ==================================================
 * @param	boolean $echo
 * @return	string  $url
 */
function ks_get_logout_url($echo = false) {
	if (class_exists('Ktai_Style_Admin')) {
		$url = ks_plugin_url(false) . 'login.php?action=logout&' . KS_SESSION_NAME . '=' . Ktai_Style_Admin::get_sid();
	} else { // Logged in from normal login form
		$url = get_bloginfo('wpurl') . 'wp-login.php?action=logout';
	}
	if ($echo) {
		echo attribute_escape($url);
	}
	return $url;
}

/* ==================================================
 * @param	none
 * @return	boolean $user_id
 */
function ks_is_loggedin() {
	$user = wp_get_current_user();
	return $user->ID;
}

/* ==================================================
 * @param	none
 * @return	none
 */
function ks_session_id_form() {
	if (class_exists('Ktai_Style_Admin')) {
		$sid = Ktai_Style_Admin::get_sid();
		if ($sid) {
			echo '<input type="hidden" name="' . KS_SESSION_NAME . '" value="' . htmlspecialchars($sid, ENT_QUOTES) . '" />';
		}
	}
}

/* ==================================================
 * @param	string   $version
 * @param	string   $operator
 * @return	boolean  $result
 */
function ks_check_wp_version($version, $operator = '>=') {
	global $Ktai_Style;
	return $Ktai_Style->check_wp_version($version, $operator);
}

/* ==================================================
 * @param	none
 * @return	none
 */
function ks_term_name() {
	global $Ktai_Style;
	echo wp_specialchars($Ktai_Style->get('term_name'));
}

/* ==================================================
 * @param	none
 * @return	none
 */
function ks_use_appl_xhtml() {
	global $Ktai_Style;
	if ($Ktai_Style->ktai->get('xhtml_head')) {
		$Ktai_Style->ktai->set('mime_type', 'application/xhtml+xml');
		echo $Ktai_Style->ktai->get('xhtml_head');
	} else {
?><html><?php
	}	
}

/* ==================================================
 * @param	none
 * @return	none
 */
function ks_applied_appl_xhtml() {
	global $Ktai_Style;
	return ($Ktai_Style->ktai->get('mime_type') == 'application/xhtml+xml');
}

/* ==================================================
 * @param	none
 * @return	none
 */
function ks_force_text_html() {
	global $Ktai_Style;
	$Ktai_Style->ktai->set('mime_type', 'text/html');
}

/* ==================================================
 * @param	none
 * @return	none
 */
function ks_mimetype() {
	global $Ktai_Style;
	echo wp_specialchars($Ktai_Style->get('mime_type'));
}

/* ==================================================
 * @param	none
 * @return	none
 */
function ks_charset() {
	global $Ktai_Style;
	echo wp_specialchars($Ktai_Style->get('iana_charset'));
}

/* ==================================================
 * @param	none
 * @return	none
 */
function ks_fix_encoding_form() {
?>
<input type="hidden" name="charset_detect" value="<?php _e('Encoding discriminant strings to avoid charset mis-understanding', 'ktai_style'); ?>" />
<?php
}

/* ==================================================
 * @param	none
 * @return	string  $charset
 */
function ks_detect_encoding() {
	return Ktai_Style::detect_encoding(@$_POST['charset_detect']);
}

/* ==================================================
 * @param	string  $key
 * @param	string  $charset
 * @return	string  $value
 */
function ks_mb_get_form($key, $charset = NULL) {
	if (! isset($_POST[$key])) {
		return NULL;
	}

	global $wpdb, $Ktai_Style;
	if (empty($charset)) {
		$charset = ks_detect_encoding();
	}
	$value = $_POST[$key];
	if ($Ktai_Style->compare_encoding($charset, $Ktai_Style->get('charset'))) {
		$value = $Ktai_Style->ktai->pickup_pics(stripslashes($value));
		if (! $Ktai_Style->get_option('ks_allow_pictograms')) {
			$value  = preg_replace('!<img localsrc="[^"]*" />!', '', $value);
		}
		$value = $wpdb->escape($value);
	}
	return mb_convert_encoding($value, get_bloginfo('charset'), $charset);
}

/* ==================================================
 * @param	none
 * @return	boolean is_required_term_id
 */
function ks_is_required_term_id() {
	global $Ktai_Style;
	return (! ks_is_loggedin() && ks_option('ks_require_term_id') && $Ktai_Style->ktai->get('sub_ID_available'));
}

/* ==================================================
 * @param	string  $action
 * @param	string  $method
 * @return	none
 */
function ks_require_term_id_form($action, $method = 'post') {
	global $Ktai_Style;
	$utn = '';
	if (! ks_is_loggedin() && ks_option('ks_require_term_id') && $Ktai_Style->is_ktai() == 'DoCoMo') {
		if ($Ktai_Style->ktai->get('sub_ID')) {
			$action .= ((strpos($action, '?') === false) ? '?' : '&') . 'guid=ON';
		} else {
			$utn = ' utn="utn"';
		}
	}
	if (strcasecmp($method, 'post') !== 0) {
		$method = 'get';
	}
	echo '<form method="' . $method . '" action="' . attribute_escape($action) . "\"$utn>";
}

/* ==================================================
 * @param	string  $value
 * @return	none
 */
function ks_inline_error_submit($value = NULL) {
	global $post;
	if ($post->post_password) {
		echo '<input type="hidden" name="post_password" value="' . htmlspecialchars($post->post_password, ENT_QUOTES) . '" />';
	}
	if (empty($value)) {
		$value = __('Say It!');
	}
	echo '<input type="submit" name="inline" value="' . attribute_escape($value) . '" />';
}

/* ==================================================
 * @param	none
 * @return	none
 */
function ks_do_comment_form_action() {
	global $post, $KS_Shrinkage;
	ob_start();
	do_action('comment_form', $post->ID);
	$form = ob_get_contents();
	ob_end_clean();
	echo $KS_Shrinkage->shrink_content($form);
}

/* ==================================================
 * @param	boolean $echo
 * @return	string  $title
 */
function ks_title($echo = true) {
	$title = trim(wp_title('', false)); 
	if (empty($title)) {
		$title = get_bloginfo('name');
	}
	if ($echo) {
		echo $title;
	}
	return $title;
}

/* ==================================================
 * @param	int     $more_link_text
 * @param	int     $stripteaser
 * @param	int     $more_file
 * @param	int     $strip_length
 * @return	none
 * based on the_content() at wp-includes/post-template.php of WP 2.2.3
 */
function ks_content($more_link_text = '(more...)', $stripteaser = 0, $more_file = '', $strip_length = 0) {
	$content = ks_get_content($more_link_text, $stripteaser, $more_file, $strip_length);
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	echo $content;
}

/* ==================================================
 * @param	int     $more_link_text
 * @param	int     $stripteaser
 * @param	int     $more_file
 * @param	int     $strip_length
 * @return	string  $output
 * based on get_the_content() at wp-includes/post-template.php of WP 2.2.3
 */
function ks_get_content($more_link_text = '(more...)', $stripteaser = 0, $more_file = '', $strip_length = 0) {
	global $id, $post, $more, $single, $page, $pages, $numpages;
	global $pagenow;
	$output = '';

	$need_password = ks_check_password();
	if ($need_password) {
		return $need_password;
	}

	if ( $more_file != '' )
		$file = $more_file;
	else
		$file = $pagenow; //$_SERVER['PHP_SELF'];

	if ( $page > count($pages) ) // if the requested page doesn't exist
		$page = count($pages); // give them the highest numbered page that DOES exist

	$content = $pages[$page-1];
	if ( preg_match('/<!--more(.*?)?-->/', $content, $matches) ) {
		$content = explode($matches[0], $content, 2);
		if ( !empty($matches[1]) && !empty($more_link_text) )
			$more_link_text = strip_tags(wp_kses_no_null(trim($matches[1])));
	} else {
		$content = array($content);
	}
	if ( (false !== strpos($post->post_content, '<!--noteaser-->') && ((!$multipage) || ($page==1))) )
		$stripteaser = 1;
	$teaser = $content[0];
	if ( ($more) && ($stripteaser) )
		$teaser = '';
	$output .= $teaser;
	if ( count($content) > 1 ) {
		if ( $more ) {
			$output .= '<a name="more-'.$id.'"></a>'.$content[1];
		} else {
			$output = balanceTags($output);
			if ( ! empty($more_link_text) )
				$output .= ' <a href="'. get_permalink() . "#more-$id\">$more_link_text</a>";
		}
	} elseif ($strip_length && strlen($output) > $strip_length) {
		$output = _cut_html($output, $strip_length, 0, get_bloginfo('charset'));
		$output .= (empty($more_link_text) ? '[...]' : '<div><a href="'. get_permalink() . "#more-$id\">$more_link_text</a></div>");
		$output = force_balance_tags($output);
	}

	return $output;
}

/* ==================================================
 * @param	string  $message
 * @return	string  $form
 * based on get_the_content and get_the_password_form() at wp-includes/post-template.php of WP 2.2.3
 */
function ks_check_password($message = '') {
	if (empty($message)) {
		$message = __("This post is password protected. To view it please enter your password below:");
	}
	if (_confirmed_post_password()) {
		return NULL;
	} else {
		$form = '<form method="post" action="' . htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES) . '"><p>' . $message . '</p><p><input name="post_password" type="password" size="20" />';
		if (ks_is_comments_list()) {
			$form .= '<input type="hidden" name="view" value="co_list" />';
		} elseif (ks_is_comment_post()) {
			$form .= '<input type="hidden" name="view" value="co_post" />';
		}
		$url = parse_url($_SERVER['REQUEST_URI']);
		$query = $url['query'];
		if (empty($query) && isset($_POST['urlquery'])) {
			$query = $_POST['urlquery'];
		}
		if ($query) {
			$form .= '<input type="hidden" name="urlquery" value="' . htmlspecialchars($query, ENT_QUOTES) . '" />';
		}
		$form .= '<input type="submit" name="Submit" value="' . __("Submit") . '" /></p></form>';
		return $form;
	}
}

/* ==================================================
 * @param	string  $content
 * @param	int     $length
 * @param	int     $start
 * @param	string  $charset
 * @return	string  $content
 */
function _cut_html($content, $length, $start = 0, $charset = NULL) {
	if (empty($charset)) {
		$charset = get_bloginfo('charset');
	}
	$fragment = mb_strcut($content, $start, $length, $charset);
	if (strlen($content) - $start < $length) {
		return $fragment;
	}
	$fragment = preg_replace('/<[^>]*$/', '', $fragment);
	$fragment = preg_replace('/&#?[a-zA-Z0-9]*?$/', '', $fragment);
	$w_start_tags = $fragment;
	while (preg_match('!(<[^/]>|<[^/][^>]*[^/]>)([^<]*?)$!', $fragment, $only_start_tag) && (preg_match('/^\s*$/', $only_start_tag[2]) || strlen($only_start_tag[2]) < 32)) {
		$fragment = preg_replace('/' . preg_quote($only_start_tag[0], '/') . '$/', '', $fragment);
	}
	if (preg_match('/^\s*$/', $fragment)) { // keep back if the fragment is empty
		$fragment = $w_start_tags;
	}
	$form_start = strrpos($fragment, '<form ');
	$form_end   = strrpos($fragment, '</form>');
	if ($form_start > 0 && ($form_end === false || $form_end < $form_start)) {
		$fragment = substr($fragment, 0, $form_start); // prevent spliting inside forms
	}
	return $fragment;
}

/* ==================================================
 * @param	int     $timestamp
 * @param	string  $year
 * @param	string  $mon_date
 * @param	string  $time
 * @return	none
 */
function _time($timestamp, $year = NULL, $mon_date = NULL, $time = NULL) {
	$year     = ! is_null($year)     ? $year     : ks_option('ks_year_format');
	$mon_date = ! is_null($mon_date) ? $mon_date : ks_option('ks_month_date_format');
	$time     = ! is_null($time)     ? $time     : ks_option('ks_time_format');
	if (date('Y', $timestamp) != date('Y')) {
		$output = date($year . ' ' . $time, $timestamp);
	} elseif (date('m-d', $timestamp) != date('m-d')) {
		$output = date($mon_date . ' ' . $time, $timestamp);
	} else {
		$output = date($time, $timestamp);
	}
	echo $output;
	return;
}

/* ==================================================
 * @param	string  $year
 * @param	string  $mon_date
 * @param	string  $time
 * @return	none
 */
function ks_time($year = NULL, $mon_date = NULL, $time = NULL) {
	_time(get_post_time(), $year, $mon_date, $time);
	return;
}

/* ==================================================
 * @param	string  $year
 * @param	string  $mon_date
 * @param	string  $time
 * @return	none
 */
function ks_mod_time($year = NULL, $mon_date = NULL, $time = NULL) {
	_time(get_the_modified_time('U'), $year, $mon_date, $time);
	return;
}

/* ==================================================
 * @param	string  $year
 * @param	string  $mon_date
 * @param	string  $time
 * @return	none
 */
function ks_comment_datetime($year = NULL, $mon_date = NULL, $time = NULL) {
	_time(get_comment_time('U'), $year, $mon_date, $time);
	return;
}

/* ==================================================
 * @param	boolean $echo
 * @param	string  $return
 * based on get_comment_author_link() at comment-template.php of WP 2.5
 */
function ks_comment_author_link($echo = true) {
	global $KS_Shrinkage;
	$url    = get_comment_author_url();
	$author = get_comment_author();
	if ( empty( $url ) || 'http://' == $url ) {
		$return = $author;
	} else {
		$return = '<a href="' . $url . '" >' . $author . '</a>';
	}
	$return = apply_filters('get_comment_author_link', $return);
	$return = $KS_Shrinkage->shrink_content($return);
	if ($echo) {
		echo $return;
	}
	return $return;
}

/* ==================================================
 * @param	int     $num
 * @param	boolean $echo
 * @return	string  $output
 */
function ks_pict_number($num, $echo = false) {
	global $Ktai_Style;
	$output = __('[]', 'ktai_style');
	if (is_numeric($num) && $num >= 0 && $num <= 10) {
		$num = $num % 10;
		if ($num) {
			$output = sprintf('<img localsrc="%d" alt="%d." />', 179 + $num, $num);
		} else {
			$output = '<img localsrc="325" alt="0." />';
		}
	}
	if ($echo) {
		echo $output;
	}
	return $output;
}

/* ==================================================
 * @param	int     $count
 * @param	int     $max
 * @param	string  $link
 * @return	none
 */
function ks_ordered_link($count, $max = 10, $link, $label = NULL) {
	if ($max <= 0 || $max > 10) {
		$max = 10;
	}
	if ($count > $max) {
		$count = -1;
	}
	$output = ks_pict_number($count);
	if ($link) {
		$output .= '<a href="' . htmlspecialchars($link, ENT_QUOTES) . '"' . ks_accesskey_html($count) . '>';
	}
	if (! is_null($label)) {
		$output .= $label;
		if ($link) {
			$output .= '</a>';
		}
	}
	echo $output;
}

/* ==================================================
 * @param	int     $i
 * @param	string  $accesskey
 * @param	string  $label
 * @param	string  $post_status
 * @param	string  $post_password
 * @return	string  $output
 * based on wp_link_pages() at wp-includes/post-template.php at WP 2.2.3
 */
function _page_link($i, $accesskey, $label, $post_status, $post_password) {
	if ($i == 1) {
		$output = _internal_link(get_permalink(), $accesskey, '', $label, $post_password);
	} elseif ('' == get_option('permalink_structure') || 'draft' == $post_status) {
		$output = _internal_link(get_permalink() . '&amp;page=' . $i, $accesskey, '', $label, $post_password);
	} else {
		$output = _internal_link(trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged'), $accesskey, '', $label, $post_password);
	}
	return $output;
}

/* ==================================================
 * @param	mix     $arg
 * @return	string  $output
 * based on wp_link_pages() at wp-includes/post-template.php at WP 2.2.3
 */
function ks_link_pages($args = '') {
	global $post, $id, $page, $numpages, $multipage, $more, $pagenow;

	if (is_array($args)) {
		$r = &$args;
	} else {
		parse_str($args, $r);
	}
	$defaults = array('before' => '<p>' . __('Pages:'), 'after' => '</p>', 'next_or_number' => 'number', 'nextpagelink' => __('Next page'),
			'previouspagelink' => __('Previous page'), 'pagelink' => '%', 'more_file' => '', 'echo' => 1);
	$r = array_merge($defaults, $r);
	extract($r, EXTR_SKIP);

	if (! $multipage || ! _confirmed_post_password()) {
		return;
	}
	if ($more_file != '') {
		$file = $more_file;
	} else {
		$file = $pagenow;
	}

	$output = '';
	if ( 'number' == $next_or_number ) {
		for ( $i = 1; $i < ($numpages+1); $i = $i + 1 ) {
			$j = str_replace('%',"$i",$pagelink);
			$output .= ' ';
			if ( ($i != $page) || ((!$more) && ($page==1)) ) {
				$output .= _page_link($i, $j, $j, $post->post_status, $post->post_password);
			}
		}
	} elseif ($more) {
		$i = $page - 1;
		if ($i > 0) {
			$output .= _page_link($i, '*', $previouspagelink, $post->post_status, $post->post_password);
		}
		$i = $page + 1;
		if ($i <= $numpages) {
			$output .= _page_link($i, '#', $nextpagelink, $post->post_status, $post->post_password);
		}
	}

	if ($output) {
		$output = $before . $output . $after;
	}

	if ($echo) {
		echo $output;
	}
	return $output;
}

/* ==================================================
 * @param	string  $before
 * @param	string  $after
 * @return	none
 */
function ks_pagenum($before = ' (', $after = ')') {
	global $paged, $wp_query;
	if (! $paged) {
		$paged = 1;
	}
	$max_page = $wp_query->max_num_pages;
	if ($wp_query->max_num_pages > 1) {
		echo "$before$paged/$max_page$after";
	}
}

/* ==================================================
 * @param	string  $format
 * @param	string  $link
 * @param	boolean $in_same_cat
 * @param	string  $excluded_categories
 * @param	string  $accesskey
 * @return	none
 * based on previous_post_link() at wp-includes/link-template.php of WP 2.2.3
 */
function ks_previous_post_link($format='<img localsrc="7" alt="&laquo;">*.%link', $link='%title', $in_same_cat = false, $excluded_categories = '', $accesskey = '*') {

	if ( is_attachment() )
		$post = & get_post($GLOBALS['post']->post_parent);
	else
		$post = get_previous_post($in_same_cat, $excluded_categories);

	if ( !$post )
		return;

	$title = apply_filters('the_title', $post->post_title, $post);
	$string = '<a href="'.get_permalink($post->ID).'"' . ks_accesskey_html($accesskey) . '>';
	$link = str_replace('%title', $title, $link);
	$link = $pre . $string . $link . '</a>';

	$format = str_replace('%link', $link, $format);

	echo $format;
}

/* ==================================================
 * @param	string  $format
 * @param	string  $link
 * @param	boolean $in_same_cat
 * @param	string  $excluded_categories
 * @param	string  $accesskey
 * @return	none
 * based on next_post_link() at wp-includes/link-template.php of WP 2.2.3
 */
function ks_next_post_link($format='#.%link<img localsrc="8" alt="&raquo;">', $link='%title', $in_same_cat = false, $excluded_categories = '', $accesskey = '#') {
	$post = get_next_post($in_same_cat, $excluded_categories);

	if ( !$post )
		return;

	$title = apply_filters('the_title', $post->post_title, $post);
	$string = '<a href="'.get_permalink($post->ID).'"' . ks_accesskey_html($accesskey) . '>';
	$link = str_replace('%title', $title, $link);
	$link = $string . $link . '</a>';
	$format = str_replace('%link', $link, $format);

	echo $format;
}

/* ==================================================
 * @param	string  $label
 * @param	string  $accesskey
 * @return	none
 * based on previous_posts_link() at wp-includes/link-template.php of WP 2.2.3
 */
function ks_previous_posts_link($label = NULL,  $accesskey = '*') {
	$label = ! is_null($label) ? $label : ('<img localsrc="7" alt="&laquo;">' . __('*.Prev', 'ktai_style'));
	global $paged;
	if ( (!is_single())	&& ($paged > 1) ) {
		echo '<a href="' . Ktai_Style::strip_host(clean_url(_quoted_remove_query_arg('kp', get_previous_posts_page_link()))) . '"' . ks_accesskey_html($accesskey) . '>';
		echo preg_replace('/&([^#])(?![a-z]{1,8};)/', '&amp;$1', $label) .'</a>';
	}
}

/* ==================================================
 * @param	string  $label
 * @param	string  $accesskey
 * @param	int     $max_page
 * @return	none
 * based on next_posts_link() at wp-includes/link-template.php of WP 2.2.3
 */
function ks_next_posts_link($label = NULL, $accesskey = '#', $max_page = 0) {
	$label = ! is_null($label) ? $label : (__('#.Next', 'ktai_style') . '<img localsrc="8" alt="&raquo;">');
	global $paged, $wpdb, $wp_query;
	if ( !$max_page ) {
		$max_page = $wp_query->max_num_pages;
	}
	if ( !$paged )
		$paged = 1;
	$nextpage = intval($paged) + 1;
	if ( (! is_single()) && (empty($paged) || $nextpage <= $max_page) ) {
		echo '<a href="' . Ktai_Style::strip_host(clean_url(_quoted_remove_query_arg('kp', get_next_posts_page_link($max_page)))) . '"' . ks_accesskey_html($accesskey) . '>';
		echo preg_replace('/&([^#])(?![a-z]{1,8};)/', '&amp;$1', $label) .'</a>';
	}
}

/* ==================================================
 * @param	string  $sep
 * @param	string  $before
 * @param	string  $after
 * @param	string  $prev_label
 * @param	string  $next_label
 * @param	string  $prev_key
 * @param	string  $next_key
 * @return	none
 * based on posts_nav_link() at wp-includes/link-template of WP 2.2.3
 */
function ks_posts_nav_link($sep = ' | ', $before = '', $after = '', $prev_label = NULL, $next_label = NULL, $prev_key = '*', $next_key = '#') {
	if (is_single() || is_page()) {
		return;
	}
	global $wp_query;
	$max_num_pages = $wp_query->max_num_pages;
	$paged = get_query_var('paged');

	//only have sep if there's both prev and next results
	if ($paged < 2 || $paged >= $max_num_pages) {
		$sep = '';
	}

	if ( $max_num_pages > 1 ) {
		echo $before;
		ks_previous_posts_link($prev_label, $prev_key);
		echo preg_replace('/&([^#])(?![a-z]{1,8};)/', '&amp;$1', $sep);
		ks_next_posts_link($next_label, $next_key);
		echo $after;
	}
	return;
}

/* ==================================================
 * @param	int     $num
 * @param	string  $first
 * @param	string  $last
 * @param	string  $prev_key
 * @param	string  $next_key
 * @return	none
 */
function ks_posts_nav_multi($num = 3, $first = NULL, $last = NULL, $prev_key = '*', $next_key = '#') {
	if ($num < 0 || $num > 9) { 
		$num = 3;
	}
	$first = ! is_null($first) ? $first : __('First', 'ktai_style');
	$last  = ! is_null($last)  ? $last  : __('Last', 'ktai_style');
	global $wp_query;
	if (is_single() || is_page()) {
		return;
	}
	$max_num_pages = $wp_query->max_num_pages;
	if ( $max_num_pages <= 1 ) {
		return;
	}
	$paged = intval(get_query_var('paged'));
	if ($paged < 1) {
		$paged = 1;
	}
	$output = '';
	if ($paged - $num > 1) {
		$output .= '<a href="' . Ktai_Style::strip_host(clean_url(get_pagenum_link(1))) . '">';
		$output .= preg_replace('/&([^#])(?![a-z]{1,8};)/', '&amp;$1', $first) .'</a>...';
	}
	for ($count = $paged - $num ; $count <= $paged + $num ; $count++) {
		if ($count < 1) {
			continue;
		} elseif ($count > $max_num_pages) {
			break;
		} elseif ($count == $paged -1) {
			$output .= ' <a href="' . Ktai_Style::strip_host(clean_url(get_pagenum_link($count))) . '"' . ks_accesskey_html($prev_key) . '>'. $count .'</a>';
		} elseif ($count == $paged) {
			$output .= " [$count]";
		} elseif ($count == $paged +1) {
			$output .= ' <a href="' . Ktai_Style::strip_host(clean_url(get_pagenum_link($count))) . '"' . ks_accesskey_html($next_key) . '>'. $count .'</a>';
		} else {
			$output .= ' <a href="' . Ktai_Style::strip_host(clean_url(get_pagenum_link($count))) . '">'. $count .'</a>';
		}
	}
	if ($paged + $num < $max_num_pages) {
		$output .= '...<a href="' . Ktai_Style::strip_host(clean_url(get_pagenum_link($max_num_pages))) . '">';
		$output .= preg_replace('/&([^#])(?![a-z]{1,8};)/', '&amp;$1', $last) .'</a>';
	}
	echo $output;
	return;
}

/* ==================================================
 * @param	none
 * @return	none
 */
function ks_posts_nav_dropdown() {
	if (is_single() || is_page()) {
		return;
	}
	global $wp_query, $Ktai_Style;
	$max_num_pages = $wp_query->max_num_pages;
	if ($max_num_pages <= 1) {
		return;
	}
	$paged = intval(get_query_var('paged'));
	if ($paged < 1) {
		$paged = 1;
	}
	$link = get_pagenum_link($paged);
	$url = parse_url($link);
	$query = $url['query'];
	if ($query) {
		parse_str($query, $params);
		unset($params['paged']);
		unset($params['kp']);
		$form_html = '';
		foreach($params as $k => $v) {
			$form_html .= '<input type="hidden" name="' . htmlspecialchars($k, ENT_QUOTES) . '" value="' . htmlspecialchars($v, ENT_QUOTES) . '" />';
		}
	} else {
		$link = preg_replace('!/page/\d+!', '', $link);
		$form_html = '';
	}
	$output = '<form method="get" action="' . htmlspecialchars($url['path'], ENT_QUOTES) . '">' . $form_html . '<select name="paged">';
	for ($count = 1; $count <= $max_num_pages ; $count++) {
		$output .= '<option value="' . $count . ($count == $paged ? '" selected="selected' : '') . '">' . $count . '</option>';
	}
	$output .= '</select><input type="submit" value="' . __('Move to page', 'ktai_style') . '" /></form>';
	if (! $Ktai_Style->ktai || ! ($size = $Ktai_Style->get('page_size')) || $size - 300 >= strlen($output)) {
		echo $output;
	}
	return;
}

/* ==================================================
 * @param	int     $id
 * @return	string  $address
 */
function ks_get_comments_list_link($id = 0) {
	$address = get_permalink($id);
	$address .= (strpos($address, '?') === false ? '?' : '&' ) . 'view=co_list';
	return $address;
}

/* ==================================================
 * @param	string  $icon
 * @param	string  $zero
 * @param	string  $one
 * @param	string  $more
 * @param	string  $none
 * @param	string  $sec
 * @param	string  $accesskey
 * @return	none
 * based on comments_popup_link() at wp-includes/comment-template.php of WP 2.2.3
 */
function ks_comments_link($icon = NULL, $zero = NULL, $one = NULL, $more = NULL, $none = NULL, $sec = NULL, $accesskey = NULL) {
	global $id, $post, $wpdb;
	$icon = ! is_null($icon) ? $icon : '<img localsrc="86" alt="" />';
	$zero = ! is_null($zero) ? $zero : __('No comments', 'ktai_style');
	$one  = ! is_null($one)  ? $one  : __('One comment', 'ktai_style');
	$more = ! is_null($more) ? $more : __('% comments', 'ktai_style');
	$none = ! is_null($none) ? $none : '<img localsrc="61" alt="' . __('X ', 'ktai_style') . '" />' . __('Comments off', 'ktai_style');
	$sec  = ! is_null($sec)  ? $sec  : __('View comments (Need password)', 'ktai_style');
	$number = get_comments_number($id);
	if ( 0 == $number && 'closed' == $post->comment_status && 'closed' == $post->ping_status ) {
		echo $icon . $none;
		return;
	}

	$co_addr = ks_get_comments_list_link();
	if (_confirmed_post_password()) {
		if ($number == 0) {
			echo $icon . $zero;
		} else {
			ob_start();
			comments_number($zero, $one, $more);
			$co_num = ob_get_contents();
			ob_end_clean();
			echo _internal_link($co_addr, $accesskey, $icon, $co_num, $post->post_password);
		}
	} else {
		echo $icon . '<a href="' . htmlspecialchars($co_addr, ENT_QUOTES) . '"' . ks_accesskey_html($accesskey) . '>' . $sec . '</a>';
	}
	return;
}

/* ==================================================
 * @param	int     $id
 * @return	string  $address
 */
function ks_comments_post_url($id = 0) {
	$address = get_permalink($id);
	$address .= (strpos($address, '?') === false ? '?' : '&' ) . 'view=co_post';
	if (! ks_is_loggedin() && ks_option('ks_require_term_id') && is_ktai() == 'DoCoMo') {
		$address .= '&guid=ON';
	}
	return $address;
}

/* ==================================================
 * @param	string  $label
 * @param	string  $before
 * @param	string  $after
 * @param	string  $icon
 * @param	string  $accesskey 
 * @return	none
 */
function ks_comments_post_link($label = NULL, $before = '', $after = '', $icon = '<img localsrc="149" alt="" />', $accesskey = NULL) {
	if (comments_open()) {
		$label = ! is_null($label) ? $label : __('Post comments', 'ktai_style');
		$co_post = ks_comments_post_url();
		global $post;
		$post_pass = _confirmed_post_password() ? $post->post_password : NULL;
		echo $before . _internal_link($co_post, $accesskey, $icon, $label, $post_pass) . $after;
	}
}

/* ==================================================
 * @param	string  $icon
 * @param	string  $label
 * @param	string  $accesskey
 * @return	none
 */
function ks_back_to_post($icon = NULL, $label = NULL, $accesskey = '*') {
	$icon = ! is_null($icon) ? $icon : ('<img localsrc="64" alt="' . __('&lt;-', 'ktai_style') . '" />');
	$label = ! is_null($label) ? $label : __('Back to the post', 'ktai_style');
	global $post;
	echo _internal_link(get_permalink(), $accesskey, $icon, $label, $post->post_password);
	return;
}

/* ==================================================
 * @param	int     $num
 * @param	string  $type
 * @param	boolean $group_by_post
 * @return	array   $sorted
 */
function ks_get_recent_comments($num = 20, $type = '', $group_by_post = true) {
	global $wpdb, $comment;
	if (! is_numeric($num) || $num <= 0) {
		$num = 20;
	} else {
		$num = intval($num);
	}
	if ($type == 'comment') {
		$refine = "AND comment_type = ''";
	} elseif ($type == 'trackback+pingback') {
		$refine = "AND (comment_type = 'trackback' OR comment_type = 'pingback')";
	} elseif ($type == 'trackback') {
		$refine = "AND comment_type = 'trackback'";
	} elseif ($type == 'pingback') {
		$refine = "AND comment_type = 'pingback'";
	} else {
		$refine = '';
	}
	$comments = $wpdb->get_results( "SELECT * FROM $wpdb->comments WHERE comment_approved = '1' $refine ORDER BY comment_date DESC LIMIT $num" );
	if (count($comments) <= 0) {
		return NULL;
	}
	if (! $group_by_post) {
		return $comments;
	}
	$grouped = array();
	foreach ($comments as $c) {
		$post_id = $c->comment_post_ID;
		if (! isset($grouped[$post_id])) {
			$grouped[$post_id][] = get_post($post_id);
		}
		$grouped[$post_id][] = $c;
	}
	return $grouped;
}

/* ==================================================
 * @param	string  $separator
 * @return	none
 */
function ks_category($separator = ', ', $parents='') {
	$categories = get_the_category();
	if (empty($categories)) {
		echo apply_filters('the_category', __('Uncategorized'), $separator, $parents);
	} else {
		$cat_links = array();
		foreach ($categories as $c) {
			$cat_links[] = '<a href="' . get_category_link($c->cat_ID) . '">' . attribute_escape($c->cat_name) . '</a>';
		}
		echo apply_filters('the_category', implode($separator, $cat_links), $separator, $parents);
	}
	return;
}

/* ==================================================
 * @param	string  $separator
 * @return	none
 */
function ks_tags($before = '', $after = '', $separator = ', ') {
	if (! function_exists('get_the_tags')) {
		return NULL;
	}
	$tags = get_the_tags();
	if ($tags) {
		$tag_links = array();
		foreach ($tags as $t) {
			$tag_links[] = '<a href="' . get_tag_link($t->term_id) . '">' . attribute_escape($t->name) . '</a>';
		}
		echo $before . apply_filters('the_tags', implode($separator, $tag_links)) . $after;
	}
	return;
}

/* ==================================================
 * @param	mix     $args
 * @return	none
 * baased on wp_tag_cloud() at category-template.php of WP 2.3.1
 */
function ks_tag_cloud($args = '') {
	if (! function_exists('get_tags')) {
		return NULL;
	}
	$defaults = array(
		'number' => 45, 'format' => 'flat', 'orderby' => 'name',
		 'order' => 'ASC', 'exclude' => '', 'include' => ''
	);
	$args = wp_parse_args( $args, $defaults );
	$tags = get_tags( array_merge($args, array('orderby' => 'count', 'order' => 'DESC')) ); // Always query top tags

	if (empty($tags)) {
		return;
	}
	$return = _generate_tag_cloud($tags, $args);
	if (is_wp_error($return)) {
		return false;
	} else {
		echo apply_filters( 'wp_tag_cloud', $return, $args );
	}
}

/* ==================================================
 * @param	array   $tags
 * @param	mix     $args
 * @return	none
 * baased on wp__generate_tag_cloud at category-template.php of WP 2.3.1
 */
function _generate_tag_cloud($tags, $args = '') {
	global $wp_rewrite, $Ktai_Style;
	$defaults = array(
		'smallest' => 1, 'largest' => 6, 'unit' => '', 'number' => 45,
		'format' => 'flat', 'orderby' => 'name', 'order' => 'ASC'
	);
	$args = wp_parse_args( $args, $defaults );
	extract($args);

	if (! $tags) {
		return;
	}
	$counts = $tag_links = array();
	foreach ( (array) $tags as $tag ) {
		$counts[$tag->name] = $tag->count;
		$tag_links[$tag->name] = get_tag_link( $tag->term_id );
		if ( is_wp_error( $tag_links[$tag->name] ) )
			return $tag_links[$tag->name];
		$tag_ids[$tag->name] = $tag->term_id;
	}

	$min_count = min($counts);
	$spread = max($counts) - $min_count;
	if ($spread <= 0) {
		$spread = 1;
	}
	$font_spread = $largest - $smallest;
	if ($font_spread <= 0) {
		$font_spread = 1;
	}
	$font_step = $font_spread / $spread;

	if ('name' == $orderby) {
		uksort($counts, 'strnatcasecmp');
	} else {
		asort($counts);
	}
	if ('DESC' == $order) {
		$counts = array_reverse($counts, true);
	}
	$a = array();

	foreach ($counts as $tag => $count) {
		$tag_id = $tag_ids[$tag];
		$tag_link = clean_url($tag_links[$tag]);
		$tag = str_replace(' ', '&nbsp;', attribute_escape( $tag ));
		$a[] = '<font size="' . ($smallest + (($count - $min_count) * $font_step)) . '"><a href="' . $tag_link . '">' . $tag . '</a></font>';
	}

	switch ( $format ) :
	case 'array' :
		$return = &$a;
		break;
	case 'list' :
		$return = '<ul><li>' . implode('</li><li>', $a) . '</li></ul>';
		break;
	default :
		$return = implode(' ', $a);
		break;
	endswitch;

	return $return;
}

/* ==================================================
 * @param	mix     $args
 * @return	none
 */
function ks_get_archives($args = '') {
	global $Ktai_Style, $KS_Shrinkage;
	ob_start();
	wp_get_archives($args);
	$output = ob_get_contents();
	ob_end_clean();
	$output = $Ktai_Style->filter_tags($output);
	$output = preg_replace('/ ?(\d+) ?/', '\\1' , $output);
	$output = str_replace('&nbsp;', ' ' , $output);
	$output = preg_replace('!href=([\'"])' . preg_quote(get_bloginfo('url'), '!') . '/?!', 'href=$1' . $KS_Shrinkage->get('url'), $output); //"syntax highlighting fix
	if (strpos($args, 'echo=0') === false) {
		echo $output;
	}
	return $output;
}

/* ==================================================
 * @param	mix     $args
 * @return	none
 */
function ks_list_bookmarks($args = '') {
	global $Ktai_Style;
	if (is_array($args)) {
		$r = &$args;
	} else {
		parse_str($args, $r);
	}
	$r = array_merge(array('echo' => 0), $r);
	$output = wp_list_bookmarks($r);
	$output = $Ktai_Style->filter_tags($output);
	$output = preg_replace('/ ?(\d+) ?/', '\\1' , $output);
	$output = str_replace('&nbsp;', ' ' , $output);
	if (strpos($args, 'echo=0') === false) {
		echo $output;
	}
	return $output;
}

/* ==================================================
 * @param	string  $sep
 * @param	string  $before
 * @param	string  $after
 * @return	none
 */
function ks_pages_menu($sep = ' | ', $before = '', $after = '', $args = '') {
	$defaults = array('parent_only' => 1, 'child_of' => 0, 'exclude' => '', 'authors' => '', 'sort_column' => 'menu_order, ID');

	if (is_array($args)) {
		$r = &$args;
	} else {
		parse_str($args, $r);
	}
	$r = array_merge($defaults, $r);
	$pages = get_pages($r);
	$menu = array();
	if (count($pages) < 1) {
		return;
	}
	$has_children = 0;
	foreach ($pages as $p) {
		if ($r['parent_only'] && $p->post_parent) {
			$has_children++;
			continue;
		}
		$menu[] = '<a href="' . Ktai_Style::strip_host(get_page_link($p->ID)) . '">' . attribute_escape($p->post_title) . '</a>';
	}
	if ($has_children) {
		$menu[] = '<a href="' . ks_blogurl(false) . '?menu=pages">' . __('All Pages', 'ktai_style') . '</a>';
	}
	$output = $before . implode($sep, $menu) . $after;
	if (strpos($args, 'echo=0') === false) {
		echo $output;
	}
	return $output;
}

/* ==================================================
 * @param	string  $before
 * @param	string  $after
 * @return	none
 */
function ks_switch_pc_view($before = ' (', $after = ')') {
	global $user_ID, $Ktai_Style;
	$here = $_SERVER['REQUEST_URI'];
	if ($Ktai_Style->is_ktai() != 'Unknown' || $user_ID) {
		return;
	} elseif (preg_match('/\?menu=/', $here)) {
		$here = preg_replace('/\?menu=.*$/', '', $here);
	}
	$menu = $before . '<a href="' . attribute_escape($here . (strpos($here, '?') === false ? '?' : '&') . 'pcview=true') . '">' . __('To PC view', 'ktai_style') . '</a>' . $after;
	echo apply_filters('switch_pc_view/ktai_style.php', $menu, $here, $before, $after);
}

/* ==================================================
 * @param	string  $before
 * @param	string  $after
 * @return	none
 */
function ks_switch_inline_images($before = '<hr /><div align="center">', $after = '</div>') {
	global $Ktai_Style;
	$inline_default = $Ktai_Style->ktai->get('image_inline_default');
	$is_inline      = ks_is_image_inline();
	if (! $inline_default && ! ks_is_flat_rate() || ! ks_has_inline_images()) {
		return;
	}
	$here = remove_query_arg('img', $_SERVER['REQUEST_URI']);
	if ($is_inline == $inline_default) {
		$value = $is_inline ? 'link' : 'inline';
		$link = '<a id="inline" href="' . attribute_escape($here . (strpos($here, '?') === false ? '?' : '&') . "img=$value") . '">';
	} else {
		$link = '<a id="inline" href="' . attribute_escape($here) . '">';
	}
	if ($is_inline) {
		$inline  = __('Show', 'ktai_style');
		$convert = $link . __('Convert to link', 'ktai_style') . '</a>';
	} else {
		$inline  = $link . __('Show', 'ktai_style') . '</a>';
		$convert = __('Convert to link', 'ktai_style');
	}
	echo apply_filters('switch_inline_images/ktai_style.php', $before . __('Images:', 'ktai_style') . ' ' . $inline . ' | ' . $convert . $after, $before, $after);
}

?>