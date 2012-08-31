<?php get_header(); ?>
    
    <?php include ( TEMPLATEPATH . '/search-form.php' ); ?>
	
	<div id="content" class="col-full">
		<div id="main" class="fullwidth">
        
       <?php if (get_option('woo_show_archive_map') == 'true') { include ( TEMPLATEPATH . '/includes/archive-maps.php' ); } ?>     
		<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>
		<?php if (have_posts()) : $count = 0; ?>
        
            <span class="archive_header"><span class="fl cat"><?php echo stripslashes( $woo_options['woo_archive_listings_header'] );  ?> | <?php _e( 'All Listings', 'woothemes' );?></span></span>
        	
            <div class="fix"></div>
        	
        	<div class="more-listings">
        	
        <?php while (have_posts()) : the_post(); $count++; ?>
                                                                    
            <div class="block">

                <?php woo_image('key=image&width=296&height=174'); ?>
                
                <?php 
                //Meta Data
                global $post;
        		$custom_field = $woo_options['woo_slider_image_caption'];
        		$listing_image_caption = get_post_meta($post->ID,$custom_field,true);
				if ($listing_image_caption != '' && $custom_field == 'price') { $listing_image_caption = number_format($listing_image_caption , 0 , '.', ','); }
				?>
                
                <?php if ($listing_image_caption != '') { ?><span class="price"><?php if ($custom_field == 'price') { echo $woo_options['woo_listings_currency']; } echo ''.$listing_image_caption ?></span><?php } ?>
                
                <h2 class="cufon"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
        		
        		<p><?php echo get_the_excerpt(); ?></p>
        		
        		<span class="more"><a href="<?php echo get_permalink($post->ID); ?>" title="<?php echo get_the_title($post->ID); ?>"><?php _e('More Info', 'woothemes'); ?></a></span>
        	
            </div><!-- /.block -->
            
            <?php
            	if ( $count % 3 == 0 ) {
            ?>
            	<div class="fix"></div>
            <?php
            	} // End IF Statement
            ?>
            
        <?php endwhile; ?>
        
        	</div><!-- /.more-listings -->
        
        <?php else: ?>
        
            <div class="post">
                <p><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></p>
            </div><!-- /.post -->
        
        <?php endif; ?> 
        
        	<div class="fix"></div>
    
			<?php woo_pagenav(); ?>
                
		</div><!-- /#main -->

    </div><!-- /#content -->
		
<?php get_footer(); ?>