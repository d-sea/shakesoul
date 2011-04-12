<?php
/* ==================================================
 *   Ktai Admin Process Comments
 *   based on wp-admin/edit.php,edit-form-comments.php of WP 2.3
   ================================================== */

require dirname(__FILE__) . '/admin.php';
$parent_file = 'edit-comments.php';
$submenu_file = 'edit-comments.php';
$Page = new KS_Admin_Comments($KS_Admin);

/* ==================================================
 *   KS_Admin_Comments class
   ================================================== */

class KS_Admin_Comments {
	private $admin;
	
public function __construct($admin) {
	$this->admin  = $admin;

	global $action;
	wp_reset_vars(array('action'));
	switch($action) {
	case 'editcomment':
		$this->edit_form();
		break;
	case 'cdc':
	case 'mac':
		$this->cdc_mac($action);
		break;
	case 'deletecomment':
		$this->delete_comment();
		exit();
	case 'unapprovecomment':
		$this->unapprove_comment();
		exit();
	case 'approvecomment':
		$this->approve_comment();
		exit();
	case 'editedcomment':
		$this->edited_comment();
		exit();
	default:
		break;
	}
}

// ==================================================
private function edit_form() {
	global $user_ID;
	$comment = intval($_GET['c']);
	if (! $comment = get_comment($comment)) {
		Ktai_Style::ks_die(__('Oops, no comment with this ID.'));
	}
	if (! current_user_can('edit_post', $comment->comment_post_ID)) {
		Ktai_Style::ks_die(__('You are not allowed to edit comments on this post.'));
	}
	$title = __('Edit Comment');
	include dirname(__FILE__) . '/admin-header.php';
	$comment = get_comment_to_edit($comment);
	$submitbutton_text = __('Edit Comment');
	$toprow_title = sprintf(__('Editing Comment # %s'), $comment->comment_ID);
	$form_action = 'editedcomment';
	$form_extra = "<input type='hidden' name='comment_ID' value='" . $comment->comment_ID . "' /><input type='hidden' name='comment_post_ID' value='" . $comment->comment_post_ID . "' />";
?>
<h2><?php echo $toprow_title; ?></h2>
<form action="comment.php" method="post">
<?php $this->admin->sid_field(); ks_fix_encoding_form(); wp_nonce_field('update-comment_' . $comment->comment_ID) ?>
<input type="hidden" name="user_ID" value="<?php echo intval($user_ID) ?>" />
<input type="hidden" name="action" value="<?php echo $form_action; ?>" /><?php echo $form_extra; ?>
<div><?php _e('Name') ?><br />
<input type="text" name="newcomment_author" size="24" maxlength="99" value="<?php echo attribute_escape( $comment->comment_author ); ?>" tabindex="1" /><br />
<?php _e('E-mail') ?><br />
<input type="text" name="newcomment_author_email" size="30" maxlength="128" value="<?php echo attribute_escape( $comment->comment_author_email ); ?>" tabindex="2" /><br />
<?php _e('URL') ?><br />
<input type="text" name="newcomment_author_url" size="36" maxlength="256" value="<?php echo attribute_escape( $comment->comment_author_url ); ?>" tabindex="3" /><br />
<?php _e('Comment') ?><br />
<textarea rows="6" cols="40" name="content"><?php echo $comment->comment_content ?></textarea><br />
<?php $id = Ktai_Services::read_term_id($comment);
if (count($id)) {
	if ($id[0]) {
		echo '<img localsrc="161" alt="&middot;"/>' . sprintf(__('Term ID: %s', 'ktai_style'), attribute_escape($id[0])) . '<br />';
	}
	if ($id[1]) {
		echo '<img localsrc="56" alt="&middot;"/>' . sprintf(__('USIM ID: %s', 'ktai_style'), attribute_escape($id[1])) . '<br />';
	}
	if ($id[2]) {
		echo '<img localsrc="d170" alt="&middot;"/>' . sprintf(__('Sub ID: %s', 'ktai_style'), attribute_escape($id[2])) . '<br />';
	}
} ?>
<input type="submit" name="editcomment" value="<?php echo $submitbutton_text ?>" /><input name="referredby" type="hidden" value="<?php echo htmlspecialchars($this->admin->get_referer(), ENT_QUOTES); ?>" /><br />
<?php _e('Approval Status', 'ktai_style') ?><br />
<input name="comment_status" type="radio" value="1" <?php checked($comment->comment_approved, '1'); ?> /> <?php _e('Approved') ?>
<input name="comment_status" type="radio" value="0" <?php checked($comment->comment_approved, '0'); ?> /> <?php _e('Moderated') ?>
<input name="comment_status" type="radio" value="spam" <?php checked($comment->comment_approved, 'spam'); ?> /> <?php _e('Spam') ?><br />
<img localsrc="61" /><a href="<?php echo $this->admin->add_sid("comment.php?action=deletecomment&c=" . $comment->comment_ID); ?>&noredir=1"><?php _e('Delete this comment', 'ktai_style') ?></a>
<input type="hidden" name="c" value="<?php echo intval($comment->comment_ID) ?>" />
<input type="hidden" name="p" value="<?php echo intval($comment->comment_post_ID) ?>" />
<input type="hidden" name="noredir" value="1" />
</div>
</form>
<?php
	$referer = $this->admin->get_referer();
	if ($referer) {
		echo '<div><img localsrc="64" alt="' . __('&lt;-', 'ktai_style') . '" />' . sprintf(__('Back to <a href="%s">the previous page</a>.', 'ktai_style'), attribute_escape($referer)) . '</div>';
	}
	include dirname(__FILE__) . '/admin-footer.php';
}

// ==================================================
private function cdc_mac($action) {
	global $comment;
	$comment = intval($_GET['c']);
	$formaction   = ('cdc' == $action) ? 'deletecomment'   : 'approvecomment';
	$nonce_action = ('cdc' == $action) ? 'delete-comment_' : 'approve-comment_';
	$nonce_action .= $comment;
	if (! $comment = get_comment_to_edit($comment)) {
		Ktai_Style::ks_die(__('Oops, no comment with this ID.') . sprintf(' <a href="%s">' . __('Go back') . '</a>', $this->admin->add_sid('edit-comments.php')), '', false);
	}
	if (! current_user_can('edit_post', $comment->comment_post_ID)) {
		Ktai_Style::ks_die('cdc' == $action ? __('You are not allowed to delete comments on this post.') : __('You are not allowed to edit comments on this post, so you cannot approve this comment.'));
	}
	include dirname(__FILE__) . '/admin-header.php';
	if ('spam' == $_GET['dt']) {
		$message = __('You are about to mark the following comment as spam:');
	} elseif ('cdc' == $action) {
		$message = __('You are about to delete the following comment:');
	} else {
		$message = __('You are about to approve the following comment:');
	}
	echo '<p><img localsrc="1" alt="" /><font color="red">' . $message . '</font><br />' . __('Are you sure you want to do that?') . '</p>';
?>
<form action="edit-comments.php" method="get">
<?php $this->admin->sid_field(); ?>
<div><input type="submit" value="<?php _e('No'); ?>" /></div></form>
<form action="comment.php" method="get">
<?php $this->admin->sid_field(); wp_nonce_field($nonce_action); ?>
<input type="hidden" name="action" value="<?php echo $formaction; ?>" />
<?php if ('spam' == $_GET['dt']) { ?>
<input type="hidden" name="dt" value="spam" />
<?php } ?>
<input type="hidden" name="p" value="<?php echo $comment->comment_post_ID; ?>" />
<input type="hidden" name="c" value="<?php echo $comment->comment_ID; ?>" />
<input type="hidden" name="noredir" value="1" />
<div><input type="submit" value="<?php _e('Yes'); ?>" /></div>
</form>
<dl><dt><img localsrc="<?php comment_type(68, 112, 112); ?>" alt="[<?php comment_type(__('Comment'), __('Trackback'), __('Pingback')); ?>] " /><?php comment_author(); ?><img localsrc="46" alt=" @ " /><font color="<?php echo ks_option('ks_date_color'); ?>"><?php ks_comment_datetime(); ?></font></dt><dd><?php 
	if ($comment->comment_author_email) { 
		?><img localsrc="108" alt="" /><font color="olive"><?php comment_author_email(); ?></font><br /><?php
	}
	if ($comment->comment_author_url && 'http://' != $comment->comment_author_url) {
		?><img localsrc="112" alt="" /><font color="olive"><?php comment_author_url(); ?></font><br /><?php 
	}
	comment_excerpt(); ?></dd></dl><?php
	include dirname(__FILE__) . '/admin-footer.php';
}

// ==================================================
private function delete_comment() {
	$comment = intval($_REQUEST['c']);
	check_admin_referer('delete-comment_' . $comment);
	if ( isset($_REQUEST['noredir']) ) {
		$noredir = true;
	} else {
		$noredir = false;
	}
	if (! $comment = get_comment($comment) ) {
		 Ktai_Style::ks_die(__('Oops, no comment with this ID.') . sprintf(' <a href="%s">'.__('Go back').'</a>!', $this->admin->add_sid('edit-comments.php')), '', false);
	}
	if (! current_user_can('edit_post', $comment->comment_post_ID) ) {
		Ktai_Style::ks_die(__('You are not allowed to edit comments on this post.'));
	}
	if ( 'spam' == $_REQUEST['dt'] ) {
		wp_set_comment_status($comment->comment_ID, 'spam');
	} else {
		wp_delete_comment($comment->comment_ID);
	}
	if (($this->admin->get_referer() != '') && (false == $noredir)) {
		$this->admin->redirect($this->admin->get_referer());
	} else {
		$this->admin->redirect(ks_admin_url(false) .'edit-comments.php');
	}
}

// ==================================================
private function unapprove_comment() {
	$comment = intval($_GET['c']);
	check_admin_referer('unapprove-comment_' . $comment);
	if (isset($_GET['noredir'])) {
		$noredir = true;
	} else {
		$noredir = false;
	}
	if ( ! $comment = get_comment($comment) ) {
		Ktai_Style::ks_die(__('Oops, no comment with this ID.') . sprintf(' <a href="%s">'.__('Go back').'</a>!', $this->admin->add_sid('edit-comments.php')), '', false);
	}
	if (! current_user_can('edit_post', $comment->comment_post_ID) ) {
		Ktai_Style::ks_die(__('You are not allowed to edit comments on this post, so you cannot disapprove this comment.'));
	}
	wp_set_comment_status($comment->comment_ID, "hold");
	if (($this->admin->get_referer() != "") && (false == $noredir)) {
		$this->admin->redirect($this->admin->get_referer());
	} else {
		$this->admin->redirect(ks_admin_url(false) .'edit-comments.php?p=' . intval($comment->comment_post_ID));
	}
}

// ==================================================
private function approve_comment() {
	$comment = intval($_GET['c']);
	check_admin_referer('approve-comment_' . $comment);
	if (isset($_GET['noredir'])) {
		$noredir = true;
	} else {
		$noredir = false;
	}
	if (! $comment = get_comment($comment)) {
		Ktai_Style::ks_die(__('Oops, no comment with this ID.') . sprintf(' <a href="%s">'.__('Go back').'</a>!', $this->admin->add_sid('edit-comments.php')), '', false);
	}
	if (! current_user_can('edit_post', $comment->comment_post_ID)) {
		Ktai_Style::ks_die(__('You are not allowed to edit comments on this post, so you cannot approve this comment.'));
	}
	wp_set_comment_status($comment->comment_ID, "approve");
	if (get_option("comments_notify") == true) {
		wp_notify_postauthor($comment->comment_ID);
	}
	if (($this->admin->get_referer() != "") && (false == $noredir)) {
		$this->admin->redirect($this->admin->get_referer());
	} else {
		$this->admin->redirect(ks_admin_url(false) .'edit-comments.php?p=' . intval($comment->comment_post_ID));
	}
}

// ==================================================
private function edited_comment() {
	$comment_ID = intval($_POST['comment_ID']);
	$comment_post_ID = intval($_POST['comment_post_ID']);
	check_admin_referer('update-comment_' . $comment_ID);
	$this->edit_comment($comment_ID, $comment_post_ID);
	$location = ( empty($_POST['referredby']) ? "edit-comments.php?p=$comment_post_ID" : $_POST['referredby'] );
	$location = apply_filters('comment_edit_redirect', $location, $comment_ID);
	$this->admin->redirect($location);
}

/* ==================================================
 * @param	none
 * @return	none
 * based on edit_post() at wp-admin/includes/post.php of WP 2.3
 */
private function edit_comment($comment_ID, $comment_post_ID) {
	if (! current_user_can('edit_post', $comment_post_ID)) {
		Ktai_Style::ks_die(__('You are not allowed to edit comments on this post, so you cannot edit this comment.'));
	}
	$charset = ks_detect_encoding();
	$_POST['comment_author'] = trim(strip_tags(ks_mb_get_form('newcomment_author', $charset)));
	$_POST['comment_author_email'] = trim($_POST['newcomment_author_email']);
	$_POST['comment_author_url'] = trim($_POST['newcomment_author_url']);
	$_POST['comment_approved'] = trim($_POST['comment_status']);
	$_POST['comment_content'] = trim(ks_mb_get_form('content', $charset));
	$_POST['comment_ID'] = intval($_POST['comment_ID']);
	wp_update_comment($_POST);
}

// ===== End of class ====================
}
?>