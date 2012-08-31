<?php
/*
Template Name: Blog
*/
?>
<?php get_header(); ?>
<?php global $woo_options; ?>

    <!-- #content Starts -->
	<?php woo_content_before(); ?>
    <div id="content" class="col-full">
    
        <!-- #main Starts -->
        <div id="main" class="col-left">      
                    
		<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>
        <?php 
			// WP 3.0 PAGED BUG FIX
			if ( get_query_var('paged') )
				$paged = get_query_var('paged');
			elseif ( get_query_var('page') ) 
				$paged = get_query_var('page');
			else 
				$paged = 1;
			//$paged = (get_query_var('paged')) ? get_query_var('paged') : 1; 
       		 
             query_posts("post_type=post&paged=$paged"); 
        ?>
        <?php if (have_posts()) : $count = 0; ?>
        <?php while (have_posts()) : the_post(); $count++; ?>
                                                                    
            <!-- Post Starts -->
            <div <?php post_class(); ?>>

                <?php woo_post_inside_before(); ?>
                
                <h2 class="title cufon"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                
                <?php woo_image('width='.$woo_options['woo_thumb_w'].'&height='.$woo_options['woo_thumb_h'].'&class=thumbnail '.$woo_options['woo_thumb_align']); ?> 

                <?php woo_post_meta(); ?>
                
                <div class="entry">
					<?php global $more; $more = 0; ?>	                                        
                    <?php if ( $woo_options['woo_post_content'] == "content" ) the_content(__('Read More...', 'woothemes')); else the_excerpt(); ?>
                </div>
    			<div class="fix"></div>
    			
                <div class="post-more">      
                	<?php if ( $woo_options['woo_post_content'] == "excerpt" ) { ?>
                    <span class="read-more"><a href="<?php the_permalink() ?>" title="<?php _e('Continue Reading','woothemes'); ?>"><?php _e('Continue Reading','woothemes'); ?></a></span> <span class="sep">&bull;</span>
                    <?php } ?>
                    <span class="comments"><?php comments_popup_link(__('Comments ( 0 )', 'woothemes'), __('Comments ( 1 )', 'woothemes'), __('Comments ( % )', 'woothemes')); ?></span>
                </div>   
    			
    
            </div><!-- /.post -->
                                                
        <?php endwhile; else: ?>
            <div class="post">
                <p><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></p>
            </div><!-- /.post -->
        <?php endif; ?>  
    
            <?php woo_pagenav(); ?>
                
        </div><!-- /#main -->
            
		<?php get_sidebar(); ?>

    </div><!-- /#content -->    
		
<?php get_footer(); ?>