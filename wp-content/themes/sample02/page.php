<?php get_header(); ?>


<div id="main">

<p id="bread">
<a href="<?php bloginfo('url'); ?>">トップページ</a>
&raquo;
<a href="<?php the_permalink(); ?>"><?php single_post_title(); ?></a>
</p>

<div id="contents">
<h2><?php single_post_title(); ?></h2>

<div id="post01">
<?php if ( have_posts() ) : while ( have_posts() ) :
the_post(); ?>
<?php the_content(); ?>
<?php endwhile; endif; ?>
</div>
</div>

</div>


<?php get_footer(); ?>