<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: Custom fields for taxonomies in the Content Builder.
Date Created: 2011-01-11.
Author: Matty.
Since: 1.1.0


TABLE OF CONTENTS

- var $token

- var $db_tablename
- var $db_metatype

- var $fields
- var $taxonomies
- var $taxonomy_fields

- function Woo_ContentBuilder_TaxonomyFields (constructor)
- function init ()
- function activate ()
- function create_metadata_table ()
- function register_table ()
- function form_fields_add ()
- function form_fields_edit ()
- function meta_data_add ()
- function meta_data_edit ()
- function meta_data_delete ()
- function register_form_fields ()
- function get_field_settings ()
- function setup_meta_for_term ()
- function enqueue_scripts ()
- function enqueue_styles ()

-----------------------------------------------------------------------------------*/
/*-----------------------------------------------------------------------------------

USING THIS CODE WITHIN YOUR TEMPLATE

// Custom taxonomy, category and tag archive page templates:
------------------------------------------------------------

On a custom taxonomy, a category or a tag archive, your template has access to
$woo_term_meta, which is an array of all the custom field data available for that term.

Example:

echo $woo_term_meta['image'];

// In another implementation:
------------------------------------------------------------

To access the meta data for a specific term, use the following:

$term_meta = get_metadata( 'woo_term', $term_id );

($term_id is the ID of the term you're looking to get the data for.)

From there, the above snippet applies again:

echo $term_meta['image'];

-----------------------------------------------------------------------------------*/

	class Woo_ContentBuilder_TaxonomyFields {
	
		/*----------------------------------------
	 	  WooTable()
	 	  ----------------------------------------
	 	  
	 	  * Constructor function.
	 	  * Sets up the class and registers
	 	  * variable action hooks.
	 	----------------------------------------*/
	 
	 	function Woo_ContentBuilder_TaxonomyFields () {
	 		
	 		$this->token = 'term';
	 		
	 		$this->db_tablename = 'woo_term_meta';
	 		$this->db_metatype = 'term';
	 		
	 		$this->fields = array();
	 		$this->taxonomies = array();
	 		$this->taxonomy_fields = array();
	 		
	 		// add_action( 'init', array( &$this, 'init' ), 99 );
	 		
	 		$this->init();
	 		
	 	} // End Woo_ContentBuilder_TaxonomyFields()
	 	
	 	/*----------------------------------------
	 	  init()
	 	  ----------------------------------------
	 	  
	 	  * This guy runs the show.
	 	  * Rocket boosters... engage!
	 	----------------------------------------*/
	 	
	 	function init () {
	 		
	 		if ( is_admin() ) {
	 		
	 			global $pagenow;
	 		
		 		$this->activate();
		 		
		 		// Load the JavaScript and CSS only on the taxonomy screens.
		 		if ( $pagenow === 'edit-tags.php' ) {
		 		
		 			add_action( 'admin_print_scripts', array( &$this, 'enqueue_scripts' ) );
		 			add_action( 'admin_print_styles', array( &$this, 'enqueue_styles' ) );
		 		
		 		} // End IF Statement
	 		
	 		} // End IF Statement
	 		
	 		$this->register_table( $this->db_tablename, $this->db_metatype );
	 		
	 		$this->register_form_fields();
	 		
	 		// Setup the meta for the term being viewed on the frontend.
	 		add_action( 'get_header', array( &$this, 'setup_meta_for_term' ) );
	 		
	 	} // End init()
	 	
	 	/*----------------------------------------
	 	  Utility Functions
	 	  ----------------------------------------
	 	  
	 	  * These functions are used within this
	 	  * class as helpers for other functions.
	 	----------------------------------------*/
	 	
	 	/*----------------------------------------
	 	  activate()
	 	  ----------------------------------------
	 	  
	 	  * Perform actions when the plugin is
	 	  * activated. In this case, we call the
	 	  * create_metadata_table () function.
	 	----------------------------------------*/
	 	
	 	function activate () {
	 	
	 		global $wpdb;
	 	
	 		$this->create_metadata_table( $this->db_tablename, $this->db_metatype );
	 		
	 	} // End activate()
	 	
	 	/*----------------------------------------
	 	  create_metadata_table()
	 	  ----------------------------------------
	 	  
	 	  * Create a new database table, if
	 	  * none already exists, to hold the
	 	  * metadata for our `tables` taxonomy.
	 	  
	 	  * Params:
	 	  * - String $table_name
	 	  * - String $type
	 	----------------------------------------*/
	 	
	 	function create_metadata_table ( $table_name, $type ) {
			
			global $wpdb;
			
			$table_name = $wpdb->prefix . $table_name;
			
			if (!empty ($wpdb->charset))
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
			if (!empty ($wpdb->collate))
			$charset_collate .= " COLLATE {$wpdb->collate}";
			
			$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			meta_id bigint(20) NOT NULL AUTO_INCREMENT,
			woo_{$type}_id bigint(20) NOT NULL default 0,
			
			meta_key varchar(255) DEFAULT NULL,
			meta_value longtext DEFAULT NULL,
			
			UNIQUE KEY meta_id (meta_id)
			) {$charset_collate};";
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			
		} // End create_metadata_table()
		
		/*----------------------------------------
	 	  register_table()
	 	  ----------------------------------------
	 	  
	 	  * Register our newly created custom
	 	  * database table, such that we can
	 	  * access it throughout the system.
	 	----------------------------------------*/
		
		function register_table () {
		
			global $wpdb;
			
			$variable_name = 'woo_' . $this->db_metatype . 'meta';
			$wpdb->$variable_name = $wpdb->prefix . $this->db_tablename;
			
			if ( ! in_array( $this->db_tablename, $wpdb->tables ) ) {
			
				$wpdb->tables[] = $this->db_tablename;
			
			} // End IF Statement
			
		} // End register_table()
		
		/*----------------------------------------
	 	  form_fields_add()
	 	  ----------------------------------------
	 	  
	 	  * Add custom form fields to the `add`
	 	  * screen of our custom taxonomy.
	 	----------------------------------------*/
		
		function form_fields_add () {
	 		
	 		global $taxonomy, $tax;
	 		
	 		if ( count( $this->taxonomy_fields ) && array_key_exists( $taxonomy, $this->taxonomy_fields ) ) {
	 		
	 			foreach ( $this->taxonomy_fields[$taxonomy] as $f ) {
	 			
	 			$_value = '';
	 			
	 			// If no value is present, set it to the standard value.
 				if ( array_key_exists( 'std', $f ) && $f['std'] != '' ) {
 				
 					$_value = $f['std'];
 				
 				} // End IF Statement
 				
 				/* Begin field output.
 				----------------------------------------*/
 				
 					switch ( $f['type'] ) {
 				
 				/* - Text Area.
 				----------------------------------------*/
 						
 						case 'textarea':
 						
?>	 		
		<div class="form-field">  
			<label for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></label>  
			<textarea name="<?php echo $f['name']; ?>" id="<?php echo $f['name']; ?>" rows="5"><?php echo $_value; ?></textarea>  
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p><?php _e( sprintf($f['desc']),'woothemes' ); ?></p><?php } // End IF Statement ?>
		</div>
<?php
 						
 						break;
 						
 				/* - Select Box.
 				----------------------------------------*/
 				
 						case 'select2':
 						
 							if ( count( $f['options'] ) ) {
 						
?>	 		
		<div class="form-field">  
			<label for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></label>  
			<select name="<?php echo $f['name']; ?>" id="<?php echo $f['name']; ?>">
				<?php
					$html = '';
					
					foreach ( $f['options'] as $o ) {
					
						$_selected = '';
						if ( $o == $_value ) { $_selected = ' selected="selected"'; } // End IF Statement
						
						$html .= '<option value="' . $o . '"' . $_selected . '>' . $o . '</option>' . "\n";
					
					} // End FOREACH Loop
					
					echo $html;
				?>
			</select>  
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p><?php _e( sprintf($f['desc']),'woothemes' ); ?></p><?php } // End IF Statement ?>
		</div>
<?php
 						
 							} // End IF Statement
 						
 						break;
 				
 				/* - Checkbox.
 				----------------------------------------*/
 				
 						case 'checkbox':
 						
?>	 		
		<div class="form-field">  
			<label for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></label>  
			<input type="checkbox" name="<?php echo $f['name']; ?>" id="<?php echo $f['name']; ?>" value="true" class="alignleft" style="width: auto; margin-right: 10px;" /><span class="alignleft"><?php echo $f['std']; ?></span>
			<br class="clear" />
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p><?php _e( sprintf($f['desc']),'woothemes' ); ?></p><?php } // End IF Statement ?>
		</div>
<?php
 						
 						break;
 				
 				/* - File Upload.
 				----------------------------------------*/
 				
 						case 'upload':
 						
?>	 		
		<div class="form-field upload-field">  
			<label for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></label>
			<?php 
				if ( function_exists( 'woothemes_medialibrary_uploader' ) ) {
					
					echo woothemes_medialibrary_uploader( $f['name'], $f['std'], null ); // New AJAX Uploader using Media Library
				
				} // End IF Statement
			?>
			<?php /*<input type='text' name="<?php echo $f['name']; ?>" id="<?php echo $f['name']; ?>" value="<?php echo $_value; ?>" />*/ ?>
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p><?php _e( sprintf($f['desc']),'woothemes' ); ?></p><?php } // End IF Statement ?>
		</div>
<?php
 						
 						break;
 				
 				/* - Radio Buttons.
 				----------------------------------------*/
 				
 						case 'radio':
 						
 							if ( count( $f['options'] ) ) {
 						
?>	 		
		<div class="form-field">  
			<label for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></label>  
				<?php
					$html = '';
					
					foreach ( $f['options'] as $o ) {
					
						$_selected = '';
						if ( $o == $_value ) { $_selected = ' selected="selected"'; } // End IF Statement
						
						$html .= '<input type="radio" name="' . $f['name'] . '" id="' . $f['name'] . '-' . esc_attr( strtolower( urlencode( $o ) ) ) . '" value="' . $o . '"' . $_selected . '  class="alignleft" style="width: auto; margin-right: 10px;" /><span class="alignleft">' . $o . '</span>' . "\n";
						
						$html .= '<br class="clear" />' . "\n";
					
					} // End FOREACH Loop
					
					echo $html;
				?>
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p><?php _e( sprintf($f['desc']),'woothemes' ); ?></p><?php } // End IF Statement ?>
		</div>
<?php
 						
 							} // End IF Statement
 						
 						break;
 				
 				/* - Datepicker Calendar.
 				----------------------------------------*/
 				
 						case 'calendar':
 						
 ?>	 		
		<div class="form-field calendar-field">  
			<label for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></label>  
			<input type='text' name="<?php echo $f['name']; ?>" id="<?php echo $f['name']; ?>" value="<?php echo $_value; ?>" class="woo-input-calendar" />  
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p><?php _e( sprintf($f['desc']),'woothemes' ); ?></p><?php } // End IF Statement ?>
		</div>
<?php
 						
 						break;
 				
 				/* - Time Input.
 				----------------------------------------*/
 				
 						case 'time':
 						
 ?>	 		
		<div class="form-field time-field">  
			<label for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></label>  
			<input type='text' name="<?php echo $f['name']; ?>" id="<?php echo $f['name']; ?>" value="<?php echo $_value; ?>" class="woo-input-time" maxlength="5" />  
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p><?php _e( sprintf($f['desc']),'woothemes' ); ?></p><?php } // End IF Statement ?>
		</div>
<?php
 						
 						break;
 				
 				/* - Information Notice.
 				----------------------------------------*/
 				
 						case 'info':
 						
 ?>	 		
		<div class="form-field">  
			<label for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></label>  
			<input type='text' name="<?php echo $f['name']; ?>" id="<?php echo $f['name']; ?>" value="<?php echo $_value; ?>" />  
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p><?php _e( sprintf($f['desc']),'woothemes' ); ?></p><?php } // End IF Statement ?>
		</div>
<?php
 						
 						break;
 				
 				/* - Google Map (not yet supported).
 				----------------------------------------*/
 				
 				/* - Default (text input field).
 				----------------------------------------*/
 				
 						default:
?>	 		
		<div class="form-field">  
			<label for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></label>  
			<input type='text' name="<?php echo $f['name']; ?>" id="<?php echo $f['name']; ?>" value="<?php echo $_value; ?>" />  
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p><?php _e( sprintf($f['desc']),'woothemes' ); ?></p><?php } // End IF Statement ?>
		</div>
<?php
	 					break;
	 				
	 				} // End SWITCH Statement
	 			
	 			} // End FOREACH Loop
	 		
	 		} // End IF Statement
	 		
	 	} // End form_fields_add()
	 	
	 	/*----------------------------------------
	 	  form_fields_edit()
	 	  ----------------------------------------
	 	  
	 	  * Add custom form fields to the `edit`
	 	  * screen of our custom taxonomy.
	 	----------------------------------------*/
	 	
	 	function form_fields_edit () {
	 		
	 		global $taxonomy, $tax, $tag_ID, $wpdb;
	 		
	 		if ( !$tag_ID || !is_numeric( $tag_ID ) ) { return; } // End IF Statement
	 		
	 		if ( count( $this->taxonomy_fields ) && array_key_exists( $taxonomy, $this->taxonomy_fields ) ) {
	 		
	 			foreach ( $this->taxonomy_fields[$taxonomy] as $f ) {
	 		
	 				${$f['name']} = get_metadata( 'woo_' . $this->db_metatype, $tag_ID, $f['name'], true );
	 				
	 				// If no value is present, set it to the standard value.
	 				if ( ${$f['name']} == '' && ( array_key_exists( 'std', $f ) && $f['std'] != '' ) ) {
	 				
	 					${$f['name']} = $f['std'];
	 				
	 				} // End IF Statement
	 				
	 				/* Begin field output.
 					----------------------------------------*/
 				
 					switch ( $f['type'] ) {
 				
 				/* - Text Area.
 				----------------------------------------*/
 						
 						case 'textarea':
 						
?>	 		
		<tr class="form-field">  
			<th for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></th>  
			<td><textarea name="<?php echo $f['name']; ?>" id="<?php echo $f['name']; ?>" rows="5"><?php echo ${$f['name']}; ?></textarea>  
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p class="description"><?php _e( $f['desc'],'woothemes' ); ?></p><?php } // End IF Statement ?></td>
		</tr>
<?php
 						
 						break;
 						
 				/* - Select Box.
 				----------------------------------------*/
 				
 						case 'select2':
 						
 							if ( count( $f['options'] ) ) {
 						
?>	 		
		<tr class="form-field">  
			<th for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></th>  
			<td><select name="<?php echo $f['name']; ?>" id="<?php echo $f['name']; ?>">
				<?php
					$html = '';
					
					foreach ( $f['options'] as $o ) {
					
						$_selected = '';
						if ( trim( $o ) == trim( ${$f['name']} ) ) { $_selected = ' selected="selected"'; } // End IF Statement
						
						$html .= '<option value="' . $o . '"' . $_selected . '>' . $o . '</option>' . "\n";
					
					} // End FOREACH Loop
					
					echo $html;
				?>
			</select>  
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p class="description"><?php _e( sprintf($f['desc']),'woothemes' ); ?></p><?php } // End IF Statement ?></td>
		</tr>
<?php
 						
 							} // End IF Statement
 						
 						break;
 				
 				/* - Checkbox.
 				----------------------------------------*/
 				
 						case 'checkbox':
 						
 						$_checked = '';
 						
 						if ( ${$f['name']} == 'true' ) {
 						
 							$_checked = ' checked="checked"';
 						
 						} // End IF Statement
 						
?>	 		
		<tr class="form-field">  
			<th for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></th>  
			<td><input type="checkbox" name="<?php echo $f['name']; ?>" id="<?php echo $f['name']; ?>" value="true" class="alignleft" style="width: auto; margin-right: 10px;"<?php echo $_checked; ?> /><span class="alignleft"><?php echo $f['std']; ?></span>
			<br class="clear" />
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p class="description"><?php _e( sprintf($f['desc']),'woothemes' ); ?></p><?php } // End IF Statement ?></td>
		</tr>
<?php
 						
 						break;
 				
 				/* - File Upload.
 				----------------------------------------*/
 				
 						case 'upload':
 						
?>	 		
		<tr class="form-field upload-field">  
			<th for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></th>  
			<td><?php 
				if ( function_exists( 'woothemes_medialibrary_uploader' ) ) {
					
					echo woothemes_medialibrary_uploader( $f['name'], ${$f['name']}, null ); // New AJAX Uploader using Media Library
				
				} // End IF Statement
			?><?php /*<input type='text' name="<?php echo $f['name']; ?>" id="<?php echo $f['name']; ?>" value="<?php echo ${$f['name']}; ?>" />*/ ?> 
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p class="description"><?php _e( sprintf($f['desc']),'woothemes' ); ?></p><?php } // End IF Statement ?></td>
		</tr>
<?php
 						
 						break;
 				
 				/* - Radio Buttons.
 				----------------------------------------*/
 				
 						case 'radio':
 						
 							if ( count( $f['options'] ) ) {
 						
?>	 		
		<tr class="form-field">  
			<th for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></th>  
				<td><?php
					$html = '';
					
					foreach ( $f['options'] as $o ) {
					
						$_checked = '';
					
						if ( trim( $o ) == trim( ${$f['name']} ) ) {
							
							$_checked = ' checked="checked"';
						
						} // End IF Statement
						
						$html .= '<input type="radio" name="' . $f['name'] . '" value="' . $o . '"' . $_checked . '  class="alignleft" style="width: auto; margin-right: 10px;" /><span class="alignleft">' . $o . '</span>' . "\n";
						
						$html .= '<br class="clear" />' . "\n";
					
					} // End FOREACH Loop
					
					echo $html;
				?>
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p class="description"><?php _e( sprintf($f['desc']),'woothemes' ); ?></p><?php } // End IF Statement ?></td>
		</tr>
<?php
 						
 							} // End IF Statement
 						
 						break;
 				
 				/* - Datepicker Calendar.
 				----------------------------------------*/
 				
 						case 'calendar':
 						
 ?>	 		
		<tr class="form-field calendar-field">  
			<th for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></th>  
			<td><input type='text' name="<?php echo $f['name']; ?>" id="<?php echo $f['name']; ?>" value="<?php echo ${$f['name']}; ?>" class="woo-input-calendar" />  
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p class="description"><?php _e( sprintf($f['desc']),'woothemes' ); ?></p><?php } // End IF Statement ?></td>
		</tr>
<?php
 						
 						break;
 				
 				/* - Time Input.
 				----------------------------------------*/
 				
 						case 'time':
 						
 ?>	 		
		<tr class="form-field time-field">  
			<th for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></th>  
			<td><input type='text' name="<?php echo $f['name']; ?>" id="<?php echo $f['name']; ?>" value="<?php echo ${$f['name']}; ?>" class="woo-input-time" maxlength="5" />  
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p class="description"><?php _e( sprintf($f['desc']),'woothemes' ); ?></p><?php } // End IF Statement ?></td>
		</tr>
<?php
 						
 						break;
 				
 				/* - Information Notice.
 				----------------------------------------*/
 				
 						case 'info':
 						
 ?>	 		
		<tr class="form-field">  
			<th scope="row" valign="top"><label for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></label></th>  
			<td><input type='text' name="<?php echo $f['name']; ?>" id="<?php echo $f['name']; ?>" value="<?php echo ${$f['name']}; ?>" />  
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p class="description"><?php _e( sprintf($f['desc']),'woothemes' ); ?></p><?php } // End IF Statement ?></td>
		</tr>
<?php
 						
 						break;
 				
 				/* - Google Map (not yet supported).
 				----------------------------------------*/
 				
 				/* - Default (text input field).
 				----------------------------------------*/
 				
 						default:
?>
		<tr class="form-field">  
			<th scope="row" valign="top"><label for="<?php echo $f['name']; ?>"><?php _e($f['label'],'woothemes'); ?></label></th>  
			<td><input type='text' name="<?php echo $f['name']; ?>" id="<?php echo $f['name']; ?>" value="<?php echo ${$f['name']}; ?>" />  
			<?php if ( array_key_exists( 'desc', $f ) && ( $f['desc'] ) ) { ?><p class="description"><?php _e( sprintf($f['desc']),'woothemes' ); ?></p><?php } // End IF Statement ?></td>
		</tr>
<?php	
						break;
						
					} // End SWITCH Statement
				
				} // End FOREACH Loop
	 		
	 		} // End IF Statement

	 	} // End form_fields_edit()
	 	
	 	/*----------------------------------------
	 	  meta_data_add()
	 	  ----------------------------------------
	 	  
	 	  * The save function for our custom form
	 	  * fields on the `add` screen of our
	 	  * custom taxonomy.
	 	  
	 	  * Params:
	 	  * - int $term_id
	 	  * - int $tt_id
	 	----------------------------------------*/
	 	
	 	function meta_data_add ( $term_id, $tt_id ) {
	 	
	 		global $wpdb, $taxonomy;
	 		
	 		// echo $term_id;
	 		
	 		$tag_ID = $term_id;
	 		
	 		$options = array();
	 		
	 		if ( count( $this->taxonomy_fields ) && array_key_exists( $taxonomy, $this->taxonomy_fields ) ) {
	 		
	 			foreach ( $this->taxonomy_fields[$taxonomy] as $f ) {
	 			
	 				$existing_data = get_metadata ( 'woo_' . $this->db_metatype, $tag_ID, $k, true );
	 			
	 				$options[$f['name']] = get_metadata ( 'woo_' . $this->db_metatype, $tag_ID, $k, true );
	 			
	 			} // End FOREACH Loop
	 			
	 		} // End IF Statement
	 		
	 		if ( count( $options ) ) {
	 		
		 		foreach ( $options as $k => $v ) {
		 		
		 			if ( ( $v == '' || ( is_array( $v ) && count( $v ) == 0 ) ) && $_POST[$k] != '' ) {
		 				
		 				add_metadata ( 'woo_' . $this->db_metatype, $tag_ID, $k, $_POST[$k], true );
		 			
		  			} // End IF Statement	
		 				 			
		 		} // End FOREACH Loop
	 		
	 		} // End IF Statement
	 		
	 	} // End meta_data_add()
	 	
	 	/*----------------------------------------
	 	  meta_data_edit()
	 	  ----------------------------------------
	 	  
	 	  * The save function for our custom form
	 	  * fields on the `edit` screen of our
	 	  * custom taxonomy.
	 	----------------------------------------*/
	 	
	 	function meta_data_edit () {
	 		
	 		global $wpdb, $taxonomy;
	 		
	 		$tag_ID = $_POST['tag_ID'];
	 		
	 		$options = array();
	 		
	 		if ( count( $this->taxonomy_fields ) && array_key_exists( $taxonomy, $this->taxonomy_fields ) ) {
	 		
	 			foreach ( $this->taxonomy_fields[$taxonomy] as $f ) {
	 			
	 				$existing_data = get_metadata ( 'woo_' . $this->db_metatype, $tag_ID, $k, true );
	 			
	 				$options[$f['name']] = get_metadata ( 'woo_' . $this->db_metatype, $tag_ID, $k, true );
	 			
	 			} // End FOREACH Loop
	 			
	 		} // End IF Statement
	 		
	 		if ( count( $options ) ) {
	 		
		 		foreach ( $options as $k => $v ) {
		 		
		 			// Insert
		 			if ( ( $v == '' || ( is_array( $v ) && count( $v ) == 0 ) ) && $_POST[$k] != '' ) {
		 				
		 				add_metadata ( 'woo_' . $this->db_metatype, $tag_ID, $k, $_POST[$k], false );
		 			
		 			// Update
		 			} else if ( ( $v != $_POST[$k] ) && ( $v != '' ) ) {
		 			
		 				update_metadata ( 'woo_' . $this->db_metatype, $tag_ID, $k, $_POST[$k] );		
		 			
		 			// Delete
		 			} else if ( $_POST[$k] == '' ) {
		 				
		 				delete_metadata ( 'woo_' . $this->db_metatype, $tag_ID, $k, '', true );
		 				
		 			} // End IF Statement	
		 				 			
		 		} // End FOREACH Loop
	 		
	 		} // End IF Statement
	 		
	 	} // End meta_data_edit()
	 	
	 	/*----------------------------------------
	 	  meta_data_delete()
	 	  ----------------------------------------
	 	  
	 	  * Makes sure to remove all meta data
	 	  * when deleting a particular term.
	 	----------------------------------------*/
	 	
	 	function meta_data_delete ( $term, $tt_id ) {
	 		
	 		$existing_meta = get_metadata ( 'woo_' . $this->db_metatype, $term );
	 		
	 		if ( count( $existing_meta ) ) {
	 		
	 			foreach ( $existing_meta as $k => $v ) {
	 			
	 				delete_metadata ( 'woo_' . $this->db_metatype, $tt_id, $k );
	 			
	 			} // End FOREACH Loop
	 		
	 		} // End IF Statement
	 	
	 	} // End meta_data_delete()
	 	
	 	/*----------------------------------------
	 	  register_form_fields()
	 	  ----------------------------------------
	 	  
	 	  * Add custom form fields to the `add`
	 	  * and `edit` forms of our custom
	 	  * taxonomy, as well as registering
	 	  * the save functions on the necessary
	 	  * WordPress hooks.
	 	----------------------------------------*/
	 	
	 	function register_form_fields () {
	 			
	 			// Get the various custom fields for our taxonomies.
	 			$this->get_field_settings();
	 			
	 			if ( count( $this->taxonomy_fields ) ) {
	 			
	 				foreach ( array_keys( $this->taxonomy_fields ) as $t ) {
	 				
	 					// Register form fields.
			 			add_action( $t . '_add_form_fields', array( &$this, 'form_fields_add' ) );
			 			add_action( $t . '_edit_form_fields', array( &$this, 'form_fields_edit' ) );
			 			
			 			// Register add, edit and delete functions.
			 			add_action( 'created_' . $t, array( &$this, 'meta_data_add' ), 10, 2 );  
		    			add_action( 'edit_' . $t, array( &$this, 'meta_data_edit' ), 10, 2 );
		    			add_action( 'delete_' . $t, array( &$this, 'meta_data_delete' ), 10, 2 );
	 				
	 				} // End FOREACH Loop
    			
    			} // End IF Statement
	 		
	 	} // End register_form_fields()
	 	
	 	/*----------------------------------------
	 	  get_field_settings()
	 	  ----------------------------------------
	 	  
	 	  * Get the settings for the various
	 	  * custom fields, as applied to the
	 	  * taxonomy in question.
	 	----------------------------------------*/
	 	
	 	function get_field_settings () {
	 	
	 		// Get all field settings.
	 		$fields = array();
	 		
	 		// woo_contentbuilder_cmb_tax
	 		$fields = get_option( 'woo_custom_template' );
	 		
	 		if ( ! is_array( $fields ) ) {
	 		
	 			$fields = array();
	 		
	 		} // End IF Statement
	 		
	 		// Remove any unnecessary fields from the array.
	 		foreach ( $fields as $k => $f ) {
	 		
	 			if ( array_key_exists( 'ctx', $f ) && array_key_exists( 'cpt', $f ) && ( in_array( 'TAXONOMY', array_keys( $f['cpt'] ) ) ) ) {
	 			
	 				$fields[$k]['taxonomies'] = array();
	 				
	 				
	 				// We want this field. Do nothing.
	 				foreach ( $f['ctx'] as $t => $v ) {
	 				
	 					$fields[$k]['taxonomies'][] = $t;
	 				
	 				} // End FOREACH Loop
	 				
	 			
	 			} else {
	 			
	 				unset( $fields[$k] );
	 			
	 			} // End IF Statement
	 		
	 		} // End FOREACH Loop
	 		
	 		// Assign our fields to the main $fields array.
	 		$this->fields = $fields;
	 		
	 		$taxonomies = array();
	 		
	 		if ( count( $this->fields ) ) {
	 		
				// Split the fields according to taxonomy.
				foreach ( $this->fields as $f ) {
				
					foreach ( $f['taxonomies'] as $t ) {
					
						if ( ! in_array( $t, $taxonomies ) ) {
						
							$taxonomies[] = $t;
						
						} // End IF Statement
					
					} // End FOREACH Loop
				
				} // End FOREACH Loop
				
				// Assign the array to the class variable.
				$this->taxonomies = $taxonomies;
			
				if ( count( $this->taxonomies ) ) {
				
					// Create the various keys needed for $this->taxonomy_fields.
					foreach ( $this->taxonomies as $t ) {
					
						if ( ! array_key_exists( $t, $this->taxonomy_fields ) ) {
						
							$this->taxonomy_fields[$t] = array();
						
						} // End IF Statement
					
					} // End FOREACH Loop
				
					if ( count( $this->taxonomy_fields ) ) {
					
						foreach ( $this->fields as $f ) {
						
							foreach ( $f['taxonomies'] as $t ) {
							
								$this->taxonomy_fields[$t][] = $f;
							
							} // End FOREACH Loop
						
						} // End FOREACH Loop
					
					} // End IF Statement
				
				} // End IF Statement
			
			} // End IF Statement
	 	
	 	} // End get_field_settings()
	 	
	 	/*----------------------------------------
	 	  setup_meta_for_term()
	 	  ----------------------------------------
	 	  
	 	  * Get the meta data for the term
	 	  * being viewed and make it available
	 	  * for use in the template.
	 	----------------------------------------*/
	 	
	 	function setup_meta_for_term () {
	 	
	 		$term_meta_data = array();
	 		
	 		// Only run on taxonomy archive pages.
	 		
	 		if ( is_tax() || is_category() || is_tag() ) {
	 		
	 			global $wp_query, $taxonomy, $cat, $tag, $tag_ID, $category_name;
	 			
	 			$term_id = 0;
	 			$term_name = '';
	 			
	 			if ( is_tax() ) {
	 			
	 				$term_data = get_term_by( 'slug', $wp_query->query_vars[$taxonomy], $taxonomy );
	 				
	 				if ( $term_data ) {
	 				
	 					$term_id = $term_data->term_id;
	 				
	 				} // End IF Statement
	 				
	 				$term_name = $taxonomy;
	 			
	 			} // End IF Statement
	 			
	 			if ( is_category() ) {
	 			
	 				$term_id = $cat;
	 				
	 				$term_name = $category_name;
	 			
	 			} // End IF Statement
	 			
	 			if ( is_tag() ) {
	 			
	 				$term_data = get_term_by( 'slug', $tag, 'post_tag' );
	 				
	 				if ( $term_data ) {
	 				
	 					$term_id = $term_data->term_id;
	 				
	 				} // End IF Statement
	 				
	 				$term_name = $tag;
	 			
	 			} // End IF Statement
	 			
	 			// Get the meta data, if we have a term_id.
	 			
	 			if ( $term_id ) {
	 			
	 				$existing_data = get_metadata ( 'woo_' . $this->db_metatype, $term_id );
	 				
	 				if ( count( $existing_data ) ) {
	 				
	 					$term_meta_data = $existing_data;
	 				
	 				} // End IF Statement
	 				
	 			} // End IF Statement
	 			
	 			/*
	 			// Make sure we include all fields, especially those that have default data and aren't in the database.
	 			if ( array_key_exists( $term_name, $this->taxonomy_fields ) && ( count( $this->taxonomy_fields[$term_name] ) ) ) {
	 			
	 				foreach ( $this->taxonomy_fields[$term_name] as $k => $t ) {
	 				
	 					if ( ! in_array( $k, array_keys( $term_meta_data ) ) && ( array_key_exists( $t['std'] ) && $t['std'] != '' ) ) {} else {
	 					
	 						$term_meta_data[$t['name']] = $t['std'];
	 					
	 					} // End IF Statement
	 				
	 				} // End FOREACH Loop
	 			
	 			} // End IF Statement
	 			*/
	 			
	 			// Sort the meta data in alphabetical order.
	 			ksort( $term_meta_data );
	 			
	 		} // End IF Statement
	 		
	 			// Make the variables available to our template.
	 			
	 			$GLOBALS['woo_term_meta'] = $term_meta_data;
	 			
	 			$woo_term_meta = $GLOBALS['woo_term_meta'];
	 		
	 		return $term_meta_data;
	 	
	 	} // End setup_meta_for_term()
	 	
	 	/*----------------------------------------
	 	  enqueue_scripts()
	 	  ----------------------------------------
	 	  
	 	  * Load the JavaScript files used in
	 	  * the admin by certain custom fields.
	 	----------------------------------------*/
	 	
	 	function enqueue_scripts () {
	 	
	 		// Register custom scripts for the Media Library AJAX uploader.
	 		wp_enqueue_script( 'thickbox' );
			wp_register_script( 'woo-medialibrary-uploader', get_template_directory_uri() . '/functions/js/woo-medialibrary-uploader.js', array( 'jquery', 'thickbox' ) );
			wp_enqueue_script( 'woo-medialibrary-uploader' );
			wp_enqueue_script( 'media-upload' );
			
			wp_enqueue_script( 'woo-cb-datepicker', get_template_directory_uri() . '/functions/js/ui.datepicker.js', array( 'jquery', 'jquery-ui-core' ) );
			wp_enqueue_script( 'woo-cb-maskedinput', get_template_directory_uri() . '/functions/js/jquery.maskedinput-1.2.2.js', array( 'jquery' ) );
			
			wp_enqueue_script( 'woo-cb-taxonomy', get_template_directory_uri() . '/includes/content-builder/js/taxonomy.js', array( 'jquery', 'woo-cb-datepicker', 'woo-cb-maskedinput' ) );
			
			// Allow our JavaScript file (the general one for taxonomy JavaScripts) to see our template and stylesheet URLs.
			$data = array( 'template_url' => get_template_directory_uri(), 'stylesheet_url' => get_stylesheet_directory_uri() );
			wp_localize_script( 'woo-cb-taxonomy', 'woo_theme_urls', $data );

	 	
	 	} // End enqueue_scripts()
	 	
	 	/*----------------------------------------
	 	  enqueue_styles()
	 	  ----------------------------------------
	 	  
	 	  * Load the CSS stylesheets files used
	 	  * in the admin by certain custom fields.
	 	----------------------------------------*/
	 	
	 	function enqueue_styles () {
	 	
	 		wp_register_style( 'woo-cb-admin', get_template_directory_uri() . '/includes/content-builder/css/content-builder.css' );
	 		wp_enqueue_style( 'woo-cb-admin' );
	 		
	 		// jQuery UI CSS.
			wp_enqueue_style( 'woo-cb-datepicker-ui', get_template_directory_uri() . '/functions/css/jquery-ui-datepicker.css', array(), '1.8.4', 'screen' );
	 	
		 	$_html = '';
			
			$_html .= '<link rel="stylesheet" href="' . site_url() . '/' . WPINC . '/js/thickbox/thickbox.css" type="text/css" media="screen" />' . "\n";
			$_html .= '<script type="text/javascript">
			var tb_pathToImage = "' . site_url() . '/' . WPINC . '/js/thickbox/loadingAnimation.gif";
		    var tb_closeImage = "' . site_url() . '/' . WPINC . '/js/thickbox/tb-close.png";
		    </script>' . "\n";
		    
		    echo $_html;
	 	
	 	} // End enqueue_styles()
	
	} // End Class
?>