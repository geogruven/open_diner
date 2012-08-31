<?php
/**
 * Plugin Name: Ajax_Notification
 * Plugin URI: http://www.georgeaguiar.com/AjaxNotification
 * Description: A tutorial on AJAX in WP
 * Version: 0.0.1
 * Author: George Aguiar
 * Author URI: http://georgeaguiar.com
 */
class Ajax_Notification {
 
    /*--------------------------------------------*
     * Constructor
     *--------------------------------------------*/
 
    /**
     * Initializes the plugin by setting localization, filters, and administration functions.
     */
    function __construct() {
 
        load_plugin_textdomain( 'ajax-notification', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
 
        register_activation_hook( __FILE__, array( &$this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
 
        add_action( 'admin_enqueue_scripts', array( &$this, 'register_admin_scripts' ) );
 
        // Display the admin notice only if it hasn't been hidden
        if( false == get_option( 'hide_ajax_notification' ) ) {
            add_action( 'admin_notices', array( &$this, 'display_admin_notice' ) );
        } // end if
 
        add_action( 'wp_ajax_hide_admin_notification', array( &$this, 'hide_admin_notification' ) );
 
    } // end constructor
 
    /*--------------------------------------------*
     * Core Functions
     *---------------------------------------------*/
 
    /**
     * Upon activation, add a new option used to track whether or not to display the notification.
     */
    public function activate() {
        add_option( 'hide_ajax_notification', false );
    } // end activate
 
    /**
     * Upon deactivation, removes the option that was created when the plugin was activated.
     */
    public function deactivate() {
        delete_option( 'hide_ajax_notification' );
    } // end deactivate
 
    /**
     * Registers and enqueues admin-specific minified JavaScript.
     */
    public function register_admin_scripts() {
 
        wp_register_script( 'ajax-notification-admin', plugins_url( 'ajax-notification/js/admin.min.js' ) );
        wp_enqueue_script( 'ajax-notification-admin' );
 
    } // end register_admin_scripts
 
    /**
     * Renders the administration notice. Also renders a hidden nonce used for security when processing the Ajax request.
     */
    public function display_admin_notice() {
 
        $html = '<div id="ajax-notification" class="updated">';
            $html .= '<p>';
                $html .= __( 'The Ajax Notification example plugin is active. This message will appear until you choose to <a href="javascript:;" id="dismiss-ajax-notification">dismiss it</a>.', 'ajax-notification' );
            $html .= '</p>';
            $html .= '<span id="ajax-notification-nonce" class="hidden">' . wp_create_nonce( 'ajax-notification-nonce' ) . '</span>';
        $html .= '</div><!-- /.updated -->';
 
        echo $html;
 
    } // end display_admin_notice
 
    /**
     * JavaScript callback used to hide the administration notice when the 'Dismiss' anchor is clicked on the front end.
     */
    public function hide_admin_notification() {
 
        // First, check the nonce to make sure it matches what we created when displaying the message.
        // If not, we won't do anything.
        if( wp_verify_nonce( $_REQUEST['nonce'], 'ajax-notification-nonce' ) ) {
 
            // If the update to the option is successful, send 1 back to the browser;
            // Otherwise, send 0.
            if( update_option( 'hide_ajax_notification', true ) ) {
                die( '1' );
            } else {
                die( '0' );
            } // end if/else
 
        } // end if
 
    } // end hide_admin_notification
 
} // end class
 
new Ajax_Notification();
    
?>
