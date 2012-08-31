/*-----------------------------------------------------------------------------------

FILE INFORMATION

Description: JavaScript for "custom fields for taxonomies" in the Content Builder.
Date Created: 2011-01-13.
Author: Matty.
Since: 1.1.0


TABLE OF CONTENTS

- Datepicker Instantiation
- Masked Input for time input field Instantiation.

- The object, "woo_theme_urls" has been made available via wp_localize_script()
- carrying with it the "template_url" and "stylesheet_url".

-----------------------------------------------------------------------------------*/

jQuery(document).ready(function(){

	//JQUERY DATEPICKER
	jQuery('.woo-input-calendar').each(function (){
		jQuery('#' + jQuery(this).attr('id')).datepicker({showOn: 'button', dateFormat: 'yy-mm-dd', buttonImage: woo_theme_urls.template_url + '/functions/images/calendar.gif', buttonImageOnly: true});
	});
	
	//JQUERY TIME INPUT MASK
	jQuery('.woo-input-time').each(function (){
		jQuery('#' + jQuery(this).attr('id')).mask("99:99");
	});

});