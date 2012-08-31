<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*-----------------------------------------------------------------------------------*/
/* WooThemes Admin Interface - woothemes_add_admin */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woothemes_add_admin' ) ) {
	
    function woothemes_add_admin() {
        
        $themename = 'Restaurant Maanager'; //  get_option( 'woo_themename' ); 
        
        // Check all the Options, then if the no options are created for a relative sub-page... it's not created.
        if( get_option( 'framework_woo_backend_icon' ) ) { 
            $icon = get_option( 'framework_woo_backend_icon' );
        } else { 
            $icon = get_stylesheet_directory_uri() . '/functions/images/woo-icon.png';   // get_template_directory_uri()
        }
/*
        if( function_exists( 'add_object_page' ) ) {
                add_object_page ( 'Page Title', $themename, 'manage_options', 'geothemes', 'geothemes_options_page', $icon );
        } else {
                add_menu_page ( 'Page Title', $themename, 'manage_options', 'geothemes_home', 'geothemes_options_page', $icon );
        }
 */   
    }
}  
    add_action( 'admin_menu', 'woothemes_add_admin', 10 );
    
/*-----------------------------------------------------------------------------------*/
/* Framework options panel - woothemes_options_page */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'geothemes_options_page' ) ) {
	function geothemes_options_page() {

		$options =  get_option( 'woo_template' );
		$themename =  get_option( 'woo_themename' );
		$shortname =  get_option( 'woo_shortname' );
		$manualurl =  get_option( 'woo_manual' );

		//Framework Version in Backend Header
		$woo_framework_version = get_option( 'woo_framework_version' );

		//Version in Backend Header
		$theme_data = get_theme_data( get_template_directory() . '/style.css' );
		$local_version = $theme_data['Version'];

		//GET themes update RSS feed and do magic
		include_once( ABSPATH . WPINC . '/feed.php' );

		$pos = strpos( $manualurl, 'documentation' );
		$theme_slug = str_replace( "/", "", substr( $manualurl, ( $pos + 13 ) ) ); //13 for the word documentation

		//add filter to make the rss read cache clear every 4 hours
		//add_filter( 'wp_feed_cache_transient_lifetime', create_function( '$a', 'return 14400;' ) );

		global $pagenow;
?>
<div class="wrap" id="geo_container">


</div><!--wrap-->

 <?php
	} // End geothemes_options_page()
}
    
?>
