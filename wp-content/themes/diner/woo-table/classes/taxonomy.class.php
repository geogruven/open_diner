<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: Custom taxonomy-specific functions for the WooTable WordPress plugin.
Date Created: 2010-08-11.
Author: Matty.
Since: 0.0.0.1


TABLE OF CONTENTS

- var $singular_label
- var $token
- var $rewrite_path
- var $description
- var $post_types_supported (array)
- var $db_tablename

- var $plugin_path
- var $plugin_url

- function WooTable_Taxonomy_Tables (constructor)
- function init ()
- function register_custom_taxonomies ()
- function register_single_custom_taxonomy ()
- function updated_messages ()
- function add_help_text ()
- function register_custom_columns_filters ()
- function add_custom_column_headings ()
- function add_custom_column_data ()
- function form_fields_add ()
- function form_fields_edit ()
- function meta_data_add ()
- function meta_data_edit ()
- function register_form_fields ()

-----------------------------------------------------------------------------------*/

	class WooTable_Taxonomy_Tables {
		
		/*----------------------------------------
	 	  Class Variables
	 	  ----------------------------------------
	 	  
	 	  * Setup of variable placeholders, to be
	 	  * populated when the constructor runs.
	 	----------------------------------------*/
		
		var $singular_label;
		var $token;
		var $rewrite_path;
		var $description;
		var $post_types_supported;
		var $db_tablename;
		
		var $plugin_path;
		var $plugin_url;
		
		/*----------------------------------------
	 	  WooTable_Taxonomy_Tables()
	 	  ----------------------------------------
	 	  
	 	  * Constructor function.
	 	  * Sets up the class and registers
	 	  * variable action hooks.
	 	  
	 	  * Params:
	 	  * - String $plugin_path
	 	  * - String $plugin_url
	 	----------------------------------------*/
		
		function WooTable_Taxonomy_Tables ( $plugin_path, $plugin_url ) {
			
			$this->plugin_path = $plugin_path;
			$this->plugin_url = $plugin_url;
			
		} // End WooTable_Taxonomy_Tables()
		
		/*----------------------------------------
	 	  init()
	 	  ----------------------------------------
	 	  
	 	  * This guy runs the show.
	 	  * Rocket boosters... engage!
	 	----------------------------------------*/
		
		function init () {
			
			global $wpdb;
			
			$this->singular_label = __( 'Table', 'woothemes' );
			$this->plural_label = __( 'Tables', 'woothemes' );
			$this->token = 'tables';
			$this->rewrite_path = 'tables';
			$this->description = __( 'The tables available at your restaurant.', 'woothemes' );
			$this->post_types_supported = array( 'reservation' );
			$this->db_tablename = $wpdb->prefix . 'woo_' . $this->token . '_meta';
			
			$this->register_form_fields();
			$this->register_custom_taxonomies();			
			
			$this->register_custom_columns_filters();
			
			// Administration area action and filter hooks
			add_action( 'contextual_help', array( &$this, 'add_help_text' ), 10, 3 );
			
		} // End init()
		
		/*----------------------------------------
	 	  Utility Functions
	 	  ----------------------------------------
	 	  
	 	  * These functions are used within this
	 	  * class as helpers for other functions.
	 	----------------------------------------*/
	 	
	 	/*----------------------------------------
	 	  register_custom_taxonomies()
	 	  ----------------------------------------
	 	  
	 	  * A helper function to call the
	 	  * `register_single_custom_taxonomy`
	 	  * function potentially multiple times.
	 	----------------------------------------*/
		
		function register_custom_taxonomies () {

	 		$this->register_single_custom_taxonomy ( $this->token, $this->singular_label, $this->plural_label, $this->post_types_supported );
	 		
	 	} // End register_custom_taxnomies()
	 	
	 	/*----------------------------------------
	 	  register_single_custom_taxonomy()
	 	  ----------------------------------------
	 	  
	 	  * A wrapper function to call register
	 	  * a custom taxonomy.
	 	----------------------------------------*/
	 	
	 	function register_single_custom_taxonomy ( $token, $single, $plural, $post_types = array('reservation'), $hierarchical = true, $rewrite = true ) {	
	 	
	 		register_taxonomy(
								$token, 
								$post_types, 
								array(
										"hierarchical" => $hierarchical, 
										"rewrite" => $rewrite, 
										"labels" => array(
											'name' => __( $plural, 'woothemes' ), 
											'singular_name' => __( $single, 'woothemes' ), 
											'search_items' => __( 'Search ' . $plural, 'woothemes' ), 
											'popular_items' => __( 'Popular ' . $plural, 'woothemes' ), 
											'all_items' => __( 'All ' . $plural, 'woothemes' ), 
											'parent_item' => __( 'Parent ' . $single, 'woothemes' ), 
											'parent_item_colon' => __( 'Parent ' . $single . ':', 'woothemes' ), 
											'edit_item' => __( 'Edit ' . $single, 'woothemes' ), 
											'update_item' => __( 'Update ' . $single, 'woothemes' ), 
											'add_new_item' => __( 'Add New ' . $single, 'woothemes' ), 
											'new_item_name' => __( 'New ' . $single, 'woothemes' )
											
										),
										// "capabilities" => array()
									)
							);
	 		
	 	} // End register_single_custom_taxonomy()
		
		/*----------------------------------------
	 	  Administration Display Functions
	 	  ----------------------------------------
	 	  
	 	  * Functions that format and customise
	 	  * the display of content in the admin
	 	  * area of our custom taxonomy.
	 	----------------------------------------*/
		
		/*----------------------------------------
	 	  updated_messages()
	 	  ----------------------------------------
	 	  
	 	  * Customise the update messages for our
	 	  * custom taxonomy.
	 	----------------------------------------*/
		
		function updated_messages ( $messages ) {
		
			$messages[$this->token] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( $this->singular_label . ' updated. <a href="%s">View ' . strtolower( $this->singular_label ) . '</a>', 'woothemes' ), esc_url( get_permalink($post_ID) ) ),
			2 => __( 'Custom field updated.', 'woothemes' ),
			3 => __( 'Custom field deleted.', 'woothemes' ),
			4 => __( $this->singular_label . ' updated.', 'woothemes' ),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __( $this->singular_label . ' restored to revision from %s', 'woothemes' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( $this->singular_label . ' published. <a href="%s">View ' . strtolower( $this->singular_label ) . '</a> ', 'woothemes' ), esc_url( get_permalink($post_ID) ) ),
			7 => __( $this->singular_label . ' saved.'),
			8 => sprintf( __( $this->singular_label . ' submitted. <a target="_blank" href="%s">Preview ' . strtolower( $this->singular_label ) . '</a>', 'woothemes' ), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( __( $this->singular_label . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' .strtolower( $this->singular_label ) . '</a>', 'woothemes' ),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __( $this->singular_label . ' draft updated. <a target="_blank" href="%s">Preview ' . strtolower( $this->singular_label ) . '</a>', 'woothemes' ), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			);
			
			return $messages;
			
		} // End updated_messages()
		
		/*----------------------------------------
	 	  add_help_text()
	 	  ----------------------------------------
	 	  
	 	  * Add contextual help text on our
	 	  * custom taxonomy's admin screen.
	 	----------------------------------------*/
		
		function add_help_text ( $contextual_help, $screen_id, $screen ) { 
		  
		  // $contextual_help .= var_dump($screen); // use this to help determine $screen->id
		  
		  if ( 'edit-' . $this->token == $screen->id ) {
		  
		    $contextual_help =
		      '<p>' . __('Things to remember when adding or editing a table listing:', 'woothemes') . '</p>' .
		      '<ul>' .
		      '<li>' . __('Specify the name of the table in the `Name` field. This can be a table number as well.', 'woothemes') . '</li>' .
		      '<li>' . __('The `slug` field can be used by your theme for displaying reservations for a particular table. Please keep this all lower-case with dashes ( - ) instead of spaces between words.', 'woothemes') . '</li>' .
		      '<li>' . __('Specify the number of seats available at the table. Numbers only, please.', 'woothemes') . '</li>' .
		      '</ul>' .
		      '<p><strong>' . __('For more information:', 'woothemes') . '</strong></p>' .
		      '<p>' . sprintf( __('<a href="%s" target="_blank">WooTable Documentation</a>', 'woothemes'), '#' ) . '</p>' .
		      '<p>' . sprintf( __('<a href="%s" target="_blank">WooThemes Support Forums</a>', 'woothemes'), 'http://forum.woothemes.com/' ) . '</p>';
		  
		  } // End IF Statement
		  
		  return $contextual_help;
		  
		} // End add_help_text()
		
		/*----------------------------------------
	 	  register_custom_columns_filters()
	 	  ----------------------------------------
	 	  
	 	  * Register our custom taxonomy's
	 	  * custom column headings and data hooks.
	 	----------------------------------------*/
		
		public function register_custom_columns_filters () {
		
			add_filter( 'manage_edit-' . $this->token . '_columns', array( &$this, 'add_custom_column_headings' ), 10, 1 );
			add_action( 'manage_' . $this->token . '_custom_column', array( &$this, 'add_custom_column_data' ), 10, 3 );
			
		} // End register_custom_columns_filters()
		
		/*----------------------------------------
	 	  add_custom_column_headings()
	 	  ----------------------------------------
	 	  
	 	  * Add custom column headings on
	 	  * the `posts list` page of our custom
	 	  * taxonomy.
	 	----------------------------------------*/
		
		public function add_custom_column_headings ( $columns ) {
			
			$new_columns = array(
			'cb' => '<input type="checkbox" />',
			'name' => __('Name', 'woothemes'),
			'number_of_seats' => __('Number of seats', 'woothemes'),
			// 'description' => __('Description'),
			// 'slug' => __('Slug'),
			// 'posts' => __('Posts')
			'upcoming_reservations' => __('Upcoming Reservations', 'woothemes')
			);
			
			return $new_columns;
		
		} // End add_custom_column_headings()
		
		/*----------------------------------------
	 	  add_custom_column_data()
	 	  ----------------------------------------
	 	  
	 	  * Add data for our custom columns on
	 	  * the `posts list` page of our custom
	 	  * taxonomy.
	 	----------------------------------------*/
		
		public function add_custom_column_data ( $out, $column_name, $id ) {
			
			switch ($column_name) {
			
			case 'number_of_seats':
						
				$number_of_seats = get_metadata( 'woo_tables', $id, 'number_of_seats', true );
				
				if ( ! $number_of_seats ) { $number_of_seats = 0; } // End IF Statement
				
				$out .= $number_of_seats;
			
			break;
			
			case 'upcoming_reservations':
			
				$current_date = date( 'Y-m-d' );
			
				$term = get_term_by( 'id', $id, 'tables' );
			
				$args = array(
							'post_type' => 'reservation', 
							'meta_key' => 'reservation_date', 
							'meta_value' => $current_date, 
							'meta_compare' => '>=', 
							'nopaging' => true, 
							'tables' => $term->slug
						);
			
				$query = get_posts( $args, ARRAY_A );
				
				$filtered_query = $query;
				
				if ( $query ) {
					
					$filtered_query = array();
					
					foreach ( $query as $q ) {
					
						if ( get_post_meta( $q->ID, 'reservation_status', true ) != 'canceled' ) {
							
							$filtered_query[] = $q;
							
						} // End IF Statement
						
					} // End IF Statement
					
				} // End IF Statement
				
				$out .= count( $filtered_query );
			
			break;
			
			default:
			break;
			
			} // End SWITCH Statement
			
			return $out;
		
		} // End add_custom_column_data()
		
		/*----------------------------------------
	 	  form_fields_add()
	 	  ----------------------------------------
	 	  
	 	  * Add custom form fields to the `add`
	 	  * screen of our custom taxonomy.
	 	----------------------------------------*/
		
		function form_fields_add () {
	 		
	 		global $tax;
?>	 		
		<div class="form-field">  
			<label for="number_of_seats"><?php _e('Number of Seats','woothemes'); ?></label>  
			<input type='text' name="number_of_seats" id="number_of_seats" />  
			<p><?php _e( 'The number of seats at this table.', 'woothemes' ); ?></p> 
		</div>
<?php
	 		
	 	} // End form_fields_add()
	 	
	 	/*----------------------------------------
	 	  form_fields_edit()
	 	  ----------------------------------------
	 	  
	 	  * Add custom form fields to the `edit`
	 	  * screen of our custom taxonomy.
	 	----------------------------------------*/
	 	
	 	function form_fields_edit () {
	 		
	 		global $tax, $tag_ID, $wpdb;
	 		
	 		if ( !$tag_ID || !is_numeric( $tag_ID ) ) { return; } // End IF Statement
	 		
	 		$number_of_seats = get_metadata( 'woo_tables', $tag_ID, 'number_of_seats', true );
?>
		<tr class="form-field">  
			<th scope="row" valign="top"><label for="number_of_seats"><?php _e('Number of Seats','woothemes'); ?></label></th>  
			<td><input type='text' name="number_of_seats" id="number_of_seats" value="<?php echo $number_of_seats; ?>" />  
			<p class="description"><?php _e( 'The number of seats at this table.','woothemes' ); ?></p></td>
		</tr>
<?php	
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
	 	
	 		global $wpdb;
	 		
	 		// echo $term_id;
	 		
	 		$tag_ID = $term_id;
	 		$taxonomy = $_POST['taxonomy'];
	 		
	 		$number_of_seats = '';
	 		
	 		$options = array(
	 						'number_of_seats' => $number_of_seats
	 						);
	 		
	 		foreach ( $options as $k => $v ) {
	 		
	 			if ( ( $v == '' && $_POST[$k] != '' ) ) {
	 				
	 				add_metadata ( 'woo_tables', $tag_ID, $k, $_POST[$k], true );
	 			
	  			} // End IF Statement	
	 				 			
	 		} // End FOREACH Loop
	 		
	 	} // End meta_data_add()
	 	
	 	/*----------------------------------------
	 	  meta_data_edit()
	 	  ----------------------------------------
	 	  
	 	  * The save function for our custom form
	 	  * fields on the `edit` screen of our
	 	  * custom taxonomy.
	 	----------------------------------------*/
	 	
	 	function meta_data_edit () {
	 		
	 		global $wpdb;
	 		
	 		$tag_ID = $_POST['tag_ID'];
	 		$taxonomy = $_POST['taxonomy'];
	 		
	 		$number_of_seats = get_metadata( 'woo_tables', $tag_ID, 'number_of_seats', true );
	 		
	 		$options = array(
	 						'number_of_seats' => $number_of_seats
	 						);
	 		
	 		foreach ( $options as $k => $v ) {
	 		
	 			// Insert
	 			if ( ( $v == '' && $_POST[$k] != '' ) ) {
	 				
	 				add_metadata ( 'woo_tables', $tag_ID, $k, $_POST[$k], false );
	 			
	 			// Update
	 			} else if ( ( $v != $_POST[$k] ) && ( $v != '' ) ) {
	 			
	 				update_metadata ( 'woo_tables', $tag_ID, $k, $_POST[$k] );		
	 			
	 			// Delete
	 			} else if ( $_POST[$k] == '' ) {
	 				
	 				delete_metadata ( 'woo_tables', $tag_ID, $k, '', true );
	 				
	 			} // End IF Statement	
	 				 			
	 		} // End FOREACH Loop
	 		
	 	} // End meta_data_edit()
	 	
	 	/*----------------------------------------
	 	  register_form_fields()
	 	  ----------------------------------------
	 	  
	 	  * Add custom form fields to the `add`
	 	  * and `edit` forms of our custom
	 	  * taxonomy, as well as registering
	 	  * the save functions on the necessary
	 	  * WordPress hooks.
	 	----------------------------------------*/
	 	
	 	private function register_form_fields () {
	 			
	 			// Register form fields.
	 			add_action( $this->token . '_add_form_fields', array( &$this, 'form_fields_add' ) );
	 			add_action( $this->token . '_edit_form_fields', array( &$this, 'form_fields_edit' ) );
	 			
	 			// Register add and edit functions.
	 			add_action( 'created_' . $this->token, array( &$this, 'meta_data_add' ), 10, 2 );  
    			add_action( 'edit_' . $this->token, array( &$this, 'meta_data_edit' ), 10, 2 );
	 		
	 	} // End register_form_fields()
		
	} // End Class WooTable_Taxonomy_Tables
?>