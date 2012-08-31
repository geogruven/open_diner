<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Content Builder - Custom Fields for Terms

- Content Builder - Menu and Scripts
-- woothemes_content_builder_menu()
-- woothemes_content_builder_scripts()
-- woothemes_content_builder_styles()

- Content Builder - Page Output
-- woothemes_content_builder_page()
-- woothemes_content_builder_content_list()
-- woothemes_content_builder_content_add()

- Content Builder - Output Actions
-- woothemes_content_builder_delete()
-- woothemes_content_builder_save()

- Content Builder - Register Functions
-- woothemes_content_builder_cpt_register()
-- woothemes_content_builder_ctx_register()

- Content Builder - Options Function
-- woothemes_content_builder_options()

- Content Builder - Helper Functions
-- woothemes_content_builder_checkbool()
-- woothemes_content_builder_array_remove_empty()
-- woothemes_content_builder_item_exists()
-- woothemes_content_builder_ajax_javascript()

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Content Builder - Custom Fields for Terms */
/*-----------------------------------------------------------------------------------*/

	require_once( 'taxonomyfields.class.php' );
	
	$woo_taxfields = new Woo_ContentBuilder_TaxonomyFields();

/*-----------------------------------------------------------------------------------*/
/* Content Builder - Menu and Scripts */
/*-----------------------------------------------------------------------------------*/

function woothemes_content_builder_menu() {

	// Woothemes Content Builder Menu	
	$woocontentbuilder = add_submenu_page( 'woothemes', 'Content Builder', 'Content Builder', 'manage_options', 'woothemes_content_builder', 'woothemes_content_builder_page' );
	
	add_action("admin_print_scripts-$woocontentbuilder", 'woo_load_only');
	add_action("admin_print_scripts-$woocontentbuilder", 'woothemes_content_builder_scripts' );
	add_action('admin_head', 'woothemes_content_builder_ajax_javascript');
	add_action("admin_print_styles-$woocontentbuilder", 'woothemes_content_builder_styles' );
	
}

function woothemes_content_builder_scripts() {

	// STYLES AND JAVASCRIPT
	wp_enqueue_script( 'jquery-validate', get_template_directory_uri() . '/includes/content-builder/js/jquery-validate/jquery.validate.min.js', array( 'jquery' ), '1.7', false );
	wp_enqueue_script( 'woo-nav-autocomplete' );
	
}

function woothemes_content_builder_styles() {

	wp_enqueue_style( 'woo-shortcodes', get_template_directory_uri() . '/functions/css/shortcodes.css', 'screen' );
	wp_enqueue_style( 'woo-contentbuilder-styles', get_template_directory_uri() . '/includes/content-builder/css/content-builder.css', 'screen', '1.4.4' );
	
	wp_register_style( 'woo-admin-interface', get_template_directory_uri() . '/functions/admin-style.css' );
		
	wp_enqueue_style( 'woo-admin-interface' );
	
} // End woothemes_content_builder_styles()


/*-----------------------------------------------------------------------------------*/
/* Content Builder - Page Output */
/*-----------------------------------------------------------------------------------*/

function woothemes_content_builder_page(){
	
	global $cb_message;
    $themename =  get_option('woo_themename');      
    $manualurl =  get_option('woo_manual'); 
	$shortname =  'woo_content_builder'; 
	
    // Framework Version in Backend Head
    $woo_framework_version = get_option('woo_framework_version');
    
    // Version in Backend Head
    $theme_data = get_theme_data(TEMPLATEPATH . '/style.css');
    $local_version = $theme_data['Version'];
    
    // GET themes update RSS feed and do magic
	include_once(ABSPATH . WPINC . '/feed.php');

	$pos = strpos($manualurl, 'documentation');
	$theme_slug = str_replace("/", "", substr($manualurl, ($pos + 13))); //13 for the word documentation
	
    // add filter to make the rss read cache clear every 4 hours
    add_filter( 'wp_feed_cache_transient_lifetime', create_function( '$a', 'return 14400;' ) );
	
	// GET all custom post types
	// $wp_custom_post_types_args = array(	'_builtin' => true	);
	$wp_custom_post_types_args = array();
	$wp_custom_post_types = array();  
	$wp_custom_post_types = get_post_types($wp_custom_post_types_args,'objects'); 
	//remove woothemes framework post type from the array
	if ( $wp_custom_post_types['wooframework'] != '' ) { unset($wp_custom_post_types['wooframework']);	}
	$woo_wp_custom_post_types_formatted = array();
	foreach ($wp_custom_post_types as $woo_wp_content_type) {
	    $woo_wp_custom_post_types_formatted[$woo_wp_content_type->name] = $woo_wp_content_type->label; }
	
	// Unset array items that we dont want	
	if ( $woo_wp_custom_post_types_formatted['nav_menu_item'] != '' ) { unset($woo_wp_custom_post_types_formatted['nav_menu_item']);	}
	if ( $woo_wp_custom_post_types_formatted['attachment'] != '' ) { unset($woo_wp_custom_post_types_formatted['attachment']);	}
	if ( $woo_wp_custom_post_types_formatted['revision'] != '' ) { unset($woo_wp_custom_post_types_formatted['revision']);	}
	if ( $woo_wp_custom_post_types_formatted['page'] != '' ) { unset($woo_wp_custom_post_types_formatted['page']);	}
	
	// GET all custom taxonomies
	// $wp_custom_taxonomy_args = array(	'_builtin' => false );
	$wp_custom_taxonomy_args = array();
	$woo_wp_custom_taxonomies = array();  
	$woo_wp_custom_taxonomies = get_taxonomies($wp_custom_taxonomy_args,'objects');   
	$woo_wp_custom_taxonomies_formatted = array();  
	foreach ($woo_wp_custom_taxonomies as $woo_wp_custom_taxonomy) {
	    $woo_wp_custom_taxonomies_formatted[$woo_wp_custom_taxonomy->name] = $woo_wp_custom_taxonomy->label; }
	
	// Unset array items that we dont want	
	if ( $woo_wp_custom_taxonomies_formatted['nav_menu'] != '' ) { unset($woo_wp_custom_taxonomies_formatted['nav_menu']);	}
	if ( $woo_wp_custom_taxonomies_formatted['link_category'] != '' ) { unset($woo_wp_custom_taxonomies_formatted['link_category']);	}
	
	// GET all custom taxonomies
	$woo_wp_custom_fields = array();  
	$woo_wp_custom_fields = get_option('woo_custom_template');  
	if ( $woo_wp_custom_fields == '' ) { $woo_wp_custom_fields = array(); } // Fix if empty
	$woo_wp_custom_fields_formatted = array();  
	
	// Get all custom fields for taxonomies.
	$woo_wp_custom_fields_ctx = array();
	$woo_wp_custom_fields_ctx = get_option( 'woo_content_builder_cmb_ctx' );
	
	if ( ! is_array( $woo_wp_custom_fields_ctx ) ) { $woo_wp_custom_fields_ctx = array(); } // End IF Statement
	
	// Post features
	$post_features = array(
                "title" => __("Title", "woothemes"),
                "editor" => __("Editor", "woothemes"),
                "excerpt" => __("Excerpts", "woothemes"),
                "trackbacks" => __("Trackbacks", "woothemes"),
                "custom-fields" => __("Custom Fields", "woothemes"),
                "comments" => __("Comments", "woothemes"),
                "revisions" => __("Revisions", "woothemes"),
                "thumbnail" => __("Post Thumbnails", "woothemes"),
                "author" => __("Author", "woothemes"),
                "page-attributes" => __("Page Attributes", "woothemes")
            );
		
	$content_types_options = array();
	$content_setting = '';
	$action_setting = '';
	if ( isset( $_REQUEST['action'] ) ) {
		$action_setting = $_REQUEST['action']; 
		if ($_REQUEST['action'] == 'delete') {
			$content_setting = '';
		} else {
			if ( isset( $_REQUEST['content'] ) ) {
				$content_setting = $_REQUEST['content'];	
			} // End If Statement
		} // End If Statement
		
	} // End If Statement
	
	$option_args = array(	'shortname' => $shortname, 
					'woo_wp_custom_taxonomies_formatted' => $woo_wp_custom_taxonomies_formatted, 
					'woo_wp_custom_post_types_formatted' => $woo_wp_custom_post_types_formatted, 
					'post_features' => $post_features,
					'action' => $action_setting, 
					'content_type' => $content_setting
					);
	
	$content_types_options = woothemes_content_builder_options($option_args);		
	
	?>
	<?php echo $cb_message; ?> 
	
	<style type="text/css">
		label.updated, label.error { display: block !important; }
		
		/* Hide all valid labels generated by jQuery.validate(). */
		
		label.valid { display: none !important; }
	</style>
	
	<script type="text/javascript">
				
		jQuery(document).ready(function() {
		
			// Position the "woo-sc-box" element in the centre of the window.
			var windowWidth = 500;
			var windowHeight = 400;
			
			// Total document dimensions
			var pageWidth = jQuery(document).width();
			var pageHeight = jQuery(document).height();
			
			// Viewport dimensions
			var viewportWidth = jQuery(window).width();
			var viewportHeight = jQuery(window).height();
			
			// Fix for Opera 9.5
			var viewportWidth = window.innerWidth ? window.innerWidth : jQuery(window).width();
			var viewportHeight = window.innerHeight ? window.innerHeight : jQuery(window).height();
			
			var windowTop = ( pageHeight - windowHeight ) / 2;
			var windowLeft = ( pageWidth - windowWidth ) / 2;
			
			/*jQuery('.woo-sc-box').css( 'width', '370px' ).css( 'position', 'absolute' ).css( 'z-index', '5' ).css( 'left', '380px' ).css( 'top', windowTop - 160 );*/ // Positioning next to the title.
			jQuery('.woo-sc-box').css( 'width', '535px' ).css( 'position', 'absolute' ).css( 'z-index', '5' ).css( 'left', '211px' ).css( 'top', windowTop - 105 ); // Positioning over the table header.
			
			// Fade out and remove the "woo-sc-box" element after a few seconds.
			jQuery('.woo-sc-box').delay(3000).fadeOut( 'slow', function () {
			
				jQuery(this).delay(200).remove();
			
			});
		
			// Attempt to "remember" the tab the user had selected, based on a query string value.
			// -- START --
			
			var tabTrigger = '';
			var tabToLoad = '';
			var cbAction = '';
			
			<?php if ( isset($_GET['action']) ) { ?>
			cbAction = '<?php echo strtolower( trim( strip_tags( $_GET['action'] ) ) ); ?>';
			<?php } ?>
			<?php if ( isset($_GET['tab']) ) { ?>
			tabToLoad = '<?php echo strtolower( trim( strip_tags( $_GET['tab'] ) ) ); ?>';
			<?php } ?>
			if ( tabToLoad == '' && ( cbAction == '' || cbAction == 'delete' ) ) {
				<?php if ( isset($_GET['content']) ) { ?>
				tabToLoad = '<?php echo strtolower( trim( strip_tags( $_GET['content'] ) ) ); ?>';
				<?php } ?>
			} // End IF Statement
			
			switch ( tabToLoad ) {
			
				case 'cpt':
				
				tabTrigger = 'woo-option-customposttypes';
				
				break;
				
				case 'ctx':
				
				tabTrigger = 'woo-option-customtaxonomies';
				
				break;
				
				case 'cmb':
				
				tabTrigger = 'woo-option-customfields';
				
				break;
			
			} // End SWITCH Statement
			
			if ( tabToLoad == '' || tabTrigger == '' ) {} else {
			
				// Set the class of the desired menu item to "current". Remove current from it's current item.
				jQuery( '#woo-nav .current' ).addClass('old').removeClass('current');
				jQuery( '#woo-nav ul li a[href*="'+tabTrigger+'"]').parents('li').addClass('current');
				
				// Hide all ".group" DIV tags and show only the one we want.
				jQuery( '#content .group' ).hide();
				jQuery( '#content #'+tabTrigger ).fadeIn();
			
			} // End IF Statement
			
			// -- END --
			
			// Determine which "Content Type" to display for the Custom Fields screen,
			// based on the `woo_content_builder_cmb_contenttype` field's value.
			
			if ( jQuery('input[name="woo_content_builder_cmb_contenttype"]').length ) {
			
				// First, we hide both content types.
				jQuery( '#woo-option-contenttypes .section-multicheck2' ).hide();
			
				var cmbContentType = jQuery('input[name="woo_content_builder_cmb_contenttype"]').val();
				
				var cmbContentTypeToUse = 'cpt';
				
				if ( cmbContentType == 'ctx' ) {
				
					cmbContentTypeToUse = 'ctx';
					
				} // End IF Statement
				
				// Toggle the content type.
				woo_toggleCmbContentType( cmbContentTypeToUse );
				
				jQuery('input[name="woo_content_builder_cmb_contenttype"], a[href="#woo-option-contenttypes"]').click( function () {
				
					var cmbContentType = jQuery('input[name="woo_content_builder_cmb_contenttype"]:checked').val();
				
					var cmbContentTypeToUse = 'cpt';
					
					if ( cmbContentType == 'ctx' ) {
					
						cmbContentTypeToUse = 'ctx';
						
					} // End IF Statement
					
					// Toggle the content type.
					woo_toggleCmbContentType( cmbContentTypeToUse );
				
				});
			
			} // End IF Statement
			
			/* woo_toggleCmbContentType()
			------------------------------------------------------------*/
			
			function woo_toggleCmbContentType ( contentTypeToUse ) {
			
				// Toggle the content type boxes in the Content Builder admin.
		
				switch ( contentTypeToUse ) {
				
					case 'cpt':
					
						jQuery( '#woo-option-contenttypes .section-multicheck2 h3.heading:contains("Post Types")' ).parent().fadeIn();
						jQuery( '#woo-option-contenttypes .section-multicheck2 h3.heading:contains("Taxonomies")' ).parent().hide();
					
					break;
					
					case 'ctx':
					
						jQuery( '#woo-option-contenttypes .section-multicheck2 h3.heading:contains("Post Types")' ).parent().hide();
						jQuery( '#woo-option-contenttypes .section-multicheck2 h3.heading:contains("Taxonomies")' ).parent().fadeIn();
					
					break;
				
				} // End SWITCH Statement
			
			} // End woo_toggleCmbContentType()
		
			jQuery.validator.addMethod("accept", function(value, element, param) {
  				return value.match(new RegExp("." + param + "$"));
			});
			
			
        			
        	var typeSelected = jQuery('#woo_content_builder_cmb_type').val();
        	//jQuery('#woo_content_builder_cmb_std').val('');
			//jQuery('#woo_content_builder_cmb_options').val('');
			jQuery('#woo_content_builder_cmb_std').unmask();

        	switch (typeSelected) {
        		case 'select2':
					jQuery('#woo_content_builder_cmb_std').parent().parent().parent().removeClass('hidden');
					jQuery('#woo_content_builder_cmb_options').parent().parent().parent().removeClass('hidden');
					jQuery('#woo-option-addcustomfield div.section-info').addClass('hidden');
					jQuery('#woo-option-editcustomfield div.section-info').addClass('hidden');
					break;
				case 'radio':
					jQuery('#woo_content_builder_cmb_std').parent().parent().parent().removeClass('hidden');
					jQuery('#woo_content_builder_cmb_options').parent().parent().parent().removeClass('hidden');
					jQuery('#woo-option-addcustomfield div.section-info').addClass('hidden');
					jQuery('#woo-option-editcustomfield div.section-info').addClass('hidden');
					break;
				case 'googlemap':
					jQuery('#woo_content_builder_cmb_std').parent().parent().parent().addClass('hidden');
					jQuery('#woo_content_builder_cmb_options').parent().parent().parent().addClass('hidden');
					jQuery('#woo-option-addcustomfield div.section-info').removeClass('hidden');
					jQuery('#woo-option-editcustomfield div.section-info').removeClass('hidden');
					break;
			}
					
			jQuery('#wooform-content-builder').validate({
	
				onsubmit: true, 
				errorLabelContainer: ".error_msg",
					
				success: "valid",
		
				<?php
				// ASCII code numbers available at: http://www.theasciicode.com.ar/index.php
				
				switch ($option_args['content_type']) {
					case 'cpt' :
					?>
			  		
					rules: {
						woo_content_builder_cpt_post_type: {
							required:true,
							accept: "[a-z]+"
							}, 
				    	woo_content_builder_cpt_description: {
							required:true,
							// accept: "[0-9a-zA-Z \32-\151]+"
							},
						woo_content_builder_cpt_label: {
							required:true,
							// accept: "[0-9a-zA-Z \32-\151]+"
							},
						woo_content_builder_cpt_singular_name: {
							required:true,
							// accept: "[0-9a-zA-Z \32-\151]+"
							},
					},
				   		
				   	messages: {
				    	woo_content_builder_cpt_post_type: "Please use only lower case letters and no special characters except for the underscore.", 
				     	woo_content_builder_cpt_description: "Please use only alpha-numeric characters, no special characters except for the underscore.",
				     	woo_content_builder_cpt_label: "Please use only non-numeric characters, no special characters except for the underscore.",
				     	woo_content_builder_cpt_singular_name: "Please use only non-numeric characters, no special characters except for the underscore.",
				    }
			
					<?php
					break;
					case 'ctx' :
					?>
			  		
					rules: {
						woo_content_builder_ctx_taxonomy: {
							required:true,
							accept: "[a-z]+"
							}, 
						woo_content_builder_ctx_label: {
							required:true,
							// accept: "[0-9a-zA-Z \32-\151]+"
							},
						woo_content_builder_ctx_singular_name: {
							required:true,
							// accept: "[0-9a-zA-Z \32-\151]+"
							},
					},
				   		
				   	messages: {
				    	woo_content_builder_ctx_taxonomy: "Please use only lower case letters and no special characters except for the underscore.", 
				     	woo_content_builder_ctx_label: "Please use only non-numeric characters, no special characters except for the underscore.",
				     	woo_content_builder_ctx_singular_name: "Please use only non-numeric characters, no special characters except for the underscore.",
				    }
			
					<?php
					break;
					case 'cmb' :
					?>
			  		
					rules: {
						woo_content_builder_cmb_name: {
							required:true,
							accept: "[a-z]+"
							}, 
				    	woo_content_builder_cmb_label: {
							required:true,
							// accept: "[0-9a-zA-Z \32-\151]+"
							},
						woo_content_builder_cmb_description: {
							required:true,
							// accept: "[0-9a-zA-Z \32-\151]+"
							},
				   	},
				   		
				   	messages: {
				    	woo_content_builder_cmb_name: "Please use only lower case letters and no special characters except for the underscore.", 
				     	woo_content_builder_cmb_label: "Please use only non-numeric characters, no special characters except for the underscore.",
				   		woo_content_builder_cmb_description: "Please use only alpha-numeric characters, no special characters except for the underscore.",
				   	}
			
					<?php
					break;
			
					default :
			
					break;
		
				}
			
				?>		
				});
			
				jQuery('#woo_content_builder_cmb_type').change(function() { 
        			
        			var typeSelected = jQuery(this).val();
        			//jQuery('#woo_content_builder_cmb_std').val('');
					//jQuery('#woo_content_builder_cmb_options').val('');
					jQuery('#woo_content_builder_cmb_std').unmask();

        			switch (typeSelected) {
						case 'text':
							jQuery('#woo_content_builder_cmb_std').parent().parent().parent().removeClass('hidden');
							jQuery('#woo_content_builder_cmb_options').parent().parent().parent().addClass('hidden');
							jQuery('#woo-option-addcustomfield div.section-info').addClass('hidden');
							jQuery('#woo-option-editcustomfield div.section-info').addClass('hidden');
							break;
						case 'select2':
							jQuery('#woo_content_builder_cmb_std').parent().parent().parent().removeClass('hidden');
							jQuery('#woo_content_builder_cmb_options').parent().parent().parent().removeClass('hidden');
							jQuery('#woo-option-addcustomfield div.section-info').addClass('hidden');
							jQuery('#woo-option-editcustomfield div.section-info').addClass('hidden');
							break;
						case 'checkbox':
							jQuery('#woo_content_builder_cmb_std').parent().parent().parent().removeClass('hidden');
							jQuery('#woo_content_builder_cmb_options').parent().parent().parent().addClass('hidden');
							jQuery('#woo-option-addcustomfield div.section-info').addClass('hidden');
							jQuery('#woo-option-editcustomfield div.section-info').addClass('hidden');
							break;
						case 'textarea':
							jQuery('#woo_content_builder_cmb_std').parent().parent().parent().removeClass('hidden');
							jQuery('#woo_content_builder_cmb_options').parent().parent().parent().addClass('hidden');
							jQuery('#woo-option-addcustomfield div.section-info').addClass('hidden');
							jQuery('#woo-option-editcustomfield div.section-info').addClass('hidden');
							break;
						case 'upload':
							jQuery('#woo_content_builder_cmb_std').parent().parent().parent().addClass('hidden');
							jQuery('#woo_content_builder_cmb_options').parent().parent().parent().addClass('hidden');
							jQuery('#woo-option-addcustomfield div.section-info').addClass('hidden');
							jQuery('#woo-option-editcustomfield div.section-info').addClass('hidden');
							break;
						case 'calendar':
							jQuery('#woo_content_builder_cmb_std').parent().parent().parent().addClass('hidden');
							jQuery('#woo_content_builder_cmb_options').parent().parent().parent().addClass('hidden');
							jQuery('#woo-option-addcustomfield div.section-info').addClass('hidden');
							jQuery('#woo-option-editcustomfield div.section-info').addClass('hidden');
							break;
						case 'timestamp':
							jQuery('#woo_content_builder_cmb_std').parent().parent().parent().addClass('hidden');
							jQuery('#woo_content_builder_cmb_options').parent().parent().parent().addClass('hidden');
							jQuery('#woo-option-addcustomfield div.section-info').addClass('hidden');
							jQuery('#woo-option-editcustomfield div.section-info').addClass('hidden');
							break;
						case 'time':
							jQuery('#woo_content_builder_cmb_std').parent().parent().parent().removeClass('hidden');
							jQuery('#woo_content_builder_cmb_std').mask("99:99");
							jQuery('#woo_content_builder_cmb_options').parent().parent().parent().addClass('hidden');
							jQuery('#woo-option-addcustomfield div.section-info').addClass('hidden');
							jQuery('#woo-option-editcustomfield div.section-info').addClass('hidden');
							break;
						case 'info':
							jQuery('#woo_content_builder_cmb_std').parent().parent().parent().addClass('hidden');
							jQuery('#woo_content_builder_cmb_options').parent().parent().parent().addClass('hidden');
							jQuery('#woo-option-addcustomfield div.section-info').addClass('hidden');
							jQuery('#woo-option-editcustomfield div.section-info').addClass('hidden');
							break;
						case 'radio':
							jQuery('#woo_content_builder_cmb_std').parent().parent().parent().removeClass('hidden');
							jQuery('#woo_content_builder_cmb_options').parent().parent().parent().removeClass('hidden');
							jQuery('#woo-option-addcustomfield div.section-info').addClass('hidden');
							jQuery('#woo-option-editcustomfield div.section-info').addClass('hidden');
							break;
						case 'googlemap':
							jQuery('#woo_content_builder_cmb_std').parent().parent().parent().addClass('hidden');
							jQuery('#woo_content_builder_cmb_options').parent().parent().parent().addClass('hidden');
							jQuery('#woo-option-addcustomfield div.section-info').removeClass('hidden');
							jQuery('#woo-option-editcustomfield div.section-info').removeClass('hidden');
							break;
						default:
							jQuery('#woo_content_builder_cmb_std').parent().parent().parent().removeClass('hidden');
							jQuery('#woo_content_builder_cmb_options').parent().parent().parent().addClass('hidden');
							jQuery('#woo-option-addcustomfield div.section-info').addClass('hidden');
							jQuery('#woo-option-editcustomfield div.section-info').addClass('hidden');
					}
					        			
        		});
        		 
			});
			
	</script>
	<?php
		// Determine which tab to "remember".
	            	
		$allowed_tabs = array( 'cpt', 'ctx', 'cmb' );
		
		$tab = 'cpt';
		$requested_tab = '';
		if ( isset( $_REQUEST['content'] ) ) {
			$requested_tab = strtolower( trim( strip_tags( $_REQUEST['content'] ) ) );
		} // End If Statement
		
		if ( in_array( $requested_tab, $allowed_tabs ) ) {
		
			$tab = $requested_tab;
		
		} // End IF Statement
		
		// Run the reset script if the user has sent through that instruction.
		if ( ( isset( $_POST ) ) && ( isset( $_POST['woo_save'] ) ) && ( $_POST['woo_save'] == 'reset_cb' ) ) {
		
			woo_listings_content_install( true );
			
			// wp_redirect() doesn't seem to work here. Using a <meta> tag as a temporary solution.
			// This makes sure that the changes are reflected immediately.
			echo '<meta http-equiv="refresh" content="0;url=' . admin_url( 'admin.php?page=woothemes_content_builder' ) . '" />' . "\n";
		
		} // End IF Statement
		
		// Get the current action for use as a CSS class.
		$action = 'manage';
		if ( isset( $_GET['action'] ) && ( $_GET['action'] != '' ) ) {
			$action = esc_attr( sanitize_user( $_GET['action'] ) );
		}
	?>
    <div class="wrap <?php echo $action; ?>" id="woo_container"> 
    <div id="woo-popup-save" class="woo-save-popup"><div class="woo-save-save"><?php _e('Options Updated', 'woothemes') ?></div></div>
    <div id="woo-popup-reset" class="woo-save-popup"><div class="woo-save-reset"><?php _e('Options Reset', 'woothemes') ?></div></div>
        <form action="<?php echo admin_url('admin.php?page=woothemes_content_builder&amp;tab='.$tab); ?>" method="POST" enctype="multipart/form-data" id="wooform-content-builder">
            <div id="header">
               <div class="logo">
                <?php if(get_option('framework_woo_backend_header_image')) { ?>
                <img alt="" src="<?php echo get_option('framework_woo_backend_header_image'); ?>"/>
                <?php } else { ?>
                <img alt="WooThemes" src="<?php echo bloginfo('template_url'); ?>/functions/images/logo.png"/>
                <?php } ?>
                </div>
                <div class="theme-info">
                    <span class="theme"><?php echo $themename; ?> <?php echo $local_version; ?></span>
                    <span class="framework"><?php _e('Framework', 'woothemes') ?> <?php echo $woo_framework_version; ?></span>
                </div>
                <div class="clear"></div>
            </div>
            <div id="support-links">
        
                <ul>
                    <li class="changelog"><a title="Theme Changelog" href="<?php echo $manualurl; ?>#Changelog"><?php _e('View Changelog', 'woothemes') ?></a></li>
                    <li class="docs"><a title="Theme Documentation" href="<?php echo $manualurl; ?>"><?php _e('View Themedocs', 'woothemes') ?></a></li>
                    <li class="forum"><a href="http://forum.woothemes.com" target="_blank"><?php _e('Visit Forum', 'woothemes') ?></a></li>
                    <li class="right"><img style="display:none" src="<?php echo bloginfo('template_url'); ?>/functions/images/loading-top.gif" class="ajax-loading-img ajax-loading-img-top" alt="Working..." /><?php if ( ( isset( $_REQUEST['action'] ) ) && ( ($_REQUEST['action'] == 'add') || ($_REQUEST['action'] == 'edit') ) ) { ?><a href="#" id="expand_options">[+]</a> <input type="submit" value="Save All Changes" class="button submit-button" /><?php } ?></li>
                </ul>
        
            </div>
            <?php
		    	/* Content builder quick navigation.
		    	----------------------------------------*/
		    	
		    	$actions_to_ignore = array( 'delete' );
		    	
		    	if ( isset( $_REQUEST['content'] ) && isset( $_REQUEST['action'] ) && ! in_array( $_REQUEST['action'], $actions_to_ignore ) ) {
		    	
		    	$_quicknav = '<div id="content-builder-quicknav" class="content-builder-quicknav">' . "\n";
		    	
		    		$links = array( 'cpt' => 'Custom Post Types', 'cmb' => 'Custom Fields', 'ctx' => 'Custom Taxonomies' );
		    		
		    		$link_tokens = array( 'cpt' => 'woo-option-customposttypes', 'cmb' => 'woo-option-customfields', 'ctx' => 'woo-option-customtaxonomies' );
		    		
		    		$_quicknav .= '<ul>' . "\n";
		    		
		    			$_quicknav .= '<li class="title"><strong>' . __( 'Quick Navigation', 'woothemes' ) . ':</strong></li>';
		    		
		    			$_quicknav .= '<li class="content-builder-home"><a href="' . admin_url( 'admin.php?page=woothemes_content_builder' ) . '" title="' . __( 'Return to the Content Builder home', 'woothemes' ) . '">' . __( 'Home', 'woothemes' ) . '</a></li>' . "\n";
		    		
		    			foreach ( $links as $k => $v ) {
		    			
		    				$content = '';
		    				
		    					if ( in_array( $_REQUEST['content'], array_keys( $links ) ) ) {
		    					
		    						if ( $k == $_REQUEST['content'] ) {
		    						
		    							$content = '<span class="current">' . $v . '</span>';
		    						
		    						} else {
		    						
		    							$content = '<a href="' . admin_url( 'admin.php?page=woothemes_content_builder&amp;tab=' . $k ) . '">' . $v . '</a>';
		    						
		    						} // End IF Statement
		    					
		    					} // End IF Statement
		    			
		    				$_quicknav .= '<li class="' . $k . '">' . $content . '</li>' . "\n";
		    			
		    			} // End FOREACH Loop
		    		
		    		$_quicknav .= '</ul>' . "\n";
		    		
		    	$_quicknav .= '</div><!--/.content_builder_quicknav-->' . "\n";
		    	
		    	echo $_quicknav;
		    	
		    	} // End IF Statement
		    ?>
            <?php
            if ( ( isset( $_REQUEST['action'] ) ) && ( ($_REQUEST['action'] == 'add') || ($_REQUEST['action'] == 'edit') ) ) {
            	
            	woothemes_content_builder_content_add($content_types_options);
            	
            } else {

            	woothemes_content_builder_content_list( $content_types_options,$wp_custom_post_types,$woo_wp_custom_taxonomies,$woo_wp_custom_fields, $woo_wp_custom_fields_ctx ); 
            } 
            ?>
            <div class="save_bar_top">
            <img style="display:none" src="<?php echo bloginfo('template_url'); ?>/functions/images/loading-bottom.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="Working..." />
            <?php if ( ( isset( $_REQUEST['action'] ) ) && ( ($_REQUEST['action'] == 'add') || ($_REQUEST['action'] == 'edit') ) ) { ?><input type="submit" value="Save All Changes" class="button submit-button" /><?php } ?>      
            </form>
            <?php $nonce = wp_create_nonce  ('cancel-nonce'); ?>
            <form action="<?php echo admin_url('admin.php?page=woothemes_content_builder&amp;tab='.$tab); ?>" method="post" style="display:inline" id="wooform-content-builder-reset" class="alignleft">
            <?php if ( ( isset( $_REQUEST['action'] ) ) && ( ($_REQUEST['action'] == 'add') || ($_REQUEST['action'] == 'edit') ) ) { ?>
            <span class="submit-footer-reset">
            <input name="reset" type="submit" value="Cancel" class="fl button submit-button reset-button" onclick="return confirm('Click OK if you are sure you want to cancel.');" />
            <input type="hidden" name="woo_save" value="reset_cancel" /> 
            <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
            
            </span>
        	<?php } else { ?>
        		<span class="submit-footer-reset">
	            <input name="reset_cb" type="submit" value="Reset Content Builder" class="button submit-button reset-button" onclick="return confirm('Click OK to reset. Any Content Builder settings will be lost!');" />
	            <input type="hidden" name="woo_save" value="reset_cb" /> 
	            </span>
        	<?php } // End IF Statement ?>
        	</form>

            
            </div>
            
    <div style="clear:both;"></div>
      
    </div><!--wrap-->

<?php } 


function woothemes_content_builder_content_list($content_types_options,$wp_custom_post_types,$woo_wp_custom_taxonomies,$woo_wp_custom_fields, $woo_wp_custom_fields_ctx ) {
	?>
	<?php $return = woothemes_machine($content_types_options); ?>
    <div id="main">
        <div id="woo-nav">
            <ul>
                <?php echo $return[1]; ?>
            </ul>		
        </div>
        <div id="content">
           	<div id="woo-option-customposttypes" class="group" style="display: block;"><?php /*<h2>Add Custom Post Type</h2>*/ ?>
	    		<div class="section section-text">
	    			<h3 class="heading"><?php _e('Manage Post Types', 'woothemes') ?></h3>
	    			<div class="option">
	    				<div class="controls" style="width:100%">
	    					<table id="contentbuilder" class="widefat post fixed">
	    						<tbody>
	    							<tr><th class="manage-column column-title"><?php _e('Name', 'woothemes') ?></th><th class="manage-column column-title"><?php _e('Label', 'woothemes') ?></th><th class="manage-column column-title column-actions"><?php _e('Actions', 'woothemes') ?></th></tr>
	    							<?php foreach ($wp_custom_post_types as $wp_custom_post_type) { ?>
	    							<tr><td><?php echo $wp_custom_post_type->name; ?></td><td><?php echo stripslashes( $wp_custom_post_type->label ); ?></td><td class="last" align="center"><?php if ($wp_custom_post_type->_builtin == 0) { ?><a class="edit" title="Edit" href="<?php echo admin_url('admin.php?page='.$_REQUEST['page'].'&content=cpt&action=edit&posttype='.$wp_custom_post_type->name); ?>"><?php _e('Edit', 'woothemes') ?></a> <a class="delete" title="Delete" href="<?php echo admin_url('admin.php?page='.$_REQUEST['page'].'&content=cpt&action=delete&posttype='.$wp_custom_post_type->name); ?>"><?php _e('Delete', 'woothemes') ?></a><?php } ?></td></tr><?php } ?>
	    						</tbody>
	    					</table>
	    					<p>
	    						<a href="<?php echo admin_url('admin.php?page=woothemes_content_builder&action=add&content=cpt'); ?>" class="button"><?php _e('Add Custom Post Type', 'woothemes') ?></a>
	    					</p>
	    				</div>
	    				<div class="explain"></div>
	    				<div class="clear"> </div>
	    			</div>
	    		</div>
	    	</div>                                
	    	<div id="woo-option-customtaxonomies" class="group" style="display: block;"><?php /*<h2>Add Custom Post Type</h2>*/ ?>
	    		<div class="section section-text ">
	    			<h3 class="heading"><?php _e('Manage Taxonomies', 'woothemes') ?></h3>
	    			<div class="option">
	    				<div class="controls" style="width:100%">
	    					<table id="contentbuilder" class="widefat post fixed">
	    						<tbody>
	    							<tr><th class="manage-column column-title"><?php _e('Name', 'woothemes') ?></th><th class="manage-column column-title"><?php _e('Label', 'woothemes') ?></th><th class="manage-column column-title column-actions"><?php _e('Actions', 'woothemes') ?></th></tr>
	    							<?php foreach ($woo_wp_custom_taxonomies as $woo_wp_custom_taxonomy) { ?>
	    							<tr><td><?php echo $woo_wp_custom_taxonomy->name; ?></td><td><?php echo stripslashes( $woo_wp_custom_taxonomy->label ); ?></td><td class="last" align="center"><?php if ($woo_wp_custom_taxonomy->_builtin == 0) { ?><a class="edit" title="Edit" href="<?php echo admin_url('admin.php?page='.$_REQUEST['page'].'&content=ctx&action=edit&taxonomyname='.$woo_wp_custom_taxonomy->name); ?>"><?php _e('Edit', 'woothemes') ?></a> <a class="delete" title="Delete" href="<?php echo admin_url('admin.php?page='.$_REQUEST['page'].'&content=ctx&action=delete&taxonomyname='.$woo_wp_custom_taxonomy->name); ?>"><?php _e('Delete', 'woothemes') ?></a><?php } ?></td></tr><?php } ?>
	    						</tbody>
	    					</table>
	    					<p>
	    						<a href="<?php echo admin_url('admin.php?page=woothemes_content_builder&action=add&content=ctx'); ?>" class="button"><?php _e('Add Custom Taxonomy', 'woothemes') ?></a>
	    					</p>
	    				</div>
	    				<div class="explain"></div>
	    				<div class="clear"> </div>
	    			</div>
	    		</div>
	    	</div> 
	    	<div id="woo-option-customfields" class="group" style="display: block;"><?php /*<h2>Add Custom Post Type</h2>*/ ?>
	    		<div class="section section-text ">
	    			<h3 class="heading"><?php _e('Manage Custom Fields', 'woothemes') ?></h3>
	    			<div class="option">
	    				<div class="controls" style="width:100%">
	    					<table id="contentbuilder" class="widefat post fixed">
	    						<tbody>
	    							<tr><th class="manage-column column-title"><?php _e('Name', 'woothemes') ?></th><th class="manage-column column-title"><?php _e('Label', 'woothemes') ?></th><th class="manage-column column-title"><?php _e('Type', 'woothemes') ?></th><th class="manage-column column-title column-actions"><?php _e('Actions', 'woothemes') ?></th></tr>
	    							<?php
	    								foreach ($woo_wp_custom_fields as $woo_wp_custom_field) {
	    								
	    								$content_type = 'cpt';
	    								
	    								if ( array_key_exists( 'ctx', $woo_wp_custom_field ) && count( $woo_wp_custom_field['ctx'] ) ) {
	    								
	    									$content_type = 'ctx';
	    								
	    								} // End IF Statement
	    							?>
	    							<tr><td><?php echo $woo_wp_custom_field['name']; ?></td><td><?php echo stripslashes( $woo_wp_custom_field['label'] ); ?></td><td><?php echo $woo_wp_custom_field['type']; ?></td><td class="last" align="center"><?php if ( ( ( isset( $woo_wp_custom_field['builtin'] ) ) && ( $woo_wp_custom_field['builtin'] == 0 ) ) || ( !isset( $woo_wp_custom_field['builtin'] ) ) ) { ?><a class="edit" title="Edit" href="<?php echo admin_url( 'admin.php?page=' . $_REQUEST['page'] . '&content=cmb&action=edit&customfieldname=' . $woo_wp_custom_field['name'] . '&contenttype=' . $content_type ); ?>"><?php _e('Edit', 'woothemes') ?></a> <a class="delete" title="Delete" href="<?php echo admin_url( 'admin.php?page=' . $_REQUEST['page'] . '&content=cmb&action=delete&customfieldname=' . $woo_wp_custom_field['name'] . '&contenttype=' . $content_type ); ?>"><?php _e('Delete', 'woothemes') ?></a><?php } ?></td></tr>
	    							<?php } // End FOREACH Loop ?>
	    						</tbody>
	    					</table>
	    					<p>
	    						<a href="<?php echo admin_url('admin.php?page=woothemes_content_builder&action=add&content=cmb'); ?>" class="button"><?php _e('Add Custom Field', 'woothemes') ?></a>
	    					</p>
	    				</div>
	    				<div class="explain"></div>
	    				<div class="clear"> </div>
	    			</div>
	    		</div>
	    	</div> 
        </div>
        <div class="clear"></div>
        
    </div>
	<?php
}

function woothemes_content_builder_content_add($content_types_options) {
	?>
	<?php $return = woothemes_machine($content_types_options); ?>
    <div id="main">
        <div id="woo-nav">
            <ul>
                <?php echo $return[1]; ?>
            </ul>		
        </div>
        <div id="content">
        	<?php echo $return[0]; ?>
        	</div>
        </div>
        <input type="hidden" name="action" value="save" />
        <div class="clear"></div>
    </div>
	<?php
}


/*-----------------------------------------------------------------------------------*/
/* Content Builder - Output Actions */
/*-----------------------------------------------------------------------------------*/

function woothemes_content_builder_delete($post_var) {
	global $cb_message;

	switch ($post_var['content']) {
		case 'cpt' :
			
			$woo_cpt_all = get_option("woo_content_builder_cpt");
			$cpt_to_delete = $post_var['posttype'];
			
			$index_to_delete = 90000000000;
			foreach ($woo_cpt_all as $key => $value) {
				if ($value['name'] == $cpt_to_delete) {
					$index_to_delete = $key;
				}
			}
			$cb_message = '';
			
			if ( ($index_to_delete == 90000000000) ) {} else {
			
				if ( ($index_to_delete >= 0) && ($index_to_delete != 90000000000) ) {
					unset($woo_cpt_all[$index_to_delete]);
					update_option("woo_content_builder_cpt", $woo_cpt_all);
					// successfully deleted message
					$cb_message = '<p id="message" class="woo-sc-box tick fade" style="display:block!important">'.__('Custom Post Type "'.$cpt_to_delete.'" deleted', 'woothemes').'</div>';
					// manually update custom post types
					// woothemes_content_builder_cpt_register(); // Commented out due to it causing the incorrect message to display. Do not remove.
				} else {
					// validation error
					$cb_message = '<p id="message" class="woo-sc-box alert fade">'.__('Failed to delete Custom Post Type', 'woothemes').'</p>';
				}
			
			} // End IF Statement
			
		break;
		
		case 'ctx' :
			
			$woo_ctx_all = get_option("woo_content_builder_ctx");
			$ctx_to_delete = $post_var['taxonomyname'];
			
			$index_to_delete = 90000000000;
			foreach ($woo_ctx_all as $key => $value) {
				if ($value['name'] == $ctx_to_delete) {
					$index_to_delete = $key;
				}
			}
			$cb_message = '';
			
			if ( ($index_to_delete >= 0) && ($index_to_delete != 90000000000) ) {
				unset($woo_ctx_all[$index_to_delete]);
				update_option("woo_content_builder_ctx", $woo_ctx_all);
				// successfully deleted message
				$cb_message = '<p id="message" class="woo-sc-box tick fade">'.__('Custom Taxonomy "'.$ctx_to_delete.'" deleted', 'woothemes').'</p>';
				// manually update custom taxonomies
				woothemes_content_builder_ctx_register();
			} else {
				// validation error
				$cb_message = '<p id="message" class="woo-sc-box alert fade">'.__('Failed to delete Custom Taxonomy', 'woothemes').'</p>';
			}
			
		break;
		
		case 'cmb' :
			
			$woo_cmb_all = get_option("woo_custom_template");
			$cmb_to_delete = $post_var['customfieldname'];
										
			$index_to_delete = 90000000000;
			foreach ($woo_cmb_all as $key => $value) {
				if ($value['name'] == $cmb_to_delete) {
					$index_to_delete = $key;
				}
			}
			$cb_message = '';
			
			if ( ($index_to_delete >= 0) && ($index_to_delete != 90000000000) ) {
				unset($woo_cmb_all[$index_to_delete]);
				update_option("woo_custom_template", $woo_cmb_all);
				// successfully deleted message
				$cb_message = '<p id="message" class="woo-sc-box tick fade">'.__('Custom Field "'.$cmb_to_delete.'" deleted', 'woothemes').'</p>';
			} else {
				// validation error
				$cb_message = '<p id="message" class="woo-sc-box alert fade">'.__('Failed to delete Custom Field', 'woothemes').'</p>';
			}
			
		break;
		
		default :
			
			$cb_message = '<p id="message" class="woo-sc-box alert fade">'.__('An unexpected error has occured, please contact WooThemes support.', 'woothemes').'</p>';
			
		break;
	}
	
	return $cb_message;
}

function woothemes_content_builder_save($post_var) {
	global $cb_message;
	switch ($post_var['woo_content_builder_save_action']) {
		case 'cpt' :
			
			// get existing
			$woo_cpt_all = get_option("woo_content_builder_cpt");
			if ($woo_cpt_all == '') { 
				unset($woo_cpt_all); 
				$woo_cpt_all = array(); 
			}
			
			if ($woo_cpt == '') { 
				$woo_cpt = array(); 
			}
			
			$cb_message = '';
			
			if (!empty($post_var['woo_content_builder_cpt_post_type'])) {
				// Strip Whitespace
				$sPattern = '/\s*/m'; 
				$sReplace = '';
				$post_var['woo_content_builder_cpt_post_type'] = preg_replace( $sPattern, $sReplace, $post_var['woo_content_builder_cpt_post_type'] );
				$post_var['woo_content_builder_cpt_post_type'] = stripslashes($post_var['woo_content_builder_cpt_post_type']);
				$post_var['woo_content_builder_cpt_post_type'] = str_replace("'","",$post_var['woo_content_builder_cpt_post_type']);
				$post_var['woo_content_builder_cpt_post_type'] = str_replace('"','',$post_var['woo_content_builder_cpt_post_type']);
				
				// Attribute escaping on label, singular_name and description.
				$post_var['woo_content_builder_cpt_label'] = esc_attr( $post_var['woo_content_builder_cpt_label'] );
				$post_var['woo_content_builder_cpt_singular_name'] = esc_attr( $post_var['woo_content_builder_cpt_singular_name'] );
				$post_var['woo_content_builder_cpt_description'] = esc_attr( $post_var['woo_content_builder_cpt_description'] );
				
				$woo_cpt['name'] = strtolower($post_var['woo_content_builder_cpt_post_type']);
				$woo_cpt['args']['label'] = !empty($post_var['woo_content_builder_cpt_label']) ? $post_var['woo_content_builder_cpt_label'] : $woo_cpt['name'];
				$woo_cpt['args']['labels']['name'] = !empty($post_var['woo_content_builder_cpt_label']) ? $post_var['woo_content_builder_cpt_label'] : __($woo_cpt['args']['label'], 'woothemes');
				$woo_cpt['args']['labels']['singular_name'] = !empty($post_var['woo_content_builder_cpt_singular_name']) ? $post_var['woo_content_builder_cpt_singular_name'] : __($woo_cpt['args']['labels']['name'], 'woothemes');
				$woo_cpt['args']['labels']['add_new'] = !empty($post_var['woo_content_builder_cpt_add_new']) ? $post_var['woo_content_builder_cpt_add_new'] : __('Add New', 'woothemes');
				$woo_cpt['args']['labels']['add_new_item'] = !empty($post_var['woo_content_builder_cpt_add_new_item']) ? $post_var['woo_content_builder_cpt_add_new_item'] : __('Add New '.$woo_cpt['args']['label'], 'woothemes');
				$woo_cpt['args']['labels']['edit_item'] = !empty($post_var['woo_content_builder_cpt_edit_item']) ? $post_var['woo_content_builder_cpt_edit_item'] : __('Edit '.$woo_cpt['args']['label'], 'woothemes');
				$woo_cpt['args']['labels']['new_item'] = !empty($post_var['woo_content_builder_cpt_new_item']) ? $post_var['woo_content_builder_cpt_new_item'] : __('New '.$woo_cpt['args']['label'], 'woothemes');
				$woo_cpt['args']['labels']['view_item'] = !empty($post_var['woo_content_builder_cpt_view_item']) ? $post_var['woo_content_builder_cpt_view_item'] : __('View '.$woo_cpt['args']['label'], 'woothemes');
				$woo_cpt['args']['labels']['search_items'] = !empty($post_var['woo_content_builder_cpt_search_items']) ? $post_var['woo_content_builder_cpt_search_items'] : __('Search '.$woo_cpt['args']['label'], 'woothemes');
				$woo_cpt['args']['labels']['not_found'] = !empty($post_var['woo_content_builder_cpt_not_found']) ? $post_var['woo_content_builder_cpt_not_found'] : __('No '.$woo_cpt['args']['label'].' found', 'woothemes');
				$woo_cpt['args']['labels']['not_found_in_trash'] = !empty($post_var['woo_content_builder_cpt_not_found_in_trash']) ? $post_var['woo_content_builder_cpt_not_found_in_trash'] : __('No '.$woo_cpt['args']['label'].' found in Thrash', 'woothemes');
				$woo_cpt['args']['labels']['parent_item_colon'] = !empty($post_var['woo_content_builder_cpt_parent_item_colon']) ? $post_var['woo_content_builder_cpt_parent_item_colon'] : __('Parent '.$woo_cpt['args']['label'], 'woothemes');
				$woo_cpt['args']['description'] = !empty($post_var['woo_content_builder_cpt_description']) ? $post_var['woo_content_builder_cpt_description'] : '';
				$woo_cpt['args']['public'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_public']);
				$woo_cpt['args']['publicly_queryable'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_publicly_queryable']);
				$woo_cpt['args']['exclude_from_search'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_exclude_from_search']);
				$woo_cpt['args']['show_ui'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_show_ui']);
				$woo_cpt['args']['capability_type'] =  !empty($post_var['woo_content_builder_cpt_capability_type']) ? $post_var['woo_content_builder_cpt_capability_type'] : 'post';
				$woo_cpt['args']['hierarchical'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_hierarchical']);
				$woo_cpt['args']['supports'] = array();
				if(woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_features_title']) == 1) array_push($woo_cpt['args']['supports'],'title');
				if(woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_features_editor']) == 1) array_push($woo_cpt['args']['supports'],'editor');
				if(woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_features_author']) == 1) array_push($woo_cpt['args']['supports'],'author');
				if(woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_features_thumbnail']) == 1) array_push($woo_cpt['args']['supports'],'thumbnail');
				if(woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_features_excerpt']) == 1) array_push($woo_cpt['args']['supports'],'excerpt');
				if(woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_features_trackbacks']) == 1) array_push($woo_cpt['args']['supports'],'trackbacks');
				if(woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_features_custom-fields']) == 1) array_push($woo_cpt['args']['supports'],'custom-fields');
				if(woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_features_comments']) == 1) array_push($woo_cpt['args']['supports'],'comments');
				if(woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_features_revisions']) == 1) array_push($woo_cpt['args']['supports'],'revisions');
				if(woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_features_page-attributes']) == 1) array_push($woo_cpt['args']['supports'],'page-attributes');
				$woo_cpt['args']['register_meta_box_cb'] = !empty($post_var['woo_content_builder_cpt_register_meta_box_cb']) ? $post_var['woo_content_builder_cpt_register_meta_box_cb'] : '';
				
				// primary taxonomy check
				$woo_wp_custom_taxonomies = get_taxonomies($wp_custom_taxonomy_args,'objects');
				$woo_cpt['args']['taxonomies'] = array();
				foreach ($woo_wp_custom_taxonomies as $ctx_item) {
					$ctx_item_name = $ctx_item->name;
					if(woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_taxonomies_'.$ctx_item_name]) == 1) array_push($woo_cpt['args']['taxonomies'],$ctx_item_name);
				}
				// secondary taxonomy check
				$woo_ctx_all = get_option("woo_content_builder_ctx");
				foreach ($woo_ctx_all as $ctx_item) {
					$ctx_item_name = $ctx_item['name'];
					if(woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_taxonomies_'.$ctx_item_name]) == 1) array_push($woo_cpt['args']['taxonomies'],$ctx_item_name);
				}	
				
				$woo_cpt['args']['menu_position'] = intval($post_var['woo_content_builder_cpt_menu_position']);
				$woo_cpt['args']['menu_icon'] = !empty($post_var['woo_content_builder_cpt_menu_icon']) ? $post_var['woo_content_builder_cpt_menu_icon'] : null;
				$woo_cpt['args']['permalink_epmask'] = !empty($post_var['woo_content_builder_cpt_permalink_epmask']) ? $post_var['woo_content_builder_cpt_permalink_epmask'] : 'EP_PERMALINK';
				$woo_cpt['args']['rewrite'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_rewrite']);
				if(woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_rewrite'])){
					$woo_cpt['args']['rewrite'] =array();
					$woo_cpt['args']['rewrite']['slug'] = !empty($post_var['woo_content_builder_cpt_rewrite_slug']) ? $post_var['woo_content_builder_cpt_rewrite_slug'] :$woo_cpt['name'];
					$woo_cpt['args']['rewrite']['with_front'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_rewrite_with_front']);
				} else {
					$woo_cpt['args']['rewrite'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_rewrite']);
				}
				$woo_cpt['args']['query_var'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_query_var']);
				$woo_cpt['args']['can_export'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_can_export']);
				$woo_cpt['args']['has_archive'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_has_archive']);
				$woo_cpt['args']['show_in_nav_menus'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_cpt_show_in_nav_menus']);
				
				// remove existing item if this is an edit save
				if ( ($post_var['woo_content_builder_cpt_array_index'] >= 0) && ($post_var['woo_content_builder_cpt_array_index'] != '' )) {
					unset($woo_cpt_all[$post_var['woo_content_builder_cpt_array_index']]);
					$woo_cpt_all[$post_var['woo_content_builder_cpt_array_index']] = $woo_cpt;
					$is_edit = true;
				} else {
					array_push($woo_cpt_all, $woo_cpt);
					$is_edit = false;
				}
				
				if ($is_edit) {
					$cb_message = '<p id="message" class="woo-sc-box tick fade">'.__('Custom Post Type edited successfully', 'woothemes').'</p>';
				} else {
					$cb_message = '<p id="message" class="woo-sc-box tick fade">'.__('Custom Post Type added successfully', 'woothemes').'</p>';
				}
				
				
				
			} else {
				// validation error
				$cb_message = '<p id="message" class="woo-sc-box alert fade">'.__('Failed to save Custom Post Type', 'woothemes').'</p>';
			}
				
			update_option("woo_content_builder_cpt", $woo_cpt_all);
			// manually update custom post types
			// woothemes_content_builder_cpt_register();
				
		break;
		
		case 'ctx' :
			
			// get existing
			// RESET - update_option("woo_content_builder_ctx",'');
			$woo_ctx_all = get_option("woo_content_builder_ctx");
			if ($woo_ctx_all == '') { 
				unset($woo_ctx_all); 
				$woo_ctx_all = array(); 
			}
			
			if ($woo_ctx == '') { 
				$woo_ctx = array(); 
			}
			
			$cb_message = '';
			
			if (!empty($post_var['woo_content_builder_ctx_taxonomy'])) {
				
				// Strip Whitespace
				$sPattern = '/\s*/m'; 
				$sReplace = '';
				$post_var['woo_content_builder_ctx_taxonomy'] = preg_replace( $sPattern, $sReplace, $post_var['woo_content_builder_ctx_taxonomy'] );
				$post_var['woo_content_builder_ctx_taxonomy'] = stripslashes($post_var['woo_content_builder_ctx_taxonomy']);
				$post_var['woo_content_builder_ctx_taxonomy'] = str_replace("'","",$post_var['woo_content_builder_ctx_taxonomy']);
				$post_var['woo_content_builder_ctx_taxonomy'] = str_replace('"','',$post_var['woo_content_builder_ctx_taxonomy']);
				
				$woo_ctx['name'] = strtolower($post_var['woo_content_builder_ctx_taxonomy']);
				
				// Attribute escaping on label and singular_name.
				$post_var['woo_content_builder_cpt_label'] = esc_attr( $post_var['woo_content_builder_cpt_label'] );
				$post_var['woo_content_builder_ctx_singular_name'] = esc_attr( $post_var['woo_content_builder_ctx_singular_name'] );
				
				$wp_custom_post_types = get_post_types($args,'objects');
				// primary post types check
				$woo_ctx['object_type'] = array();
				foreach ($wp_custom_post_types as $ctx_item) {
					$ctx_item_name = $ctx_item->name;
					if (woothemes_content_builder_checkbool($post_var['woo_content_builder_ctx_post_types_'.$ctx_item_name]) == 1) { array_push($woo_ctx['object_type'],$ctx_item_name); }
				}
				// secondary post type check
				$woo_cpt_all = get_option("woo_content_builder_cpt");
				foreach ($woo_cpt_all as $ctx_item) {
					$ctx_item_name = $ctx_item['name'];
					if (woothemes_content_builder_checkbool($post_var['woo_content_builder_ctx_post_types_'.$ctx_item_name]) == 1) { array_push($woo_ctx['object_type'],$ctx_item_name); }
				}
				
				$woo_ctx['args']['label'] = !empty($post_var['woo_content_builder_ctx_label']) ? $post_var['woo_content_builder_ctx_label'] : $woo_ctx['name'];
				$woo_ctx['args']['labels']['name'] = !empty($post_var['woo_content_builder_cpt_label']) ? $post_var['woo_content_builder_cpt_label'] : __($woo_ctx['args']['label'], 'woothemes');
				$woo_ctx['args']['labels']['singular_name'] = !empty($post_var['woo_content_builder_ctx_singular_name']) ? $post_var['woo_content_builder_ctx_singular_name'] : __($woo_ctx['args']['labels']['name'], 'woothemes');
				
				
				$woo_ctx['args']['labels']['search_items'] = !empty($post_var['woo_content_builder_ctx_search_items']) ? $post_var['woo_content_builder_ctx_search_items'] : __('Search Items', 'woothemes');
				
				$woo_ctx['args']['labels']['popular_items'] = !empty($post_var['woo_content_builder_ctx_popular_items']) ? $post_var['woo_content_builder_ctx_popular_items'] : __('Popular Items', 'woothemes');
				$woo_ctx['args']['labels']['all_items'] = !empty($post_var['woo_content_builder_ctx_all_items']) ? $post_var['woo_content_builder_ctx_all_items'] : __('All Items', 'woothemes');
				$woo_ctx['args']['labels']['parent_item'] = !empty($post_var['woo_content_builder_ctx_parent_item']) ? $post_var['woo_content_builder_ctx_parent_item'] : __('Parent Item', 'woothemes');
				$woo_ctx['args']['labels']['parent_item_colon'] = !empty($post_var['woo_content_builder_ctx_parent_item_with_colon']) ? $post_var['woo_content_builder_ctx_parent_item_with_colon'] : __('Parent Item:', 'woothemes');
				$woo_ctx['args']['labels']['edit_item'] = !empty($post_var['woo_content_builder_ctx_edit_item']) ? $post_var['woo_content_builder_ctx_edit_item'] : __('Edit Item', 'woothemes');
				$woo_ctx['args']['labels']['update_item'] = !empty($post_var['woo_content_builder_ctx_update_item']) ? $post_var['woo_content_builder_ctx_update_item'] : __('Update Item', 'woothemes');
				$woo_ctx['args']['labels']['add_new_item'] = !empty($post_var['woo_content_builder_ctx_add_new_item']) ? $post_var['woo_content_builder_ctx_add_new_item'] : __('Add New Item', 'woothemes');
				$woo_ctx['args']['labels']['new_item_name'] = !empty($post_var['woo_content_builder_ctx_new_item_name']) ? $post_var['woo_content_builder_ctx_new_item_name'] : __('New Item Name', 'woothemes');
				$woo_ctx['args']['labels']['separate_items_with_commas'] = !empty($post_var['woo_content_builder_ctx_separate_items_with_commas']) ? $post_var['woo_content_builder_ctx_separate_items_with_commas'] : __('Separate items with commas', 'woothemes');
				$woo_ctx['args']['labels']['add_or_remove_items'] = !empty($post_var['woo_content_builder_ctx_add_or_remove_items']) ? $post_var['woo_content_builder_ctx_add_or_remove_items'] : __('Add or remove items', 'woothemes');
				$woo_ctx['args']['labels']['choose_from_most_used'] = !empty($post_var['woo_content_builder_ctx_choose_from_the_most_used_items']) ? $post_var['woo_content_builder_ctx_choose_from_the_most_used_items'] : __('Choose from the most used items', 'woothemes');
				
				$woo_ctx['args']['public'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_ctx_public']);
				$woo_ctx['args']['hierarchical'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_ctx_hierarchical']);
				$woo_ctx['args']['show_ui'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_ctx_show_ui']);
				$woo_ctx['args']['show_in_nav_menus'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_ctx_show_in_nav_menus']);
				$woo_ctx['args']['show_tagcloud'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_ctx_show_tag_cloud']);
				$woo_ctx['args']['rewrite'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_ctx_rewrite']);
				if(woothemes_content_builder_checkbool($post_var['woo_content_builder_ctx_rewrite'])){
					$woo_ctx['args']['rewrite'] =array();
					$woo_ctx['args']['rewrite']['slug'] = !empty($post_var['woo_content_builder_ctx_rewrite_slug']) ? $post_var['woo_content_builder_ctx_rewrite_slug'] :$woo_ctx['name'];
					$woo_ctx['args']['rewrite']['with_front'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_ctx_rewrite_with_front']);
					$woo_ctx['args']['rewrite']['hierarchical'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_ctx_rewrite_hierarchical']);
				} else {
					$woo_ctx['args']['rewrite'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_ctx_rewrite']);
				}
				$woo_ctx['args']['query_var'] = woothemes_content_builder_checkbool($post_var['woo_content_builder_ctx_query_var']);
				
				$woo_ctx['args']['update_count_callback'] = !empty($post_var['woo_content_builder_ctx_update_count_callback']) ? $post_var['woo_content_builder_ctx_update_count_callback'] : '';
				
				// remove existing item if this is an edit save
				if ( ($post_var['woo_content_builder_ctx_array_index'] >= 0) && ($post_var['woo_content_builder_ctx_array_index'] != '' )) {
					unset($woo_ctx_all[$post_var['woo_content_builder_ctx_array_index']]);
					$woo_ctx_all[$post_var['woo_content_builder_ctx_array_index']] = $woo_ctx;
					$is_edit = true;
				} else {
					array_push($woo_ctx_all, $woo_ctx);
					$is_edit = false;
				}
				
				if ($is_edit) {
					$cb_message = '<p id="message" class="woo-sc-box tick fade">'.__('Custom Taxonomy edited successfully', 'woothemes').'</p>';
				} else {
					$cb_message = '<p id="message" class="woo-sc-box tick fade">'.__('Custom Taxonomy added successfully', 'woothemes').'</p>';
				}
				
			} else {
				// validation error
				$cb_message = '<p id="message" class="woo-sc-box alert fade">'.__('Failed to save Custom Taxonomy', 'woothemes').'</p>';
			}
				
			update_option("woo_content_builder_ctx", $woo_ctx_all);
			// manually update custom taxonomies
			// woothemes_content_builder_ctx_register();
			
		break;
		
		case 'cmb' :
			
			$woo_cmb_all = get_option("woo_custom_template");
			if ($woo_cmb_all == '') {
				$woo_cmb_all = array();
			}
			
			$options_array = array();
			$options_array = explode("\n", $post_var['woo_content_builder_cmb_options']);
			$options_array = woothemes_content_builder_array_remove_empty($options_array);
			
			// Strip Whitespace
			$sPattern = '/\s*/m'; 
			$sReplace = '';
			$post_var['woo_content_builder_cmb_name'] = preg_replace( $sPattern, $sReplace, $post_var['woo_content_builder_cmb_name'] );
			$post_var['woo_content_builder_cmb_name'] = stripslashes($post_var['woo_content_builder_cmb_name']);
			$post_var['woo_content_builder_cmb_name'] = str_replace("'","",$post_var['woo_content_builder_cmb_name']);
			$post_var['woo_content_builder_cmb_name'] = str_replace('"','',$post_var['woo_content_builder_cmb_name']);
				
			// Attribute escaping on label and description.
			$post_var['woo_content_builder_cmb_label'] = esc_attr( $post_var['woo_content_builder_cmb_label'] );
			$post_var['woo_content_builder_cmb_description'] = esc_attr( $post_var['woo_content_builder_cmb_description'] );
				
			$woo_cmb 	= array (	"name" 		=> 	strtolower($post_var['woo_content_builder_cmb_name']),
									"std" 		=> 	$post_var['woo_content_builder_cmb_std'],
									"label" 	=> 	$post_var['woo_content_builder_cmb_label'],
									"type" 		=> 	$post_var['woo_content_builder_cmb_type'],
									"desc" 		=> 	$post_var['woo_content_builder_cmb_description'],
									"options" 	=> 	$options_array);
			
			// Determine whether this field will be for CPTs or CTXs, and add the appropriate data
			// to the end of the array.
			
			$content_type = 'cpt';
			$db_option = 'woo_custom_template';
			
			if ( $post_var['woo_content_builder_cmb_contenttype'] == 'ctx' ) {
			
				$content_type = 'ctx';
				$db_option = 'woo_content_builder_cmb_ctx';
				/*
				$woo_cmb_all = get_option("woo_content_builder_cmb_ctx");
				
				if ($woo_cmb_all == '') {
				
					$woo_cmb_all = array();
					
				} // End IF Statement
				*/
			
			} // End IF Statement
			
			// If it's for a CPT, add the CPTs to the array.
			
			switch ( $content_type ) {
		
				case 'cpt':
				
					$wp_custom_post_types = get_post_types($wp_custom_post_types_args,'objects');
					// primary post types check
					foreach ($wp_custom_post_types as $ctx_item) {
						
						if ($post_var['woo_content_builder_cmb_cpt_'.$ctx_item->name] == 'true') {
							$woo_cmb['cpt'][$ctx_item->name] = $post_var['woo_content_builder_cmb_cpt_'.$ctx_item->name];
						}
						
					}
					// secondary post type check
					$woo_cpt_all = get_option("woo_content_builder_cpt");
					foreach ($woo_cpt_all as $ctx_item) {
						if ($post_var['woo_content_builder_cmb_cpt_'.$ctx_item['name']] == 'true') {
							$woo_cmb['cpt'][$ctx_item['name']] = $post_var['woo_content_builder_cmb_cpt_'.$ctx_item['name']];
						}
					}
				
				break;
				
				case 'ctx':
				
					// Get the field to support woothemes_machine().
					$woo_cmb['cpt']['TAXONOMY'] = 'true';
					
				
					$wp_custom_taxonomies_args = array();
				
					$wp_custom_taxonomies = get_taxonomies( $wp_custom_taxonomies_args, 'objects' );
					// primary post types check
					foreach ($wp_custom_taxonomies as $ctx_item) {
						
						if ($post_var['woo_content_builder_cmb_ctx_'.$ctx_item->name] == 'true') {
							$woo_cmb['ctx'][$ctx_item->name] = $post_var['woo_content_builder_cmb_ctx_'.$ctx_item->name];
						}
						
					}
					// secondary post type check
					$woo_ctx_all = get_option("woo_content_builder_ctx");
					foreach ($woo_ctx_all as $ctx_item) {
						if ($post_var['woo_content_builder_cmb_ctx_'.$ctx_item['name']] == 'true') {
							$woo_cmb['ctx'][$ctx_item['name']] = $post_var['woo_content_builder_cmb_ctx_'.$ctx_item['name']];
						}
					}
				
				break;
			
			} // End SWITCH Statement
				
			// remove existing item if this is an edit save
			if ( ($post_var['woo_content_builder_cmb_array_index'] >= 0) && ($post_var['woo_content_builder_cmb_array_index'] != '' )) {
				unset($woo_cmb_all[$post_var['woo_content_builder_cmb_array_index']]);
				$woo_cmb_all[$post_var['woo_content_builder_cmb_array_index']] = $woo_cmb;
				$is_edit = true;
			} else {
				array_push($woo_cmb_all, $woo_cmb);
				$is_edit = false;
			}
			
			/*
			print_r( $woo_cmb_all );
			die(); // DEBUG
			*/
			
			update_option('woo_custom_template', $woo_cmb_all);
			update_option($db_option, $woo_cmb_all);
										
			if ($is_edit) {
				$cb_message = '<p id="message" class="woo-sc-box tick fade">'.__('Custom Field edited successfully', 'woothemes').'</p>';
			} else {
				$cb_message = '<p id="message" class="woo-sc-box tick fade">'.__('Custom Field added successfully', 'woothemes').'</p>';
			}
			
			// Make a copy of the woo_custom_template for when we re-activate the theme.
			listings_content_builder_cmb_backup();
			
		break;
		
		default :
			
			$cb_message = '<p id="message" class="woo-sc-box alert fade">'.__('An unexpected error has occured, please contact WooThemes support.', 'woothemes').'</p>';
			
		break;
	}
	
	return $cb_message;
}


/*-----------------------------------------------------------------------------------*/
/* Content Builder - Register Functions */
/*-----------------------------------------------------------------------------------*/

if ( !function_exists( 'woothemes_content_builder_cpt_register' ) ) { 

	add_action( 'init', 'woothemes_content_builder_cpt_register' , 1);

	function woothemes_content_builder_cpt_register() {
    	global $cb_message;
    	//RESET DEBUG 
    	//update_option("woo_content_builder_cpt",'');
		if ( isset( $_REQUEST['action']) && $_REQUEST['action'] == 'save') {
			$cb_message = woothemes_content_builder_save($_POST);
		} elseif ( isset( $_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
			$cb_message = woothemes_content_builder_delete($_REQUEST);
		}
    	$woo_db_cpts = get_option("woo_content_builder_cpt");
    	
    	if (is_array($woo_db_cpts) && !empty($woo_db_cpts))
    	    foreach ($woo_db_cpts  as $k => $cpt )
    	    {
				$woo_cpt['name'] = $cpt['name'];
				$woo_cpt['args']['label'] = !empty($cpt['args']['label']) ? $cpt['args']['label'] : $woo_cpt['name'];
				$woo_cpt['args']['labels']['name'] = !empty($cpt['args']['labels']['name']) ? $cpt['args']['labels']['name'] : __($woo_cpt['args']['label'], 'woothemes');
				$woo_cpt['args']['labels']['singular_name'] = !empty($cpt['args']['labels']['singular_name']) ? $cpt['args']['labels']['singular_name'] : __($woo_cpt['args']['labels']['name'], 'woothemes');
				$woo_cpt['args']['labels']['add_new'] = !empty($cpt['args']['labels']['add_new']) ? $cpt['args']['labels']['add_new'] : __('Add New', 'woothemes');
				$woo_cpt['args']['labels']['add_new_item'] = !empty($cpt['args']['labels']['add_new_item']) ? $cpt['args']['labels']['add_new_item'] : __('Add New '.$woo_cpt['args']['label'], 'woothemes');
				$woo_cpt['args']['labels']['edit_item'] = !empty($cpt['args']['labels']['edit_item']) ? $cpt['args']['labels']['edit_item'] : __('Edit '.$woo_cpt['args']['label'], 'woothemes');
				$woo_cpt['args']['labels']['new_item'] = !empty($cpt['args']['labels']['new_item']) ? $cpt['args']['labels']['new_item'] : __('New '.$woo_cpt['args']['label'], 'woothemes');
				$woo_cpt['args']['labels']['view_item'] = !empty($cpt['args']['labels']['view_item']) ? $cpt['args']['labels']['view_item'] : __('View '.$woo_cpt['args']['label'], 'woothemes');
				$woo_cpt['args']['labels']['search_items'] = !empty($cpt['args']['labels']['search_items']) ? $cpt['args']['labels']['search_items'] : __('Search '.$woo_cpt['args']['label'], 'woothemes');
				$woo_cpt['args']['labels']['not_found'] = !empty($cpt['args']['labels']['not_found']) ? $cpt['args']['labels']['not_found'] : __('No '.$woo_cpt['args']['label'].' found', 'woothemes');
				$woo_cpt['args']['labels']['not_found_in_trash'] = !empty($cpt['args']['labels']['not_found_in_trash']) ? $cpt['args']['labels']['not_found_in_trash'] : __('No '.$woo_cpt['args']['label'].' found in Thrash', 'woothemes');
				$woo_cpt['args']['labels']['parent_item_colon'] = !empty($cpt['args']['labels']['parent_item_colon']) ? $cpt['args']['labels']['parent_item_colon'] : __('Parent '.$woo_cpt['args']['label'], 'woothemes');
				$woo_cpt['args']['description'] = !empty($cpt['args']['description']) ? $cpt['args']['description'] : '';
				if (woothemes_content_builder_checkbool($cpt['args']['public']) == 1) { $woo_cpt['args']['public'] = true;  } else { $woo_cpt['args']['public'] = false;}
				if (woothemes_content_builder_checkbool($cpt['args']['publicly_queryable']) == 1) { $woo_cpt['args']['publicly_queryable'] = true; } else { $woo_cpt['args']['publicly_queryable'] = false;}
				if (woothemes_content_builder_checkbool($cpt['args']['exclude_from_search']) == 1) { $woo_cpt['args']['exclude_from_search'] = true; } else { $woo_cpt['args']['exclude_from_search'] = false;}
				
				if (woothemes_content_builder_checkbool($cpt['args']['show_ui']) == 1) { $woo_cpt['args']['show_ui'] = true; } else { $woo_cpt['args']['show_ui'] = false;}
				if (woothemes_content_builder_checkbool($cpt['args']['hierarchical']) == 1) { $woo_cpt['args']['hierarchical'] = true; } else { $woo_cpt['args']['hierarchical'] = false;}
				
				$woo_cpt['args']['capability_type'] =  !empty($cpt['args']['capability_type']) ? $cpt['args']['capability_type'] : 'post';
				$woo_cpt['args']['supports'] = $cpt['args']['supports'];
				$woo_cpt['args']['taxonomies'] = $cpt['args']['taxonomies'];
				$woo_cpt['args']['register_meta_box_cb'] = !empty($cpt['args']['register_meta_box_cb']) ? $cpt['args']['register_meta_box_cb'] : '';
				$woo_cpt['args']['menu_position'] = intval($cpt['args']['menu_position']);
				$woo_cpt['args']['menu_icon'] = !empty($cpt['args']['menu_icon']) ? $cpt['args']['menu_icon'] : null;
				$woo_cpt['args']['permalink_epmask'] = !empty($cpt['args']['permalink_epmask']) ? $cpt['args']['permalink_epmask'] : 'EP_PERMALINK';
				$woo_cpt['args']['rewrite'] = woothemes_content_builder_checkbool($cpt['args']['rewrite']);
				if(woothemes_content_builder_checkbool($cpt['args']['rewrite']) == 1){
					$woo_cpt['args']['rewrite'] = array();
					$woo_cpt['args']['rewrite']['slug'] = !empty($cpt['args']['rewrite']['slug']) ? $cpt['args']['rewrite']['slug'] :$woo_cpt['name'];
					if (woothemes_content_builder_checkbool($cpt['args']['rewrite']['with_front']) == 1) { $woo_cpt['args']['rewrite']['with_front'] = true; } else { $woo_cpt['args']['rewrite']['with_front'] = false;}
				} else {
					if (woothemes_content_builder_checkbool($cpt['args']['rewrite']) == 1) { $woo_cpt['args']['rewrite'] = true; } else { $woo_cpt['args']['rewrite'] = false;}
				}
				
				if (woothemes_content_builder_checkbool($cpt['args']['query_var']) == 1) { $woo_cpt['args']['query_var'] = true; } else { $woo_cpt['args']['query_var'] = false;}
				if (woothemes_content_builder_checkbool($cpt['args']['can_export']) == 1) { $woo_cpt['args']['can_export'] = true; } else { $woo_cpt['args']['can_export'] = false;}
				if (woothemes_content_builder_checkbool($cpt['args']['show_in_nav_menus']) == 1) { $woo_cpt['args']['show_in_nav_menus'] = true; } else { $woo_cpt['args']['show_in_nav_menus'] = false;}
				
				// Set if has archive page
				if ( isset( $cpt['args']['has_archive'] ) ) {
					if (woothemes_content_builder_checkbool($cpt['args']['has_archive']) == 1) { $woo_cpt['args']['has_archive'] = true; } else { $woo_cpt['args']['has_archive'] = false; }
				} else {
					$woo_cpt['args']['has_archive'] = false;
				}
				
				// Register the post type
				register_post_type( $woo_cpt['name'], $woo_cpt['args'] );

			}
			
		if(function_exists('flush_rewrite_rules') && isset( $_REQUEST['action'] ) && ( $_REQUEST['action'] == 'save' || $_REQUEST['action'] == 'delete' ) ) { flush_rewrite_rules(); }
	
	}
}

if ( !function_exists( 'woothemes_content_builder_ctx_register' ) ) { 

	add_action( 'init', 'woothemes_content_builder_ctx_register' , 2);

	function woothemes_content_builder_ctx_register() {
		
		//RESET DEBUG 
    	//update_option("woo_content_builder_ctx",'');
    	$woo_db_ctxs = get_option("woo_content_builder_ctx");
    	
    	if (is_array($woo_db_ctxs) && !empty($woo_db_ctxs))
    	    foreach ($woo_db_ctxs  as $k => $ctx )
    	    {
				$woo_ctx['name'] = $ctx['name'];
				$woo_ctx['object_type'] = $ctx['object_type'];
				
				$woo_ctx['args']['label'] = !empty($ctx['args']['label']) ? $ctx['args']['label'] : $woo_ctx['name'];
				
				$woo_ctx['args']['labels']['name'] = !empty($ctx['args']['labels']['name']) ? $ctx['args']['labels']['name'] : __($woo_ctx['args']['label'], 'woothemes');
				$woo_ctx['args']['labels']['singular_name'] = !empty($ctx['args']['labels']['singular_name']) ? $ctx['args']['labels']['singular_name'] : __($woo_ctx['args']['labels']['name'], 'woothemes');
				$woo_ctx['args']['labels']['search_items'] = !empty($ctx['args']['labels']['search_items']) ? $ctx['args']['labels']['search_items'] : __('Search Items', 'woothemes');
				$woo_ctx['args']['labels']['popular_items'] = !empty($ctx['args']['labels']['popular_items']) ? $ctx['args']['labels']['popular_items'] : __('Popular Items', 'woothemes');
				$woo_ctx['args']['labels']['all_items'] = !empty($ctx['args']['labels']['all_items']) ? $ctx['args']['labels']['all_items'] : __('All Items', 'woothemes');
				$woo_ctx['args']['labels']['parent_item'] = !empty($ctx['args']['labels']['parent_item']) ? $ctx['args']['labels']['parent_item'] : __('Parent Item', 'woothemes');
				$woo_ctx['args']['labels']['parent_item_colon'] = !empty($ctx['args']['labels']['parent_item_colon']) ? $ctx['args']['labels']['parent_item_colon'] : __('Parent Item:', 'woothemes');
				$woo_ctx['args']['labels']['edit_item'] = !empty($ctx['args']['labels']['edit_item']) ? $ctx['args']['labels']['edit_item'] : __('Edit Item', 'woothemes');
				$woo_ctx['args']['labels']['update_item'] = !empty($ctx['args']['labels']['update_item']) ? $ctx['args']['labels']['update_item'] : __('Update Item', 'woothemes');
				$woo_ctx['args']['labels']['add_new_item'] = !empty($ctx['args']['labels']['add_new_item']) ? $ctx['args']['labels']['add_new_item'] : __('Add New Item', 'woothemes');
				$woo_ctx['args']['labels']['new_item_name'] = !empty($ctx['args']['labels']['new_item_name']) ? $ctx['args']['labels']['new_item_name'] : __('New Item Name', 'woothemes');
				$woo_ctx['args']['labels']['separate_items_with_commas'] = !empty($ctx['args']['labels']['separate_items_with_commas']) ? $ctx['args']['labels']['separate_items_with_commas'] : __('Separate items with commas', 'woothemes');
				$woo_ctx['args']['labels']['add_or_remove_items'] = !empty($ctx['args']['labels']['add_or_remove_items']) ? $ctx['args']['labels']['add_or_remove_items'] : __('Add or remove items', 'woothemes');
				$woo_ctx['args']['labels']['choose_from_most_used'] = !empty($ctx['args']['labels']['choose_from_most_used']) ? $ctx['args']['labels']['choose_from_most_used'] : __('Choose from the most used items', 'woothemes');
				
				if (woothemes_content_builder_checkbool($ctx['args']['public']) == 1) { $woo_ctx['args']['public'] = true; } else { $woo_ctx['args']['public'] = false;}
				if (woothemes_content_builder_checkbool($ctx['args']['hierarchical']) == 1) { $woo_ctx['args']['hierarchical'] = true; } else { $woo_ctx['args']['hierarchical'] = false;}
				if (woothemes_content_builder_checkbool($ctx['args']['show_ui']) == 1) { $woo_ctx['args']['show_ui'] = true; } else { $woo_ctx['args']['show_ui'] = false;}
				if (woothemes_content_builder_checkbool($ctx['args']['show_in_nav_menus']) == 1) { $woo_ctx['args']['show_in_nav_menus'] = true; } else { $woo_ctx['args']['show_in_nav_menus'] = false;}
				if (woothemes_content_builder_checkbool($ctx['args']['show_tagcloud']) == 1) { $woo_ctx['args']['show_tagcloud'] = true; } else { $woo_ctx['args']['show_tagcloud'] = false;}
				if (woothemes_content_builder_checkbool($ctx['args']['rewrite']) == 1) { $woo_ctx['args']['rewrite'] = true; } else { $woo_ctx['args']['rewrite'] = false;}
				
				if(woothemes_content_builder_checkbool($ctx['args']['rewrite']) == 1){
					$woo_ctx['args']['rewrite'] = array();
					$woo_ctx['args']['rewrite']['slug'] = !empty($ctx['args']['rewrite']['slug']) ? $ctx['args']['rewrite']['slug'] :$woo_ctx['name'];
					if (woothemes_content_builder_checkbool($ctx['args']['rewrite']['with_front']) == 1) { $woo_ctx['args']['rewrite']['with_front'] = true; } else { $woo_ctx['args']['rewrite']['with_front'] = false;}
					if (woothemes_content_builder_checkbool($ctx['args']['rewrite']['hierarchical']) == 1) { $woo_ctx['args']['rewrite']['hierarchical'] = true; } else { $woo_ctx['args']['rewrite']['hierarchical'] = false;}
					
				} else {
					if (woothemes_content_builder_checkbool($ctx['args']['rewrite']) == 1) { $woo_ctx['args']['rewrite'] = true; } else { $woo_ctx['args']['rewrite'] = false;}
				}
				
				if (woothemes_content_builder_checkbool($ctx['args']['query_var']) == 1) { $woo_ctx['args']['query_var'] = true; } else { $woo_ctx['args']['query_var'] = false;}
				
				$woo_ctx['args']['update_count_callback'] = !empty($ctx['args']['update_count_callback']) ? $ctx['args']['update_count_callback'] : '';
				
				// Register the taxonomy
				register_taxonomy($woo_ctx['name'], $woo_ctx['object_type'],$woo_ctx['args']);
					
			}
	
		if(function_exists('flush_rewrite_rules') && isset( $_REQUEST['action'] ) && ( $_REQUEST['action'] == 'save' || $_REQUEST['action'] == 'delete' ) ) { flush_rewrite_rules(); }
    
    }
}


/*-----------------------------------------------------------------------------------*/
/* Content Builder - Options Function */
/*-----------------------------------------------------------------------------------*/

function woothemes_content_builder_options($data = array()) {
	
	$content_types_options = array();
	
	if (isset($data)) {
		if ( !is_array($data) ) 
		parse_str( $data, $data );
	
		extract($data);
	}
	
	switch ($content_type) {
	
		case 'cpt':
			
			// SET DEFAULT VALUES - ADD
			$header_text = 'Add';
			$name_text = '';
			$description_text = '';
			$label_text = '';
			$label_singular_text = '';
			$label_add_new_text = 'Add New';
			$label_add_new_item_text = 'Add New Post';
			$label_edit_item_text = 'Edit Post';
			$label_new_item_text = 'New Post';
			$label_view_item_text = 'View Post';
			$label_search_items_text = 'Search Posts';
			$label_not_found_text = 'No posts found';
			$label_not_found_in_trash_text = 'No posts found in Trash';
			$label_parent_item_colon_text = '';
			$label_search_item_text = '';
			
			$public_setting = 'true';
			$hierarchical_setting = 'false';
			$menu_position_setting = '20';
			$menu_icon_setting = '';
			$show_ui_setting = $public_setting;
			$show_in_nav_menus_setting = $public_setting;
			$features_setting = 'title,editor';
			$taxonomies_setting = '';
			$capability_type_setting = 'post';
			
			$publicly_queryable_setting = $public_setting;	
			$exclude_from_search_setting = 'false';
			$rewrite_setting = 'true';
			$rewrite_slug_setting = $name_text;
			$rewrite_with_front_setting = $public_setting;
			$query_var_setting = 'true';
			$can_export_setting = 'true';
			$has_archive_setting = 'true';
			
			$register_meta_box_cb_setting = '';
			$permalink_epmask_setting = 'EP_PERMALINK';
			
			$save_action = 'cpt';
			
			$cpt_array_index = '';
			
			// SET VALUES - EDIT
			if ($action == 'edit') {
				
				$posttype = $_REQUEST['posttype'];

				$woo_cpt_all = get_option("woo_content_builder_cpt");
				$array_index = 0;
				
				foreach ($woo_cpt_all as $key => $cpt_item) {
					if ($cpt_item['name'] == $posttype) {
						$array_index = $key;
					}
				}
				
				$cpt_array_index = $array_index;
				
				$cpt_obj = $woo_cpt_all[$array_index];
				
				$header_text = 'Edit';
				$name_text = $cpt_obj['name'];
				$description_text = $cpt_obj['args']['description'];
				$label_text = $cpt_obj['args']['label'];
				$label_singular_text = $cpt_obj['args']['labels']['singular_name'];
				$label_add_new_text = $cpt_obj['args']['labels']['add_new'];
				$label_add_new_item_text = $cpt_obj['args']['labels']['add_new_item'];
				$label_edit_item_text = $cpt_obj['args']['labels']['edit_item'];
				$label_new_item_text = $cpt_obj['args']['labels']['new_item'];
				$label_view_item_text = $cpt_obj['args']['labels']['view_item'];
				$label_search_items_text = $cpt_obj['args']['labels']['search_items'];
				$label_not_found_text = $cpt_obj['args']['labels']['not_found'];
				$label_not_found_in_trash_text = $cpt_obj['args']['labels']['not_found_in_trash'];
				$label_parent_item_colon_text = $cpt_obj['args']['labels']['parent_item_colon'];
				$label_search_item_text = $cpt_obj['args']['labels']['search_items'];
				
				if ($cpt_obj['args']['public'] == 1) { $public_setting = 'true'; } else { $public_setting = 'false'; }
				if ($cpt_obj['args']['hierarchical'] == 1) { $hierarchical_setting = 'true'; } else { $hierarchical_setting = 'false'; }
				if ($cpt_obj['args']['menu_position'] > 0) { $menu_position_setting = $cpt_obj['args']['menu_position']; } else { $menu_position_setting = '20'; }
				if ($cpt_obj['args']['menu_icon'] != '') { $menu_icon_setting = $cpt_obj['args']['menu_icon']; } else { $menu_icon_setting = ''; }
				if ($cpt_obj['args']['show_ui'] == 1) { $show_ui_setting = 'true'; } else { $show_ui_setting = $public_setting; }
				if ($cpt_obj['args']['show_in_nav_menus'] == 1) { $show_in_nav_menus_setting = 'true'; } else { $show_in_nav_menus_setting = $public_setting; }
				
				$counter = 0;
				if ( isset($cpt_obj['args']['supports']) && is_array($cpt_obj['args']['supports']) ) {
					foreach($cpt_obj['args']['supports'] as $key => $support_item) {
						if ($counter == 0) {
							$features_setting = $support_item;
						} else {
							$features_setting .= ','.$support_item;
						}
						$counter++;
					} // End For Loop
				} // End If Statement
				
				$counter = 0;
				if ( isset($cpt_obj['args']['taxonomies']) && is_array($cpt_obj['args']['taxonomies']) ) {
					foreach($cpt_obj['args']['taxonomies'] as $key => $taxonomy_item) {
						if ($counter == 0) {
							$taxonomies_setting = $taxonomy_item;
						} else {
							$taxonomies_setting .= ','.$taxonomy_item;
						}
						$counter++;
					} // End For Loop
				} // End If Statement
				
				if ($cpt_obj['args']['capability_type'] != '') { $capability_type_setting = $cpt_obj['args']['capability_type']; } else { $capability_type_setting = 'post'; }
				if ($cpt_obj['args']['publicly_queryable'] == 1) { $publicly_queryable_setting = 'true'; } else { $publicly_queryable_setting = $public_setting; }	
				if ($cpt_obj['args']['exclude_from_search'] == 1) { $exclude_from_search_setting = 'true'; } else { $exclude_from_search_setting = 'false'; }	
				if ($cpt_obj['args']['rewrite'] == 0) { $rewrite_setting = 'false'; } else { $rewrite_setting = 'true'; }
				if($rewrite_setting == 'true'){
					if ($cpt_obj['args']['rewrite']['slug'] == '') { $rewrite_slug_setting = $name_text; } else { $rewrite_slug_setting = $cpt_obj['args']['rewrite']['slug']; }
					if ($cpt_obj['args']['rewrite']['with_front'] == 1) { $rewrite_with_front_setting = 'true'; } else { $rewrite_with_front_setting = $public_setting; }
				} else {
					$rewrite_slug_setting = $name_text;
					$rewrite_with_front_setting = $public_setting;
				}
				
				if ($cpt_obj['args']['query_var'] == 0) { $query_var_setting = 'false'; } else { $query_var_setting = 'true'; }
				if ($cpt_obj['args']['can_export'] == 0) { $can_export_setting = 'false'; } else { $can_export_setting = 'true'; }
				if ($cpt_obj['args']['has_archive'] == 0) { $has_archive_setting = 'false'; } else { $has_archive_setting = 'true'; }
				if ($cpt_obj['args']['register_meta_box_cb'] != '') { $register_meta_box_cb_setting = $cpt_obj['args']['register_meta_box_cb']; } else { $register_meta_box_cb_setting = ''; }
				if ($cpt_obj['args']['permalink_epmask'] != '') { $permalink_epmask_setting = $cpt_obj['args']['permalink_epmask']; } else { $permalink_epmask_setting = 'EP_PERMALINK'; }
					
			} 
			
			// SET OPTIONS ARRAY
			$content_types_options[] = array( "name" => $header_text." Custom Post Type",
					"icon" => "misc",
					"type" => "heading");
					
			$content_types_options[] = array( "name" => "Name",
					"desc" => "A general name for the post type, usually plural. Also, use only lower case letters and no special characters except for the underscore.",
					"id" => $shortname."_cpt_post_type",
					"std" => $name_text,
					"type" => "text");
					
			$content_types_options[] = array( "name" => "Description",
					"desc" => "Description of your Custom Post Type.",
					"id" => $shortname."_cpt_description",
					"std" => $description_text,
					"type" => "textarea");
	
			$content_types_options[] = array( "name" => "Plural Name",
					"desc" => "This will be the plural output when your Custom Post Types name is displayed.",
					"id" => $shortname."_cpt_label",
					"std" => $label_text,
					"type" => "text");
	
			$content_types_options[] = array( "name" => "Singular Name",
					"desc" => "This will be the singular output when your Custom Post Types name is displayed.",
					"id" => $shortname."_cpt_singular_name",
					"std" => $label_singular_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Advanced: Labels",
					"icon" => "misc",
					"type" => "heading");
			
			$content_types_options[] = array( "name" => "Please Read",
					"type" => "info",
					"std" => "<small>These are advanced options for creating a Custom Post Type. Do not alter these unless you have experience working with Custom Post Types</small>");
					
			$content_types_options[] = array( "name" => "Add New Text",
					"desc" => "The add new text. The default is Add New for both hierarchical and non-hierarchical types.",
					"id" => $shortname."_cpt_add_new",
					"std" => $label_add_new_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Add New Item Text",
					"desc" => "The add new item text. Default is Add New Post/Add New Page.",
					"id" => $shortname."_cpt_add_new_item",
					"std" => $label_add_new_item_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Edit Item Text",
					"desc" => "The edit item text. Default is Edit Post/Edit Page.",
					"id" => $shortname."_cpt_edit_item",
					"std" => $label_edit_item_text,
					"type" => "text");
					
			$content_types_options[] = array( "name" => "New Item Text",
					"desc" => "The new item text. Default is New Post/New Page.",
					"id" => $shortname."_cpt_new_item",
					"std" => $label_new_item_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "View Item Text",
					"desc" => "The view item text. Default is View Post/View Page.",
					"id" => $shortname."_cpt_view_item",
					"std" => $label_view_item_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Search Items Text",
					"desc" => "The search items text. Default is Search Posts/Search Pages.",
					"id" => $shortname."_cpt_search_items",
					"std" => $label_search_items_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Not Found Text",
					"desc" => "The not found text. Default is No posts found/No pages found.",
					"id" => $shortname."_cpt_not_found",
					"std" => $label_not_found_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Not Found in Trash Text",
					"desc" => "The not found in trash text. Default is No posts found in Trash/No pages found in Trash.",
					"id" => $shortname."_cpt_not_found_in_trash",
					"std" => $label_not_found_in_trash_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Parent Item with Colon Text",
					"desc" => "The parent text. This string isn't used on non-hierarchical types. In hierarchical ones the default is Parent Page:",
					"id" => $shortname."_cpt_parent_item_colon",
					"std" => $label_parent_item_colon_text,
					"type" => "text");
							
			$content_types_options[] = array( "name" => "Advanced: UI",
					"icon" => "misc",
					"type" => "heading");
			
			$content_types_options[] = array( "name" => "Please Read",
					"type" => "info",
					"std" => "<small>These are advanced options for creating a Custom Post Type. Do not alter these unless you have experience working with Custom Post Types</small>");
			
			$content_types_options[] = array( "name" => "Public",
					"desc" => "Meta argument used to define default values for publicly_queriable, show_ui, show_in_nav_menus and exclude_from_search.",
					"id" => $shortname."_cpt_public",
					"std" => $public_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");
			
			$content_types_options[] = array( "name" => "Hierarchical",
					"desc" => "Whether the post type is hierarchical. Allows Parent to be specified.",
					"id" => $shortname."_cpt_hierarchical",
					"std" => $hierarchical_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");		
			
			$content_types_options[] = array( "name" => "Menu Position",
					"desc" => "The position in the menu order the post type should appear.",
					"id" => $shortname."_cpt_menu_position",
					"std" => $menu_position_setting,
					"options" => array(	'5' => 'Below Posts',
										'10' => 'Below Media',
										'20' => 'Below Pages',
										'60' => 'Below First Separator',
										'100' => 'Below Second Separator'),
					"type" => "select2");
			
			$content_types_options[] = array( "name" => "Menu Icon",
					"desc" => "Enter the URL for the menu icon.",
					"id" => $shortname."_cpt_menu_icon",
					"std" => $menu_icon_setting,
					"type" => "upload");
			
			$content_types_options[] = array( "name" => "Show UI",
					"desc" => "Whether to generate a default UI for managing this post type.",
					"id" => $shortname."_cpt_show_ui",
					"std" => $show_ui_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");
			
			$content_types_options[] = array( "name" => "Show in Navigation Menus",
					"desc" => "Whether post_type is available for selection in navigation menus.",
					"id" => $shortname."_cpt_show_in_nav_menus",
					"std" => $show_in_nav_menus_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");
									
			$content_types_options[] = array( "name" => "Advanced: Features",
					"icon" => "misc",
					"type" => "heading");
			
			$content_types_options[] = array( "name" => "Please Read",
					"type" => "info",
					"std" => "<small>These are advanced options for creating a Custom Post Type. Do not alter these unless you have experience working with Custom Post Types</small>");
															
			$content_types_options[] = array( "name" => "Supported Features",
					"desc" => "Select which archives to index on your site. Aids in removing duplicate content from being indexed, preventing content dilution.",
					"id" => $shortname."_cpt_features",
					"std" => $features_setting,
					"type" => "multicheck2",
					"options" => $post_features); 
			
			$content_types_options[] = array( "name" => "Assigned Taxonomies",
					"desc" => "Select which archives to index on your site. Aids in removing duplicate content from being indexed, preventing content dilution.",
					"id" => $shortname."_cpt_taxonomies",
					"std" => $taxonomies_setting,
					"type" => "multicheck2",
					"options" => $woo_wp_custom_taxonomies_formatted); 
			
						
			$content_types_options[] = array( "name" => "Advanced: Other",
					"icon" => "misc",
					"type" => "heading");
			
			$content_types_options[] = array( "name" => "Please Read",
					"type" => "info",
					"std" => "<small>These are advanced options for creating a Custom Post Type. Do not alter these unless you have experience working with Custom Post Types</small>");
			
			$content_types_options[] = array( "name" => "Capability Type",
					"desc" => "The post type to use for checking read, edit, and delete capabilities.",
					"id" => $shortname."_cpt_capability_type",
					"std" => $capability_type_setting,
					"type" => "text");
					
			$content_types_options[] = array( "name" => "Publicly Queryable",
					"desc" => "Whether post_type queries can be performed from the front end.",
					"id" => $shortname."_cpt_publicly_queryable",
					"std" => $publicly_queryable_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");
			
			$content_types_options[] = array( "name" => "Exclude from Search",
					"desc" => "Whether to exclude posts with this post type from search results.",
					"id" => $shortname."_cpt_exclude_from_search",
					"std" => $exclude_from_search_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");
			
			$content_types_options[] = array( "name" => "Rewrite",
					"desc" => "Change the position where the paged variable will appear.",
					"id" => $shortname."_cpt_rewrite",
					"std" => $rewrite_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");
			
			$content_types_options[] = array( "name" => "Rewrite Slug",
					"desc" => "This must be a unique name, not used by any other default or custom post type. Also, use only lower case letters and no special characters except for the underscore.",
					"id" => $shortname."_cpt_rewrite_slug",
					"std" => $rewrite_slug_setting,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Rewrite with Front",
					"desc" => "Change the position where the paged variable will appear.",
					"id" => $shortname."_cpt_rewrite_with_front",
					"std" => $rewrite_with_front_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");
									
			$content_types_options[] = array( "name" => "Query Variable",
					"desc" => "Name of the query var to use for this post type.",
					"id" => $shortname."_cpt_query_var",
					"std" => $query_var_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");
			
			$content_types_options[] = array( "name" => "Can be Exported",
					"desc" => "Can this post_type be exported.",
					"id" => $shortname."_cpt_can_export",
					"std" => $can_export_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");
					
			$content_types_options[] = array( "name" => "Has Archive",
					"desc" => "Allows this custom post type to have an archive page. <strong>You must then create the file archive-postypename.php in order for a specific post type archive page</strong>, otherwise WP will default to archive.php for output.",
					"id" => $shortname."_cpt_has_archive",
					"std" => $has_archive_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");	
			
			$content_types_options[] = array( "name" => "Register Meta Box Callback",
					"desc" => "Provide a callback function that will be called when setting up the meta boxes for the edit form.",
					"id" => $shortname."_cpt_register_meta_box_cb",
					"std" => $register_meta_box_cb_setting,
					"type" => "text");
				
			$content_types_options[] = array( "name" => "Permalink EP Mask",
					"desc" => "The default rewrite endpoint bitmasks.",
					"id" => $shortname."_cpt_permalink_epmask",
					"std" => $permalink_epmask_setting,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Array ID",
					"desc" => "Array ID.",
					"id" => $shortname."_cpt_array_index",
					"std" => $cpt_array_index,
					"class" => "hidden",
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Save Action",
					"desc" => "Save Action.",
					"id" => $shortname."_save_action",
					"std" => $save_action,
					"class" => "hidden",
					"type" => "text");
									
			break;
			
		case 'ctx' :
		
			// SET DEFAULT VALUES - ADD
			$header_text = 'Add';
			
			$name_text = '';
			$label_text = '';
			$label_singular_text = '';
			
			$label_search_items_text = 'Search Items';
			$label_popular_items_text = 'Popular Items';
			$label_all_items_text = 'All Items';
			$label_parent_item_text = 'Parent Item';
			$label_parent_item_with_colon_text = 'Parent Item:';
			$label_edit_item_text = 'Edit Item';
			$label_update_item_text = 'Update Item';
			$label_add_new_item_text = 'Add New Item';
			$label_new_item_name_text = 'New Item Name';
			$label_separate_items_with_commas_text = 'Separate items with commas';
			$label_add_or_remove_items = 'Add or remove items';
			$label_choose_from_most_used = 'Choose from the most used items';
			
			$public_setting = 'true';
			$show_in_nav_menus_setting = $public_setting;
			$show_ui_setting = $public_setting;
			$show_tag_cloud_setting = $show_ui_setting;
			$hierarchical_setting = 'false';
			$update_count_callback_setting = '';
			$rewrite_setting = 'true';
			$rewrite_slug_setting = $name_text;
			$rewrite_with_front_setting = $public_setting;
			$rewrite_hierarchical = 'false';
			$query_var_setting = 'true';
			
			$custom_post_types_setting = 'post';
			
			$save_action = 'ctx';
			
			$ctx_array_index = '';
			
			// SET VALUES - EDIT
			if ($action == 'edit') {
				
				$taxonomy_name = $_REQUEST['taxonomyname'];
				
				$woo_ctx_all = get_option("woo_content_builder_ctx");
				$array_index = 0;
				
				foreach ($woo_ctx_all as $key => $ctx_item) {
					if ($ctx_item['name'] == $taxonomy_name) {
						$array_index = $key;
					}
				}
				
				$ctx_array_index = $array_index;
				
				$ctx_obj = $woo_ctx_all[$array_index];
				
				$header_text = 'Edit';
				
				$name_text = $ctx_obj['name'];
				$label_text = $ctx_obj['args']['labels']['name'];
				$label_singular_text = $ctx_obj['args']['labels']['singular_name'];
				
				$label_search_items_text = $ctx_obj['args']['labels']['search_items'];
				$label_popular_items_text = $ctx_obj['args']['labels']['popular_items'];
				$label_all_items_text = $ctx_obj['args']['labels']['all_items'];
				$label_parent_item_text = $ctx_obj['args']['labels']['parent_item'];
				$label_parent_item_with_colon_text = $ctx_obj['args']['labels']['parent_item_colon'];
				$label_edit_item_text = $ctx_obj['args']['labels']['edit_item'];
				$label_update_item_text = $ctx_obj['args']['labels']['update_item'];
				$label_add_new_item_text = $ctx_obj['args']['labels']['add_new_item'];
				$label_new_item_name_text = $ctx_obj['args']['labels']['new_item_name'];
				$label_separate_items_with_commas_text = $ctx_obj['args']['labels']['separate_items_with_commas'];
				$label_add_or_remove_items = $ctx_obj['args']['labels']['add_or_remove_items'];
				$label_choose_from_most_used = $ctx_obj['args']['labels']['choose_from_most_used'];
				
				if ($ctx_obj['args']['public'] == 1) { $public_setting = 'true'; } else { $public_setting = 'false'; }
				if ($ctx_obj['args']['hierarchical'] == 1) { $hierarchical_setting = 'true'; } else { $hierarchical_setting = 'false'; }
				if ($ctx_obj['args']['show_ui'] == 1) { $show_ui_setting = 'true'; } else { $show_ui_setting = $public_setting; }
				if ($ctx_obj['args']['show_in_nav_menus'] == 1) { $show_in_nav_menus_setting = 'true'; } else { $show_in_nav_menus_setting = $public_setting; }
				if ($ctx_obj['args']['show_tagcloud'] == 1) { $show_tag_cloud_setting = 'true'; } else { $show_tag_cloud_setting = $show_ui_setting; }
				
				if ($ctx_obj['args']['rewrite'] == 0) { $rewrite_setting = 'false'; } else { $rewrite_setting = 'true'; }
				if($rewrite_setting == 'true'){
					if ($ctx_obj['args']['rewrite']['slug'] == '') { $rewrite_slug_setting = $name_text; } else { $rewrite_slug_setting = $ctx_obj['args']['rewrite']['slug']; }
					if ($ctx_obj['args']['rewrite']['with_front'] == 1) { $rewrite_with_front_setting = 'true'; } else { $rewrite_with_front_setting = $public_setting; }
					if ($ctx_obj['args']['rewrite']['hierarchical'] == 1) { $rewrite_hierarchical = 'true'; } else { $rewrite_hierarchical = 'false'; }
				} else {
					$rewrite_slug_setting = $name_text;
					$rewrite_with_front_setting = $public_setting;
					$rewrite_hierarchical = 'false';
				}

				$update_count_callback_setting = $ctx_obj['args']['update_count_callback'];
				
				if ($ctx_obj['args']['query_var'] == 0) { $query_var_setting = 'false'; } else { $query_var_setting = 'true'; }
				
				$counter = 0;
				if ( isset($ctx_obj['object_type']) && is_array($ctx_obj['object_type']) ) {
					foreach($ctx_obj['object_type'] as $key => $object_type) {
						if ($counter == 0) {
							$custom_post_types_setting = $object_type;
						} else {
							$custom_post_types_setting .= ','.$object_type;
						}
						$counter++;
					} // End For Loop
				} // End If Statement
				
			} 
			
			// SET OPTIONS ARRAY
			$content_types_options[] = array( "name" => $header_text." Custom Taxonomy",
					"icon" => "misc",
					"type" => "heading");
					
			$content_types_options[] = array( "name" => "Name",
					"desc" => "A general name for the taxonomy, usually plural. Also, use only lower case letters and no special characters except for the underscore.",
					"id" => $shortname."_ctx_taxonomy",
					"std" => $name_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Assigned Post Types",
					"desc" => "Select which post types you would like to assign this custom taxonomy to.",
					"id" => $shortname."_ctx_post_types",
					"std" => $custom_post_types_setting,
					"type" => "multicheck2",
					"options" => $woo_wp_custom_post_types_formatted); 
							
			$content_types_options[] = array( "name" => "Plural Name",
					"desc" => "This will be the plural output when your Custom Taxonomy name is displayed.",
					"id" => $shortname."_ctx_label",
					"std" => $label_text,
					"type" => "text");
	
			$content_types_options[] = array( "name" => "Singular Name",
					"desc" => "This will be the singular output when your Custom Taxonomy name is displayed.",
					"id" => $shortname."_ctx_singular_name",
					"std" => $label_singular_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Advanced: Labels",
					"icon" => "misc",
					"type" => "heading");
			
			$content_types_options[] = array( "name" => "Please Read",
					"type" => "info",
					"std" => "<small>These are advanced options for creating a Custom Taxonomy. Do not alter these unless you have experience working with Custom Taxonomies</small>");
					
			$content_types_options[] = array( "name" => "Search Items",
					"desc" => "The search items text. The default is Search Items for both hierarchical and non-hierarchical types.",
					"id" => $shortname."_ctx_search_items",
					"std" => $label_search_items_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Popular Items",
					"desc" => "The popular items text. The default is Popular Items.",
					"id" => $shortname."_ctx_popular_items",
					"std" => $label_popular_items_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "All Items",
					"desc" => "The all items text. The default is All Items.",
					"id" => $shortname."_ctx_all_items",
					"std" => $label_all_items_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Parent Item",
					"desc" => "The parent item text. The default is Parent Item.",
					"id" => $shortname."_ctx_parent_item",
					"std" => $label_parent_item_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Parent Item with Colon",
					"desc" => "The parent item with colon text. The default is Parent Item:.",
					"id" => $shortname."_ctx_parent_item_with_colon",
					"std" => $label_parent_item_with_colon_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Edit Item",
					"desc" => "The edit item text. The default is Edit Item.",
					"id" => $shortname."_ctx_edit_item",
					"std" => $label_edit_item_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Update Item",
					"desc" => "The update item text. The default is Update Item.",
					"id" => $shortname."_ctx_update_item",
					"std" => $label_update_item_text,
					"type" => "text");
					
			$content_types_options[] = array( "name" => "Add New Item",
					"desc" => "The add new item text. The default is Add New Item.",
					"id" => $shortname."_ctx_add_new_item",
					"std" => $label_add_new_item_text,
					"type" => "text");
					
			$content_types_options[] = array( "name" => "New Item Name",
					"desc" => "The new item name text. The default is New Item Name.",
					"id" => $shortname."_ctx_new_item_name",
					"std" => $label_new_item_name_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Separate Items with Commas",
					"desc" => "The separate items with commas text. The default is Separate items with commas.",
					"id" => $shortname."_ctx_separate_items_with_commas",
					"std" => $label_separate_items_with_commas_text,
					"type" => "text");
					
			$content_types_options[] = array( "name" => "Add or Remove Items",
					"desc" => "The add or remove items text. The default is Add or remove items.",
					"id" => $shortname."_ctx_add_or_remove_items",
					"std" => $label_add_or_remove_items,
					"type" => "text");
					
			$content_types_options[] = array( "name" => "Choose from the most used Items",
					"desc" => "The choose from the most used items text. The default is Choose from the most used items.",
					"id" => $shortname."_ctx_choose_from_the_most_used_items",
					"std" => $label_choose_from_most_used,
					"type" => "text");
											
			$content_types_options[] = array( "name" => "Advanced: UI",
					"icon" => "misc",
					"type" => "heading");
			
			$content_types_options[] = array( "name" => "Please Read",
					"type" => "info",
					"std" => "<small>These are advanced options for creating a Custom Taxonomy. Do not alter these unless you have experience working with Custom Taxonomies</small>");
			
			$content_types_options[] = array( "name" => "Public",
					"desc" => "Meta argument used to define default values for publicly_queriable, show_ui, show_in_nav_menus and exclude_from_search.",
					"id" => $shortname."_ctx_public",
					"std" => $public_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");
			
			$content_types_options[] = array( "name" => "Hierarchical",
					"desc" => "Whether the post type is hierarchical. Allows Parent to be specified.",
					"id" => $shortname."_ctx_hierarchical",
					"std" => $hierarchical_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");		
			
			$content_types_options[] = array( "name" => "Show UI",
					"desc" => "Whether to generate a default UI for managing this post type.",
					"id" => $shortname."_ctx_show_ui",
					"std" => $show_ui_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");
			
			$content_types_options[] = array( "name" => "Show in Navigation Menus",
					"desc" => "Whether post_type is available for selection in navigation menus.",
					"id" => $shortname."_ctx_show_in_nav_menus",
					"std" => $show_in_nav_menus_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");
			
			$content_types_options[] = array( "name" => "Show Tag Cloud",
					"desc" => "Whether to allow the Tag Cloud widget to use this taxonomy.",
					"id" => $shortname."_ctx_show_tag_cloud",
					"std" => $show_tag_cloud_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");
											
			$content_types_options[] = array( "name" => "Advanced: Other",
					"icon" => "misc",
					"type" => "heading");
			
			$content_types_options[] = array( "name" => "Please Read",
					"type" => "info",
					"std" => "<small>These are advanced options for creating a Custom Taxonomy. Do not alter these unless you have experience working with Custom Taxonomies</small>");
			
			
			$content_types_options[] = array( "name" => "Rewrite",
					"desc" => "Change the position where the paged variable will appear.",
					"id" => $shortname."_ctx_rewrite",
					"std" => $rewrite_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");
			
			$content_types_options[] = array( "name" => "Rewrite Slug",
					"desc" => "This must be a unique name, not used by any other default or custom post type. Also, use only lower case letters and no special characters except for the underscore.",
					"id" => $shortname."_ctx_rewrite_slug",
					"std" => $rewrite_slug_setting,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Rewrite with Front",
					"desc" => "Change the position where the paged variable will appear.",
					"id" => $shortname."_ctx_rewrite_with_front",
					"std" => $rewrite_with_front_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");
			
			$content_types_options[] = array( "name" => "Hierarchical URLs",
					"desc" => "Allow hierarchical urls.",
					"id" => $shortname."_ctx_rewrite_hierarchical",
					"std" => $rewrite_hierarchical,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");
											
			$content_types_options[] = array( "name" => "Query Variable",
					"desc" => "Name of the query var to use for this post type.",
					"id" => $shortname."_ctx_query_var",
					"std" => $query_var_setting,
					"options" => array(	'true' => 'Yes',
										'false' => 'No'),
					"type" => "select2");
			
			$content_types_options[] = array( "name" => "Update Count Callback",
					"desc" => "A function name that will be called to update the count of an associated object_type, such as post, is updated.",
					"id" => $shortname."_ctx_update_count_callback",
					"std" => $update_count_callback_setting,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Array ID",
					"desc" => "Array ID.",
					"id" => $shortname."_ctx_array_index",
					"std" => $ctx_array_index,
					"class" => "hidden",
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Save Action",
					"desc" => "Save Action.",
					"id" => $shortname."_save_action",
					"std" => $save_action,
					"class" => "hidden",
					"type" => "text");		
					
			break;
			
		case 'cmb' :
			
			// SET DEFAULT VALUES - ADD
			$header_text = 'Add';
			
			$name_text = '';
			$label_text = '';
			$std_text = '';
			$type_setting = 'text';
			$desc_text = '';
			$options_setting = '';
			
			$object_types = 'post';
			$builtin = 'false';
			
			$save_action = 'cmb';
			
			// Get the content type for this custom field. // TO DO - get this from the stored data.
			$cmb_contenttype = $_GET['contenttype'];
			
			// Set to "cpt" by default if no value is present (like on the "Add" screen).
			if ( ! isset( $_GET['contenttype'] ) ) {
			
				$cmb_contenttype = 'cpt';
			
			} // End IF Statement
			
			// SET VALUES - EDIT
			if ($action == 'edit') {
				
				$custom_field_name = $_REQUEST['customfieldname'];
				$woo_cmb_all = get_option("woo_custom_template");
				$array_index = 0;
				
				foreach ($woo_cmb_all as $key => $cmb_item) {
					if ($cmb_item['name'] == $custom_field_name) {
						$array_index = $key;
					}
				}
				
				$cmb_array_index = $array_index;
				
				$cmb_obj = $woo_cmb_all[$array_index];
				
				$header_text = 'Edit';
				
				$name_text = $cmb_obj['name'];
				$label_text = $cmb_obj['label'];
				$type_setting = $cmb_obj['type'];
				$desc_text = $cmb_obj['desc'];
				$std_text = $cmb_obj['std'];
				
				$options_setting_raw = $cmb_obj['options'];
				$counter = 0;
				if ( isset($options_setting_raw) && is_array($options_setting_raw) ) {
					foreach ($options_setting_raw as $key => $value) {
						if ($counter == 0) {
							$options_setting = $value;
						} else {
							$options_setting .= $value."\n";
						}
						$counter++;
						
					} // End For Loop
				} // End If Statement
				
				$counter = 0;
				if ( isset($cmb_obj['cpt']) && is_array($cmb_obj['cpt']) ) {
					foreach($cmb_obj['cpt'] as $key => $object_type) {
						if ($counter == 0) {
							$object_types = $key;
						} else {
							$object_types .= ','.$key;
						}
						$counter++;
					} // End For Loop
				} // End If Statement
				
				// Add support for ctx on custom fields as well.
				
				$counter = 0;
				$object_taxonomies = '';
				
				if ( array_key_exists( 'ctx', $cmb_obj ) ) {
				
					$object_taxonomies = join( ',', array_keys( $cmb_obj['ctx'] ) );
					
					/*
					foreach($cmb_obj['ctx'] as $key => $object_type) {
						if ($counter == 0) {
							$object_taxonomies = $key;
						} else {
							$object_taxonomies .= ','.$key;
						}
						$counter++;
					}
					*/
				
				} // End IF Statement
				
				// Get the content type for this custom field. // TO DO - get this from the stored data.
				$cmb_contenttype = $_GET['contenttype'];
				
				// Set to "cpt" by default if no value is present (like on the "Add" screen).
				if ( ! isset( $_GET['contenttype'] ) ) {
				
					$cmb_contenttype = 'cpt';
				
				} // End IF Statement
				
			} 
			
			// SET OPTIONS ARRAY
			$content_types_options[] = array( "name" => $header_text." Custom Field",
					"icon" => "misc",
					"type" => "heading");
			
			$content_types_options[] = array( "name" => "Field Type",
					"desc" => "This is the type of input field that will be shown to the user.",
					"id" => $shortname."_cmb_type",
					"std" => $type_setting,
					"options" => array(	'text' => 'Text box',
										'textarea' => 'Text Area',
										'select2' => 'Drop down box',
										'checkbox' => 'Check box',
										'upload' => 'File Upload',
										'radio' => 'Radio Buttons',
										'calendar' => 'Datepicker Calendar',
										'timestamp' => 'Timestamp',
										'time' => 'Time Input',
										'info' => 'Information Notice', 
										'googlemap' => 'Google Maps'),
					"type" => "select2");
			
			$content_types_options[] = array( "name" => "Warning",
					"type" => "info",
					"class" => "hidden",
					"std" => "<small><strong>Do not create more than one custom field for Google Maps as only one is allowed.</strong> <br/> If more than one exists, the Content Builder will simply apply the latest Google Map custom field settings to the theme.<br /><br />Please also note that Google Maps fields will be treated as \"Text box\" fields when used on Taxonomies.</small>");
									
			$content_types_options[] = array( "name" => "Name",
					"desc" => "A general name for the custom field, usually plural. Also, use only lower case letters and no special characters except for the underscore.",
					"id" => $shortname."_cmb_name",
					"std" => $name_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Label",
					"desc" => "A label for the custom field, that will be output next to the input field.",
					"id" => $shortname."_cmb_label",
					"std" => $label_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Default Text",
					"desc" => "The default text of the custom field.",
					"id" => $shortname."_cmb_std",
					"std" => $std_text,
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Description",
					"desc" => "A description for the custom field that will be output to help the user understand what the custom field is for. This paragraph of text is an example of where the output will be.",
					"id" => $shortname."_cmb_description",
					"std" => $desc_text,
					"type" => "textarea");
									
			$content_types_options[] = array( "name" => "Options",
					"desc" => "A list of values that will be shown in the input field.",
					"id" => $shortname."_cmb_options",
					"std" => $options_setting,
					"class" => "hidden",
					"type" => "textarea");
			
			$content_types_options[] = array( "name" => "Content Type(s)",
					"icon" => "misc",
					"type" => "heading");
			
			$content_types_options[] = array( "name" => "Select Content Type",
					"desc" => "Choose which content type you would like to use the custom field for.",
					"id" => $shortname."_cmb_contenttype",
					"std" => $cmb_contenttype,
					"options" => array( 'cpt' => 'Custom Post Types', 'ctx' => 'Taxonomies' ),
					"type" => "radio");
					
			$content_types_options[] = array( "name" => "Post Types",
					"desc" => "Choose which Post Types you would like to use the custom field for.",
					"id" => $shortname."_cmb_cpt",
					"std" => $object_types,
					"options" => $woo_wp_custom_post_types_formatted,
					"type" => "multicheck2");
																		
			$content_types_options[] = array( "name" => "Array ID",
					"desc" => "Array ID.",
					"id" => $shortname."_cmb_array_index",
					"std" => $cmb_array_index,
					"class" => "hidden",
					"type" => "text");
			
			$content_types_options[] = array( "name" => "Taxonomies",
					"desc" => "Choose which Taxonomies you would like to use the custom field for.",
					"id" => $shortname."_cmb_ctx",
					"std" => $object_taxonomies,
					"options" => $woo_wp_custom_taxonomies_formatted,
					"type" => "multicheck2");
																		
			$content_types_options[] = array( "name" => "Array ID",
					"desc" => "Array ID.",
					"id" => $shortname."_cmb_array_index_ctx",
					"std" => $cmb_array_index,
					"class" => "hidden",
					"type" => "text");
					
			$content_types_options[] = array( "name" => "Save Action",
					"desc" => "Save Action.",
					"id" => $shortname."_save_action",
					"std" => $save_action,
					"class" => "hidden",
					"type" => "text");
					
			break;
			
		default :
			
			$content_types_options[] = array( "name" => "Custom Post Types",
					"icon" => "misc",
					"type" => "heading");
					
			$content_types_options[] = array( "name" => "Custom Taxonomies",
					"icon" => "misc",
					"type" => "heading");
			
			$content_types_options[] = array( "name" => "Custom Fields",
					"icon" => "misc",
					"type" => "heading");
					
			break;
	}
	
	return $content_types_options;
			
}

/*-----------------------------------------------------------------------------------*/
/* Content Builder - Helper Functions */
/*-----------------------------------------------------------------------------------*/

if ( !function_exists( 'woothemes_content_builder_checkbool' ) ) { 
	function woothemes_content_builder_checkbool($string) {
		if ( $string == 'false' || $string == '' )
			return 0;
		else
			return 1;
	}
}

function woothemes_content_builder_array_remove_empty($arr){
    $narr = array();
    while(list($key, $val) = each($arr)){
        if (is_array($val)){
            $val = array_remove_empty($val);
            // does the result array contain anything?
            if (count($val)!=0){
                // yes :-)
                $narr[$key] = $val;
            }
        }
        else {
            if (trim($val) != ""){
                $narr[$key] = $val;
            }
        }
    }
    unset($arr);
    return $narr;
}

function woothemes_content_builder_item_exists() {

	$item_string = $_POST['itemstring'];
	$content_type = $_POST['contenttype'];
	
	$success = false;
	if ($item_string != '' && $content_type != '') {
		switch ($content_type) {
			case 'cpt' :
				$success = post_type_exists($item_string);
				break;
			case 'ctx' :
				$success = taxonomy_exists($item_string);
				break;
			case 'cmb' :
				$woo_cmb_all = get_option('woo_custom_template');
				$array_index = 0;
				foreach ($woo_cmb_all as $key => $cmb_item) {
					if ($cmb_item['name'] == $item_string) {
						$array_index = $key;
						$success = true;
					}
				}
				break;
			default :
				$success = false;	
				break;
		}
	} else {
		$success = false;
	}
	
	die($success);

}

function woothemes_content_builder_ajax_javascript() {

	$element_name = '';
	
	if ( isset( $_REQUEST['content'] ) ) {
		
		switch ($_REQUEST['content']) {
			case 'cpt' :
				$element_name = '#woo_content_builder_cpt_post_type';
				break;
			case 'ctx' :
				$element_name = '#woo_content_builder_ctx_taxonomy';
				break;
			case 'cmb' :
				$element_name = '#woo_content_builder_cmb_name';
				break;
			
		} // End Switch Statement
		
	} // End If Statement
	

?>
<script type="text/javascript" >
jQuery(document).ready(function($) {
	
	<?php if ( ( isset( $_GET['page'] ) ) && ( $_GET['page'] == 'woothemes_content_builder' ) ) { ?>
	// Remove all items with the class "updated".
	jQuery('.updated, .error').remove();
	
	// Move all "woo-sc-box" elements to inside "#content".
	var messageBox = jQuery('#message').remove();
	messageBox.prependTo('#content');
	<?php } ?>
	
	// Add confirmation dialog box to all "delete" links.
	jQuery('body.listings_page_woothemes_content_builder a.delete').click( function () {
	
		var is_deleting = confirm( 'Are you sure you want to delete this entry?' );
		
		if ( is_deleting == true ) {} else {
		
			return false;
		
		} // End IF Statement
	
	});

	// Remove the "name" field from the custom taxonomy "edit" screen
	// in the content builder. This prevents the "loss" of data if the
	// user happens to change this value.
	
	// TO DO: Add a "hidden" field type to woothemes_machine().
	
	if ( jQuery('input[name="woo_content_builder_ctx_taxonomy"]').length ) {
	
		var taxonomyLength = jQuery('input[name="woo_content_builder_ctx_taxonomy"]').val().length;
		
		if ( taxonomyLength > 0 ) {
		
			jQuery('input[name="woo_content_builder_ctx_taxonomy"]').parents( '.section' ).hide();
			
		} // End IF Statement
	
	} // End IF Statement

	<?php if ( $element_name != '' ) { ?>
		
		jQuery('<?php echo $element_name; ?>').keyup(function(){
		
			var elementval = '';
			var contenttypepost = '<?php echo $_REQUEST['content']; ?>';
		
			switch (contenttypepost) {
				case 'cpt' :
					elementval = jQuery('#woo_content_builder_cpt_post_type').val().toLowerCase();
					break;
				case 'ctx' :
					elementval = jQuery('#woo_content_builder_ctx_taxonomy').val().toLowerCase();
					break;
				case 'cmb' :
					elementval = jQuery('#woo_content_builder_cmb_name').val().toLowerCase();
					break;
			}
			
			var data = {
				action: 'woothemes_content_builder_check_for_existing_custom_content',
				itemstring: elementval,
				contenttype: contenttypepost
			};
		
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				//alert('Got this from the server: ' + response);
				//console.log(response);
				if (response == 1) {
					//already exists - WARN THE USER
					switch (data['contenttype']) {
						case 'cpt' :
							jQuery('#woo_content_builder_cpt_post_type').parent().parent().find('div.explain').html('<strong style="color: #CC7777;">THIS CUSTOM POST TYPE ALREADY EXISTS, PLEASE CHOOSE ANOTHER NAME BEFORE SAVING.</strong>');
							break;
						case 'ctx' :
							jQuery('#woo_content_builder_ctx_taxonomy').parent().parent().find('div.explain').html('<strong style="color: #CC7777;">THIS CUSTOM TAXONOMY ALREADY EXISTS, PLEASE CHOOSE ANOTHER NAME BEFORE SAVING.</strong>');
							break;
						case 'cmb' :
							jQuery('#woo_content_builder_cmb_name').parent().parent().find('div.explain').html('<strong style="color: #CC7777;">THIS CUSTOM FIELD ALREADY EXISTS, PLEASE CHOOSE ANOTHER NAME BEFORE SAVING.</strong>');
							break;
					}
					jQuery('#wooform-content-builder').find('input.submit-button').addClass('hidden');
				} else {
					switch (data['contenttype']) {
						case 'cpt' :
							jQuery('#woo_content_builder_cpt_post_type').parent().parent().find('div.explain').text('A general name for the post type, usually plural. Also, use only lower case letters and no special characters except for the underscore.');
							break;
						case 'ctx' :
							jQuery('#woo_content_builder_ctx_taxonomy').parent().parent().find('div.explain').text('A general name for the taxonomy, usually plural. Also, use only lower case letters and no special characters except for the underscore.');
							break;
						case 'cmb' :
							jQuery('#woo_content_builder_cmb_name').parent().parent().find('div.explain').text('A general name for the custom field, usually plural. Also, use only lower case letters and no special characters except for the underscore.');
							break;
					}
					jQuery('#wooform-content-builder').find('input.submit-button').removeClass('hidden');
				}
			});
		
		});
	
	<?php } // End If Statement ?>
	
});
</script>
<?php
}

add_action('wp_ajax_woothemes_content_builder_check_for_existing_custom_content', 'woothemes_content_builder_item_exists');

?>