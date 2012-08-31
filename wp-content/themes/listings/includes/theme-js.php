<?php
if (!is_admin()) add_action( 'wp_print_scripts', 'woothemes_add_javascript' );
if (!function_exists('woothemes_add_javascript')) {
	function woothemes_add_javascript( ) {
		wp_enqueue_script('jquery');    
		wp_enqueue_script( 'superfish', get_bloginfo('template_directory').'/includes/js/superfish.js', array( 'jquery' ) );
		wp_enqueue_script( 'slidesJS', get_bloginfo('template_directory') . '/includes/js/slides.min.jquery.js', array( 'jquery' ) );
		wp_enqueue_script('thickbox');
		wp_enqueue_script( 'general', get_bloginfo('template_directory').'/includes/js/general.js', array( 'jquery' ) );
		wp_register_script('woo-autocomplete', get_bloginfo('template_directory').'/functions/js/jquery.autocomplete.js', array( 'jquery' ));
		wp_enqueue_script('woo-autocomplete');
		wp_enqueue_script('carousel', get_bloginfo('template_directory').'/includes/js/jquery.jcarousel.min.js', array( 'jquery' ));
	}
}
?>