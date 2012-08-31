<?php
	/* Custom fields.
	--------------------------------------------------------------------------------*/
	
	$post_type = $_POST['woo_post_type'];
	
	$_html = '';
	
	$custom_fields = get_option("woo_custom_template");
	
	$cf_formatted = array();
	
	// Strip out `image` fields into a separate array.
	$cf_images = array();
	
	if ( is_array( $custom_fields ) && count( $custom_fields ) ) {
	
		foreach ( $custom_fields as $c ) {
		
			if ( array_key_exists( 'cpt', $c ) && is_array( $c['cpt'] ) && in_array( $post_type, array_keys( $c['cpt'] ) ) ) {
			
				// Ignore `googlemap` fields, and place `upload` fields in a separate array.
			
				switch ( $c['type'] ) {
				
					case 'googlemap' :
					
					break;
					
					case 'info' :
					
					break;
					
					case 'upload' :
					
					$c['id'] = $c['name'];
					$c['name'] = $c['label'];
					
					$cf_images[] = $c;
					
					break;
					
					default :
					
					$c['id'] = $c['name'];
					$c['name'] = $c['label'];
					
					$cf_formatted[] = $c;
					
					break;
				
				} // End SWITCH Statement
			
			} // End IF Statement
		
		} // End FOREACH Loop
	
		$return = woothemes_machine( $cf_formatted );
		
		$_html .= stripslashes( $return[0] );
	
	} // End IF Statement
	
	// echo '<xmp>'; print_r($return); echo '</xmp>'; // DEBUG
	
	// If there's nothing to display, don't display the fieldset.
	
	if ( $_html == '' ) {} else {
	
	/*------------------------------------------------------------------------------*/
?>
<fieldset id="custom-fields" class="custom-fields">
	<div class="form_row">
<?php
	// And finally, display the data.
	
	echo $_html;
?>
	</div><!--/.form_row-->
</fieldset>
<?php
	} // End IF Statement ( $_html )
?>