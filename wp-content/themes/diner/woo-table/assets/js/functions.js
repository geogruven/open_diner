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
Integrate the jQueryUI datepicker
----------------------------------------*/
	
	var correctDate = jQuery( 'form[name="wootable-booking-form"] input.input-reservation_date_real' ).attr( 'value' );
	
	var currentDate = new Date();
	var currentDay = currentDate.getDate();
	var currentMonth = currentDate.getMonth();
	var currentYear = currentDate.getFullYear();
	
	jQuery( '#wootable-calendar' ).datepicker( { 
		dateFormat: 'yy-mm-dd', 
		altField: '#reservation_date', 
		minDate: new Date( currentYear, currentMonth, currentDay ), 
		onSelect: function ( dateText, inst ) {
				
			var timeText = jQuery(this).parents('form').find('.reservation_time').val();
			var peopleText = jQuery(this).parents('form').find('.number_of_people').val();
			var pageId = jQuery(this).parents('form').find('.input-page_id').val();
			var isUpdate = jQuery(this).parents('form').find('.input-is_update').val();
			
			// Can't use '#' in jQuery 1.4.4, so get the URL to the page we're currently on. Cast it as a string.
			var ajax_url = String( document.location );
			
			// Run an AJAX call to get the possible reservation times for the date selected.
			jQuery.ajax({
			   type: "POST",
			   url: ajax_url,
			   data: { 'date' : dateText, 'time' : timeText, 'ajax' : 1, 'ajax-action' : 'get_times', 'page_id' : pageId, 'is_admin' : isUpdate },
			   success: function( data ) {
			     
			     jQuery('form[name="wootable-booking-form"] .reservation_time').replaceWith(data);
			     
			     // Change the confirmation message.
			     // wootable_widget_confirmation_message( dateText, timeText, peopleText, 'form[name="wootable-booking-form"] .confirmation_message', 'form[name="wootable-booking-form"]', 'generate_confirmation_message' );
			     
			   }
			 });
			
		} 
		} ).datepicker( 'setDate', correctDate );

/*----------------------------------------
Widget-specific AJAX call
----------------------------------------*/
	
	jQuery( '.widget-wootable-makereservation #wootable-calendar-widget' ).datepicker( {
		dateFormat: 'yy-mm-dd', 
		altField: '#reservation_date', 
		minDate: new Date( currentYear, currentMonth, currentDay ), 
		onSelect: function ( dateText, inst ) {
			
			// Set `this` to a temporary variable for use later on in the AJAX response.
			var _this = jQuery(this);
				
			var timeText = jQuery(this).parents('form').find('.reservation_time').val();
			var peopleText = jQuery(this).parents('form').find('.number_of_people').val();
			
			// Can't use '#' in jQuery 1.4.4, so get the URL to the page we're currently on. Cast it as a string.
			var ajax_url = String( document.location );
			
			// Run an AJAX call to get the possible reservation times for the date selected.
			jQuery.ajax({
			   type: "POST",
			   url: ajax_url,
			   data: { 'date' : dateText, 'time' : timeText, 'ajax' : 1, 'ajax-action' : 'get_times' },
			   success: function( data ) {
			     
			     jQuery('.widget-wootable-makereservation .reservation_time').replaceWith(data);
			     
			     // Make sure we get the latest times when adjusting the datepicker, not the values
			     // from the previous date selection.
			     
			     var timeText = _this.parents('form').find('.reservation_time').val();
				 var peopleText = _this.parents('form').find('.number_of_people').val();
			     
			     // Change the confirmation message.
			     // wootable_widget_confirmation_message( dateText, timeText, peopleText, 'form[name="wootable-booking-form"] .confirmation_message', 'form[name="wootable-booking-form"]', 'generate_confirmation_message' );
			     
			     // Change the confirmation message.
				 wootable_widget_confirmation_message( dateText, timeText, peopleText, '.widget-wootable-makereservation .confirmation_message', '.widget-wootable-makereservation form', 'generate_confirmation_message_widget' );
			     
			   }
			 });
			
			// Change the confirmation message.
			// wootable_widget_confirmation_message( dateText, timeText, peopleText, '.widget-wootable-makereservation .confirmation_message', '.widget-wootable-makereservation form', 'generate_confirmation_message_widget' );
			
		}
	
	} );

/*----------------------------------------
Widget-specific AJAX call on `select`s
----------------------------------------*/
	
	jQuery( '.widget-wootable-makereservation select' ).change( function () {
	
		var dateText = jQuery(this).parents('form').find('#reservation_date').val();
		var timeText = jQuery(this).parents('form').find('.reservation_time').val();
		var peopleText = jQuery(this).parents('form').find('.number_of_people').val();
		
		// Change the confirmation message.
		wootable_widget_confirmation_message( dateText, timeText, peopleText, '.widget-wootable-makereservation .confirmation_message', '.widget-wootable-makereservation form', 'generate_confirmation_message_widget' );
		
	});

}); // End jQuery()

/*----------------------------------------
wootable_widget_confirmation_message()
----------------------------------------*/

function wootable_widget_confirmation_message ( dateText, timeText, peopleText, selectorText, formElement, ajaxAction ) {

	var formAction = jQuery( formElement ).attr('action');

	// Run an AJAX call to get the possible reservation times for the date selected.
	jQuery.ajax({
	   type: "POST",
	   url: formAction,
	   data: { 'ajax' : 1, 'ajax-action' : ajaxAction, 'date' : dateText, 'time' : timeText, 'people' : peopleText },
	   success: function( response ) {
	     
	     jQuery( selectorText ).replaceWith(response);
	     
	   }
	 });
	
} // End wootable_widget_confirmation_message()