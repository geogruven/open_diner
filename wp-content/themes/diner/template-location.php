<?php
/*
Template Name: Location
*/
?>

<?php get_header(); ?>
    <script type="text/javascript">
    jQuery(document).ready(function(){
		
		// Make the directions popup draggable. // 2010-11-14.
		jQuery('#directions').css('cursor', 'move');
		jQuery('#directions').draggable();
		
		jQuery('#emailtofriend').click(function(){
		
			jQuery('#content.location #directions').toggle();
			if(jQuery('#overlay').length > 0){
        		jQuery('#overlay').remove();
        	} else {
        		jQuery('body').append('<div id="overlay"></div>');
        		var doc_height = jQuery(document).height();
        		jQuery('#overlay').height(doc_height);
        		jQuery('#overlay').click(function(){
        			jQuery('#content.location #directions').toggle();
        			jQuery(this).remove();
        		});
        	}
   			jQuery('#modal-email').dialog('open');
   		});
		
		var yourname = jQuery( "#yourname" ),
			email = jQuery( "#email" ),
			theirname = jQuery( "#theirname" ),
			allFields = jQuery( [] ).add( yourname ).add( email ).add( theirname ),
			tips = jQuery( ".validateTips" );

		function updateTips( t ) {
			tips
				.text( t )
				.addClass( "ui-state-highlight" );
			setTimeout(function() {
				tips.removeClass( "ui-state-highlight", 1500 );
			}, 500 );
		}

		function checkLength( o, n, min, max ) {
			if ( o.val().length > max || o.val().length < min ) {
				o.addClass( "ui-state-error" );
				updateTips( "Length of " + n + " must be between " +
					min + " and " + max + "." );
				return false;
			} else {
				return true;
			}
		}

		function checkRegexp( o, regexp, n ) {
			if ( !( regexp.test( o.val() ) ) ) {
				o.addClass( "ui-state-error" );
				updateTips( n );
				return false;
			} else {
				return true;
			}
		}

		jQuery('#modal-email').hide();
	
		// Initialise the dialog box
		jQuery('#modal-email').dialog({
								autoOpen: false, 
								title: 'Send to a friend', 
								resizable: false,
								height: 175,
								width: 300, 
								modal: true, 
								buttons: {
									Confirm: function() {
										//jQuery('#modal-email-fields').validate();
										
										var bValid = true;
										allFields.removeClass( 'ui-state-error' );

										bValid = bValid && checkLength( yourname, "your name", 3, 16 );
										bValid = bValid && checkLength( theirname, "your friends name", 5, 16 );
										bValid = bValid && checkLength( email, "email", 6, 80 );
										
										bValid = bValid && checkRegexp( yourname, /^[a-z]([0-9a-z _])+$/i, "Your name may consist of a-z, 0-9, underscores, begin with a letter." );
										bValid = bValid && checkRegexp( theirname, /^[a-z]([0-9a-z _])+$/i, "Your friends name may consist of a-z, 0-9, underscores, begin with a letter." );
										// From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
										bValid = bValid && checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );
										
										if ( bValid ) {
											woo_ajax_email_js();
											jQuery( this ).dialog( "close" );
										}

	 									
									}, 
									Cancel: function() {
										jQuery(this).dialog("close");
										
									}
								}
							});
		
	});
    </script>   
    <div id="content" class="page location col-full">
    
    	<div class="text">
    	
    		<h2 class="title"><?php _e('Location', 'woothemes'); ?></h2>
    		
    		<address><?php echo nl2br(get_option('woo_diner_address')); ?></address>
    		
    		<h3><?php _e('Directions', 'woothemes'); ?></h3>
    		
    		<p><?php _e('Enter your current location below to generate directions to Diner.', 'woothemes'); ?></p>
    		
    		<form id="directions-form" name="directions-form" method="post" action="#">
    		
    			<input class="txt" type="text" name="address" value="<?php _e('Enter your address', 'woothemes'); ?>" />
    			
    			<a class="button" href="#" ><?php _e('Get Directions', 'woothemes'); ?></a>
    		
    		</form>
    	
    		<script type="text/javascript">	
    	
    			var map;
				var directionsPanel;
				var directions;

				function initialize_directions(fromdir, todir) {
				
				<?php
					$extra_params = '';
					$lang = get_option('woo_maps_directions_locale');
					$locale = '';
					if(!empty($lang)){
						$locale = ',locale : \''.$lang.'\'';
					}
					$extra_params = ',{travelMode:G_TRAVEL_MODE_WALKING,avoidHighways:true '.$locale.'}';
				?>
							
  					map = new GMap2(document.getElementById("featured_overview"));
  					directionsPanel = document.getElementById("direction-result");
  					directions = new GDirections(map, directionsPanel);
  					directions.load("from: " + fromdir + " to: " + todir + ""<?php echo $extra_params; ?>);
  					GEvent.addListener(directions, "error", handleErrors);
				}
				
				function handleErrors(){
				
					if (directions.getStatus().code == G_GEO_UNKNOWN_ADDRESS)
  		 				jQuery('#direction-result').show().html('No corresponding geographic location could be found for one of the specified addresses. This may be due to the fact that the address is relatively new, or it may be incorrect.');
 		 			else if (directions.getStatus().code == G_GEO_SERVER_ERROR)
 		  				jQuery('#direction-result').show().html('A geocoding or directions request could not be successfully processed, yet the exact reason for the failure is not known.');
 					else if (directions.getStatus().code == G_GEO_MISSING_QUERY)
   						jQuery('#direction-result').show().html('The HTTP q parameter was either missing or had no value. For geocoder requests, this means that an empty address was specified as input. For directions requests, this means that no query was specified in the input.');
 					else if (directions.getStatus().code == G_GEO_BAD_KEY)
   						jQuery('#direction-result').show().html('The given key is either invalid or does not match the domain for which it was given.');
 		 			else {
 		 				if (directions.getStatus().code == G_GEO_BAD_REQUEST)
   							jQuery('#direction-result').show().html('A directions request could not be successfully parsed.');
 						else jQuery('#direction-result').show().html('An unknown error occurred.');
						}
					map = new GMap2(document.getElementById("featured_overview"));
					map.setCenter(new GLatLng(<?php echo get_option('woo_diner_map_latitude').', '.get_option('woo_diner_map_longitude'); ?>), <?php echo get_option('woo_diner_map_zoom_level'); ?>);
				}
  				
    	jQuery(document).ready(function(){
    		//DIRECTIONS LIGHTBOX
	
			jQuery('#content.location .text .button').click(function(){
    	
        		jQuery('#content.location #directions').toggle();
        		
        		var fromdir = jQuery('#directions-form input.txt').val();
        		var todir = '<?php echo get_option('woo_diner_map_latitude').', '.get_option('woo_diner_map_longitude'); ?>';
        		
        		jQuery('#direction-result').text('');
        		initialize_directions(fromdir, todir);
        		
        		if(jQuery('#overlay').length > 0){
        			jQuery('#overlay').remove();
        		} else {
        			jQuery('body').append('<div id="overlay"></div>');
        			var doc_height = jQuery(document).height();
        			jQuery('#overlay').height(doc_height);
        			jQuery('#overlay').click(function(){
        				jQuery('#content.location #directions').toggle();
        				jQuery(this).remove();
        			});
        		}
        
    
    		});
    		
    		//PRINT EVENT
    		jQuery('#print-directions').click(function(){
    			w=window.open();
				w.document.write(jQuery('#direction-result').html());
				w.print();
				w.close();
    		});
    		
    		

    	
		});
		</script>
		
    	</div><!-- /.text -->
    	
    	<div class="map">
    		
    		<div class="map-frame">
    			<div class="woo_map_single_output">
                    <?php
                            				
							$zoom = get_option('woo_diner_map_zoom_level');
							if(empty($zoom)) $zoom = '6';
							$type = get_option('woo_diner_map_type');
							$map_types = array('Normal' => 'G_NORMAL_MAP','Satellite' => 'G_SATELLITE_MAP','Hybrid' => 'G_HYBRID_MAP','Terrain' => 'G_PHYSICAL_MAP',); 
							$type = $map_types[$type];
							if(empty($type)) $type = 'G_NORMAL_MAP';
							$center = get_option('woo_diner_map_latitude').', '.get_option('woo_diner_map_longitude');
		
							$key = get_option('woo_maps_apikey');
							if(empty($key)){ ?>
		 					<div style="margin:10px">Please enter your <strong>API Key</strong> before using the maps.</div>
							<?php
		
							} else {
							?>
							<div id="featured_overview" style="height:390px; width:610px"></div>
							<?php		
			
							/* Maps Bit */	
							if(!empty($center)) { $center_final = $center; }
		
							?>
							<script src="<?php bloginfo('template_url'); ?>/includes/js/markers.js" type="text/javascript"></script>
							<script type="text/javascript">
							// Creates a marker and returns
    						jQuery(document).ready(function(){
    							
								function initialize() {
								  if (GBrowserIsCompatible()) {
									var map = new GMap2(document.getElementById("featured_overview"));
									map.setMapType(<?php echo $type; ?>);
									map.setUIToDefault();
									<?php if(get_option('woo_maps_scroll') == 'true'){ ?>
									map.disableScrollWheelZoom();
									<?php } ?>
									map.setCenter(new GLatLng(<?php echo $center_final; ?>), <?php echo $zoom; ?>);
									
																
	  								var point = new GLatLng(<?php echo $center; ?>);
	  								var root = "<?php bloginfo('template_url'); ?>";
	  								var the_link = '<?php echo get_permalink(); ?>';
	  								var the_title = '<?php echo preg_replace('/[^a-z 0-9 ]/i', '', get_the_title()); ?>';
	  								var color = 'blue';
									map.addOverlay(createMarker(point,root,the_link,the_title,color));
									
								
				  				}
							}
						initialize();
						});
						</script>
						<?php } ?>
                    </div>
            </div>

    	
    	</div><!-- /.map -->
    	
    	<div id="directions">
    		
    		<h4>
    			<?php _e('Directions', 'woothemes'); ?>
    			<span>
    				<a id="print-directions" class="print" href="#" title="<?php _e('Print Directions', 'woothemes'); ?>"><?php _e('Print', 'woothemes'); ?></a> <a id="emailtofriend" class="email" href="#" title="Email Directions"><?php _e('Email', 'woothemes'); ?></a>
    			</span>
    		</h4>
    		
    		<p id="direction-result"><?php _e('Directions go here', 'woothemes'); ?></p>
    		
    	</div>
        <div id="modal-email">
			<form id="modal-email-fields" method="post" action="">
				<fieldset>
					<p class="validateTips"><?php _e('All form fields are required.', 'woothemes'); ?></p>
					<p class="form-field">
						<label for="yourname"><?php _e('Your Name', 'woothemes'); ?></label>
						<input type="text" name="yourname" id="yourname" class="text ui-widget-content ui-corner-all input-text required" />
					</p>
					<p class="form-field">
						<label for="theirname"><?php _e('Your Friends Name', 'woothemes'); ?></label>
						<input type="text" name="theirname" id="theirname" class="text ui-widget-content ui-corner-all input-text required" />
					</p>
					<p class="form-field">
						<label for="email"><?php _e('Email', 'woothemes'); ?></label>
						<input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all input-text required email" />
						<input type="hidden" name="url" id="url" value="<?php echo woo_curPageURL(); ?>" />
						<input type="hidden" name="action" id="action" value="location" />
					</p>
				</fieldset>
			</form>
	
		</div>    
    </div><!-- /#content -->
		
<?php get_footer(); ?>