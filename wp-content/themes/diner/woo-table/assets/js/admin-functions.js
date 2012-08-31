/*-----------------------------------------------------------------------------------

FILE INFORMATION

Description: JavaScript on the Custom Post Type `add/edit` screens for the WooTable WordPress plugin.
Date Created: 2010-08-11.
Author: Matty.
Since: 0.0.0.1


TABLE OF CONTENTS

-----------------------------------------------------------------------------------*/

jQuery(document).ready( function() {
	
/*----------------------------------------
Integrate the jQueryUI datepicker
----------------------------------------*/
	
	var correctDate = jQuery( 'input#reservation_date' ).attr( 'value' );
	var correctDateFormatted = new Date(correctDate);
	var currentDate = new Date();
	var currentDay = currentDate.getDate();
	var currentMonth = currentDate.getMonth();
	var currentYear = currentDate.getFullYear();
	
	jQuery( 'input#reservation_date' ).attr( 'readonly', 'readonly' ).datepicker( { 
		dateFormat: 'yy-mm-dd', 
		minDate: new Date( currentYear, currentMonth, currentDay ), 
		onSelect: function ( dateText, inst ) {

			data = new Array();
			
			var timeText = '';
			timeText = jQuery(this).parents('form').find('.reservation_time').val();
			
			// Run an AJAX call to get the possible reservation times for the date selected.
			jQuery.ajax({
			   type: "POST",
			   url: ajaxurl,
			   data: { 'action' : 'get_times', 'date' : dateText, 'time' : timeText },
			   success: function( response ) {
			     
			     jQuery('#post-body-content #reservation-data .reservation_time').replaceWith(response);
			     
			   }
			 });
			
		} 
		} );
		
/*----------------------------------------
Remove the "View Reservation" button
----------------------------------------*/

	jQuery( '#post-body-content .inside:contains("View Reservation")' ).remove(); // Remove the "Permalink" area as it's irrelevant.
	// jQuery( '#post-body-content #view-post-btn:contains("View Reservation")' ).remove(); // Remove the "View Reservation" button as it's irrelevant.

});