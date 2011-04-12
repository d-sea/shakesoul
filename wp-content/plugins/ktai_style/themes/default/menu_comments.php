<?php ks_header(); ?>
<!--start paging-->
<?php global $post, $comment, $ol_count, $ol_max;
$ol_count = isset($ol_count) ? $ol_count : 1;
$ol_max = isset($ol_max) ? $ol_max : 9;
for ($loop = ks_option('ks_separate_comments') ? 0 : 1 ; 
	 $loop <= 2 ;
	 $loop += 2 ) {
	if ($loop <= 1) { ?>
		<h2 id="comments"><?php _e('Recent Comments', 'ktai_style'); ?></h2>
	<?php } else { ?>
		<br /><h2 id="trackbacks"><?php _e('Recent Tracbacks/Pingbacks', 'ktai_style'); ?></h2>
	<?php }
	switch ($loop) {
	case 1:
		$comments = ks_get_recent_comments();
		break;
	case 0:
		$comments = ks_get_recent_comments(8, 'comment');
		break;
	case 2:
		$comments = ks_get_recent_comments(8, 'trackback+pingback');
		break;
	}
	if ($comments) : ?>
		<dl>
		<?php for (; $target = array_shift($comments) ; $ol_count++) :
			$post = array_shift($target);
			$link = ks_get_comments_list_link($post->ID); ?>
			<dt><?php ks_ordered_link($ol_count, $ol_max, $link, get_the_title()); ?></dt>
			<?php if (empty($post->post_password)) {
				foreach ($target as $comment) : ?>
					<dd><?php 
					if (! ks_option('ks_separate_comments')) {
						?><font size="-1" color="<?php echo ks_option('ks_comment_type_color'); ?>">[<?php 
						comment_type(__('Comment'), __('Trackback'), __('Pingback')); ?>]</font> <?php 
					}
					?><img localsrc="<?php comment_type(68, 112, 112); ?>" alt="" />
					<a href="<?php echo attribute_escape($link . '#comment-' . get_comment_ID()); ?>"><?php comment_author(); ?></a>
					<img localsrc="46" alt=" @ " /><font color="<?php echo ks_option('ks_date_color'); ?>"><?php ks_comment_datetime(); ?></font>
					</dd>
				<?php endforeach; ?>
			<?php } else { ?>
				<dd><?php _e('Can\'t show comments because this post is password protected.', 'ktai_style'); ?></dd>
			<?php } // post_password
		endfor; ?>
		</dl>
	<?php else: ?>
		<p><?php $loop <= 1 ? 
			_e('No comments yet.', 'ktai_style') : 
			_e('No trackbacks/pingbacks yet.', 'ktai_style');
		?></p>
	<?php endif; // $comments
} // $loop
ks_footer(); ?>