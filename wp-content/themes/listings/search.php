<?php ob_start(); ?>
<?php get_header(); ?>
<?php 
// Do results check
$has_results = have_posts();
if ( get_search_query() == stripslashes( $woo_options['woo_search_panel_keyword_text'] ) ) { $keyword_to_search = ''; } else { $keyword_to_search = get_search_query(); }
// Output to the browser
ob_flush();
?>
    <?php include ( TEMPLATEPATH . '/search-form.php' ); ?>  
    <div id="content" class="col-full">
		<div id="main" class="fullwidth">
            <?php
            	$_more = false;
            	
            	if ( isset( $_REQUEST['more'] ) ) {
            	
            		$_moretext = strtolower( trim( strip_tags( $_REQUEST['more'] ) ) );
            	
            		if ( $_moretext == 'yes' ) {
            		
            			$_more = true;
            		
            		} // End IF Statement
            	
            	} // End IF Statement
            	
            	if ( $_more ) {
            	
            	$_text = __( 'More listings', 'woothemes' );
            	
            	if ( array_key_exists( 'woo_listings_more_header', $woo_options ) && $woo_options['woo_listings_more_header'] != '' ) {
				
					$_text = $woo_options['woo_listings_more_header'];
				
				} // End IF Statement
            	
            ?>
            <span class="archive_header"><?php echo $_text; ?></span>
            <?php
            	
            	} else {
            	
            ?>
            <span class="archive_header"><?php echo stripslashes( $woo_options['woo_search_results_header'] ) ?> <?php if ($keyword_to_search != '') { _e('for', 'woothemes'); ?> <em><?php echo '"'.$keyword_to_search.'"'; ?></em><?php } ?></span>
            <?php
            	
            	} // End IF Statement
            	
            ?>
            <?php if ($has_results || $_more) { ?>
			<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>
			<?php if (have_posts()) : $count = 0; ?>
            
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
        		
        		<span class="more"><a href="<?php echo get_permalink($post_item->ID); ?>" title="<?php echo get_the_title($post_item->ID); ?>"><?php _e('More Info', 'woothemes'); ?></a></span>
        	
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
            
            <div class="fix"></div>
            
            <?php else: ?>
            
                <div class="post">
                    <p class="woo-sc-box note"><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></p>
                </div><!-- /.post -->

            <?php endif; ?>  
        		
        	<?php if ($has_results) { ?> 
        		<?php woo_listingsnav(); ?>
        	 <?php } ?> 
        	 
            <?php } else { ?>
            	<div class="fix"></div>
            	
            	<div class="post">
                    <p class="woo-sc-box note"><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></p>
                </div><!-- /.post -->
                
            <?php } ?>    
        </div><!-- /#main -->

    </div><!-- /#content -->

	
<?php get_footer(); ?>
