<?php
/**
 * add menu of vicuna options
 */
function add_vicuna_adaptor_menu() {
        if ( ! current_user_can('edit_themes') )
		return;
	add_submenu_page('plugins.php', __('Vicuna Adaptor', 'vicuna_adaptor'), __('Vicuna Adaptor', 'vicuna_adaptor'), 0, basename(__FILE__), 'vicuna_adaptor_menu');
}
add_action('admin_menu', 'add_vicuna_adaptor_menu');

/**
 * Display menu of layout setting.
 */
function vicuna_adaptor_menu() {
	if ( ! current_user_can('edit_themes') )
		return;

	$options = get_option('vicuna_adaptor');
	if (isset($_POST['hatena-star-token'])) {
		$options['hatena-star-token'] = $_POST['hatena-star-token'];
		update_option('vicuna_adaptor', $options);
	}
	if (isset($_GET['action']) && isset($_GET['extension'])) {
		$options[$_GET['extension']] = ($_GET['action'] == 'active');
		update_option('vicuna_adaptor', $options);
	}
	if ($_GET['action'] == 'deactivate-all') {
		$tmp = array();
		if (isset($options['hatena-star-token'])) {
			$tmp['hatena-star-token'] = $options['hatena-star-token'];
		}
		$options = $tmp;
		update_option('vicuna_adaptor', $options);
	}
?>
<div class="wrap">
	<h2><?php _e('Vicuna Adaptor Management', 'vicuna_adaptor'); ?></h2>
	<p><?php _e('Vicuna Adaptor is to extend and expand the functionality of wp.Vicuna. You may activate it or deactivate it here.', 'vicuna_adaptor'); ?></p>

<div class="tablenav">
	<div class="alignleft">
		<a class="button-secondary" href="plugins.php?page=admin_ui.php&action=deactivate-all" class="delete">Deactivate All Extension</a>
	</div>
	<br class="clear" />
</div>

<br class="clear" />

<table class="widefat">
	<thead>
	<tr>
		<th>Extension</th>
		<th>Description</th>
		<th class="status">Status</th>
		<th class="action-links">Action</th>
	</tr>
	</thead>
	<tbody id="plugins">
<?php	/* はてなスター */
	$status = $options['hatena-star']; ?>
	<tr<?php if ($status) : echo ' class="active"'; endif; ?>>
		<td class='name'><a href="http://s.hatena.ne.jp/">はてなスター</a></td>
		<td class='desc'><p>はてなスターは、ブログにワンクリックで☆が付けられるサービスです。あなたのブログをもっと楽しくします。</p></td>
		<td class='status'><span class="<?php if (!$status) : echo 'in'; endif; ?>active"><?php if ($status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive</span></td>
		<td class='togl action-links'><a href='plugins.php?page=admin_ui.php&action=<?php if ($status) : echo 'in'; endif; ?>active&#038;extension=hatena-star' title='<?php if (!$status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive this plugin' class='edit'><?php if (!$status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive</a></td>
	</tr>
	<tr<?php if ($status) : echo ' class="active"'; endif; ?>><td colspan="4" style="text-align: center"><form method="post" action="plugins.php?page=admin_ui.php">Hatena.Star.Token = <input type="text" name="hatena-star-token" value="<?php echo htmlspecialchars($options["hatena-star-token"]); ?>" size="45" /> <input type="submit" value="<?php _e('submit', 'vicuna_adaptor'); ?>" /></form></td>

<?php	/* ブックマーク数の表示 */
	$status = $options['hatena-count']; ?>
	<tr<?php if ($status) : echo ' class="active"'; endif; ?>>
		<td class='name'><a href="http://b.hatena.ne.jp/help/count"><img src="<?php bloginfo("wpurl"); ?>/wp-content/plugins/vicuna-adaptors/images/mark/icon_00011.png" width="58" height="15" />の表示</a></td>
		<td class='desc'><p>ブログのエントリーがブックマークされた数を表示させることができます。あなたの書いたエントリーについてのコメントを一覧したり、ブックマークしてもらうためのナビゲーションとしてお使いください。</p></td>
		<td class='status'><span class="<?php if (!$status) : echo 'in'; endif; ?>active"><?php if ($status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive</span></td>
		<td class='togl action-links'><a href='plugins.php?page=admin_ui.php&action=<?php if ($status) : echo 'in'; endif; ?>active&#038;extension=hatena-count' title='<?php if (!$status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive this plugin' class='edit'><?php if (!$status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive</a></td>
	</tr>

<?php	/* 「このエントリーを含むブックマーク」ボタンの表示 */
	$status = $options['hatena-button']; ?>
	<tr<?php if ($status) : echo ' class="active"'; endif; ?>>
		<td class='name'><a href="http://b.hatena.ne.jp/help/button"><img src="<?php bloginfo("wpurl"); ?>/wp-content/plugins/vicuna-adaptors/images/mark/icon_hatena_bookmark.gif" width="16" height="12" />の表示</a></td>
		<td class='desc'><p>ブログのエントリーに<img src="<?php bloginfo("wpurl"); ?>/wp-content/plugins/vicuna-adaptors/images/mark/icon_hatena_bookmark.gif" width="16" height="12" />ボタンを表示させることができます。このリンクを辿ることで、自分の書いたエントリーに対するブックマークについたコメントを一覧したり、さらにブックマークしてもらうためのナビゲーションを行うことができます。</p></td>
		<td class='status'><span class="<?php if (!$status) : echo 'in'; endif; ?>active"><?php if ($status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive</span></td>
		<td class='togl action-links'><a href='plugins.php?page=admin_ui.php&action=<?php if ($status) : echo 'in'; endif; ?>active&#038;extension=hatena-button' title='<?php if (!$status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive this plugin' class='edit'><?php if (!$status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive</a></td>
	</tr>

<?php	/* hatena-bookmark-anywhere.js */
	$status = $options['hatena-anywhere']; ?>
	<tr<?php if ($status) : echo ' class="active"'; endif; ?>>
		<td class='name'><a href="http://blog.masuidrive.jp/index.php/2008/04/18/how-to-setup-hatana_bookmark_anywhere-js/">hatena-bookmark-anywhere.js</a></td>
		<td class='desc'><p>はてなブックマークのコメントを通常のコメントと同じように表示させることができます。</p></td>
		<td class='status'><span class="<?php if (!$status) : echo 'in'; endif; ?>active"><?php if ($status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive</span></td>
		<td class='togl action-links'><a href='plugins.php?page=admin_ui.php&action=<?php if ($status) : echo 'in'; endif; ?>active&#038;extension=hatena-anywhere' title='<?php if (!$status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive this plugin' class='edit'><?php if (!$status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive</a></td>
	</tr>

<?php	/* del.icio.us ボタン */
	$status = $options['delicious']; ?>
	<tr<?php if ($status) : echo ' class="active"'; endif; ?>>
		<td class='name'><a href="http://del.icio.us/help/savebuttons"><img src="<?php bloginfo("wpurl"); ?>/wp-content/plugins/vicuna-adaptors/images/mark/icon_delicious.gif" title="save to del.icio.us" alt="save to del.icio.us" /> buttons</a></td>
		<td class='desc'><p>With a <img src="<?php bloginfo("wpurl"); ?>/wp-content/plugins/vicuna-adaptors/images/mark/icon_delicious.gif" alt="Save This Page" title="Save This Page" /> link, you can provide your site visitors an easy way to save it to del.icio.us.</p></td>
		<td class='status'><span class="<?php if (!$status) : echo 'in'; endif; ?>active"><?php if ($status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive</span></td>
		<td class='togl action-links'><a href='plugins.php?page=admin_ui.php&action=<?php if ($status) : echo 'in'; endif; ?>active&#038;extension=delicious' title='<?php if (!$status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive this plugin' class='edit'><?php if (!$status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive</a></td>
	</tr>

<?php	/* Fontsize Switcher */
	$status = $options['fontsize-switcher']; ?>
	<tr<?php if ($status) : echo ' class="active"'; endif; ?>>
		<td class='name'><a href="http://10coin.com/products/fontsize-switcher">Fontsize Switcher</a></td>
		<td class='desc'><p>ページのフォントサイズをユーザが任意で切り替えができるようになります。変更したフォントサイズ情報はクッキーに保存されるため、再度閲覧した際には、既に変更したフォントサイズになります。</p></td>
		<td class='status'><span class="<?php if (!$status) : echo 'in'; endif; ?>active"><?php if ($status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive</span></td>
		<td class='togl action-links'><a href='plugins.php?page=admin_ui.php&action=<?php if ($status) : echo 'in'; endif; ?>active&#038;extension=fontsize-switcher' title='<?php if (!$status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive this plugin' class='edit'><?php if (!$status) : echo 'A'; else : echo 'Ina'; endif; ?>ctive</a></td>
	</tr>

	</tbody>
</table>

</div>
<?php
}
?>
