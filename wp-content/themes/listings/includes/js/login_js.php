<?php
	header("Content-Type:text/javascript");
	
	$url = urldecode( $_GET['url'] );
	
	// Require WordPress bootstrap.
	require_once( $url . '/wp-load.php' );
	
	$woo_options = get_option( 'woo_options' );
?>
<?php
	// Clean up unnecessary elements if in the registration popup.
	
	if ( isset( $_REQUEST['is_woothemes_register'] ) && $_REQUEST['is_woothemes_register'] == 'yes' ) {
	
	wp_enqueue_script( 'jquery' );
?>
jQuery(document).ready( function() {

	jQuery('form#registerform input[name="wp-submit"]').appendTo('<input type="hidden" name="is_woothemes_register" value="yes" />');

});
<?php
	} // End IF Statement
?>