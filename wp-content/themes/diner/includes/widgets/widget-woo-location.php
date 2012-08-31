<?php
/*---------------------------------------------------------------------------------*/
/* Location Widget */
/*---------------------------------------------------------------------------------*/


class Woo_Location extends WP_Widget {

	function Woo_Location() {
		/*
		$widget_ops = array('description' => 'DIsplay your Restaurant Location' );
		parent::WP_Widget(false, __('Woo - Location', 'woothemes'),$widget_ops);
		*/
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_woo_location', 'description' => __('Display your Restaurant Location', 'woothemes' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'woo_location' );

		/* Create the widget. */
		$this->WP_Widget( 'woo_location', __('Woo - Location', 'woothemes' ), $widget_ops, $control_ops );    
	}

	function widget($args, $instance) { 
		extract( $args );
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base );
		$pageid = $instance['pageid'];

        echo $before_widget; ?>
       	
       	<?php echo $before_title .$title. $after_title; ?>
        <?php
        	$address_text = get_option('woo_diner_address');
        ?>
        <span class="location-address"><?php echo $address_text; ?></span>
        
        <div class="location-map">
        	<?php
                            				
							// $zoom = get_option('woo_diner_map_zoom'); // Incorrect option call. 2010-11-04.
							$zoom = get_option('woo_diner_map_zoom_level'); // Correct option call. 2010-11-04.
							if(empty($zoom)) $zoom = '6';
							
							// $type = get_post_meta($pageid,'map_type',true); // Call for map type when on the location page.
							$type_in_db = get_option( 'woo_diner_map_type' );
							
							switch ( $type_in_db ) {
							
								case 'Normal':
								
								$type = 'G_NORMAL_MAP';
								
								break;
								
								case 'Satellite':
								
								$type = 'G_SATELLITE_MAP';
								
								break;
								
								case 'Hybrid':
								
								$type = 'G_HYBRID_MAP';
								
								break;
								
								case 'Terrain':
								
								$type = 'G_PHYSICAL_MAP';
								
								break;
								
								default:
								
								$type = 'G_NORMAL_MAP';
								
								break;
							
							} // End SWITCH Statement
							
							if(empty($type)) $type = 'G_NORMAL_MAP';
							$center = get_option('woo_diner_map_latitude').', '.get_option('woo_diner_map_longitude');
		
							$key = get_option('woo_maps_apikey');
							if(empty($key)){ ?>
		 					<div style="margin:10px"><?php echo __('Please enter your <strong>API Key</strong> before using the maps.', 'woothemes'); ?></div>
							<?php
		
							} else {
							?>
							<div id="featured_overview" style="height:175px; width:290px"></div>
							<?php		
			
							/* Maps Bit */	
							if(!empty($center)) { $center_final = $center; }
		
							?>
							<script src="<?php bloginfo('template_url'); ?>/includes/js/markers.js" type="text/javascript"></script>
							<script type="text/javascript">
							// Creates a marker and returns
    						jQuery(document).ready(function(){
    							
    							// alert( GBrowserIsCompatible() ); // DEBUG
    							
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
	  								var the_link = '<?php echo get_permalink($pageid); ?>';
	  								var the_title = '<?php echo preg_replace('/[^a-z0-9&]/i', ' ', $address_text ); ?>';
	  								var color = 'blue';
									map.addOverlay(createMarker(point,root,the_link,the_title,color));
									
				  					}
								}
								initialize();
							});
						</script>
						<?php } ?>
						
        	
        	
        	<a class="button inactive" href="<?php echo get_permalink($pageid); ?>" title="Directions">Get Directions</a>
        
        </div><!-- /.location-map -->

        <?php
			
		echo $after_widget;

	}

	function update($new_instance, $old_instance) {                
		return $new_instance;
	}

	function form($instance) {
	
		$title = esc_attr($instance['title']);
		$pageid = esc_attr($instance['pageid']);
		?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','woothemes'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
        </p>
        <p>
			<label for="<?php echo $this->get_field_id('pageid'); ?>"><?php _e('Location Page Template:','woothemes'); ?></label>
			<?php $args = array(
								'show_option_none'  => __('Select a Page:'), 
    							'depth'            => 0,
    							'child_of'         => 0,
    							'selected'         => $pageid,
    							'echo'             => 1,
    							'name'             => $this->get_field_name('pageid'),
    							'id'               => $this->get_field_name('pageid'),
    							); ?>
    		<?php wp_dropdown_pages($args); ?>
    							
		</p>
        <?php
	
	}
} 

register_widget('Woo_Location');


?>