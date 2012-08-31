<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: General utility functions for the WooTable WordPress plugin.
Date Created: 2010-08-11.
Author: Matty.
Since: 0.0.0.1


TABLE OF CONTENTS

- var $plugin_path
- var $plugin_url

- function WooTable_Utilities (constructor)
- function init ()
- function register_enqueues ()
- function enqueue_script ()
- function enqueue_style ()
- function admin_enqueue_script ()
- function admin_enqueue_style ()
- function dashboard_widget_setup ()
- function dashboard_upcoming_reservations_widget_content ()
- function dashboard_upcoming_reservations_widget_control ()
- function register_widgets ()

-----------------------------------------------------------------------------------*/

	class WooTable_Utilities {
		
		/*----------------------------------------
	 	  Class Variables
	 	  ----------------------------------------
	 	  
	 	  * Setup of variable placeholders, to be
	 	  * populated when the constructor runs.
	 	----------------------------------------*/
	
		var $plugin_path;
		var $plugin_url;
	
		/*----------------------------------------
	 	  WooTable_Utilities()
	 	  ----------------------------------------
	 	  
	 	  * Constructor function.
	 	  * Sets up the class and registers
	 	  * variable action hooks.
	 	  
	 	  * Params:
	 	  * - String $plugin_path
	 	  * - String $plugin_url
	 	----------------------------------------*/
	
		function WooTable_Utilities ( $plugin_path, $plugin_url ) {
		
			$this->init( $plugin_path, $plugin_url );
			
		} // End WooTable_Utilities()
		
		/*----------------------------------------
	 	  init()
	 	  ----------------------------------------
	 	  
	 	  * This guy runs the show.
	 	  * Rocket boosters... engage!
	 	----------------------------------------*/
		
		private function init ( $plugin_path, $plugin_url ) {
			
			$this->plugin_path = $plugin_path;
			$this->plugin_url = $plugin_url;
			
			$this->register_enqueues();
			
			// Register the custom dashboard widget
	 		add_action( 'wp_dashboard_setup', array( &$this, 'dashboard_widget_setup' ) );
	 		
	 		// Register the custom widget
	 		add_action ( 'widgets_init', array( &$this, 'register_widgets' ) );
			
		} // End init()
		
		/*----------------------------------------
	 	  register_enqueues()
	 	  ----------------------------------------
	 	  
	 	  * A helper function to register the
	 	  * various JavaScript enqueues.
	 	----------------------------------------*/
	 	
	 	private function register_enqueues () {
	 		
	 		// Enqueue scripts and styles for the administration area
	 		add_action( 'admin_print_scripts', array( &$this, 'admin_enqueue_script' ), null, 2 );
	 		add_action( 'admin_print_styles', array( &$this, 'admin_enqueue_style' ), null, 2 );
	 		
	 		// Enqueue scripts and styles for the frontend
	 		add_action( 'wp_print_scripts', array( &$this, 'enqueue_script' ), null, 2 );
	 		add_action( 'wp_print_styles', array( &$this, 'enqueue_style' ), null, 2 );
	 	
	 	} // End register_enqueues()
	 	
	 	/*----------------------------------------
	 	  enqueue_script()
	 	  ----------------------------------------
	 	  
	 	  * Enqueue front-end specific
	 	  * JavaScript files.
	 	----------------------------------------*/
	 	
	 	public function enqueue_script () {
	 			
	 			if ( !is_admin() ) {
	 				if ( is_page( get_option( 'wootable_page_booking' ) ) || is_page( get_option( 'wootable_page_manage' ) ) ) {
		 				// Enqueue the JavaScript functions file
		 				wp_enqueue_script('woo-table-functions', $this->plugin_url . '/assets/js/functions.js', array( 'jquery' ), '0.0.0.1', false);
	 				}
	 			} // End IF Statement
	 		
	 	} // End enqueue_script()
	 	
	 	/*----------------------------------------
	 	  enqueue_style()
	 	  ----------------------------------------
	 	  
	 	  * Enqueue front-end specific CSS files.
	 	----------------------------------------*/
	 	
	 	public function enqueue_style () {
	 		
	 	} // End enqueue_style()
	 	
	 	/*----------------------------------------
	 	  admin_enqueue_script()
	 	  ----------------------------------------
	 	  
	 	  * Enqueue admin specific
	 	  * JavaScript files.
	 	----------------------------------------*/
	 	
	 	public function admin_enqueue_script () {

				$current_page_path = basename( $_SERVER['REQUEST_URI'] );
				
				$current_page_file = substr( $current_page_path, 0, 8 );
				
				$allowed_pages = array( 'post.php', 'post-new' );
				
				if ( is_admin() && !isset( $_REQUEST['tag_ID'] ) && in_array( $current_page_file, $allowed_pages ) ) {

	 				wp_enqueue_script('woo-table-admin-functions', $this->plugin_url . '/assets/js/admin-functions.js', array( 'jquery' ), '0.0.0.1', false);
	 			
				} // End IF Statement
	 		
	 	} // End admin_enqueue_script()
	 	
	 	/*----------------------------------------
	 	  admin_enqueue_style()
	 	  ----------------------------------------
	 	  
	 	  * Enqueue admin specific CSS files.	 	  
	 	----------------------------------------*/
	 	
	 	public function admin_enqueue_style () {
	 		
	 			global $post_type;
	 			
	 			if ( isset( $_GET['post_type'] ) && ( $_GET['post_type'] == 'reservation' ) || ( $post_type == 'reservation' ) ) {
	 		
		 			// Enqueue CSS file for custom post type screen	
		 			wp_enqueue_style( 'woo-table-posttype-admin-icon', $this->plugin_url . '/assets/css/posttype-icon-reservation.css', array(), '0.0.0.1', 'screen' );
		 			wp_enqueue_style( 'woo-table-jqueryui-theme', $this->plugin_url . '/assets/css/smoothness/jquery-ui-1.8.4.custom.css', array(), '1.8.4', 'screen' );
		 			wp_enqueue_style( 'woo-admin-style', get_bloginfo('template_directory') . '/functions/admin-style.css', array(), '0.0.0.1', 'screen' );
	 			
	 			} // End IF Statement
	 		
	 				// Enqueue CSS file for custom post type screen	
		 			wp_enqueue_style( 'woo-table-admin', $this->plugin_url . '/assets/css/woo-table.css', array(), '0.0.0.1', 'screen' );
		 			
	 		
	 	} // End admin_enqueue_style()
	 	
	 	/*----------------------------------------
	 	  dashboard_widget_setup()
	 	  ----------------------------------------
	 	  
	 	  * Setup the `Upcoming Reservations`
	 	  * dashboard widget.
	 	----------------------------------------*/
	 	
	 	public function dashboard_widget_setup () {
	 	
			if ( !isset( $widget_options['dashboard_upcoming_reservations'] ) || !isset( $widget_options['dashboard_upcoming_reservations']['items'] ) ) {
				$update = true;
				$widget_options['dashboard_upcoming_reservations'] = array(
																'items' => 5,
																);
			}
			$upcoming_reservations_title = __( 'Upcoming Reservations', 'woothemes' );
			wp_add_dashboard_widget( 'dashboard_upcoming_reservations', $upcoming_reservations_title, array( &$this, 'dashboard_upcoming_reservations_widget_content' ), array( &$this, 'dashboard_upcoming_reservations_widget_control' ) );
			
			// Globalize the metaboxes array, this holds all the widgets for wp-admin
			global $wp_meta_boxes;
			// Get the regular dashboard widgets array 
			$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
			// Backup and delete dashboard widget from the end of the array
			$woo_table_widget_backup = array('dashboard_upcoming_reservations' => $normal_dashboard['dashboard_upcoming_reservations']);
			unset($normal_dashboard['dashboard_upcoming_reservations']);
			// Merge the two arrays together so reservations widget is at the beginning
			$sorted_dashboard = array_merge($woo_table_widget_backup, $normal_dashboard);
			// Save the sorted array back into the original metaboxes 
			$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	 		
	 	} // End dashboard_widget_setup()
	 	
	 	/*----------------------------------------
	 	  dashboard_upcoming_reservations_widget_content()
	 	  ----------------------------------------
	 	  
	 	  * Setup the content for the
	 	  * `Upcoming Reservations` dashboard widget.
	 	----------------------------------------*/
	 	
	 	public function dashboard_upcoming_reservations_widget_content () {
	 	
	 			global $wpdb;

				if ( current_user_can('edit_posts') )
					$allowed_states = array('0', '1');
				else
					$allowed_states = array('1');

				$reservations = array();
			
				$widgets = get_option( 'dashboard_widget_options' );
				if ( isset( $widgets['dashboard_upcoming_reservations'] ) && isset( $widgets['dashboard_upcoming_reservations']['items'] ) )
					$total_items = (int) $widgets['dashboard_upcoming_reservations']['items'];
				else
					$total_items = 5;
			
				$args = array(
								'post_type' => 'reservation', 
								'meta_key' => 'reservation_date', 
								'meta_compare' => '>=', 
								'meta_value' => date( 'Y-m-d' ), 
								'orderby' => 'meta_value', 
								'order' => 'ASC', 
								'numberposts' => $total_items
							);
			
				$raw_reservations = get_posts( $args );
			
				if ( $raw_reservations ) {
							
					foreach ( $raw_reservations as $r ) {
						
						$custom_values = get_post_custom( $r->ID );
						
						$reservation_date = $custom_values['reservation_date'][0];
						$date_bits = explode( '-', $reservation_date );
						$date_formatted = date("jS F Y", mktime(0, 0, 0, $date_bits[1], $date_bits[2], $date_bits[0] ));
						
						$entry = array( 
										'id' => $r->ID, 
										'title' => $r->post_title, 
										'reservation_date' => $date_formatted, 
										'reservation_time' => $custom_values['reservation_time'][0], 
										'number_of_people' => (float) $custom_values['number_of_people'][0], 
										'reservation_instructions' => $custom_values['reservation_instructions'][0], 
										'reservation_status' => $custom_values['reservation_status'][0]
									);
						
						$reservations[] = $entry;
						
					} // End FOREACH Loop
					
				} // End IF Statement
			
				if ( $reservations ) :
			?>
			
					<div id="the-reservations-list" class="list:reservation">
			<?php
					foreach ( $reservations as $r ) {
			?>					
						<div id="comment-<?php echo $r['id']; ?>" class="reservation">
						
							<div class="dashboard-reservation-wrap">
								<div  class="alignleft">
									<h4><?php echo sprintf( __( 'For %s on %s at %s.', 'woothemes' ), '<span class="reservation-title">' . $r['title'] . '</span>', '<span class="reservation-date">' . $r['reservation_date'] . '</span>', '<span class="reservation-time">' . $r['reservation_time'] . '</span>' ); ?></h4>
								
									<?php if ( $r['reservation_instructions'] ) { ?>
										<span class="instructions"><?php echo $r['reservation_instructions']; ?></span>
									<?php } /* End IF Statement */ ?>
									
								</div><!-- /.alignleft -->
								
								<span class="reservation_status reservation_status_<?php echo $r['reservation_status']; ?> alignright"><?php echo apply_filters( 'woo_custom_reservation_labels', ucfirst( $r['reservation_status'] ) ); ?></span><!--/.reservation_status-->
								<span class="clear"></span><!--/.clear-->
							</div>
						</div>
			<?php						
	 				} // End FOREACH Loop
			?>
			
					</div>
					
					<script type="text/javascript">
						
						jQuery(document).ready(function(){
						
							jQuery('#dashboard_upcoming_reservations div.reservation:odd').addClass('alt');
						
						});
					
					</script>
					
			<?php
					if ( current_user_can('edit_posts') ) { ?>
						<p class="textright"><a href="edit.php?post_type=reservation" class="button"><?php _e( 'View all','woothemes' ); ?></a></p>
			<?php	};
			
				else :
			?>
			
				<p class="no-reservations"><?php _e( 'No reservations have been made yet.','woothemes' ); ?></p>
			
			<?php
				endif;
	 		
	 	} // End dashboard_upcoming_reservations_widget_content()
	 	
	 	/*----------------------------------------
	 	  dashboard_upcoming_reservations_widget_control()
	 	  ----------------------------------------
	 	  
	 	  * Setup the controller for the
	 	  * `Upcoming Reservations` dashboard widget.
	 	----------------------------------------*/
	 	
	 	public function dashboard_upcoming_reservations_widget_control () {
	 	
			if ( !$widget_options = get_option( 'dashboard_widget_options' ) )
				$widget_options = array();
		
			if ( !isset($widget_options['dashboard_upcoming_reservations']) )
				$widget_options['dashboard_upcoming_reservations'] = array();
		
			if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['widget-upcoming-reservations']) ) {
				$number = (int) stripslashes($_POST['widget-upcoming-reservations']['items']);
				if ( $number < 1 || $number > 30 )
					$number = 5;
				$widget_options['dashboard_upcoming_reservations']['items'] = $number;
				update_option( 'dashboard_widget_options', $widget_options );
			}
		
			$number = isset( $widget_options['dashboard_upcoming_reservations']['items'] ) ? (int) $widget_options['dashboard_upcoming_reservations']['items'] : '';
		
			echo '<p><label for="reservations-number">' . __('Number of reservations to show:', 'woothemes') . '</label>';
			echo '<input id="reservations-number" name="widget-upcoming-reservations[items]" type="text" value="' . $number . '" size="3" /> <small>' . __( '(at most 30)' , 'woothemes') . '</small></p>';
	 		
	 	} // End dashboard_upcoming_reservations_widget_control()
		
		public function register_widgets () {
		
			register_widget ( 'WooTable_Widget_MakeReservation' );
			
		} // End register_widgets()
		
	} // End Class WooTable_Utilities
?>