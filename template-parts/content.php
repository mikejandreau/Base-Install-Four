<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Base_Install
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<h2 class="screen-reader-text">Article Content</h2>

	<?php // delete commented stuff below to clean up headers ?>
	<!-- <header class="entry-header"> -->
		<?php
			// if ( is_single() ) :
			// the_title( '<h1 class="entry-title">', '</h1>' );
			// else :
			// the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			// endif;

			// if ( 'post' === get_post_type() ) : 
		?>
			<!-- <div class="entry-meta"> -->
		<?php 
			// baseinstall_posted_on(); 
		?>
			<!-- </div> -->
		<?php
			// endif; 
		?>
	<!-- </header> -->




	<?php if ( !is_single() ) : ?>
		<header class="entry-header">
			<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
			<?php if ( 'post' === get_post_type() ) : ?>
				<div class="entry-meta">
					<?php baseinstall_posted_on(); ?>
				</div><!-- .entry-meta -->
			<?php endif; ?>
		</header><!-- .entry-header -->
	<?php endif; ?>

	<div class="entry-content">

        <?php
        if ( has_post_thumbnail() ) { ?>
            <div class="featured-image-container">
                <?php the_post_thumbnail(); ?>
            </div>
        <?php } ?>

		<?php
			the_content( sprintf(
				/* translators: %s: Name of current post. */
				wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'baseinstall' ), array( 'span' => array( 'class' => array() ) ) ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			) );

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'baseinstall' ),
				'after'  => '</div>',
			) );
		?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php baseinstall_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
