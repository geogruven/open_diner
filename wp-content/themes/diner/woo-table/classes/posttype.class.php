<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: Custom post type-specific functions for the WooTable WordPress plugin.
Date Created: 2010-08-11.
Author: Matty.
Since: 0.0.0.1


TABLE OF CONTENTS

- var $singular_label
- var $token
- var $rewrite_path
- var $description

- var $plugin_path
- var $plugin_url

- function WooTable_PostType_Reservation (constructor)
- function init ()
- function register_post_type ()
- function meta_box_reservationdata ()
- function save_meta_box_reservationdata ()
- function restrict_manage_posts ()
- function posts_join ()
- function posts_where ()
- function posts_orderby ()
- function posts_groupby ()
- function create_meta_boxes ()
- function updated_messages ()
- function add_help_text ()
- function register_custom_columns_filters ()
- function add_custom_column_headings ()
- function add_custom_column_data ()
- function admin_scripts ()
- function ajax_get_times ()

-----------------------------------------------------------------------------------*/

	class WooTable_PostType_Reservation {
		
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
		
		var $plugin_path;
		var $plugin_url;
		var $plugin_prefix;
		
		/*----------------------------------------
	 	  WooTable_PostType_Reservation()
	 	  ----------------------------------------
	 	  
	 	  * Constructor function.
	 	  * Sets up the class and registers
	 	  * variable action hooks.
	 	  
	 	  * Params:
	 	  * - String $plugin_path
	 	  * - String $plugin_url
	 	  * - String $plugin_prefix
	 	----------------------------------------*/
		
		function WooTable_PostType_Reservation ( $plugin_path, $plugin_url, $plugin_prefix ) {
		
			$this->plugin_path = $plugin_path;
			$this->plugin_url = $plugin_url;
			$this->plugin_prefix = $plugin_prefix;
		
			$this->init();
			
			// Add actions to implement AJAX time changing in the administration area.
			add_action( 'wp_ajax_get_times', array( &$this, 'get_times' ) );
			// add_action( 'wp_ajax_nopriv_get_times', array( &$this, 'get_times' ) );
			
		} // End WooTable_PostType_Reservation()
		
		/*----------------------------------------
	 	  init()
	 	  ----------------------------------------
	 	  
	 	  * This guy runs the show.
	 	  * Rocket boosters... engage!
	 	----------------------------------------*/
		
		function init () {
			
			$this->singular_label = 'Reservation';
			$this->token = 'reservation';
			$this->rewrite_path = 'reservations';
			$this->description = 'A listing of reservations made at your restaurant.';
			
			$this->register_post_type();
			
			// Administration action and filter hooks for the administration area
			add_filter( 'post_updated_messages', array( &$this, 'updated_messages' ) );
			add_action( 'contextual_help', array( &$this, 'add_help_text' ), 10, 3 );
			
			if ( isset( $_REQUEST['post_type'] ) && is_admin() && $_REQUEST['post_type'] == $this->token ) {
				
				add_action( 'restrict_manage_posts', array( &$this, 'restrict_manage_posts' ), 99 );
				add_filter( 'posts_join', array( &$this, 'posts_join' ), 99 );
				add_filter( 'posts_where', array( &$this, 'posts_where' ), 99 );
				add_filter( 'posts_groupby', array( &$this, 'posts_groupby' ), 99 );
				add_filter( 'posts_orderby', array( &$this, 'posts_orderby' ), 99 );
				
			} // End IF Statement
			
			// Meta box hooks
			add_action ( 'admin_menu', array( &$this, 'create_meta_boxes' ) );
			add_action ( 'save_post', array( &$this, 'save_meta_box_reservationdata' ) );
				
			// Admin Script and Style hooks
			add_action( 'admin_print_scripts-post-new.php', array( &$this, 'admin_scripts' ), 99 );
			add_action( 'admin_print_scripts-post.php', array( &$this, 'admin_scripts' ), 99 );
			
			// Register custom columns on the `List` screen			
			$this->register_custom_columns_filters();
			
		} // End init()
		
		/*----------------------------------------
	 	  Utility Functions
	 	  ----------------------------------------
	 	  
	 	  * These functions are used within this
	 	  * class as helpers for other functions.
	 	----------------------------------------*/
	 	
	 	/*----------------------------------------
	 	  register_post_type()
	 	  ----------------------------------------
	 	  
	 	  * A helper function to register our
	 	  * custom post type.
	 	----------------------------------------*/
		
		public function register_post_type () {
			
			register_post_type( $this->token,
				array(
					'labels' => array(
						'name' => __( $this->singular_label . 's', 'woothemes' ),
						'singular_name' => __( $this->singular_label, 'woothemes' ),
						'add_new' => __( 'Add New', 'woothemes' ),
						'add_new_item' => __( 'Add New ' . $this->singular_label, 'woothemes' ),
						'edit_item' => __( 'Edit ' . $this->singular_label, 'woothemes' ),
						'edit' => __( 'Edit', 'woothemes' ),
						'new_item' => __( 'New ' . $this->singular_label, 'woothemes' ),
						'view_item' => __( 'View ' . $this->singular_label, 'woothemes' ),
						'search_items' => __( 'Search ' . $this->singular_label . 's', 'woothemes' ),
						'not_found' => __( 'No ' . strtolower( $this->singular_label ) . 's found.', 'woothemes' ),
						'not_found_in_trash' => __( 'No ' . strtolower( $this->singular_label ) . 's found in Trash.', 'woothemes' ),
						'view' => __( 'View ' . $this->singular_label, 'woothemes' ),
						'parent_item_colon' => __( 'Parent ' . $this->singular_label . ':', 'woothemes' ),
						'parent' => __( 'Parent ' . $this->singular_label, 'woothemes' )
					),
					'public' => true, 
					'publicly_queryable' => false, 
					'show_ui' => true,
					'exclude_from_search' => true,
					'description' => __( $this->description, 'woothemes' ),
					'menu_position' => 20,
					'menu_icon' => trailingslashit( $this->plugin_url ) . 'assets/icons/reservations_active' . '.png',
					'hierarchical' => false,
					'query_var' => true,
					/* Global control over capabilities. */
					// 'capability_type' => $this->capability_type,
					
					/* Specific control over capabilities. */
					// 'capabilities' => $this->capabilities,
					'supports' => array( 
									'title', 
									// 'editor', 
									// 'excerpt', 
									// 'custom-fields', 
									// 'thumbnail' 
									),
					'rewrite' => array( 'slug' => $this->rewrite_path, 'with_front' => false ),
					'taxonomies' => array( 'tables' ),
					'can_export' => true, 
					'show_in_nav_menus' => true, 
					//'register_meta_box_cb' => 'your_callback_function_name', // Custom callback function for after the meta boxes have been set up.
					'permalink_epmask' => EP_PERMALINK
				)
			);
			

		
		} // End register_post_type()

		
		/*----------------------------------------
	 	  Meta Box Functions
	 	  ----------------------------------------
	 	  
	 	  * Various functions to create and work
	 	  * with meta boxes within our custom
	 	  * post type.
	 	----------------------------------------*/
		
		/*----------------------------------------
	 	  meta_box_reservationdata()
	 	  ----------------------------------------
	 	  
	 	  * Get, format and setup the data in
	 	  * legible XHTML for display in our
	 	  * meta box.
	 	----------------------------------------*/
		
		public function meta_box_reservationdata () {
		
			global $post;
			
			$box_content = '';
			
			// Unique nooonce.
			$box_content .= '<input type="hidden" name="wootable_' . $this->token . '_noonce" id="wootable_' . $this->token . '_noonce" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
			
			// Get the meta information for this entry.
			$number_of_people = get_post_meta($post->ID, 'number_of_people', true);
			$reservation_date = get_post_meta($post->ID, 'reservation_date', true);
			$reservation_time = get_post_meta($post->ID, 'reservation_time', true);
			$reservation_instructions = get_post_meta($post->ID, 'reservation_instructions', true);
			$contact_tel = get_post_meta($post->ID, 'contact_tel', true);
			$contact_email = get_post_meta($post->ID, 'contact_email', true);
			$reservation_confirmed = get_post_meta($post->ID, 'reservation_confirmed', true);
			$reservation_status = get_post_meta($post->ID, 'reservation_status', true);
			
			$checked_value = '';
			if ( $reservation_confirmed ) { $checked_value = ' checked="checked"'; } // End IF Statement
			
			if ( $reservation_date == '' || $reservation_date == '0000-00-00' ) { $reservation_date = date('Y-m-d'); } // End IF Statement
			
			$box_content .= '<p><label for="number_of_people" class="alignleft">' . __( 'Number of people:', 'woothemes' ) . '</label>';
			$box_content .= '<span class="right"><input type="text" name="number_of_people" id="number_of_people" class="alignright" value="' . $number_of_people . '" style="width: 100%;" /></span></p>';
			$box_content .= '<div class="clear"></div><!--/.clear-->';
			
			$box_content .= '<p><label for="reservation_date" class="alignleft">' . __( 'Reservation Date:', 'woothemes' ) . '</label>';
			$box_content .= '<span class="right"><input type="text" name="reservation_date" id="reservation_date" class="alignright" value="' . $reservation_date . '" style="width: 100%;" maxlength="10" /><em>(' . __( 'Date format is YYYY-MM-DD- The current date is inserted for you automatically', 'woothemes' ) . ')</em></span></p>';
			$box_content .= '<div class="clear"></div><!--/.clear-->';
			
			$box_content .= '<div id="reservation_date_picker"></div><!--/#reservation_date_picker-->';
			
			$box_content .= '<p><label for="reservation_time" class="alignleft reservation_time_label">' . __( 'Reservation Time:', 'woothemes' ) . '</label>';
			
			$box_content .= WTFactory::display_changed_times ( $this->plugin_prefix, false, $reservation_time, 0, $reservation_date, true );
			
			/*
			$business_hours = WTFactory::get_business_hours( $this->plugin_prefix );
			
			$index = strtolower( date('D', strtotime($reservation_date) ) );
			
			// Compensate for the colloquial convention of "thurs" instead of "thu", and "tues" instead of "tue".
			if ( $index == 'thu' ) { $index = 'thurs'; } // End IF Statement
			if ( $index == 'tue' ) { $index = 'tues'; } // End IF Statement
			
			$times = $business_hours[$index];
			
			$times_array = WTFactory::get_times_between( $times['openingtime'], $times['closingtime'], $this->plugin_prefix, $reservation_date ); // 2010-11-01.
			
			if ( $times_array ) {
			
				$box_content .= '<span class="right"><select name="reservation_time" class="reservation_time alignleft">' . "\n";
					
					$is_twelvehour = false;
						
					if ( get_option( $this->plugin_prefix . 'time_format' ) == '12' ) { $is_twelvehour = true; } // End IF Statement
					
					$selected_hour = $reservation_time;
				
					$separator = '_________';
				
					foreach ( $times_array as $t ) {
						
						$_selected = '';
						
						if ( $t == $selected_hour ) { $_selected = ' selected="selected"'; } // End IF Statement
						
						$_display = $t;
						
						if ( $is_twelvehour && ( $t != $separator ) ) {
														
							$_hour = substr( $t, 0, 2 );
							$_min = substr( $t, 3, 2 );
							
							$_display = date( 'h:i A', mktime( $_hour, $_min, 0, 12, 32, 1997 ) );
							
						} // End IF Statement
						
						// Set an empty value for the time seperator.
						
						if ( $t == $separator ) { $t = ''; } // End IF Statement
						
						$box_content .= '<option value="' . $t . '"' . $_selected . '>' . $_display . '</option>' . "\n";
				
						// $box_content .= '<option value="' . $t . '"' . $_selected . '>' . $t . '</option>' . "\n";
						
					} // End FOREACH Loop
				
				$box_content .= '</select></span>' . "\n";
			
			} else {
			
				$box_content .= __( 'None available.', 'woothemes' );
			
	 		} // End IF Statement
			*/
			$box_content .= '<div class="clear"></div><!--/.clear-->';
			
			$box_content .= '<p><label for="reservation_instructions" class="alignleft">' . __( 'Reservation Instructions:', 'woothemes' ) . '</label>';
			$box_content .= '<span class="right"><textarea name="reservation_instructions" id="reservation_instructions" class="alignright" rows="5" style="width: 100%;">' . $reservation_instructions . '</textarea></span></p>';
			$box_content .= '<div class="clear"></div><!--/.clear-->';
			
			$box_content .= '<p><label for="contact_tel" class="alignleft">' . __( 'Contact Telephone Number:', 'woothemes' ) . '</label>';
			$box_content .= '<span class="right"><input type="text" name="contact_tel" id="contact_tel" class="alignright" value="' . $contact_tel . '" style="width: 100%;" /></span></p>';
			$box_content .= '<div class="clear"></div><!--/.clear-->';
			
			$box_content .= '<p><label for="contact_email" class="alignleft">' . __( 'Contact E-mail Address:', 'woothemes' ) . '</label>';
			$box_content .= '<span class="right"><input type="text" name="contact_email" id="contact_email" class="alignright" value="' . $contact_email . '" style="width: 100%;" /></span></p>';
			$box_content .= '<div class="clear"></div><!--/.clear-->';
			
			$box_content .= '<p><label for="reservation_status" class="alignleft">' . __( 'Reservation status:', 'woothemes' ) . '</label>';
			$box_content .= '<span class="right"><select name="reservation_status" id="reservation_status">';
			
			$statuses = array( 'unconfirmed', 'confirmed', 'cancelled' );
			
			$options = '';
			
			foreach ( $statuses as $s ) {

				$selected = '';
				if ( $reservation_status == $s ) { $selected = ' selected="selected" '; } // End IF Statement										
				
				$options .= '<option value="' . $s . '"' . $selected . '>' . apply_filters( 'wootable_reservation_status_label', ucfirst( $s ) ) . '</option>' . "\n";
				
			} // End FOREACH Loop
			
			$box_content .= $options;
			
			$box_content .= '</select></span></p>' . "\n";
			$box_content .= '<div class="clear"></div><!--/.clear-->';
			
			echo $box_content;
			
		} // End meta_box_reservationdata()
		
		/*----------------------------------------
	 	  save_meta_box_reservationdata()
	 	  ----------------------------------------
	 	  
	 	  * Save the data from our meta box to
	 	  * the database.
	 	  
	 	  * Params:
	 	  * - Int $post_id
	 	----------------------------------------*/
		
		public function save_meta_box_reservationdata ( $post_id ) {
		
			global $post, $messages;
			
			// Verify  
			
			if ( !wp_verify_nonce( $_POST['wootable_' . $this->token . '_noonce'], plugin_basename(__FILE__) )) {  
				return $post_id;  
			}
			  
			if ( 'page' == $_POST['post_type'] ) {  
				if ( !current_user_can( 'edit_page', $post_id )) { 
					return $post_id;
				}
			} else {  
				if ( !current_user_can( 'edit_post', $post_id )) { 
					return $post_id;
				}
			}
			
			
			$fields = array( 'number_of_people', 'reservation_date', 'reservation_time', 'reservation_instructions', 'contact_tel', 'contact_email', 'reservation_confirmed', 'reservation_status' );
			
			foreach ( $fields as $f ) {
			
				${$f} = strip_tags(trim($_POST[$f]));
				
				
				if ( $f == 'reservation_date' && empty( ${$f} ) ) {
				
					wp_die( __( 'A reservation date is required.', 'woothemes' ) );
										
				} // End IF Statement
				
				if(get_post_meta($post_id, $f) == "") { 
					add_post_meta($post_id, $f, ${$f}, true); 
				}
				elseif(${$f} != get_post_meta($post_id, $f, true)) { 
					update_post_meta($post_id, $f, ${$f});
				}
				elseif(${$f} == "") { 
					delete_post_meta($post_id, $f, get_post_meta($post_id, $f, true) );
				}
				
			} // End FOREACH Loop
			
		} // End save_meta_box_reservationdata()
		
		/*----------------------------------------
	 	  Administration Display Functions
	 	  ----------------------------------------
	 	  
	 	  * Functions that format and customise
	 	  * the display of content in the admin
	 	  * area of our custom post type.
	 	----------------------------------------*/
		
		/*----------------------------------------
	 	  restrict_manage_posts()
	 	  ----------------------------------------
	 	  
	 	  * Add a custom filter to the
	 	  * `posts list` page for our custom
	 	  * post type.
	 	----------------------------------------*/
		
		public function restrict_manage_posts () {
		    global $wpdb;
		?>
				<form name="wootable_filter_form" id="wootable_filter_form" action="" method="get">
		    			<select name='wootable_date_filter' id='wootable_date_filter' class='postform alignleft'>
		        			<?php /*<option value=""><?php _e('All','woothemes'); ?></option>*/ ?>
		        			<option value="all" <?php if( isset($_GET['wootable_date_filter']) && $_GET['wootable_date_filter']=='all' ) echo 'selected="selected"' ?>><?php _e('All Reservations','woothemes'); ?></option>
		        			<option value="upcoming" <?php if( isset($_GET['wootable_date_filter']) && $_GET['wootable_date_filter']=='upcoming' ) echo 'selected="selected"' ?>><?php _e('Upcoming Reservations','woothemes'); ?></option>
		        		</select>
					<input type="submit" name="submit" value="<?php _e('Filter','woothemes'); ?>" class="button-secondary action alignleft" />
				</form>
			<?php
		} // End restrict_manage_posts()
		
		/*----------------------------------------
	 	  posts_join()
	 	  ----------------------------------------
	 	  
	 	  * Adjust the `JOIN` section of the
	 	  * admin MySQL query for the `posts list`
	 	  * page of our custom post type.
	 	  
	 	  * Params:
	 	  * - String $join (holds the query)
	 	----------------------------------------*/
		
		public function posts_join ( $join ) {
		
			global $wpdb;
		
			if ( is_admin() && $_GET['post_type'] == $this->token ) {
				
				$join .= "INNER JOIN $wpdb->postmeta as meta ON $wpdb->posts.ID = meta.post_id ";
				
			} // End IF Statement
			
			return $join;
			
		} // End posts_join()
		
		/*----------------------------------------
	 	  posts_where()
	 	  ----------------------------------------
	 	  
	 	  * Adjust the `WHERE` section of the
	 	  * admin MySQL query for the `posts list`
	 	  * page of our custom post type.
	 	  
	 	  * Params:
	 	  * - String $where (holds the query)
	 	----------------------------------------*/
		
		public function posts_where ( $where ) {
		    
		    global $wpdb;
		    
		   $where .= " AND meta.meta_key = 'reservation_date'";
		    
		    if( $_GET['wootable_date_filter'] == 'upcoming' && is_admin() && $_GET['post_type'] == $this->token ) {
		        
		        $current_date = date( 'Y-m-d' );
		    
		        $where .= " AND ID IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'reservation_date' AND meta_value >= '{$current_date}' GROUP BY post_id )";
		    
		    } // End IF Statement
		    
		    // echo $where;
		    
		    return $where;
		    
		} // End posts_where()
		
		/*----------------------------------------
	 	  posts_orderby()
	 	  ----------------------------------------
	 	  
	 	  * Adjust the `ORDER BY` section of the
	 	  * admin MySQL query for the `posts list`
	 	  * page of our custom post type.
	 	  
	 	  * Params:
	 	  * - String $orderby (holds the query)
	 	----------------------------------------*/
		
		public function posts_orderby ( $orderby ) {
			
			global $wpdb;
		
			if ( is_admin() && $_GET['post_type'] == $this->token ) {
				
				$orderby = "meta.meta_value ASC";
				
			} // End IF Statement
			
			return $orderby;
		
		} // End posts_orderby()
		
		/*----------------------------------------
	 	  posts_groupby()
	 	  ----------------------------------------
	 	  
	 	  * Adjust the `GROUP BY` section of the
	 	  * admin MySQL query for the `posts list`
	 	  * page of our custom post type.
	 	  
	 	  * Params:
	 	  * - String $groupby (holds the query)
	 	----------------------------------------*/
		
		public function posts_groupby ( $groupby ) {
		
			global $wpdb;
		
			if ( is_admin() && $_GET['post_type'] == $this->token ) {
				
				$groupby .= " meta.post_id ";
				
			} // End IF Statement
			
			return $groupby;
			
		} // End posts_groupby()
		
		/*----------------------------------------
	 	  create_meta_boxes()
	 	  ----------------------------------------
	 	  
	 	  * Create the custom meta box for our
	 	  * custom post type.
	 	----------------------------------------*/
		
		public function create_meta_boxes () {
		
			if ( function_exists('add_meta_box') ) { 		
				
				add_meta_box( 'reservation-data', __( 'Reservation Details', 'woothemes' ), array(&$this, 'meta_box_reservationdata'), $this->token, 'normal', 'low');
			
			} else {
				
				add_action('dbx_post_advanced', array(&$this, 'meta_box_reservationdata'));
			
			} // End IF Statement
			
			// Remove "Diner Custom Settings" meta box.
			remove_meta_box( 'woothemes-settings', 'reservation', 'normal' );
			
		} // End create_meta_boxes()
		
		/*----------------------------------------
	 	  updated_messages()
	 	  ----------------------------------------
	 	  
	 	  * Customise the update messages for our
	 	  * custom post type.
	 	----------------------------------------*/
		
		public function updated_messages ( $messages ) {
		
			$messages[$this->token] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( $this->singular_label . ' updated. <a href="%s">View ' . strtolower( $this->singular_label ) . '</a>', 'woothemes' ), esc_url( get_permalink($post_ID) ) ),
			2 => __( 'Custom field updated.', 'woothemes' ),
			3 => __( 'Custom field deleted.', 'woothemes' ),
			4 => __( $this->singular_label . ' updated.', 'woothemes' ),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __( $this->singular_label . ' restored to revision from %s', 'woothemes' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( $this->singular_label . ' published.', 'woothemes'), esc_url( get_permalink($post_ID) ) ),
			7 => __( $this->singular_label . ' saved.', 'woothemes'),
			8 => sprintf( __( $this->singular_label . ' submitted. <a target="_blank" href="%s">Preview ' . strtolower( $this->singular_label ) . '</a>' , 'woothemes'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( __( $this->singular_label . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' .strtolower( $this->singular_label ) . '</a>' , 'woothemes'),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i' , 'woothemes'), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __( $this->singular_label . ' draft updated. <a target="_blank" href="%s">Preview ' . strtolower( $this->singular_label ) . '</a>', 'woothemes'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			// CUSTOM MESSAGES
			11 => __( 'Reservation date is required. Please select or enter a date in YYYY-MM-DD format.' , 'woothemes'), 
			);
			
			// 6 -  <a href="%s">View ' . strtolower( $this->singular_label ) . '</a>
			
			return $messages;
			
		} // End updated_messages()
		
		/*----------------------------------------
	 	  add_help_text()
	 	  ----------------------------------------
	 	  
	 	  * Add contextual help text on our
	 	  * custom post type's `posts list` and
	 	  * `add/edit` screens.
	 	----------------------------------------*/
		
		public function add_help_text ( $contextual_help, $screen_id, $screen ) { 
		  
		  // $contextual_help .= var_dump($screen); // use this to help determine $screen->id
		  
		  if ( $this->token == $screen->id ) {
		  
		    $contextual_help =
		      '<p>' . __('Things to remember when adding or editing a reservation:', 'woothemes') . '</p>' .
		      '<ul>' .
		      '<li>' . __('Specify the name under which the reservation was made in the `Title` field.', 'woothemes') . '</li>' .
		      '<li>' . __('Specify the reservation date (in YYYY-MM-DD format- example, 2010-08-18). A date picker has been provided.', 'woothemes') . '</li>' .
		      '<li>' . __('Specify the number of persons for which the reservation is being made.', 'woothemes') . '</li>' .
		      '</ul>' .
		      '<p>' . __('Other important notes when making a reservation:', 'woothemes') . '</p>' .
		      '<ul>' .
		      '<li>' . __('Select the table(s) for which the reservation is being made.', 'woothemes') . '</li>' .
		      '</ul>' .
		      '<p><strong>' . __('For more information:', 'woothemes') . '</strong></p>' .
		      '<p>' . __('<a href="#" target="_blank">WooTable Documentation</a>', 'woothemes') . '</p>' .
		      '<p>' . __('<a href="#" target="_blank">WooThemes Support Forums</a>', 'woothemes') . '</p>';
		  
		  } elseif ( 'edit-' . $this->token == $screen->id ) {
		  
		    $contextual_help = 
		      '<p>' . __('This screen shows a list of all reservations made through WooTable.', 'woothemes') . '</p>' . 
		      '<p>' . __('To edit a reservation, click the name under which the reservation has been made.', 'woothemes') . '</p>';
		  
		  } // End IF Statement
		  
		  return $contextual_help;
		  
		} // End add_help_text()
		
		/*----------------------------------------
	 	  register_custom_columns_filters()
	 	  ----------------------------------------
	 	  
	 	  * Register our custom post type's
	 	  * custom column headings and data hooks.
	 	----------------------------------------*/
		
		public function register_custom_columns_filters () {
		
			add_filter( 'manage_edit-' . $this->token . '_columns', array( __CLASS__, 'add_custom_column_headings' ), 10, 1 );
			add_action( 'manage_posts_custom_column', array( __CLASS__, 'add_custom_column_data' ), 10, 2);
			
		} // End register_custom_columns_filters()
		
		/*----------------------------------------
	 	  add_custom_column_headings()
	 	  ----------------------------------------
	 	  
	 	  * Add custom column headings on
	 	  * the `posts list` page of our custom
	 	  * post type.
	 	----------------------------------------*/
		
		public static function add_custom_column_headings ( $defaults ) {
			
			$new_columns['cb'] = '<input type="checkbox" />';
 			// $new_columns['id'] = __( 'ID' );
 			$new_columns['title'] = _x( 'Reserved under the name', 'column name' );
 			$new_columns['reserveddatetime'] = __( 'Date and Time', 'woothemes' );
			$new_columns['tables'] = __( 'Table(s) Reserved', 'woothemes' );
			$new_columns['personcount'] = __( 'Number of persons', 'woothemes' );
			$new_columns['author'] = __( 'Resevation Placed With', 'woothemes' );
	 		$new_columns['date'] = _x('Reserved On', 'column name');
	 		$new_columns['confirmed'] = _x('Status', 'column name');
	 
			return $new_columns;
			
		} // End add_custom_column_headings()
		
		/*----------------------------------------
	 	  add_custom_column_data()
	 	  ----------------------------------------
	 	  
	 	  * Add data for our custom columns on
	 	  * the `posts list` page of our custom
	 	  * post type.
	 	----------------------------------------*/
		
		public static function add_custom_column_data ( $column_name, $id ) {
		
			global $wpdb, $wootable;
			
			$custom_values = get_post_custom( $id );
			
			switch ($column_name) {
			
				case 'id':
				
					echo $id;
				
				break;
				
				case 'reserveddatetime':
					
					$output = '';
					
					// Retrieve and format the date					
					$reservation_date = $custom_values['reservation_date'][0];
					$date_bits = explode( '-', $reservation_date );
					$date_formatted = date("jS F Y", mktime(0, 0, 0, $date_bits[1], $date_bits[2], $date_bits[0] ));
					
					$output .= $date_formatted;
					
					// Retrieve and format the time
					$reservation_time = $custom_values['reservation_time'][0];
					
					// Cater for 12 hour time. - 2010-11-02.
					$is_twelvehour = false;
					
					if ( get_option( $wootable->plugin_prefix . 'time_format' ) == '12' ) { $is_twelvehour = true; } // End IF Statement
					
					if ( $is_twelvehour ) {
													
						$_hour = substr( $reservation_time, 0, 2 );
						$_min = substr( $reservation_time, 3, 2 );
						
						$reservation_time = date( 'h:i A', mktime( $_hour, $_min, 0, 12, 32, 1997 ) );
						
					} // End IF Statement
					
					if ( strlen( $reservation_time ) ) { $output .= ' at ' . $reservation_time; } // End IF Statement
										
					echo $output;
				
				break;
				
				case 'tables':
					
					$tables = get_the_term_list( $id, 'tables', '', ', ', '' );
					
					echo $tables;			
					
				break;
				
				case 'personcount':
					
					$output = '';
					
					$number_of_people = (float) $custom_values['number_of_people'][0];
					
					$output .= $number_of_people;
					
					echo $output;				
				
				break;
				
				case 'confirmed':
					
					$output = '';
					
					$reservation_status = $custom_values['reservation_status'][0];
					
					$confirmation_status_message = '<p class="wootable_confirmation_message wootable_reservation_' . $reservation_status . '">' . apply_filters( 'wootable_reservation_status_label', ucfirst( $reservation_status ) ) . '</p><!--/.wootable_reservation_' . $reservation_status . '-->';
					
					$output .= $confirmation_status_message;

					echo $output;				
				
				break;
				
				default:
				break;
			
			} // End SWITCH Statement
			
		} // End add_custom_column_data()

		/*----------------------------------------
	 	  admin_scripts()
	 	  ----------------------------------------
	 	  
	 	  * Enqueue various JavaScript files
	 	  * for use in the admin area.
	 	----------------------------------------*/
		
		public function admin_scripts () {
			
			wp_enqueue_script( 'jquery-ui-core' );
			// wp_enqueue_script( 'woo-table-datepicker', $this->plugin_url . '/assets/js/jquery.ui.datepicker-mod.js', array( 'jquery', 'jquery-ui-core' ), '1.8.4', false );
			
		} // End admin_scripts()
		
		/*----------------------------------------
	 	  get_times()
	 	  ----------------------------------------
	 	  
	 	  * Get the times the restaurant is open
	 	  * on a given date, via an AJAX call.
	 	----------------------------------------*/
		
		public function get_times () {
		
			// WTFactory::display_changed_times( $this->plugin_prefix, $_POST['time'] );
			WTFactory::display_changed_times ( $this->plugin_prefix, true, $_POST['time'], 0, $_POST['date'], true );
			
			die();
		
		} // End get_times()
		
	} // End Class WooTable_PostType_Reservation
?>