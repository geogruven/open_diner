<?php
/*---------------------------------------------------------------------------------*/
/* Overview Map widget */
/*---------------------------------------------------------------------------------*/

class woo_MapsOverviewWidget extends WP_Widget {
	function woo_MapsOverviewWidget() {
		$widget_ops = array('classname' => 'widget_maps_overview_widget', 'description' => 'Add a Overview of all the Listings in the system.' );
		$this->WP_Widget('maps_overview_widget', 'Woo - Overview Map', $widget_ops);
	}

	function widget($args, $instance) {
		extract($args, EXTR_SKIP);

		extract( $args );
		$title = $instance['title'];
		$zoom = $instance['zoom'];
		$tag = $instance['tag'];
		$height = $instance['height'];	
		$type = $instance['type'];	
		$center = $instance['center'];
		
		switch ($type) {
			case 'G_NORMAL_MAP':
			    $type = 'ROADMAP';
			    break;
			case 'G_SATELLITE_MAP':
			    $type = 'SATELLITE';
			    break;
			case 'G_HYBRID_MAP':
			    $type = 'HYBRID';
			    break;
			case 'G_PHYSICAL_MAP':
			    $type = 'TERRAIN';
			    break;
			default:
			    $type = 'ROADMAP';
			    break;
		} 
			  	
		if(!empty($title)){
			echo $before_widget;
			echo $before_title . $title . $after_title;
		} 
		else {
			echo $before_widget;
		}
		
		// No More API Key Needed
		
		?>
        <div id="overview_map_canvas_<?php echo $args['widget_id']; ?>" style="height:<?php echo $height; ?>px; width:100%"></div>
        <?php		
			
	   	echo $after_widget; 
		
		/* Maps Bit */
		$coords = array();
		if(!empty($tag)) {
			$posts = get_posts('post_type=listing&numberposts=100&tag=' . $tag);
		} else {
			$posts = get_posts('post_type=listing&numberposts=100');
		}
		
		foreach($posts as $post){
			$lat = get_post_meta($post->ID, 'woo_maps_lat',true);
			if(!empty($lat)){
				$coords[$post->ID] = array(	'coords' => get_post_meta($post->ID, 'woo_maps_lat',true) . ', ' . get_post_meta($post->ID, 'woo_maps_long',true),
												'color' => cat_to_color(get_the_category( $post->ID )));
			}		
		}
		$coord_keys = array_keys($coords);
		$first_key = $coord_keys[0];
		
		$center_final = $coords[$first_key]['coords'];
		if(!empty($center)) { $center_final = $center; }
		
		?>
		<script src="<?php bloginfo('template_url'); ?>/includes/js/markers.js" type="text/javascript"></script>
        <script type="text/javascript">
			jQuery(document).ready(function(){
				function initialize() {
					
					var myLatlng = new google.maps.LatLng(<?php echo $center_final; ?>);
					var myOptions = {
					  zoom: <?php echo $zoom; ?>,
					  center: myLatlng,
					  mapTypeId: google.maps.MapTypeId.<?php echo $type; ?>
					};
			  		var map = new google.maps.Map(document.getElementById("overview_map_canvas_<?php echo $args['widget_id']; ?>"),  myOptions);
					<?php if(get_option('woo_maps_scroll') == 'true'){ ?>
			  		map.scrollwheel = false;
			  		<?php } ?>
					
					<?php foreach($coords as $c_key => $c_value) { ?>
					    var point = new google.maps.LatLng(<?php echo $c_value['coords']; ?>);
	  				    var root = "<?php bloginfo('template_url'); ?>";
	  				    var the_link = '<?php echo get_permalink($c_key); ?>';
	  				    <?php $title = str_replace(array('&#8220;','&#8221;'),'"',get_the_title($c_key)); ?>
	  				    <?php $title = str_replace('&#8211;','-',$title); ?>
	  				    <?php $title = str_replace('&#8217;',"`",$title); ?>
	  				    <?php $title = str_replace('&#038;','&',$title); ?>
	  				    var the_title = '<?php echo html_entity_decode($title) ?>';
					    <?php if ($c_value['color'] != '') { $color_value = $c_value['color']; } else { $color_value = 'red'; } ?>
					    var color = '<?php echo $c_value['color']; ?>';
			 		    createMarker(map,point,root,the_link,the_title,color);
					<?php } ?>
						    		
				}
				initialize();
			})
		</script>
        
        <?php
		
	}

   function update($new_instance, $old_instance) {                
       return $new_instance;
   }


	function form($instance) {
		$title = strip_tags($instance['title']);
		$zoom = $instance['zoom'];
		$tag = $instance['tag'];
		$height = $instance['height'];
		$type = $instance['type'];
		$center = $instance['center'];
		
		if(empty($zoom)) $zoom = '0';
		if(empty($height)) $height = '300';
		if(empty($type)) $type = 'G_NORMAL_MAP';
?>
	    <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','woothemes'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
        </p>
	    <p>
            <label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Tag: (optional / clustering)','woothemes'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('tag'); ?>" value="<?php echo $tag; ?>" class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('zoom'); ?>"><?php _e('Map Zoom:','woothemes'); ?></label>
            <select class="widefat" name="<?php echo $this->get_field_name('zoom'); ?>" id="<?php echo $this->get_field_id('zoom'); ?>">
			<?php 
                for($i = 0; $i < 20; $i++) {
                if($i == $zoom){ $selected = 'selected="selected"';} else { $selected = '';}		
                 ?><option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
            <?php } ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('center'); ?>"><?php _e('Center Coordinates: (optional)','woothemes'); ?> <small>Example: 43.7712879,11.2064976</small></label>
            <input type="text" name="<?php echo $this->get_field_name('center'); ?>" value="<?php echo $center; ?>" class="widefat" id="<?php echo $this->get_field_id('center'); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Map Height:','woothemes'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('height'); ?>" value="<?php echo $height; ?>" class="widefat" id="<?php echo $this->get_field_id('height'); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Map Type:','woothemes'); ?></label>
           <select class="widefat" name="<?php echo $this->get_field_name('type'); ?>" id="<?php echo $this->get_field_id('type'); ?>">
			<?php
                $map_types = array('Normal' => 'G_NORMAL_MAP','Satellite' => 'G_SATELLITE_MAP','Hybrid' => 'G_HYBRID_MAP','Physical' => 'G_PHYSICAL_MAP',); 
                foreach($map_types as $k => $v) {
                if($type == $v){ $selected = 'selected="selected"';} else { $selected = '';}		
                 ?><option value="<?php echo $v; ?>" <?php echo $selected; ?>><?php echo $k; ?></option>
            <?php } ?>
            </select>
          </p>
        <?php
	}
}
register_widget('woo_MapsOverviewWidget');
?>