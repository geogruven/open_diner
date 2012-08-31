<?php
/*
Plugin Name: WooTable
Plugin URI: http://woothemes.com/
Description: A Woo-driven restaurant reservations management system.
Version: 0.0.3.2
Author: Matty
Author URI: http://matty.co.za/
*/
?>
<?php
/*  Copyright 2010  Matty  (email : nothanks@idontwantspam.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php
	require_once( 'classes/posttype.class.php' );
	require_once( 'classes/taxonomy.class.php' );
	require_once( 'classes/utilities.class.php' );
	require_once( 'classes/manager.class.php' );
	require_once( 'classes/frontend.class.php' );
	require_once( 'classes/factory.class.php' );
	
	require_once( 'widgets/make-reservation.widget.php' );
	require_once( 'widgets/business-hours.widget.php' );
	
	require_once( 'classes/wootable.class.php' );
	
	/*$wootable = new WooTable( dirname( __FILE__ ), trailingslashit( WP_PLUGIN_URL ) . plugin_basename( dirname( __FILE__ ) ) );
	
	register_activation_hook( __FILE__, array( &$wootable, 'activate' ) );*/
	
	/*---For using WooTable as part of a WordPress theme----------*/
	
	/*
		For using WooTable as part of a WordPress theme, replace
		the following lines above with the code below (be sure to
		comment out the above two lines for future use.
		
		Replace:
		
		$wootable = new WooTable( dirname( __FILE__ ), trailingslashit( WP_PLUGIN_URL ) . plugin_basename( dirname( __FILE__ ) ) );

		register_activation_hook( __FILE__, array( &$wootable, 'activate' ) );
		
		Also, remove the line at the top of this file that reads:
		"Plugin Name: WooTable". From there, simply place the "woo-table"
		folder in your theme folder and add the following line to functions.php:
		
		require_once( 'woo-table/woo-table.php' );
		
		That's all, folks!
	*/
	
		$wootable = new WooTable( dirname( __FILE__ ), trailingslashit( get_template_directory_uri() ) . basename( dirname( __FILE__ ) ) );
	
		global $pagenow;
		if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
		
			$wootable->activate();
			
		} // End IF Statement
	
	/*------------------------------------------------------------*/
?>