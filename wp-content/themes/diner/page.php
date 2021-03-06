<?php get_header(); ?>
<?php global $woo_options, $wootable; ?>
    <div id="content" class="page col-full">
		<div id="main" class="<?php if( is_page( $wootable->frontend->bookings_page ) || is_page( $wootable->frontend->manage_page ) ): echo 'reservation-page '; endif; ?>col-left">
		           
			<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>

            <?php if (have_posts()) : $count = 0; ?>
            <?php while (have_posts()) : the_post(); $count++; ?>
                                                                        
                <div class="post">

                    <h1 class="title">
                    	<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                    	<?php if(is_page(get_option('wootable_page_booking' ))): ?>
                    		<span class="heading-description"><?php echo get_option('woo_page_booking_desc'); ?></span>
                    	<?php endif; ?>
                    </h1>

                    <div class="entry">
	                	<?php the_content(); ?>
	               	</div><!-- /.entry -->

					<?php edit_post_link( __('{ Edit }', 'woothemes'), '<span class="small">', '</span>' ); ?>
                    
                </div><!-- /.post -->
                
                <?php $comm = $woo_options['woo_comments']; if ( ($comm == "page" || $comm == "both") ) : ?>
                    <?php comments_template(); ?>
                <?php endif; ?>
                                                    
			<?php endwhile; else: ?>
				<div class="post">
                	<p><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></p>
                </div><!-- /.post -->
            <?php endif; ?>  
        
		</div><!-- /#main -->
		
		<?php
			if ( ! is_page( $wootable->frontend->bookings_page ) && ! is_page( $wootable->frontend->manage_page ) ) {
				get_sidebar();
			}
		?>

    </div><!-- /#content -->		
<?php get_footer(); ?>