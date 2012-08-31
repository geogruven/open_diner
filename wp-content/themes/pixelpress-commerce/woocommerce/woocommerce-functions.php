<?php

// WooCommerce overrides etc

function pixelpress_after_wc_content() {
    do_action('pixelpress_after_wc_content');
}

// Change columns in product loop to 4
function loop_columns() {
	return 3;
}
add_filter('loop_shop_columns', 'loop_columns');

// Display 16 products per page
add_filter('loop_shop_per_page', create_function('$cols', 'return 9;'));

// Move the catalog ordering
remove_action( 'woocommerce_pagination', 'woocommerce_catalog_ordering', 20 );
add_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 20 );