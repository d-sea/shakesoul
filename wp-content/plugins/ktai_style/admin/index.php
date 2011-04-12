<?php
/* ==================================================
 *   Ktai Admin Dashboard
 *   based on wp-admin/index.php of WP 2.3
   ================================================== */

require dirname(__FILE__) . '/admin.php';
$title = __('Dashboard');
$parent_file = './';
$today = current_time('mysql', 1);
include dirname(__FILE__) . '/admin-header.php'; ?>
<h2><?php echo ($Ktai_Style->check_wp_version('2.5', '>=') ? __('Right Now') : __('Blog Stats')); ?></h2>
<?php
if (function_exists('wp_count_posts')) {
	$num_posts = wp_count_posts('post');
} else {
	$count = $wpdb->get_results("SELECT post_status, COUNT(*) AS num_posts FROM {$wpdb->posts} WHERE post_type = 'post' GROUP BY post_status", ARRAY_A);
	$num_posts = array();
	foreach( (array) $count as $row_num => $row ) {
		$num_posts[$row['post_status']] = $row['num_posts'];
	}
	$num_posts = (object) $num_posts;
}
$post_type_texts[] = sprintf(__ngettext('%1$s post', '%1$s posts', $num_posts->publish, 'ktai_style'), number_format($num_posts->publish));
if ($Ktai_Style->check_wp_version('2.3', '>=') && current_user_can('edit_posts') && $num_posts->draft) {
	$post_type_texts[] = sprintf(__ngettext('<a href="%2$s">%1$s draft</a>', '<a href="%2$s">%1$s drafts</a>', $num_posts->draft, 'ktai_style'), number_format($num_posts->draft), $KS_Admin->add_sid('edit.php?post_status=draft'));
}

if (function_exists('wp_count_terms')) {
	$post_type_text = implode(__(', ', 'ktai_style'), $post_type_texts);
	$numcats  = wp_count_terms('category');
	$numtags = wp_count_terms('post_tag');
	$cat_str  = sprintf(__ngettext('%1$s category', '%1$s categories', $numcats, 'ktai_style'), number_format_i18n($numcats));
	$tag_str  = sprintf(__ngettext('%1$s tag', '%1$s tags', $numtags, 'ktai_style'), number_format_i18n($numtags));
	echo '<p>' . sprintf(__('You have %1$s, contained within %2$s and %3$s.', 'ktai_style'), $post_type_text, $cat_str, $tag_str) . '</p>';
} else {
	$post_type_text = implode(__(', ', 'ktai_style'), $post_type_texts);
	$numcats  = (int) $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->categories");
	$cat_str = sprintf(__ngettext('%1$s category', '%1$s categories', $numcats, 'ktai_style'), number_format($numcats));
	echo '<p>' .  sprintf(__('You have %1$s, contained within %2$s.', 'ktai_style'), $post_type_text, $cat_str) . '</p>';
}

$comments = $wpdb->get_results("SELECT comment_author, comment_ID, comment_post_ID, comment_type FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT 5");
$numcomments = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'");

$count = 1;
if ( $comments || $numcomments ) :
?><h3><?php _e('Comments'); ?></h3>
<?php if ( $numcomments ) : ?>
<p><a href="<?php echo $KS_Admin->add_sid('moderation.php'); ?>"><?php echo sprintf(__('Comments in moderation (%s)', 'ktai_style'), number_format($numcomments) ); ?></a><img localsrc="8" alt="&raquo;" /></p><?php
endif;
if ( $comments ) {
?><dl><?php
	foreach ($comments as $comment) {
		$edit_link = $KS_Admin->get_edit_comment_link(get_comment_ID());
		$edit_link = $edit_link ? $KS_Admin->add_sid(html_entity_decode($edit_link), false) : '';
?><dt><?php ks_ordered_link($count, 10, $edit_link, get_the_title($comment->comment_post_ID));?></dt><dd><img localsrc="<?php comment_type(68, 112, 112); ?>" alt="[<?php comment_type(__('Comment'), __('Trackback'), __('Pingback')); ?>] " /><?php comment_author(); ?></dd><?php
		$count++;	
	}
?></dl><?php
}
endif; // ( $comments || $numcomments )
if ( $recentposts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'post' AND " . get_private_posts_cap_sql('post') . " AND post_date_gmt < '$today' ORDER BY post_date DESC LIMIT 5") ) :
?><h3><?php _e('Posts'); ?></h3><dl><?php
	foreach ($recentposts as $post) {
		if ($post->post_title == '') {
			$post->post_title = sprintf(__('Post #%d'), $post->ID);
		}
		$edit_link = '';
		if ( current_user_can('edit_post',$post->ID) ) {
			$edit_link = $KS_Admin->add_sid('post.php?action=edit&post=' . intval($post->ID), false);
		}
?><dt><?php ks_ordered_link($count, 10, $edit_link, get_the_title()); ?></dt><?php 
		$count++;
	}
?></dl><?php
endif; // $recentposts
if ( $scheduled = $wpdb->get_results("SELECT ID, post_title, post_date_gmt FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'future' ORDER BY post_date ASC") ) :
?><h3><?php _e('Scheduled Entries:') ?></h3><dl><?php
	foreach ($scheduled as $post) {
		if ($post->post_title == '') {
			$post->post_title = sprintf(__('Post #%d'), $post->ID);
		}
?><dt><?php ks_ordered_link($count, 10, $KS_Admin->add_sid('post.php?action=edit&post=' . intval($post->ID), false), $post->post_title);
		echo sprintf(__(' in %s', 'ktai_style'),human_time_diff(current_time('timestamp', 1), strtotime($post->post_date_gmt. ' GMT')));
?></dt><?php 
		$count++;
	}
?></dl><?php
endif; // $scheduled
//do_action('activity_box_end');
include dirname(__FILE__) . '/admin-footer.php'; ?>