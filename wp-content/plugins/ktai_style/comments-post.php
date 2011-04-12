<?php
/* ==================================================
 *   comments-post.php
 *   based on wp-comments-post.php of WP 2.2.3
   ================================================== */
   
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Allow: POST');
	header("HTTP/1.1 405 Method Not Allowed");
	header("Content-type: text/plain");
    exit;
}

$wpload_error = 'Could not post comments because custom WP_PLUGIN_DIR is set.';
require dirname(__FILE__) . '/wp-load.php';
nocache_headers();

global $wpdb, $Ktai_Style;
$comment_post_ID = (int) $_POST['comment_post_ID'];
$status = $wpdb->get_row("SELECT post_status, comment_status FROM $wpdb->posts WHERE ID = $comment_post_ID");

if ( empty($status->comment_status) ) {
	do_action('comment_id_not_found', $comment_post_ID);
	$Ktai_Style->ks_die(__('No target for your post.', 'ktai_style'));
	exit;
} elseif ( 'closed' ==  $status->comment_status ) {
	do_action('comment_closed', $comment_post_ID);
	$Ktai_Style->ks_die(__('Sorry, comments are closed for this item.'));
} elseif ( in_array($status->post_status, array('draft', 'pending')) ) {
	do_action('comment_on_draft', $comment_post_ID);
	$Ktai_Style->ks_die(__('No target for your post.', 'ktai_style'));
	exit;
}

$charset = $Ktai_Style->detect_encoding(isset($_POST['charset_detect']) ? $_POST['charset_detect'] : '');

$comment_author  = $_POST['author'];
$comment_content = $_POST['comment'];
if ($Ktai_Style->compare_encoding($charset, $Ktai_Style->get('charset'))) {
	$comment_author  = $Ktai_Style->ktai->pickup_pics(stripslashes($comment_author));
	$comment_content = $Ktai_Style->ktai->pickup_pics(stripslashes($comment_content));
	if (! $Ktai_Style->get_option('ks_allow_pictograms')) {
		$comment_author  = preg_replace('!<img localsrc="[^"]*" />!', '', $comment_author);
		$comment_content = preg_replace('!<img localsrc="[^"]*" />!', '', $comment_content);
	}
	$comment_author  = $wpdb->escape($comment_author);
	$comment_content = $wpdb->escape($comment_content);
}
$comment_author       = trim(strip_tags(mb_convert_encoding($comment_author, get_bloginfo('charset'), $charset)));
$comment_author_email = trim($_POST['email']);
$comment_author_url   = trim($_POST['url']);
$comment_content      = trim(mb_convert_encoding($comment_content, get_bloginfo('charset'), $charset));

// If the user is logged in
$user = wp_get_current_user();
if ( $user->ID ) {
	$comment_author       = $wpdb->escape($user->display_name);
	$comment_author_email = $wpdb->escape($user->user_email);
	$comment_author_url   = $wpdb->escape($user->user_url);
	if ( current_user_can('unfiltered_html') ) {
		if ( wp_create_nonce('unfiltered-html-comment_' . $comment_post_ID) != $_POST['_wp_unfiltered_html_comment'] ) {
			kses_remove_filters(); // start with a clean slate
			kses_init_filters(); // set up the filters
		}
	}
} else {
	if ( get_option('comment_registration') )
		Ktai_Style::ks_die(__('Sorry, you must be logged in to post a comment.'));
}

try {
global $Ktai_Style;
$comment_type = '';

if (! $user->ID && ks_option('ks_require_term_id') && $Ktai_Style->get('sub_ID_available')) {
	if (! $Ktai_Style->get('term_ID') && ! $Ktai_Style->get('sub_ID')) {
		$message = $Ktai_Style->get('require_id_msg');
		if (empty($message)) {
			$message = __('Error: please configure to send your terminal ID (serial number, EZ number etc).', 'ktai_style');
		}
		throw new Exception($message);
	}
}

if (get_option('require_name_email') && ! $user->ID) {
	if ( 6 > strlen($comment_author_email) || '' == $comment_author )
		throw new Exception(__('Error: please fill the required fields (name, email).'));
	elseif (! is_email($comment_author_email))
		throw new Exception(__('Error: please enter a valid email address.'));
}

if ('' == $comment_content) {
	throw new Exception(__('Error: please type a comment.'));
}

$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'user_ID');

global $allowedtags;
if ($allowedtags) {
	$allowedtags['img']['localsrc'] = array();
	$allowedtags['img']['alt'] = array();
}
$comment_id = wp_new_comment($commentdata);
$comment = get_comment($comment_id);

$location = ( empty($_POST['redirect_to']) ? get_permalink($comment_post_ID) : $_POST['redirect_to'] );
$location = apply_filters('comment_post_redirect', $location, $comment);

if ( $user->ID && class_exists('Ktai_Style_Admin')) {
	$admin = new Ktai_Style_Admin;
	$sid = $admin->get_sid();
	if ($sid) {
		$admin->unset_session($sid);
		$admin->unset_prev_session($sid);
		do_action('wp_logout');
	}
}

wp_redirect($location);
exit;

} catch (Exception $e) {
	if (! isset($_POST['inline'])) {
		Ktai_Style::ks_die($e->getMessage());
		exit();
	}
	global $Ktai_Style, $ks_commentdata, $withcomments, $comment_post_ID, $comment_author, $comment_author_email, $comment_author_url, $comment_content;
	$ks_commentdata['author']  = stripslashes($comment_author);
	$ks_commentdata['email']   = stripslashes($comment_author_email);
	$ks_commentdata['url']     = stripslashes($comment_author_url);
	$ks_commentdata['content'] = stripslashes($comment_content);
	$ks_commentdata['message'] = $e->getMessage();
	unset($_POST['author']);
	unset($_POST['email']);
	unset($_POST['url']);
	unset($_POST['comment']);
	$_POST['view'] = 'co_post'; // force ks_is_comment_post() to true
	query_posts("p=$comment_post_ID");
	$withcomments = true;
	$Ktai_Style->output();
	exit;
}
?>