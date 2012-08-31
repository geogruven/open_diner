<?php
/****************************************
Section Name: Array of Fields
****************************************/

$matty_prefix 			= 'wootable_';


$matty_field_array = array(
					/*array(
						'field' => 'send_confirmation_to_restaurant', 
						'message' => __( 'Please specify if you would like all reservation confirmation emails to be CCed to your email address.', 'woothemes' ), 
						'type' => 'text', 
						'required' => 0
						),*/
					array(
						'field' => 'reservation_interval', 
						'message' => __( 'Please select the time interval between reservations at a single table.', 'woothemes' ), 
						'type' => 'text', 
						'required' => 1
						),
					array(
						'field' => 'page_booking', 
						'message' => __( 'Please select the page that is to display the booking form.', 'woothemes' ), 
						'type' => 'text', 
						'required' => 1
						),
					array(
						'field' => 'page_manage', 
						'message' => __( 'Please select the page that is to display the upcoming bookings.', 'woothemes' ), 
						'type' => 'text', 
						'required' => 1
						),
					array(
						'field' => 'confirmation_box_message', 
						'message' => __( 'Please enter the text you would like to display in the reservation confirmation popup window.', 'woothemes' ), 
						'type' => 'text', 
						'required' => 1
						),
					array(
						'field' => 'time_format', 
						'message' => __( 'Please select the format in which you would like to display booking times on your website\'s frontend.', 'woothemes' ), 
						'type' => 'text', 
						'required' => 1
						),
					array(
						'field' => 'date_format', 
						'message' => __( 'Please select the format in which you would like to display booking dates on your website\'s frontend.', 'woothemes' ), 
						'type' => 'text', 
						'required' => 1
						),
					array(
						'field' => 'default_status', 
						'message' => __( 'Please select the default status for reservations made from your website\'s frontend.', 'woothemes' ), 
						'type' => 'text', 
						'required' => 1
						),
					array(
						'field' => 'email_pleaseconfirm', 
						'message' => __( 'Please enter the text you would like to display in the "Please Confirm" e-mail message.', 'woothemes' ), 
						'type' => 'text', 
						'required' => 1
						),
					array(
						'field' => 'email_thankyou', 
						'message' => __( 'Please enter the text you would like to display in the "Thank You" e-mail message.', 'woothemes' ), 
						'type' => 'text', 
						'required' => 1
						),
					array(
						'field' => 'email_statuschange', 
						'message' => __( 'Please enter the text you would like to display in the "Status Change" e-mail message to the user.', 'woothemes' ), 
						'type' => 'text', 
						'required' => 1
						),
					array(
						'field' => 'email_specialrequest', 
						'message' => __( 'Please enter the text you would like to display in the "Special Request" e-mail message to the user.', 'woothemes' ), 
						'type' => 'text', 
						'required' => 1
						),
					array(
						'field' => 'adminemail_reservationmade', 
						'message' => __( 'Please enter the text you would like to display in the "Reservation Made" e-mail message to the administrator.', 'woothemes' ), 
						'type' => 'text', 
						'required' => 1
						),
					array(
						'field' => 'adminemail_statuschange', 
						'message' => __( 'Please enter the text you would like to display in the "Status Change" e-mail message to the administrator.', 'woothemes' ), 
						'type' => 'text', 
						'required' => 1
						),
					array(
						'field' => 'adminemail_specialrequest', 
						'message' => __( 'Please enter the text you would like to display in the "Special Request" e-mail message to the administrator.', 'woothemes' ), 
						'type' => 'text', 
						'required' => 1
						),
					array(
						'field' => 'reserve_button_text', 
						'message' => __( 'Please enter the text you would like to display on the "Reserve Table" button.', 'woothemes' ), 
						'type' => 'text', 
						'required' => 1
						),
					array(
						'field' => 'view_button_text', 
						'message' => __( 'Please enter the text you would like to display on the "View Reservations" button.', 'woothemes' ), 
						'type' => 'text', 
						'required' => 1
						)
					);

/****************************************
Section Name: Form Processing
****************************************/

$days = array(
				'sun' => __( 'Sunday', 'woothemes' ), 
				'mon' => __( 'Monday', 'woothemes' ), 
				'tues' => __( 'Tuesday', 'woothemes' ), 
				'wed' => __( 'Wednesday', 'woothemes' ), 
				'thurs' => __( 'Thursday', 'woothemes' ), 
				'fri' => __( 'Friday', 'woothemes' ), 
				'sat' => __( 'Saturday', 'woothemes' )
			);

if (isset($_POST['wootable_update'])) {

$error_array 			= array();
$error_array 			= NULL;
													
	/*---Create Empty Variables----------*/
	
	foreach ($matty_field_array as $field) {
		${'v_' . $matty_prefix . $field['field']} = ''; echo "\n";
	}
	
	/*---Create POST variables-----------*/
	
	foreach ($matty_field_array as $field) {	
		${'v_' . $matty_prefix . $field['field']} = stripslashes( trim( strip_tags( $_POST[$matty_prefix . $field['field']] ) ) ); echo "\n";
	}
	
	/*---Form Validation-----------------*/
	
	foreach ($matty_field_array as $field) {
		
		if ($field['required'] == 1) {
		
			if (!isset($_POST[$matty_prefix . $field['field']]) || ${'v_' . $matty_prefix . $field['field']} == '') {
			
				$error_array[] = $field['message'];
			
			} else if ($field['type'] == 'email' && !is_email(${'v_' . $matty_prefix . $field['field']})) {
		
				$error_array[] = 'The email address provided is invalid. Please re-enter your email address.';
		
			}
		
		} // End IF Statement
	}
	
	/*---Custom hours validation---------*/
	
	$business_hours = array();
	
	$day_tokens = array_keys( $days ); // 2010-10-25.
	
	foreach ( $days as $k => $v ) {
	
		// Determine the token for the next day. // 2010-10-25.
		if ( $k == 'sat' ) { $_nextday = 'sun'; }
		else { $_nextday = next( $day_tokens ); } // End IF Statement
	
		$day = array();
		
		$closed_value = 0;
		
		if ( isset( $_POST[$k . '_closed'] ) && $_POST[$k . '_closed'] == 1 ) { $closed_value = 1; } // End IF Statement
		
		$day['openingtime'] = $_POST[$k . '_openingtime'];
		$day['closingtime'] = $_POST[$k . '_closingtime'];
		$day['closed'] = $closed_value;
		
		/*
			Logic explanation:
			------------------
			
			If the closing time for a day is later than the opening time of the next,
			throw an error message.
			
			Otherwise, if the closing time is after 12am, make sure that the restaurant
			is open the next day.
		*/
		
		$_beforenoon = '11:59';
		
		if ( ( $day['closingtime'] >= $_POST[$_nextday . '_openingtime'] ) && $day['closingtime'] <= $_beforenoon ) { // 2010-10-25.
		// if ( $day['openingtime'] >= $day['closingtime'] ) { // Replaced by above line. - 2010-10-25.
			
			// $error_array[] = $v . '\'s opening time is later than its closing time.';
			$error_array[] = $v . '\'s closing time is later than ' . $days[$_nextday] . '\'s opening time.';
		
		} else if ( ( $day['closingtime'] <= $_beforenoon ) && $_POST[$_nextday . '_closed'] ) {
		
			$error_array[] = $days[$_nextday] . ' is closed. Please adjust the closing time for ' . $v . '.';
		
		} else {
			
			$business_hours[$k] = $day;
			
		} // End IF Statement
		
		// $business_hours[$k] = $day;
		
	} // End FOREACH Loop
	
	/*---Custom closed hours validation---------*/
	
	$closed_hours = array();
	
	$day_tokens = array_keys( $days ); // 2010-12-02.
	
	// Take each "day" token and check if hours have been posted for it.
	
	foreach ( $day_tokens as $d ) {
	
		if ( isset( $_POST['closed_hours_' . $d] ) ) {
		
			$closed_hours[$d] = $_POST['closed_hours_' . $d];
		
		} // End IF Statement
	
	} // End FOREACH Loop

	/*---Error Reports & Data Insertion--*/
	
	if ($error_array == NULL) {
	
		$matty_options = array();
		
		foreach ($matty_field_array as $field) {
			
			if ($field['type'] == 'nav') {
			
				$pages_array = $_POST[$matty_prefix . $field['field']];
				$pages_csv = '';
				for ($i = 0; $i < count($pages_array); $i++)
				{
					$pages_csv .= $pages_array[$i]; 
					if ($i < count($pages_array)-1 && $i < count($pages_array)) {
						$pages_csv .= ',';
					} // End IF Statement
			
				} // End FOR Loop
			
				$matty_options[] = array($matty_prefix . $field['field'], $pages_csv);
			} else {
				$matty_options[] = array($matty_prefix . $field['field'], ${'v_' . $matty_prefix . $field['field']});
			}
			
		} // End FOREACH
		
		// Save the business hours.
		$matty_options[] = array( $matty_prefix . 'business_hours', $business_hours );
		
		// Save the closed hours.
		$matty_options[] = array( $matty_prefix . 'closed_hours', $closed_hours );
		
		/*---Custom Information For Times----*/
		
		
		/*---Update Database Fields----------*/
		
		foreach ($matty_options as $option) {
			
			update_option($option[0], $option[1]);
		} // End FOREACH Loop
		
		echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . esc_html( $title ) . " settings have been updated successfully.</strong></p></div>";
	
	} else {
		echo '<div id="message" class="error"><p><strong>The following errors have occurred:</strong><br /><ul>';
		for($i = 0; $i < count($error_array); $i++) {
		
			echo '<li>' . $error_array[$i] . '</li>';
		
		}
		echo '</ul></p></div>';
	
	} // End IF Statement

} // End $_POST IF Statement

/*---Variables For Form--------------*/

foreach ($matty_field_array as $field) {
	
	if (isset($_POST[$matty_prefix . $field['field']])) {
	
		${'v_' . $matty_prefix . $field['field']} = $_POST[$matty_prefix . $field['field']];
	
	} else {

		${'v_' . $matty_prefix . $field['field']} = get_option($matty_prefix . $field['field']);
		
	} // End IF Statement
	
	// Strip out unnecessary slashes.
	${'v_' . $matty_prefix . $field['field']} = stripslashes( ${'v_' . $matty_prefix . $field['field']} );

} // End FOREACH

${'v_' . $matty_prefix . 'business_hours'} = '';

${'v_' . $matty_prefix . 'closed_hours'} = '';

if ( $_POST ) {

	// Grab the posted business hours.

	foreach ( $days as $k => $v ) {
	
		$day = array();
		
		$closed_value = 0;
		
		if ( isset( $_POST[$k . '_closed'] ) && $_POST[$k . '_closed'] == 1 ) { $closed_value = 1; } // End IF Statement
		
		$day['openingtime'] = $_POST[$k . '_openingtime'];
		$day['closingtime'] = $_POST[$k . '_closingtime'];
		$day['closed'] = $closed_value;
		
		${'v_' . $matty_prefix . 'business_hours'}[$k] = $day;
		
		// Grab the posted closed hours.
		
		if ( isset( $_POST['closed_hours_' . $k] ) ) {
		
			${'v_' . $matty_prefix . 'closed_hours'}[$k] = $_POST['closed_hours_' . $k];
		
		} // End IF Statement
		
	} // End FOREACH Loop

} else {
	
	${'v_' . $matty_prefix . 'business_hours'} = get_option( $matty_prefix . 'business_hours' );
	
	${'v_' . $matty_prefix . 'closed_hours'} = get_option( $matty_prefix . 'closed_hours' );
	
	// print_r( ${'v_' . $matty_prefix . 'business_hours'} );
	
	// print_r( ${'v_' . $matty_prefix . 'closed_hours'} );

} // End IF Statement
	
/****************************************/
?>