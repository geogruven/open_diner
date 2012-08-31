<?php
// Load the required JavaScripts for the "Upload a listing" template page.

if ( ! is_admin() ) { add_action( 'wp_print_scripts', 'woo_listings_upload_js' ); } // End IF Statement

if ( ! function_exists( 'woo_listings_upload_js' ) ) {

	function woo_listings_upload_js () {
		
		$_required = array( 'jquery', 'listings-upload-datepicker', 'listings-upload-maskedinput', 'listings-upload-uploader', 'listings-upload-validate' );
		
		wp_enqueue_script( 'listings-upload-datepicker', get_template_directory_uri().'/functions/js/ui.datepicker.js', array( 'jquery', 'jquery-ui-core' ) );
		wp_enqueue_script( 'listings-upload-maskedinput', get_template_directory_uri().'/functions/js/jquery.maskedinput-1.2.2.js', array( 'jquery' ) );
		wp_enqueue_script( 'listings-upload-uploader', get_template_directory_uri().'/includes/upload-listing/js/listings-medialibrary-uploader.php', array( 'jquery', 'thickbox' ) );
		wp_enqueue_script( 'listings-upload-validate', get_template_directory_uri().'/includes/content-builder/js/jquery-validate/jquery.validate.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'listings-upload-functions', get_template_directory_uri().'/includes/upload-listing/js/js_code.php', $_required );
	
	} // End woo_listings_upload_js()

} // End IF Statement

if ( ! is_admin() ) { add_action( 'wp_print_styles', 'woo_listings_upload_css', null, 2 ); } // End IF Statement

if ( ! function_exists( 'woo_listings_upload_css' ) ) {

	function woo_listings_upload_css () {
		
		// jQuery UI CSS.
		wp_enqueue_style( 'listings-upload-jqueryui-theme', get_template_directory_uri() . '/functions/css/jquery-ui-datepicker.css', array(), '1.8.4', 'screen' );
	
	} // End woo_listings_upload_css()

} // End IF Statement
?>