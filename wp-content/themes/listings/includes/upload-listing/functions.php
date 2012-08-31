<?php

	add_action( 'admin_head', 'woo_listings_inside_popup' );
	
	
	if ( ! function_exists( 'woo_listings_inside_popup' ) ) {
	
		function woo_listings_inside_popup () {
		
			echo 'test';
		
			add_filter( 'media_upload_tabs', 'woo_listings_mlu_tabs', 1, 2 );
		
		} // End woo_listings_inside_popup()
	
	} // End IF Statement
	

	if ( ! function_exists( 'woo_listings_mlu_tabs' ) ) {
	
		function woo_listings_mlu_tabs ( $tabs ) {
		
			unset( $tabs['library'] );
		
			return $tabs;
		
		} // End woo_listings_mlu_tabs()
	
	} // End IF Statement

?>