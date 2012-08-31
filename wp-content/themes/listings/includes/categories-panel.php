<?php
global $woo_options;
$post_types_to_loop_through = array();
$posts_to_exclude = array();
$categories_panel_entries = $woo_options['woo_categories_panel_entries'];
$more_listings_setting = $woo_options['woo_more_listings_area'];
$wp_custom_post_types_args = array();
$wp_custom_post_types = get_post_types($wp_custom_post_types_args,'objects');
foreach ($wp_custom_post_types as $post_type_item) {
	$cpt_test = get_option('woo_categories_panel_post_types_'.$post_type_item->name);
	if ($cpt_test == 'true') {
		$cpt_nice_name = $post_type_item->labels->name;
		$cpt_has_archive = $post_type_item->has_archive;
		$post_types_to_loop_through[$post_type_item->name] = array('nice_name' => $cpt_nice_name, 'has_archive' => $cpt_has_archive);
	}
}

$section_counter = 0;

foreach ($post_types_to_loop_through as $post_type_item => $post_type_item_nice_name) {
	$taxonomies = get_object_taxonomies($post_type_item);
	$block_counter = 0;
	$args = array();
	?>

	<div class="listings <?php /*if ($section_counter > 0) { echo 'bordertop'; }*/ ?>">
    	<h2 class="cufon"><?php printf( __( '%s Categories', 'woothemes' ), $post_type_item_nice_name['nice_name'] ); ?></h2>
		
		<?php 
		// NEW AND IMPROVED QUERY
		$all_terms = get_terms( $taxonomies, $args );
		
		$block_counter = 0;
		foreach ( $all_terms as $all_term) {
			
			$tax_test = get_option('woo_categories_panel_taxonomies_'.$all_term->taxonomy);
			
			if ( ($tax_test == 'true') && ($block_counter < $categories_panel_entries) ) {
			
				$post_images = array();
				$posts_aray = array();
				
				$term_name = $all_term->name;
				$term_slug = $all_term->slug;
				$term_id = $all_term->term_id;
				$term_link = get_term_link( $all_term, $all_term->taxonomy );
				$counter_value = $all_term->count;
				?>
				<div class="block">
				    <a href="<?php echo $term_link; ?>">
				    <?php
				    	$block_counter++;
				    	
				    	// GET LATEST POST IMAGE - gets latest 5 results
				    		
				    		$posts_array = woo_get_posts_in_taxonomy($term_id, $all_term->taxonomy, $post_type_item, 5);
				    		
				    		$temp_array = array();
				    		
				    		if ( count( $posts_array ) > 0 ) {
								
								$has_image = false;
								
								$loop_counter = 0;
								foreach	($posts_array as $post_item) {
								
									if ( $loop_counter == 0 ) {
										$post_date_raw = $post_item['date'];
									}
									$post_id_raw = $post_item['ID'];
									array_push($posts_to_exclude, $post_id_raw);
									$post_image_check = get_post_meta($post_id_raw,'image',true);
			        				
			        				if ($post_image_check != '' || has_post_thumbnail( $post_id_raw ) ) {
			        					array_push($post_images, $post_id_raw);
			        					if (!$has_image) {
			        						$post_id = $post_id_raw;
			        						$has_image = true;
			        					}
			        					
			        				} else {
			        					$post_id = 0;
			        				}
									$loop_counter++;
									
								} // End FOREACH Loop
								
								} else {
								
									$post_id = 0;
								
								} // End IF Statement

				    	
				    	if ( $post_id > 0 ) {
				    	
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
				    						
				    						woo_image('src=' . $_image_url . '&key=image&width=139&height=81&link=img');
				    					
				    					} // End IF Statement
				    				
				    				} // End IF Statement
				    			
				    			} else {
				    			
				    				woo_image('id='.$post_id.'&key=image&width=139&height=81&link=img');
				    				
				    			} // End IF Statement
				    		
				    		} else {
				    		
				    			woo_image('id='.$post_id.'&key=image&width=139&height=81&link=img');
				    		
				    		} // End IF Statement
				    	
				    	} else {
				    		// Fallback
				    		woo_taxonomy_image($post_images,$term_link);
				    		
				    	} // End IF Statement
				    	$php_formatting = "m\/d\/Y";
		        		$post_item_date = strtotime($post_date_raw);
				    ?>
				    	</a>
        		    <h2><a href="<?php echo $term_link; ?>"><?php echo $term_name ?> <br/><span>(<?php echo $counter_value; if( $counter_value == 1 ) {_e(' Listing', 'woothemes');} else {_e(' Listings', 'woothemes');} ?>)</span></a></h2>
        		    <p><?php _e('Latest listing ', 'woothemes') ?><?php echo date($php_formatting,$post_item_date); ?></p>
        		</div><!-- /.block -->
				<?php
        		if ( $block_counter % 3 == 0 ) {
        		?>
        		    <div class="fix"></div>
        		<?php
        	   	} // End IF Statement	
				
			} // End IF Statement
			
        	?>
			<?php
					
        			
		} // End For Loop
		
		?>
				
    	<div class="fix"></div>
		
		<?php if ( $block_counter > 0 ) { ?>
		
		<?php
			$view_all_link = '';
			// Check if CPT taxonomy landing page exists
			$page_exists = false;
			$listings_cpt_page = get_option('woo_listings_cpt_page');
			if ( $listings_cpt_page != '' ) {
				$page_test = get_page($listings_cpt_page);
				if ( isset($page_test) && ( $page_test->post_status == 'publish' ) ) {
					$page_exists = true;
				} else {
					$page_exists = false;
				}
			} // End If Statement
			if ( $page_exists ) {
				$view_all_link = get_permalink($listings_cpt_page).'?landing_page='.$post_type_item;
			} else {
				$view_all_link = get_post_type_archive_link( $post_type_item );
			}
		?>
		<h3 class="banner"><a href="<?php echo $view_all_link; ?>" title="<?php printf( __( 'View all %s categories', 'woothemes' ), $post_type_item_nice_name['nice_name'] ); ?>"><?php printf( __( 'View all %s categories', 'woothemes' ), $post_type_item_nice_name['nice_name'] ); ?></a></h3>
		
		<?php } else { ?>    
		<p class="woo-sc-box note" style="margin:20px 20px;"><?php _e('Please add some posts in order for this panel to work correctly. You must assign the post to these taxonomies for it to work.','woothemes'); ?></p>
		<?php } ?>
		
    </div><!-- /.listings -->
    
    <div class="fix"></div>
	
<?php } // End FOR Loop ?>