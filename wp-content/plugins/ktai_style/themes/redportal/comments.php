<?php if (! ks_is_comments()) {
	return;
}
$need_password = ks_check_password(ks_is_comments_list() ? 
	__('Enter your password to view comments.') : 
	__('Enter your password to post a comment.', 'ktai_style'));
if ($need_password && ! $user_ID) : 
	echo $need_password;
elseif (ks_is_comments_list()) : ?>
	<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>へのコメント一覧</h2>
	<hr color="red" />
	<div align="center">最新順 | <font color="gray">古い順</font></div>
	<?php ks_comments_post_link('コメントを書く', '', '', ks_pict_number(1), '1');
	$sep_comments = array(array(),array());
	if ($comments) :
		foreach ($comments as $comment) :
			if ($comment->comment_type) {
				array_unshift($sep_comments[1], $comment);
			} else {
				array_unshift($sep_comments[0], $comment);
			}
		endforeach;
	endif;
	$label = array(__('Comments'), __('Trackbacks and Pingbacks', 'ktai_style'));
	for ($type = 0 ; $type < count($sep_comments) ; $type++) :
		if ($label[$type]) {
			echo '<hr color="red" /><h3>' . $label[$type] . '</h3>';
		}
		if (count($sep_comments[$type])) : ?>
			<ol>
			<?php foreach ($sep_comments[$type] as $comment) : ?>
				<li><a name="comment-<?php comment_ID(); ?>"></a> <img localsrc="<?php comment_type(68, 112, 112); ?>" alt="" /><?php ks_comment_author_link(); 
				?><img localsrc="46" alt=" @ " /><font color="red"><?php ks_comment_datetime(); ?></font><br />
				<?php comment_text() ?></li>
			<?php endforeach; ?>
			</ol>
		<?php else : // If there are no comments yet ?>
			<p><?php echo ($type == 0) ? 
				__('No comments yet.') : 
				__('No trackbacks/pingbacks yet.', 'ktai_style');
			?></p>
		<?php endif;
	endfor;
	ks_comments_post_link('コメントを書く', '<hr color="red" />', '', ks_pict_number(1), '1');
elseif (ks_is_comment_post()) : ?>
	<h2 align="center">コメント</h2>
	<hr color="red" />
	<?php if (! comments_open()) { ?>
		<p><?php _e('Sorry, the comment form is closed at this time.'); ?></p>
	<?php } elseif (get_option('comment_registration') && ! $user_ID) {
		if (ks_admin_url(false)) { ?>
			<p><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.'), ks_plugin_url(false) . 'login.php?redirect_to=' . urlencode(ks_comments_post_url()));?></p>
		<?php } else { ?>
			<p><?php _e('Can\'t post a comment from mobile phones. You must logged in from PC to make a comment.', 'ktai_style'); ?></p>
		<?php }
	} else { 
		?>「<?php bloginfo('name'); ?>」<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> にコメントします。<br /><?php
		global $ks_commentdata;
		if (isset($ks_commentdata['message']) && $ks_commentdata['message']) {
			$comment_author       = $ks_commentdata['author'];
			$comment_author_email = $ks_commentdata['email'];
			$comment_author_url   = $ks_commentdata['url'];
			$comment_content      = $ks_commentdata['content'];
			?><p>以下の項目をご確認下さい</p>
			<ul><font color="blue">
			<?php echo implode('</font></li><li><font color="blue">', wp_specialchars(explode("\n", $ks_commentdata['message']))); ?>
			</font></li></ul>
		<?php }
		ks_require_term_id_form(ks_plugin_url(false) . 'comments-post.php');
		ks_fix_encoding_form();
		if ( $user_ID ) {
			ks_session_id_form(); ?>
			<p><?php printf(__('Logged in as %s.', 'ktai_style'), wp_specialchars($user_identity));
			?> [<a href="<?php echo attribute_escape(ks_get_logout_url()); ?>"><?php _e('Logout'); ?></a>]<br />
			<small><?php _e('Note: Ater posting a comment, you are automatically logged out.', 'ktai_style'); ?></small><br />
			<font color="orange">◆</font><?php _e('Comment');
			if (ks_option('ks_allow_pictograms')) {
				_e('(Pictograms Available)', 'ktai_style');
			} ?>
			<br />
			<textarea name="comment" cols="100%" rows="4"><?php echo wp_specialchars($comment_content); ?></textarea><br />
		<?php } else { ?>
			<div align="right"><img localsrc="120" alt="" /><?php printf(__('<a href="%s">Log in</a> and post a comment.', 'ktai_style'), ks_plugin_url(false) . 'login.php?redirect_to=' . urlencode(ks_comments_post_url()));?></div>
			<p><font color="orange">◆</font>ニックネーム<?php if ($req) _e('(required)'); ?><br />
			<input type="text" name="author" value="<?php echo attribute_escape($comment_author); ?>" size="12" /><br />
			<font color="orange">◆</font><?php _e('Comment');
			if (ks_option('ks_allow_pictograms')) {
				_e('(Pictograms Available)', 'ktai_style');
			} ?><br />
			<textarea name="comment" cols="100%" rows="4"><?php echo wp_specialchars($comment_content); ?></textarea><br />
			<font color="orange">◆</font><?php _e('Mail (will not be published)', 'ktai_style'); if ($req) _e('(required)'); ?><br />
			<input type="text" name="email" istyle="3" mode="alphabet" value="<?php echo attribute_escape($comment_author_email); ?>" /><br />
			<font color="orange">◆</font>URL<br />
			<input type="text" name="url" istyle="3" mode="alphabet" value="<?php echo attribute_escape($comment_author_url); ?>" /><br />
		<?php } // $user_ID ?>
		<?php ks_inline_error_submit('投稿する'); ?>
		<input type="hidden" name="comment_post_ID" value="<?php echo intval($id); ?>" /></p>
		<?php ks_do_comment_form_action(); ?>
		</form>
		<?php if (ks_is_required_term_id()) { ?>
			<div><font color="orange">◆</font><?php _e('NOTE: If submit comments, your terminal ID will be sent.', 'ktai_style'); ?></div>
		<?php }
	} // comments_open, comment_registration
endif; // $need_password, ks_is_comments_list, ks_is_comments_post
?>