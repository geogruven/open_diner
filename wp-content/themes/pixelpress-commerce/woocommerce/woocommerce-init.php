<?php

// Check WooCommerce is installed first
add_action('plugins_loaded', 'checked_environment');

function checked_environment() {
	if (!class_exists('woocommerce')) wp_die('WooCommerce must be installed'); 
}

// Redefine woo_load_frontend_css
function woo_load_frontend_css () {
	wp_register_style( 'woo-layout', get_template_directory_uri() . '/css/layout.css' );
	wp_register_style( 'woo-layout-child', get_stylesheet_directory_uri() . '/woocommerce/css/style.css' );
	wp_register_style( 'woo-custom-child', get_stylesheet_directory_uri() . '/custom.css' );
	
	wp_enqueue_style( 'woo-layout' );
	wp_enqueue_style( 'woo-layout-child' );
	wp_enqueue_style( 'woo-custom-child' );
} // End woo_load_frontend_css()

// Set up child theme lang
load_child_theme_textdomain('woothemes', dirname(__FILE__).'/lang');

$locale = get_locale();
$locale_file = get_stylesheet_directory_uri()."/lang/$locale.php";
if ( is_readable($locale_file) )
require_once($locale_file);

// Add more options to theme options panel
function woo_options_add($options) {
	
	// New Theme Options for Shop Area
	$shortname = 'woo';
	$other_entries = array( "Select a number:","4","8","12","16","20" );
	
	$new_options[] = array( "name" => "Shop Area",
					"type" => "subheading" );
					
	$new_options[] = array( "name" => "Enable Shop Area",
	                    "desc" => "Enable the shop area on the homepage.",
	                    "id" => $shortname."_shop_area",
	                    "std" => "false",
	                    "class" => "collapsed",
	                    "type" => "checkbox");			
	
	$new_options[] = array( "name" => "Number of Products",
	                    "desc" => "Select the number of products that should appear in the shop area on the home page.",
	                    "id" => $shortname."_shop_area_entries",
	                    "std" => "8",
	                    "class" => "hidden",
	                    "type" => "select",
	                    "options" => $other_entries);
	                    
	$new_options[] = array( "name" => "Shop Area Title Text",
						"desc" => "Enter the title for the shop area to be displayed on your homepage.",
						"id" => $shortname."_shop_area_title",
						"std" => "Latest store additions",
						"class" => "hidden",
						"type" => "text" );
											
	$new_options[] = array( "name" => "Shop Area Message",
	                    "desc" => "Enter the message for the shop area to be displayed on your homepage.",
	                    "id" => $shortname."_shop_area_message",
	                    "std" => 'Cras adipiscing pellentesque feugiat. Curabitur posuere tellus nulla, ac fringilla erat.',
	                    "class" => "hidden",
	                    "type" => "textarea" );
	

	// Loop through existing options
	foreach ( $options as $key => $option ) {
		// Look for id = woo_blog_area_page	
		if ( isset( $option['id'] ) && $option['id'] == 'woo_blog_area_page' ) {
			// Add the new theme options afterwards
			array_splice($options, $key + 1, 0, $new_options);
			break;
		} // End If Statement
	} // End For Loop
	
	// Return new options
	return $options;
}

?>