<?php
/* ==================================================
 *   Ktai Admin Process Posts
 *   based on wp-admin/post.php of WP 2.3
   ================================================== */

require dirname(__FILE__) . '/admin.php';
$parent_file = 'edit.php';
$Page = new KS_Admin_Posts($KS_Admin);

/* ==================================================
 *   KS_Admin_Posts class
   ================================================== */

class KS_Admin_Posts {
	private $admin;

public function __construct($admin) {
	$this->admin = $admin;

	global $action, $posts;
	wp_reset_vars(array('action', 'posts'));

	if (isset($_POST['selcats'])) {
		$action = 'selcats';
	}
	switch ($action) {
	case 'post':
		$this->post($action);
		exit();
	case 'edit':
		$this->edit();
		break;
	case 'editpost':
		$this->editpost();
		exit();
	case 'delete':
		$this->delete();
		exit();
	case 'selcats':
		$this->select_cats();
		break;
	case 'changecats':
		$this->change_cats();
		break;
	default:
		$this->admin->redirect('edit.php');
		exit();
	}
}

// ==================================================
private function post($action) {
	global $parent_file, $submenu_file;
	$parent_file = 'post-new.php';
	$submenu_file = 'post-new.php';
	check_admin_referer('add-post');
	$post_ID = $this->write_post();
	// Redirect.
	if (! empty($_POST['mode'])) {
		$location = 'post-new.php';
	} else {
		$location = "post-new.php?posted=$post_ID";
	}
	if (isset($_POST['save'])) {
		$location = "post.php?action=edit&post=$post_ID";
	}
	if (empty($post_ID)) {
		$location = 'post-new.php';
	}
	$this->admin->redirect($location);
}

// ==================================================
private function edit() {
	global $post_ID;
	$title = __('Edit');
	$action = 'edit';
	$post_ID = (int) $_GET['post'];
	$post = $this->get_post($post_ID);
	include dirname(__FILE__) . '/admin-header.php';
	include dirname(__FILE__) . '/edit-form.php';
	$referer = $this->admin->get_referer();
	if ($referer) {
		echo '<div><img localsrc="64" alt="' . __('&lt;-', 'ktai_style') . '" />' . sprintf(__('Back to <a href="%s">the previous page</a>.', 'ktai_style'), clean_url($referer)) . '</div>';
	include dirname(__FILE__) . '/admin-footer.php'; 
	}
}

// ==================================================
private function editpost() {
	$post_ID = (int) $_POST['post_ID'];
	check_admin_referer('update-post_' . $post_ID);
	$post_ID = $this->edit_post($post_ID);
	if ( 'post' == $_POST['originalaction'] ) {
		if (! empty($_POST['mode'])) {
			$location = 'post-new.php';
		} else {
			$location = "post-new.php?posted=$post_ID";
		}
		if ( isset($_POST['save']) )
			$location = "post.php?action=edit&post=$post_ID";
	} else {
		$referredby = '';
		if (!empty($_POST['referredby'])) {
			$referredby = preg_replace('|https?://[^/]+|i', '', $_POST['referredby']);
		}
		$referer = preg_replace('|https?://[^/]+|i', '', $this->admin->get_referer());
		if ($_POST['save']) {
			$location = "post.php?action=edit&post=$post_ID";
		} elseif (!empty($referredby) && $referredby != $referer) {
			$location = $_POST['referredby'];
			if ( $_POST['referredby'] == 'redo' )
				$location = get_permalink( $post_ID );
		} else {
			$location = 'post-new.php';
		}
	}
	$this->admin->redirect($location); // Send user on their way while we keep working
}

// ==================================================
private function delete() {
	$post_id = (isset($_GET['post'])) ? intval($_GET['post']) : intval($_POST['post_ID']);
	check_admin_referer('delete-post_' . $post_id);
	$post = & get_post($post_id);
	if (! current_user_can('delete_post', $post_id)) {
		Ktai_Style::ks_die(__('You are not allowed to delete this post.'));
	}
	if ( $post->post_type == 'attachment' ) {
		if (! wp_delete_attachment($post_id)) {
			Ktai_Style::ks_die(__('Error in deleting...'));
		}
	} else {
		if (! wp_delete_post($post_id)) {
			Ktai_Style::ks_die(__('Error in deleting...'));
		}
	}
	$sendback = $this->admin->get_referer();
	if (strpos($sendback, 'post.php') !== false) {
		$sendback = ks_admin_url(false) . 'post-new.php';
	}
	$sendback = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $sendback);
	$this->admin->redirect($sendback);
}

// ==================================================
private function select_cats() {
	global $post_ID, $parent_file;
	if ($_POST['originalaction'] == 'editpost') {
		$post_ID = (int) $_POST['post_ID'];
	 	if ($post_ID < 1) {
			Ktai_Style::ks_die(__("You attempted to edit a post that doesn't exist. Perhaps it was deleted?"));
		}
		check_admin_referer('update-post_' . $post_ID);
	} else {
		$parent_file = 'post-new.php';
		$post_ID = 0;
		check_admin_referer('add-post');
	}
	foreach (array('post_ID', 'post_cats', 'originalaction', 'referredby') as $k) {
		if (isset($_POST[$k])) {
			$this->admin->set_data($k, stripslashes($_POST[$k]));
		}
	}
	$charset = ks_detect_encoding();
	$this->admin->set_data('post_title', ks_mb_get_form('post_title', $charset));
	$this->admin->set_data('post_name', ks_mb_get_form('post_name', $charset));
	$this->admin->set_data('post_content', ks_mb_get_form('content', $charset));
	$this->admin->set_data('tags_input', ks_mb_get_form('tags_input', $charset));
	$title = __('Select Category', 'ktai_style');
	include dirname(__FILE__) . '/admin-header.php';?>
<form action="post.php" method="post">
<input type="hidden" name="action" value="changecats" />
<?php $this->admin->sid_field(); wp_nonce_field('change-cats_' . $post_ID);
$this->category_checklist(array_map('intval', explode(',', $_POST['post_cats']))); ?>
<input type="submit" name="cancel" value="<?php _e('Cancel', 'ktai_style'); ?>" />
<input type="submit" value="<?php _e('Set Category', 'ktai_style'); ?>" />
</form>
<?php
	include dirname(__FILE__) . '/admin-footer.php'; 
}

// ==================================================
private function change_cats() {
	global $post_ID, $parent_file;
	if ($this->admin->get_data('originalaction') == 'editpost') {
		$title = __('Edit');
		$action = 'edit';
		$post_ID = intval($this->admin->get_data('post_ID'));
		$post = $this->get_post($post_ID);
	} else {
		$parent_file = 'post-new.php';
		$title = __('Create New Post');
		$post_ID = 0;
		$post = get_default_post_to_edit();
	}
	check_admin_referer('change-cats_' . $post_ID);
	$post->post_title = $this->admin->get_data('post_title');
	$post->post_name = $this->admin->get_data('post_name');
	$post->post_content = $this->admin->get_data('post_content');
	$post->tags_input = $this->admin->get_data('tags_input');
	$post_referredby = $this->admin->get_data('referredby');
	if (! isset($_POST['cancel'])) {
		$checked_cats = array();
		if (is_array($_POST['cat']) && count($_POST['cat']) >= 1) {
			foreach ($_POST['cat'] as $c) {
				$checked_cats[] = intval($c);
			}
		} else {
			$checked_cats[] = get_option('default_category');
		}
	} else {
		$checked_cats = array_map('intval', explode(',', $this->admin->get_data('post_cats')));
	}
	include dirname(__FILE__) . '/admin-header.php';
	include dirname(__FILE__) . '/edit-form.php';
	if ($this->admin->get_data('originalaction') == 'editpost') {
		$referer = $post_referredby;
		if ($referer && $post_referredby != 'redo') {
			echo '<div><img localsrc="64" alt="' . __('&lt;-', 'ktai_style') . '" />' . sprintf(__('Back to <a href="%s">the previous page</a>.', 'ktai_style'), clean_url($referer)) . '</div>';
		}
	}
	include dirname(__FILE__) . '/admin-footer.php'; 
}

// ==================================================
private function get_post($post_ID) {
	$post = get_post($post_ID);
	if (empty($post->ID)) {
		Ktai_Style::ks_die(__("You attempted to edit a post that doesn't exist. Perhaps it was deleted?"));
	}
	if ('page' == $post->post_type) {
		Ktai_Style::ks_die(__('Ktai Style is not available for page editing.', 'ktai_style'));
	}
	$post = get_post_to_edit($post_ID);
	if (! current_user_can('edit_post', $post_ID) ) {
		Ktai_Style::ks_die(__('You are not allowed to edit this post.'));
	}
	return $post;
}

/* ==================================================
 * @param	none
 * @return	int     $post_ID
 * based on write_post() at wp-admin/includes/post.php of WP 2.3
 */
private function write_post() {
	global $current_user;
	if (! current_user_can('edit_posts')) {
		Ktai_Style::ks_die(__('You are not allowed to create posts or drafts on this blog.'));
	}
	$charset = ks_detect_encoding();
	$_POST['post_title']   = trim(strip_tags(ks_mb_get_form('post_title', $charset)));
	$_POST['post_content'] = trim(ks_mb_get_form('content', $charset));
	if (isset($_POST['tags_input'])) {
		$_POST['tags_input'] = trim(strip_tags(ks_mb_get_form('tags_input', $charset)));
	}
	$_POST['post_excerpt']  = '';
//	$_POST['post_date']     = current_time('mysql');
//	$_POST['post_date_gmt'] = get_gmt_from_date($_POST['post_date']);
	$_POST['post_name']     = isset($_POST['post_name']) ? $_POST['post_name'] : date('His', strtotime($_POST['post_date']));
	$_POST['post_author']   = intval($_POST['user_ID']);

	if (isset($_POST['post_cats'])) {
		$_POST['post_category'] = array_map('intval', explode(',', $_POST['post_cats']));
	}

	// What to do based on which button they pressed
	if (isset($_POST['publish']) && '' != $_POST['publish'] && $_POST['post_status'] != 'private') {
		$_POST['post_status'] = 'publish';
	}
/*
	if (isset($_POST['advanced']) && '' != $_POST['advanced']) {
		$_POST['post_status'] = 'draft';
	}
 */
 	if ('publish' == $_POST['post_status'] && ! current_user_can('publish_posts')) {
		$_POST['post_status'] = 'pending';
	}
	if (! isset( $_POST['comment_status'])) {
		$_POST['comment_status'] = 'closed';
	}
	if (! isset( $_POST['ping_status'])) {
		$_POST['ping_status'] = 'closed';
	}

	$post_ID = wp_insert_post($_POST);
	if (is_wp_error($post_ID)) {
		Ktai_Style::ks_die($post_ID->get_error_message());
	} else {
		return $post_ID;
	}
}

/* ==================================================
 * @param	none
 * @return	int     $post_ID
 * based on edit_post() at wp-admin/includes/post.php of WP 2.3
 */
private function edit_post($post_ID) {
	global $current_user;
	if (! $post_ID) {
		Ktai_Style::ks_die(__("You attempted to edit a post that doesn't exist. Perhaps it was deleted?"));
	} elseif (! current_user_can('edit_post', $post_ID)) {
		Ktai_Style::ks_die(__('You are not allowed to edit this post.'));
	}
	$post = wp_get_single_post($post_ID, ARRAY_A);
	$charset = ks_detect_encoding();
	$_POST['ID']           = intval($post_ID);
	$_POST['post_title']   = trim(strip_tags(ks_mb_get_form('post_title', $charset)));
	$_POST['post_name']    = trim(strip_tags(ks_mb_get_form('post_name', $charset)));
	$_POST['post_content'] = trim(ks_mb_get_form('content', $charset));
	if (isset($_POST['tags_input'])) {
		$_POST['tags_input'] = trim(ks_mb_get_form('tags_input', $charset));
	}
	if ($post['post_author'] != $current_user->ID && ! current_user_can('edit_others_posts')) {
		Ktai_Style::ks_die(__('You are not allowed to edit posts as this user.'));
	}
	if (isset($_POST['post_cats'])) {
		$_POST['post_category'] = array_map('intval', explode(',', $_POST['post_cats']));
	}

	// What to do based on which button they pressed
	if (isset($_POST['publish']) && '' != $_POST['publish']) {
		$_POST['post_status'] = 'publish';
	}
/*
	if (! current_user_can('edit_published_posts')) {
		$_POST['post_status'] = 'pending';
	}
 */
	if ('publish' == $_POST['post_status'] && ! current_user_can('publish_posts')) {
		$_POST['post_status'] = 'pending';
	}
	if (! isset( $_POST['comment_status'])) {
		$_POST['comment_status'] = 'closed';
	}
	if (! isset( $_POST['ping_status'])) {
		$_POST['ping_status'] = 'closed';
	}

	wp_update_post( $_POST );
	return intval($post_ID);
}

/* ==================================================
 * based on wp_category_checklist() at wp-admin/includes/template.php of WP 2.5.1
 */
public function category_checklist($selected_cats = false, $descendants_and_self = 0) {
	$walker = new KS_Category_Checklist();
	$descendants_and_self = (int) $descendants_and_self;
	$args = array('orderby' => 'name','order' => 'ASC', 'show_count' => 0, 'hierarchical' => false);

	if (is_array( $selected_cats)) {
		$args['selected_cats'] = $selected_cats;
	}
	$args['popular_cats'] = array();
	if ( $descendants_and_self ) {
		$categories = get_categories( "child_of=$descendants_and_self&hierarchical=0&hide_empty=0" );
		$self = get_category( $descendants_and_self );
		array_unshift( $categories, $self );
	} else {
		$categories = get_categories('get=all');
	}

	$args = array($categories, -1, $args);
	$output = call_user_func_array(array(&$walker, 'walk'), $args);

	echo $output;
}

// ===== End of class ====================
}

?>