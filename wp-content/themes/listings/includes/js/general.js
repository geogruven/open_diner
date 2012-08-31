jQuery(document).ready(function(){


	jQuery('#loopedSlider.gallery div.container div.slides div a.thickbox').each(function(){
	
		jQuery(this).mouseenter(function(){
			
			if (jQuery(this).find('div.gallery-hover').length > 0){
				//do nothing
			} else {
				var adddiv = jQuery('<div>').addClass('gallery-hover');
				jQuery(this).append(adddiv);
			}
		});
		
		jQuery(this).mouseleave(function(){
			
			//kill it
			if (jQuery(this).find('div.gallery-hover').length > 0){
				jQuery(this).find('div.gallery-hover').remove();
			} else {
				//do nothing
			}
			
		});
		
		
	});
	
	
});