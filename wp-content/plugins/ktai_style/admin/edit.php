<?php
/* ==================================================
 *   Ktai Admin Edit Post
 *   based on wp-admin/edit.php of WP 2.3
   ================================================== */

require dirname(__FILE__) . '/admin.php';
$Page = new KS_Admin_Edit_Posts($KS_Admin);
$title = __('Manage Posts', 'ktai_style');
$parent_file = 'edit.php';
$year = intval(@$_GET['year']);
$monthnum = intval(@$_GET['monthnum']);
$cat = intval(@$_GET['cat']);
$_GET['s'] = stripslashes(@$_GET['s']);
$s = urlencode($_GET['s']);

global $wpdb;
if (function_exists('get_available_post_statuses')) {
	$avail_post_stati = get_available_post_statuses('post');
} else {
	$query = "SELECT post_status FROM {$wpdb->posts} WHERE post_type = 'post' GROUP BY post_status";
	 $avail_post_stati = $wpdb->get_col($query);
}
$post_status_label = __('Posts');
$post_status_q = '&post_status=0';
if (isset($_GET['post_status']) && $_GET['post_status'] != 'any' && in_array( $_GET['post_status'], $avail_post_stati)) {
	$post_status_q = '&post_status=' . $_GET['post_status'];
//	$post_status_q .= '&perm=readable';
}
if ( 'pending' === $_GET['post_status'] ) {
	$post_status_label = __('Pending');
	$order = 'ASC';
	$orderby = 'modified';
} elseif ( 'draft' === $_GET['post_status'] ) {
	$post_status_label = __('Draft');
	$order = 'DESC';
	$orderby = 'modified';
} else {
	$order = 'DESC';
	$orderby = 'date';
}

$per = intval(($Ktai_Style->get('page_size') - 2250) / 345);
if ($per < 1) {
	$per = 5;
} elseif ($per > 15) {
	$per = 15;
}
$page_num = isset($_GET['paged']) ? abs((int) $_GET['paged']) : 1;

query_posts("post_type=post&what_to_show=posts$post_status_q&posts_per_page=$per&order=$order&orderby=$orderby&year=$year&monthnum=$monthnum&cat=$cat&s=$s&paged=$page_num");

include dirname(__FILE__) . '/admin-header.php';
if ($Ktai_Style->check_wp_version('2.3', '<')) {
	$KS_Admin->show_drafts(get_users_drafts($user_ID), __('Your Drafts:'));
	$KS_Admin->show_drafts(get_others_drafts($user_ID), __('Other&#8217;s Drafts:'));
} ?>
<h2><?php echo $Page->page_title($post_status_label); ?></h2>
<form name="searchform" action="<?php echo basename(__FILE__); ?>" method="get">
<?php $KS_Admin->sid_field(); ?>
<div><?php _e('Search'); ?><input type="text" name="ks" value="<?php echo wp_specialchars($_GET['s']); ?>" size="17" /><br />
<?php
if ($Ktai_Style->check_wp_version('2.3', '>=')) {
	echo $Page->status_menu($avail_post_stati) . '<br />';
}
$author_menu = $Page->author_menu();
if ($author_menu) {
	echo $author_menu . '<br />';
}
$month_menu = $Page->month_menu();
if ($month_menu) {
	echo $month_menu . '<br />';
}
?>
<input type="submit" name="Submit" value="<?php _e('Filter', 'ktai_style'); ?>" /></div></form>
<?php
$Page->edit_post_rows();

$start = $offset = ( $page_num - 1 ) * $per;
$page_links = paginate_links( array(
	'base' => add_query_arg('paged', '%#%'), 
	'format' => '',
	'total' => $wp_query->max_num_pages,
	'current' => $page_num,
	'prev_text' => '<img localsrc="7" alt="&laquo;" />' . __('Previous Page', 'ktai_style'),
	'next_text' => __('Next Page', 'ktai_style') . '<img localsrc="8" alt="&raquo;" />'
));
if ($page_links) {
	$page_links = $Ktai_Style->filter_tags($page_links);
	$page_links = str_replace("\n", ' ', $page_links);
	$page_links = str_replace(ks_admin_url(false), '', $page_links);
	echo '<p>' . $page_links . '</p>';
}
include dirname(__FILE__) . '/admin-footer.php';
exit();

/* ==================================================
 *   KS_Admin_Edit_Posts class
   ================================================== */

class KS_Admin_Edit_Posts {
	private $parent;

// ==================================================
public function __construct($admin) {
	$this->parent = $admin;
}

// ==================================================
public function page_title($post_status_label) {
	global $user_ID, $post_listing_pageable, $wp_locale;
	if ($post_listing_pageable && ! is_archive() && ! is_search()) {
		$h2_noun = is_paged() ? sprintf(__( 'Previous %s' ), $post_status_label) : sprintf(__('Latest %s'), $post_status_label);
	} else {
		$h2_noun = $post_status_label;
	}
	// Use $_GET instead of is_ since they can override each other
	$h2_author = '';
	$_GET['author'] = intval($_GET['author']);
	if ( $_GET['author'] != 0 ) {
		if ( $_GET['author'] == '-' . $user_ID ) { // author exclusion
			$h2_author = ' ' . __('by other authors');
		} else {
			$author_user = get_userdata( get_query_var( 'author' ) );
			$h2_author = ' ' . sprintf(__('by %s', 'ktai_style'), wp_specialchars( $author_user->display_name ));
		}
	}
	$h2_search = isset($_GET['s']) && $_GET['s'] ? ' ' . sprintf(__('matching &#8220;%s&#8221;', 'ktai_style'), wp_specialchars($_GET['s'])) : '';
	$h2_cat = isset($_GET['cat']) && $_GET['cat'] ? ' ' . sprintf(__('in &#8220;%s&#8221;', 'ktai_style'), single_cat_title('', false)) : '';
	$h2_m = isset($_GET['monthnum']) ? $wp_locale->get_month($_GET['monthnum']) : '';
	$h2_y = isset($_GET['year']) ? $_GET['year'] : '';
	$h2_month = "$h2_m$h2_y" ? sprintf(__('during %1$s, %2$d', 'ktai_style'), $h2_m, $h2_y) : '';
	return sprintf(_c('%1$s%2$s%3$s%4$s%5$s|You can reorder these: 1: Posts, 2: by {s}, 3: matching {s}, 4: in {s}, 5: during {s}'), $h2_noun, $h2_author, $h2_search, $h2_cat, $h2_month);
}

// ==================================================
public function author_menu() {
	global $user_ID, $Ktai_Style;
	$editable_ids = get_editable_user_ids($user_ID);
	$html = '';
	if ( $editable_ids && count($editable_ids) >= 1 ) {
		if (function_exists('wp_dropdown_users')) {
			$html = __('Author');
			$html .= $Ktai_Style->filter_tags(wp_dropdown_users(array('include' => $editable_ids, 'show_option_all' => __('Any', 'ktai_style'), 'name' => 'author', 'selected' => isset($_GET['author']) ? $_GET['author'] : 0, 'echo' => 0)));
		} else {
			$html = __('Post Author');
			$html .= '<select name="author"><option value="0">' . __('Any', 'ktai_style') . '</option>';
			foreach ($editable_ids as $e) {
				$a = get_userdata($e);
				if (isset($_GET['author']) && $_GET['author'] == $a->ID) { 
					$selected = ' selected="selected"';
				} else {
					$selected = '';
				}
				$html .= '<option value="' . intval($e) . '"' . $selected . '>' . $a->display_name . '</option>';
			}
			$html .= '</select>';
		}
	}
	return $html;
}

// ==================================================
public function month_menu() {
	global $wpdb, $wp_locale;
	$monthnum = intval(@$_GET['monthnum']);
	$year = intval(@$_GET['year']);
	$html = sprintf(__('<label>Monthnum: <input type="text" name="monthnum" size="2" istyle="4" mode="numeric" value="%1$s" /></label>, <label>Year: <input type="text" name="year" size="4" istyle="4" mode="numeric" value="%2$s" /></label>', 'ktai_style'), ($monthnum ? $monthnum : ''), ($year ? $year : ''));
	return $html;
}

// ==================================================
public function status_menu($statuses) {
	global $Ktai_Style;
	$html =  $Ktai_Style->check_wp_version('2.5', '>=') ? __('Publish Status') :  __('Post Status');
	$html .= '<select name="post_status">';
	array_unshift($statuses, 'any');
	$status = isset($_GET['post_status']) ? $_GET['post_status'] : '';
	foreach ($statuses as $s) {
		$selected = ($status == $s) ? ' selected="selected"' : '';
		$html .= '<option value="' . $s . $selected . '">' . __(ucfirst($s), 'ktai_style') . '</option>';
	}
	$html .= '</select>';
	return $html;
}

// ==================================================
public function edit_post_rows() {
	global $post, $id;
?><dl><?php
	if ( have_posts() ) : while (have_posts()) : the_post();
		$title = get_the_title();
		if (empty($title)) {
			$title = __('(no title)');
		}
		if ( current_user_can('edit_post',$post->ID) ) {
			$title = '<img localsrc="149" alt="" /><a href="' . $this->parent->add_sid('post.php?action=edit&post=' . intval($id)) . '">' . $title . '</a>';
		}
		echo "<dt>$id:$title</dt>" . '<dd><img localsrc="46" alt="" /><font color="' . ks_option('ks_date_color') . '">';
		if ('draft' === $_GET['post_status'] || 'pending' === $_GET['post_status']) {
			if ('0000-00-00 00:00:00' == $post->post_modified) {
				_e('Never');
			} else {
				ks_mod_time();
			}
		} else {
			if ('0000-00-00 00:00:00' == $post->post_date) {
				_e('Unpublished');
			} else {
				ks_time();
			}
		}
		echo '</font><img localsrc="68" alt=" by " />' . get_the_author() . '</dd>';
		$categories = get_the_category();
		if (count($categories)) {
			$cat_links = array();
			foreach ($categories as $c) {
				$cat_links[] = '<a href="' . basename(__FILE__) . $this->parent->add_sid('?cat=' . $c->cat_ID) . '">' . wp_specialchars($c->cat_name) . '</a>';
			}
			echo '<dd><img localsrc="354" alt="' . __('Category:') . '" /><font size="-1">' . implode(', ', $cat_links) . '</font></dd>';
		}
	endwhile;
	else :
?>
<dd><?php _e('No posts found.') ?></dd>
<?php
	endif; // have_posts()
?></dl><?php
}

// ===== End of class ====================
}
?>