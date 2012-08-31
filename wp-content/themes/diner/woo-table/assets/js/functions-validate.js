/*-----------------------------------------------------------------------------------

FILE INFORMATION

Description: JavaScript on the frontend for the WooTable WordPress plugin.
Date Created: 2010-08-11.
Author: Matty.
Since: 0.0.0.1


TABLE OF CONTENTS

- Integrate the jQueryUI datepicker
- Widget-specific AJAX call
- Widget-specific AJAX call on `select`s
- Form AJAX call on `select`s

- function wootable_widget_confirmation_message ()

-----------------------------------------------------------------------------------*/

jQuery(function($) {

/*----------------------------------------
Reservation Form Validation
----------------------------------------*/
	/* jQuery('form[name="wootable-booking-form"]').after('<div class="modal-content">test</div>'); */
	jQuery('.modal-content').hide();
	
	// Initialise the dialog box
	jQuery('.modal-content').dialog({
								autoOpen: false, 
								title: 'Confirm your reservation', 
								resizable: false,
								height: 175,
								width: 300, 
								modal: true, 
								buttons: {
									Confirm: function() {
										// jQuery(this).dialog("close");
										// jQuery('form[name="wootable-booking-form"]').submit();
										jQuery('form[name="wootable-booking-form"]').get(0).submit();
									}, 
									Cancel: function() {
										jQuery(this).dialog("close");
										// jQuery(form).bind('submit', function () { jQuery(form).valid(); });
									}
								}
							});
	
	jQuery('form[name="wootable-booking-form"]').validate({
	
		onsubmit: true, 
		modal: true, 
		errorLabelContainer: ".error_msg",
		
		success: "valid", 
  		
  		submitHandler: function ( form ) {
  		
  			jQuery('.modal-content').dialog('open');
  			
  		},
		rules: {
		 reservation_time: "required", 
	     contact_name: "required", 
	     contact_tel: "required", 
	     contact_email: {
	       required: true,
	       email: true
	     }
	   },
	   messages: {
	     reservation_time: "Please select the time you would like to make your reservation at", 
	     contact_name: "Please enter your full name",
	     contact_tel: "Please enter your telephone number",
	     contact_email: {
	       required: "Please enter your e-mail address",
	       email: "Your email address must be in the format of name@domain.com"
	     }
	   }, 
	   
	   close: function ( event, ui ) {}
		
	});

}); // End jQuery()