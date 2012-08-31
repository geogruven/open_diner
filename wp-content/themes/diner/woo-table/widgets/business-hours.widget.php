<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: A widget to display the "Business Hours" in a friendly format for the WooTable WordPress plugin.
Date Created: 2010-09-28.
Author: Matty.
Since: 0.0.3.2


TABLE OF CONTENTS

- function WooTable_Widget_BusinessHours () (constructor)
- function widget ()
- function update ()
- function form ()

-----------------------------------------------------------------------------------*/

class WooTable_Widget_BusinessHours extends WP_Widget {

	/*----------------------------------------
 	  Class Variables
 	  ----------------------------------------
 	  
 	  * Setup of variable placeholders, to be
 	  * populated when the constructor runs.
 	----------------------------------------*/

	var $plugin_prefix;

	/*----------------------------------------
	  WooTable_Widget_BusinessHours()
	  ----------------------------------------
	  
	  * The constructor. Sets up the widget.
	----------------------------------------*/
	
	function WooTable_Widget_BusinessHours () {
		
		global $wootable;
		
		$this->plugin_prefix = $wootable->plugin_prefix;
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget-wootable-businesshours', 'description' => __('Displays the business hours listed in WooTable in a friendly format.', 'woothemes' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'widget_wootable_businesshours' );

		/* Create the widget. */
		$this->WP_Widget( 'widget_wootable_businesshours', __('WooTable - Business Hours', 'woothemes' ), $widget_ops, $control_ops );
		
	} // End WooTable_Widget_BusinessHours()

	/*----------------------------------------
	  widget()
	  ----------------------------------------
	  
	  * Displays the widget on the frontend.
	  
	  * Globals:
	  * - $wootable
	----------------------------------------*/
	
	function widget( $args, $instance ) {
		extract( $args );	
		
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		
		$is_twelvehour = $instance['is_twelvehour'];
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		
		/* Widget content. */
		
		$html = '';
		
		$_business_hours = WTFactory::get_business_hours_display( $this->plugin_prefix, $is_twelvehour );
		
		if ( $_business_hours ) { $html = $_business_hours; } // End IF Statement
		
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
		$instance['is_twelvehour'] = $new_instance['is_twelvehour'];

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
		$defaults = array( 'title' => __('Business Hours', 'woothemes' ) );
		$instance = wp_parse_args( (array) $instance, $defaults );
		
		$_checked = '';
		
		if ( $instance['is_twelvehour'] ) { $_checked = ' checked="checked"'; } // End IF Statement	
?>
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:','woothemes'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'is_twelvehour' ); ?>" class="alignleft"><?php _e('Display times in 12-hour format:','woothemes'); ?></label>
			<input id="<?php echo $this->get_field_id( 'is_twelvehour' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'is_twelvehour' ); ?>"<?php echo $_checked; ?> value="1" class="alignright" />
			<span class="clear"></span>
		</p>

<?php
	
	} // End form()
	
} // End Class
?>