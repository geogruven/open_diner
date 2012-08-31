<?php
/**
 * Template Name: Custom Post Types Page Template
 *
 * Selectable from a dropdown menu on the edit page screen.
 */
?>

<?php get_header(); ?>

<?php include ( TEMPLATEPATH . '/search-form.php' ); ?>
	
	<div id="content" class="col-full">
		<div id="main" class="col-left">
        
        <?php    
		$post_type_item = $_GET['landing_page'];
		if ($post_type_item == '') {
			$post_type_item = 'listing';
		}
		
		// Sanitise.
		$post_type_item = strtolower( trim( strip_tags( $post_type_item ) ) );
		
		// If a custom taxonomy is passed through the query string, show only that taxonomy.
		$taxonomy_override = '';
		if ( isset($_GET['taxonomy']) ) {
			$taxonomy_override = $_GET['taxonomy'];
		}
		
		if ( $taxonomy_override == '' ) {} else {
		
			$taxonomy_override = strtolower( trim( strip_tags( $taxonomy_override ) ) );
		
		} // End IF Statement
		
		$taxonomies = get_object_taxonomies($post_type_item);
		//remove post_tag from listings
		if ( (in_array('post_tag', $taxonomies)) ) {
			foreach ($taxonomies as $key => $value) {
				if ($value == 'post_tag') {
					$key_to_unset = $key;
				}
			}
			unset($taxonomies[$key_to_unset]);	
		}
		
		// If we've got a taxonomy override, and it's valid, use only that taxonomy.
		if ( $taxonomy_override != '' && in_array( $taxonomy_override, $taxonomies ) ) {
		
			foreach ( $taxonomies as $k => $v ) {
			
				if ( $v == $taxonomy_override ) {} else {
				
					unset( $taxonomies[$k] );
				
				} // End IF Statement
			
			} // End FOREACH Loop
		
		} // End IF Statement
		
		$custom_post_type_obj = get_post_type_object($post_type_item);
		$post_type_item_nice_name = $custom_post_type_obj->labels->name;
		
		$block_counter = 0;
		?>
		
		<div class="listings <?php if ($section_counter > 0) { echo 'bordertop'; } ?>">
    		<h2 class="cufon"><?php printf( __( '%s Categories', 'woothemes' ), $post_type_item_nice_name ); ?></h2>
			<?php
			foreach ($taxonomies as $taxonomy) {
				$post_images = array();
				$args = array();
				$terms = get_terms( $taxonomy, $args );
			    foreach ($terms as $term) {
			    	$term_name = $term->name;
			    	$term_slug = $term->slug;
			    	$term_id = $term->term_id;
			    	
			    	$term_count = $term->count;
			        // Get the URL of Taxonomy Term
    		        $term_link = get_term_link( $term, $taxonomy );
			        $category_list = get_categories(array('taxonomy' => $taxonomy,'pad_counts' => true));
			        foreach ($category_list as $count_item) {
			        	
			        	$custom_taxonomy_posts_query_args = array(
                        	'post_type' => 'any',
                        	'post_status' => 'publish',
								'tax_query' => array(
									array(
										'taxonomy' => $taxonomy,
										'field' => 'slug',
										'terms' => $count_item->slug
									)
								)
							);
						$custom_taxonomy_posts_query = new WP_Query( $custom_taxonomy_posts_query_args );
			        
			        	if ( count($custom_taxonomy_posts_query->posts) > 0 ) {
							// Do nothing
							if ($count_item->name == $term_name) {
                        		$counter_value = $count_item->count;
                        	}
						} else {
							if ($count_item->name == $term_name) {
                        		$counter_value = -1;
                        	}
						} // End If Statement

			        }
			        ?>
			        <?php 
			        if ($counter_value != -1) {
			        
			        	if ( ($term_count > 0) ) {
			        
			        		//$myposts = get_posts('post_type='.$post_type_item.'&orderby=date&order=DESC&'.$taxonomy.'='.$term_slug);
			        		$posts_array = woo_get_posts_in_taxonomy($term_id, $taxonomy, $post_type_item, 5);
			        		$temp_array = array();
			        		
			        		$posts_to_exclude = array(); // Make sure this is an array before working with it.
			        		
			        		$has_image = false;
			        		
			        		// FIX FOR INCORRECT IMAGES
			        		$post_images = array();
			        		
			        		foreach	($posts_array as $post_item) {
			        		
			        			$post_date_raw = $post_item['date'];
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
			        		
			        		}
			        		
			        		} else {
								
									$post_id = 0;
								
							} // End IF Statement
			        		
			        		$php_formatting = "m\/d\/Y";
			        		$post_item_date = strtotime($post_date_raw);
			        		/*$gallery = do_shortcode('[gallery id="'.$post_id.'" size="thumbnail" columns="4"]');
			        		if ($gallery) { } else { $gallery = get_post_meta($post_id,'image',true);  }*/
			        		
			        		$gallery = '';
						
							if ( $post_id ) { $gallery = do_shortcode('[gallery id="'.$post_id.'" size="thumbnail" columns="4"]'); } // End IF Statement
		        			
		        			if ($gallery) {} else { $gallery = get_post_meta($post_id,'image',true);  }
        	        		?>
			        		<div class="block"><?php $block_counter++; ?>
			        			<a href="<?php echo $term_link; ?>">
			        			<?php // if ($gallery) { woo_image('id='.$post_id.'&key=image&width=139&height=81&link=img'); } else { woo_taxonomy_image($post_images,$term_link); } ?>
			        			<?php
									if ( $gallery && $post_id > 0 ) {
									
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
									
										woo_taxonomy_image($post_images,$term_link);
										
									} // End IF Statement
								?>
			        			</a>
    	            			<h2><a href="<?php echo $term_link; ?>"><?php echo $term_name ?> <br/><span>(<?php echo $counter_value; ?> <?php _e('Listings', 'woothemes') ?>)</span></a></h2>
    	            			<p><?php _e('Latest listing ', 'woothemes') ?><?php echo date($php_formatting,$post_item_date); ?></p>
    	            		</div><?php if ($block_counter == 2) { $block_counter = 0; echo '<div class="fix"></div>'; } ?>
    	            	
    	            	<?php }  // End IF Statement
				
					} // End FOREACH Loop
    	        
    	        } // End FOREACH Loop
				
			//} ?>
    	    		
    		<div class="fix"></div>
		
    	</div><!-- /.listings -->
    	
    	<div class="fix"></div>

                
		</div><!-- /#main -->

        <?php get_sidebar(); ?>

    </div><!-- /#content -->
    

<?php get_sidebar(); ?>
<?php get_footer(); ?>
