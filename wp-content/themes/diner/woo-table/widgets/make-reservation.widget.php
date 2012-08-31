<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: A widget to display the "Make a Reservation" form for the WooTable WordPress plugin.
Date Created: 2010-09-18.
Author: Matty.
Since: 0.0.2.8


TABLE OF CONTENTS

- function WooTable_Widget_MakeReservation () (constructor)
- function widget ()
- function update ()
- function form ()

-----------------------------------------------------------------------------------*/

class WooTable_Widget_MakeReservation extends WP_Widget {

	/*----------------------------------------
	  WooTable_Widget_MakeReservation()
	  ----------------------------------------
	  
	  * The constructor. Sets up the widget.
	----------------------------------------*/
	
	function WooTable_Widget_MakeReservation () {
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget-wootable-makereservation', 'description' => __('Displays a basic reservation form.', 'woothemes' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'widget_wootable_makereservation' );

		/* Create the widget. */
		$this->WP_Widget( 'widget_wootable_makereservation', __('WooTable - Make a Reservation', 'woothemes' ), $widget_ops, $control_ops );
		
	} // End WooTable_Widget_MakeReservation()

	/*----------------------------------------
	  widget()
	  ----------------------------------------
	  
	  * Displays the widget on the frontend.
	----------------------------------------*/
	
	function widget( $args, $instance ) {
		extract( $args );
		
				/* Our variables from the widget settings. */
				$title = apply_filters('widget_title', $instance['title'] );
				
				/* Before widget (defined by themes). */
				echo $before_widget;
		
				/* Display the widget title if one was input (before and after defined by themes). */
				if ( $title ) {
					echo $before_title . $title . $after_title;
				}
				
				/* Widget content. */
				
				$html = '';
				
					global $wootable;
		
					$content .= '<form name="wootable-booking-form-widget" method="post" action="' . get_permalink( $wootable->frontend->bookings_page ) . '">' . "\n";
				 			
					$reservation_date_real = '';
					
					if ( ! $reservation_date_real ) { $reservation_date_real = date( 'Y-m-d' ); } // End IF Statement
			
					$content .= '<div id="wootable-calendar-holder"><div id="wootable-calendar-widget"></div></div><!--/#wootable-calendar-holder-->' . "\n";
					$content .= '<input type="hidden" name="reservation_date" id="reservation_date" class="input-reservation_date input-text" value="" />' . "\n";
					$content .= '<input type="hidden" name="reservation_widget" id="reservation_widget" value="widget" />' . "\n";
					$content .= '<input type="hidden" name="reservation_date_real" id="reservation_date_real" class="input-reservation_date_real input-text" value="' . $reservation_date_real . '" />' . "\n";
				
					// Begin the bookings form.
					
					$_max_number_of_people = WTFactory::get_max_number_of_people();
					
					$content .= '<p class="form-field people">' . "\n";
						$content .= '<label for="number_of_people">' . __( 'People', 'woothemes' ) . ':</label>' . "\n";
						
						if ( $_max_number_of_people ) {
							
							$content .= '<select name="number_of_people" class="number_of_people">' . "\n";
							
								$selected_number = '';
							
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
							
							$content .= '</select>' . "\n";
							
							
						} else {
						
							$content .= __( 'No tables are currently listed. Please check back soon.', 'woothemes' );
							
						} // End IF Statement
					
					$content .= '</p>' . "\n";
					
					$content .= '<p class="form-field time">' . "\n";
						$content .= '<label for="reservation_time">' . __( 'Time', 'woothemes' ) . ':</label>' . "\n";
						
						$content .= WTFactory::display_changed_times( $wootable->plugin_prefix, false, $_POST['time'] ); // 2010-11-02.
						
						/*
						$business_hours = WTFactory::get_business_hours( $wootable->plugin_prefix );
						
						$index = strtolower( date('D', strtotime($date) ) );
						
						// Compensate for the colloquial convention of "thurs" instead of "thu", and "tues" instead of "tue".
						if ( $index == 'thu' ) { $index = 'thurs'; } // End IF Statement
						if ( $index == 'tue' ) { $index = 'tues'; } // End IF Statement
						
						$times = $business_hours[$index];
						
						$times_array = WTFactory::get_times_between( $times['openingtime'], $times['closingtime'], $wootable->plugin_prefix );
						
						if ( $times_array ) {
						
							$content .= '<select name="reservation_time" class="reservation_time">' . "\n";
							
								$selected_hour = '';
							
								foreach ( $times_array as $t ) {
									
									$_selected = '';
									
									if ( $t == $selected_hour ) { $_selected = ' selected="selected"'; } // End IF Statement
									
									$content .= '<option value="' . $t . '"' . $_selected . '>' . $t . '</option>' . "\n";
									
								} // End FOREACH Loop
							
							$content .= '</select>' . "\n";
						
						} // End IF Statement
						*/
			
					$content .= '</p>' . "\n";
					
					// Generate a confirmation message based on the data entered on the form.
					
					$business_hours = WTFactory::get_business_hours( $wootable->plugin_prefix );
					
					$index = strtolower( date('D', strtotime($reservation_date_real) ) );
					
					// Compensate for the colloquial convention of "thurs" instead of "thu", and "tues" instead of "tue".
					if ( $index == 'thu' ) { $index = 'thurs'; } // End IF Statement
					if ( $index == 'tue' ) { $index = 'tues'; } // End IF Statement
					
					$times = $business_hours[$index];
					
					$_message = '';
					
					$_friendly_date = date( 'l, F jS', strtotime( $reservation_date_real ) );
					
					$_friendly_time = __( ' at ', 'woothemes' );
					
					if ( is_array( $times ) ) { $_friendly_time .= $times['openingtime']; } // End IF Statement
					
					$_friendly_number = '';
					
					if ( $_max_number_of_people ) { $_friendly_number = 1; } // End IF Statement
					
					$_message .= sprintf( __( '%s for a party of %s', 'woothemes' ), $_friendly_date . $_friendly_time, $_friendly_number );
					
					$content .= '<p><span class="confirmation_message">' . $_message . '</span></p>';
					
					$content .= '<p class="form-field buttons-set">' . "\n";
						$content .= '<input class="button button-submit" type="submit" value="'. __('Reserve Table', 'woothemes' ) . '" />' . "\n";
					$content .= '</p>' . "\n";
				
				$content .= '</form><div class="clear"></div>' . "\n";
				
				$html = $content;

								
				echo $html;
				
		
				/* After widget (defined by themes). */
				echo $after_widget;
		
	} // End widget()

	/*----------------------------------------
	  update()
	  ----------------------------------------
	  
	  * Function to update the settings from
	  * the form() function.
	  
	  * Params:
	  * - Array $new_instance
	  * - Array $old_instance
	----------------------------------------*/
	function update ( $new_instance, $old_instance ) {
		
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
		
	} // End update()

	/*----------------------------------------
	  form()
	  ----------------------------------------
	  
	  * The form on the widget control in the
	  * widget administration area.
	  
	  * Make use of the get_field_id() and 
	  * get_field_name() function when creating
	  * your form elements. This handles the confusing stuff.
	  
	  * Params:
	  * - Array $instance
	----------------------------------------*/

	function form ( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Reservations', 'woothemes' ) );
		$instance = wp_parse_args( (array) $instance, $defaults );		
?>
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:','woothemes'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>

<?php
	
	} // End form()
	
} // End Class
?>