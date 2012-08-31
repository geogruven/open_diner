<?php
/**
 * Homepage Features Panel
 */
 	
	/**
 	* The Variables
 	*
 	* Setup default variables, overriding them if the "Theme Options" have been saved.
 	*/
	
	global $woocommerce;
	
	$settings = array(
					'thumb_w' => 100, 
					'thumb_h' => 100, 
					'thumb_align' => 'alignleft',
					'shop_area' => 'false',
					'shop_area_entries' => 8,
					'shop_area_title' => '',
					'shop_area_message' => '',
					);
					
	$settings = woo_get_dynamic_values( $settings );
	
?>
			<section id="home-shop" class="minor flexslider fix">
			
				<div class="col-full">
    	
					<header class="section-title">
						<h1><?php echo stripslashes( $settings['shop_area_title'] ); ?></h1>
						<p><span><?php echo stripslashes( $settings['shop_area_message'] ); ?></span></p>
					</header> 
						
	    			<div class="flex-direction-nav"></div><!--/.flex-direction-nav-->
	    			<div class="fix"></div>
    			
	    			<ul class="slides">
					
						<?php
						$number_of_products = $settings['shop_area_entries'];
						$args = array( 
							'post_type' => 'product', 
							'posts_per_page' => $number_of_products, 
							'meta_query' => array( array(
								'key' => '_visibility',
								'value' => array('catalog', 'visible'),
								'compare' => 'IN'
								)) 
						);
	
						$first_or_last = 'first';
						$loop = new WP_Query( $args );
						$count = 0;
						global $post;
						while ( $loop->have_posts() ) : $loop->the_post(); $_product = &new WC_Product( $loop->post->ID ); $count++; ?>
						
							<li class="product <?php //if ( $count % 3 == 0 ) { echo 'last'; } ?>">
								<a href="<?php echo get_permalink( $loop->post->ID ) ?>" title="<?php echo esc_attr($loop->post->post_title ? $loop->post->post_title : $loop->post->ID); ?>" class="product-shot">
																
										<?php woocommerce_show_product_sale_flash( $post, $_product ); ?>
									
										<?php if (has_post_thumbnail( $loop->post->ID )) echo get_the_post_thumbnail($loop->post->ID, 'shop_catalog'); else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" />'; ?>
															
								</a>
								
								<div class="mask">
								
									<div class="content">
								
										<h3><a href="<?php echo get_permalink( $loop->post->ID ) ?>" title="<?php echo esc_attr($loop->post->post_title ? $loop->post->post_title : $loop->post->ID); ?>"><?php the_title(); ?></a></h3>
										<?php //echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?>
										<span class="price"><?php echo $_product->get_price_html(); ?></span>
										<?php woocommerce_template_loop_add_to_cart( $loop->post, $_product ); ?>
									
									</div><!--/.content-->
									
								</div><!--/.mask-->
								
								<div class="fix"></div>
								
							</li>
							
						<?php endwhile; ?>
					
					</ul><!--/ul.recent-->
					    			
				</div><!--/.col-full-->
    		
    		</section>
    		
    		<script type="text/javascript">
				jQuery(window).load(function() {
					jQuery('#home-shop .col-full').flexslider({
						controlsContainer: "#home-shop .flex-direction-nav",
						animation: "slide",
					    animationLoop: false,
					    itemWidth: 228,
					    controlNav: false,
					    maxItems: 4,
					    move: 4
					});
				});
			</script>
    		
    		<?php wp_reset_query(); ?>