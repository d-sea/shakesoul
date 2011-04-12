<?php if (! ks_is_comments()) {
	return;
}
$need_password = ks_check_password(ks_is_comments_list() ?
	__('Enter your password to view comments.') :
	__('Enter your password to post a comment.', 'ktai_style'));
if ($need_password && ! $user_ID) : 
	echo $need_password;
elseif (ks_is_comments_list()) :
	ks_comments_post_link(NULL, '', '<hr />', '');
	if (ks_option('ks_separate_comments')) {
		$sep_comments = array(array(),array());
		if ($comments) :
			foreach ($comments as $comment) :
				if ($comment->comment_type) {
					$sep_comments[1][] = $comment;
				} else {
					$sep_comments[0][] = $comment;
				}
			endforeach;
		endif;
		$label = array(__('Comments'), __('Trackbacks and Pingbacks', 'ktai_style'));
	} else {
		$sep_comments[0] = $comments;
		$label = array('');
	}
	for ($type = 0 ; $type < count($sep_comments) ; $type++) :
		if ($label[$type]) {
			echo '<h3>' . $label[$type] . '</h3>';
		}
		if (count($sep_comments[$type])) : ?>
			<ul>
			<?php foreach ($sep_comments[$type] as $comment) : ?>
				<li><a name="comment-<?php comment_ID(); ?>"><?php 
				if (! ks_option('ks_separate_comments')) {
					?>[<?php comment_type(__('Comment'), __('Trackback'), __('Pingback')); ?>]
				<?php } ?>
				</a> <?php ks_comment_author_link(); ?>@ <?php ks_comment_datetime(); ?><br />
				<?php comment_text() ?></li>
			<?php endforeach; ?>
			</ul>
		<?php else : // If there are no comments yet ?>
			<p><?php echo ($type == 0) ? 
				__('No comments yet.') : 
				__('No trackbacks yet.', 'ktai_style');
			?></p>
		<?php endif;
	endfor; 
elseif (ks_is_comment_post()) :
	ks_comments_link('', __('No Comments/Trackbacks', 'ktai_style'), __('One Comment/Trackback', 'ktai_style'), __('% Comments and Trackbacks', 'ktai_style')); ?>
	<hr />
	<?php if (! comments_open()) { ?>
		<p><?php _e('Sorry, the comment form is closed at this time.'); ?></p>
	<?php } elseif ( get_option('comment_registration') && ! $user_ID ) {
		if (ks_admin_url(false)) { ?>
			<p><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.'), ks_plugin_url(false) . 'login.php?redirect_to=' . urlencode(ks_comments_post_url()));?></p>
		<?php } else { ?>
			<p><?php _e('Can\'t post a comment from mobile phones. You must logged in from PC to make a comment.', 'ktai_style'); ?></p>
		<?php }
	} else {
		global $ks_commentdata;
		if (isset($ks_commentdata['message']) && $ks_commentdata['message']) {
			$comment_author       = $ks_commentdata['author'];
			$comment_author_email = $ks_commentdata['email'];
			$comment_author_url   = $ks_commentdata['url'];
			$comment_content      = $ks_commentdata['content'];
			?><p><font color="red">
			<?php echo implode("<br />", wp_specialchars(explode("\n", $ks_commentdata['message']))); ?>
			</font></p>
		<?php }
		ks_require_term_id_form(ks_plugin_url(false) . 'comments-post.php');
		ks_fix_encoding_form();
		if ( $user_ID ) {
			ks_session_id_form(); ?>
			<p><?php printf(__('Logged in as %s.', 'ktai_style'), wp_specialchars($user_identity));
			?> [<a href="<?php echo attribute_escape(ks_get_logout_url()); ?>"><?php _e('Logout'); ?></a>]<br />
			<small><?php _e('Note: Ater posting a comment, you are automatically logged out.', 'ktai_style'); ?></small><br />
		<?php } else { ?>
			<div align="right"><?php printf(__('<a href="%s">Log in</a> and post a comment.', 'ktai_style'), ks_plugin_url(false) . 'login.php?redirect_to=' . urlencode(ks_comments_post_url()));?></div>
			<p><?php _e('Name'); if ($req) _e('(required)'); ?><br />
			<input type="text" name="author" value="<?php echo attribute_escape($comment_author); ?>" size="12" /><br />
			<?php _e('Mail (will not be published)', 'ktai_style'); if ($req) _e('(required)'); ?><br />
			<input type="text" name="email" istyle="3" mode="alphabet" value="<?php echo attribute_escape($comment_author_email); ?>" /><br />
			<?php _e('Website'); ?><br />
			<input type="text" name="url" istyle="3" mode="alphabet" value="<?php echo attribute_escape($comment_author_url); ?>" /><br />
		<?php } // $user_ID ?>
		<?php _e('Comment');
		if (ks_option('ks_allow_pictograms')) {
			_e('(Pictograms Available)', 'ktai_style');
		} ?>
		<br />
		<textarea name="comment" cols="100%" rows="4"><?php echo wp_specialchars($comment_content); ?></textarea><br />
		<input type="submit" name="submit" value="<?php _e('Say It!'); ?>" />
		<input type="hidden" name="comment_post_ID" value="<?php echo intval($id); ?>" /></p>
		<?php ks_do_comment_form_action();?>
		</form>
		<?php if (ks_is_required_term_id()) { ?>
			<div><?php _e('NOTE: If submit comments, your terminal ID will be sent.', 'ktai_style'); ?></div>
		<?php }
	} // comments_open, comment_registration
endif; // $need_password, ks_is_comments_list, ks_is_comments_post ?>