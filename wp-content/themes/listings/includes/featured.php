<?php global $woo_options; $count = 0; ?>
<div id="loopedSlider" class="loopedSlider">
	<?php if($woo_options['woo_featured_header']) { ?><h2 class="cufon"><?php echo stripslashes($woo_options['woo_featured_header']); ?></h2><?php } ?>
	<?php $woo_slider_pos = $woo_options['woo_slider_image']; ?>
	<?php 
	$featposts = $woo_options['woo_featured_entries']; // Number of featured entries to be shown
	
	$GLOBALS['feat_tags_array'] = explode(',',get_option('woo_featured_tags')); // Tags to be shown
	foreach ($GLOBALS['feat_tags_array'] as $tags){ 
    	$tag = get_term_by( 'name', trim($tags), 'post_tag', 'ARRAY_A' );
		if ( $tag['term_id'] > 0 )
			$tag_array[] = $tag['term_id'];
	}
	$slides = get_posts(array('post_type' => 'any','tag__in' => $tag_array, 'numberposts' => $featposts)); ?>
	
	<?php if (!empty($slides) && ($featposts > 1)) : $count = 0; ?>
	
	<ul class="nav-buttons <?php if ($woo_slider_pos == 'Right') { echo 'right'; } ?>">
    	<li id="n"><a href="#" class="next"></a></li>
        <li id="p"><a href="#" class="previous"></a></li>
    </ul>
	        
	<?php endif; ?> 
	
	<div class="container">
	
	<?php if (!empty($slides)) { ?>
		<div class="slides" <?php if($featposts == 1) { echo 'style="display: block;position: relative;"'; }?>>  
		<?php foreach($slides as $post) : setup_postdata($post); $count++; ?>
			
			<?php 
        	    $post_id = $post->ID;
        	    $post_type = $post->post_type;
        	    //Meta Data
        	    $custom_field = $woo_options['woo_slider_image_caption'];
        	    $listing_image_caption = get_post_meta($post->ID,$custom_field,true);
				if ($listing_image_caption != '' && $custom_field == 'price') { $listing_image_caption = number_format($listing_image_caption , 0 , '.', ','); }
        	?>
			
			<div class="slide slide-<?php echo $count; ?>" <?php if($featposts == 1) { echo 'style="display:block;"'; }?>>
			
				<div class="slider-img <?php if ($woo_slider_pos == 'Right') { echo 'fr'; } else { echo 'fl'; } ?>">
            		<?php
            		// If a featured image is available, use it in priority over the "image" field.
					if ( function_exists( 'has_post_thumbnail' ) && current_theme_supports( 'post-thumbnails' ) ) {
					
						if ( has_post_thumbnail( $post_id ) ) {
						
							$_id = 0;
							$_id = get_post_thumbnail_id( $post_id );
							
							if ( intval( $_id ) ) {
							
								$_image = array();
								$_image = wp_get_attachment_image_src( $_id, 'full' );
								
								// $_image should have 3 indexes: url, width and height.
								if ( count( $_image ) ) {
								
									$_image_url = $_image[0];
									
									echo '<a href="' . get_permalink( $post_id ) . '" title="' . get_the_title() . '">';
									
									woo_image('src=' . $_image_url . '&key=image&width=534&height=321&link=img');
									
									echo '</a>' . "\n";
												
								} // End IF Statement
							
							} // End IF Statement
						
						} else {
						
							echo '<a href="' . get_permalink( $post_id ) . '" title="' . get_the_title() . '">';
							
							woo_image('id='.$post_id.'&key=image&width=534&height=321&link=img');
							
							echo '</a>' . "\n";
						
						} // End IF Statement
					
					} else {
					
						echo '<a href="' . get_permalink( $post_id ) . '" title="' . get_the_title() . '">';
						
						woo_image('id='.$post_id.'&key=image&width=534&height=321&link=img');
						
						echo '</a>' . "\n";
					
					} // End IF Statement
            		
            		?>
            		<?php if ($listing_image_caption != '') { ?><span class="price"><?php if ($custom_field == 'price') { echo $woo_options['woo_listings_currency']; } echo ''.$listing_image_caption ?></span><?php } ?>
            		
            	</div>
            	
            	<div class="slider-content <?php if($featposts > 1){ echo 'with_buttons '; } if ($woo_slider_pos == 'Right') { echo 'fl'; } else { echo 'fr'; } ?>">
					<h2 class="cufon"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>	
				</div>
	
            	<div class="fix"></div>    
            	            
			</div><!-- /.slide -->
			
		<?php endforeach; ?>
						
		</div><!-- /.slides_container -->
	
	<?php if (get_option('woo_exclude') <> $GLOBALS['shownposts']) update_option("woo_exclude", $GLOBALS['shownposts']); ?>
    <?php } else { ?>    
	<p class="woo-sc-box note"><?php _e('Please setup Featured Panel tag(s) in your options panel. You must setup tags that are used on active posts.','woothemes'); ?></p>
	<?php } ?>
	
	</div><!-- /#slide-box -->
	<div class="fix"></div>
</div><!-- /#slides -->
