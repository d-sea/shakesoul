<?php
/*
Template Name: PAGE TEMPLATE 02
*/
?>


<?php get_header(); ?>


<div id="main">

<p id="bread">
<a href="<?php bloginfo('url'); ?>">トップページ</a>
&raquo;
<a href="<?php the_permalink(); ?>"><?php single_post_title(); ?></a>
</p>

<div id="contents">
<h2><?php single_post_title(); ?></h2>

<div id="post02">
<?php if ( have_posts() ) : while ( have_posts() ) :
the_post(); ?>
<?php the_content(); ?>
<?php endwhile; endif; ?>

<?php if(is_page('旅のリンク集')): ?>
<table id="link">
<?php get_links('-1', '<tr><th>', '</td></tr>', '</th><td>'); ?>
</table>
<?php endif; ?>
</div>

<div id="sidebar">
<p><img src="<?php bloginfo('url'); ?>/wp-content/uploads/<?php echo get_post_meta($post->ID, 'サイドバー', TRUE); ?>" /></p>
</div>

<p class="clear">&nbsp;</p>
</div>

</div>


<?php get_footer(); ?>