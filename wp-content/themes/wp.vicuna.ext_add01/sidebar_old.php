<div id="utilities">
	<dl class="navi">
<?php	if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('navi') ) : ?>
<?php		if ($pages = &get_pages('')) : ?>

		<dt><?php _e('Pages', 'vicuna'); ?></dt>
		<dd>
			<ul class="pages">
<?php			wp_list_pages('sort_column=menu_order&title_li=0'); ?>
			</ul>
		</dd>
<?php		endif; ?>

		<dt><?php _e('Recent Entries', 'vicuna'); ?></dt>
		<dd>
			<ul class="recentEntries">
<?php		wp_get_archives('type=postbypost&limit=5'); ?>
			</ul>
		</dd>



		<dt><?php _e('Categories', 'vicuna'); ?></dt>
		<dd>
			<ul class="category">
<?php		wp_list_cats('sort_column=name&optioncount=0&hierarchical=1'); ?>
			</ul>
		</dd>

		<dt><?php _e('Archives', 'vicuna'); ?></dt>
		<dd>
			<ul class="archive">
<?php		vicuna_archives_link(); ?>
			</ul>
		</dd>
<?php		if (function_exists('get_tags')) : ?>
		<dt><?php _e('Tag Cloud', 'vicuna'); ?></dt>
		<dd>
<?php			vicuna_tag_cloud(); ?>
		</dd>
<?php		endif; ?>
<?php	endif; ?>
	</dl><!--end navi-->




	<dl class="others">
<?php	if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('others') ) : ?>

		<dt><?php _e('Search', 'vicuna'); ?></dt>
		<dd>
			<form method="get" action="<?php bloginfo('home'); ?>/">
				<fieldset>
					<legend><label for="searchKeyword"><?php printf(__('Search %s', 'vicuna'), get_bloginfo('name')); ?></label></legend>
					<div>
						<input type="text" class="inputField" id="searchKeyword" name="s" size="10" onfocus="if (this.value == '<?php _e('Keyword(s)', 'vicuna'); ?>') this.value = '';" onblur="if (this.value == '') this.value = '<?php _e('Keyword(s)', 'vicuna'); ?>';" value="<?php if ( is_search() ) echo wp_specialchars($s, 1); else _e('Keyword(s)', 'vicuna'); ?>" />
						<input type="submit" class="submit" id="submit" value="<?php _e('Search', 'vicuna'); ?>" />
					</div>
				</fieldset>
			</form>
		</dd>

		<dt><?php _e('最近の投稿', 'vicuna'); ?></dt>
		<dd>
			<ul class="recentEntries">
<?php
$posts = get_posts('numberposts=7');
foreach($posts as $post) {
setup_postdata($post);
?>
		<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>   (<?php the_date('y/m/d'); ?>)</li>
<?php } ?>
			</ul>
		</li>
		</dd>

<?php	endif; ?>


		<dt><?php _e('Feeds', 'vicuna'); ?></dt>
		<dd>
			<ul class="feeds">
				<li class="rss"><a href="<?php bloginfo('rss2_url'); ?>"><?php _e('RSS 2.0', 'vicuna'); ?></a></li>
			</ul>
		</dd>
		<dd>
			<ul class="links">
				<li class="link"><a href="http://www.fhy-works.com"></a></li>
			</ul>
		</dd>
	</dl><!--end others-->
</div><!--end utilities-->
