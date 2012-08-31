<?php	
	/* Taxonomies.
	--------------------------------------------------------------------------------*/
	
	$post_type = $_POST['woo_post_type'];
	
	$_html = '';
	
	$custom_taxonomies = get_option("woo_content_builder_ctx");
	
	$ctx_formatted = array();
		
	if ( count( $custom_taxonomies ) ) {
	
		foreach ( $custom_taxonomies as $ctx ) {
		
			$ctx_formatted[$ctx['name']] = array( 'label' => $ctx['args']['label'], 'plural' => $ctx['args']['labels']['name'] );
			
			if ( count( $ctx['object_type'] ) ) {
			
				$ctx_formatted[$ctx['name']]['objects'] = join( ',', $ctx['object_type'] );
			
			} // End IF Statement
		
		} // End FOREACH Loop
	
	} // End IF Statement
	
	if ( count( $ctx_formatted ) ) {
	
		foreach ( $ctx_formatted as $k => $v ) {
		
			// Create an array of objects, if necessary.
			$objects = explode( ',', $v['objects'] );
		
			if ( in_array( $post_type, $objects ) ) {} else {
			
				unset( $ctx_formatted[$k] );
			
			} // End IF Statement
		
		} // End FOREACH Loop
			
	} // End IF Statement
	
	if ( count( $ctx_formatted ) ) {
	
		foreach ( $ctx_formatted as $ctx => $data ) {
		
			// Get the data for all the terms.
			$_terms_args = array( 'hide_empty' => false );
			$_terms = get_terms( $ctx, $_terms_args );
			
			if ( count( $_terms ) ) {
		
				$_html .= '<p class="form_row custom_taxonomy_listing custom_taxonomy_' . $ctx . '" rel="' . $data['objects'] . '">' . "\n";
			
				$_html .= '<label for="custom_taxonomy_' . $ctx . '">' . __( 'Please select from the following ' . strtolower( $data['plural'] ), 'woothemes' ) . '</label>';
				
				/*
				$_html .= '<ul id="' . $ctx . 'checklist" class="list:' . $ctx . ' categorychecklist form-no-clear">' . "\n";
				
				$_html .= wp_terms_checklist( 0, array( 'taxonomy' => $ctx ) );
			
				$_html .= '</ul>' . "\n";
				*/
				
				$_html .= '<select name="custom_taxonomy_' . $ctx . '[]" multiple="multiple">' . "\n";
				
				foreach ( $_terms as $t => $p ) {
				
					$_html .= '<option value="' . $p->term_id . '">' . $p->name . '</option>' . "\n";
				
				} // End FOREACH Loop
				
				$_html .= '</select>' . "\n";
				
				$_html .= '</p>' . "\n";
			
			} // End IF Statement
		
		} // End FOREACH Loop
	
	} // End IF Statement
	
	// If there's nothing to display, don't display the fieldset.
	
	if ( $_html == '' ) {} else {
	
	/*------------------------------------------------------------------------------*/
?>
<fieldset id="custom-taxonomies" class="custom-taxonomies">
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