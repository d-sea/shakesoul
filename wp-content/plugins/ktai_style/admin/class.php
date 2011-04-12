<?php
/* ==================================================
 *   Ktai_Style_Admin class
   ================================================== */

define ('KS_SESSION_NAME', 'ksid');
define ('KS_SESSION_LIFETIME', 3600);
define ('KS_SESSION_RENEWTIME', 1800);

class Ktai_Style_Admin {
	private $sid;
	private $next_id;
	private $user_login;
	private $user_agent;
	private $term_ID;
	private $sub_ID;
	private $data;

/* ==================================================
 * @param	none
 * @return	string  $user_login
 */
public function get_current_user() {
	return $this->user_login;
}

/* ==================================================
 * @param	string  $key
 * @return	mix     $param
 */
public function get($key) {
	return @$this->$key;
}

/* ==================================================
 * @param	string  $key;
 * @return	mix     $data
 */
public function get_data($key) {
	return stripslashes(@$this->data[$key]);
}

/* ==================================================
 * @param	string  $key
 * @param   mix     $value
 * @return	mix     $value
 */
public function set_data($key, $value) {
	return $this->data[$key] = $value;
}

/* ==================================================
 * @param	none
 * @return	mix     $result
 */
public function save_data() {
	global $wpdb;
	if (! $this->sid) {
		return false;
	}
	$sid4sql    = $wpdb->escape($this->sid);
	$data4sql   = $wpdb->escape(serialize($this->data));
	$sql = "UPDATE `{$wpdb->prefix}ktaisession` SET data = '$data4sql' WHERE sid = '$sid4sql' LIMIT 1";
	$result = $wpdb->query($sql);
	return $result;
}

/* ==================================================
 * @param	none
 * @return	string  $sid
 */
public function get_sid() {
	if (isset($_POST[KS_SESSION_NAME])) {
		return $_POST[KS_SESSION_NAME];
	} elseif (isset($_GET[KS_SESSION_NAME])) {
		return $_GET[KS_SESSION_NAME];
	} else {
		return NULL;
	}
}

/* ==================================================
 * @param	none
 * @return	none
 */
public function sid_field() {
	if ($this->sid) {
		echo '<input type="hidden" name="' .KS_SESSION_NAME .'" value="' . htmlspecialchars($this->sid, ENT_QUOTES) . '" />';
	}
}

/* ==================================================
 * @param	string  $uri
 * @return	string  $uri
 */
public function add_sid($uri, $escape = true) {
	if ($this->sid && preg_match('!(^|^\.{1,2}/|' . KS_ADMIN_DIR . '/)[-\w]*(\.php)?!', $uri)) {
//		$uri = add_query_arg(KS_SESSION_NAME, $this->sid, $uri);
		$uri .= (strpos($uri, '?') === false ? '?' : '&') . KS_SESSION_NAME . '=' . $this->sid;
		if ($escape) {
			$uri = htmlspecialchars($uri, ENT_QUOTES);
		}
	}
	return $uri;
}

/* ==================================================
 * @param	string  $uri
 * @return	string  $uri
 */
public function remove_sid($uri) {
	if (strpos($uri, KS_SESSION_NAME . '=') !== false) {
		$uri = remove_query_arg(KS_SESSION_NAME, $uri);
	}
	return $uri;
}

/* ==================================================
 * @param	none
 * @return	string  $sid
 */
private function make_sid() {
	return str_replace(array('+', '/', '='), array('_', '.', ''), base64_encode(sha1(uniqid(mt_rand(), true), true)));
}

/* ==================================================
 * @param	string  $user_login
 * @param	string  $sid
 * @param	mix     $data
 * @return	string  $sid
 */
public function set_session($user_login, $sid = NULL, $data = NULL) {
	global $wpdb, $Ktai_Style;
	$sid      = $sid ? $sid : $this->make_sid();
	$expires  = time() + intval(KS_SESSION_LIFETIME);
	$exp4sql  = $wpdb->escape(date('Y-m-d H:i:s', $expires));
	$user4sql = $wpdb->escape($user_login);
	$ua_hash  = $Ktai_Style->get('user_agent') ? sha1($Ktai_Style->get('user_agent')) : '';
	$tid_hash = $Ktai_Style->get('term_ID') ? sha1($Ktai_Style->get('term_ID')) : '';
	$sub_hash = $Ktai_Style->get('sub_ID') ? sha1($Ktai_Style->get('sub_ID')) : '';
	$data4sql = $wpdb->escape(serialize($data));
	$sql = "INSERT IGNORE `{$wpdb->prefix}ktaisession` (sid, expires, user_login, user_agent, term_id, sub_id, data) VALUES ('$sid', '$exp4sql', '$user4sql', '$ua_hash', '$tid_hash', '$sub_hash', " . ($data ? "'$data4sql'" : "NULL") . ")";
	$result = $wpdb->query($sql);
	if (! $result) {
		return NULL;
	}
	$this->sid        = $sid;
	$this->next_id    = NULL;
	$this->expires    = $expires;
	$this->user_login = $user_login;
	$this->user_agent = $ua_hash;
	$this->term_ID    = $tid_hash;
	$this->sub_ID     = $sub_hash;
	$this->data       = $data;
	return $sid;
}

/* ==================================================
 * @param	none
 * @return	string  $new_sid
 */
public function renew_session() {
	global $wpdb;
	if ($this->next_id) {
		$sid = $this->next_id;
		$next4sql = $wpdb->escape($sid);
		$sql = "SELECT * FROM `{$wpdb->prefix}ktaisession` WHERE next_id = '$next4sql'";
		$result = $wpdb->get_row($sql);
		if ($result) {
			$this->sid        = $sid;
			$this->next_id    = $result->next_id;
			$this->expires    = strtotime($result->expires);
			$this->user_login = $result->user_login;
			$this->user_agent = $result->user_agent;
			$this->term_ID    = $result->term_id;
			$this->sub_ID     = $result->sub_id;
			$this->data       = unserialize($result->data);
			return $sid;
		}
		$this->next_id = NULL;
	}
	if ($this->sid && time() + intval(KS_SESSION_RENEWTIME) > $this->expires) {
		$new_sid    = $this->make_sid();
		$sid4sql    = $wpdb->escape($this->sid);
		$newsid4sql = $wpdb->escape($new_sid);
		$sql = "UPDATE `{$wpdb->prefix}ktaisession` SET next_id = '$newsid4sql' WHERE sid = '$sid4sql' AND next_id IS NULL LIMIT 1";
		$result = $wpdb->query($sql);
		if ($result) {
			return $this->set_session($this->user_login, $new_sid, $this->data);
		}
	}
	return false;
}

/* ==================================================
 * @param	string  $sid
 * @return	boolean $is_succeeded
 */
public function unset_session($sid) {
	global $wpdb;
	if ($sid) {
		$sid4sql = $wpdb->escape($sid);
		$sql = "DELETE FROM `{$wpdb->prefix}ktaisession` WHERE sid = '$sid4sql' LIMIT 1";
		$result = $wpdb->query($sql);
		if ($result) {
			return true;
		}
	}
	return false;
}

/* ==================================================
 * @param	string  $sid
 * @return	boolean $is_succeeded
 */
public function unset_prev_session($sid) {
	global $wpdb;
	if ($sid) {
		$sid4sql = $wpdb->escape($sid);
		$sql = "DELETE FROM `{$wpdb->prefix}ktaisession` WHERE next_id = '$sid4sql'  LIMIT 1";
		$result = $wpdb->query($sql);
		if ($result) {
			return true;
		}
	}
	return false;
}

/* ==================================================
 * @param	none
 * @return	boolean $is_succeeded
 */
public function garbage_sessions() {
	global $wpdb;
	$sql = "DELETE FROM `{$wpdb->prefix}ktaisession` WHERE expires < NOW()";
	$result = $wpdb->query($sql);
	if ($result) {
		return true;
	}
	return false;
}

/* ==================================================
 * @param	none
 * @return	string   $user_login
 */
public function check_session() {
	global $wpdb;
	$sid = self::get_sid();
	if (empty($sid)) {
		return false;
	}
	self::garbage_sessions();
	$sid4sql = $wpdb->escape($sid);
	$sql = "SELECT * FROM `{$wpdb->prefix}ktaisession` WHERE sid = '$sid4sql'";
	$result = $wpdb->get_row($sql);

	if (! $result || strcmp(sha1($_SERVER['HTTP_USER_AGENT']), $result->user_agent) != 0) {
		return false;
	}

	// restore the session
	if (isset($this)) {
		$this->sid        = $sid;
		$this->next_id    = $result->next_id;
		$this->expires    = strtotime($result->expires);
		$this->user_login = $result->user_login;
		$this->user_agent = $result->user_agent;
		$this->term_ID    = $result->term_id;
		$this->sub_ID     = $result->sub_id;
		$this->data       = unserialize($result->data);
	}
	return $result->user_login;
}

/* ==================================================
 * @param	none
 * @return	none
 */
public function redirect($redirect_to) {
	wp_redirect($this->add_sid($redirect_to, false));
}

/* ==================================================
 * @param	none
 * @return	none
 * based on wp_safe_redirect() at pluggable.php of WP 2.3
 */
public function safe_redirect($location) {
	$location = preg_replace('|[^a-z0-9-~+_.?#=&;,/:%]|i', '', $location);
	$location = wp_kses_no_null($location);

	// remove %0d and %0a from location
	$strip = array('%0d', '%0a');
	$found = true;
	while($found) {
		$found = false;
		foreach($strip as $val) {
			while(strpos($location, $val) !== false) {
				$found = true;
				$location = str_replace($val, '', $location);
			}
		}
	}

	if ( substr($location, 0, 2) == '//' ) {
		$location = 'http:' . $location;
	}
	$lp  = parse_url($location);
	$wpp = parse_url(get_option('home'));
	$allowed_hosts = (array) apply_filters('allowed_redirect_hosts', array($wpp['host']), $lp['host']);
	if ( isset($lp['host']) && ! in_array($lp['host'], $allowed_hosts) ) {
		$location = ks_admin_url(false);
	}
	wp_redirect($this->add_sid($location, false), $status);
}

/* ==================================================
 * @param	none
 * @return	none
 */
public function shrink_redirect_to($uri) {
	global $Ktai_Style;
	$plugin_dir_pat = preg_quote(preg_replace('!^https?://[^/]*/!', '', $Ktai_Style->get('plugin_url')), '!');
	$redirect_to = preg_replace("!^.*?$plugin_dir_pat!", '', $uri);
	return $this->remove_sid($redirect_to);
}

/* ==================================================
 * @param	none
 * @return	none
 */
public function auth_redirect() {
	if (! $this->check_session()) {
		global $Ktai_Style;
		$uri = preg_replace('!^.*' . preg_quote($Ktai_Style->strip_host($Ktai_Style->get('plugin_url')), '!') . '!', '', $_SERVER['REQUEST_URI']);
		wp_redirect($Ktai_Style->get('plugin_url') . 'login.php?redirect_to=' . urlencode($uri));
		exit();
	}
}

/* ==================================================
 * @param	none
 * @return	object  $this
 */
public function store_referer() {
	$path = $_SERVER['REQUEST_URI'];
	if ($this->get_data('referer')) {
		if (strcmp($this->get_data('referer'), $path) != 0) {
			$this->set_data('referer2', $this->get_data('referer'));
		}
	}
	$this->set_data('referer', $path);
	return $this;
}

/* ==================================================
 * @param	none
 * @return	string  $referer
 */
public function get_referer() {
	$referer = '';
	if (isset($_POST['_wp_http_referer'])) {
		$referer = $_POST['_wp_http_referer'];
	} elseif (isset($_GET['_wp_http_referer'])) {
		$referer = $_GET['_wp_http_referer'];
	} elseif (! isset($this->data['referer'])) {
		$referer = NULL;
	} elseif (strcmp($this->get_data('referer'), $_SERVER['REQUEST_URI']) == 0 && isset($this->data['referer2'])) {
		$referer = $this->get_data('referer2');
	} else {
		$referer = $this->get_data('referer');
	}
	if (strpos($referer, KS_SESSION_NAME . '=') !== false) {
		$referer = remove_query_arg(KS_SESSION_NAME, $referer);
		$referer = $this->add_sid($referer, false);
	}
	return $referer;
}

/* ==================================================
 * @param	string  $action
 * @return	none
 */
public function nonce_ays($action) {
	global $pagenow, $menu, $submenu, $parent_file, $submenu_file, $Ktai_Style;

	if ( $this->get_referer() ) {
		$adminurl = clean_url($this->get_referer());
		if (! preg_match('/' . KS_SESSION_NAME . '=/', $adminurl)) {
			$adminurl = $this->add_sid($adminurl);
		}
	} else {
		$adminurl = $this->add_sid(ks_admin_url(false));
	}
	
	$title = mb_convert_encoding(__('WordPress Confirmation', 'ktai_style'), $Ktai_Style->ktai->get('charset'), get_bloginfo('charset'));
	list ($desc, $allow_proceed) = $this->explain_nonce($action);
	if ($allow_proceed) {
		$no  = __('No');
		$yes = __('Yes');
	} else {
		$no  = __('Back', 'ktai_style');
	}
	// Remove extra layer of slashes.
	$_POST = stripslashes_deep($_POST);
	if ($_POST) {
		$q = http_build_query($_POST);
		$q = explode( ini_get('arg_separator.output'), $q);
		$html .= '<form method="post" action="' . clean_url($pagenow) . '"><input type="hidden" name="' . KS_SESSION_NAME . '" value="' . $this->sid . '" />';
		foreach ( (array) $q as $a ) {
			$v = substr(strstr($a, '='), 1);
			$k = substr($a, 0, -(strlen($v)+1));
			$html .= '<input type="hidden" name="' . wp_specialchars(urldecode($k)) . '" value="' . wp_specialchars(urldecode($v)) . '" />';
		}
		$add_html = '<input type="hidden" name="_wpnonce" value="' . wp_create_nonce($action) . '" /><div>' . wp_specialchars($desc) . '</p><p><a href="' . $adminurl . '">' . $no . '</a>' . ($allow_proceed ? ' <input type="submit" value="' . $yes . '" />' : '') . '</div></form>';
	} else {
		$add_html = '<p>' . wp_specialchars($desc) . '</p><p><a href="' . $adminurl . '">' . $no. '</a>' . ($allow_proceed ? ' <a href="' . clean_url(add_query_arg('_wpnonce', wp_create_nonce($action), $_SERVER['REQUEST_URI'])) . '">' . $yes . '</a>' : '') . ' </p>';
	}
	$html .= mb_convert_encoding($add_html, $Ktai_Style->ktai->get('charset'), get_bloginfo('charset'));
	Ktai_Style::ks_die($html, $title, false, true);
}

/* ==================================================
 * @param	string  $action
 * @return	string  $desc
 * @return	boolean $allow_proceed
 */
private function explain_nonce($action) {
	global $Ktai_Style, $KS_Shrinkage;
	remove_filter('the_title', array($KS_Shrinkage, 'shrink_title'), 90);
	if ( $action === -1 || ! preg_match('/([a-z]+)-([a-z]+)(_(.+))?/', $action, $matches) ) {
		return;
	}
	$verb = $matches[1];
	$noun = $matches[2];
	$trans = array();
	$trans['change']['cats'] = array(__('Are you sure you want to change categories of this post: &quot;%s&quot;?', 'ktai_style'), 'get_the_title');
	$trans['unapprove']['comment'] = array(__('Are you sure you want to unapprove this comment: &quot;%s&quot;?', 'ktai_style'), 'use_id');
	$trans['approve']['comment'] = array(__('Are you sure you want to approve this comment: &quot;%s&quot;?', 'ktai_style'), 'use_id');
	$trans['delete']['comment'] = array(__('Are you sure you want to delete this comment: &quot;%s&quot;?', 'ktai_style'), 'use_id');
	$trans['delete']['post'] = array(__('Are you sure you want to delete this post: &quot;%s&quot;?', 'ktai_style'), 'get_the_title');

	if ( isset($trans[$verb][$noun]) ) {
		if (! empty($trans[$verb][$noun][1]) ) {
			$lookup = $trans[$verb][$noun][1];
			$object = $matches[4];
			if ('use_id' != $lookup) {
				$object = call_user_func($lookup, $object);
			}
			$desc = sprintf($trans[$verb][$noun][0], $object);
		} else {
			$desc = $trans[$verb][$noun][0];
		}
		$allow_proceed = true;
	} else {
		$desc = wp_explain_nonce($action);
		$allow_proceed = $Ktai_Style->check_wp_version('2.5', '<');
	}
	return array($desc, $allow_proceed);
}

/* ==================================================
 * @param   array   $drafts
 * @param   string  $before
 * @return  none
 */
public function show_drafts($drafts, $before) {
	if ($drafts) {
		$titles = array();
		foreach ($drafts as $d) {
			$d->post_title = apply_filters('the_title', stripslashes($d->post_title));
			if ($d->post_title == '') {
				$d->post_title = sprintf(__('Post #%s'), $d->ID);
			}
			$titles[] =  '<a href="' . $this->add_sid('post.php?action=edit&post=' . $d->ID) . '">' . $d->post_title . '</a>';
		}
		echo '<p>' . $before . '</p><ul><li>' . implode('</li><li>', $titles) . '</li></ul>';
	}
	return;
}

/* ==================================================
 * @param	int     $comment_id
 * @return	none
 * based on get_edit_comment_link() at wp-includes/link-template.php of WP 2.3
 */
public function get_edit_comment_link($comment_id = 0) {
	$comment = &get_comment($comment_id);
	$post = &get_post($comment->comment_post_ID);

	if ($post->post_type == 'attachment') {
		return;
	} elseif ($post->post_type == 'page') {
		if (! current_user_can( 'edit_page', $post->ID)) {
			return;
		}
	} elseif (! current_user_can( 'edit_post', $post->ID)) {
		return;
	}

	$location = 'comment.php?action=editcomment&c=' . $comment->comment_ID;
	return apply_filters( 'get_edit_comment_link', $location );
}

/* ==================================================
 * @param	string  $name
 * @param	int     $selected
 * @return	string  $html
 */
public function dropdown_categories($name = 'default_category', $selected = 0) {
	global $wpdb;
	$html = '';
	$categories = get_categories('get=all');
	if (count($categories) < 1) {
		return $html;
	}
	$html .= '<select name="' . htmlspecialchars($name, ENT_QUOTES) . '">';
	foreach ($categories as $c) {
		$cat_id = isset($c->term_id) ? $c->term_id : $c->cat_ID;
		$html .= '<option value="' . intval($cat_id) . '"' . ($selected == $cat_id ? ' selected="selected"' : '') . '>' . wp_specialchars($c->cat_name) . '</option>';
	}
	$html .= '</select>';
	return $html;
}

// ===== End of class ====================
}

/* ==================================================
 *   KS_Category_Checklist class
 *   based on class Walker_Category_Checklist at wp-admin/includes/template.php of WP 2.5.1
   ================================================== */

class KS_Category_Checklist extends Walker {
	public $tree_type = 'category';
//	public $db_fields = array ('parent' => 'parent', 'id' => 'term_id');
	public $db_fields = array ('parent' => 'category_parent', 'id' => 'cat_ID');

	function start_lvl(&$output, $depth, $args) {
		return '';
	}

	function end_lvl(&$output, $depth, $args) {
		return '';
	}

	function start_el(&$output, $category, $depth, $args) {
		extract($args);

		$output .= "\n<li>" . '<label><input value="' . $category->cat_ID . '" type="checkbox" name="cat[]" ' . (in_array( $category->cat_ID, $selected_cats ) ? ' checked="checked"' : "" ) . '/> ' . wp_specialchars( apply_filters('the_category', $category->cat_name )) . '</label>';
		return $output;
	}

	function end_el(&$output, $category, $depth, $args) {
		$output .= "</li>\n";
		return $output;
	}
}
?>