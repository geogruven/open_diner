<?php
	header("Content-Type:text/css");
	
	// Get the path to the root.
	$full_path = __FILE__;
	
	$path_bits = explode( 'wp-content', $full_path );
	
	$url = $path_bits[0];
	
	// Require WordPress bootstrap.
	require_once( $url . '/wp-load.php' );
	
	$woo_options = get_option( 'woo_options' );
	
	$css = '';
?>

#login h1 { display :none; }

<?php
	if ( $woo_options['woo_logo'] == '' ) {} else {
	
		// Dynamically get the logo image dimensions.
		
		$dimensions = getimagesize( $woo_options['woo_logo'] );
		
		$css .= '#login h1 a { background: url(' . $woo_options['woo_logo'] . ') no-repeat center top; height: ' . $dimensions[1] . 'px; }' . "\n";
	
	} // End IF Statement
	
	// Clean up unnecessary elements if in the registration popup.
	
	if ( isset( $_REQUEST['is_woothemes_register'] ) && $_REQUEST['is_woothemes_register'] == 'yes' ) {
	
		// $css .= 'body { padding-top: 0px; }' . "\n";
		
		$css .= '#login { margin-top: 0px; }' . "\n";
	
		$css .= '#backtoblog { display: none; }' . "\n";
	
	} // End IF Statement
	
	$css = apply_filters( 'woo_login_css', $css );
	
	echo $css;
?>