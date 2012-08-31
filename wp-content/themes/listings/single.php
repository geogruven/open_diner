<?php get_header(); ?>
<?php global $woo_options; ?>
       
    <div id="content" class="col-full">
		<div id="main" class="col-left">
		           
			<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>

            <?php if (have_posts()) : $count = 0; ?>
            <?php while (have_posts()) : the_post(); $count++; ?>
            
				<div <?php post_class(); ?>>

                    <h1 class="title cufon"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>

                    <?php if ( $woo_options['woo_thumb_single'] == "true" ) woo_image('width='.$woo_options['woo_single_w'].'&height='.$woo_options['woo_single_h'].'&class=thumbnail '.$woo_options['woo_thumb_single_align']); ?>
                    
                    <?php woo_post_meta(); ?>
                    
                    <div class="entry">
                    	<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>
					</div>
										
					<?php the_tags('<p class="tags">'.__('Tags: ', 'woothemes'), ', ', '</p>'); ?>
					
					<?php edit_post_link( __('{ Edit }', 'woothemes'), '<span class="small">', '</span>' ); ?>
                    
                </div><!-- /.post -->

				<?php woo_subscribe_connect(); ?>
				
                <?php woo_postnav(); ?>
                
                <?php $comm = $woo_options['woo_comments']; if ( ($comm == "post" || $comm == "both") ) : ?>
	                <?php comments_template('', true); ?>
                <?php endif; ?>
                                                    
			<?php endwhile; else: ?>
				<div class="post">
                	<p><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></p>
  				</div><!-- /.post -->             
           	<?php endif; ?>  
        
		</div><!-- /#main -->

        <?php get_sidebar(); ?>

    </div><!-- /#content -->
		
<?php get_footer(); ?>