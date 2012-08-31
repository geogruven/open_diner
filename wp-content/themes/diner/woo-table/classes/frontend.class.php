<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: Frontend reservation booking system for the WooTable WordPress plugin.
Date Created: 2010-08-30.
Author: Matty.
Since: 0.0.1.2


TABLE OF CONTENTS

- var $plugin_path
- var $plugin_url
- var $plugin_prefix

- var $errors
- var $message
- var $bookings_page

- var $fields_detail
- var $fields_for_form

- function WooTable_FrontEnd (constructor)
- function init ()
- function filter_content ()
- function validate_booking ()
- function process_booking ()
- function send_customer_email ()
- function send_admin_email ()
- function send_statuschange_email ()
- function send_mail ()
- function display_message ()
- function display_specialrequest_message ()
- function display_success_message ()
- function display_fail_message ()
- function display_error_messages ()
- function form_processing ()
- function ajax_functions ()

- function get_table_to_seat ()

- function register_enqueues ()
- function enqueue_script ()

-----------------------------------------------------------------------------------*/

	class WooTable_FrontEnd {
		
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
		var $bookings_page;
		var $manage_page;
		
		var $fields_detail;
		var $fields_for_form;
	
		/*----------------------------------------
	 	  WooTable_FrontEnd()
	 	  ----------------------------------------
	 	  
	 	  * Constructor function.
	 	  * Sets up the class and registers
	 	  * variable action hooks.
	 	  
	 	  * Params:
	 	  * - String $plugin_path
	 	  * - String $plugin_url
	 	----------------------------------------*/
	
		function WooTable_FrontEnd ( $plugin_path, $plugin_url, $plugin_prefix ) {
		
			$this->init( $plugin_path, $plugin_url, $plugin_prefix );
			
		} // End WooTable_FrontEnd()
		
		/*----------------------------------------
	 	  init()
	 	  ----------------------------------------
	 	  
	 	  * This guy runs the show.
	 	  * Rocket boosters... engage!
	 	----------------------------------------*/
		
		public function init ( $plugin_path, $plugin_url, $plugin_prefix ) {
			
			global $wootable;
			
			$this->plugin_path = $plugin_path;
			$this->plugin_url = $plugin_url;
			$this->plugin_prefix = $plugin_prefix;
			
			$this->errors = array();
			$this->message = '';
			$this->bookings_page = get_option( $this->plugin_prefix . 'page_booking' );
			$this->manage_page = get_option( $this->plugin_prefix . 'page_manage' );
			
			// WPML compatibility.
			if( function_exists( 'icl_object_id' ) ) {
				$this->bookings_page = icl_object_id( $this->bookings_page, 'page', true );
				$this->manage_page = icl_object_id( $this->manage_page, 'page', true );
			}
			
			// Frontend actions and filters
			if ( ! is_admin() ) {
			
				add_filter( 'the_content', array( &$this, 'filter_content' ) );
				add_action( 'wp', array( &$this, 'form_processing' ) );
			
				// if ( is_page( $this->bookings_page ) ) {
				
					$this->register_enqueues();
					
				// } // End IF Statement
			
				// If the system is trying to make an AJAX call, include our AJAX functions.
				if ( isset( $_POST['ajax'] ) ) {
					
					$this->ajax_functions();
					
				} // End IF Statement
				
			} // End IF Statement
			
		} // End init()
		
		/*----------------------------------------
	 	  filter_content()
	 	  ----------------------------------------
	 	  
	 	  * Adds the booking form, validation
	 	  * notices and messages to the_content()
	 	  * on the user-selected bookings page.
	 	----------------------------------------*/
	 	
	 	public function filter_content ( $content ) {
	 		
	 		if ( $this->bookings_page ) {
	 		
	 			// Set the default as "we're not on an update screen".
	 			$_is_update = false;
	 		
	 			if ( is_page( $this->bookings_page ) ) {
	 				
	 				// Concatonate either the success, failure or error messages.
	 				if ( ( isset($_POST['reservation_widget']) ) && ( $_POST['reservation_widget'] == 'widget' ) && ( ! isset( $_REQUEST['action'] ) ) ) {
	 					
	 					$content .= '<p class="woo-sc-box note">' . __( 'Please fill out the form below to complete your booking.', 'woothemes' ) . '</p>';
	 				
	 				// UPDATE AN EXISTING BOOKING - 2010-11-04.
	 				
	 				} else if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'update' && isset( $_REQUEST['id'] ) && is_numeric( $_REQUEST['id'] ) && ! isset( $_REQUEST['is_updated'] ) ) {
	 				
	 					$content .= '<p class="woo-sc-box note">' . __( 'Please fill out the form below to update your existing booking.', 'woothemes' ) . '</p>';
	 					
	 					// Get the data for the existing booking.
	 					
	 					$_id = (int) $_REQUEST['id'];
	 					
	 					$_bookingdata = get_post_custom( $_id );
	 					
	 					$_fields = array( 'reservation_date', 'reservation_time', 'number_of_people', 'contact_tel', 'contact_name', 'contact_email', 'reservation_instructions' );
	 					
	 					foreach ( $_fields as $_f ) {
	 					
	 						$this->fields_for_form[$_f] = $_bookingdata[$_f][0];
	 						
	 					} // End FOREACH Loop
	 					
	 					// Set a flag for if we're on an update screen.
	 					$_is_update = true;
	 					
	 				} else {
	 					
	 					$content .= $this->display_message();
	 					
	 				} // End IF Statement
	 				
	 				$content .= '<form name="wootable-booking-form" method="post" action="">' . "\n";
	 			
	 					$reservation_date_real = $this->fields_for_form['reservation_date'];
	 					
	 					if ( ! $reservation_date_real ) { $reservation_date_real = date( 'Y-m-d' ); } // End IF Statement
	 					
	 					$content .= '<input type="hidden" name="page_id" id="page_id" class="input-page_id input-text" value="' . $this->bookings_page . '" />' . "\n";
	 					$content .= '<input type="hidden" name="is_update" id="is_update" class="input-is_update input-text" value="' . $_is_update . '" />' . "\n";
	 					
		 				$content .= '<div id="wootable-calendar-holder"><h3>'. __('Select the date for your reservation:','woothemes') .'</h3><div id="wootable-calendar"></div></div><!--/#wootable-calendar-holder-->' . "\n";
		 				$content .= '<input type="hidden" name="reservation_date" id="reservation_date" class="input-reservation_date input-text required" value="' . $this->fields_for_form['reservation_date'] . '" />' . "\n";
		 				$content .= '<input type="hidden" name="reservation_date_real" id="reservation_date_real" class="input-reservation_date_real input-text required" value="' . $reservation_date_real . '" />' . "\n";
		 			
		 				// Begin the bookings form.
		 				$content .= '<div class="reservations-form">' . "\n";
		 				$content .= '<h3>'. __('Reservation Information:','woothemes') .'</h3>' . "\n";
		 				
		 				$_max_number_of_people = WTFactory::get_max_number_of_people();
		 				
		 				$content .= '<p class="form-field">' . "\n";
			 				$content .= '<span class="people"><label for="number_of_people">' . __( 'People', 'woothemes') . ':</label>' . "\n";
			 				
			 				if ( $_max_number_of_people ) {
			 					
			 					$content .= '<select name="number_of_people" class="number_of_people required">' . "\n";
		 						
		 							$selected_number = $this->fields_for_form['number_of_people'];
		 						
		 							for ( $i = 1; $i <= $_max_number_of_people; $i++ ) {
		 								
		 								$_selected = '';
		 								
		 								if ( $i == $selected_number ) { $_selected = ' selected="selected"'; } // End IF Statement
		 								
		 								$content .= '<option value="' . $i . '"' . $_selected . '>' . $i . '</option>' . "\n";
		 								
		 							} // End FOR Loop
		 						
		 							// Allow for reservations of numbers greater than the maximum that a single table
		 							// can handle, but e-mail the request to the restaurant instead of placing the
		 							// reservation in the system.
		 						
		 							$_selected = '';
		 						
		 							$_special_number = $_max_number_of_people+1;
		 						
		 							if ( 'special' == $selected_number ) { $_selected = ' selected="selected"'; } // End IF Statement
		 						
		 							$content .= '<option value="special"' . $_selected . '>' . $_special_number . '+' . '</option>' . "\n";
		 						
		 						$content .= '</select></span>' . "\n";
			 					
			 					
			 				} else {
			 				
			 					$content .= __( 'No tables are currently listed. Please check back soon.', 'woothemes' );
			 					
			 				} // End IF Statement
			 			
			 				$content .= '<span class="time"><label for="reservation_time">' . __( 'Time', 'woothemes' ) . ':</label>' . "\n";
			 				
			 				$content .= WTFactory::display_changed_times( $this->plugin_prefix, false, $this->fields_for_form['reservation_time'], 0, $this->fields_for_form['reservation_date'], $_is_update );
			 				
			 				/*
			 				$business_hours = WTFactory::get_business_hours( $this->plugin_prefix );
			 				
			 				$index = strtolower( date('D', strtotime($reservation_date_real) ) );
		 					
		 					// Compensate for the colloquial convention of "thurs" instead of "thu", and "tues" instead of "tue".
		 					if ( $index == 'thu' ) { $index = 'thurs'; } // End IF Statement
		 					if ( $index == 'tue' ) { $index = 'tues'; } // End IF Statement
		 					
		 					$times = $business_hours[$index];
			 				
			 				$times_array = WTFactory::get_times_between( $times['openingtime'], $times['closingtime'], $this->plugin_prefix, $reservation_date_real ); // 2010-11-01.
	 						
	 						if ( $times_array ) {
	 						
		 						$content .= '<select name="reservation_time" class="reservation_time required">' . "\n";
		 						
		 							$selected_hour = $this->fields_for_form['reservation_time'];
		 						
		 							foreach ( $times_array as $t ) {
		 								
		 								$_selected = '';
		 								
		 								if ( $t == $selected_hour ) { $_selected = ' selected="selected"'; } // End IF Statement
		 								
		 								$content .= '<option value="' . $t . '"' . $_selected . '>' . $t . '</option>' . "\n";
		 								
		 							} // End FOREACH Loop
		 						
		 						$content .= '</select></span>' . "\n";
			 				
	 						} // End IF Statement
	 						*/

		 				$content .= '</p>' . "\n";
		 				
		 				$content .= '<p class="form-field name">' . "\n";
			 				$content .= '<label for="contact_name">' . __( 'Name', 'woothemes') . ':</label>' . "\n";
			 				$content .= '<input type="text" name="contact_name" class="input-title input-text required" value="' . $this->fields_for_form['contact_name'] . '" />' . "\n";
		 				$content .= '</p>' . "\n";
		 				
		 				$content .= '<p class="form-field">' . "\n";
			 				$content .= '<span class="phone"><label for="contact_tel">' . __( 'Phone', 'woothemes') . ':</label>' . "\n";
			 				$content .= '<input type="text" name="contact_tel" class="input-contact_tel input-text required" value="' . $this->fields_for_form['contact_tel'] . '" /></span>' . "\n";
		 				
		 				// Remember the user's e-mail address, if they have been here before and have not yet filled in the form.
		 				$_email = $this->fields_for_form['contact_email'];
		 				if ( ! $_email ) { $_email = WTFactory::get_saved_email(); } // End IF Statement
		 				
			 				$content .= '<span class="email"><label for="contact_email">' . __( 'Email', 'woothemes') . ':</label>' . "\n";
			 				$content .= '<input type="text" name="contact_email" class="input-contact_email input-text required email" value="' . $_email . '" /></span>' . "\n";
		 					// $content .= '<em>Inputting your e-mail address allows you to track your reservations online.</em>';
		 				$content .= '</p>' . "\n";
		 				
		 				$content .= '<p class="form-field notes">' . "\n";
			 				$content .= '<label for="reservation_instructions">' . __( 'Notes', 'woothemes') . ':</label>' . "\n";
			 				$content .= '<textarea name="reservation_instructions" class="textarea-reservation_instructions textarea">' . $this->fields_for_form['reservation_instructions'] . '</textarea>' . "\n";
		 				$content .= '</p>' . "\n";
		 				
		 				$_no_cookie = '';
		 				if ( $this->fields_for_form['no_cookie'] ) {
		 				
		 					$_no_cookie = ' checked="checked"';
		 				
		 				} // End IF Statement
		 				
		 				$content .= '<p class="form-field submit">' . "\n";
			 				$content .= '<label for="no_cookie">' . __( 'I\'m at a public computer', 'woothemes') . '</label>' . "\n";
			 				$content .= '<input type="checkbox" name="no_cookie" class="input-no_cookie input-checkbox" value="1"' . $_no_cookie . ' />' . "\n";
		 					// $content .= '<em>Inputting your e-mail address allows you to track your reservations online.</em>';
		 				
		 				// Generate a confirmation message based on the data entered on the form.
					
						$_message = '';
						
						$_friendly_date = date( 'l, F jS', strtotime( $reservation_date_real ) );
						
						$_friendly_time = ' at ';
						
						if ( $this->fields_for_form['reservation_time'] ) { $_friendly_time .= $this->fields_for_form['reservation_time']; }
						else if ( is_array( $times_array ) ) { $_friendly_time .= $times_array[0]; } // End IF Statement
						
						$_friendly_number = '';
						
						if ( $this->fields_for_form['number_of_people'] ) { $_friendly_number = $this->fields_for_form['number_of_people']; }
						else { $_friendly_number = 1; } // End IF Statement
						
						$_message .= $_friendly_date . $_friendly_time . ' for a party of ' . $_friendly_number;
						
						// $content .= '<p>' . __( 'Current reservation:', 'woothemes' ) . ' <span class="confirmation_message">' . $_message . '</span>.</p>';
		 				
		 				$content .= '<input type="hidden" name="confirmation_message" class="confirmation_message" value="' . $_message . '" />' . "\n";
		 				
		 				if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'update' && isset( $_REQUEST['id'] ) && is_numeric( $_REQUEST['id'] ) ) {
				 		
				 			$content .= '<input type="hidden" name="is_updated" value="' . 'yes' . '" />' . "\n";
				 		
				 		} // End IF Statement
		 				
		 					if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'update' ) {
		 						
		 						$_button_text = get_option( $this->plugin_prefix . 'view_button_text' );
		 						
		 						if ( $_button_text == '' ) { $_button_text = __( 'View Reservations', 'woothemes' ); } // End IF Statement
		 						
		 						$content .= '<a href="' . get_permalink( $this->manage_page ) . '" class="button inactive">' . $_button_text . '</a>' . "\n";
		 						
		 					} // End IF Statement
			 				
			 				$_button_text = get_option( $this->plugin_prefix . 'reserve_button_text' );
		 						
		 					if ( $_button_text == '' ) { $_button_text = __( 'Reserve Table', 'woothemes' ); } // End IF Statement
			 				
			 				$content .= '<input class="button button-submit" type="submit" value="' . $_button_text . '" />' . "\n";
		 				$content .= '</p>' . "\n";
		 				
		 				$content .= '</div><!-- /#reservations-form -->' . "\n";
	 				
	 				$content .= '</form>' . "\n";
	 				$content .= '<div class="modal-content">' . get_option( $this->plugin_prefix . 'confirmation_box_message' ) . '</div><!--/.modal-content-->' . "\n";
	 			} // End IF Statement
	 			
	 		} // End IF Statement
	 		
	 		return $content;
	 			 	
	 	} // End filter_content()
	 	
	 	/*----------------------------------------
	 	  validate_booking()
	 	  ----------------------------------------
	 	  
	 	  * Validates a single booking.
	 	----------------------------------------*/
	 	
	 	public function validate_booking () {
	 			 		
	 		$is_valid = false;
	 		$business_hours = get_option( $this->plugin_prefix . 'business_hours' );
	 		
	 		// Check if the restaurant is closed on the selected date. If it is, don't bother with
	 		// the other calculations.
	 		
	 		$attempted_date = $this->fields_for_form['reservation_date'];
	 		$attempted_date_day = strtolower( date( "D", mktime(0, 0, 0, substr( $attempted_date, 5, 2 ), substr( $attempted_date, 8, 2 ), substr( $attempted_date, 0, 4 ) ) ) );
	 		
	 		if ( is_array( $business_hours ) && array_key_exists( $attempted_date_day, $business_hours ) ) {
	 		
	 			if ( $business_hours[$attempted_date_day]['closed'] == 1 ) {
	 			
	 				$this->errors[] = sprintf( __( 'Unfortunately, we are closed on %ss. Please select a different date.', 'woothemes' ), date( "l", mktime(0, 0, 0, substr( $attempted_date, 5, 2 ), substr( $attempted_date, 8, 2 ), substr( $attempted_date, 0, 4 ) ) ) );
	 				
	 				$is_valid = false;
	 				
	 				return $is_valid;
	 				
	 			} // End IF Statement
	 			
	 		// Lets also run some checks to see if the restaurant is open at the time selected.
	 		// You know, just to be safe and all.
	 			
	 		} // End IF Statement
	 		
	 		foreach ( $this->fields_detail as $field ) {
				
				if ( $field['required'] == 1 ) {
				
					if ( !isset( $_POST[$field['field']] ) || $this->fields_for_form[$field['field']] == '' ) {
					
						$this->errors[] = $field['message'];
					
					} // End IF Statement
					
					if ( $field['type'] == 'email' && ! is_email( $this->fields_for_form[$field['field']] ) ) {
				
						$this->errors[] = __( 'The email address provided is invalid. Please re-enter your email address.', 'woothemes' );

					} // End IF Statement
					
					// Date field validation
					if ( $field['type'] == 'date' ) {
					
						$is_valid_date = true;
					
						$date = trim( strip_tags( $this->fields_for_form[$field['field']] ) );
						$date_bits = explode( '-', $date );
						
						$y = $date_bits[0];
						$m = $date_bits[1];
						$d = $date_bits[2];
						
						if ( strlen( $y ) == 4 && strlen( $m ) == 2 && strlen( $d ) == 2 && $m <= 12 && $d <= 31 ) {
							
							$is_valid_date = true;
						
						} else {
							
							$is_valid_date = false;
							
						} // End IF Statement
						
						// A reservation cannot be made for the past. This isn't "Back to the Future", or something!
						if ( $date < date('Y-m-d') ) {
						
							$this->errors[] = __( 'A reservation cannot be made for a date that has already been.', 'woothemes' );
							$is_valid_date = false;
							
						} // End IF Statement
						
						if ( ! $is_valid_date ) {
							
							$this->errors[] = __( 'Please enter a valid date in YYYY-MM-DD format.', 'woothemes' );
							
						} // End IF Statement
						
					} // End IF Statement
					
					// Time field validation
					if ( $field['type'] == 'time' ) {
					
						$is_valid_time = true;
					
						$time = trim( strip_tags( $this->fields_for_form[$field['field']] ) );
						$time_bits = explode( ':', $time );
						
						$h = $time_bits[0];
						$m = $time_bits[1];
						
						if ( strlen( $h ) == 2 && strlen( $m ) == 2 && $h <= 23 && $m <= 59 ) {
							
							$is_valid_time = true;
						
						} else {
							
							$is_valid_time = false;
							
						} // End IF Statement
						
						if ( ! $is_valid_time ) {
							
							$this->errors[] = __( 'Please specify a valid time in HH:MM format.', 'woothemes' );
							
						} // End IF Statement
						
					} // End IF Statement
					
					// Int field validation
					if ( $field['type'] == 'int' ) {
											
						if ( ! is_numeric( $this->fields_for_form[$field['field']] ) && $this->fields_for_form[$field['field']] != '' ) {
							
							$field_label = ucfirst( str_replace( '_', ' ', $field['field'] ) );
							
							$this->errors[] = __( 'Please enter a valid number for "' . $field_label . '".', 'woothemes' );
							
						} // End IF Statement
						
					} // End IF Statement
					
					// Number of people field validation
					if ( $field['type'] == 'number_of_people' ) {
											
						if ( ! is_numeric( $this->fields_for_form[$field['field']] ) && $this->fields_for_form[$field['field']] != '' ) {
							
							if ( $this->fields_for_form[$field['field']] == 'special' ) {} else {
							
								$field_label = ucfirst( str_replace( '_', ' ', $field['field'] ) );
								
								$this->errors[] = __( 'Please select a valid number for "' . $field_label . '".', 'woothemes' );
							
							} // End IF Statement
							
						} // End IF Statement
						
					} // End IF Statement
				
				} // End IF Statement

			} // End FOREACH Loop
	 		
	 		if ( count( $this->errors ) < 1 ) {
	 		
	 			$is_valid = true;
	 			
	 		} // End IF Statement
	 		
	 		return $is_valid;
	 		
	 	} // End validate_booking()
	 	
	 	/*----------------------------------------
	 	  process_booking()
	 	  ----------------------------------------
	 	  
	 	  * Processes a single booking and assign
	 	  * it to the best available table.
	 	  
	 	  * Params:
	 	  * - int $available_table
	 	----------------------------------------*/
	 	
	 	public function process_booking ( $available_table ) {
	 		
	 		// global $wootable;
	 		
	 		$is_processed = false;
	 		
	 		// If the reservation is a `special` reservation,
	 		// e-mail the restaurant administrator and the customer
	 		// with notifications of the reservation instead
	 		// of processing it in the system.
	 		
	 		if ( $this->fields_for_form['number_of_people'] == 'special' ) {
	 			
	 			$_admin_message = '';
	 			
	 			$_message_from_db = get_option( $this->plugin_prefix . 'adminemail_specialrequest' );
	 			
	 			$_admin_message .= $_message_from_db;
	 			
	 			if ( $_admin_message == '' ) {
	 			
		 			$_admin_message .= 'Hey There!' . "\n";
		 			$_admin_message .= '[contact_name] requested a reservation for [number_of_people] on [reservation_date] at [reservation_time].' . "\n";
		 			$_admin_message .= 'The following notes were made:' . "\n\n";
		 			$_admin_message .= '[reservation_instructions]' . "\n\n";
		 			$_admin_message .= 'To discuss this reservation further, please follow up with [contact_name] either via telephone on [contact_tel] or e-mail on [contact_email].' . "\n";
		 			$_admin_message .= 'Sincerely,' . "\n";
		 			$_admin_message .= '[restaurant_name] Reservations.' . "\n";	
	 			
	 			} // End IF Statement
	 			
	 			// $_admin_message .= $wootable->default_emails['admin_specialrequest'];
	 			
	 			$_admin_message = apply_filters( 'wootable_email_admin_message_special', $_admin_message );
	 			
	 			$_customer_message = '';
	 			
	 			$_message_from_db = get_option( $this->plugin_prefix . 'email_specialrequest' );
	 			
	 			$_customer_message .= $_message_from_db;
	 			
	 			if ( $_customer_message == '' ) {
	 			
		 			$_customer_message .= 'Dear [contact_name],' . "\n";
		 			$_customer_message .= 'You recently requested a reservation at [restaurant_name] for [number_of_people] on [reservation_date] at [reservation_time].' . "\n";
		 			$_customer_message .= 'The following notes were left for the manager:' . "\n\n";
		 			$_customer_message .= '[reservation_instructions]' . "\n\n";
		 			$_customer_message .= 'To discuss this reservation further, a manager at [restaurant_name] will follow up with you either via telephone (you left [contact_tel] as your contact number) or e-mail (on [contact_email]).' . "\n";
		 			$_customer_message .= 'Sincerely,' . "\n";
		 			$_customer_message .= '[restaurant_name] Reservations.' . "\n";
		 			
		 		} // End IF Statement
	 			
	 			// $_customer_message .= $wootable->default_emails['specialrequest'];
	 			
	 			$_customer_message = apply_filters( 'wootable_email_customer_message_special', $_customer_message );
	 			
	 			$this->send_admin_email( $_admin_message );
	 			$this->send_customer_email( $this->fields_for_form['contact_email'], $_customer_message );
	 		
	 			$is_processed = true;
	 			
	 			return $is_processed;
	 			
	 		} // End IF Statement
	 		
	 		if ( ! $available_table ) { return $is_processed; } // End IF Statement // ORIGINAL PLACEMENT - 2010-11-04.
	 		
	 		// Prepare data for insertion into the database.
	 		$reservation_data = array(
	 									'post_title' => $this->fields_for_form['contact_name'], 
	 									'post_type' => 'reservation', 
	 									'post_status' => 'publish'
	 								);
	 		
	 		// UPDATE AN EXISTING BOOKING - 2010-11-04.
	 				
	 		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'update' && isset( $_REQUEST['id'] ) && is_numeric( $_REQUEST['id'] ) ) {
	 			
	 			$_id = (int) $_REQUEST['id'];
	 			
	 			$_existing_data = get_post( $_id );
	 			
	 			if ( $_existing_data ) {
	 			
	 				$_new_data = array( 'ID' => $_id );
	 				
	 				$_update_info = array_merge( $_new_data, $reservation_data );
	 				
	 				$reservation = wp_update_post( $_update_info );
	 				
	 				// Unassign the tables currently assigned to this reservation.
	 				
	 				wp_delete_object_term_relationships( $_id, 'tables' );
	 				
	 			} // End IF Statement
	 			
	 		} else {
	 							
	 			$reservation = wp_insert_post( $reservation_data );
	 		
	 		} // End IF Statement
	 		
	 		if ( is_numeric( $reservation ) ) {
	 		
	 			$meta_fields = array( 'reservation_date', 'reservation_time', 'reservation_instructions', 'number_of_people', 'contact_name', 'contact_tel', 'contact_email', 'reservation_status' );
	 			
	 			// Set the reservation status to `confirmed` by default.
	 			$_defaultstatus = get_option( $this->plugin_prefix . 'default_status' );
	 			
	 			// Whitelist check on status options. Set to `unconfirmed` if it doesn't pass the test.
	 			
	 			if ( ! in_array( $_defaultstatus, array( 'confirmed', 'unconfirmed' ) ) ) { $_defaultstatus = 'unconfirmed'; } // End IF Statement
	 			
	 			
	 			$this->fields_for_form['reservation_status'] = $_defaultstatus; // 'unconfirmed'
	 			
	 			foreach ( $meta_fields as $m ) {
	 		
	 				if ( $this->fields_for_form[$m] ) { update_post_meta( $reservation, $m, $this->fields_for_form[$m] ); } // End IF Statement
	 				
	 			} // End FOREACH Loop
	 			
	 			// Assign the reservation to it's selected table.
	 			$assign_to_tables = wp_set_object_terms( $reservation, array( (int) $available_table ), 'tables', false );
	 			
	 		} // End IF Statement
	 		
	 		$is_processed = true;
	 		
	 		// Assign the new reservation to the best available table.
	 		
	 		return $is_processed;
	 		
	 	} // End process_booking()
	 	
	 	/*----------------------------------------
	 	  send_customer_email()
	 	  ----------------------------------------
	 	  
	 	  * A wrapper function for send_email()
	 	  * that e-mails the booking confirmation
	 	  * to the customer.
	 	  
	 	  * Params:
	 	  * - String $to
	 	----------------------------------------*/
	 	
	 	public function send_customer_email ( $to, $message = '' ) {
	 		
	 		global $wootable;
	 		
	 		$date_format = '';
			
			$date_format = get_option( $this->plugin_prefix . 'date_format' );
			
			if ( $date_format == '' ) { $date_format = 'jS F Y'; } // End IF Statement
			
			$_reservation_date_timestamp = strtotime( $this->fields_for_form['reservation_date'] );
			$_reservation_date = date( $date_format, $_reservation_date_timestamp );
	 		
	 		$_codes = array(
	 						'[restaurant_name]' => get_option('blogname'), 
	 						'[number_of_people]' => $this->fields_for_form['number_of_people'], 
	 						'[reservation_time]' => $this->fields_for_form['reservation_time'], 
	 						'[reservation_date]' => $_reservation_date, 
	 						'[reservation_instructions]' => $this->fields_for_form['reservation_instructions'], 
	 						'[contact_name]' => $this->fields_for_form['contact_name'], 
	 						'[contact_tel]' => $this->fields_for_form['contact_tel'], 
	 						'[contact_email]' => $this->fields_for_form['contact_email'] 
	 					);
	 		
	 		
	 		$subject = '[' . get_option('blogname') . ']: Booking request';
	 		
	 		$subject = apply_filters('wootable_email_customer_subject', $subject);
	 		
	 		if ( ! $message ) {
	 				
		 		// Check what the default reservation status is.
		 		$_defaultstatus = get_option( $this->plugin_prefix . 'default_status' );
	 			
	 			$message = '';
	 			
	 			switch ( $_defaultstatus ) {
	 			
	 				case 'confirmed':
	 				
	 					$message = get_option( $this->plugin_prefix . 'email_thankyou' );
	 					$default = $wootable->default_emails['thankyou'];
	 				
	 				break;
	 				
	 				case 'unconfirmed':
	 				
	 					$message = get_option( $this->plugin_prefix . 'email_pleaseconfirm' );
	 					$default = $wootable->default_emails['pleaseconfirm'];
	 				
	 				break;
	 				
	 			} // End SWITCH Statement
	 			
	 		} // End IF Statement
	 		
	 		// If no custom message is present, use the default.
	 		
	 		if ( $message == '' ) {
	 		
	 			/*
		 		$message = '';
		 		$message .= 'Dear [contact_name],' . "\n\n";
		 		$message .= 'Thank you for your reservation at [restaurant_name] for [number_of_people] at [reservation_time] on [reservation_date].' . "\n";
		 		$message .= 'To manage your reservations, please visit our reservation management page at the URL below.' . "\n";
		 		$message .= 'We look forward to your patronage.' . "\n\n";
		 		$message .= 'Sincerely,' . "\n";
		 		$message .= 'The staff at [restaurant_name].' . "\n";
		 		*/
		 		
		 		$message = $default;
		 	
		 	} // End IF Statement
		 		
		 		// Let the user customise the message via a filter.
		 		$message = apply_filters( 'wootable_email_customer_message', $message );
		 		
		 		$message = stripslashes( $message );
		 		
		 		$_manager_url = '';
		 		$_manager_id = get_option( $this->plugin_prefix . 'page_manage' );
		 		
		 		if ( $_manager_id ) { $_manager_url = get_permalink( $_manager_id ); } // End IF Statement
		 		
		 		if ( $_manager_url ) {
		 		
		 			// Change the character used to concatenate the custom query variables,
			 		// depending on whether the user has pretty permalinks on or not.
			 		
			 		$_concatenator = '&';
			 		
			 		$_permalink_structure = get_option( 'permalink_structure' );
			 		
			 		if ( $_permalink_structure != '' ) { $_concatenator = '?'; } // End IF Statement
		 		
		 			
		 			$message .= "\n\n" . apply_filters( 'wootable_manage_reservations_email_text', 'Manage your reservations here: ' ) . $_manager_url . $_concatenator . 'e-mail=' . urlencode($this->fields_for_form['contact_email']) . '&key=' . md5($this->fields_for_form['contact_email']) . "\n\n";
		 			
		 		} // End IF Statement
		 			 		
		 		// Let the user customise the message via a filter.
		 		// $message = apply_filters('wootable_email_customer_message', $message);	 		
	 		
	 		// } // End IF Statement
	 		
	 		// In "special" cases, replace the word "special" with the maximum number and a "+" sign.
	 		$_max_number_of_people = WTFactory::get_max_number_of_people()+1 . '+';
	 		
	 		if ( $this->fields_for_form['number_of_people'] == 'special' ) {
	 		
	 			$message = str_replace( '[number_of_people]', $_max_number_of_people, $message );
	 			
	 		} // End IF Statement
	 		
	 		// "Parse" shortcodes.	 		
	 		foreach ( $_codes as $_k => $_v ) {
	 		
	 			$message = str_replace( $_k, $_v, $message );
	 			
	 		} // End FOREACH Loop
	 		
	 		$headers = 'From: ' . get_option('blogname') . ' Reservations <' . get_option('admin_email') . '>' . "\r\n\\";
	 		
	 		// If the option to CC the restaurant on all reservation e-mails is checked,
	 		// BCC  the restaurant on this e-mail.
	 		
	 		if ( get_option( $this->plugin_prefix . 'send_confirmation_to_restaurant' ) ) {
	 		
	 			$headers .= 'Bcc: ' . get_option('blogname') . ' Reservations <' . get_option('admin_email') . '>' . "\r\n\\";
	 			
	 		} // End IF Statement
	 		
	 		return wp_mail( $to, $subject, $message, $headers );
	 		// Used imap_mail() as the wp_mail() and mail() functions were returning `false`.
	 		
	 	} // End send_customer_email()
	 	
	 	/*----------------------------------------
	 	  send_admin_email()
	 	  ----------------------------------------
	 	  
	 	  * A wrapper function for send_email()
	 	  * that e-mails the booking confirmation
	 	  * to the administrator.
	 	----------------------------------------*/
	 	
	 	public function send_admin_email ( $message = '' ) {
		
			$date_format = '';
			
			$date_format = get_option( $this->plugin_prefix . 'date_format' );
			
			if ( $date_format == '' ) { $date_format = 'jS F Y'; } // End IF Statement
			
			$_reservation_date_timestamp = strtotime( $this->fields_for_form['reservation_date'] );
			$_reservation_date = date( $date_format, $_reservation_date_timestamp );
		
	 		$_codes = array(
	 						'[restaurant_name]' => get_option('blogname'), 
	 						'[number_of_people]' => $this->fields_for_form['number_of_people'], 
	 						'[reservation_time]' => $this->fields_for_form['reservation_time'], 
	 						'[reservation_date]' => $_reservation_date, 
	 						'[reservation_instructions]' => $this->fields_for_form['reservation_instructions'], 
	 						'[contact_name]' => $this->fields_for_form['contact_name'], 
	 						'[contact_tel]' => $this->fields_for_form['contact_tel'], 
	 						'[contact_email]' => $this->fields_for_form['contact_email']
	 					);
	 		
	 		$to = get_option('admin_email');
	 		
	 		$subject = '[' . get_option('blogname') . ']: Booking request for ' . $this->fields_for_form['contact_name'] . '.';
	 		
	 		$subject = apply_filters('wootable_email_admin_subject', $subject);
	 		
	 		$message = get_option( $this->plugin_prefix . 'adminemail_reservationmade' );
	 		$default = $wootable->default_emails['admin_reservationmade'];

	 		
	 		if ( $message == '' ) {
	 			
		 		// $message = '';
		 		
		 		/*
		 		
		 		$message .= 'Hey there!' . "\n\n";
		 		$message .= 'A reservation has been made at [restaurant_name] for [number_of_people], to be seated at [reservation_time] on [reservation_date].' . "\n";
		 		$message .= 'The reservee, [contact_name], can be contacted at:' . "\n";
		 		$message .= 'Telephone: [contact_tel]' . "\n";
		 		$message .= 'E-mail: [contact_email]' . "\n";
		 		$message .= "\n" . '[reservation_instructions]' . "\n\n";
		 		$message .= 'Please contact [contact_name] at your earliest convenience to confirm their reservation.' . "\n\n";
		 		$message .= 'Sincerely,' . "\n";
		 		$message .= '[restaurant_name] Reservations.' . "\n";
		 		
		 		*/
		 		
		 		$message = $default; 		
	 		
	 		} // End IF Statement
			
			// Let the user customise the message via a filter.
		 	$message = apply_filters('wootable_email_admin_message', $message);
		 	
		 	$message = stripslashes( $message );

	 		// In "special" cases, replace the word "special" with the maximum number and a "+" sign.
	 		$_max_number_of_people = WTFactory::get_max_number_of_people()+1 . '+';
	 		
	 		if ( $this->fields_for_form['number_of_people'] == 'special' ) {
	 		
	 			$message = str_replace( '[number_of_people]', $_max_number_of_people, $message );
	 			
	 		} // End IF Statement
	 		
	 		// "Parse" shortcodes.	 		
	 		foreach ( $_codes as $_k => $_v ) {
	 		
	 			if ( $_k == '[reservation_instructions]' && $_v == '' ) {
	 			
	 				$message = str_replace( $_k, '', $message );
	 				
	 			} else {
	 					 		
	 				$message = str_replace( $_k, $_v, $message );
	 				
	 			} // End IF Statement
	 			
	 		} // End FOREACH Loop
	 		
	 		$headers = 'From: ' . get_option('blogname') . ' Reservations <' . get_option('admin_email') . '>' . "\r\n\\";
	 		
	 		return wp_mail( $to, $subject, $message, $headers );
	 		// Used imap_mail() as the wp_mail() and mail() functions were returning `false`.
	 		
	 	} // End send_admin_email()
	 	
	 	/*----------------------------------------
	 	  send_statuschange_email()
	 	  ----------------------------------------
	 	  
	 	  * Sends an e-mail to either the customer
	 	  * or administrator, notifying them of a
	 	  * status change.
	 	  
	 	  * Params:
	 	  * - String $type
	 	  * - Int $id
	 	  
	 	  * Globals:
	 	  * - wootable
	 	----------------------------------------*/
	 	
	 	public function send_statuschange_email ( $type, $id ) {
	 		
	 		global $wootable;
	 		
	 		// Check that the $type given is valid.
	 		
	 		if ( in_array( $type, array( 'user', 'admin' ) ) && is_numeric( $id ) ) {
	 		
	 			// Check that the id passed is in the database.
	 			
	 			$post = get_post( $id );
	 			
	 			if ( $post ) {
	 			
	 				// Check if the post_type is valid for our context.
	 				
	 				if ( $post->post_type == $wootable->post_type->token ) {
	 				
	 					// Setup the various shortcodes for parsing.
	 					
	 					$_postmeta = get_post_custom( $id );
	 					
	 					$date_format = '';
					
						$date_format = get_option( $this->plugin_prefix . 'date_format' );
						
						if ( $date_format == '' ) { $date_format = 'jS F Y'; } // End IF Statement
						
						$_reservation_date_timestamp = strtotime( $_postmeta['reservation_date'][0] );
						$_reservation_date = date( $date_format, $_reservation_date_timestamp );
	 					
	 					$_codes = array(
	 						'[restaurant_name]' => get_option('blogname'), 
	 						'[number_of_people]' => $_postmeta['number_of_people'][0], 
	 						'[reservation_time]' => $_postmeta['reservation_time'][0], 
	 						'[reservation_date]' => $_reservation_date, 
	 						'[reservation_instructions]' => $_postmeta['reservation_instructions'][0], 
	 						'[contact_name]' => $_postmeta['contact_name'][0], 
	 						'[contact_tel]' => $_postmeta['contact_tel'][0], 
	 						'[contact_email]' => $_postmeta['contact_email'][0], 
	 						'[reservation_status]' => ucfirst( $_postmeta['reservation_status'][0] )
	 					);
	 					
	 					// Get the status message from the database.
	 					
	 					// Setup the message for sending.
	 					
	 					$subject = '[' . get_option('blogname') . ']: Reservation # ' . $post->ID . ': Status of reservation for ' . $_postmeta['contact_name'][0] . ' has changed.';
	 		
				 		$subject = apply_filters('wootable_email_statuschange_subject', $subject);
				 		
				 		switch ( $type ) {
				 		
				 			case 'user':
				 			
				 			$to = '';
				 			
				 			if ( is_email( $_postmeta['contact_email'][0] ) ) {
				 			
				 				$to = $_postmeta['contact_email'][0];
				 			
				 			} // End IF Statement
				 			
				 			$message = get_option( $this->plugin_prefix . 'email_statuschange' );
				 			$default = $wootable->default_emails['statuschange'];
				 			
				 			break;
				 			
				 			case 'admin':
				 			
				 			$admin_email = get_option('admin_email');
				 			
				 			$to = '';
				 			
				 			if ( is_email( $admin_email ) ) {
				 			
				 				$to = $admin_email;
				 			
				 			} // End IF Statement
				 			
				 			$message = get_option( $this->plugin_prefix . 'adminemail_statuschange' );
				 			$default = $wootable->default_emails['admin_statuschange'];
				 			
				 			break;
				 			
				 		} // End SWITCH Statement
			
				 		
				 		if ( $message == '' ) {
					 		
					 		$message = $default;		
				 		
				 		} // End IF Statement
				 		
				 		// Let the user customise the message via a filter.
					 	$message = apply_filters('wootable_email_statuschange', $message);
				 		
				 		// "Parse" shortcodes.
				 		 		
				 		foreach ( $_codes as $_k => $_v ) {
				 		
				 			if ( $_k == '[reservation_instructions]' && $_v == '' ) {
				 			
				 				$message = str_replace( $_k, '', $message );
				 				
				 			} else {
				 					 		
				 				$message = str_replace( $_k, $_v, $message );
				 				
				 			} // End IF Statement
				 			
				 		} // End FOREACH Loop
				 		
				 		$message = stripslashes( $message );
				 		
				 		$headers = 'From: ' . get_option('blogname') . ' Reservations <' . get_option('admin_email') . '>' . "\r\n\\";
				 		
				 		return wp_mail( $to, $subject, $message, $headers );
				 		// Used imap_mail() as the wp_mail() and mail() functions were returning `false`.
	 					
	 					
	 				} // End IF Statement
	 				
	 			} // End IF Statement
	 			
	 		} // End IF Statement
	 	
	 	} // End send_statuschange_email()
	 	
	 	/*----------------------------------------
	 	  send_email()
	 	  ----------------------------------------
	 	  
	 	  * Sends e-mails... does what it says on
	 	  * the tin.
	 	----------------------------------------*/
	 	
	 	public function send_email () {
	 		
	 		// TO DO
	 		
	 	} // End send_email()
	 	
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
	 			//$content = '<p class="woo-sc-box note">Please fill out the form below to complete your booking</p>';
	 			
	 		} // End IF Statement
	 		
	 		if ( $this->message && ! $this->errors ) {
	 		
	 			$content = $this->message;
	 			
	 		} // End IF Statement
	 		
	 		return $content;
	 		
	 	} // End display_message()
	 	
	 	/*----------------------------------------
	 	  display_specialrequest_message()
	 	  ----------------------------------------
	 	  
	 	  * The message to display if the booking
	 	  * has been added successfully and is a
	 	  * special request.
	 	----------------------------------------*/
	 	
	 	public function display_specialrequest_message () {
	 		
	 		// TO DO
	 		
	 		// $content = __( 'Your reservation has been submitted successfully. As this is a special request, our management has been informed of your request and will contact you timeously to confirm the finer details.', 'woothemes' );
	 		
	 		$content = '<p class="woo-sc-box tick">'.__( 'Your reservation has been submitted successfully. As this is a special request, our management has been informed of your request and will contact you timeously to confirm the finer details.', 'woothemes' ).'</p>';
	 		
	 		$content = apply_filters( 'wootable_specialrequest_message', $content );
	 		
	 		return $content;
	 		
	 	} // End display_specialrequest_message()
	 	
	 	/*----------------------------------------
	 	  display_success_message()
	 	  ----------------------------------------
	 	  
	 	  * The message to display if the booking
	 	  * has been added successfully.
	 	----------------------------------------*/
	 	
	 	public function display_success_message () {
	 		
	 		// TO DO
	 		
	 		//$content = __( 'Your reservation has been booked successfully. You\'ll receive an email confirming this with a link should you wish to cancel your booking.', 'woothemes' );
	 		
	 		if ( $_REQUEST['is_updated'] == 'yes' ) {
	 		
	 			$content = '<p class="woo-sc-box tick">'.__( 'Your reservation has been updated successfully. You\'ll receive an email confirming this with a link should you wish to cancel your booking.', 'woothemes' ).'</p>';
	 			
	 		} else {
	 		
	 			$content = '<p class="woo-sc-box tick">'.__( 'Your reservation has been booked successfully. You\'ll receive an email confirming this with a link should you wish to cancel your booking.', 'woothemes' ).'</p>';
	 		
	 		} // End IF Statement
	 		
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
			
				//$content .= '<div class="error fade"><p>' . __( 'Please correct the following', 'woothemes' ) . ':</p>' . "\n";
				$content = '<p class="woo-sc-box alert">'.__( 'Please correct the following', 'woothemes' ).'</p>';
				
					//$content .= '<ul class="messages">' . "\n";
			
					foreach ( $this->errors as $e ) {
					
						$content .= '<p class="woo-sc-box alert">'.__( $e, 'woothemes' ).'</p>' . "\n";
						
					} // End FOREACH Loop
					
					//$content .= '</ul>' . "\n";
				
				//$content .= '</div><!--/.error fade-->' . "\n";
				
			} // End IF Statement
	 		
	 		return $content;
	 		
	 	} // End display_error_messages()
	 	
	 	/*----------------------------------------
	 	  form_processing()
	 	  ----------------------------------------
	 	  
	 	  * Wrapper for validating and processing
	 	  * the form on the bookings page.
	 	----------------------------------------*/
	 	
	 	public function form_processing () {
	 		
	 		// TO DO
	 		
	 		if ( is_page( $this->bookings_page ) ) {
	 			
	 			// If the form has been submitted...
	 			if ( $_POST && $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	 				
	 				// Setup variables for working with the form data.
	 				$this->fields_detail = $this->get_form_fields();
	 				
	 				$this->create_empty_variables(); 				
					$this->create_post_variables();
	 				
	 				// Check that the user has filled in all necessary fields,
	 				// and that the data is valid.
	 				$is_valid = $this->validate_booking();
	 				
	 				// If the data is valid, check if any table are available.
	 				if ( $is_valid ) {
	 					
	 					$number_of_people = $this->fields_for_form['number_of_people'];
	 					$time = $this->fields_for_form['reservation_time'];
	 					$date = $this->fields_for_form['reservation_date'];
	 					
	 					if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'update' && isset( $_REQUEST['id'] ) && is_numeric( $_REQUEST['id'] ) ) {
				 		
				 			$_id = (int) $_REQUEST['id'];
				 		
				 			// Get the current tables assigned and store them for later.
				 			$current_tables = wp_get_object_terms( $_id, 'tables' );
				 			
				 			$current_table_ids = array();
				 			
				 			if ( count( $current_tables ) ) {
				 			
				 				foreach ( $current_tables as $ct ) {
				 				
				 					$current_table_ids[] = $ct->term_id;
				 					
				 				} // End FOREACH Loop
				 				
				 			} // End IF Statement
				 			
				 			// Unassign the tables currently assigned to this reservation.
	 						// wp_delete_object_term_relationships( $_id, 'tables' );
				 		
				 		} // End IF Statement
	 					
	 					$available_table = $this->get_table_to_seat( $number_of_people, $time, $date, $current_table_ids, $_id );
	 					
	 					if ( $this->fields_for_form['number_of_people'] == 'special' ) {
	 						
	 						$available_table = 'special';
	 						
	 					} // End IF Statement
	 					
	 					// UPDATE RESERVATION - 2010-11-04.
	 					
	 					// If running an update and no tables are available, reset the tables.
	 					/*
	 					if ( ! $available_table && $_REQUEST['action'] == 'update' ) {
	 						
	 						wp_set_object_terms( $_id, array( $available_table ), 'tables', false );
	 						
	 					} // End IF Statement 
	 					*/
	 					// If a table is available, make the reservation.
	 					if ( $available_table ) {
	 						
	 						$is_processed = $this->process_booking( $available_table );
	 						
	 						// If the reservation is processed successfully,
	 						// display a message and send necessary e-mails.
	 						if ( $is_processed ) {
	 							
	 							if ( $this->fields_for_form['number_of_people'] == 'special' ) {
	 							
	 								$this->message = $this->display_specialrequest_message();
	 							
	 							} else {
	 								
	 								$this->message = $this->display_success_message();
	 							
	 							} // End IF Statement
	 							
	 							// Don't send "thank you" or "notification" e-mails if the booking
	 							// is a special request (ie: not in the system).
	 							if ( $this->fields_for_form['number_of_people'] == 'special' ) {} else {
	 							
		 							// Send e-mails to the administrator and the customer.
		 							$_email_sent = $this->send_admin_email();
		 							$_customer_email_sent = $this->send_customer_email( $this->fields_for_form['contact_email'] );
	 							
	 							} // End IF Statement
	 							
	 							// If the user is at a public computer, don't set the cookie.
	 							if ( $this->fields_for_form['no_cookie'] ) {} else {
	 							
		 							// Set the $_COOKIE so we can remember the user.
		 							$_cookie_token = WTFactory::get_cookie_token();
		 							
		 							$_domain = str_replace( 'http://', '', get_bloginfo('url') );
		 							$_domain_bits = explode( '/', $_domain );
		 							$_domain = str_replace( 'www', '', $_domain_bits[0] );
		 							
		 							setcookie( 'wootable_' . $_cookie_token . '_email', $this->fields_for_form['contact_email'], time()+60*60*24*365, COOKIEPATH, false );
									$_COOKIE['wootable_' . $_cookie_token . '_email'] = $this->fields_for_form['contact_email'];
	 							 		
	 							} // End IF Statement
	 							 						
	 						// Otherwise, display a failure message.
	 						} else {
	 							
	 							$this->message = $this->display_fail_message();
	 							
	 						} // End IF Statement
	 						
	 					// Otherwise, add an error message to $this->errors.
	 					} else {
	 						
	 						$this->errors[] = apply_filters( 'wootable_msg_no_table', __( 'There are no tables available at the requested time and date for your sized party. Please try again.', 'woothemes' ) );
	 						
	 					} // End IF Statement
	 					
	 				} // End IF Statement
	 				
	 			} // End IF Statement
	 			
	 			
	 		} // End IF Statement
	 		
	 		
	 	} // End form_processing()
	 	
	 	/*----------------------------------------
	 	  ajax_functions()
	 	  ----------------------------------------
	 	  
	 	  * A switch to perform specific actions
	 	  * pertaining to various AJAX calls.
	 	----------------------------------------*/
	 	
	 	public function ajax_functions () {
	 	
	 		switch ( $_POST['ajax-action'] ) {
	 		
	 			// Get the times available on a particular day.
	 			case 'get_times':
	 				
	 				WTFactory::display_changed_times( $this->plugin_prefix, true, $_POST['time'], $_POST['page_id'], $_POST['date'], $_POST['is_admin'] );
	 			
	 			break;
	 			
	 			// Generate a confirmation message for the main reservation form.
	 			case 'generate_confirmation_message':
	 				
	 				$_message = '';
					
					if ( strlen( $_POST['time'] ) > 8 ) { echo $_message; } else {
					
						$_friendly_date = date( 'l, F jS', strtotime( $_POST['date'] ) );
						
						$_friendly_time = '';
						
						if ( $_POST['time'] ) {
						
							$_friendly_time = __( ' at ', 'woothemes' );
							
							$_friendly_time .= $_POST['time'];
							
						} // End IF Statement
						
						$_friendly_number = '';
						
						if ( $_POST['people'] == 'special' ) {
							
							$_friendly_number = WTFactory::get_max_number_of_people() + 1;
							$_friendly_number .= '+';
							
						} else {
							
							$_friendly_number = $_POST['people'];
						
						} // End IF Statement
						
						// $_message .= '<span class="confirmation_message">' . $_friendly_date . $_friendly_time . ' for a party of ' . $_friendly_number . '</span>';
	 			
	 					$_message = '<input type="hidden" name="confirmation_message" class="confirmation_message" value="' . sprintf( __( '%s for a party of %s', 'woothemes' ), $_friendly_date . $_friendly_time, $_friendly_number ) . '" />' . "\n";
	 			
	 					echo $_message;
	 			
					} // End IF Statement
	 			
	 			break;
	 			
	 			// Generate a confirmation message for the "Make a reservation" widget.
	 			case 'generate_confirmation_message_widget':
	 				
	 				$_message = '';
					
					if ( strlen( $_POST['time'] ) > 8 ) { echo $_message; } else {
					
						$_days = array(
							'sun' => __( 'Sunday','woothemes' ), 
							'mon' => __( 'Monday', 'woothemes' ), 
							'tue' => __( 'Tuesday', 'woothemes' ), 
							'wed' => __( 'Wednesday', 'woothemes' ), 
							'thu' => __( 'Thursday', 'woothemes' ), 
							'fri' => __( 'Friday', 'woothemes' ), 
							'sat' => __( 'Saturday', 'woothemes' )
						);
					
						// Get the various business hours.
						$business_hours = WTFactory::get_business_hours( $this->plugin_prefix );
					
						$index = strtolower( date('D', strtotime($_POST['date']) ) );
						
						// $full_dayname = date('l', strtotime($_POST['date']) );
						$full_dayname = $_days[$index];
						
						// Compensate for the colloquial convention of "thurs" instead of "thu", and "tues" instead of "tue".
						if ( $index == 'thu' ) { $index = 'thurs'; } // End IF Statement
						if ( $index == 'tue' ) { $index = 'tues'; } // End IF Statement
						
						$times = $business_hours[$index];
					
						if ( $times['closed'] ) {
							
							$_message = '<span class="confirmation_message">' . sprintf( __( 'We are unfortunately closed on %ss.', 'woothemes' ), $full_dayname ) . '<input type="hidden" name="reservation_time" class="required" value="" /></span>' . "\n";
						
						} else {
						
							$_friendly_date = date( 'l, F jS', strtotime( $_POST['date'] ) );
							
							$_friendly_time = '';
							
							if ( $_POST['time'] ) {
							
								$_friendly_time = ' at ';
								
								$_friendly_time .= $_POST['time'];
								
							} // End IF Statement
						
							
							$_friendly_number = '';
							
							if ( $_POST['people'] == 'special' ) {
								
								$_friendly_number = WTFactory::get_max_number_of_people() + 1;
								$_friendly_number .= '+';
								
							} else {
								
								$_friendly_number = $_POST['people'];
							
							} // End IF Statement
							
							$_message .= '<span class="confirmation_message">' . sprintf( __( '%s for a party of %s', 'woothemes' ), $_friendly_date . $_friendly_time, $_friendly_number ) . '</span>';
	 			
	 					} // End IF Statement
	 				
	 					echo $_message;
	 			
					} // End IF Statement
	 			
	 			break;

	 			
	 		} // End SWITCH Statement
	 		
	 		exit;
	 		
	 	} // End ajax_functions()
	 	
	 	/*----------------------------------------
	 	  get_table_to_seat()
	 	  ----------------------------------------
	 	  
	 	  * Check if there is a table available
	 	  * to seat $number_of_people at $time
	 	  * on $date. If there are more than
	 	  * one, sort by the number of seats
	 	  * and return the $table_id with the 
	 	  * closest seat count.
	 	  
	 	  * Params:
	 	  * - int $number_of_people
	 	  * - string $time
	 	  * - string $date
	 	  * - array $current_tables
	 	  * - int $current_id
	 	----------------------------------------*/
	 	
	 	public function get_table_to_seat ( $number_of_people, $time, $date, $current_tables = array(), $current_id = 0 ) {
	 		
	 		// TO DO
	 		
	 		// return 31; // TEMP - 2010-09-09
	 		
	 		global $wpdb;
	 		
	 		// Setup defaults
	 		$table_id = 0;
	 		$best_table = 0;
	 		$second_best_table = 0;
	 		$number_of_people = (int) $number_of_people;
	 		$number_of_people_plus_one = $number_of_people + 1;
	 		$number_of_people_plus_two = $number_of_people + 2;
	 		
	 		// Setup query to check if there are any tables at all
	 		// that could accomodate this party
	 		/*
	 		$query = "SELECT 
	 					terms.term_id as table_id, terms.name, terms.slug, meta.meta_value as number_of_seats 
	 				  FROM " . $wpdb->prefix . "terms as terms 
	 				  JOIN " . $wpdb->prefix . "term_taxonomy as tax ON terms.term_id = tax.term_id 
	 				  JOIN " . $wpdb->prefix . "woo_tables_meta as meta ON terms.term_id = meta.woo_tables_id 
	 				 WHERE meta.meta_key = 'number_of_seats' 
	 				 AND ( meta.meta_value = " . $number_of_people . " OR meta.meta_value = " . $number_of_people_plus_one . " OR meta.meta_value = " . $number_of_people_plus_two . " )";
	 		*/
	 		
	 		$query = "SELECT 
	 					terms.term_id as table_id, terms.name, terms.slug, meta.meta_value as number_of_seats 
	 				  FROM " . $wpdb->prefix . "terms as terms 
	 				  JOIN " . $wpdb->prefix . "term_taxonomy as tax ON terms.term_id = tax.term_id 
	 				  JOIN " . $wpdb->prefix . "woo_tables_meta as meta ON terms.term_id = meta.woo_tables_id 
	 				 WHERE meta.meta_key = 'number_of_seats' 
	 				 AND ( meta.meta_value >= " . $number_of_people . " )";
	 		
	 		// Execute the query
	 		$rs = $wpdb->get_results( $query, ARRAY_A );
	 		
	 		// If there are tables, process the data and return the table with
	 		// the closest number of seats to the desired number
	 		if ( $rs ) {
	 		
	 			$available_tables = array();
	 		
	 			foreach ( $rs as $r ) {
	 			
	 				$number_of_seats = $r['number_of_seats'];
	 				$term_id = $r['table_id'];
	 			
	 				$available_tables[$number_of_seats . '-' . $term_id] = $term_id;
	 				
	 			} // End FOREACH Loop
	 			
	 			ksort( $available_tables );
	 			
	 			$table_ids_array = array();
	 			
	 			foreach ( $available_tables as $number_of_seats => $term_id ) {
	 					 			
	 				$table_ids_array[] = $term_id;
	 				
	 			} // End FOREACH Loop
	 			
	 			/*	 			
	 			foreach ( $available_tables as $number_of_seats => $term_id ) {
	 					 			
	 				if ( $best_table == 0 ) { $best_table = $term_id; } // End IF Statement
	 				// if ( $second_best_table == 0 && $table_id != 0 ) { $second_best_table = $term_id; } // End IF Statement
	 				
	 			} // End FOREACH Loop
	 			*/
	 		// } // End IF Statement
	 		
	 		// Okay, we have our $table_id. If it's not 0, lets see if there are any reservations
	 		// at this table at the time and date supplied.
	 			 		
		 		// if ( $best_table == 0 ) {} else {
		 		if ( count( $available_tables ) == 0 ) {} else {
		 				
		 				/*
		 				if ( count( $current_tables ) ) {
		 				
		 					$table_ids_array = array_merge( $table_ids_array, $current_tables ); // Make the current tables available without having to remove them.
		 					
		 				} // End IF Statement
		 				*/
		 				
		 				$table_ids = join( ',', $table_ids_array );
		 				
				 		$query = "SELECT ID, post_title, post_date, post_name, post_author, meta_date.meta_value as reservation_date, meta_time.meta_value as reservation_time, $wpdb->terms.term_id as table_id  
									FROM $wpdb->posts 
									LEFT JOIN $wpdb->postmeta as meta_date ON($wpdb->posts.ID = meta_date.post_id) 
									LEFT JOIN $wpdb->postmeta as meta_time ON($wpdb->posts.ID = meta_time.post_id) 
									LEFT JOIN $wpdb->postmeta as meta_status ON($wpdb->posts.ID = meta_status.post_id) 
									LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
									LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
									LEFT JOIN $wpdb->terms ON($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id) 
									WHERE $wpdb->posts.post_type = 'reservation' 
									AND meta_date.meta_key = 'reservation_date' 
									AND meta_date.meta_value = '$date' 
									AND meta_time.meta_key = 'reservation_time' 
									AND meta_time.meta_value = '$time' 
									AND meta_status.meta_key = 'reservation_status' 
									AND meta_status.meta_value != 'cancelled' 
									AND $wpdb->posts.post_status = 'publish'
									AND $wpdb->term_taxonomy.taxonomy = 'tables'
									AND $wpdb->terms.term_id IN (" . $table_ids . ")";
									
						if ( $current_id > 0 ) {
						
							$query .= " AND $wpdb->posts.ID != '$current_id'";
							
						} // End IF Statement
						
									/*
									if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'update' && isset( $_REQUEST['id'] ) && is_numeric( $_REQUEST['id'] ) ) {
							 		
							 			$_id = (int) $_REQUEST['id'];
							 		
							 			// Get the current tables assigned.
							 			$current_tables = wp_get_object_terms( $_id, 'tables' );
							 			
							 			if ( count( $current_tables ) ) {
							 			
							 				$query .= ' OR ' . $wpdb->posts.ID . ' = "' . $_id . '"';
							 				
							 			} // End IF Statement
							 		
							 		} // End IF Statement
							 		*/
						/*								
						if ( $second_best_table ) {
							
							$query .= " OR $wpdb->terms.term_id = " . $second_best_table . "";
						
						} // End IF Statement
						*/
						
						$query .= "	GROUP BY $wpdb->posts.ID ORDER BY meta_date.meta_value, meta_time.meta_value DESC";
						
				 		// Execute the query
				 		$rs = $wpdb->get_results( $query, ARRAY_A );
		 				
		 				// If there are reservations, loop through and get the IDs of the suitable tables that are empty.
		 				// Otherwise, assign the $best_table as $table_id... there's an opening!
		 				if ( $rs ) {
		 					
		 					$open_suitable_tables = array();
		 					
		 					// If the table assigned to this reservation is one of the $table_ids_array,
		 					// don't assign it as an $open_suitable_table.
		 					
		 					$_filtered_tables = array();
		 					
		 					foreach ( $table_ids_array as $table_key => $table ) {
		 					
		 						foreach ( $rs as $k => $v ) {
			 						
			 						// If the table_id returned is present in the table_ids_array, remove it as it's not available.
			 						if ( $v['table_id'] == $table ) {
			 						
			 							unset( $table_ids_array[$table_key] );
			 						
			 						} else {
			 							
			 							$_filtered_tables[] = $v['table_id'];
		
			 						} // End IF Statement
			 					
			 					} // End FOREACH Loop
		 					
		 					} // End FOREACH Loop
		 					
		 					if ( $table_ids_array ) {
		 					
		 						// Reindex table IDs into new array
		 						foreach ( $table_ids_array as $t ) {
		 							
		 							$open_suitable_tables[] = $t;
		 							
		 						} // End FOREACH Loop
		 					
		 						$table_id = $open_suitable_tables[0];
		 						
		 					} // End IF Statement
		 					
		 				} else {
		 				
		 					foreach ( $available_tables as $number_of_seats => $term_id ) {
				 					 			
				 				if ( $best_table == 0 ) { $best_table = $term_id; } // End IF Statement
				 				
				 			} // End FOREACH Loop
		 				
		 					$table_id = $best_table;
		 					
		 				} // End IF Statement
		 		
		 		} // End IF Statement
	 		
	 		} // End IF Statement
	 		
	 		return $table_id;
	 		
	 	} // End get_table_to_seat()
	 	
	 	/*----------------------------------------
	 	  get_form_fields()
	 	  ----------------------------------------
	 	  
	 	  * A helper function to compile an array
	 	  * of form fields for use with validation.
	 	----------------------------------------*/
	 	
	 	public function get_form_fields () {
	 		
	 		$fields = array(
	 					array(
	 							'field' 	=> 'reservation_date', 
	 							'type' 		=> 'date', 
	 							'message' 	=> __( 'Please enter a valid date for your reservation.', 'woothemes' ), 
	 							'required' 	=> 1
	 						), 
	 					array(
	 							'field' 	=> 'reservation_time', 
	 							'type' 		=> 'time', 
	 							'message' 	=> __( 'Please select a valid time for your reservation.', 'woothemes' ), 
	 							'required' 	=> 1
	 						),
	 					array(
	 							'field' 	=> 'number_of_people', 
	 							'type' 		=> 'number_of_people', 
	 							'message' 	=> __( 'Please enter a valid number of people for your reservation.', 'woothemes' ), 
	 							'required' 	=> 1
	 						),
	 					array(
	 							'field' 	=> 'reservation_instructions', 
	 							'type' 		=> 'text', 
	 							'message' 	=> __( 'Please enter instructions for your reservation.', 'woothemes' ), 
	 							'required' 	=> 0
	 						),
	 					array(
	 							'field' 	=> 'contact_name', 
	 							'type' 		=> 'text', 
	 							'message' 	=> __( 'Please enter your full name.', 'woothemes' ), 
	 							'required' 	=> 1
	 						),
	 					array(
	 							'field' 	=> 'contact_tel', 
	 							'type' 		=> 'text', 
	 							'message' 	=> __( 'Please enter a valid telephone number for us to contact you on.', 'woothemes' ), 
	 							'required' 	=> 1
	 						),
	 					array(
	 							'field' 	=> 'contact_email', 
	 							'type' 		=> 'email', 
	 							'message' 	=> __( 'Please enter your e-mail address.', 'woothemes' ), 
	 							'required' 	=> 1
	 						),
	 					array(
	 							'field' 	=> 'no_cookie', 
	 							'type' 		=> 'int', 
	 							'message' 	=> __( 'Please select if you are on a public computer (if you are, we won\'t save your details.)', 'woothemes' ), 
	 							'required' 	=> 0
	 						) 		
	 					);
	 		
	 		$fields = apply_filters( 'wootable_form_fields', $fields );
	 	
	 		return $fields;
	 	
	 	} // End get_form_fields()
	 	
	 	/* Utility Functions
		----------------------------------------*/
		
		/*----------------------------------------
	 	  create_empty_variables()
	 	  ----------------------------------------
	 	  
	 	  * A helper function to create empty
	 	  * variables to hold form data.
	 	----------------------------------------*/
		
		function create_empty_variables () {
		
			foreach ( $this->fields_detail as $field ) {
				
				$this->fields_for_form[$field['field']] = '';
				
			} // End FOREACH Loop
			
		} // End create_empty_variables()
		
		/*----------------------------------------
	 	  create_post_variables()
	 	  ----------------------------------------
	 	  
	 	  * A helper function to create variables
	 	  * from the $_POST'ed data.
	 	----------------------------------------*/
		
		function create_post_variables () {
		
			foreach ( $this->fields_detail as $field ) {
					
				$this->fields_for_form[$field['field']] = trim( strip_tags( $_POST[$field['field']] ) );
				
			} // End FOREACH Loop
			
		} // End create_post_variables()
		
		/*----------------------------------------
	 	  create_form_variables()
	 	  ----------------------------------------
	 	  
	 	  * A helper function to create variables
	 	  * for use in the reservation form.
	 	----------------------------------------*/
		
		function create_form_variables () {
		
			foreach ( $this->fields_detail as $field ) {
			
				if ( isset( $_POST[$field['field']] ) ) {
				
					$this->fields_for_form[$field['field']] = $_POST[$field['field']];
				
				} else {
			
					$this->fields_for_form[$field['field']] = '';
					
				}
			
			} // End FOREACH Loop
			
		} // End create_form_variables()
	 	
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
	 	  
	 	  * Enqueue various JavaScript files
	 	  * for use on the frontend.
	 	----------------------------------------*/
		
		public function enqueue_script () {
			
			if ( ( is_page( $this->bookings_page ) || is_page( $this->manage_page ) || is_page_template('template-menu.php') || is_page_template('template-menu-full.php') || is_page_template('template-location.php') )  ) {
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-dialog' );
				wp_enqueue_script( 'jquery-ui-datepicker', $this->plugin_url . '/assets/js/jquery.ui.datepicker.min.js', array( 'jquery', 'jquery-ui-core' ), '1.8.4', false );
				wp_enqueue_script( 'jquery-validate', $this->plugin_url . '/assets/js/jquery-validate/jquery.validate.min.js', array( 'jquery' ), '1.7', false );
				// Enqueue the JavaScript functions file(s)
		 		wp_enqueue_script('woo-table-functions', $this->plugin_url . '/assets/js/functions.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-dialog', 'jquery-validate' ), '0.0.0.1', false);
		 		wp_enqueue_script('woo-table-functions-validate', $this->plugin_url . '/assets/js/functions-validate.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-dialog', 'jquery-validate' ), '0.0.0.1', false);
		 	} elseif( ( is_active_widget( false,false,'widget_wootable_makereservation', true ) )) {
		 		wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-datepicker', $this->plugin_url . '/assets/js/jquery.ui.datepicker.min.js', array( 'jquery', 'jquery-ui-core' ), '1.8.4', false );
				// Enqueue the JavaScript functions file
		 		wp_enqueue_script('woo-table-functions', $this->plugin_url . '/assets/js/functions.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), '0.0.0.1', false);
		 	} else {
		 		//No JS
		 	}
		 	
		} // End enqueue_script()
		
	} // End Class WooTable_FrontEnd
?>