<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: Frontend user-driven reservation management system for the WooTable WordPress plugin.
Date Created: 2010-08-24.
Author: Matty.
Since: 0.0.1.1


TABLE OF CONTENTS

- var $plugin_path
- var $plugin_url

- var $errors
- var $message
- var $manage_page

- function WooTable_Manager (constructor)
- function init ()
- function register_enqueues ()
- function enqueue_script ()
- function get_reservations_for_email ()
- function update_reservation ()
- function filter_content ()
- function form_processing ()
- function display_message ()
- function display_success_message ()
- function display_fail_message ()
- function display_error_messages ()

-----------------------------------------------------------------------------------*/

	class WooTable_Manager {
		
		/*----------------------------------------
	 	  Class Variables
	 	  ----------------------------------------
	 	  
	 	  * Setup of variable placeholders, to be
	 	  * populated when the constructor runs.
	 	----------------------------------------*/
	
		var $plugin_path;
		var $plugin_url;
		var $plugin_prefix;
		
		var $errors;
		var $message;
		var $manage_page;
		var $bookings_page;
	
		/*----------------------------------------
	 	  WooTable_Manager()
	 	  ----------------------------------------
	 	  
	 	  * Constructor function.
	 	  * Sets up the class and registers
	 	  * variable action hooks.
	 	  
	 	  * Params:
	 	  * - String $plugin_path
	 	  * - String $plugin_url
	 	----------------------------------------*/
	
		function WooTable_Manager ( $plugin_path, $plugin_url, $plugin_prefix ) {
		
			$this->init( $plugin_path, $plugin_url, $plugin_prefix );
			
		} // End WooTable_Manager()
		
		/*----------------------------------------
	 	  init()
	 	  ----------------------------------------
	 	  
	 	  * This guy runs the show.
	 	  * Rocket boosters... engage!
	 	----------------------------------------*/
		
		private function init ( $plugin_path, $plugin_url, $plugin_prefix ) {
			
			$this->plugin_path = $plugin_path;
			$this->plugin_url = $plugin_url;
			$this->plugin_prefix = $plugin_prefix;
			
			$this->errors = array();
			$this->message = '';
			$this->manage_page = get_option( $this->plugin_prefix . 'page_manage' );
			$this->booking_page = get_option( $this->plugin_prefix . 'page_booking' );
			
			// WPML compatibility.
			if( function_exists( 'icl_object_id' ) ) {
				$this->booking_page = icl_object_id( $this->booking_page, 'page', true );
				$this->manage_page = icl_object_id( $this->manage_page, 'page', true );
			}
			
			$this->register_enqueues();
			
			// Frontend actions and filters
			if ( ! is_admin() && WTFactory::get_saved_email() ) {
			
				add_filter( 'the_content', array( &$this, 'filter_content' ) );
				add_action( 'wp', array( &$this, 'form_processing' ) );
				
			} // End IF Statement
			
		} // End init()
		
		/*----------------------------------------
	 	  register_enqueues()
	 	  ----------------------------------------
	 	  
	 	  * A helper function to register the
	 	  * various JavaScript enqueues.
	 	----------------------------------------*/
	 	
	 	private function register_enqueues () {
	 		
	 		// Enqueue scripts and styles for the frontend
	 		add_action( 'wp_print_scripts', array( &$this, 'enqueue_script' ), null, 2 );
	 	
	 	} // End register_enqueues()
	 	
	 	/*----------------------------------------
	 	  enqueue_script()
	 	  ----------------------------------------
	 	  
	 	  * Enqueue front-end specific
	 	  * JavaScript files.
	 	----------------------------------------*/
	 	
	 	public function enqueue_script () {
	 			
	 			if ( !is_admin() ) {
	 			
		 			// Enqueue the JavaScript functions file
		 			// wp_enqueue_script('woo-table-functions', $this->plugin_url . '/assets/js/functions.js', array( 'jquery' ), '0.0.0.1', false);
	 			
	 			} // End IF Statement
	 		
	 	} // End enqueue_script()
	 	
	 	/*----------------------------------------
	 	  get_reservations_for_email()
	 	  ----------------------------------------
	 	  
	 	  * Return an array of reservations with
	 	  * a specific e-mail address as the user's
	 	  * contact e-mail.
	 	----------------------------------------*/
	 	
	 	public function get_reservations_for_email ( $email ) {
	 		
	 		global $wpdb;
	 		
	 		$email = $wpdb->escape( strtolower( $email ) );
	 		
	 		if ( ! is_email( $email ) ) { return; } // End IF Statement
	 		
	 		$reservations = array();
	 		
	 		$date = date( 'Y-m-d' );
	 		$time = date( 'h:i' );
	 		
	 		// TO DO: Run SQL query to retrieve reservations based on `$email`.
	 		$query = "SELECT ID, post_title, post_date, post_name, meta_date.meta_value as reservation_date, meta_time.meta_value as reservation_time, meta_status.meta_value as reservation_status, meta_number_of_people.meta_value as number_of_people, meta_contact.meta_value   
						FROM $wpdb->posts 
						LEFT JOIN $wpdb->postmeta as meta_date ON($wpdb->posts.ID = meta_date.post_id) 
						LEFT JOIN $wpdb->postmeta as meta_time ON($wpdb->posts.ID = meta_time.post_id) 
						LEFT JOIN $wpdb->postmeta as meta_status ON($wpdb->posts.ID = meta_status.post_id) 
						LEFT JOIN $wpdb->postmeta as meta_number_of_people ON($wpdb->posts.ID = meta_number_of_people.post_id) 
						LEFT JOIN $wpdb->postmeta as meta_contact ON($wpdb->posts.ID = meta_contact.post_id) 
						WHERE $wpdb->posts.post_type = 'reservation' 
						AND meta_date.meta_key = 'reservation_date' 
						AND meta_date.meta_value >= '$date' 
						AND meta_time.meta_key = 'reservation_time' 
						AND meta_time.meta_value >= '$time' 
						AND meta_status.meta_key = 'reservation_status' 
						AND meta_number_of_people.meta_key = 'number_of_people' 
						AND meta_contact.meta_value = '$email'
						AND $wpdb->posts.post_status = 'publish'
						GROUP BY $wpdb->posts.ID ORDER BY meta_date.meta_value, meta_time.meta_value, post_date DESC";
		 				
	 		// Execute the query
	 		$rs = $wpdb->get_results( $query, ARRAY_A );
	 		
	 		if ( $rs ) { $reservations = $rs; } // End IF Statement
	 		
	 		return $reservations;
	 		
	 	} // End get_reservations_for_email()
	 	
	 	/*----------------------------------------
	 	  update_reservation()
	 	  ----------------------------------------
	 	  
	 	  * Update a reservation's data.
	 	  
	 	  * Params:
	 	  * - Array $new_data
	 	  * - int $id
	 	----------------------------------------*/
	 	
	 	public function update_reservation ( $new_data, $id ) {

	 	} // End update_reservation()
	 	
	 	/*----------------------------------------
	 	  filter_content()
	 	  ----------------------------------------
	 	  
	 	  * Adds the booking form, validation
	 	  * notices and messages to the_content()
	 	  * on the user-selected bookings page.
	 	----------------------------------------*/
	 	
	 	public function filter_content ( $content ) {
	 		
	 		if ( $this->manage_page ) {
	 		
	 			if ( is_page( $this->manage_page ) ) {
	 			
	 				// Set a unique token, based on domain name, for our custom
	 				// $_COOKIE. This is used to differentiate between different
	 				// users using WooTable.
	 				
	 				// $_cookie_token = WTFactory::get_cookie_token();
	 			
	 				// If a $_COOKIE is set, use that value (the e-mail address).
	 				// Otherwise, check for $_GET values for both the e-mail address
	 				// and the secret key. Validate these to obtain the e-mail address
	 				// of which to retrieve upcoming reservations.
	 				
	 				$_email = '';
	 				$_email = WTFactory::get_saved_email();
	 				
	 				// If a valid e-mail address is present, display upcoming reservations
	 				// for the appropriate user.
	 				
	 				if ( isset( $_email ) && is_email( $_email ) ) {
	 			
	 					$reservations = $this->get_reservations_for_email( $_email );
	 			
	 					if ( $reservations ) {
	 						
	 						// Set the time format to be used, based in the user's setting in the admin.
								
							$is_twelvehour = false;
							
							if ( get_option( $this->plugin_prefix . 'time_format' ) == '12' ) { $is_twelvehour = true; } // End IF Statement
							
							$date_format = '';
							
							$date_format = get_option( $this->plugin_prefix . 'date_format' );
							
							if ( $date_format == '' ) { $date_format = 'jS F Y'; } // End IF Statement
	 			
	 						// Concatonate either the success, failure or error messages.
	 						$content .= $this->display_message();
	 			
			 				$content .= '<form name="wootable-booking-manager" method="post" action="">' . "\n";
			 			
								$content .= '<table id="wootable-bookings-table">' . "\n";
								
									$content .= '<thead>' . "\n";
										$content .= '<tr>' . "\n";
											$content .= '<th>' . __( 'Reservation Date', 'woothemes' ) . '</th>' . "\n";
											$content .= '<th>' . __( 'Reservation Time', 'woothemes' ) . '</th>' . "\n";
											$content .= '<th>' . __( 'Reservation Details', 'woothemes' ) . '</th>' . "\n";
											$content .= '<th></th>' . "\n";
										$content .= '</tr>' . "\n";
									$content .= '</thead>' . "\n";
									
									$content .= '<tbody>' . "\n";
									
										foreach ( $reservations as $r ) {
											
											// Calculate the human-readable time as to when the reservation was made.
											
											$timestamp_bits = explode( ' ', $r['post_date'] );
											
											$posted_date = $timestamp_bits[0];
											
												$posted_date_bits = explode( '-', $posted_date );
											
											$posted_time = $timestamp_bits[1];
											
												$posted_time_bits = explode( ':', $posted_time );
										
											$timestamp = mktime( $posted_time_bits[0], $posted_time_bits[1], $posted_time_bits[2], $posted_date_bits[1], $posted_date_bits[2], $posted_date_bits[0] );
										
											// $timestamp = strtotime( $['post_date'] );
											
											$_reservation_date_timestamp = strtotime( $r['reservation_date'] );
											$_reservation_date = date( $date_format, $_reservation_date_timestamp );
										
										$human_time_diff = human_time_diff( $timestamp, time() ) . ' ago';
										
										$_reservation_time = $r['reservation_time'];
								
										if ( $is_twelvehour ) {
																		
											$_hour = substr( $_reservation_time, 0, 2 );
											$_min = substr( $_reservation_time, 3, 2 );
											
											$_reservation_time = date( 'h:i A', mktime( $_hour, $_min, 0, 12, 32, 1997 ) );
											
										} // End IF Statement
										
										$number_of_people = $r['number_of_people'];
										
										// In "special" cases, replace the word "special" with the maximum number and a "+" sign.
								 		$_max_number_of_people = WTFactory::get_max_number_of_people()+1 . '+';
								 		
								 		if ( $number_of_people == 'special' ) {
								 		
								 			$number_of_people = $_max_number_of_people;
								 			
								 		} // End IF Statement
								 		
								 		// Change the character used to concatenate the custom query variables,
								 		// depending on whether the user has pretty permalinks on or not.
								 		
								 		$_concatenator = '&';
								 		
								 		$_permalink_structure = get_option( 'permalink_structure' );
								 		
								 		if ( $_permalink_structure != '' ) { $_concatenator = '?'; } // End IF Statement
										
										$content .= '<tr>' . "\n";
											$content .= '<td>' . $_reservation_date . '</td>' . "\n";
											$content .= '<td>' . $_reservation_time . '</td>' . "\n";
											$content .= '<td>' . "\n";
											$content .= '<p><small>' . sprintf( __( 'Placed %s for %s by %s', 'woothemes' ), '<strong>' . $human_time_diff . '</strong>', '<strong>' . $number_of_people . '</strong>' , '<strong>' . $r['post_title'] . '</strong>' ) . '</small></p>' . "\n";
											$content .= '<p class="status status_' . $r['reservation_status'] . '">Reservation status: <strong>' . apply_filters( 'wootable_reservation_status_label',  ucfirst( $r['reservation_status'] ) ) . '</strong></p>' . "\n";
											$content .= '</td>' . "\n";
											$content .= '<td>' . "\n";
													
													
													if ( $r['reservation_status'] !== 'cancelled' ) {
														$content .= '<a class="update" href="' . get_permalink( $this->booking_page ) . $_concatenator . 'action=update&id=' . $r['ID'] . '">' . __( 'Update', 'woothemes' ) . '</a>';
													} // End IF Statement
													
													if ( $r['reservation_status'] == 'unconfirmed' ) {
														$content .= '<a class="confirm" href="' . get_permalink( $this->manage_page ) . $_concatenator . 'action=confirm&id=' . $r['ID'] . '"><span>' . __( 'Confirm', 'woothemes' ) . '</span></a>';
													} // End IF Statement
													
													if ( $r['reservation_status'] !== 'cancelled' ) {
														$content .= '<a class="cancel" href="' . get_permalink( $this->manage_page ) . $_concatenator . 'action=cancel&id=' . $r['ID'] . '">' . __( 'Cancel', 'woothemes' ) . '</a>';
													} // End IF Statement
													
													if ( $r['reservation_status'] == 'cancelled' ) {
														$content .= '<span class="cancelled">' . __( 'Cancelled', 'woothemes' ) . '</span>';
													} // End IF Statement
													
											$content .= '</td>' . "\n";
										$content .= '</tr>' . "\n";
										
										} // End FOREACH Loop
										
									$content .= '</tbody>' . "\n";
								
								$content .= '</table>' . "\n";
			 				
			 				$content .= '</form>' . "\n";
	 					
	 					} else {
	 					
	 						$_message = sprintf( __( 'You have no upcoming reservations. To book one, <a href="%s">visit our online reservations form</a>.', 'woothemes' ), get_permalink( $this->booking_page ) );
	 					
	 						$_message = apply_filters( 'wootable_msg_no_reservations', $_message );	 					
	 					
	 						$content = $_message;	 					
	 					
	 					} // End IF ( $reservations ) Statement
	 				
	 				} else {
	 					
	 						$_message = sprintf( __( 'To view your upcoming reservations, you need to have made a reservation with us previously. To view your reservations, simply follow the link e-mailed to you in your reservation confirmation e-mail. To make a reservation, <a href="%s">visit our online reservations form</a>.', 'woothemes' ), get_permalink( $this->booking_page ) );
	 					
	 						$_message = apply_filters( 'wootable_msg_no_email', $_message );	 					
	 					
	 						$content = $_message;
	 				
	 				} // End IF ( isset( $_email ) ) Statement
	 				
	 			} // End IF Statement
	 			
	 		} // End IF Statement
	 		
	 		return $content;
	 			 	
	 	} // End filter_content()
		
		/*----------------------------------------
	 	  form_processing()
	 	  ----------------------------------------
	 	  
	 	  * Wrapper for validating and processing
	 	  * the actions on the management page.
	 	  
	 	  * Globals:
	 	  * - wootable
	 	----------------------------------------*/
	 	
	 	public function form_processing () {
	 		
	 		global $wootable;
	 		
	 		$_is_acted = false;
	 		$_actions = array( 'confirm', 'cancel' );
	 		
	 		if ( isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $_actions ) && isset( $_REQUEST['id'] ) && is_numeric( $_REQUEST['id'] ) ) {
	 		
	 			$_id = (int) $_REQUEST['id'];
	 			$_current_action = strtolower( strip_tags( $_REQUEST['action'] ) );
	 			
	 			$_current_status = get_post_meta( $_id, 'reservation_status', true );
	 			
	 			switch ( $_current_action ) {
	 			
	 				case 'confirm':
	 				
	 					if ( $_current_status != 'confirmed' ) {
	 				
	 						$_is_acted = $this->change_status( $_id, 'confirmed' );
	 						
	 					} // End IF Statement
	 				
	 				break;
	 				
	 				case 'cancel':
	 				
	 					if ( $_current_status != 'cancelled' ) {
	 				
	 						$_is_acted = $this->change_status( $_id, 'cancelled' );
	 						
	 					} // End IF Statement
	 				
	 				break;
	 				
	 			} // End SWITCH Statement
	 			
	 			if ( $_is_acted == true ) {
	 							
	 				$_is_user_emailed = $wootable->frontend->send_statuschange_email( 'user', $_id );
	 				$_is_admin_emailed = $wootable->frontend->send_statuschange_email( 'admin', $_id );
	 			
	 			} // End IF Statement
	 			
	 		} // End IF Statement
	 		
	 	} // End form_processing()
	 	
	 	/*----------------------------------------
	 	  change_status()
	 	  ----------------------------------------
	 	  
	 	  * Changes the status of a reservation
	 	  * from one state to another, if the
	 	  * new state is different from the current.
	 	  
	 	  * Params:
	 	  * - int $id
	 	  * - String $new_state
	 	----------------------------------------*/
	 	
	 	private function change_status ( $id, $new_state ) {
	 		
	 		$_is_changed = false;
	 		
	 		// Make sure a reservation with this $id already exists.
	 		$_reservation = get_post( $id );
	 		
	 		if ( $_reservation ) {
	 		
	 			$_current_state = get_post_meta( $_reservation->ID, 'reservation_status', true );
	 		
	 			if ( $_current_state != $new_state ) {
	 		
	 				$_is_changed = update_post_meta( $_reservation->ID, 'reservation_status', $new_state );
	 				
	 				switch( $new_state ) {
	 				
	 					case 'cancelled':
	 					
	 						$this->message = __( 'Your reservation has been cancelled successfully.', 'woothemes' );
	 					
	 					break;
	 					
	 					case 'confirmed':
	 					
	 						$this->message = __( 'Your reservation has been confirmed successfully.', 'woothemes' );
	 					
	 					break;
	 					
	 					default:
	 						
	 						$this->message = __( 'The status of your reservation has been changed successfully.', 'woothemes' );
	 					
	 					break;
	 				
	 				} // End SWITCH Statement
	 				
	 			/*} else {
	 				
	 				$_is_changed = true;
	 			*/	
	 			} // End IF Statement
	 			
	 		} // End IF Statement
	 		
	 			// header( "Location:" . get_permalink( $this->manage_page ) );
	 		
	 		return $_is_changed;
	 		
	 	} // End change_status()
	 	
	 	/*----------------------------------------
	 	  display_message()
	 	  ----------------------------------------
	 	  
	 	  * Displays a message to the user after
	 	  * their booking has been e-mailed through.
	 	  
	 	  * Params:
	 	  * - Boolean $status
	 	----------------------------------------*/
	 	
	 	public function display_message () {
	 		
	 		// TO DO
	 		
	 		$content = '';
	 		
	 		if ( count( $this->errors ) ) {
	 		
	 			$content = $this->display_error_messages();
	 			
	 		} // End IF Statement
	 		
	 		if ( $this->message && ! $this->errors ) {
	 		
	 			$content = '<p class="woo-sc-box note">' . $this->message . '</p>' . "\n";
	 			
	 		} // End IF Statement
	 		
	 		return $content;
	 		
	 	} // End display_message()
	 	
	 	/*----------------------------------------
	 	  display_success_message()
	 	  ----------------------------------------
	 	  
	 	  * The message to display if the booking
	 	  * has been added successfully.
	 	----------------------------------------*/
	 	
	 	public function display_success_message () {
	 		
	 		// TO DO
	 		
	 		$content = __( 'Your reservation has been booked successfully.', 'woothemes' );
	 		
	 		$content = apply_filters( 'wootable_success_message', $content );
	 		
	 		return $content;
	 		
	 	} // End display_success_message()
	 	
	 	/*----------------------------------------
	 	  display_fail_message()
	 	  ----------------------------------------
	 	  
	 	  * The message to display if the booking
	 	  * has not been added successfully.
	 	----------------------------------------*/
	 	
	 	public function display_fail_message () {
	 		
	 		// TO DO
	 		
	 		$content = __( 'An issue was encountered while attempting to place your reservation.', 'woothemes' );
	 		
	 		$content = apply_filters( 'wootable_fail_message', $content );
	 		
	 		return $content;
	 		
	 	} // End display_fail_message()
	 	
	 	/*----------------------------------------
	 	  display_error_messages()
	 	  ----------------------------------------
	 	  
	 	  * Displays error messages, if any are
	 	  * present in the $errors array.
	 	----------------------------------------*/
	 	
	 	public function display_error_messages () {
	 		
	 		// TO DO
	 		
	 		$content = '';
	 		
	 		if ( $this->errors ) {
			
				$content .= '<div class="error fade"><p>' . __( 'Please correct the following', 'woothemes' ) . ':</p>' . "\n";
			
					$content .= '<ul class="messages">' . "\n";
			
					foreach ( $this->errors as $e ) {
					
						$content .= '<li>' . $e . '</li>' . "\n";
						
					} // End FOREACH Loop
					
					$content .= '</ul>' . "\n";
				
				$content .= '</div><!--/.error fade-->' . "\n";
				
			} // End IF Statement
	 		
	 		return $content;
	 		
	 	} // End display_error_messages()
		
	} // End Class WooTable_Manager
?>