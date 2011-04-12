<?php

if ($options['hatena-anywhere']) {

	function add_hatena_bookmark_anywhere() {
?>
			<script type="text/javascript"></script>
			<script src="<?php bloginfo("wpurl"); ?>/wp-content/plugins/vicuna-adaptors/js/hatena-bookmark-anywhere.js" type="text/javascript" charset="utf-8"></script>
			<p class="section" id="hatena_bookmark_anywhere"></p>
<?php
	}

	add_action("comments_footer", add_hatena_bookmark_anywhere);
}

if ($options['hatena-button']) {

	function add_hatena_bookmark_button() {
?>
	<li class="icon hatena button"><a href="http://b.hatena.ne.jp/entry/<?php the_permalink() ?>"><img src="<?php bloginfo("wpurl"); ?>/wp-content/plugins/vicuna-adaptors/images/mark/icon_hatena_bookmark.gif" width="16" height="12" alt="hatena button" /></a></li>
<?php
	}
	add_action('entry_info', add_hatena_bookmark_button);
	add_action('single_entry_info', add_hatena_bookmark_button);
	add_action('page_entry_info', add_hatena_bookmark_button);
}

if ($options['hatena-count']) {
	function add_hatena_bookmark_count() {
?>
	<li class="icon hatena users"><a href="http://b.hatena.ne.jp/entry/<?php the_permalink() ?>"><img src="http://b.hatena.ne.jp/entry/image/<?php the_permalink() ?>" alt="hatena count" /></a></li>
<?php
	}
	add_action("entry_info", add_hatena_bookmark_count);
	add_action('single_entry_info', add_hatena_bookmark_count);
	add_action('page_entry_info', add_hatena_bookmark_count);
}

if ($options['hatena-star']) {

	function add_hatena_star() {
?>
	<script type="text/javascript" src="http://s.hatena.ne.jp/js/HatenaStar.js"></script>
	<script type="text/javascript">
<?php
		$options = get_option('vicuna_adaptor');
		if (!empty($options['hatena-star-token'])) { ?>
		Hatena.Star.Token = '<?php echo htmlspecialchars($options["hatena-star-token"]); ?>';
<?php		} ?>
		Hatena.Star.SiteConfig = {
		  entryNodes: {
<?php	if (is_page() || is_single()) : ?>
		    'div#main' : {
			uri: 'document.location',
			title: 'h1',
			container: 'h1'
		    }
<?php	else : ?>
		    'div.section': {
		      uri: 'h2 a',
		      title: 'h2',
		      container: 'h2'
		    }
<?php	endif; ?>
		  }
		};
	</script>
	<?php
	}
	add_action("wp_head", add_hatena_star);
}
?>