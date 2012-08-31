<?php 
global $woo_options;
$more_listings_setting = $woo_options['woo_more_listings_area'];
if ($more_listings_setting == 'true') { ?>
	<?php
	$post_types_to_loop_through = array();
	$wp_custom_post_types_args = array();
	$wp_custom_post_types = get_post_types($wp_custom_post_types_args,'objects');
	foreach ($wp_custom_post_types as $post_type_item) {
		$cpt_test = get_option('woo_more_listings_area_post_types_'.$post_type_item->name);
		if ($cpt_test == 'true') {
			array_push($post_types_to_loop_through, $post_type_item->name);
		}
	}
	
	$more_query_args['post_type'] = $post_types_to_loop_through;
	$more_query_args['numberposts'] = $woo_options['woo_more_listings_area_entries'];
	$more_query_args['orderby'] = 'date';
	$more_query_args['order'] = 'DESC';
	$more_query_args['post_status'] = 'publish';
	$more_query_args['suppress_filters'] = 0;
	$more_posts = get_posts($more_query_args);
	?>
	<div class="more-listings">
	
    	<h2 class="cufon"><?php echo stripslashes( $woo_options['woo_listings_more_header'] ) ?></h2>

		<?php
		$post_counter = 0;
		foreach ($more_posts as $post_item) {
			$post_counter++;
			//Meta Data
        	$custom_field = $woo_options['woo_slider_image_caption'];
        	$listing_image_caption = get_post_meta($post_item->ID,$custom_field,true);
			if ($listing_image_caption != '' && $custom_field == 'price') { $listing_image_caption = number_format($listing_image_caption , 0 , '.', ','); }
		?>
        <div class="block">
      		<a href="<?php echo get_permalink($post_item->ID); ?>" title="<?php echo get_the_title($post_item->ID); ?>">
        	<?php
				if ( $post_item->ID > 0 ) {
				
					// If a featured image is available, use it in priority over the "image" field.
					if ( function_exists( 'has_post_thumbnail' ) && current_theme_supports( 'post-thumbnails' ) ) {
					
						if ( has_post_thumbnail( $post_item->ID ) ) {
						
							$_id = 0;
							$_id = get_post_thumbnail_id( $post_item->ID );
							
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
						
							woo_image('id='.$post_item->ID.'&key=image&width=296&height=174&link=img');
						
						} // End IF Statement
					
					} else {
					
						woo_image('id='.$post_item->ID.'&key=image&width=296&height=174&link=img');
					
					} // End IF Statement
					
				} // End IF Statement
				
				// Determine the post excerpt, based on whether a custom excerpt is present or not.
				
				$excerpt = '';
				
				if ( has_excerpt( $post_item->ID ) ) {
				
					$excerpt = $post_item->post_excerpt;
				
				} else {
				
					$excerpt = $post_item->post_content;
					
					// $excerpt = apply_filters( 'get_the_excerpt', $excerpt );
				
					// $excerpt = wp_trim_excerpt( $excerpt );
					
					// Manually truncate text from the content into an excerpt,
					// as the wp_trim_excerpt() function and apply_filters( 'get_the_excerpt' )
					// weren't working correctly.
					
					// The below is from wp_trim_excerpt() in wp-includes/formatting.php.
					
					$text = $excerpt;
					
					$text = strip_shortcodes( $text );
	
					$text = apply_filters('the_content', $text);
					$text = str_replace(']]>', ']]&gt;', $text);
					$text = strip_tags($text);
					$excerpt_length = apply_filters('excerpt_length', 55);
					$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
					$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
					
					if ( count($words) > $excerpt_length ) {
						
						array_pop($words);
						$text = implode(' ', $words);
						$text = $text . $excerpt_more;
					
					} else {
					
						$text = implode(' ', $words);
					
					} // End IF Statement
					
					$excerpt = $text;
				
				} // End IF Statement
			?>       	
        	<?php if ($listing_image_caption != '') { ?><span class="price"><?php if ($custom_field == 'price') { echo $woo_options['woo_listings_currency']; } echo ''.$listing_image_caption ?></span><?php } ?>
        	</a>
        	<h2 class="cufon"><a href="<?php echo get_permalink($post_item->ID); ?>" title="<?php echo get_the_title($post_item->ID); ?>"><?php echo $post_item->post_title; ?></a></h2>
        	<p><?php echo $excerpt; /*get_the_excerpt($post_item->ID);*/ ?></p>
        	<span class="more"><a href="<?php echo get_permalink($post_item->ID); ?>" title="<?php echo get_the_title($post_item->ID); ?>"><?php _e('More Info', 'woothemes'); ?></a></span>
        </div><!-- /.block -->
		<?php
        if ( $post_counter % 3 == 0 ) {
        ?>
        	<div class="fix"></div>
        <?php
           	} // End IF Statement
        ?>
		<?php } ?>
        	<div class="fix"></div>
        	<?php
	$_link_text = 'View more latest listings';
	
	if ( array_key_exists( 'woo_listings_viewmore_label', $woo_options ) && $woo_options['woo_listings_viewmore_label'] != '' ) {
	
		$_link_text = $woo_options['woo_listings_viewmore_label'];
	
	} // End IF Statement
?>
<?php $view_all_link = get_bloginfo('url') . '/?s=' . stripslashes( $woo_options['woo_search_panel_keyword_text'] ) . '&amp;more=yes'; ?>
<h3 class="banner"><a href="<?php echo $view_all_link; ?>"><?php echo $_link_text; ?></a></h3>
        	</div><!-- /.more-listings -->
    		<div class="fix"></div>
	
	<?php $section_counter++; ?>
	
	<?php } ?>