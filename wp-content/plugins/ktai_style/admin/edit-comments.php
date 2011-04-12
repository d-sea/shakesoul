<?php
/* ==================================================
 *   Ktai Admin Edit Comments
 *   based on wp-admin/edit-comments.php of WP 2.3
   ================================================== */

define ('KS_COMMENT_EXCERPT_SIZE', 200);
require dirname(__FILE__) . '/admin.php';
$Page = new KS_Admin_Edit_Comments;
$title = __('Edit Comments');
$parent_file = 'edit-comments.php';
include dirname(__FILE__) . '/admin-header.php';

$per = intval(($Ktai_Style->get('page_size') - 2280) / (KS_COMMENT_EXCERPT_SIZE + 480));
if ($per < 1) {
	$per = 3;
} elseif ($per > 20) {
	$per = 20;
}
$page_num = isset($_GET['apage']) ? abs((int) $_GET['apage']) : 1;
$start = $offset = ( $page_num - 1 ) * $per;

if (empty($_GET['p']) || intval($_GET['p']) < 1) { ?>
<h2><?php _e('Comments'); ?></h2>
<form name="searchform" action="<?php echo basename(__FILE__); ?>" method="get">
<?php $KS_Admin->sid_field(); ?>
<input type="text" name="ks" value="<?php if (isset($_GET['s'])) { echo attribute_escape($_GET['s']); } ?>" size="20" />
<input type="submit" name="Submit" value="<?php _e('Search') ?>"  />  
</form>
<?php
	if ($Ktai_Style->check_wp_version('2.5', '>=')) {
		list($comments, $total) = _wp_get_comment_list('', isset($_GET['s']) ? $_GET['s'] : false, $start, $per);
	} else {
		list($comments, $total) = _wp_get_comment_list( isset($_GET['s']) ? $_GET['s'] : false, $start, $per);
	}
} else {
	$id = intval($_GET['p']); ?>
<h2><?php printf(__('Comments for %s', 'ktai_style'), $Page->get_post_title($id)); ?></h2>
<?php
	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $id AND comment_approved != 'spam' ORDER BY comment_date");
	$total = count($comments);
} // $_GET['p']

if ($comments) {
	echo '<dl>';
	foreach ($comments as $c) {
		echo '<dt>' . $c->comment_ID . ':' . $Page->get_post_title($c->comment_post_ID) . '</dt><dd>';
		$Page->comment_list_item($c);
		echo '</dd>';
	}
	echo '</dl>';
} else {
?><p><?php
	_e('No comments found.');
?></p><?php
} // end if ($comments)
$page_links = paginate_links( array(
	'base' => add_query_arg('apage', '%#%'), 
	'format' => '',
	'total' => ceil($total / $per),
	'current' => $page_num,
	'prev_text' => '<img localsrc="7" alt="&laquo;" />' . __('Previous Page', 'ktai_style'),
	'next_text' => __('Next Page', 'ktai_style') . '<img localsrc="8" alt="&raquo;" />'
));
if ($page_links) {
	$page_links = $Ktai_Style->filter_tags($page_links);
	$page_links = str_replace("\n", ' ', $page_links);
	$page_links = str_replace(ks_admin_url(false), '', $page_links);
	echo "<p>$page_links</p>";
}
include dirname(__FILE__) . '/admin-footer.php';
exit();

/* ==================================================
 *   KS_Admin_Edit_Comments class
   ================================================== */

class KS_Admin_Edit_Comments {

// ==================================================
public function get_post_title($id) {
	if ($id > 0) {
		$post = get_post($id, OBJECT, 'display');
		$post_title = wp_specialchars( $post->post_title, 'double' );
		$post_title = ('' == $post_title) ? "# $comment->comment_post_ID" : $post_title;
	} else {
		$post_title = NULL;
	}
	return $post_title;
}

// ==================================================
public function comment_list_item($c) {
	global $wpdb, $comment, $KS_Admin;
	$comment = $c;
	$comment_status = wp_get_comment_status($comment->comment_ID);
	if ('unapproved' == $comment_status ) {
		$gray_start = '<font color="gray">';
		$gray_end   = '</font>';
		$date_start = '';
		$link_color = 'gray';
	} else {
		$gray_start = '';
		$gray_end   = '';
		$date_start = '<font color="' . ks_option('ks_date_color') . '">';
		$link_color = 'olive';
	}
	echo $gray_start;
?>
<img localsrc="<?php comment_type(68, 112, 112); ?>" alt="[<?php comment_type(__('Comment'), __('Trackback'), __('Pingback')); ?>] " /><?php echo comment_author(); ?><img localsrc="46" alt=" @ " /><?php echo $date_start; ks_comment_datetime(); ?></font><br />
<?php /******
	if ($comment->comment_author_email) { 
		?><img localsrc="108" alt="" /><font color="<?php echo $link_color; ?>"><?php comment_author_email(); ?></font><br /><?php
	}
	if ($comment->comment_author_url && 'http://' != $comment->comment_author_url) {
		?><img localsrc="112" alt="" /><font color="<?php echo $link_color; ?>"><?php comment_author_url(); ?></font><br /><?php 
	} *****/ 
	echo $gray_start . mb_strcut(get_comment_excerpt(), 0, KS_COMMENT_EXCERPT_SIZE) . $gray_end . '<br />';
	if ( current_user_can('edit_post', $comment->comment_post_ID) ) {
		echo '[ <a href="' . $KS_Admin->add_sid('comment.php?action=editcomment&c=' . $comment->comment_ID) . '">' .  __('Edit') . '</a>';
		echo ' | <a href="' . wp_nonce_url($KS_Admin->add_sid('comment.php?action=deletecomment&p=' . $comment->comment_post_ID . '&c=' . $comment->comment_ID, 'delete-comment_' . $comment->comment_ID)) . '">' . __('Delete') . '</a> ';
		if ( ('none' != $comment_status) && ( current_user_can('moderate_comments') ) ) {
			if ('unapproved' != $comment_status) { 
				echo ' | <a href="' . wp_nonce_url($KS_Admin->add_sid('comment.php?action=unapprovecomment&p=' . $comment->comment_post_ID . '&c=' . $comment->comment_ID, 'unapprove-comment_' . $comment->comment_ID)) . '">' . __('Unapprove') . '</a>';
			} else {
				echo ' | <a href="' . wp_nonce_url($KS_Admin->add_sid('comment.php?action=approvecomment&p=' . $comment->comment_post_ID . '&c=' . $comment->comment_ID, 'approve-comment_' . $comment->comment_ID)) . '">' . __('Approve') . '</a>';
			}
		}
/*****
		echo ' | <a href="' . $KS_Admin->add_sid("comment.php?action=deletecomment&dt=spam&p=" . $comment->comment_post_ID . "&c=" . $comment->comment_ID, 'delete-comment_' . $comment->comment_ID) .'">' . __('Spam') . '</a> ';
*****/
		echo ' ]';
	}
}

// ===== End of class ====================
}
?>