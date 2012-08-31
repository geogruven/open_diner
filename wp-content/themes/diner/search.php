<?php get_header(); ?>
       
    <div id="content" class="col-full">
		<div id="main" class="col-left">
            
			<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>
			<?php if (have_posts()) : $count = 0; ?>
            
            <span class="archive_header"><?php _e('Search results:', 'woothemes') ?> <?php printf(the_search_query());?></span>
                
            <?php while (have_posts()) : the_post(); $count++; ?>
            <?php 	
            	$meal_price = 0;
            	$price = get_post_meta(get_the_ID(),'price',true);
				if ( $price ) { $meal_price = number_format($price , 2 , '.', ','); }
			?>
            <div class="fix"></div>
                                                     
            <!-- Post Starts -->
            <div class="post<?php if ($meal_price > 0) { ?> woo_menu<?php } ?>">
            
                <h2 class="title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                
                <?php if ($meal_price > 0) { } else { ?><?php woo_post_meta(); } ?>
                
                <div class="fix"></div>
                <?php if ($meal_price > 0) { $key_value = 'key=thumbnail&'; } else { $key_value = ''; } ?>
                <?php woo_image($key_value.'width='.$woo_options['woo_thumb_w'].'&height='.$woo_options['woo_thumb_h'].'&class=thumbnail '.$woo_options['woo_thumb_align']); ?> 
                
                <div class="entry">
                    <?php the_excerpt(); ?>
                    <?php if ($meal_price > 0) { ?><p class="ratings"><?php _e('Average Rating:', 'woothemes'); ?> <?php echo woo_get_post_rating_average(get_the_ID()); ?></p><?php } ?>
                </div><!-- /.entry -->
            
                <div class="post-more menu-comments">      
                    <span class="read-more"><a href="<?php the_permalink() ?>" title="<?php _e('Continue Reading','woothemes'); ?>"><?php _e('Continue Reading','woothemes'); ?></a></span> <span class="sep">&bull;</span>
                    <?php if ($meal_price > 0) { ?><span class="comments"><?php _e('Price per Serving:', 'woothemes'); ?> <?php echo get_option('woo_diner_currency').''.$meal_price; ?></span><?php } else { ?><span class="comments"><?php comments_popup_link(__('Comments ( 0 )', 'woothemes'), __('Comments ( 1 )', 'woothemes'), __('Comments ( % )', 'woothemes')); ?></span><?php } ?>
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
