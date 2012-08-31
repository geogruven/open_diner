<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: Main class for the WooTable WordPress plugin.
Date Created: 2010-08-11.
Author: Matty.
Since: 0.0.0.1


TABLE OF CONTENTS

- var $plugin_path
- var $plugin_url
- var $plugin_prefix

- var $token

- var $db_tablename
- var $db_metatype

- var $post_type
- var $taxonomy
- var $utilities
- var $manager
- var $frontend

- function WooTable (constructor)
- function init ()
- function load_post_type ()
- function load_taxonomy ()
- function load_utilities ()
- function load_manager ()
- function load_frontend ()
- function activate ()
- function create_metadata_table ()
- function register_table ()
- function setup_defaults ()
- function settings_screen ()
- function settings_register ()
- function settings_help_text ()
- function register_widgets ()

-----------------------------------------------------------------------------------*/
	 
	 class WooTable {
	 
	 	/*----------------------------------------
	 	  Class Variables
	 	  ----------------------------------------
	 	  
	 	  * Setup of variable placeholders, to be
	 	  * populated when the constructor runs.
	 	----------------------------------------*/
	 
	 	var $plugin_path;
	 	var $plugin_url;
	 	var $plugin_prefix;
	 	
	 	var $token;
	 	
	 	var $db_tablename;
	 	var $db_metatype;
	 	
	 	var $post_type;
	 	var $taxonomy;
	 	var $utilities;
	 	var $manager;
	 	var $frontend;
	 	
	 	var $default_emails;
	 	
	 	/*----------------------------------------
	 	  WooTable()
	 	  ----------------------------------------
	 	  
	 	  * Constructor function.
	 	  * Sets up the class and registers
	 	  * variable action hooks.
	 	  
	 	  * Params:
	 	  * - String $plugin_path
	 	----------------------------------------*/
	 
	 	function WooTable ( $plugin_path, $plugin_url ) {
	 	
	 		$this->plugin_path = $plugin_path;
	 		$this->plugin_url = $plugin_url;
	 		$this->plugin_prefix = 'wootable_';	 		
	 		
	 		// $this->token = 'settings_page_woo-table';
	 		$this->token = 'reservation_page_woo-table';
	 		
	 		$this->db_tablename = 'woo_tables_meta';
	 		$this->db_metatype = 'tables';
	 		
	 		$this->default_emails = array();
	 		
	 		add_action( 'init', array( &$this, 'init' ), 99 );
	 		add_action( 'admin_menu', array( &$this, 'settings_register' ), 99 );
	 		add_action( 'contextual_help', array( &$this, 'settings_help_text' ), 10, 3 );
	 		add_action( 'widgets_init', array( &$this, 'register_widgets' ) );
	 		
	 	} // End WooTable()
	 	
	 	/*----------------------------------------
	 	  init()
	 	  ----------------------------------------
	 	  
	 	  * This guy runs the show.
	 	  * Rocket boosters... engage!
	 	----------------------------------------*/
	 	
	 	public function init () {
			
			/*
			$this->load_class( 'WooTable_PostType_Reservation', 'post_type' );
			$this->load_class( 'WooTable_Taxonomy_Tables', 'taxonomy' );
			$this->load_class( 'WooTable_Utilities', 'utility' );
			$this->load_class( 'WTFactory', 'utility' );
			*/

			$this->load_post_type();
			$this->load_taxonomy();
	 		$this->load_utilities();
	 		$this->load_manager();
	 		$this->load_frontend();
	 		
	 		$this->register_table( $this->db_tablename, $this->db_metatype );
	 		
	 	} // End init()
	 	
	 	/*----------------------------------------
	 	  Utility Functions
	 	  ----------------------------------------
	 	  
	 	  * These functions are used within this
	 	  * class as helpers for other functions.
	 	----------------------------------------*/
	 	
	 	/*----------------------------------------
	 	  load_class()
	 	  ----------------------------------------
	 	  
	 	  * Instantiate and load a specified class.
	 	----------------------------------------*/
	 	
	 	public function load_class ( $class, $type ) {
	 	
	 		if ( class_exists( $type ) ) {
	 			
	 			$class_name = $class;
	 			
	 	 		$class = new $class_name( $this->plugin_path, $this->plugin_url );
	 	 		
	 	 		if ( $type == 'utility' ) {} else { $class->init(); } // End IF Statement
	 	 		
	 	 	} // End IF Statement
	 		
	 	} // End load_class()
	 	
	 	/*----------------------------------------
	 	  load_post_type()
	 	  ----------------------------------------
	 	  
	 	  * Instantiate and load the
	 	  * `WooTable_PostType_Reservation` class.
	 	----------------------------------------*/
	 	
	 	public function load_post_type () {
	 	
	 		if ( class_exists( 'WooTable_PostType_Reservation' ) ) {
	 			
	 	 		$this->post_type = new WooTable_PostType_Reservation( $this->plugin_path, $this->plugin_url, $this->plugin_prefix );
	 	 		$this->post_type->init();
	 	 		
	 	 	} // End IF Statement
	 		
	 	} // End load_post_type()
	 	
	 	/*----------------------------------------
	 	  load_taxonomy()
	 	  ----------------------------------------
	 	  
	 	  * Instantiate and load the
	 	  * `WooTable_Taxonomy_Tables` class.
	 	----------------------------------------*/
	 	
	 	public function load_taxonomy () {
	 	
	 		if ( class_exists( 'WooTable_Taxonomy_Tables' ) ) {
	 	 		
	 	 		$this->taxonomy = new WooTable_Taxonomy_Tables( $this->plugin_path, $this->plugin_url, $this->plugin_prefix );
	 	 		$this->taxonomy->init();
	 	 	
	 	 	} // End IF Statement
	 		
	 	} // End load_taxonomy()
	 	
	 	/*----------------------------------------
	 	  load_utilities()
	 	  ----------------------------------------
	 	  
	 	  * Instantiate and load the
	 	  * `WooTable_Utilities` class.
	 	----------------------------------------*/
	 	
	 	public function load_utilities () {
	 	
	 		if ( class_exists( 'WooTable_Utilities' ) ) {
	 			
	 	 		$this->utilities = new WooTable_Utilities( $this->plugin_path, $this->plugin_url, $this->plugin_prefix );
	 	 		
	 	 	} // End IF Statement
	 		
	 	} // End load_utilities()
	 	
	 	/*----------------------------------------
	 	  load_manager()
	 	  ----------------------------------------
	 	  
	 	  * Instantiate and load the
	 	  * `WooTable_Manager` class.
	 	----------------------------------------*/
	 	
	 	public function load_manager () {
	 	
	 		if ( class_exists( 'WooTable_Manager' ) ) {
	 			
	 	 		$this->manager = new WooTable_Manager( $this->plugin_path, $this->plugin_url, $this->plugin_prefix );
	 	 		
	 	 	} // End IF Statement
	 		
	 	} // End load_manager()
	 	
	 	/*----------------------------------------
	 	  load_frontend()
	 	  ----------------------------------------
	 	  
	 	  * Instantiate and load the
	 	  * `WooTable_Manager` class.
	 	----------------------------------------*/
	 	
	 	public function load_frontend () {
	 	
	 		if ( class_exists( 'WooTable_FrontEnd' ) ) {
	 			
	 	 		$this->frontend = new WooTable_FrontEnd( $this->plugin_path, $this->plugin_url, $this->plugin_prefix );
	 	 		
	 	 	} // End IF Statement
	 		
	 	} // End load_frontend()
	 	
	 	/*----------------------------------------
	 	  activate()
	 	  ----------------------------------------
	 	  
	 	  * Perform actions when the plugin is
	 	  * activated. In this case, we call the
	 	  * create_metadata_table () function.
	 	----------------------------------------*/
	 	
	 	public function activate () {
	 	
	 		global $wpdb;
	 	
	 		$this->create_metadata_table( $this->db_tablename, $this->db_metatype );
	 		$this->setup_defaults();
	 		update_option('woo_table_version', '1.0');
	 		
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
	 	
	 	public function create_metadata_table ( $table_name, $type ) {
			
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
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			
		} // End create_metadata_table()
		
		/*----------------------------------------
	 	  register_table()
	 	  ----------------------------------------
	 	  
	 	  * Register our newly created custom
	 	  * database table, such that we can
	 	  * access it throughout the system.
	 	----------------------------------------*/
		
		public function register_table () {
		
			global $wpdb;
			
			$variable_name = 'woo_' . $this->db_metatype . 'meta';
			$wpdb->$variable_name = $wpdb->prefix . $this->db_tablename;
			
			$wpdb->tables[] = $this->db_tablename;
			
		} // End register_table()
		
		/*----------------------------------------
	 	  setup_defaults()
	 	  ----------------------------------------
	 	  
	 	  * Sets up the default settings for the
	 	  * plugin. This includes the creation
	 	  * of the pages used by the plugin.
	 	----------------------------------------*/
		
		public function setup_defaults () {
		
			$is_setup = false;
		
			// Setup the default e-mail messages in variables for use in the array below.
			
			// Reservation confirmation e-mail.
			
			$_message_customer_confirm = '';
	 		$_message_customer_confirm .= 'Dear [contact_name],' . "\n\n";
	 		$_message_customer_confirm .= 'Thank you for your reservation at [restaurant_name] for [number_of_people] at [reservation_time] on [reservation_date].' . "\n";
	 		$_message_customer_confirm .= 'The last thing we require from you is to please confirm your reservation.' . "\n";
	 		$_message_customer_confirm .= 'To confirm and manage your reservations, please visit our reservation management page at the URL below.' . "\n";
	 		$_message_customer_confirm .= 'We look forward to your patronage.' . "\n\n";
	 		$_message_customer_confirm .= 'Sincerely,' . "\n";
	 		$_message_customer_confirm .= 'The staff at [restaurant_name].' . "\n";
			
			$this->default_emails['pleaseconfirm'] = $_message_customer_confirm;
			
			// Reservation thank you e-mail.
			
			$_message_customer_thankyou = '';
	 		$_message_customer_thankyou .= 'Dear [contact_name],' . "\n\n";
	 		$_message_customer_thankyou .= 'Thank you for your reservation at [restaurant_name] for [number_of_people] at [reservation_time] on [reservation_date].' . "\n";
	 		$_message_customer_thankyou .= 'To manage your reservations, please visit our reservation management page at the URL below.' . "\n";
	 		$_message_customer_thankyou .= 'We look forward to your patronage.' . "\n\n";
	 		$_message_customer_thankyou .= 'Sincerely,' . "\n";
	 		$_message_customer_thankyou .= 'The staff at [restaurant_name].' . "\n";
	 		
	 		$this->default_emails['thankyou'] = $_message_customer_thankyou;
	 		
	 		// Custom "Status Has Changed" e-mail.
	 		
	 		$_message_customer_statuschange = '';
	 		$_message_customer_statuschange .= 'Dear [contact_name],' . "\n\n";
	 		$_message_customer_statuschange .= 'This is a courtesy e-mail to inform you that the status of your reservation at [restaurant_name] for [number_of_people] at [reservation_time] on [reservation_date] has been updated.' . "\n";
	 		$_message_customer_statuschange .= 'The new reservation status is "[reservation_status]".' . "\n";
	 		$_message_customer_statuschange .= 'Sincerely,' . "\n";
	 		$_message_customer_statuschange .= 'The staff at [restaurant_name].' . "\n";
	 		
	 		$this->default_emails['statuschange'] = $_message_customer_statuschange;
	 		
	 		// Custom "Special Request" e-mail.
	 		
	 		$_message_customer_specialrequest .= 'Dear [contact_name],' . "\n";
 			$_message_customer_specialrequest .= 'You recently requested a reservation at [restaurant_name] for [number_of_people] on [reservation_date] at [reservation_time].' . "\n";
 			$_message_customer_specialrequest .= 'The following notes were left for the manager:' . "\n\n";
 			$_message_customer_specialrequest .= '[reservation_instructions]' . "\n\n";
 			$_message_customer_specialrequest .= 'To discuss this reservation further, a manager at [restaurant_name] will follow up with you either via telephone (you left [contact_tel] as your contact number) or e-mail (on [contact_email]).' . "\n";
 			$_message_customer_specialrequest .= 'Sincerely,' . "\n";
 			$_message_customer_specialrequest .= '[restaurant_name] Reservations.' . "\n";
	 		
	 		$this->default_emails['specialrequest'] = $_message_customer_specialrequest;
	 		
	 		// Administrator "Reservation Made" e-mail.
	 		
	 		$_message_admin_reservationmade = '';
	 		
	 		$_message_admin_reservationmade .= 'Hey there!' . "\n\n";
	 		$_message_admin_reservationmade .= 'A reservation has been made at [restaurant_name] for [number_of_people], to be seated at [reservation_time] on [reservation_date].' . "\n";
	 		$_message_admin_reservationmade .= 'The reservee, [contact_name], can be contacted at:' . "\n";
	 		$_message_admin_reservationmade .= 'Telephone: [contact_tel]' . "\n";
	 		$_message_admin_reservationmade .= 'E-mail: [contact_email]' . "\n";
	 		$_message_admin_reservationmade .= "\n" . '[reservation_instructions]' . "\n\n";
	 		$_message_admin_reservationmade .= 'Please contact [contact_name] at your earliest convenience to confirm their reservation.' . "\n\n";
	 		$_message_admin_reservationmade .= 'Sincerely,' . "\n";
	 		$_message_admin_reservationmade .= '[restaurant_name] Reservations.' . "\n";
	 		
	 		$this->default_emails['admin_reservationmade'] = $_message_admin_reservationmade;
	 		
	 		// Administrator "Status Has Changed" e-mail.
	 		
	 		$_message_admin_statuschange = '';
	 		$_message_admin_statuschange .= 'Hey there!,' . "\n\n";
	 		$_message_admin_statuschange .= 'This is a courtesy e-mail to inform you that [contact_name] has changed the status of their reservation at [restaurant_name] for [number_of_people] at [reservation_time] on [reservation_date].' . "\n";
	 		$_message_admin_statuschange .= 'The new reservation status is "[reservation_status]".' . "\n";
	 		$_message_admin_statuschange .= 'Sincerely,' . "\n";
	 		$_message_admin_statuschange .= 'The staff at [restaurant_name].' . "\n";
	 		
	 		$this->default_emails['admin_statuschange'] = $_message_admin_statuschange;
	 		
	 		// Administrator "Special Request" e-mail.
	 		
	 		$_message_admin_specialrequest = '';
 			
 			$_message_admin_specialrequest .= 'Hey There!' . "\n";
 			$_message_admin_specialrequest .= '[contact_name] requested a reservation for [number_of_people] on [reservation_date] at [reservation_time].' . "\n";
 			$_message_admin_specialrequest .= 'The following notes were made:' . "\n\n";
 			$_message_admin_specialrequest .= '[reservation_instructions]' . "\n\n";
 			$_message_admin_specialrequest .= 'To discuss this reservation further, please follow up with [contact_name] either via telephone on [contact_tel] or e-mail on [contact_email].' . "\n";
 			$_message_admin_specialrequest .= 'Sincerely,' . "\n";
 			$_message_admin_specialrequest .= '[restaurant_name] Reservations.' . "\n";
 			
 			$this->default_emails['admin_specialrequest'] = $_message_admin_specialrequest;
		
			// First, lets add the standard fields.
			
			// Work out a better place to store this array,
			// and use it on the settings screen as well.
			
			$fields = array(
					array(
						'field' => 'send_confirmation_to_restaurant', 
						'message' => __( 'Please specify if you would like all reservation confirmation emails to be CCed to your email address.', 'woothemes' ), 
						'type' => 'text', 
						'default' => '1', 
						'required' => 0
						), 
					array(
						'field' => 'max_number_of_people', 
						'message' => __( 'Please specify the maximum number of people per booking to attempt table combinations.', 'woothemes' ), 
						'type' => 'text', 
						'default' => '10', 
						'required' => 0
					),
					array(
						'field' => 'reservation_interval', 
						'message' => __( 'Please select the time interval between reservations at a single table.', 'woothemes' ), 
						'type' => 'text', 
						'default' => '1', 
						'required' => 1
						),
					array(
						'field' => 'page_booking', 
						'message' => __( 'Please select the page that is to display the booking form.', 'woothemes' ), 
						'type' => 'page', 
						'default' => 'Make a reservation', 
						'content' => 'Please place your booking by filling in the form below.', 
						'required' => 1
						),
					array(
						'field' => 'page_manage', 
						'message' => __( 'Please select the page that is to display the upcoming bookings.', 'woothemes' ), 
						'type' => 'page', 
						'default' => 'Manage your reservations', 
						'content' => 'Your upcoming reservations are listed below. Here you are able to cancel or confirm your registration with us.', 
						'required' => 1
						),
					array(
						'field' => 'confirmation_box_message', 
						'message' => __( 'Please enter the text you would like to display in the reservation confirmation popup window.', 'woothemes' ), 
						'type' => 'text', 
						'default' => 'Would you like to proceed with the selected reservation details?', 
						'required' => 1
						),
					array(
						'field' => 'time_format', 
						'message' => __( 'Please select the format in which you would like to display booking times on your website\'s frontend', 'woothemes' ), 
						'type' => 'text', 
						'default' => '24', 
						'required' => 1
						),
					array(
						'field' => 'date_format', 
						'message' => __( 'Please select the format in which you would like to display booking dates on your website\'s frontend', 'woothemes' ), 
						'type' => 'text', 
						'default' => 'jS F Y', 
						'required' => 1
						),
					array(
						'field' => 'default_status', 
						'message' => __( 'Please select the default status for reservations made from your website\'s frontend.', 'woothemes' ), 
						'type' => 'text', 
						'default' => 'unconfirmed', 
						'required' => 1
						),
					array(
						'field' => 'email_pleaseconfirm', 
						'message' => __( 'Please enter the text you would like to display in the "Please Confirm" e-mail message.', 'woothemes' ), 
						'type' => 'text', 
						'default' => $_message_customer_confirm, 
						'required' => 1
						),
					array(
						'field' => 'email_thankyou', 
						'message' => __( 'Please enter the text you would like to display in the "Thank You" e-mail message.', 'woothemes' ), 
						'type' => 'text', 
						'default' => $_message_customer_thankyou, 
						'required' => 1
						),
					array(
						'field' => 'email_statuschange', 
						'message' => __( 'Please enter the text you would like to display in the "Status Change" e-mail message to the user.', 'woothemes' ), 
						'type' => 'text', 
						'default' => $_message_customer_statuschange, 
						'required' => 1
						),
					array(
						'field' => 'email_specialrequest', 
						'message' => __( 'Please enter the text you would like to display in the "Special Request" e-mail message to the user.', 'woothemes' ), 
						'type' => 'text', 
						'default' => $_message_customer_specialrequest, 
						'required' => 1
						),
					array(
						'field' => 'adminemail_reservationmade', 
						'message' => __( 'Please enter the text you would like to display in the "Reservation Made" e-mail message to the administrator.', 'woothemes' ), 
						'type' => 'text', 
						'default' => $_message_admin_reservationmade, 
						'required' => 1
						),
					array(
						'field' => 'adminemail_statuschange', 
						'message' => __( 'Please enter the text you would like to display in the "Status Change" e-mail message to the administrator.', 'woothemes' ), 
						'type' => 'text', 
						'default' => $_message_admin_statuschange, 
						'required' => 1
						),
					array(
						'field' => 'adminemail_specialrequest', 
						'message' => __( 'Please enter the text you would like to display in the "Special Request" e-mail message to the administrator.', 'woothemes' ), 
						'type' => 'text', 
						'default' => $_message_admin_specialrequest, 
						'required' => 1
						),
					array(
						'field' => 'reserve_button_text', 
						'message' => __( 'Please enter the text you would like to display on the "Reserve Table" button.', 'woothemes' ), 
						'type' => 'text', 
						'default' => 'Reserve Table', 
						'required' => 1
						),
					array(
						'field' => 'view_button_text', 
						'message' => __( 'Please enter the text you would like to display on the "View Reservations" button.', 'woothemes' ), 
						'type' => 'text', 
						'default' => 'View Reservations', 
						'required' => 1
						)
					);
			
			// Right. That's the basics. Now lets add the business hours defaults.
			$business_hours = array (
								    'sun' => array (
								            'openingtime' => '09:00', 
								            'closingtime' => '17:00', 
								            'closed' => '1'
								        ), 
								
								    'mon' => array (
								            'openingtime' => '09:00', 
								            'closingtime' => '17:00', 
								            'closed' => '0'
								        ),
								
								    'tues' => array (
								            'openingtime' => '09:00', 
								            'closingtime' => '17:00', 
								            'closed' => '0'
								        ),
								
								    'wed' => array (
								            'openingtime' => '09:00', 
								            'closingtime' => '17:00', 
								            'closed' => '0'
								        ),
								
								    'thurs' => array (
								            'openingtime' => '09:00', 
								            'closingtime' => '17:00', 
								            'closed' => '0'
								        ),
								
								    'fri' => array (
								            'openingtime' => '09:00', 
								            'closingtime' => '17:00', 
								            'closed' => '0'
								        ),
								
								    'sat' => array (
								            'openingtime' => '09:00', 
								            'closingtime' => '17:00', 
								            'closed' => '0'
								        )
								);
								
			// Set business hours defaults.
			// update_option( $this->plugin_prefix . 'business_hours', $business_hours );			
			
			$fields[] = array(
						'field' => 'business_hours', 
						'message' => __( 'Please specify the business hours, per day, for your restaurant.', 'woothemes' ), 
						'type' => 'array', 
						'default' => $business_hours, 
						'required' => 1
						);
			
			foreach ( $fields as $f ) {
			
				// If $f's 'type' is a 'page', add the page to the system
				
				if ( $f['type'] == 'page' ) {
					
					$page_data = array( 'post_title' => $f['default'], 'post_status' => 'publish', 'post_type' => 'page', 'post_content' => $f['content'] );
					
					// Check if a page already exists that we can use.
					$existing_page = get_page_by_title( $f['default'] );
					
					if ( $existing_page ) {
					
						$page_id = $existing_page->ID;
					
					} else {
						
						$page_id = wp_insert_post( $page_data );
						
					} // End IF Statement
					
					if ( $page_id ) {
						
						update_option( $this->plugin_prefix . $f['field'], $page_id );
						
					} // End IF Statement
					
				} else {
					
					// If the value is already present in the database, don't update it.
					
					if ( ! get_option( $this->plugin_prefix . $f['field'] ) ) {
					
						update_option( $this->plugin_prefix . $f['field'], $f['default'] );
						
					} // End IF Statement
					
				} // End IF Statement
				
			} // End FOREACH Loop
			
			$is_setup = true;
			
			return $is_setup;
			
		} // End setup_defaults()
		
		/*----------------------------------------
	 	  settings_screen()
	 	  ----------------------------------------
	 	  
	 	  * Require the plugin setting screen.
	 	----------------------------------------*/
		
		function settings_screen () {
			
			// Separate the admin page XHTML to keep things neat and in the appropriate location.
			require_once( $this->plugin_path . '/settings/screen.php' );
			
		} // End settings_screen()
		
		/*----------------------------------------
	 	  settings_register()
	 	  ----------------------------------------
	 	  
	 	  * Register the plugin settings screen
	 	  * under the `Settings` admin menu.
	 	----------------------------------------*/
		
		function settings_register () {
		
			if (function_exists('add_submenu_page')) {
				
				// add_submenu_page('options-general.php', __( 'WooTable', 'woothemes' ), __( 'WooTable', 'woothemes' ), 'manage_options', $this->plugin_path, array( &$this, 'settings_screen' ) );
				// $this->plugin_path // Instead of 'woo-table'
				
				add_submenu_page('edit.php?post_type=reservation', __( 'WooTable', 'woothemes' ), __( 'WooTable Settings', 'woothemes' ), 'manage_options', 'woo-table', array( &$this, 'settings_screen' ) );
				
			} // End IF Statement
			
		} // End settings_register()
		
		/*----------------------------------------
	 	  settings_help_text()
	 	  ----------------------------------------
	 	  
	 	  * The contextual help text to be
	 	  * displayed on the settings screen.
	 	  
	 	  * Params:
	 	  * - String $contextual_help
	 	  * - String $screen_id
	 	  * - Object $screen
	 	----------------------------------------*/
		
		function settings_help_text ( $contextual_help, $screen_id, $screen ) { 
		  
		  // $contextual_help .= var_dump($screen); // use this to help determine $screen->id
		  
		  if ( $this->token == $screen->id ) {
		  
		    $contextual_help =
		      '<p>' . __('Welcome to the WooTable settings screen!', 'woothemes') . '</p>' .
		      '<p>' . __('Here are a few notes on using this screen.', 'woothemes') . '</p>' .
		      '<ul>' .
		      '<li>' . __('<strong>Interval between reservations at a single table:</strong> How long each reservation at a single table is to be.', 'woothemes') . '</li>' .
		      '<li>' . __('<strong>Bookings Page:</strong> The page that will display the reservation booking form.', 'woothemes') . '</li>' .
		      '<li>' . __('<strong>Bookings Management Page:</strong> The page that will display a user\'s upcoming reservations for them to manage.', 'woothemes') . '</li>' .
		      '<li>' . __('<strong>Reservation confirmation popup message:</strong> The message that is displayed in the confirmation dialog box when a user places a reservation.', 'woothemes') . '</li>' .
		      '<li>' . __('<strong>Opening &amp; closing times for each day:</strong> Let WooTable (and your customers) know when you\'re open on each day of the week.', 'woothemes') . '</li>' .
		      '</ul>' .
		      '<p><strong>' . __('For more information:', 'woothemes') . '</strong></p>' .
		      '<p>' . sprintf( __('<a href="%s" target="_blank">WooTable Documentation</a>', 'woothemes'), '#' ) . '</p>' .
		      '<p>' . sprintf( __('<a href="%s" target="_blank">WooThemes Support Forums</a>', 'woothemes'), 'http://forum.woothemes.com/' ) . '</p>';
		 
		 } // End IF Statement
		  
		  return $contextual_help;
		  
		} // End settings_help_text()
		
		/*----------------------------------------
	 	  register_widgets()
	 	  ----------------------------------------
	 	  
	 	  * Registers our custom widgets.
	 	----------------------------------------*/
		
		function register_widgets () {
		
			register_widget( 'WooTable_Widget_MakeReservation' );
			register_widget( 'WooTable_Widget_BusinessHours' );
			
		} // End register_widgets()
	 	
	 } // End Class WooTable
?>