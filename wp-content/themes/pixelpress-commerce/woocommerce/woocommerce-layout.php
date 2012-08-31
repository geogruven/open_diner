<?php
/*-----------------------------------------------------------------------------------*/
/* Any WooCommerce overrides can be found here
/*-----------------------------------------------------------------------------------*/

// Disable WooCommerce styles 
define('WOOCOMMERCE_USE_CSS', false);

// Remove the WooCommerce sidebar
remove_action('woocommerce_sidebar','woocommerce_get_sidebar');

// Adjust markup on all WooCommerce pages
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

add_action( 'woocommerce_before_main_content', 'pixelpress_before_content', 10 );
add_action( 'woocommerce_after_main_content', 'pixelpress_after_content', 20 );

// Fix the layout etc
if (!function_exists('pixelpress_before_content')) {
	function pixelpress_before_content() {
	?>
		<!-- #content Starts -->
		<?php woo_content_before(); ?>
	    <div id="content">
	    	<div class="col-full">
			
	        <!-- #main Starts -->
	        <?php woo_main_before(); ?>
	        <div id="main" class="col-left">
	    <?php
	}
}

if (!function_exists('pixelpress_after_content')) {
	function pixelpress_after_content() {
	?>
			<?php if ( is_search() && is_post_type_archive() ) { add_filter( 'woo_pagination_args', 'woocommerceframework_add_search_fragment', 10 ); } ?>
			<?php woo_pagenav(); ?>
			</div><!-- /#main -->
	        <?php pixelpress_after_wc_content(); ?>
			</div>
	    </div><!-- /#content -->
		<?php woo_content_after(); ?>
	    <?php
	}
}

// Hook the sidebar into the Woo Layout
add_action('pixelpress_after_wc_content','woocommerce_get_sidebar');

// Remove breadcrumb (we're using the WooFramework default breadcrumb)
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
/*
add_action( 'woo_content_before', 'woocommerceframework_breadcrumb', 01, 0);

if (!function_exists('woocommerceframework_breadcrumb')) {
	function woocommerceframework_breadcrumb() {
		global  $woo_options;
		if ( $woo_options[ 'woo_breadcrumbs_show' ] == 'true' ) {
			woo_breadcrumbs();
		}
	}
}
*/
// Remove pagination (we're using the WooFramework default pagination)
remove_action( 'woocommerce_pagination', 'woocommerce_pagination', 10 );

function woocommerceframework_pagination() {
	if ( is_search() && is_post_type_archive() ) {
		add_filter( 'woo_pagination_args', 'woocommerceframework_add_search_fragment', 10 );
	}
	woo_pagenav();
}

function woocommerceframework_add_search_fragment ( $settings ) {
	$settings['add_fragment'] = '&post_type=product';
	return $settings;
} // End woocommerceframework_add_search_fragment()

// Remove the default description by redefining woocommerce_archive_product_content()
function woocommerce_archive_product_content() {
	woocommerce_get_template_part( 'loop', 'shop'  );
	do_action( 'woocommerce_pagination' );
}


// Add the shop description in the right place with the right markup
add_action('woo_main_before', 'pixelpress_shop_intro');
function pixelpress_shop_intro() {
	if (is_shop() || is_product_category() || is_product()) {
			
			if ( ! is_search() ) {
			$shop_page = get_post( woocommerce_get_page_id( 'shop' ) );
			$shop_page_title = apply_filters( 'the_title', ( get_option( 'woocommerce_shop_page_title' ) ) ? get_option( 'woocommerce_shop_page_title' ) : $shop_page->post_title );
			if ( is_object( $shop_page  ) )
				$shop_page_content = $shop_page->post_content;
		} else {
			$shop_page_title = __( 'Search Results:', 'woocommerce' ) . ' &ldquo;' . get_search_query() . '&rdquo;';
			if ( get_query_var( 'paged' ) ) $shop_page_title .= ' &mdash; ' . __( 'Page', 'woocommerce' ) . ' ' . get_query_var( 'paged' );
			$shop_page_content = '';
		}

		?>
		<section class="section-title">
		<h1><?php echo $shop_page_title ?></h1>

		<?php if ( ! empty( $shop_page_content  ) ) echo '<p><span>'; echo strip_tags(apply_filters( 'the_content', $shop_page_content )); echo '</span></p>'; ?>
		</section>
		
		<?php
	}
}

// Change columns in related products output to 3 and move below the product summary 
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
add_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 20);

if (!function_exists('woocommerce_output_related_products')) {
	function woocommerce_output_related_products() {
	    woocommerce_related_products(3,3); // 3 products, 3 columns
	}
}

// Change columns in upsells output to 3 and move below the product summary
remove_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display');
add_action( 'woocommerce_after_single_product', 'woocommerceframework_upsell_display', 20);
 
if (!function_exists('woocommerceframework_upsell_display')) {
	function woocommerceframework_upsell_display() {
	    woocommerce_upsell_display(3,3); // 3 products, 3 columns
	}
}

// Adjust the star rating in the sidebar
add_filter('woocommerce_star_rating_size_sidebar', 'woostore_star_sidebar');

if (!function_exists('woostore_star_sidebar')) {
	function woostore_star_sidebar() {
		return 12;
	}
}

// Adjust the star rating in the recent reviews
add_filter('woocommerce_star_rating_size_recent_reviews', 'woostore_star_reviews');

if (!function_exists('woostore_star_reviews')) {
	function woostore_star_reviews() {
		return 12;
	}
}

// Sticky shortcode
function woo_shortcode_sticky( $atts, $content = null ) {
   extract( shortcode_atts( array(
      'class' => '',
      ), $atts ) );
 
   return '<div class="shortcode-sticky ' . esc_attr($class) . '">' . $content . '</div><!--/shortcode-sticky-->';
}

add_shortcode( 'sticky', 'woo_shortcode_sticky' );

// Sale shortcode
function woo_shortcode_sale ( $atts, $content = null ) {
	$defaults = array();
	extract( shortcode_atts( $defaults, $atts ) );
	return '<div class="shortcode-sale"><span>' . $content . '</span></div><!--/.shortcode-sale-->';
}

add_shortcode( 'sale', 'woo_shortcode_sale' );

// Add image wrap
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_product_thumbnail_wrap_open', 5, 2);

if (!function_exists('woocommerce_product_thumbnail_wrap_open')) {
	function woocommerce_product_thumbnail_wrap_open() {
		echo '<div class="img-wrap">';
	}
}

// Close image wrap
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_product_thumbnail_wrap_close', 15, 2);

if (!function_exists('woocommerce_product_thumbnail_wrap_close')) {
	function woocommerce_product_thumbnail_wrap_close() {
		echo '</div> <!--/.wrap-->';
	}
}

// Handle cart in header fragment for ajax add to cart
add_filter('add_to_cart_fragments', 'woocommerce_cart_link');

if (!function_exists('woocommerce_cart_link')) {
	function woocommerce_cart_link( $fragments ) {
		global $woocommerce;
		
		ob_start();
		
		?>
		<ul class="mini-cart">
			<li>
				<a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'woothemes'); ?>" class="cart-parent">
					<span> 
				<?php 
				echo $woocommerce->cart->get_cart_total();;
				echo sprintf(_n('<mark>%d</mark>', '<mark>%d</mark>', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count);
				?>
				</span>
				</a>
				<?php
		
		        echo '<ul class="cart_list">';
		           if (sizeof($woocommerce->cart->cart_contents)>0) : foreach ($woocommerce->cart->cart_contents as $cart_item_key => $cart_item) :
			           $_product = $cart_item['data'];
			           if ($_product->exists() && $cart_item['quantity']>0) :
			               echo '<li class="cart_list_product"><a href="'.get_permalink($cart_item['product_id']).'">';
			               
			               echo $_product->get_image();
			               
			               echo '</a>';
			               
			               if($_product instanceof woocommerce_product_variation && is_array($cart_item['variation'])) :
			                   echo woocommerce_get_formatted_variation( $cart_item['variation'] );
			                 endif;
			               
			               echo '<span class="details quantity"><a href="'.get_permalink($cart_item['product_id']).'">'.apply_filters('woocommerce_cart_widget_product_title', $_product->get_title(), $_product).'</a>' .$cart_item['quantity'].' &times; '.woocommerce_price($_product->get_price()).'</span></li>';
			           endif;
			       endforeach;
		
		        	else: echo '<li class="empty">'.__('No products in the cart.','woothemes').'</li>'; endif;
		        	if (sizeof($woocommerce->cart->cart_contents)>0) :
		            echo '<li class="total"><strong>';
		
		            if (get_option('js_prices_include_tax')=='yes') :
		                _e('Total', 'woothemes');
		            else :
		                _e('Subtotal', 'woothemes');
		            endif;
							
						
							
		            echo ':</strong>'.$woocommerce->cart->get_cart_total();'</li>';
		
		            echo '<li class="buttons"><a href="'.$woocommerce->cart->get_cart_url().'" class="button">'.__('View Cart &rarr;','woothemes').'</a> <a href="'.$woocommerce->cart->get_checkout_url().'" class="button checkout">'.__('Checkout &rarr;','woothemes').'</a></li>';
		        endif;
		        
		        echo '</ul>';
		
		    ?>
			</li>
		</ul>
		<?php
		
		$fragments['ul.mini-cart'] = ob_get_clean();
		
		return $fragments;
		
	}
}

function pixelpress_order_by_rating_post_clauses( $args ) {
		
    global $wpdb;
    
    $args['where'] .= " AND $wpdb->commentmeta.meta_key = 'rating' ";
    
    $args['join'] = "
    	LEFT JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID)
    	LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
    ";

    $args['orderby'] = "$wpdb->commentmeta.meta_value DESC";
    
    $args['groupby'] = "$wpdb->posts.ID";
    
    return $args;
}

?>