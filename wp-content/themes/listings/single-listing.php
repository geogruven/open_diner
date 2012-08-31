<?php get_header(); ?>

<?php include ( TEMPLATEPATH . '/search-form.php' ); ?>

<?php if (have_posts()) : $count = 0; ?>
<?php while (have_posts()) : the_post(); $count++; ?>

	<?php 
	global $woo_options, $post; 
	if ( isset( $woo_options['woo_single_listing_image_caption'] ) ) {
		$listing_image_caption = get_post_meta($post->ID,$woo_options['woo_single_listing_image_caption'],true);
		if ($listing_image_caption != '' && $woo_options['woo_single_listing_image_caption'] == 'price') { $listing_image_caption = number_format($listing_image_caption , 0 , '.', ','); }
	} else {
		$listing_image_caption = '';
	} // End If Statement
	//setup features array
	if ( $woo_options['woo_single_listing_feature_taxonomy'] ) {
		$features_list = get_the_term_list( $post->ID, $woo_options['woo_single_listing_feature_taxonomy'], '' , '|' , ''  );
		$features_array = explode('|', $features_list);
		//setup similar array
		$similar_list = get_the_term_list( $post->ID, $woo_options['woo_single_listing_related_taxonomy'], '' , '|' , ''  );
		$similar_list = strip_tags($similar_list);
		$similar_array = explode('|', $similar_list);
		$similar_results = '';
		foreach ($similar_array as $similar_item) {
		    $similar_id = get_term_by( 'name', $similar_item, $woo_options['woo_single_listing_related_taxonomy'] );
		    $similar_results = $similar_id->slug.',';
		}
	} else {
		$features_array = array();
		$similar_results = '';
	} // End If Statement
	?>
       
    <div id="content" class="col-full">
		<div id="main-single" class="col-left">

			<div class="post">
				<h1><a href="<?php the_permalink(); ?>" class="cufon"><?php the_title(); ?></a><?php if ($listing_image_caption != '') { ?><span class="price"><?php if ($woo_options['woo_single_listing_image_caption'] == 'price') { ?><?php echo stripslashes( $woo_options['woo_single_listing_starting_at_text'] ) ?><strong><?php echo $woo_options['woo_listings_currency'].$listing_image_caption; ?></strong><?php } else { echo $listing_image_caption; } ?></span><?php } ?></h1>
			
				<div class="meta">
					<ul>
					<?php foreach ($features_array as $feature_item) { ?>
    			        <li><?php echo $feature_item; ?></li>
    			    <?php } ?>
					</ul>
				</div>
				
				<div class="entry">
				
					<?php the_content(); ?>
				
				</div>
				
				<?php edit_post_link( __('{ Edit }', 'woothemes'), '<span class="small">', '</span>' ); ?>
			
			</div>
	
			<?php woo_subscribe_connect(); ?>
			
		</div>
		
		<div id="sidebar-single" class="col-right">
			
			<div id="gallery">
				<h2 class="cufon"><?php echo stripslashes( $woo_options['woo_single_listing_image_gallery_title'] ) ?></h2>
				<?php 
				$gallery = do_shortcode('[gallery size="thumbnail" columns="4"]');
				if ($gallery) {
					include('includes/gallery.php'); // Photo gallery
				} else {
					// echo 'no-gallery'; 
				}
				?>
			</div>
			
			<?php $maps_active = get_post_meta($post->ID,'woo_maps_enable',true); ?>
	    	                    
            <?php if($maps_active == 'on') { ?>
               
			<div class="map">
				<h2 class="cufon"><?php echo stripslashes( $woo_options['woo_single_listing_google_map_title'] ) ?></h2>
				
               	<div class="map <?php if (!empty($video)) { echo 'fr'; } else { echo 'wide'; } ?>">
                	
               		<?php 
	    			if($maps_active == 'on'){
	    				$mode = get_post_meta($post->ID,'woo_maps_mode',true);
						$streetview = get_post_meta($post->ID,'woo_maps_streetview',true);
                       	$address = get_post_meta($post->ID,'woo_maps_address',true);
                       	$long = get_post_meta($post->ID,'woo_maps_long',true);
                      	$lat = get_post_meta($post->ID,'woo_maps_lat',true);
						$pov = get_post_meta($post->ID,'woo_maps_pov',true);
                       	$from = get_post_meta($post->ID,'woo_maps_from',true);
                       	$to = get_post_meta($post->ID,'woo_maps_to',true);
                       	$zoom = get_post_meta($post->ID,'woo_maps_zoom',true);
                       	$type = get_post_meta($post->ID,'woo_maps_type',true);
                       	$yaw = get_post_meta($post->ID,'woo_maps_pov_yaw',true);
						$pitch = get_post_meta($post->ID,'woo_maps_pov_pitch',true);
                       	if(!empty($lat) OR !empty($from)){
                           	woo_maps_single_output("mode=$mode&streetview=$streetview&address=$address&long=$long&lat=$lat&pov=$pov&from=$from&to=$to&zoom=$zoom&type=$type&yaw=$yaw&pitch=$pitch"); 
                       	}
	    			}
	    			?>
                		                    	
            	</div><!-- /.map -->
                
			</div>
			
			<?php } ?>
			
		</div>
			
		<div class="fix"></div>
		
		<div class="fullwidth">	
	           
			<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>


			 <?php 
            	//RELATED PROPERTIES - BY LOCATION
	    		$similar_results = chop($similar_results,',');
	    		$query_args = array(	'post_type' 		=> $post->post_type,
	    		    					'post__not_in'		=> array($post->ID),
	    		    					'tax_query' => array(
																array(
																	'taxonomy' => $woo_options['woo_single_listing_related_taxonomy'],
																	'field' => 'slug',
																	'terms' => $similar_results
																)
															),
	    		    					'posts_per_page' 	=> 3,
	    		    					'orderby' 			=> 'rand'
	    		    					);
	    		    					
	    		$related_query = new WP_Query($query_args);
	    		if ($related_query->have_posts()) : $count = 0; ?>
	    			
	    			<div class="similar-listings">
        				<h2 class="cufon"><?php echo stripslashes( $woo_options['woo_single_listing_similar_listings_title'] ) ?></h2>
	    			
	    				<?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
	    			        <?php  
	    			        $listing_image_caption = get_post_meta($post->ID,$woo_options['woo_single_listing_image_caption'],true);
							if ($listing_image_caption != '' && $woo_options['woo_single_listing_image_caption'] == 'price') { $listing_image_caption = number_format($listing_image_caption , 0 , '.', ','); }
	    			        ?>
	    			    	<div class="block">
	    			    		<a href="<?php the_permalink(); ?>">
	    			    		<?php // woo_image('id='.$post->ID.'&key=image&width=296&height=174&link=img'); ?>
	    			    		<?php
									if ( $post->ID > 0 ) {
									
										// If a featured image is available, use it in priority over the "image" field.
										if ( function_exists( 'has_post_thumbnail' ) && current_theme_supports( 'post-thumbnails' ) ) {
										
											if ( has_post_thumbnail( $post->ID ) ) {
											
												$_id = 0;
												$_id = get_post_thumbnail_id( $post->ID );
												
												if ( intval( $_id ) ) {
												
													$_image = array();
													$_image = wp_get_attachment_image_src( $_id, 'full' );
													
													// $_image should have 3 indexes: url, width and height.
													if ( count( $_image ) ) {
													
														$_image_url = $_image[0];
														
														woo_image('src=' . $_image_url . '&key=image&width=296&height=174&link=img');
													
													} // End IF Statement
												
												} // End IF Statement
											
											} else {
											
												woo_image('id='.$post->ID.'&key=image&width=296&height=174&link=img');
											
											} // End IF Statement
										
										} else {
										
											woo_image('id='.$post->ID.'&key=image&width=296&height=174&link=img');
										
										} // End IF Statement
										
									} // End IF Statement
								?>
	    			    		</a> 
        						<?php if ($listing_image_caption != '') { ?><span class="price"><?php if ($woo_options['woo_single_listing_image_caption'] == 'price') { ?><?php echo $woo_options['woo_listings_currency'].$listing_image_caption; ?><?php } else { echo $listing_image_caption; } ?></span><?php } ?>
        						<h2 class="cufon"><?php the_title(); ?></h2>
        						<?php the_excerpt(); ?>
        						<span class="more"><a href="<?php the_permalink(); ?>"><?php _e('More Info', 'woothemes') ?></a></span>
        					</div>
        		
	    			        <?php endwhile; ?>
	    	
        			</div><!-- /.more-listings -->
    				<div class="fix"></div>  
        	
        	<?php else: endif; ?>
        	
        </div><!-- /.fullwidth -->
        	
    </div><!-- /#content -->
	
<?php endwhile; ?>         
<?php endif; ?>
	
<?php get_footer(); ?>