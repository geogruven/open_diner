<?php

// Check WooCommerce is installed first
add_action('wp_head', 'checked_environment');

function checked_environment() {
	if (!class_exists('woocommerce')) wp_die('WooCommerce must be installed'); 
}

// Disable WooCommerce styles 
define('WOOCOMMERCE_USE_CSS', false);


?>