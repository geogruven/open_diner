<?php
// Remove WC sidebar
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

// WooCommerce layout overrides
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

add_action( 'woocommerce_before_main_content', 'woocommerce_canvas_before_content', 10 );
add_action( 'woocommerce_after_main_content', 'woocommerce_canvas_after_content', 20 );

function woocommerce_canvas_before_content() {
?>
	<!-- #content Starts -->
	<?php woo_content_before(); ?>
    <div id="content" class="col-full">

    	<div id="main-sidebar-container">

            <!-- #main Starts -->
            <?php woo_main_before(); ?>
            <div id="main" class="col-left">
    <?php
}
function woocommerce_canvas_after_content() {
?>
			</div><!-- /#main -->
            <?php woo_main_after(); ?>

		</div><!-- /#main-sidebar-container -->

		<?php get_sidebar( 'alt' ); ?>

    </div><!-- /#content -->
	<?php woo_content_after(); ?>
    <?php
}

// Add the WC sidebar in the right place
add_action( 'woo_main_after', 'woocommerce_get_sidebar', 10 );

// Remove breadcrumb (we're using the WooFramework default breadcrumb)
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
add_action( 'woocommerce_before_main_content', 'woocommerceframework_breadcrumb', 20, 0 );

function woocommerceframework_breadcrumb() {
	global  $woo_options;
	if ( $woo_options['woo_breadcrumbs_show'] == 'true' ) {
		woo_breadcrumbs();
	}
}

add_action( 'woocommerce_after_main_content', 'canvas_commerce_pagination', 01, 0 );

function canvas_commerce_pagination() {
	if ( is_search() && is_post_type_archive() ) {
		add_filter( 'woo_pagination_args', 'woocommerceframework_add_search_fragment', 10 );
	}
	woo_pagenav();
}

function woocommerceframework_add_search_fragment ( $settings ) {
	$settings['add_fragment'] = '&post_type=product';
	return $settings;
} // End woocommerceframework_add_search_fragment()

// Change columns in related products output to 3
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

function woocommerce_output_related_products() {
	woocommerce_related_products( 3, 3 ); // 3 products, 3 columns
}

// Change columns in product loop to 3
function loop_columns() {
	return 3;
}

add_filter( 'loop_shop_columns', 'loop_columns' );

// Remove pagination - we're using WF pagination.
remove_action( 'woocommerce_pagination', 'woocommerce_pagination', 10 );

// Display 12 products per page
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 12;' ) );

// Fix sidebar on shop page
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

// Adjust the star rating in the sidebar
add_filter( 'woocommerce_star_rating_size_sidebar', 'woostore_star_sidebar' );

function woostore_star_sidebar() {
	return 12;
}

// Adjust the star rating in the recent reviews
add_filter( 'woocommerce_star_rating_size_recent_reviews', 'woostore_star_reviews' );

function woostore_star_reviews() {
	return 12;
}
?>