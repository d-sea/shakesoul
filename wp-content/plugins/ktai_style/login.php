<?php
/* ==================================================
 *   Ktai Login Page
   ================================================== */

$wpload_error = 'Could not open the login page because custom WP_PLUGIN_DIR is set.';
require dirname(__FILE__) . '/wp-load.php';
nocache_headers();

if (! isset($Ktai_Style) || ! $Ktai_Style->is_ktai() || @$_COOKIE[KS_COOKIE_PCVIEW] || ! class_exists('Ktai_Style_Admin')) {
	wp_redirect(get_bloginfo('wpurl') . '/wp-login.php');
	exit();
}

define ('KS_ADMIN_MODE', true);
$KS_Login = new Ktai_Style_Login();
exit();

/* ==================================================
 *   Ktai_Style_Login class
   ================================================== */

class Ktai_Style_Login {
	private $parent;
	private $admin;

// ==================================================
public function __construct() {
	global $Ktai_Style;
	$this->parent = $Ktai_Style;
	$this->admin = new Ktai_Style_Admin;
	switch ($_REQUEST['action']) {
	case 'logout':
		$this->logout();
		break;
	default:
		$this->login();
		break;
	}
}

// ==================================================
private function logout() {
	$sid = $this->admin->get_sid();
	$this->admin->unset_session($sid);
	$this->admin->unset_prev_session($sid);
	do_action('wp_logout');
	$redirect_to = 'login.php?loggedout=true';
	if (isset($_POST['redirect_to']) || isset($_GET['redirect_to'])) {
		$redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : $_GET['redirect_to'];
		$redirect_to = $this->admin->shrink_redirect_to($redirect_to);
	}
	$this->admin->safe_redirect($redirect_to);
}

// ==================================================
private function login() {
	global $error;
	$errors = array();
	if ((! isset($_POST['redirect_to']) || empty($_POST['redirect_to'])) && ! isset($_GET['redirect_to'])) {
		$redirect_to = KS_ADMIN_DIR . '/';
	} else {
		$redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : $_GET['redirect_to'];
		$redirect_to = $this->admin->shrink_redirect_to($redirect_to);
	}
	if ($_POST) {
		$user_login = sanitize_user($_POST['log']);
		$user_pass  = $_POST['pwd'];
	} else {
		$user_login = '';
		$user_pass  = '';
	}
	do_action_ref_array('wp_authenticate', array(&$user_login, &$user_pass));
	if ($user_login && $user_pass) {
		$user = new WP_User(0, $user_login);
		if (! $user->has_cap('edit_posts') && (empty($redirect_to) || $redirect_to == KS_ADMIN_DIR . '/')) {
			$redirect_to = KS_ADMIN_DIR . '/profile.php';
		}
		if (wp_login($user_login, $user_pass, false)) {
			if ($this->admin->set_session($user_login)) {
				do_action('wp_login', $user_login);
				$this->admin->safe_redirect($redirect_to);
				exit();
			} else {
				$errors['session'] = __('<strong>ERROR</strong>: Cannot create a login session.', 'ktai_style');
			}
		}
	}
	if ($_POST && empty($user_login)) {
		$errors['user_login'] = __('<strong>ERROR</strong>: The username field is empty.');
	}
	if ($_POST && empty($user_pass)) {
		$errors['user_pass'] = __('<strong>ERROR</strong>: The password field is empty.');
	}
	if (true == $_GET['loggedout']) {
		$errors['loggedout'] = __('You are now logged out.', 'ktai_style');
	}
	if ($error) {
		$errors['error'] = $error;
	}
	if ($this->parent->check_wp_version('2.5', '>=')) {
		$body_color = 'bgcolor="#ccffff" text="#333333" link="#777777" vlink="#777777"';
		$error_color = 'color="red"';
		$logo_file = 'wplogo25-login';
	} else {
		$body_color = 'bgcolor="#006699" text="white" link="#ff99cc" vlink="#ff99cc"';
		$error_color = 'color="#ff99cc"';
		$logo_file = 'wplogo-login';
	}
	if (preg_match('!^' . KS_ADMIN_DIR . '/$!', $redirect_to)) {
		$redirect_to = '';
	}
	$charset      = $this->parent->get('charset');
	$iana_charset = $this->parent->get('iana_charset');
	$mime_type    = 'text/html';
	$this->parent->ktai->set('mime_type', $mime_type); // don't use 'application/xhtml+xml'
	switch ($this->parent->is_ktai()) {
	case 'DoCoMo':
		$logo_ext = '.gif';
		$wrap_start = '';
		$wrap_end = '';
		$style_input = '';
		break;
	case 'Unknown':
		$logo_ext = '.png';
		$style_body = ' style="text-align:center;"';
		$wrap_start = '<div style="width:19em;margin:0 auto;text-align:left;">';
		$wrap_end = '</div>';
		$style_input = 'style="width:100%" ';
		break;
	default:
		$logo_ext = '.png';
		$wrap_start = '';
		$wrap_end = '';
		$style_input = '';
		break;
	}
	ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.0//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd">
<html><head>
<meta http-equiv="Content-Type" content="<?php echo wp_specialchars($mime_type); ?>; charset=<?php echo wp_specialchars($iana_charset); ?>" />
<title><?php bloginfo('name'); ?> &rsaquo; <?php _e('Login'); ?></title>
</head><body <?php echo $body_color . $style_body; ?>><?php echo $wrap_start;
echo apply_filters('ks_login_logo/ktai_style.php', '<center><h1><img src="' . KS_ADMIN_DIR . '/' . $logo_file . $logo_ext . '" alt="WordPress"/></h1></center>', KS_ADMIN_DIR . '/' . $logo_file, $logo_ext); ?>
<br />
<?php if ($errors) { echo "<p><font $error_color>" . apply_filters('login_errors', implode('<br />', $errors)) . "</font></p>\n"; } ?>
<form method="post" action="login.php"><div>
<?php _e('Username:') ?><br /><input type="text" name="log" size="20" istyle="3" mode="alphabet" value="<?php echo attribute_escape(stripslashes($user_login)); ?>" <?php echo $style_input; ?>/><br />
<?php _e('Password:') ?><br /><input type="password" name="pwd" size="20" istyle="3" mode="alphabet" value="" <?php echo $style_input; ?>/></div>
<br />
<?php // do_action('login_form'); ?>
<div><input type="submit" name="wp-submit" value="<?php _e('Login'); ?>" /><input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($redirect_to, ENT_QUOTES); ?>" />
</div></form>
<p><img localsrc="64" alt="<?php _e('&lt;-', 'ktai_style'); ?>" /><a href="<?php bloginfo('url'); ?>/"><?php printf(__('Back to %s', 'ktai_style'), get_bloginfo('title', 'display')); ?></a>
<?php 
	if ($this->parent->is_ktai() == 'Unknown') { 
		echo '<br /><img localsrc="64" alt="' . __('&lt;-', 'ktai_style') . '" /><a href="' . get_bloginfo('wpurl') . '/wp-login.php?pcview=true">' . __('Go to PC login form.', 'ktai_style') . '</a>';
	}
?>
<p><?php echo $wrap_end; ?></body></html>
<?php
	$buffer = ob_get_contents();
	ob_end_clean();
	$buffer = mb_convert_encoding($buffer, $charset, get_bloginfo('charset'));
	$buffer = $this->parent->ktai->convert_pict($buffer);
	$buffer = $this->parent->ktai->shrink_pre_split($buffer);
	$buffer = $this->parent->ktai->shrink_post_split($buffer);
	header ("Content-Type: $mime_type; charset=$iana_charset");
	echo $buffer;
}

// ===== End of class ====================
}
?>