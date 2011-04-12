<?php
/* ==================================================
 *   Ktai Admin Moderate Comments
 *   based on wp-admin/edit.php of WP 2.3
   ================================================== */

define ('KS_COMMENT_EXCERPT_SIZE', 100);
require dirname(__FILE__) . '/admin.php';
$Page = new KS_Admin_Moderate($KS_Admin);
$title = __('Moderate Comments', 'ktai_style');
$parent_file = 'edit-comments.php';

wp_reset_vars(array('action', 'feelinglucky'));

if ($action == 'update') {
	check_admin_referer('moderate-comments');
	if (! current_user_can('moderate_comments')) {
		Ktai_Style::ks_die(__('Your level is not high enough to moderate comments.' ));
	}
	$Page->moderate($feelinglucky);
	exit();
}

include dirname(__FILE__) . '/admin-header.php';
if (! current_user_can('moderate_comments')) {
	echo '<p><font color="red">' . __('Your level is not high enough to moderate comments.') . '</font></p>';
} else {
	$Page->result();
	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_approved = '0'");
	if (! $comments) {
		echo '<p>' . __('Currently there are no comments for you to moderate.', 'ktai_style') . '</p>';
	} else {
		$Page->list_queue($comments);
	}
}
include dirname(__FILE__) . '/admin-footer.php';
exit();

/* ==================================================
 *   KS_Admin_Moderate class
   ================================================== */

class KS_Admin_Moderate {
	private $admin;

// ==================================================
function __construct($admin) {
	$this->admin  = $admin;
}

// ==================================================
function moderate($feelinglucky) {
	$comment = array();
	if ( isset( $_POST['comment'] ) && is_array( $_POST['comment'] ) ) {
		foreach ( $_POST['comment'] as $k => $v ) {
			$comment[intval($k)] = $v;
		}
	}
	$item_ignored = 0;
	$item_deleted = 0;
	$item_approved = 0;
	$item_spam = 0;
	foreach ( $comment as $k => $v ) {
		if ( $feelinglucky && $v == 'later' ) {
			$v = 'delete';
		}
		switch ($v) {
		case 'later' :
			$item_ignored++;
			break;
		case 'delete' :
			wp_set_comment_status($k, 'delete');
			$item_deleted++;
			break;
		case 'spam' :
			wp_set_comment_status($k, 'spam');
			$item_spam++;
			break;
		case 'approve' :
			wp_set_comment_status($k, 'approve');
			if (get_option('comments_notify') == true ) {
				wp_notify_postauthor($k);
			}
			$item_approved++;
			break;
		}
	}
	$this->admin->redirect(basename(__FILE__) . '?ignored=' . $item_ignored . '&deleted=' . $item_deleted . '&approved=' . $item_approved . '&spam=' . $item_spam);
}

// ==================================================
function result() {
	global $wpdb;
	if ( isset( $_GET['approved'] ) || isset( $_GET['deleted'] ) || isset( $_GET['spam'] ) ) {
		$approved = isset( $_GET['approved'] ) ? (int) $_GET['approved'] : 0;
		$deleted = isset( $_GET['deleted'] ) ? (int) $_GET['deleted'] : 0;
		$spam = isset( $_GET['ignored'] ) ? (int) $_GET['spam'] : 0;
		$messages = array();
		if ( $approved > 0 ) {
			$messages[] = sprintf(__ngettext('%s comment approved.', '%s comments approved.', $approved, 'ktai_style'), $approved);
		}
		if ( $deleted > 0 ) {
			$messages[] = sprintf(__ngettext('%s comment deleted.', '%s comments deleted.', $deleted, 'ktai_style'), $deleted);
		}
		if ( $spam > 0 ) {
			$messages[] = sprintf(__ngettext('%s comment marked as spam.', '%s comments marked as spam.', $spam, 'ktai_style'), $spam);
		}
		if ($messages) {
			echo '<p><font color="blue">' . implode('<br />', $messages) . '</font></p>';
		}
	}
	return;
}

// ==================================================
function list_queue($comments) {
	global $comment, $Ktai_Style;
	$per = intval(($Ktai_Style->get('page_size') - 2600) / (KS_COMMENT_EXCERPT_SIZE + 700));
	if ($per < 1) {
		$per = 2;
	} elseif ($per > 20) {
		$per = 20;
	}
	$total = count($comments);
	$page_num = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
	$start = ($page_num - 1) * $per;
	$page_links = paginate_links(array(
		'base' => add_query_arg('paged', '%#%'),
		'format' => '',
		'total' => ceil($total / $per),
		'current' => $page_num,
		'prev_text' => '<img localsrc="7" alt="&laquo;" />' . __('Previous Page', 'ktai_style'),
		'next_text' => __('Next Page', 'ktai_style') . '<img localsrc="8" alt="&raquo;" />'
	));
	$comments = array_slice($comments, $start, $per);
?>
<h2><?php printf(__('Awaiting Moderation (%s)', 'ktai_style'), $total); ?></h2>
<?php
	if ( $page_links ) {
		$page_links = $Ktai_Style->filter_tags($page_links);
		$page_links = str_replace("\n", ' ', $page_links);
		$page_links = str_replace(ks_admin_url(false), '', $page_links);
		echo "<p>$page_links</p>";
	}
?>
<form action="<?php echo basename(__FILE__); ?>" method="post">
<?php $this->admin->sid_field(); wp_nonce_field('moderate-comments'); ?>
<input type="hidden" name="action" value="update" />
<dl><?php
	foreach ( $comments as $comment ) {
		?><dt><?php echo intval($comment->comment_ID); ?>:<img localsrc="<?php comment_type(68, 112, 112); ?>" alt="[<?php comment_type(__('Comment'), __('Trackback'), __('Pingback')); ?>] " /><?php comment_author(); ?><img localsrc="46" alt=" @ " /><?php echo '<font color="' . ks_option('ks_date_color') . '">'; ks_comment_datetime(); ?></font></dt><dd><?php
		if ( !empty( $comment->comment_author_email ) ) {
			?><img localsrc="108" alt="" /><font color="olive"><?php comment_author_email(); ?></font><br /><?php
		}
		if ( !empty( $comment->comment_author_url ) && $comment->comment_author_url != 'http://' ) {
			?><img localsrc="112" alt="" /><font color="olive"><?php comment_author_url(); ?></font><br /><?php
		}
		echo mb_strcut(get_comment_excerpt(), 0, KS_COMMENT_EXCERPT_SIZE); ?><br />[<?php
//		echo ' <a href="' . $this->admin->add_sid('comment.php?action=editcomment&c=' . $comment->comment_ID) . '">' .  __('Edit') . '</a> |';
		echo ' <a href="' . wp_nonce_url($this->admin->add_sid('comment.php?action=deletecomment&p=' . $comment->comment_post_ID . '&c=' . $comment->comment_ID, 'delete-comment_' . $comment->comment_ID)) . '">' . __('Delete') . '</a> ';
?>] <?php _e('Bulk action:', 'ktai_style'); ?>
<input type="radio" name="comment[<?php comment_ID(); ?>]" value="approve" /> <?php _e( 'Approve' ); ?>
<input type="radio" name="comment[<?php comment_ID(); ?>]" value="spam" /> <?php _e( 'Spam' ); ?>
<input type="radio" name="comment[<?php comment_ID(); ?>]" value="delete" /> <?php _e( 'Delete' ); ?>
<input type="radio" name="comment[<?php comment_ID(); ?>]" value="later" checked="checked" /> <?php _e('No action', 'ktai_style');?>
</dd><?php
	} ?>
</dl><p><input name="feelinglucky" type="checkbox" value="true" /> <?php _e('Delete every comment marked \'No action\'. <img localsrc="1" alt="Warning:" />This can\'t be undone.', 'ktai_style'); ?></p>
<p><input type="submit" name="submit" value="<?php _e('Bulk Moderate Comments', 'ktai_style'); ?>" /></p>
</form>
<?php 
}

// ===== End of class ====================
}
?>