<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: A factory class of common static functions for the WooTable WordPress plugin.
Date Created: 2010-08-24.
Author: Matty.
Since: 0.0.1.1


TABLE OF CONTENTS

- function get_business_hours_display ()
- function get_time_interval ()
- function display_changed_times ()
- function get_saved_email ()
- function get_cookie_token ()
- function get_max_number_of_people ()
- function get_business_hours ()
- function get_times_between ()
- function get_reserved_times_for_date ()
- function get_available_tables ()
- function attempt_table_combo ()

-----------------------------------------------------------------------------------*/

	class WTFactory {
		
		/*----------------------------------------
	 	  get_business_hours_display()
	 	  ----------------------------------------
	 	  
	 	  * Returns the business hours in a user
	 	  * friendly display format.
	 	  
	 	  * Params:
	 	  * String $plugin_prefix.
	 	  * Boolean $is_twelvehour.
	 	----------------------------------------*/
		
		public static function get_business_hours_display ( $plugin_prefix, $is_twelvehour = false ) {
		
			// Setup variables
			$_hours_indexed = array();	
			$_times_display = array();
			$_html = '';
			
			$_days = array(
				'sun' => __( 'Sunday','woothemes' ), 
				'mon' => __( 'Monday', 'woothemes' ), 
				'tues' => __( 'Tuesday', 'woothemes' ), 
				'wed' => __( 'Wednesday', 'woothemes' ), 
				'thurs' => __( 'Thursday', 'woothemes' ), 
				'fri' => __( 'Friday', 'woothemes' ), 
				'sat' => __( 'Saturday', 'woothemes' )
			);
			
			$_business_hours = WTFactory::get_business_hours( $plugin_prefix );
			
			$_closed_hours = get_option( $plugin_prefix . 'closed_hours' );
			
			// Setup a zero-indexed array from $_business_hours, adding the key as
			// an index in the array.
			
			foreach ( $_business_hours as $k => $v ) {
			
				$_value = '';
				
				$times = array();
				
				$_times_string = '';
				$_time_shifts = array();
				
				$_hours_array = array();
			
				if ( $v['closed'] ) {
					
					$_value = __( 'Closed', 'woothemes' );
					
				} else {
					
					$_opening = $v['openingtime'];
					$_closing = $v['closingtime'];
					
					/*
					if ( $is_twelvehour ) {
						
						$_opening_hour = substr( $v['openingtime'], 0, 2 );
						$_opening_min = substr( $v['openingtime'], 3, 2 );
						
						$_closing_hour = substr( $v['closingtime'], 0, 2 );
						$_closing_min = substr( $v['closingtime'], 3, 2 );
						
						$_opening = date( 'h:i A', mktime( $_opening_hour, $_opening_min, 0, 12, 32, 1997 ) );
						$_closing = date( 'h:i A', mktime( $_closing_hour, $_closing_min, 0, 12, 32, 1997 ) );
					
					} else {
					
						$_opening = $v['openingtime'];
						$_closing = $v['closingtime'];
						
					} // End IF Statement
					*/
					
					if ( isset( $_closed_hours[$k] ) && ( count( $_closed_hours[$k] ) > 0 ) ) {
					
						// Determine the date to use with get_times_between().
						
						$day_num = array( '', 'sun', 'mon', 'tues', 'wed', 'thurs', 'fri', 'sat' );
										
						$current_day =  strtolower( date('D') );
						
						if ( $current_day == 'tue' ) { $current_day = 'tues'; } // End IF Statement
						if ( $current_day == 'thu' ) { $current_day = 'thurs'; } // End IF Statement
						
						$current_day_num = 0;
						$main_day_num = 0;
						
						foreach ( $day_num as $i => $day ) {
						
							if ( $day == $current_day ) { $current_day_num = $i; } // End IF Statement
							
							if ( $k == $day ) { $main_day_num = $i; } // End IF Statement
						
						} // End FOREACH Loop
						
						$current_timestamp = strtotime( date( 'Y-m-d' ) );
						
						$result = 0;
						
						if ( $main_day_num > $current_day_num ) {
						
							$result = $current_day_num - ( $current_day_num - $main_day_num ) - $current_day_num;
						
						} else if ( $main_day_num < $current_day_num ) {
						
							$result = $current_day_num + ( $main_day_num - $current_day_num ) - $current_day_num;
						
						} // End IF Statement
																
						$date = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm', $current_timestamp ), date( 'd', $current_timestamp ) + $result, date( 'Y', $current_timestamp ) ) );
							
					
						// Get all times for the current day.
						$times = WTFactory::get_times_between ( $_opening, $_closing, $plugin_prefix, $date, '', false );
						
						// DEBUG
						/*
						echo '<br /><br /><br/>TIMES<br /><br /><xmp>';
							print_r($times);
						echo '</xmp>';
						*/
						
						if ( count( $times ) > 1 ) {
						
							for ( $i = 0; $i < count( $times ); $i++ ) {

								// Make sure our times are in the correct format.
								if ( $is_twelvehour ) {
									
									$_hour = substr( $times[$i], 0, 2 );
									$_min = substr( $times[$i], 3, 2 );
									
									$_time = date( 'h:i A', mktime( $_hour, $_min, 0, 12, 32, 1997 ) );
									
									$times[$i] = $_time;
									
								} // End IF Statement
						
							} // End FOR Loop
						
							for ( $i = 0; $i < count( $times ); $i++ ) {
								
								if ( in_array ( $times[$i], $_closed_hours[$k] ) ) {
									
									// echo $times[$i];
									$times[$i] = '|';
								
								} // End IF Statement
							
							} // End FOR Loop
							
							// DEBUG
							/*
							echo '<br /><br /><br/>TIMES WITH INDICATED FOR REMOVAL<br /><br /><xmp>';
								print_r($times);
							echo '</xmp>';
							*/
							
							// Split the times into the different segments.
							$_times_string = join( ',', $times );
							$_time_shifts = explode( '|', $_times_string );
							
							// DEBUG
							/*
							echo '<br /><br /><br/>TIME SHIFTS<br /><br /><xmp>';
								print_r($_time_shifts);
							echo '</xmp>';
							*/
							
							// Clean up unnecessary shifts and strip opening and trailing commas.
							foreach ( $_time_shifts as $shift_k => $shift_v ) {
							
								if ( strlen( $shift_v ) < 5 ) {
									
									unset( $_time_shifts[$shift_k] );
								
								} // End IF Statement
							
							} // End FOREACH Loop
							
							// DEBUG
							/*
							echo '<br /><br /><br/>REMOVE MARKED TIME SHIFTS<br /><br /><xmp>';
								print_r($_time_shifts);
							echo '</xmp>';
							*/
							
							foreach ( $_time_shifts as $shift_k => $shift_v ) {
							
								$_filtered_value = $shift_v;
							
								if ( substr( $_filtered_value, 0, 1 ) == ',' ) {
								
									$_filtered_value = substr( $_filtered_value, 1, strlen( $_filtered_value ) );
								
								} // End IF Statement
								
								if ( substr( $_filtered_value, -1 ) == ',' ) {
								
									$_filtered_value = substr( $_filtered_value, 0, -1 );
								
								} // End IF Statement
								
								$_time_shifts[$shift_k] = $_filtered_value;
							
							} // End FOREACH Loop
							
							// DEBUG
							/*
							echo '<br /><br /><br/>FIX COMMAS<br /><br /><xmp>';
								print_r($_time_shifts);
							echo '</xmp>';
							*/
						
							// Split the shifts back into arrays.
							
							foreach ( $_time_shifts as $t ) {
							
								$_hours_array[] = explode( ',', $t );
							
							} // End FOREACH Loop
							
							// DEBUG
							/*
							echo '<br /><br /><br/>SPLIT TIME SHIFTS INTO HOURS<br /><br /><xmp>';
								print_r($_hours_array);
							echo '</xmp>';
							*/

							// Add the values back into the display variable.
							
							foreach ( $_hours_array as $h ) {
							
								$_value .= $h[0] . ' - ' . $h[ count( $h )-1 ] . ' <br />';
							
							} // End FOREACH Loop
							
							// DEBUG
							/*
							echo '<br /><br /><br/>ADD ITEMS TO THE $_value VARIABLE<br /><br /><xmp>';
								print_r($_value);
							echo '</xmp>';
							*/
						
						} else {
						
							$_value = $_opening . ' - ' . $_closing; // Used before we added the "closed hours" functionality.
										
						} // End IF Statement
					
					} else {
					
						if ( $is_twelvehour ) {
							
							$_opening_hour = substr( $v['openingtime'], 0, 2 );
							$_opening_min = substr( $v['openingtime'], 3, 2 );
							
							$_closing_hour = substr( $v['closingtime'], 0, 2 );
							$_closing_min = substr( $v['closingtime'], 3, 2 );
							
							$_opening = date( 'h:i A', mktime( $_opening_hour, $_opening_min, 0, 12, 32, 1997 ) );
							$_closing = date( 'h:i A', mktime( $_closing_hour, $_closing_min, 0, 12, 32, 1997 ) );
							
						} // End IF Statement
					
						 $_value = $_opening . ' - ' . $_closing; // Used before we added the "closed hours" functionality.
					
					} // End IF Statement
										
				} // End IF Statement
					
				$_hours_indexed[] = array( 'key' => $_days[$k], 'value' => $_value );
				
			} // End FOREACH Loop
			
			// Move `Sunday` to the end of $_hours_indexed.
			// $_temp_sunday = $_hours_indexed[0];
			
			$_temp_sunday = array_shift($_hours_indexed);
			$_hours_indexed[] = $_temp_sunday;
					
			// Loop through the $_business_hours and, if the next item is different
			// to the previous item, add a new index to the $_times_display array.
			
			for ( $i = 0; $i < count( $_hours_indexed ); $i++ ) {
			
				$j = $i - 1;
				
				if ( $j < 0 ) {
				
					$_times_display[] = $_hours_indexed[$i];
					
				} else {
				
					if ( $_hours_indexed[$j]['value'] === $_hours_indexed[$i]['value'] ) {
										
						$_times_display[count($_times_display) - 1]['key'] .= ', ' . $_hours_indexed[$i]['key'];
									
					} else {
						
						$_times_display[] = $_hours_indexed[$i];
						
					} // End IF Statement
					
				} // End IF Statement
				
			} // End FOR Loop
			
			// If there are times to display, format them in valid XHTML.
			if ( $_times_display ) {
			
				$_html .= '<ul class="wootable-business-hours">' . "\n";
				
				foreach ( $_times_display as $_t ) {
					
					// If more than one day is present for this time, only display the first and last.
					$_key = $_t['key'];
					$_key_formatted = '';
					$_day_keys = explode( ', ', $_key );
					if ( count( $_day_keys ) == 1 ) {
					
						$_key_formatted = $_key;
						
					} else {
						
						$_key_formatted = array_shift( $_day_keys ) . ' - ' . array_pop( $_day_keys );
						
					} // End IF Statement					
					
					$class = '';
					if ( $_t['value'] == __( 'Closed', 'woothemes' ) ) { $class = 'closed'; } else { $class = 'open'; } // End IF Statement
					
					$_html .= '<li class="' . $class . '"><span class="days">' . $_key_formatted . ':</span><span class="times">' . $_t['value'] . '</span></li>' . "\n";
					
				} // End FOREACH Loop
				
				$_html .= '</ul>' . "\n";
				
			} // End IF Statement
			
			return $_html;
			
		} // End get_business_hours_display()
		
		
		/*----------------------------------------
	 	  get_time_interval()
	 	  ----------------------------------------
	 	  
	 	  * A helper function to return the time
	 	  * interval between reservations.
	 	  
	 	  * Params:
	 	  * - String $plugin_prefix.
	 	----------------------------------------*/
		
		public static function get_time_interval ( $plugin_prefix ) {
			
			$_interval = get_option( $plugin_prefix . 'reservation_interval' );		
						
			return $_interval;
			
		} // End get_time_interval()
		
		/*----------------------------------------
	 	  display_changed_times()
	 	  ----------------------------------------
	 	  
	 	  * A helper function to display either
	 	  * the changed times select box, or a
	 	  * message, depending on whether closed
	 	  * or open.
	 	  
	 	  * Used in AJAX calls on the frontend
	 	  * and in the administration area.
	 	  
	 	  * Params:
	 	  * - String $plugin_prefix
	 	  * - Boolean $echo
	 	  * - String $time
	 	  * - Int $page_id
	 	  * - String $date
	 	  * - Boolean $is_admin
	 	----------------------------------------*/
		
		public static function display_changed_times ( $plugin_prefix, $echo, $time, $page_id = 0, $date = '', $is_admin = false ) {
			
			$_html = '';
			
			$is_closed = false;
			
	 		if ( $date == '' ) { $date = $_POST['date']; } // End IF Statement
			
			if ( $date == '' ) { $date = $_POST['reservation_date']; } // End IF Statement
			
			if ( $date == '' ) { $date = date('Y-m-d'); } // End IF Statement
			
			// Get the various business hours.
			$business_hours = get_option( $plugin_prefix . 'business_hours' );
			
			if ( $business_hours ) {
			
				$index = strtolower( date('D', strtotime($date) ) );
				$full_dayname = date('l', strtotime($date) );

				// Compensate for the colloquial convention of "thurs" instead of "thu", and "tues" instead of "tue".
				if ( $index == 'thu' ) { $index = 'thurs'; } // End IF Statement
				if ( $index == 'tue' ) { $index = 'tues'; } // End IF Statement
				
				$times = $business_hours[$index];
				
				if ( $times['closed'] ) {
				
					$is_closed = true;
					
					if ( isset( $_POST['ajax'] ) && $_POST['ajax'] == 1 && $page_id == 0 ) {
						
						$_html .= '<span class="reservation_time required">' . __( 'Closed.', 'woothemes' ) . '<input type="hidden" name="reservation_time" class="required" value="" /></span>' . "\n";
					
					} else {
					
						$_html .= '<span class="reservation_time required">' . sprintf( __( 'We are unfortunately closed on %ss.', 'woothemes' ), $full_dayname ) . '<input type="hidden" name="reservation_time" class="required" value="" /></span>' . "\n";
						
					} // End IF Statement	
				
				} else {
				
					
					// echo $date;
				
					// If we're in the admin area, allow the existing reservation's time to display.
					if ( $is_admin ) {
					
						// $time
						
						$times_array = WTFactory::get_times_between( $times['openingtime'], $times['closingtime'], $plugin_prefix, $date, '', true ); // 2010-11-01. - Added $date.
					
					} else {
					
						$times_array = WTFactory::get_times_between( $times['openingtime'], $times['closingtime'], $plugin_prefix, $date, '', true ); // 2010-11-01. - Added $date.
						
					} // End IF Statement
					
					if ( count( $times_array ) ) {
						
						// Set the time format to be used, based in the user's setting in the admin.
								
						$is_twelvehour = false;
						
						if ( get_option( $plugin_prefix . 'time_format' ) == '12' ) { $is_twelvehour = true; } // End IF Statement
						
						$_html .= '<select name="reservation_time" class="reservation_time required">' . "\n";
							
							/*
							print_r('<xmp>');
								print_r( $times_array );
							print_r('</xmp>');
							*/
						
							$separator = '_________';
							
							foreach ( $times_array as $t ) {
								
								$_selected = '';
								
								if ( $t == $time ) { $_selected = ' selected="selected"'; } // End IF Statement
								
								// Set the current value to a separate variable for use in the "time format" logic. // 2010-11-02.
								
								$_display = $t;
								
								if ( $is_twelvehour && ( $t != $separator ) ) {
																
									$_hour = substr( $t, 0, 2 );
									$_min = substr( $t, 3, 2 );
									
									$_display = date( 'h:i A', mktime( $_hour, $_min, 0, 12, 32, 1997 ) );
									
								} // End IF Statement
								
								// Set an empty value for the time seperator.
								
								if ( $t == $separator ) { $t = ''; } // End IF Statement
								
								$_html .= '<option value="' . $t . '"' . $_selected . '>' . $_display . '</option>' . "\n";
								
							} // End FOREACH Loop
						
						$_html .= '</select>' . "\n";
					
					} else {
					
					// Display this if no times are available.
					
					
					if ( isset( $_POST['ajax'] ) && $_POST['ajax'] == 1 && $page_id == 0 ) {
						
						$_html .= '<span class="reservation_time required">' . __( 'None.', 'woothemes' ) . '<input type="hidden" name="reservation_time" class="required" value="" /></span>' . "\n";
					
					} else {
					
						$_html .= '<span class="reservation_time required">' . __( 'None available.', 'woothemes' ) . '<input type="hidden" name="reservation_time" class="required" value="" /></span>' . "\n";
						
					} // End IF Statement
					
					
					} // End IF Statement
					
				} // End IF Statement	

			} // End IF Statement
			
			if ( $echo ) {
			
				echo $_html;
				
			} else {
				
				return $_html;
				
			} // End IF Statement
			
		} // End display_changed_times ()
		
		/*----------------------------------------
	 	  get_saved_email()
	 	  ----------------------------------------
	 	  
	 	  * A helper function to get the e-mail
	 	  * address desired for use by the user.
	 	----------------------------------------*/
		
		public static function get_saved_email () {
		
			$_email = '';
			
			$_cookie_token = WTFactory::get_cookie_token();
	 				
			// If a $_COOKIE is in place, use the cookie's e-mail address.	 				
			if ( isset( $_COOKIE['wootable_' . $_cookie_token . '_email'] ) && is_email( $_COOKIE['wootable_' . $_cookie_token . '_email'] ) ) {
			
				$_email = $_COOKIE['wootable_' . $_cookie_token . '_email'];
				
			// Otherwise, if no cookie is present, set a temporary $_SESSION instead.
			} else if ( isset( $_SESSION['wootable_' . $_cookie_token . '_email'] ) && is_email( $_SESSION['wootable_' . $_cookie_token . '_email'] ) ) {
			
				$_email = $_SESSION['wootable_' . $_cookie_token . '_email'];	 				
			
			// Otherwise, grab the data from the query string, validate it (and the key) and set a temporary $_SESSION for the user.	
			} else {
			
				if ( isset( $_GET['e-mail'] ) && isset( $_GET['key'] ) && md5( urldecode( $_GET['e-mail'] ) ) == $_GET['key'] ) {
				
					$_email = urldecode( $_GET['e-mail'] );
					
					$_SESSION['wootable_' . $_cookie_token . '_email'] = $_email;
					
				} // End IF Statement
				
			} // End IF Statement
			
			return $_email;
			
		} // End get_saved_email()
		
		/*----------------------------------------
	 	  get_cookie_token()
	 	  ----------------------------------------
	 	  
	 	  * A helper function to generate a unique
	 	  * token, based on the domain name, to be
	 	  * used with WooTable.
	 	----------------------------------------*/
		
		public static function get_cookie_token () {
		
			$_cookie_token = get_bloginfo('url');
			$_cookie_token = str_replace( 'http://', '', $_cookie_token );
			$_cookie_token = str_replace( 'www.', '', $_cookie_token );
			$_cookie_token = str_replace( '/', '', $_cookie_token );
			$_cookie_token = str_replace( '?', '', $_cookie_token );
			$_cookie_token = str_replace( ':', '', $_cookie_token );
			
			$_cookie_token_bits = explode( '.', $_cookie_token );
			
			$_cookie_token = $_cookie_token_bits[0];
			
			return $_cookie_token;
			
		} // End get_cookie_token()
		
		/*----------------------------------------
	 	  get_max_number_of_people()
	 	  ----------------------------------------
	 	  
	 	  * A helper function to return an integer
	 	  * of the maximum number of people a
	 	  * single table in the restaurant can seat.
	 	----------------------------------------*/
		
		public static function get_max_number_of_people () {
		
			global $wpdb;
			
			$max_number_of_people = 0;
			
			$_term_args = array(
								'hide_empty' => 0, 
								'fields' => 'ids', 
								'hierarchical' => false
								);
								
			$_terms = get_terms( array('tables'), $_term_args );
			
			if ( $_terms ) {
			
				$_terms_string = join( ',', $_terms );
				
				$query = "SELECT meta_value as number_of_seats 
						  FROM " . $wpdb->prefix . "woo_tables_meta 
						  WHERE meta_key = 'number_of_seats' 
						  AND woo_tables_id IN (" . $_terms_string . ") GROUP BY number_of_seats ORDER BY number_of_seats DESC";
			
				$rs = $wpdb->get_results($query, ARRAY_A);
				
				if ( $rs ) {
				
					$max_number_of_people = $rs[0]['number_of_seats'];
					
				} // End IF Statement
			
			} // End IF Statement
			
			return $max_number_of_people;
			
		} // End get_max_number_of_people()
		
		/*----------------------------------------
	 	  get_business_hours()
	 	  ----------------------------------------
	 	  
	 	  * A helper function to return an array
	 	  * of all business hours, as stored in
	 	  * the database.
	 	  
	 	  * Params:
	 	  * - String $plugin_prefix
	 	----------------------------------------*/
		
		public static function get_business_hours ( $plugin_prefix ) {
		
			$business_hours = get_option( $plugin_prefix . 'business_hours' );
		
			return $business_hours;
			
		} // End get_business_hours()
		
		/*----------------------------------------
	 	  get_times_between()
	 	  ----------------------------------------
	 	  
	 	  * A helper function to return an array
	 	  * of the times between a $start and
	 	  * $finish time, with 30 minute intervals.
	 	  
	 	  * Params:
	 	  * - int $start
	 	  * - int $finish
	 	  * - string $plugin_prefix
	 	  * - string $time_to_skip
	 	----------------------------------------*/
	 	
		public static function get_times_between ( $start, $finish, $plugin_prefix, $date, $time_to_skip = '', $exclude_closed_hours = false ) {
					
			$times = array();
			$hours = '';
			
			$_yesterday = '';
			
			// Get the time interval between reservations at a single table
			$_interval = WTFactory::get_time_interval( $plugin_prefix );
			
			// TO DO
			// Pass date of reservation to this function. Use the date to get the
			// opening and closing times of the previous day. If the closing time is
			// after midnight, display those midnight hours (until closing time) in this select box.
			// That way, no extra logic is required to switch the date on the datepicker
			// as the after midnight hours of the previous day are in fact hours on the selected day.
			
			// Make sure the date value passed is in fact a valid date.
			// If it isn't, stop the function.
			
			$_is_date = false;
			
			$_date_year = substr( $date, 0, 4 );
			$_date_month = substr( $date, 5, 2 );
			$_date_day = substr( $date, 8, 2 );
			
			if ( checkdate( $_date_month, $_date_day, $_date_year ) ) {
				
				$_is_date = true;
			
			} // End IF Statement
			
			if ( ! $_is_date ) { return; } // End IF Statement
			
			// Get today's day.
			$_today_day = strtolower( date( 'D', mktime( 0, 0, 0, $_date_month,( $_date_day ), $_date_year ) ) );
			
			// Get the previous date.
			$_yesterday = date( 'Y-m-d', mktime( 0, 0, 0, $_date_month,( $_date_day-1 ), $_date_year ) );
			
			$_yesterday_day = strtolower( date( 'D', mktime( 0, 0, 0, $_date_month,( $_date_day-1 ), $_date_year ) ) );
			
			// Compensate for the colloquial convention of "thurs" instead of "thu", and "tues" instead of "tue".
			if ( $_today_day == 'thu' ) { $_today_day = 'thurs'; } // End IF Statement
			if ( $_today_day == 'tue' ) { $_today_day = 'tues'; } // End IF Statement
			
			if ( $_yesterday_day == 'thu' ) { $_yesterday_day = 'thurs'; } // End IF Statement
			if ( $_yesterday_day == 'tue' ) { $_yesterday_day = 'tues'; } // End IF Statement
			
			// Get the business hours array and determine the opening and closing times for "yesterday".
			$_businesshours = WTFactory::get_business_hours( $plugin_prefix );
			
			// Get reserved times for the specified date. // 2010-11-11.
			// $reserved_times = array();
			$reserved_times = WTFactory::get_reserved_times_for_date( $date );
			
			// Remove the current time selected from the $reserved_times array, if it is set.
			if ( $time_to_skip != '' ) {
			
				for ( $j = 0; $j < count($reserved_times); $j++ ) {
				
					if ( $reserved_times[$j] == $time_to_skip ) {
					
						unset( $reserved_times[$j] );
					
					} // End IF Statement
				
				} // End FOR Loop
			
			} // End IF Statement
			
			// Add all the hours after midnight to an array.
			$_aftermidnight = array();
			
			$_yesterday_hours = $_businesshours[$_yesterday_day];
			
			// Cater for restaurants that close after midnight. - 2010-10-25.		
			$_beforenoon = '11:59';
			
			// echo $_yesterday_hours['closingtime'];
			
			if ( $_yesterday_hours['closingtime'] <= $_beforenoon ) {
			
				$_yesterday_hour = substr( $_yesterday_hours['closingtime'], 0, 2 );
			
				for ( $i = 0; $i <= $_yesterday_hour; $i++ ) {
				
					$hour = $i;
					
					if ( $hour < 10 && $hour > 0 ) { $hour = '0' . $hour; } // End IF statement
					
					$full_hour = $hour . ':00';
					$half_hour = $hour . ':30';										
					
					// $_aftermidnight[] = $full_hour;
					
					if ( $full_hour < $_yesterday_hours['closingtime'] ) { $_aftermidnight[] = $full_hour; } // End IF Statement
					
					// Don't add the extra $half_hour for the end time.
					if ( $_interval == '0.5' ) {

						if ( $half_hour == $_yesterday_hours['closingtime'] ) {} else { $_aftermidnight[] = $half_hour; } // End IF Statement
						
					} // End IF Statement
					
				} // End FOR Loop
				
			} // End IF Statement
			
			// Remove reserved times from the $_aftermidnight array.
			if ( count( $reserved_times ) > 0 && count( $_aftermidnight ) > 0 ) {
			
				for ( $i = 0; $i < count( $_aftermidnight ); $i++ ) {
				
					if ( in_array ( $_aftermidnight[$i], $reserved_times ) ) {
					
						unset( $_aftermidnight[$i] );
					
					} // End IF Statement
				
				} // End FOR Loop
			
			} // End IF Statement
			
			
			if ( count( $_aftermidnight ) > 0 ) {
			
				foreach ( $_aftermidnight as $_a ) {
				
					$times[] = $_a;
					
				} // End FOREACH Loop
				
				$times[] = '_________';
				
			} // End IF Statement
			
			// If the finish is after midnight, set the finish at 23:59.
			
			if ( $finish <= $_beforenoon ) {
				
				$orig_finish = $finish;
				$finish = '23:59';
				
			} // End IF Statement
			
			/*
			// If we're dealing with an after midnight time, add these times to the array. - 2010-10-25.				
			if ( isset( $orig_finish ) ) {
			
				$orig_start = '00';
				$orig_finish = substr( $orig_finish, 0, 2 );
			
				for ( $i = $orig_start; $i <= $orig_finish; $i++ ) {
				
					$hour = $i;
					
					if ( $hour < 10 && $hour > 0 ) { $hour = '0' . $hour; } // End IF statement
					
					$full_hour = $hour . ':00';
					$half_hour = $hour . ':30';										
					
					$times[] = $full_hour;
					
					// Don't add the extra $half_hour for the end time.
					if ( $_interval == '0.5' ) {
						if ( $i == $orig_finish ) {} else { $times[] = $half_hour; } // End IF Statement
					} // End IF Statement
					
				} // End FOR Loop
				
			} // End IF Statement
			*/
			
			$start_ampm = '';
			$end_ampm = '';
			
			if ( strlen( $start ) > 4 ) { $start_ampm = substr( $start, 6, 2 ); }
			if ( strlen( $end ) > 4 ) { $end_ampm = substr( $end, 6, 2 ); }
			
			// Get only the hour values for the start and end times.
			$start = substr( $start, 0, 2 );
			$end = substr( $finish, 0, 2 );
		
			for ( $i = $start; $i <= $finish; $i++ ) {
			
				$hour = $i;
				
				if ( $hour < 10 && $hour > 0 && ( substr( $hour, 0, 1 ) != '0' ) ) { $hour = '0' . $hour; } // End IF statement
				
				$full_hour = $hour . ':00';
				$half_hour = $hour . ':30';										
				
				// $times[] = $full_hour;
				
				// Don't add the extra $half_hour for the end time.
				
				if ( $full_hour < $finish ) { $times[] = $full_hour; } // End IF Statement
					
					// Don't add the extra $half_hour for the end time.
					if ( $_interval == '0.5' ) {
					
						if ( $half_hour >= $finish ) {} else { $times[] = $half_hour; } // End IF Statement
						
					} // End IF Statement
				
				/*
				if ( $_interval == '0.5' ) {
					if ( $i == $finish ) {} else { $times[] = $half_hour; } // End IF Statement
				} // End IF Statement
				*/
				
			} // End FOR Loop
		
			// Remove reserved times from the main $times array.
			if ( count( $reserved_times ) > 0 && count( $times ) > 0 ) {
			
				for ( $i = 0; $i < count( $times ); $i++ ) {
				
					if ( in_array ( $times[$i], $reserved_times ) ) {
					
						unset( $times[$i] );
					
					} // End IF Statement
				
				} // End FOR Loop
			
			} // End IF Statement
			
			// If the option is set, exclude closed hours from the $times array.
			if ( $exclude_closed_hours ) {
			
				$closed_hours = get_option( $plugin_prefix . 'closed_hours' );
				
				if ( isset( $closed_hours[$_today_day] ) && count( $closed_hours[$_today_day] ) > 0 ) {
				
					foreach ( $times as $k => $v ) {
						if ( in_array( $v, $closed_hours[$_today_day] ) ) {
							unset( $times[$k] );
						} // End IF Statement
					} // End FOREACH Loop
								
				} // End IF Statement
			
			} // End IF Statement
			
			return $times;
			
		} // End get_times_between()
		
		/*----------------------------------------
	 	  get_reserved_times_for_date()
	 	  ----------------------------------------
	 	  
	 	  * A helper function to return all times
	 	  * that are unavailable for a specified
	 	  * date in the calendar.
	 	  
	 	  * Params:
	 	  * - string $date
	 	----------------------------------------*/
		
		public static function get_reserved_times_for_date ( $date ) {
		
			global $wpdb;
			
			$reserved_times = array();
			
			$query = "SELECT $wpdb->posts.ID, meta_time.meta_value as reservation_time, meta_date.meta_value as reservation_date, meta_people.meta_value as number_of_people  
						FROM $wpdb->posts 
						LEFT JOIN $wpdb->postmeta as meta_date ON($wpdb->posts.ID = meta_date.post_id) 
						LEFT JOIN $wpdb->postmeta as meta_time ON($wpdb->posts.ID = meta_time.post_id) 
						LEFT JOIN $wpdb->postmeta as meta_status ON($wpdb->posts.ID = meta_status.post_id) 
						LEFT JOIN $wpdb->postmeta as meta_people ON($wpdb->posts.ID = meta_people.post_id) 
						WHERE $wpdb->posts.post_type = 'reservation' 
						AND meta_date.meta_key = 'reservation_date' 
						AND meta_date.meta_value = '$date' 
						AND meta_status.meta_key = 'reservation_status' 
						AND meta_status.meta_value != 'cancelled' 
						AND meta_time.meta_key = 'reservation_time' 
						AND meta_people.meta_key = 'number_of_people' 
						AND $wpdb->posts.post_status = 'publish'";
			
			// $query .= "	GROUP BY reservation_time";
			
	 		// Execute the query
	 		$rs = $wpdb->get_results( $query, ARRAY_A );
	 		
	 		if ( count( $rs ) ) {
	 		
	 			foreach ( $rs as $r ) {
	 			
	 				// Check if other tables are available for the time, date and number of people at the table.
	 				
	 				// TO DO - move get_table_to_seat() to a more generic location.
	 				// get_table_to_seat ( $number_of_people, $time, $date, $current_tables = array(), $current_id = 0 )
	 				 				
	 				$tables = WooTable_FrontEnd::get_table_to_seat( $r['number_of_people'], $r['reservation_time'], $r['reservation_date'] );
	 				
	 				if ( $tables == 0 ) {
	 				
	 					$reserved_times[] = $r['reservation_time'];
	 				
	 				} // End IF Statement
	 			
	 			} // End FOREACH Loop
	 		
	 		} // End IF Statement
	 		
	 		return $reserved_times;
		
		} // End get_reserved_times_for_date()
		
		/*----------------------------------------
	 	  get_available_tables()
	 	  ----------------------------------------
	 	  
	 	  * A helper function to return either an
	 	  * array of or a single available table.
	 	  
	 	  * Params:
	 	  * - string $date
	 	  * - string $time
	 	  * - int $number_of_people
	 	----------------------------------------*/
	 	
	 	public static function get_available_tables ( $date, $time, $number_of_people ) {
	 	
	 		$table_id = null;
	 		
	 		return $table_id;
	 	
	 	} // End get_available_tables()
	 	
	 	/*----------------------------------------
	 	  attempt_table_combo()
	 	  ----------------------------------------
	 	  
	 	  * A helper function which attempts to
	 	  * combine two or more tables to match
	 	  * the number of people in a reservation.
	 	  
	 	  * Params:
	 	  * - string $date
	 	  * - string $time
	 	  * - int $number_of_people
	 	----------------------------------------*/
	 	
	 	public static function attempt_table_combo ( $date, $time, $number_of_people  ) {
	 	
	 		$table_ids = array();
	 		
	 		return $table_ids;
	 	
	 	} // End attempt_table_combo()
		
	} // End Class WTFactory
?>