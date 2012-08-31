<?php

	$maps_active = get_post_meta(get_the_id(),'woo_maps_enable',true);
	$src = get_post_meta(get_the_id(),'image',true);
	$maps_overview = get_option('woo_show_overview');
	
	//if($maps_active == 'on' OR !empty($src) OR $maps_overview == 'true') {
	
	// Load Coords from Posts
	$coords = array();
	$pin_amount = 1000;
	global $query_string;
	global $wp_query;
	global $post;
	
	$search_page = false;
	// search results page
	if (isset($has_results) && $has_results) {
		$search_query_args = array();
		if (is_array($query_args)) {
			if (isset($query_args['post__in'])) { $search_query_args['include'] = $query_args['post__in']; }
			if (isset($query_args['post_type'])) { $search_query_args['post_type'] = $query_args['post_type']; } 
			if (isset($query_args['posts_per_page'])) { $search_query_args['numberposts'] = $query_args['posts_per_page']; }
			if (isset($query_args['paged'])) { $search_query_args['paged'] = $query_args['paged']; }
		}
		$list_posts = get_posts($search_query_args);
		$search_page = true;
	} else {
		// non search page
		$list_posts = get_posts($query_string .'&post_type=any&post_status=publish&numberposts=-1');
	}

	$x = 0;
	foreach($list_posts as $single_post){
		$x++;
		$id = $single_post->ID;
		$lat = get_post_meta($id, 'woo_maps_lat',true);
		//echo $lat;
		if(!empty($lat)){
			$coords[$id] = array(	'coords' => get_post_meta($id, 'woo_maps_lat',true) . ', ' . get_post_meta($id, 'woo_maps_long',true),
											'color' => $woo_options['woo_cat_colors_pages']);
		} else {
			$x--;
		}
		//echo cat_to_color(get_the_category( $id ));
		if($x == $pin_amount) { break; }
	}


	if(!empty($coords)) :
	
 	?>

    <div id="featured-map" class="<?php if ( ! $search_page ) { ?>fl<?php } ?>" style="width:940px;">
        <div class="woo_map_single_output">
        <?php
        
        //	if($maps_overview == 'true'){
        	
	    		$zoom = get_option('woo_archive_zoom');
	    		if(empty($zoom)) $zoom = '6';
	    		$type = get_option('woo_archive_type');
	    		$center = get_option('woo_archive_center');
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
									
				// API KEY NO LONGER NEEDED
				
	    		$feat_h = get_option('woo_archive_featured_h');
	    		if(empty($feat_h)){ $feat_h = 250;}
	    		?>
	    		<div id="featured_overview" style="height:<?php echo $feat_h; ?>px; width:100%"></div>
	    		<?php		
	
	    		/* Maps Bit */
	    		
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
			  			    var map = new google.maps.Map(document.getElementById("featured_overview"),  myOptions);
						    <?php if(get_option('woo_maps_archive_scroll') == 'true'){ ?>
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
						         		
						        var color = '<?php echo $c_value['color']; ?>';
			 			        createMarker(map,point,root,the_link,the_title,color);
						    <?php } ?>
						    }
						initialize();
					});
	    	</script>
	    	
        </div>
    </div>
    
    <?php else : ?><div class="spacer"></div><?php
    endif;  ?>